<?php
ob_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

$host = 'mysql8';
$user = '39330634_banknotewiki';
$pass = 'eWgl1bn8';
$db_name = '39330634_banknotewiki';

$conn = new mysqli($host, $user, $pass, $db_name);

if ($conn->connect_errno) {
    die("Ошибка подключения: " . $conn->connect_error);
}

$conn->set_charset("utf8mb4");

$slug = isset($_GET['slug']) ? $conn->real_escape_string($_GET['slug']) : '';

if (!$slug) {
    die("Страна не найдена.");
}

$banknote_id = isset($banknote_id) ? (int) $banknote_id : 0; // Проверяем, был ли передан ID банкноты


$sql = "SELECT v.id, v.banknote_id, v.order_id, v.pick_num, 
               v.img_front, v.img_back, 
               v.description_short, v.description_long, v.issue_date, 
               c.id AS country_id 
        FROM b_varieties v 
        JOIN banknotes b ON v.banknote_id = b.id 
        JOIN countries c ON b.country_id = c.id 
        WHERE c.slug = '$slug' " . ($banknote_id ? " AND v.banknote_id = $banknote_id" : "") . "
        ORDER BY v.order_id ASC";

$result = $conn->query($sql);
$varieties = $result->fetch_all(MYSQLI_ASSOC);

$conn->close();
?>

<table class="table">
    <thead>
        <tr>
            <th>Image</th>
            <th>Variation</th>
            <th>Date</th>
            <th>Description</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($varieties as $variety): ?>
            <tr>
                <td>
                    <div class="table-img">
                        <img src="/banknotes/<?= htmlspecialchars($variety['country_id']) ?>/<?= htmlspecialchars($variety['img_front']) ?>"
                            alt="">
                        <img src="/banknotes/<?= htmlspecialchars($variety['country_id']) ?>/<?= htmlspecialchars($variety['img_back']) ?>"
                            alt="">
                        <div class="modal-body">
                            <div class="modal-top row">
                                <div class="col-lg-2">
                                    <div id="thumbnail-container" class="mod-img">
                                        <img src="/banknotes/<?= htmlspecialchars($variety['country_id']) ?>/<?= htmlspecialchars($variety['img_front']) ?>"
                                            alt="" class="thumbnail"
                                            data-large="/banknotes/<?= htmlspecialchars($variety['country_id']) ?>/<?= htmlspecialchars($variety['img_front']) ?>">
                                        <img src="/banknotes/<?= htmlspecialchars($variety['country_id']) ?>/<?= htmlspecialchars($variety['img_back']) ?>"
                                            alt="" class="thumbnail"
                                            data-large="/banknotes/<?= htmlspecialchars($variety['country_id']) ?>/<?= htmlspecialchars($variety['img_back']) ?>">
                                    </div>

                                </div>
                                <div class="col-lg-2">
                                    <span class="mod-type"><?= htmlspecialchars($variety['pick_num']) ?? '' ?></span>
                                </div>
                                <div class="col-lg-2">
                                    <span class="mod-date"><?= htmlspecialchars($variety['issue_date']) ?? '' ?></span>
                                </div>
                                <div class="col-lg-6">
                                    <span
                                        class="mod-desc"><?= htmlspecialchars($variety['description_short']) ?? '' ?></span>
                                </div>
                            </div>
                            <div class="image-popup hidden">
                                <img class="image-viewer"
                                    src="/banknotes/<?= htmlspecialchars($variety['country_id']) ?>/<?= htmlspecialchars($variety['img_front']) ?>"
                                    alt="">
                            </div>
                        </div>
                    </div>
                </td>
                <td><?= htmlspecialchars($variety['pick_num']) ?? '' ?></td>
                <td><?= htmlspecialchars($variety['issue_date']) ?? '' ?></td>
                <td class="desc-hover" data-desc="<?= htmlspecialchars($variety['description_long']) ?? '' ?>">
                    <?= htmlspecialchars($variety['description_short']) ?? '' ?>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>