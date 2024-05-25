<?php
include_once "./base/header.php";


if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

?>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="profile">
                <h2 class="text-center mb-4">Профиль</h2>
                <p>Добро пожаловать, <?php echo htmlspecialchars($_SESSION['user_email']); ?>!</p>
                <!-- Дополнительная информация о пользователе может быть добавлена здесь -->
            </div>
        </div>
    </div>
</div>

<?php
include_once "./base/footer.php";
?>
