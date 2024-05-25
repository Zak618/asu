<?php

$servername = 'localhost';
$username = 'root';
$password = '';
$dbname = 'asu';

$conn = mysqli_connect($servername, $username, $password, $dbname);


if (!$conn) {
    die("Ошибка подключения");
}

?>