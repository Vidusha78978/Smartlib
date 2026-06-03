<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET");
header("Content-Type: application/json; charset=UTF-8");

if (!isset($_GET['q']) || empty($_GET['q'])) {
    http_response_code(400);
    echo json_encode(["success" => false, "message" => "No search query provided."]);
    exit();
}

$query = urlencode($_GET['q']);

// Use Open Library API instead of Google Books API (Completely Free, No API Key needed)
$url = "https://openlibrary.org/search.json?title=" . $query . "&limit=1";

// Use cURL to fetch data
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);a
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Disabled SSL verification for localhost XAMPP
curl_setopt($ch, CURLOPT_USERAGENT, 'LibraryTechApp/1.0'); // OpenLibrary requires a User-Agent

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curlErrno = curl_errno($ch);
$curlError = curl_error($ch);
curl_close($ch);

if ($curlErrno) {
    http_response_code(500);
    echo json_encode(["success" => false, "message" => "cURL Error: " . $curlError]);
} elseif ($httpCode !== 200) {
    http_response_code(502);
    echo json_encode(["message" => "Open Library API Error: Failed to fetch data."]);
} else {
    $olData = json_decode($response, true);
    $items = [];
    
    // Map Open Library response to match the existing Google Books structure in React
    if (isset($olData['docs']) && count($olData['docs']) > 0) {
        $doc = $olData['docs'][0];
        $items[] = [
            "volumeInfo" => [
                "title" => $doc['title'] ?? "Unknown Title",
                "authors" => $doc['author_name'] ?? [],
                "categories" => $doc['subject'] ?? []
            ]
        ];
    }
    
    http_response_code(200);
    echo json_encode(["items" => $items]);
}
?>
