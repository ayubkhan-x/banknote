<?php
ob_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

$host = '';
$user = '';
$pass = '';
$db_name = '';

$conn = new mysqli($host, $user, $pass, $db_name);

if ($conn->connect_errno) {
    die("Ошибка подключения: " . $conn->connect_error);
}

$conn->set_charset("utf8mb4");

// Получаем slug страны из URL
$slug = isset($_GET['slug']) ? $conn->real_escape_string($_GET['slug']) : '';

if (!$slug) {
    die("Страна не найдена.");
}

// Запрос к БД
$sql = "SELECT id, name, flag_icon, description FROM countries WHERE slug = '$slug'";
$result = $conn->query($sql);

if ($result->num_rows === 0) {
    die("Страна не найдена.");
}

$country = $result->fetch_assoc();
$conn->close();
?>

<section class="banknote-sec" id="spy1">
    <?php include "components/fixed-nav.php"; ?>
    <div class="container">
        <div class="flag">
            <img src="/images/<?php echo htmlspecialchars($country['flag_icon']); ?>"
                alt="<?php echo htmlspecialchars($country['name']); ?>">
        </div>

        <div class="inner-block">
            <div class="title">
                <h2 class="title-black">
                    <a href="/"><img src="/images/arrow-back.svg" alt="Back"></a>
                    <span><?php echo htmlspecialchars($country['name']); ?> Paper Money Catalog</span>
                </h2>
            </div>

            <div class="row">
                <div class="col-lg-3">
                    <div class="money-map">
                        <img src="/images/globus-img.png" alt="">
                    </div>
                </div>

                <div class="col-lg-9">
                    <div class="money-block">
                        <div class="money-text">
                            <?php echo nl2br(htmlspecialchars($country['description'] ?? 'No description')); ?>
                        </div>

                        <div class="row">
                            <div class="col-lg-5">
                                <div class="money-left">
                                    <span>Monetary System</span>
                                    <ul>
                                        <li>Russian currency, till 1918</li>
                                        <li>Latvian Ruble = (Russian Ruble),1918-1922</li>
                                        <li>Lat = (50 Latvian Rubles) = 100 Santimi, 1922-1940</li>
                                        <li>Russian currency, 1940-1991</li>
                                        <li>Latvian Ruble = (Russian Ruble),1991-1993</li>
                                        <li>Lat = (200 Latvian Rubles) = 100 Santimi, from 1993</li>
                                        <li>Euro = 0.7028 Lat, from 2014</li>
                                    </ul>
                                </div>
                            </div>

                            <div class="col-lg-7">
                                <div class="money-right">
                                    <div class="money-item">
                                        <span>See also</span>
                                        <a href="#!">Link title</a>
                                    </div>
                                    <div class="money-item">
                                        <span>References</span>
                                        <a href="#!">Link title</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
        <?php include "components/country_money.php"; ?>
    </div>
</section>