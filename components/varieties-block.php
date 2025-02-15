<?php
ob_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Подключение к базе данных
$host = 'mysql8';
$user = '39330634_banknotewiki';
$pass = 'eWgl1bn8';
$db_name = '39330634_banknotewiki';

$conn = new mysqli($host, $user, $pass, $db_name);
$conn->set_charset("utf8mb4");

if ($conn->connect_error) {
    die("Ошибка подключения: " . $conn->connect_error);
}

// Получаем данные из сессии
$ref1 = $_SESSION['ref1'] ?? '';
$ref2 = $_SESSION['ref2'] ?? '';
$ref3 = $_SESSION['ref3'] ?? '';
$name = $_SESSION['name'] ?? '';

if (!$ref1 || !$ref2 || !$ref3 || !$name) {
    die("Ошибка: Недостаточно данных для поиска банкноты.");
}

// Подготовка запроса
$sql = "SELECT b.name, b.img_front, b.img_back, b.description_long, b.issuer, b.remarks, c.flag_icon 
        FROM banknotes b 
        JOIN countries c ON b.country_id = c.id 
        WHERE b.ref1 = ? AND b.ref2 = ? AND b.ref3 = ? AND b.name = ?";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    die("Ошибка подготовки запроса: " . $conn->error);
}

$stmt->bind_param("ssss", $ref1, $ref2, $ref3, $name);
$stmt->execute();
$result = $stmt->get_result();
$banknote = $result->fetch_assoc();
$stmt->close();
$conn->close();

if (!$banknote) {
    die("Ошибка: Банкнота не найдена.");
}
?>

<div class="flag">
    <img src="/images/<?= htmlspecialchars($banknote['flag_icon'] ?? 'default-flag.png', ENT_QUOTES, 'UTF-8'); ?>"
        alt="">
</div>

<div class="varieties-block">
    <div class="title">
        <h2 class="title-black">
            <a href="#"><img src="/images/arrow-back.svg" alt=""></a>
            <span><?= htmlspecialchars($banknote['name'] ?? 'Unknown Banknote', ENT_QUOTES, 'UTF-8'); ?></span>
        </h2>
    </div>

    <div class="row">
        <div class="col-xl-3 col-lg-6">
            <div class="varieties-img">
                <span><img
                        src="/images/<?= htmlspecialchars($banknote['img_back'] ?? 'default-back.png', ENT_QUOTES, 'UTF-8'); ?>"
                        alt=""></span>
                <span><img
                        src="/images/<?= htmlspecialchars($banknote['img_front'] ?? 'default-front.png', ENT_QUOTES, 'UTF-8'); ?>"
                        alt=""></span>
            </div>
        </div>

        <div class="col-xl-5 col-lg-6">
            <div class="varieties-desc">
                <div class="desc-text">
                    <span>Description</span>
                    <p><?= nl2br(htmlspecialchars($banknote['description_long'] ?? 'No description available.', ENT_QUOTES, 'UTF-8')); ?>
                    </p>
                </div>
                <div class="desc-issuer">
                    <span>Issuer</span>
                    <p><?= htmlspecialchars($banknote['issuer'] ?? 'Unknown', ENT_QUOTES, 'UTF-8'); ?></p>
                </div>
                <div class="desc-issuer">
                    <span>Remark</span>
                    <p><?= htmlspecialchars($banknote['remarks'] ?? 'No remarks.', ENT_QUOTES, 'UTF-8'); ?></p>
                </div>
            </div>
        </div>
    </div>
</div>
