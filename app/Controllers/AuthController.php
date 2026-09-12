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

        if ($flash) {
            echo "<div style='padding:10px;background:#f8d7da;color:#721c24;margin-bottom:10px;'>$flash</div>";
        }

        echo '
            <form method="POST" action="' . BASE_PATH . '/login">
                <label>Username: <input type="text" name="username"></label><br>
                <label>Password: <input type="password" name="password"></label><br>
                <button type="submit">Login</button>
            </form>
        ';
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