<div class="watermark-box">
    <?php
    // Проверяем, что ID банкноты корректный
    if (!empty($banknote_id) && ctype_digit($banknote_id)) {
        // Запрос к таблице с водяными знаками
        $stmt_watermark = $conn->prepare("
            SELECT watermark_type, image, comment 
            FROM b_watermark 
            WHERE banknote_id = ? 
            ORDER BY id ASC
        ");
        $stmt_watermark->bind_param("i", $banknote_id);
        $stmt_watermark->execute();
        $result_watermark = $stmt_watermark->get_result();

        if ($result_watermark->num_rows > 0) {
            while ($watermark_row = $result_watermark->fetch_assoc()) {
                // Заменяем null на пустую строку
                $watermark_type = htmlspecialchars($watermark_row['watermark_type'] ?? '');
                $image = htmlspecialchars($watermark_row['image'] ?? '');
                $comment = htmlspecialchars($watermark_row['comment'] ?? '');
                ?>
                <div class="watermark-item">
                    <div class="watermark-name"><?= $watermark_type ?></div>
                    <div class="watermark-signature">
                        <?php if (!empty($comment)): ?>
                            <img src="/images/signature.png" alt="Signature">
                        <?php endif; ?>
                    </div>

                    <div class="row">
                        <div class="col-lg-5">
                            <div class="watermark-img">
                                <?php if (!empty($image)): ?>
                                    <img src="http://banknote.wiki/banknotes/<?= $country_id ?>/<?= $image ?>" alt="<?= $watermark_type ?>">
                                <?php else: ?>
                                    <img src="/images/modal-money-front.png" alt="Placeholder">
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
                <?php
            }
        } else {
            echo '<div class="alert alert-info">Нет данных о водяных знаках для этой банкноты.</div>';
        }
        $stmt_watermark->close();
    } else {
        echo '<div class="alert alert-danger">Неверный ID банкноты.</div>';
    }
    ?>
</div>