<?php
ob_start();
header('Content-Type: application/json; charset=UTF-8');
error_reporting(E_ALL);
ini_set('display_errors', 1);

$host = '';
$user = '';
$pass = '';
$db_name = '';

$conn = new mysqli($host, $user, $pass, $db_name);

if ($conn->connect_errno) {
    http_response_code(500);
    echo json_encode(["error" => "Ошибка подключения: " . $conn->connect_error]);
    exit;
}

$conn->set_charset("utf8mb4");

// Получаем массивы ID
$continentIds = isset($_GET['continent_id']) ? (array)$_GET['continent_id'] : [];
$subregionIds = isset($_GET['subregion_id']) ? (array)$_GET['subregion_id'] : [];

// Преобразуем в числа и фильтруем
$continentIds = array_filter(array_map('intval', $continentIds));
$subregionIds = array_filter(array_map('intval', $subregionIds));

$sql = "SELECT c1.id AS main_id, c1.name AS main_name, c1.flag_icon AS main_flag, c1.slug AS main_slug, 
               c1.continent_id AS main_continent_id, c1.subregion_id AS main_subregion_id,
               c2.id AS linked_id, c2.name AS linked_name, c2.flag_icon AS linked_flag, c2.slug AS linked_slug 
        FROM countries c1
        LEFT JOIN countries_link cl ON c1.id = cl.country_id1
        LEFT JOIN countries c2 ON cl.country_id2 = c2.id
        WHERE 1";

if (!empty($continentIds)) {
    $sql .= " AND c1.continent_id IN (" . implode(',', $continentIds) . ")";
}

if (!empty($subregionIds)) {
    $sql .= " AND c1.subregion_id IN (" . implode(',', $subregionIds) . ")";
}

$sql .= " ORDER BY c1.name ASC";

$result = $conn->query($sql);

if (!$result) {
    http_response_code(500);
    echo json_encode(["error" => "Ошибка SQL-запроса: " . $conn->error]);
    exit;
}

$countries = [];
while ($row = $result->fetch_assoc()) {
    $mainId = $row['main_id'];
    if (!isset($countries[$mainId])) {
        $countries[$mainId] = [
            'id' => $row['main_id'],
            'name' => $row['main_name'],
            'flag_icon' => $row['main_flag'],
            'slug' => $row['main_slug'],
            'continent_id' => $row['main_continent_id'],
            'subregion_id' => $row['main_subregion_id'],
            'linked_countries' => []
        ];
    }
    
    if (!empty($row['linked_id'])) {
        $countries[$mainId]['linked_countries'][] = [
            'id' => $row['linked_id'],
            'name' => $row['linked_name'],
            'flag_icon' => $row['linked_flag'],
            'slug' => $row['linked_slug']
        ];
    }
}

ob_clean();
echo json_encode(array_values($countries), JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
$conn->close();
?>