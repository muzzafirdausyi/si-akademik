<?php

use App\Core\Router;

// Buat instance Router bawaan project kamu
$router = new Router();

// Route Login
$router->add('GET', '/login', 'AuthController@loginForm');
$router->add('POST', '/login', 'AuthController@login');
$router->add('POST', '/logout', 'AuthController@logout');

// Route Terproteksi (Dashboard & Mahasiswa)
$router->add('GET', '/dashboard', 'HomeController@dashboard', ['AuthMiddleware']);
$router->add('GET', '/mahasiswa', 'MahasiswaController@index', ['AuthMiddleware']);