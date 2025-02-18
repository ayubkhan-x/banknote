<div class="varieties-box">
    <?php
    // Получаем ID банкноты из GET-параметра
    $banknote_id = $_GET['id'] ?? '';

    if (!empty($banknote_id) && ctype_digit($banknote_id)) {
        // Получаем country_id для банкноты
        $stmt_country = $conn->prepare("
            SELECT country_id 
            FROM banknotes 
            WHERE id = ?
        ");
        $stmt_country->bind_param("i", $banknote_id);
        $stmt_country->execute();
        $result_country = $stmt_country->get_result();

        if ($result_country->num_rows > 0) {
            $country_row = $result_country->fetch_assoc();
            $country_id = $country_row['country_id'];

            // Получаем данные о вариациях
            $stmt = $conn->prepare("
                SELECT pick_num, description_long, img_front, img_back 
                FROM b_varieties 
                WHERE banknote_id = ? 
                ORDER BY order_id ASC
            ");
            $stmt->bind_param("i", $banknote_id);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    // Разделяем описание на дату выпуска и детали
                    $description = $row['description_long'] ?? ''; // Заменяем NULL на пустую строку
                    $descriptionParts = explode("\n", $description);
                    $issueDate = $descriptionParts[0] ?? '';
                    $details = $descriptionParts[1] ?? '';

                    ?>
                    <div class="varieties-item">
                        <div class="varieties-number"><?= htmlspecialchars($row['pick_num']) ?></div>
                        <div class="varieties-top">
                            <span><?= htmlspecialchars($issueDate) ?></span>
                            <span><?= htmlspecialchars($details) ?></span>
                            <img src="/images/signature.png" alt="Signature">
                        </div>

                        <div class="row">
                            <div class="col-xl-10">
                                <div class="row">
                                    <div class="col-lg-6">
                                        <div class="banknote-img">
                                            <img src="http://banknote.wiki/banknotes/<?= $country_id ?>/<?= htmlspecialchars($row['img_front']) ?>"
                                                alt="Front side">
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="banknote-img">
                                            <img src="http://banknote.wiki/banknotes/<?= $country_id ?>/<?= htmlspecialchars($row['img_back']) ?>"
                                                alt="Back side">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php
                }
            } else {
                echo '<div class="alert alert-info">No varieties found for this banknote.</div>';
            }
            $stmt->close();
        } else {
            echo '<div class="alert alert-danger">Country not found for this banknote.</div>';
        }
        $stmt_country->close();
    } else {
        echo '<div class="alert alert-danger">Invalid banknote ID.</div>';
    }
    ?>
</div>