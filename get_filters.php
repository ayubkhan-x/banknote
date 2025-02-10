<?php
ob_start();
header('Content-Type: application/json; charset=UTF-8');
error_reporting(E_ALL);
ini_set('display_errors', 1);

$host = 'mysql8';
$user = '39330634_banknotewiki';
$pass = 'eWgl1bn8';
$db_name = '39330634_banknotewiki';

$conn = new mysqli($host, $user, $pass, $db_name);

if ($conn->connect_errno) {
    http_response_code(500);
    echo json_encode(["error" => "Ошибка подключения: " . $conn->connect_error]);
    exit;
}

$conn->set_charset("utf8mb4");

// Получаем континенты
$continents = [];
$result = $conn->query("SELECT id, name FROM continents ORDER BY name ASC");
while ($row = $result->fetch_assoc()) {
    $continents[] = $row;
}

// Получаем суб-регионы
$subregions = [];
$result = $conn->query("SELECT id, name FROM subregions ORDER BY name ASC");
while ($row = $result->fetch_assoc()) {
    $subregions[] = $row;
}

ob_clean();
echo json_encode(["continents" => $continents, "subregions" => $subregions], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
$conn->close();
?>
