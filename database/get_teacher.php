<?php
// Получаем заблокированные IP-адреса
$blocked_ips = include 'blocked_ips.php';

// Проверка IP-адреса
if (in_array($_SERVER['REMOTE_ADDR'], $blocked_ips)) {
    header('HTTP/1.0 403 Forbidden');
    echo 'Access denied';
    exit;
}

// Запрет прямого доступа к файлу
if (!defined('ACCESS_ALLOWED')) {
    // Добавляем IP-адрес в заблокированные
    $blocked_ips[] = $_SERVER['REMOTE_ADDR'];
    file_put_contents('blocked_ips.php', '<?php return ' . var_export($blocked_ips, true) . ';');
    
    header('HTTP/1.0 403 Forbidden');
    echo 'Access denied';
    exit;
}


include_once "db.php";


if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $sql = "SELECT id, first_name, last_name, middle_name, email, phone_number FROM users WHERE id = ? AND role = 2";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $teacher = $result->fetch_assoc();
    echo json_encode($teacher);
}
?>
