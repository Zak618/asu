<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="../css/start.css">
</head>
<body>
<div class="container">
    <header class="navbar navbar-expand-lg navbar-light py-3 mb-4">
      <a class="navbar-brand" href="../start.php"><span class="fs-4"><span class="text-primary">Э</span>верест</span></a>
      <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse justify-content-center" id="navbarNav">
        <ul class="navbar-nav">
          <li class="nav-item">
            <a class="nav-link px-2 link-dark" href="../profile.php">Профиль</a>
          </li>
          <li class="nav-item">
            <a class="nav-link px-2 link-dark" href="#">Мероприятия</a>
          </li>
          <li class="nav-item">
            <a class="nav-link px-2 link-dark" href="#">Сертификаты</a>
          </li>
        </ul>
        <ul class="navbar-nav d-lg-none w-100 justify-content-center">
          <li class="nav-item">
            <?php if (isset($_SESSION['user_id'])): ?>
              <span class="navbar-text px-2">Баланс: <?php echo htmlspecialchars($_SESSION['balance']); ?> руб.</span>
            <?php else: ?>
              <a href="login.php" class="btn btn-outline-primary me-2">Войти</a>
            <?php endif; ?>
          </li>
        </ul>
      </div>
      <div class="d-none d-lg-block ms-auto">
        <?php if (isset($_SESSION['user_id'])): ?>
          <span class="navbar-text px-2">Баланс: <?php echo htmlspecialchars($_SESSION['balance']); ?> руб.</span>
        <?php else: ?>
          <a href="login.php" class="btn btn-outline-primary me-2">Войти</a>
        <?php endif; ?>
      </div>
    </header>
  </div>

