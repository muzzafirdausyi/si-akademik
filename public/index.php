<?php
spl_autoload_register(function (string $class): void {
    $prefix = 'App\\';
    if (!str_starts_with($class, $prefix)) {
        return;
    }
    $relative = substr($class, strlen($prefix));
    $path = __DIR__ . '/../app/' . str_replace('\\', '/', $relative) . '.php';

    if (file_exists($path)) {
        require $path;
    }
});

require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../routes/web.php';

define('BASE_PATH', '/si-akademik/public');

function redirect(string $path): void
{
    header('Location: ' . BASE_PATH . $path);
    exit;
}

function runMiddleware(array $middlewareList): void
{
    foreach ($middlewareList as $mw) {
        $mwInstance = new $mw();
        $mwInstance->handle();
    }
}

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

if (str_starts_with($uri, BASE_PATH)) {
    $uri = substr($uri, strlen(BASE_PATH)) ?: '/';
}

$method = $_SERVER['REQUEST_METHOD'];

$authMiddleware = ['App\Core\Middleware\AuthMiddleware'];

// ------------------------------------------------------------------
// 1) Route statis (persis sama), dari routes/web.php
// ------------------------------------------------------------------
if (isset($routes[$method][$uri])) {
    $route = $routes[$method][$uri];
    $controllerName = $route[0];
    $action = $route[1];

    runMiddleware($route['middleware'] ?? []);

    $controllerClass = "App\\Controllers\\{$controllerName}";
    $controller = new $controllerClass();
    $controller->$action();

} else {
    // ------------------------------------------------------------------
    // 2) Route dinamis: /mahasiswa/{id}, /prodi/{id}/edit, dst
    // ------------------------------------------------------------------
    $segments = explode('/', trim($uri, '/'));

    if ($method === 'GET' && count($segments) === 2
        && $segments[0] === 'mahasiswa'
        && ctype_digit($segments[1])) {

        // GET /mahasiswa/5
        runMiddleware($authMiddleware);
        (new App\Controllers\MahasiswaController())->show((int) $segments[1]);

    } elseif ($method === 'GET' && count($segments) === 3
        && $segments[0] === 'mahasiswa'
        && ctype_digit($segments[1])
        && $segments[2] === 'edit') {

        // GET /mahasiswa/5/edit
        runMiddleware($authMiddleware);
        (new App\Controllers\MahasiswaController())->edit((int) $segments[1]);

    } elseif ($method === 'POST' && count($segments) === 3
        && $segments[0] === 'mahasiswa'
        && ctype_digit($segments[1])
        && $segments[2] === 'update') {

        // POST /mahasiswa/5/update
        runMiddleware($authMiddleware);
        (new App\Controllers\MahasiswaController())->update((int) $segments[1]);

    } elseif ($method === 'POST' && count($segments) === 3
        && $segments[0] === 'mahasiswa'
        && ctype_digit($segments[1])
        && $segments[2] === 'delete') {

        // POST /mahasiswa/5/delete
        runMiddleware($authMiddleware);
        (new App\Controllers\MahasiswaController())->destroy((int) $segments[1]);

    } elseif ($method === 'GET' && count($segments) === 3
        && $segments[0] === 'prodi'
        && ctype_digit($segments[1])
        && $segments[2] === 'edit') {

        // GET /prodi/{id}/edit
        runMiddleware($authMiddleware);
        (new App\Controllers\ProdiController())->edit((int) $segments[1]);

    } elseif ($method === 'POST' && count($segments) === 3
        && $segments[0] === 'prodi'
        && ctype_digit($segments[1])
        && $segments[2] === 'update') {

        // POST /prodi/{id}/update
        runMiddleware($authMiddleware);
        (new App\Controllers\ProdiController())->update((int) $segments[1]);

    } elseif ($method === 'POST' && count($segments) === 3
        && $segments[0] === 'prodi'
        && ctype_digit($segments[1])
        && $segments[2] === 'delete') {

        // POST /prodi/{id}/delete
        runMiddleware($authMiddleware);
        (new App\Controllers\ProdiController())->destroy((int) $segments[1]);

    } elseif ($method === 'GET' && count($segments) === 3
        && $segments[0] === 'matakuliah'
        && ctype_digit($segments[1])
        && $segments[2] === 'edit') {

        // GET /matakuliah/{id}/edit
        runMiddleware($authMiddleware);
        (new App\Controllers\MatakuliahController())->edit((int) $segments[1]);

    } elseif ($method === 'POST' && count($segments) === 3
        && $segments[0] === 'matakuliah'
        && ctype_digit($segments[1])
        && $segments[2] === 'update') {

        // POST /matakuliah/{id}/update
        runMiddleware($authMiddleware);
        (new App\Controllers\MatakuliahController())->update((int) $segments[1]);

    } elseif ($method === 'POST' && count($segments) === 3
        && $segments[0] === 'matakuliah'
        && ctype_digit($segments[1])
        && $segments[2] === 'delete') {

        // POST /matakuliah/{id}/delete
        runMiddleware($authMiddleware);
        (new App\Controllers\MatakuliahController())->destroy((int) $segments[1]);

    } else {
        http_response_code(404);
        echo "404 - Halaman tidak ditemukan";
    }
}