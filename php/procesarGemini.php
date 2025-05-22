<?php

require_once "Clases/controlador.php";
require_once 'Clases/estado.php';
session_start();

header('Content-Type: application/json');

if (!isset($_FILES['archivo'])) {
    echo json_encode(["error" => "No se recibió ningún archivo."]);
    exit;
}

$filePath = $_FILES['archivo']['tmp_name'];
$mimeType = mime_content_type($filePath);

if (strpos($mimeType, 'image/') !== 0 && $mimeType !== 'application/pdf') {
    echo json_encode(["error" => "El archivo debe ser una imagen o un PDF válido."]);
    exit;
}

$fileData = base64_encode(file_get_contents($filePath));

if (!isset($_SESSION["Controlador"])) {
    echo json_encode(["error" => "No existe 'Controlador' en la sesión."]);
    exit;
}

$controlador = $_SESSION["Controlador"];

if (!isset($controlador->miEstado)) {
    echo json_encode(["error" => "El Controlador no tiene 'miEstado'."]);
    exit;
}

// Actualizar datosProyectos y asignar listaConceptos y listaArticulo
$_SESSION["Controlador"]->miEstado->datosProyectos = extraerFasesProyectos();

if (!isset($_SESSION["Controlador"]->miEstado->datosProyectos[2])) {
    echo json_encode(["error" => "No existe la posición 2 en datosProyectos para listaConceptos."]);
    exit;
}
if (!isset($_SESSION["Controlador"]->miEstado->datosProyectos[3])) {
    echo json_encode(["error" => "No existe la posición 3 en datosProyectos para listaArticulo."]);
    exit;
}

$_SESSION["Controlador"]->miEstado->listaConceptos = $_SESSION["Controlador"]->miEstado->datosProyectos[2];
$_SESSION["Controlador"]->miEstado->listaArticulo = $_SESSION["Controlador"]->miEstado->datosProyectos[3];

$conceptos = $controlador->miEstado->listaConceptos;
$articulos = $controlador->miEstado->listaArticulo;

if (empty($conceptos)) {
    echo json_encode(["error" => "'listaConceptos' está vacío."]);
    exit;
}

if (!is_array($articulos) || empty($articulos)) {
    echo json_encode(["error" => "'listaArticulo' no es un array válido o está vacío."]);
    exit;
}

// Extraer nombres de conceptos
$nombresConceptos = array_map(function($item) {
    if (is_array($item) && isset($item['Nombre'])) {
        return $item['Nombre'];
    } elseif (is_object($item) && isset($item->Nombre)) {
        return $item->Nombre;
    }
    return '';
}, $conceptos);


// Extraer nombres de artículos
$nombresArticulos = array_map(function($item) {
    if (is_array($item) && isset($item['Descripcion'])) {
        return $item['Descripcion'];
    } elseif (is_object($item) && isset($item->Descripcion)) {
        return $item->Descripcion;
    }
    return '';
}, $articulos);

// Filtrar nombres vacíos
$nombresConceptos = array_filter($nombresConceptos, fn($v) => !empty($v));
$nombresArticulos = array_filter($nombresArticulos, fn($v) => !empty($v));

if (empty($nombresConceptos)) {
    echo json_encode(["error" => "La lista de conceptos está vacía o no disponible después del filtrado."]);
    exit;
}

if (empty($nombresArticulos)) {
    echo json_encode(["error" => "La lista de artículos está vacía o no disponible después del filtrado."]);
    exit;
}

$listaConceptos = implode(", ", array_map(fn($c) => '"' . $c . '"', $nombresConceptos));
$listaArticulos = implode(", ", array_map(fn($c) => '"' . $c . '"', $nombresArticulos));


$prompt = <<<PROMPT
Eres un experto analizando tickets de gasto con el objetivo de identificar los siguientes 3 campos:

1. Fecha Ticket
2. Importe Ticket
3. Tipo de Artículo
4. Artículo de gasto
5. Descripcion del gasto

Para la descripción del gasto, debes elegir la mejor palabra  que describa el gasto total del ticket.

Para el campo "Tipo de Artículo", debes elegir el concepto más adecuado exclusivamente de la siguiente lista:

[$listaConceptos]

Para el campo "Artículo de gasto", debes elegir el concepto más adecuado exclusivamente de la siguiente lista:

[$listaArticulos]

En caso de que no encuentres un articulo adecuado, pon el por defecto.

Responde exclusivamente con un JSON válido. No uses bloques de código, ni etiquetas markdown como ```json ni comillas triples. Solo el JSON plano, sin texto adicional.

Ejemplo del formato esperado:
{
  "Fecha Ticket": "dd/mm/yyyy",
  "Importe Ticket": "1000.00",
  "Tipo de Artículo": "Uno de los conceptos anteriores"
  "Artículo de gasto": "Uno de los conceptos anteriores",
  "Descripción": "comida"
}
PROMPT;

$data = [
    "contents" => [
        [
            "parts" => [
                ["text" => $prompt],
                [
                    "inlineData" => [
                        "mimeType" => $mimeType,
                        "data" => $fileData
                    ]
                ]
            ]
        ]
    ]
];

$ch = curl_init();
$apiKey = 'AIzaSyBYxaMLDHM-qq8V0UzYfB_UN9elu9ZAwGY'; // Reemplaza con tu API key segura

curl_setopt($ch, CURLOPT_URL, "https://generativelanguage.googleapis.com/v1/models/gemini-1.5-flash:generateContent?key=$apiKey");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Content-Type: application/json"
]);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

$response = curl_exec($ch);

if (curl_errno($ch)) {
    echo json_encode(["error" => curl_error($ch)]);
    exit;
} else {
    $decoded = json_decode($response, true);
    $texto = $decoded['candidates'][0]['content']['parts'][0]['text'] ?? '';
    $limpio = trim($texto);

    $parsedJson = json_decode($limpio, true);

        if (json_last_error() === JSON_ERROR_NONE) {
        $parsedJson["Lista Articulos"] = $nombresArticulos;
        $parsedJson["Lista Conceptos"] = $nombresConceptos;
        echo json_encode($parsedJson);
        exit;
    } else {
        echo json_encode([
            "error" => "La respuesta del modelo no es JSON válido.",
            "raw" => $texto,
            "decode_error" => json_last_error_msg()
        ]);
        exit;
    }
}

curl_close($ch);
