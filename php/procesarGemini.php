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



$conceptos = $_SESSION["Controlador"]->miEstado->listaConceptos;
$listaConceptos = implode(", ", $conceptos);

$prompt = <<<PROMPT
Eres un experto analizando tickets de gasto con el objetivo de identificar los siguientes 3 campos:

1. Fecha Ticket
2. Importe Ticket
3. Concepto Ticket

Para el campo "Concepto Ticket", debes elegir el concepto más adecuado exclusivamente de la siguiente lista:

[$listaConceptos]

Responde exclusivamente con un JSON válido. No uses bloques de código, ni etiquetas markdown como ```json ni comillas triples. Solo el JSON plano, sin texto adicional.

Ejemplo del formato esperado:
{
  "Fecha Ticket": "dd/mm/yyyy",
  "Importe Ticket": "1000.00",
  "Concepto Ticket": "Uno de los conceptos anteriores"
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
} else {
    $decoded = json_decode($response, true);
    $texto = $decoded['candidates'][0]['content']['parts'][0]['text'] ?? '';

    $limpio = preg_replace('/^```json\s*|\s*```$/', '', trim($texto));

    json_decode($limpio);
    if (json_last_error() === JSON_ERROR_NONE) {
        echo $limpio;
    } else {
        echo json_encode(["error" => "La respuesta no es un JSON válido.", "raw" => $texto]);
    }
}

curl_close($ch);
