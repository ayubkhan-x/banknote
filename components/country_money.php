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

// Получаем параметры фильтра
$selected_eras = isset($_GET['era']) ? array_map('intval', $_GET['era']) : [];
$selected_currencies = isset($_GET['currency']) ? array_map('intval', $_GET['currency']) : [];
$demonetized = $_GET['demonetized'] ?? 'all';
$commemorative = $_GET['commemorative'] ?? 'all';

// Формируем условия запроса
$where = ["c.country_id = $country_id"];
if (!empty($selected_eras)) {
    $where[] = "c.era_id IN (" . implode(',', $selected_eras) . ")";
}
if (!empty($selected_currencies)) {
    $where[] = "c.id IN (" . implode(',', $selected_currencies) . ")";
}

$sql_currencies = "SELECT c.* FROM c_currency c WHERE " . implode(' AND ', $where) . " ORDER BY c.order_id ASC";
$currencies = $conn->query($sql_currencies)->fetch_all(MYSQLI_ASSOC);

$currency_ids = array_column($currencies, 'id');
$issues = [];
if (!empty($currency_ids)) {
    $issues = $conn->query("SELECT * FROM c_issue WHERE currency_id IN (" . implode(',', $currency_ids) . ") ORDER BY order_id ASC")
        ->fetch_all(MYSQLI_ASSOC);
}

$banknotes = [];
$issue_ids = array_column($issues, 'id');
if (!empty($issue_ids)) {
    $banknote_where = ["issue_id IN (" . implode(',', $issue_ids) . ")"];
    if ($demonetized !== 'all') {
        $banknote_where[] = "is_demonetized = " . ($demonetized === 'yes' ? 1 : 0);
    }
    if ($commemorative !== 'all') {
        $banknote_where[] = "is_commemorative = " . ($commemorative === 'yes' ? 1 : 0);
    }
    $sql_banknotes = "SELECT * FROM banknotes WHERE " . implode(' AND ', $banknote_where) . " ORDER BY order_id ASC";
    $banknotes = $conn->query($sql_banknotes)->fetch_all(MYSQLI_ASSOC);
}

