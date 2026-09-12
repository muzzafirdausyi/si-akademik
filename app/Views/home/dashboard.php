<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dashboard</title>
</head>
<body>
    <h1>Selamat Datang di Dashboard, <?= $_SESSION['user'] ?? 'User'; ?>!</h1>
    <p>Ini adalah halaman utama setelah berhasil login.</p>

    <!-- Tombol Navigasi ke Mahasiswa -->
    <a href="/si-akademik/public/mahasiswa">Ke Halaman Data Mahasiswa</a>
    
    <br><br>
    
    <!-- Form Logout -->
    <form action="/si-akademik/public/logout" method="POST">
        <button type="submit">Logout</button>
    </form>
</body>
</html>