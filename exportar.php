<?php
header('Content-Type: application/json; charset=utf-8');

$archivo = 'diccionario.json';

if (!file_exists($archivo)) {
    echo json_encode([
        "meta" => ["version" => "2.0", "total_entradas" => 0],
        "reglas" => [],
        "entradas" => []
    ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    exit;
}

echo file_get_contents($archivo);
?>
