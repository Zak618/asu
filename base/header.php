<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
  <link rel="stylesheet" href="../css/start.css">
  <link rel="stylesheet" href="../css/profile.css">
  <link rel="stylesheet" href="../css/events.css">
  <link rel="stylesheet" href="../css/market.css">
</head>

<body>
  <div class="container">
    <header class="navbar navbar-expand-lg navbar-light py-3 mb-4">
      <a class="navbar-brand" href="../start"><span class="fs-4"><span class="text-primary">Э</span>верест</span></a>
      <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse justify-content-center" id="navbarNav">
        <ul class="navbar-nav">
          <?php if (isset($_SESSION['user_id']) && $_SESSION['role'] == 1) : ?>
            <li class="nav-item">
              <a class="nav-link px-2 link-dark" href="../profile">Профиль</a>
            </li>
            <li class="nav-item">
              <a class="nav-link px-2 link-dark" href="../events">Мероприятия</a>
            </li>
            <li class="nav-item">
              <a class="nav-link px-2 link-dark" href="../market">Маркет</a>
            </li>
          <?php endif; ?>
        </ul>
        <ul class="navbar-nav d-lg-none w-100 justify-content-center">
          <li class="nav-item">
            <?php if (isset($_SESSION['user_id'])) : ?>
              <div class="balance-container">
                <?php if ($_SESSION['role'] == 1) : ?>
                  <span class="balance-text"><?php echo htmlspecialchars($_SESSION['balance']); ?></span>
                <?php endif; ?>
                <a href="logout.php" class="btn btn-outline-danger me-2">Выйти</a>
              </div>
            <?php else : ?>
              <a href="login.php" class="btn btn-outline-primary me-2">Войти</a>
            <?php endif; ?>
          </li>
        </ul>
      </div>
      <div class="d-none d-lg-block ms-auto">
        <?php if (isset($_SESSION['user_id'])) : ?>
          <div class="balance-container">
            <?php if ($_SESSION['role'] == 1) : ?>
              <span class="balance-text mr-10"><?php echo htmlspecialchars($_SESSION['balance']); ?></span>
            <?php endif; ?>
            <a href="logout.php" class="btn btn-outline-danger me-2">Выйти</a>
          </div>
        <?php else : ?>
          <a href="login.php" class="btn btn-outline-primary me-2">Войти</a>
        <?php endif; ?>
      </div>
    </header>
  </div>
</body>
</html>
