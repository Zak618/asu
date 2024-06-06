<?php
include_once "db.php";

function check_user_data($user_id) {
    global $conn;
    
    $data = [];

    // Проверка количества участий
    $sql = "SELECT COUNT(*) as participation_count FROM event_participation WHERE student_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $data['participation_count'] = $row['participation_count'];
    $stmt->close();

    // Проверка сертификатов
    $sql_certificate = "SELECT * FROM certificate WHERE user_id = ? AND place = 'участник' AND moderator_status = 'принято'";
    $stmt_certificate = $conn->prepare($sql_certificate);
    $stmt_certificate->bind_param("i", $user_id);
    $stmt_certificate->execute();
    $result_certificate = $stmt_certificate->get_result();
    $data['certificate_accepted'] = $result_certificate->num_rows > 0;
    $stmt_certificate->close();

    // Проверка призёров
    $sql_prizer = "SELECT * FROM certificate WHERE user_id = ? AND place = 'призёр' AND moderator_status = 'принято'";
    $stmt_prizer = $conn->prepare($sql_prizer);
    $stmt_prizer->bind_param("i", $user_id);
    $stmt_prizer->execute();
    $result_prizer = $stmt_prizer->get_result();
    $data['prizer_accepted'] = $result_prizer->num_rows > 0;
    $stmt_prizer->close();

    // Проверка количества призёров
    $sql_prizer_count = "SELECT COUNT(*) as prizer_count FROM certificate WHERE user_id = ? AND place = 'призёр' AND moderator_status = 'принято'";
    $stmt_prizer_count = $conn->prepare($sql_prizer_count);
    $stmt_prizer_count->bind_param("i", $user_id);
    $stmt_prizer_count->execute();
    $result_prizer_count = $stmt_prizer_count->get_result();
    $row_prizer_count = $result_prizer_count->fetch_assoc();
    $data['prizer_count'] = $row_prizer_count['prizer_count'];
    $stmt_prizer_count->close();

    // Проверка победителей
    $sql_winner = "SELECT * FROM certificate WHERE user_id = ? AND place = 'победитель' AND moderator_status = 'принято'";
    $stmt_winner = $conn->prepare($sql_winner);
    $stmt_winner->bind_param("i", $user_id);
    $stmt_winner->execute();
    $result_winner = $stmt_winner->get_result();
    $data['winner_accepted'] = $result_winner->num_rows > 0;
    $stmt_winner->close();

    // Проверка количества активированных купонов
    $sql_activated_coupons = "SELECT COUNT(*) as activated_count FROM history_market WHERE user_id = ? AND status = 'неактивно'";
    $stmt_activated_coupons = $conn->prepare($sql_activated_coupons);
    $stmt_activated_coupons->bind_param("i", $user_id);
    $stmt_activated_coupons->execute();
    $result_activated_coupons = $stmt_activated_coupons->get_result();
    $row_activated_coupons = $result_activated_coupons->fetch_assoc();
    $data['activated_count'] = $row_activated_coupons['activated_count'];
    $stmt_activated_coupons->close();

    // Проверка количества победителей
    $sql_winner_count = "SELECT COUNT(*) as winner_count FROM certificate WHERE user_id = ? AND place = 'победитель' AND moderator_status = 'принято'";
    $stmt_winner_count = $conn->prepare($sql_winner_count);
    $stmt_winner_count->bind_param("i", $user_id);
    $stmt_winner_count->execute();
    $result_winner_count = $stmt_winner_count->get_result();
    $row_winner_count = $result_winner_count->fetch_assoc();
    $data['winner_count'] = $row_winner_count['winner_count'];
    $stmt_winner_count->close();

    return $data;
}
?>
