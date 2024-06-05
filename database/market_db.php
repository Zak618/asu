<?php
include_once "db.php";

function getMarketItems($conn) {
    $sql = "SELECT * FROM market";
    $result = $conn->query($sql);
    $items = [];
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $items[] = $row;
        }
    }
    return $items;
}

function getRandomColorClass() {
    $colors = ['red', 'yellow', 'blue', 'pink', 'dark-blue'];
    return $colors[array_rand($colors)];
}

function addMarketItem($item_name, $price, $conn) {
    $color_class = getRandomColorClass();
    $sql = "INSERT INTO market (item_name, price, color_class) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sis", $item_name, $price, $color_class);
    return $stmt->execute();
}

function updateMarketItem($id, $item_name, $price, $conn) {
    $sql = "UPDATE market SET item_name = ?, price = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sii", $item_name, $price, $id);
    return $stmt->execute();
}

function deleteMarketItem($id, $conn) {
    $sql = "DELETE FROM market WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    return $stmt->execute();
}
?>
