<?php
// Функция для безопасного вывода данных
function safe_output($data) {
    return htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
}

// Функция для валидации и нормализации путей
function validate_path($path) {
    $path = trim($path, '/');
    $path = explode('?', $path, 2)[0];
    $path = filter_var($path, FILTER_SANITIZE_URL);
    return $path;
}


// Получаем запрашиваемый путь
$request = validate_path($_SERVER['REQUEST_URI']);

// Проверка на наличие расширения .php в запросе
if (preg_match('/\.php$/', $request)) {
    header('HTTP/1.0 403 Forbidden');
    echo 'Access denied';
    exit;
}

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
        $_GET['id'] = intval($matches[1]);
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
?>
