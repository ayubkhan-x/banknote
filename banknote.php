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

// Проверка соединения
if ($conn->connect_error) {
    die("Ошибка подключения: " . $conn->connect_error);
}

// Получаем параметры из URL
$slug = $_GET['slug'] ?? '';
$banknote_id = $_GET['id'] ?? '';

// Определяем значение $ref
$ref = $_SERVER['HTTP_REFERER'] ?? '/';
$_SESSION['ref'] = $ref;

// Получаем данные страны и банкноты
// Расширяем SQL-запрос, чтобы получить дополнительные данные
$query = "SELECT 
             c.id as country_id, 
             c.flag_icon, 
             c.name as country_name,
             b.id as banknote_id, 
             b.name as banknote_name, 
             b.img_front, 
             b.img_back, 
             b.description_long, 
             b.issuer, 
             b.remarks,
             b.ref1, b.ref2, b.ref3,
             e.name as era_name,
             cu.name as currency_name,
             i.name as issue_name,
             b.is_commemorative,  -- Исправлено здесь
             b.composition,
             b.is_demonetized
         FROM banknotes b
         LEFT JOIN countries c ON c.id = b.country_id
         LEFT JOIN c_era e ON e.id = b.era_id
         LEFT JOIN c_currency cu ON cu.id = b.currency_id
         LEFT JOIN c_issue i ON i.id = b.issue_id
         WHERE c.slug = ? AND b.id = ?";

$stmt = $conn->prepare($query);
$stmt->bind_param('si', $slug, $banknote_id);
$stmt->execute();
$result = $stmt->get_result();
$banknote = $result->fetch_assoc();

if (!$banknote) {
    die("Ошибка: Банкнота не найдена.");
}

$_SESSION['name'] = $banknote['banknote_name']; // Имя банкноты для заголовка
$pageTitle = $banknote['banknote_name']; // Устанавливаем заголовок страницы

// Формируем значения
$era = $banknote['era_name'] ?? 'Unknown';
$currency = $banknote['currency_name'] ?? 'Unknown';
$issue = $banknote['issue_name'] ?? 'Unknown';
$composition = $banknote['composition'] ?? 'Unknown';
$is_commemorative = $banknote['is_commemorative'] ? 'Yes' : 'No';
$is_demonetized = $banknote['is_demonetized'] ? 'Yes' : 'No';
$country_name = $banknote['country_name'] ?? 'Unknown';
$flag_icon = $banknote['flag_icon'] ? "/images/country-flag/{$banknote['flag_icon']}" : "/images/flag.png";

// Подключаем header
include('components/header_country.php');
?>

<div class="wrapper">
    <main class="main">
        <div class="banner-sec"><img src="/images/banner-img.png" alt=""></div>
        <section class="banknote-sec">
            <div class="container">
                <div class="flag">
                    <img width="78" height="78"
                        src="/images/country-flag/<?= htmlspecialchars($banknote['flag_icon'] ?? 'default-flag.png', ENT_QUOTES, 'UTF-8'); ?>"
                        alt="Flag">
                </div>

                <div class="varieties-block">
                    <div class="title">
                        <h2 class="title-black">
                            <a href="<?= htmlspecialchars($_SESSION['ref'] ?? '/', ENT_QUOTES, 'UTF-8'); ?>">
                                <img src="/images/arrow-back.svg" alt="">
                            </a>
                            <span><?= htmlspecialchars($banknote['banknote_name'] ?? '', ENT_QUOTES, 'UTF-8'); ?></span>
                        </h2>
                    </div>

                    <div class="row">
                        <div class="col-xl-3 col-lg-6">
                            <div class="varieties-img">
                                <span><img width="222"
                                        src="/banknotes/<?= $banknote['country_id']; ?>/<?= htmlspecialchars($banknote['img_front'] ?? 'default-front.jpg', ENT_QUOTES, 'UTF-8'); ?>"
                                        alt=""></span>
                                <span><img width="222"
                                        src="/banknotes/<?= $banknote['country_id']; ?>/<?= htmlspecialchars($banknote['img_back'] ?? 'default-back.jpg', ENT_QUOTES, 'UTF-8'); ?>"
                                        alt=""></span>
                            </div>
                        </div>
                        <div class="col-xl-5 col-lg-6">
                            <div class="varieties-desc">
                                <div class="desc-text">
                                    <span>Description</span>
                                    <p><?= nl2br(htmlspecialchars($banknote['description_long'] ?? 'No description available', ENT_QUOTES, 'UTF-8')); ?>
                                    </p>
                                </div>
                                <div class="desc-issuer">
                                    <span>Issuer</span>
                                    <p><?= htmlspecialchars($banknote['issuer'] ?? 'Unknown', ENT_QUOTES, 'UTF-8'); ?>
                                    </p>
                                </div>
                                <div class="desc-issuer">
                                    <span>Remarks</span>
                                    <p><?= nl2br(htmlspecialchars($banknote['remarks'] ?? 'No remarks', ENT_QUOTES, 'UTF-8')); ?>
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-4 col-lg-6">
                            <div class="properties">
                                <div class="properties-title">
                                    <p>Properties</p>
                                </div>
                                <div class="properties-list">
                                    <div class="properties-item">
                                        <span>Country</span>
                                        <p><img width="25" height="25"
                                                src="<?= htmlspecialchars($flag_icon, ENT_QUOTES, 'UTF-8'); ?>" alt="">
                                            <?= htmlspecialchars($country_name, ENT_QUOTES, 'UTF-8'); ?></p>
                                    </div>
                                    <div class="properties-item">
                                        <span>Era</span>
                                        <p><?= htmlspecialchars($era, ENT_QUOTES, 'UTF-8'); ?></p>
                                    </div>
                                    <div class="properties-item">
                                        <span>Currency</span>
                                        <p><?= htmlspecialchars($currency, ENT_QUOTES, 'UTF-8'); ?></p>
                                    </div>
                                    <div class="properties-item">
                                        <span>Issue</span>
                                        <p><?= htmlspecialchars($issue, ENT_QUOTES, 'UTF-8'); ?></p>
                                    </div>
                                    <div class="properties-item">
                                        <span>Type</span>
                                        <p>Standard banknote</p>
                                    </div>
                                    <div class="properties-item">
                                        <span>Composition</span>
                                        <p><?= htmlspecialchars($composition, ENT_QUOTES, 'UTF-8'); ?></p>
                                    </div>
                                    <div class="properties-item">
                                        <span>Commemorative</span>
                                        <p><?= htmlspecialchars($is_commemorative, ENT_QUOTES, 'UTF-8'); ?></p>
                                    </div>
                                    <div class="properties-item">
                                        <span>Demonetized</span>
                                        <p><?= htmlspecialchars($is_demonetized, ENT_QUOTES, 'UTF-8'); ?></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php include "components/tab-navbar.php"; ?>
                <!-- Добавьте этот контейнер после подключения навигации -->
                <div class="tab-content" id="tabContent">
                    <?php
                    $tab = $_GET['tab'] ?? 'varieties';
                    $componentMap = [
                        'varieties' => 'banknote-varieties',
                        'valuation' => 'banknote-valuation',
                        'description' => 'banknote-description',
                        'watermark' => 'banknote-watermark',
                        'buysell' => 'banknote-buysell'
                    ];

                    $component = $componentMap[$tab] ?? 'banknote-varieties';
                    include("components/$component.php");
                    ?>
                </div>
            </div>
        </section>
    </main>
</div>

<?php include "components/footer.php"; ?>