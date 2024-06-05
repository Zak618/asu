<?php
include_once "db.php";
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_SESSION['moderator_id'])) {
        echo json_encode(['success' => false, 'error' => 'Пользователь не авторизован']);
        exit;
    }

    if (!isset($_POST['certificate_id']) || !isset($_POST['status'])) {
        echo json_encode(['success' => false, 'error' => 'Отсутствуют необходимые данные']);
        exit;
    }

    $certificate_id = intval($_POST['certificate_id']);
    $status = $_POST['status'];
    $comment = isset($_POST['comment']) ? $_POST['comment'] : '';

    if ($status !== 'отклонено') {
        // Получаем информацию о сертификате и мероприятии, только если статус не "отклонено"
        $sql = "SELECT c.*, e.points_winner, e.points_prize, e.points_participant, u.id as user_id, u.balance 
                FROM certificate c 
                JOIN event e ON c.event_id = e.id 
                JOIN users u ON c.user_id = u.id 
                WHERE c.id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $certificate_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $certificate = $result->fetch_assoc();

        if (!$certificate) {
            echo json_encode(['success' => false, 'error' => 'Сертификат не найден']);
            exit;
        }

        $points_awarded = 0;
        if ($status === 'принято') {
            if ($certificate['place'] === 'победитель') {
                $points_awarded = $certificate['points_winner'];
            } elseif ($certificate['place'] === 'призер') {
                $points_awarded = $certificate['points_prize'];
            } elseif ($certificate['place'] === 'участник') {
                $points_awarded = $certificate['points_participant'];
            }
        }

        // Обновляем статус сертификата и количество начисленных баллов
        $sql = "UPDATE certificate SET moderator_status = ?, points_awarded = ?, moderator_comment = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sisi", $status, $points_awarded, $comment, $certificate_id);
    } else {
        // Обновляем только статус и комментарий, если статус "отклонено"
        $sql = "UPDATE certificate SET moderator_status = ?, moderator_comment = ? WHERE id = ?";
        $stmt->prepare($sql);
        $stmt->bind_param("ssi", $status, $comment, $certificate_id);
    }

    if ($stmt->execute()) {
        if ($status === 'принято') {
            // Обновляем баланс пользователя
            $new_balance = $certificate['balance'] + $points_awarded;
            $user_id = $certificate['user_id'];
            $sql = "UPDATE users SET balance = ? WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ii", $new_balance, $user_id);
            
            if ($stmt->execute()) {
                echo json_encode(['success' => true, 'balance' => $new_balance]);
            } else {
                echo json_encode(['success' => false, 'error' => 'Ошибка при обновлении баланса: ' . $stmt->error]);
            }
        } else {
            echo json_encode(['success' => true]);
        }
    } else {
        echo json_encode(['success' => false, 'error' => 'Ошибка при обновлении сертификата: ' . $stmt->error]);
    }

    $stmt->close();
} else {
    echo json_encode(['success' => false, 'error' => 'Некорректный метод запроса']);
}
?>
