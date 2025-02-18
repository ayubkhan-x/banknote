<div class="description-block">
    <?php
    // Используем уже полученный ранее ID банкноты
    if (!empty($banknote_id) && ctype_digit($banknote_id)) {
        // Запрос к таблице с описанием
        $stmt_desc = $conn->prepare("
            SELECT description 
            FROM b_description 
            WHERE banknote_id = ?
        ");
        $stmt_desc->bind_param("i", $banknote_id);
        $stmt_desc->execute();
        $result_desc = $stmt_desc->get_result();

        if ($result_desc->num_rows > 0) {
            $desc_row = $result_desc->fetch_assoc();
            // Выводим HTML-содержимое как есть
            echo $desc_row['description'];
        } else {
            echo '<div class="alert alert-info">Описание отсутствует</div>';
        }
        $stmt_desc->close();
    } else {
        echo '<div class="alert alert-danger">Неверный ID банкноты</div>';
    }
    ?>
</div>