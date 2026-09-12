<?php
$authMiddleware = ['App\Core\Middleware\AuthMiddleware'];

$routes = [
    'GET' => [
        '/'                      => ['HomeController', 'index'],
        '/login'                 => ['AuthController', 'loginForm'],
        '/logout'                => ['AuthController', 'logout'],
        '/dashboard'             => ['AuthController', 'dashboard', 'middleware' => $authMiddleware],

        '/mahasiswa'             => ['MahasiswaController', 'index', 'middleware' => $authMiddleware],
        '/mahasiswa/create'      => ['MahasiswaController', 'create', 'middleware' => $authMiddleware],

        '/prodi'                 => ['ProdiController', 'index', 'middleware' => $authMiddleware],
        '/prodi/create'          => ['ProdiController', 'create', 'middleware' => $authMiddleware],

        '/matakuliah'            => ['MatakuliahController', 'index', 'middleware' => $authMiddleware],
        '/matakuliah/create'     => ['MatakuliahController', 'create', 'middleware' => $authMiddleware],
    ],
    'POST' => [
        '/login'      => ['AuthController', 'login'],

        '/mahasiswa'  => ['MahasiswaController', 'store', 'middleware' => $authMiddleware],
        '/prodi'      => ['ProdiController', 'store', 'middleware' => $authMiddleware],
        '/matakuliah' => ['MatakuliahController', 'store', 'middleware' => $authMiddleware],
    ],
];