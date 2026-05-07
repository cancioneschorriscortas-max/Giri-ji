[guardar_lote.php](https://github.com/user-attachments/files/27482826/guardar_lote.php)
<?php
header('Content-Type: application/json; charset=utf-8');

$input = file_get_contents('php://input');
$lote = json_decode($input, true);

if (!is_array($lote)) {
    echo json_encode(["status" => "error", "mensaje" => "JSON inválido ou non é un array"]);
    exit;
}

$archivo = 'diccionario.json';
$datos = [];

if (file_exists($archivo)) {
    $datos = json_decode(file_get_contents($archivo), true);
    if (!is_array($datos)) $datos = [];
}

if (!isset($datos['meta'])) {
    $datos['meta'] = [
        "version" => "2.0",
        "lingua_base" => "galego",
        "creado" => date("Y-m-d H:i:s"),
        "modificado" => date("Y-m-d H:i:s"),
        "total_entradas" => 0
    ];
}

if (!isset($datos['reglas'])) {
    $datos['reglas'] = [];
}

if (!isset($datos['entradas'])) {
    $datos['entradas'] = [];
}

$insertadas = 0;
$duplicadas = 0;

foreach ($lote as $item) {
    $cat = trim($item['categoria'] ?? '');
    $galego = trim($item['galego'] ?? '');
    $emoji = trim($item['emoji'] ?? '');

    if ($cat === '' || $galego === '' || $emoji === '') continue;

    // Check duplicados
    $dup = false;
    foreach ($datos['entradas'][$cat] ?? [] as $exist) {
        if ($exist['emoji'] === $emoji && $exist['galego'] === $galego) {
            $dup = true;
            break;
        }
    }
    if ($dup) { $duplicadas++; continue; }

    $nueva = [
        "id"              => uniqid('gj_'),
        "galego"          => $galego,
        "castellano"      => trim($item['castellano'] ?? ''),
        "emoji"           => $emoji,
        "tipo_simbolo"    => $item['tipo_simbolo'] ?? 'raiz',
        "nivel"           => intval($item['nivel'] ?? 4),
        "modo"            => $item['modo'] ?? 'entidad',
        "posicion_frase"  => $item['posicion_frase'] ?? 'libre',
        "subcategoria"    => trim($item['subcategoria'] ?? ''),
        "propiedades" => [
            "es_contenedor"    => $item['es_contenedor'] ?? false,
            "lleva_accion"     => $item['lleva_accion'] ?? false,
            "es_pasivo"        => $item['es_pasivo'] ?? false,
            "permite_plural"   => $item['permite_plural'] ?? true,
            "permite_negacion" => $item['permite_negacion'] ?? false,
            "permite_atributo" => $item['permite_atributo'] ?? true
        ],
        "composicion" => [
            "componentes"       => trim($item['componentes'] ?? ''),
            "regla_composicion" => trim($item['regla_composicion'] ?? ''),
            "derivaciones"      => trim($item['derivaciones'] ?? '')
        ],
        "ejemplos"         => $item['ejemplos'] ?? [],
        "logica"           => trim($item['logica'] ?? ''),
        "notas_traductor"  => trim($item['notas_traductor'] ?? ''),
        "fecha_creacion"   => date("Y-m-d H:i:s"),
        "fecha_modificacion" => date("Y-m-d H:i:s")
    ];

    $datos['entradas'][$cat][] = $nueva;
    $insertadas++;
}

// Actualizar meta
$datos['meta']['modificado'] = date("Y-m-d H:i:s");
$total = 0;
foreach ($datos['entradas'] as $cat => $lista) {
    $total += count($lista);
}
$datos['meta']['total_entradas'] = $total;

$json = json_encode($datos, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);

if (file_put_contents($archivo, $json, LOCK_EX)) {
    echo json_encode([
        "status" => "ok",
        "insertadas" => $insertadas,
        "duplicadas" => $duplicadas,
        "total" => $total
    ]);
} else {
    echo json_encode(["status" => "error", "mensaje" => "Erro ao escribir o arquivo"]);
}
?>