$conn->close();
?>
<div class="tab-content" id="pills-tabContent">
    <div class="tab-pane fade show active" id="pills-full" role="tabpanel">
        <div class="tab-block">

            <?php foreach ($currencies as $currency): ?>
                <div class="tab-title spy" id="currency-<?= $currency['id']; ?>">
                    <h3 class="title-tab">
                        <?= htmlspecialchars($currency['name'] ?? '', ENT_QUOTES, 'UTF-8'); ?>

                    </h3>
                </div>
                <?php if (!empty($currency['ext_link'])): ?>

                    <div class="note ext-link-block">
                        <a href="<?= htmlspecialchars($currency['ext_link'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                            <img src="/images/clip-icon.svg" alt="">
                            <?= htmlspecialchars($currency['ext_link_text'] ?? '', ENT_QUOTES, 'UTF-8'); ?>
                        </a>
                    </div>
                <?php endif; ?>
                <?php if (empty($currency['ext_link'])): ?>
                    <?php foreach ($issues as $issue): ?>
                        <?php if ($issue['currency_id'] == $currency['id']): ?>
                            <div class="full-box">
                                <span class="full-title spy" id="issue-<?= $issue['id']; ?>">
                                    <?= htmlspecialchars($issue['name'] ?? '', ENT_QUOTES, 'UTF-8'); ?>
                                </span>

                                <?php foreach ($banknotes as $banknote): ?>
                                    <?php if ($banknote['issue_id'] == $issue['id']): ?>
                                        <div class="full-item">
                                            <div class="full-img">
                                                <div class="full-money-img">
                                                    <span>
                                                        <img width="222"
                                                            src="/banknotes/<?= $banknote['country_id']; ?>/<?= htmlspecialchars($banknote['img_front'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                                                            alt="">
                                                    </span>
                                                    <span>
                                                        <img width="222"
                                                            src="/banknotes/<?= $banknote['country_id']; ?>/<?= htmlspecialchars($banknote['img_back'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                                                            alt="">
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="full-block">
                                                <div class="money-name">
                                                    <?php if (!empty($banknote['ref1'])): ?>
                                                        <span><?= htmlspecialchars($banknote['ref1'] ?? '', ENT_QUOTES, 'UTF-8'); ?></span>
                                                    <?php endif; ?>

                                                    <?php if (!empty($banknote['ref2'])): ?>
                                                        <span><?= htmlspecialchars($banknote['ref2'] ?? '', ENT_QUOTES, 'UTF-8'); ?></span>
                                                    <?php endif; ?>

                                                    <?php if (!empty($banknote['ref3'])): ?>
                                                        <span><?= htmlspecialchars($banknote['ref3'] ?? '', ENT_QUOTES, 'UTF-8'); ?></span>
                                                    <?php endif; ?>

                                                    <a href="#"> <?= htmlspecialchars($banknote['name'] ?? '', ENT_QUOTES, 'UTF-8'); ?> </a>
                                                </div>
                                                <div class="money-type">
                                                    <span>
                                                        <?= $banknote['is_commemorative'] ? 'Commemorative banknote' : 'Standart banknote'; ?>
                                                    </span>
                                                </div>
                                                <div class="money-issued">
                                                    <span><?= htmlspecialchars($banknote['issuer'] ?? '', ENT_QUOTES, 'UTF-8'); ?> </span>
                                                </div>
                                                <?php
                                                $banknote_id = $banknote['id'];
                                                include "components/country_table.php";
                                                ?>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    <?php endforeach; ?>
                <?php endif; ?>
            <?php endforeach; ?>


        </div>
    </div>


    <div class="tab-pane fade" id="pills-grid" role="tabpanel" aria-labelledby="pills-grid-tab">
        <div class="tab-block">

            <?php foreach ($currencies as $currency): ?>
                <!-- Header Currency -->
                <div id="soviet" class="tab-title spy" id="currency-<?= $currency['id']; ?>">
                    <h3 class="title-tab">
                        <?= htmlspecialchars($currency['name'] ?? '', ENT_QUOTES, 'UTF-8'); ?>
                    </h3>
                </div>
                <?php if (!empty($currency['ext_link'])): ?>
                    <div class="note">
                        <a href="<?= htmlspecialchars($currency['ext_link'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                            <?= htmlspecialchars($currency['ext_link_text'] ?? '', ENT_QUOTES, 'UTF-8'); ?>
                        </a>
                    </div>
                <?php endif; ?>
                <?php foreach ($issues as $issue): ?>
                    <?php if ($issue['currency_id'] == $currency['id']): ?>
                        <!-- Header Issue -->
                        <div class="grid-box">
                            <span class="full-title spy" id="issue-<?= $issue['id']; ?>">
                                <?= htmlspecialchars($issue['name'] ?? '', ENT_QUOTES, 'UTF-8'); ?>
                            </span>
                            <div class="row grid-rov">

                                <?php foreach ($banknotes as $banknote): ?>
                                    <?php if ($banknote['issue_id'] == $issue['id']): ?>
                                        <!-- Item -->
                                        <div class="col-xl-3 col-lg-4 col-md-6">
                                            <a href="#!" class="grid-item">
                                                <span class="grid-img">
                                                    <img src="/banknotes/<?= htmlspecialchars($banknote['country_id']); ?>/<?= htmlspecialchars($banknote['img_front'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                                                        alt="">
                                                    <img src="/banknotes/<?= htmlspecialchars($banknote['country_id']); ?>/<?= htmlspecialchars($banknote['img_back'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                                                        alt="">
                                                </span>

                                                <div style="display:flex; margin-right: 8px;">
                                                    <?php if (!empty($banknote['ref1'])): ?>
                                                        <span
                                                            class="grid-tag"><?= htmlspecialchars($banknote['ref1'] ?? '', ENT_QUOTES, 'UTF-8'); ?></span>
                                                    <?php endif; ?>
                                                    <?php if (!empty($banknote['ref2'])): ?>
                                                        <span
                                                            class="grid-tag"><?= htmlspecialchars($banknote['ref2'] ?? '', ENT_QUOTES, 'UTF-8'); ?></span>
                                                    <?php endif; ?>
                                                    <?php if (!empty($banknote['ref3'])): ?>
                                                        <span
                                                            class="grid-tag"><?= htmlspecialchars($banknote['ref3'] ?? '', ENT_QUOTES, 'UTF-8'); ?></span>
                                                    <?php endif; ?>
                                                </div>

                                                <span class="grid-text">
                                                    <span
                                                        class="grid-name"><?= htmlspecialchars($banknote['name'] ?? '', ENT_QUOTES, 'UTF-8'); ?></span>
                                                    <br>
                                                    <span class="grid-types">
                                                        <?php if (isset($varieties_grouped[$banknote['id']])): ?>
                                                            <?php foreach ($varieties_grouped[$banknote['id']] as $variety): ?>
                                                                <span
                                                                    class="grid-type"><?= htmlspecialchars($variety['pick_num'] ?? '', ENT_QUOTES, 'UTF-8') ?></span>
                                                            <?php endforeach; ?>
                                                        <?php endif; ?>
                                                    </span>

                                                </span>
                                            </a>
                                        </div>
                                    <?php endif; ?>
                                <?php endforeach; ?>

                            </div>
                        </div>
                    <?php endif; ?>
                <?php endforeach; ?>

            <?php endforeach; ?>

        </div>
    </div>
</div>