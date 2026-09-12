<?php
namespace App\Controllers;

class AuthController
{
   public function loginForm(): void
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    $flash = $_SESSION['flash'] ?? null;
    unset($_SESSION['flash']);

    echo '<div style="max-width:400px;margin:50px auto;padding:20px;border:1px solid #ccc;border-radius:8px;font-family:sans-serif;">';
    echo "<h1>Sistem Informasi Akademik</h1>";
    if ($flash) {
        echo "<div style='padding:10px;background:#f8d7da;color:#721c24;margin-bottom:10px;border-radius:4px;'>$flash</div>";
    }

    echo '<form method="POST" action="' . BASE_PATH . '/login">';
    echo '<p>Username: <input type="text" name="username" style="width:100%;padding:8px;box-sizing:border-box;"></p>';
    echo '<p>Password: <input type="password" name="password" style="width:100%;padding:8px;box-sizing:border-box;"></p>';
    echo '<button type="submit" style="width:100%;padding:10px;background:#4CAF50;color:white;border:none;border-radius:4px;cursor:pointer;">Login</button>';
    echo '</form>';
    echo '</div>';
}

    public function login(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $username = $_POST['username'] ?? '';
        $password = $_POST['password'] ?? '';

        // Simulasi login hardcode dulu (nanti Acara 7/8 diganti cek ke database)
        if ($username === 'admin' && $password === 'admin123') {
            $_SESSION['logged_in'] = true;
            $_SESSION['user_name'] = 'Admin';
            $_SESSION['flash'] = 'Selamat datang, Admin';

            redirect('/dashboard');
        }

        echo "Login gagal, username/password salah.";
    }

    public function logout(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        session_destroy();

        // mulai session baru, cuma untuk bawa pesan flash ke halaman login
        session_start();
        $_SESSION['flash'] = 'Anda telah logout';

        redirect('/login');
    }

    public function dashboard(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $flash = $_SESSION['flash'] ?? null;
        unset($_SESSION['flash']);

        if ($flash) {
            echo "<div style='padding:10px;background:#d4edda;color:#155724;margin-bottom:10px;'>$flash</div>";
        }

        echo "Selamat datang di Dashboard, " . ($_SESSION['user_name'] ?? 'User');
        echo '<br><br><a href="' . BASE_PATH . '/logout">Logout</a>';
    }
}