<?php
ob_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

$host = 'mysql8';
$user = '39330634_banknotewiki';
$pass = 'eWgl1bn8';
$db_name = '39330634_banknotewiki';

// Создаем подключение через MySQLi
$conn = new mysqli($host, $user, $pass, $db_name);

// Проверяем на ошибки подключения
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Получаем параметр slug из URL
$slug = isset($_GET['slug']) ? $_GET['slug'] : '';

// Запрос к базе данных для получения информации о стране по slug
$query = "SELECT * FROM countries WHERE slug = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param('s', $slug);  // Привязка параметра для предотвращения SQL-инъекций
$stmt->execute();
$result = $stmt->get_result();
$country = $result->fetch_assoc();

if ($country) {
    // Если страна найдена, устанавливаем её название в pageTitle
    $pageTitle = $country['name']; // Динамический заголовок
} else {
    // Если страна не найдена, можно установить дефолтный заголовок
    $pageTitle = "Страна не найдена";
}

// Подключаем header_country.php
include('components/header_country.php');
?>

<div class="wrapper">
    <main class="main">
        <div class="banner-sec"><img src="/images/banner-img.png" alt=""></div>

        <?php include "components/country_detail.php"; ?>
    </main>
</div>

<?php include "components/footer.php"; ?>
