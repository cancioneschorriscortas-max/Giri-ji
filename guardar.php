<?php
header('Content-Type: text/plain; charset=utf-8');

// 1. Recibir datos
$categoria = trim($_POST['categoria'] ?? '');
$galego    = trim($_POST['galego'] ?? '');
$emoji     = trim($_POST['emoji'] ?? '');

// 2. Validación mínima
if ($categoria === '' || $galego === '' || $emoji === '') {
    die("Faltan datos obrigatorios.");
}

// 3. Recoger ejemplos
$ejemplos = [];
$ej_giriji = $_POST['ejemplo_giriji'] ?? [];
$ej_trad   = $_POST['ejemplo_traduccion'] ?? [];
for ($i = 0; $i < count($ej_giriji); $i++) {
    $g = trim($ej_giriji[$i] ?? '');
    $t = trim($ej_trad[$i] ?? '');
    if ($g !== '' || $t !== '') {
        $ejemplos[] = [
            "giriji"     => $g,
            "traduccion" => $t
        ];
    }
}

// 4. Crear entrada con estructura completa para traductor
$nueva = [
    "id"              => uniqid('gj_'),
    "galego"          => $galego,
    "castellano"      => trim($_POST['castellano'] ?? ''),
    "emoji"           => $emoji,
    "tipo_simbolo"    => $_POST['tipo_simbolo'] ?? 'raiz',
    "nivel"           => intval($_POST['nivel'] ?? 4),
    "modo"            => $_POST['modo'] ?? 'entidad',
    "posicion_frase"  => $_POST['posicion_frase'] ?? 'libre',
    "subcategoria"    => trim($_POST['subcategoria'] ?? ''),
    
    // Propiedades gramaticales
    "propiedades" => [
        "es_contenedor"    => isset($_POST['es_contenedor']),
        "lleva_accion"     => isset($_POST['lleva_accion']),
        "es_pasivo"        => isset($_POST['es_pasivo']),
        "permite_plural"   => isset($_POST['permite_plural']),
        "permite_negacion" => isset($_POST['permite_negacion']),
        "permite_atributo" => isset($_POST['permite_atributo'])
    ],
    
    // Composición
    "composicion" => [
        "componentes"        => trim($_POST['componentes'] ?? ''),
        "regla_composicion"  => trim($_POST['regla_composicion'] ?? ''),
        "derivaciones"       => trim($_POST['derivaciones'] ?? '')
    ],
    
    // Ejemplos
    "ejemplos" => $ejemplos,
    
    // Metadata
    "logica"           => trim($_POST['logica'] ?? ''),
    "notas_traductor"  => trim($_POST['notas_traductor'] ?? ''),
    "fecha_creacion"   => date("Y-m-d H:i:s"),
    "fecha_modificacion" => date("Y-m-d H:i:s")
];

// 5. Cargar JSON existente
$archivo = 'diccionario.json';
$datos = [];

if (file_exists($archivo)) {
    $contenido = file_get_contents($archivo);
    $datos = json_decode($contenido, true);
    if (!is_array($datos)) $datos = [];
}

// Asegurar estructura base
if (!isset($datos['meta'])) {
    $datos['meta'] = [
        "version"       => "2.0",
        "lingua_base"   => "galego",
        "creado"        => date("Y-m-d H:i:s"),
        "modificado"    => date("Y-m-d H:i:s"),
        "total_entradas" => 0
    ];
}

if (!isset($datos['reglas'])) {
    $datos['reglas'] = [
        "composicion" => "símbolos pegados = bloque compositivo, separados = relación sintáctica",
        "accion"      => "↗️ = comportamento inherente ou acción intencional",
        "estado"      => "sen marca = estado/situación actual",
        "atributo"    => "↓ = cualidade, modifica bloque anterior",
        "negacion"    => "❌ pegado + entidade = concepto novo; ❌ separado + verbo = negación",
        "plural"      => "⚫ = plural (multiplicidade)",
        "tempo"       => "◀️ = pasado, ▶️ = futuro, sen marca = presente, 〰️ = continuo",
        "orde"        => "primeiro símbolo = contexto/marco, último antes de ↗️ = acción base",
        "contedor"    => "[ ] = contexto explícito",
        "intensidade" => "⊕ = moito, ⊕⊕ = moitísimo, ⊕⊕⊕ = enorme",
        "posesion"    => "🔗 = relación xeral (de), posesión por contexto",
        "existencia"  => "✳️ = existir (ontolóxico, distinto de 💓 vida e 📍 posición)",
        "espacio"     => "en Giri-ji, o espazo entre símbolos tamén significa"
    ];
}

if (!isset($datos['entradas'])) {
    $datos['entradas'] = [];
}

// 6. Evitar duplicados
foreach ($datos['entradas'][$categoria] ?? [] as $entrada) {
    if ($entrada['emoji'] === $emoji && $entrada['galego'] === $galego) {
        die("Esta entrada xa existe.");
    }
}

// 7. Añadir
$datos['entradas'][$categoria][] = $nueva;

// 8. Actualizar meta
$datos['meta']['modificado'] = date("Y-m-d H:i:s");
$total = 0;
foreach ($datos['entradas'] as $cat => $lista) {
    $total += count($lista);
}
$datos['meta']['total_entradas'] = $total;

// 9. Guardar
$json = json_encode($datos, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);

if (file_put_contents($archivo, $json, LOCK_EX)) {
    echo "ok";
} else {
    echo "Erro ao gardar.";
}
?>
