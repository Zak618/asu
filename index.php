<?php
// Получаем запрашиваемый путь
$request = $_SERVER['REQUEST_URI'];
$request = trim($request, '/');
$request = explode('?', $request, 2)[0];

// Обрабатываем маршруты
switch ($request) {
    case '':
    case 'start':
        require __DIR__ . '/start.php';
        break;
    case 'login':
        require __DIR__ . '/login.php';
        break;
    case 'register':
        require __DIR__ . '/register.php';
        break;
    case 'profile':
        require __DIR__ . '/profile.php';
        break;
    case 'events':
        require __DIR__ . '/events.php';
        break;
    case (preg_match('/^event\/(\d+)$/', $request, $matches) ? true : false):
        $_GET['id'] = $matches[1];
        require __DIR__ . '/event.php';
        break;
    case 'market':
        require __DIR__ . '/market.php';
        break;
    case 'teacher_coupons':
        require __DIR__ . '/teacher_coupons.php';
        break;
    case 'admin':
        require __DIR__ . '/admin.php';
        break;
    case 'admin_dashboard':
        require __DIR__ . '/admin_dashboard.php';
        break;
        // Добавьте другие маршруты по необходимости
    default:
        http_response_code(404);
        require __DIR__ . '/404.php';
        break;
}
