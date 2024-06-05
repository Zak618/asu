<?php
session_start();

include_once "db.php";

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Пользователь не авторизован']);
    exit();
}

$user_id = $_SESSION['user_id'];
$item_id = $_POST['item_id'];
$date = date('Y-m-d H:i:s');
$status = 'активно';

// Получение текущего баланса пользователя
$get_balance_sql = "SELECT balance FROM users WHERE id = ?";
$get_balance_stmt = $conn->prepare($get_balance_sql);
$get_balance_stmt->bind_param("i", $user_id);
$get_balance_stmt->execute();
$get_balance_result = $get_balance_stmt->get_result();
$user_balance = $get_balance_result->fetch_assoc()['balance'];

// Получение стоимости купона из таблицы market
$get_price_sql = "SELECT price FROM market WHERE id = ?";
$get_price_stmt = $conn->prepare($get_price_sql);
$get_price_stmt->bind_param("i", $item_id);
$get_price_stmt->execute();
$get_price_result = $get_price_stmt->get_result();
$item_price = $get_price_result->fetch_assoc()['price'];

// Проверка, хватает ли у пользователя баллов для покупки купона
if ($user_balance < $item_price) {
    echo json_encode(['success' => false, 'message' => 'Недостаточно баллов для покупки купона']);
    exit();
}

// Вычитание стоимости купона из баланса пользователя
$new_balance = $user_balance - $item_price;

// Обновление баланса пользователя в базе данных
$update_balance_sql = "UPDATE users SET balance = ? WHERE id = ?";
$update_balance_stmt = $conn->prepare($update_balance_sql);
$update_balance_stmt->bind_param("ii", $new_balance, $user_id);
$update_balance_stmt->execute();

// Обновление баланса в сессии
$_SESSION['balance'] = $new_balance;

// Вставка записи о покупке купона в таблицу history_market
$sql = "INSERT INTO history_market (user_id, item_id, purchase_date, status) VALUES (?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("iiss", $user_id, $item_id, $date, $status);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Купон успешно приобретен', 'new_balance' => $new_balance]);
} else {
    echo json_encode(['success' => false, 'message' => 'Ошибка при покупке купона']);
}
?>
