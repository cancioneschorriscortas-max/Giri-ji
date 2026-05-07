<?php
header('Content-Type: application/json; charset=utf-8');

$archivo = 'diccionario.json';

if (!file_exists($archivo)) {
    echo json_encode(["total" => 0, "categorias" => 0, "raices" => 0, "ejemplos" => 0]);
    exit;
}

$datos = json_decode(file_get_contents($archivo), true);
$entradas = $datos['entradas'] ?? [];

$total = 0;
$raices = 0;
$ejemplos = 0;

foreach ($entradas as $cat => $lista) {
    $total += count($lista);
    foreach ($lista as $e) {
        if (($e['tipo_simbolo'] ?? '') === 'raiz') $raices++;
        $ejemplos += count($e['ejemplos'] ?? []);
    }
}

echo json_encode([
    "total" => $total,
    "categorias" => count($entradas),
    "raices" => $raices,
    "ejemplos" => $ejemplos
]);
?>
