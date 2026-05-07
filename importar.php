<?php
header('Content-Type: application/json; charset=utf-8');

$input = json_decode(file_get_contents('php://input'), true);

if (!$input || !isset($input['datos'])) {
    echo json_encode(["status" => "error", "mensaje" => "JSON inválido"]);
    exit;
}

$merge = $input['merge'] ?? true;
$nuevos = $input['datos'];
$archivo = 'diccionario.json';

// Si merge, cargar existente
$datos = [];
if ($merge && file_exists($archivo)) {
    $datos = json_decode(file_get_contents($archivo), true);
    if (!is_array($datos)) $datos = [];
}

// Si los datos importados tienen estructura completa (meta, reglas, entradas)
if (isset($nuevos['entradas'])) {
    // Formato completo
    if (!isset($datos['meta'])) $datos['meta'] = $nuevos['meta'] ?? [];
    if (!isset($datos['reglas'])) $datos['reglas'] = $nuevos['reglas'] ?? [];
    if (!isset($datos['entradas'])) $datos['entradas'] = [];

    foreach ($nuevos['entradas'] as $cat => $lista) {
        if (!isset($datos['entradas'][$cat])) {
            $datos['entradas'][$cat] = [];
        }

        if ($merge) {
            // Merge: añadir solo las que no existan
            foreach ($lista as $nueva) {
                $dup = false;
                foreach ($datos['entradas'][$cat] as $exist) {
                    if ($exist['emoji'] === $nueva['emoji'] && $exist['galego'] === $nueva['galego']) {
                        $dup = true;
                        break;
                    }
                }
                if (!$dup) {
                    $datos['entradas'][$cat][] = $nueva;
                }
            }
        } else {
            // Sobreescribir categoría
            $datos['entradas'][$cat] = $lista;
        }
    }
} else {
    // Formato antiguo (categorías directas sin wrapper)
    if (!isset($datos['meta'])) {
        $datos['meta'] = ["version" => "2.0", "creado" => date("Y-m-d H:i:s")];
    }
    if (!isset($datos['reglas'])) $datos['reglas'] = [];
    if (!isset($datos['entradas'])) $datos['entradas'] = [];

    foreach ($nuevos as $cat => $lista) {
        if ($cat === 'meta' || $cat === 'reglas') continue;
        if (!is_array($lista)) continue;
        
        if (!isset($datos['entradas'][$cat])) {
            $datos['entradas'][$cat] = [];
        }
        foreach ($lista as $nueva) {
            $datos['entradas'][$cat][] = $nueva;
        }
    }
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
        "total" => $total,
        "categorias" => count($datos['entradas'])
    ]);
} else {
    echo json_encode(["status" => "error", "mensaje" => "Erro ao escribir"]);
}
?>
