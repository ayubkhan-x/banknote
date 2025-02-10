<?php
$host = 'mysql8';
$user = '39330634_banknotewiki';
$pass = 'eWgl1bn8';
$db_name = '39330634_banknotewiki';

$conn = new mysqli($host, $user, $pass, $db_name);
if ($conn->connect_errno) {
    die("Ошибка подключения: " . $conn->connect_error);
}
$conn->set_charset("utf8mb4");

$slug = $conn->real_escape_string($_GET['slug'] ?? '');
$country_result = $conn->query("SELECT id FROM countries WHERE slug = '$slug' LIMIT 1");
if (!$country_result || $country_result->num_rows === 0) {
    die("Страна не найдена.");
}
$country_id = $country_result->fetch_assoc()['id'];

// Получаем валюты текущей страны
$sql_currencies = "SELECT id, name FROM c_currency WHERE country_id = $country_id ORDER BY order_id ASC";
$currencies = $conn->query($sql_currencies)->fetch_all(MYSQLI_ASSOC);

$currency_ids = array_column($currencies, 'id');
$issues_by_currency = [];
if (!empty($currency_ids)) {
    $sql_issues = "SELECT id, currency_id, name FROM c_issue WHERE currency_id IN (" . implode(',', $currency_ids) . ") ORDER BY order_id ASC";
    $result = $conn->query($sql_issues);
    while ($issue = $result->fetch_assoc()) {
        $issues_by_currency[$issue['currency_id']][] = $issue;
    }
}

$conn->close();
?>

<div class="fixed-nav">
    <div class="fixed-nav-links" id="navbar">
        <?php foreach ($currencies as $currency): ?>
            <!-- Выводим валюту -->
            <a data-scroll="currency-<?= $currency['id'] ?>" href="#currency-<?= $currency['id'] ?>" class="nav-line line-20"></a>
            
            <!-- Выводим выпуски для текущей валюты -->
            <?php if (!empty($issues_by_currency[$currency['id']])): ?>
                <?php foreach ($issues_by_currency[$currency['id']] as $issue): ?>
                    <a data-scroll="issue-<?= $issue['id'] ?>" href="#issue-<?= $issue['id'] ?>" class="nav-line line-12"></a>
                <?php endforeach; ?>
            <?php endif; ?>
        <?php endforeach; ?>
    </div>

    <div class="hover-box" style="display: none;">
        <div class="hover-block">
            <?php foreach ($currencies as $currency): ?>
                <div class="page-info">
                    <a href="#currency-<?= $currency['id'] ?>" class="fixed-nav-link">
                        <?= htmlspecialchars($currency['name'], ENT_QUOTES, 'UTF-8') ?>
                    </a>
                    
                    <!-- Выводим выпуски для текущей валюты -->
                    <?php if (!empty($issues_by_currency[$currency['id']])): ?>
                        <div class="page-inner-info">
                            <?php foreach ($issues_by_currency[$currency['id']] as $issue): ?>
                                <a href="#issue-<?= $issue['id'] ?>"><?= htmlspecialchars($issue['name'], ENT_QUOTES, 'UTF-8') ?></a>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>