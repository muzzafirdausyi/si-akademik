<?php
namespace App\Controllers;

use App\Core\Controller;   //ditambahkan ini untuk acara 10
use App\Repositories\MahasiswaRepository;
use App\Repositories\ProdiRepository;
use App\Services\MahasiswaService;   // <-- TAMBAHKAN INI
use App\Core\Database;
use App\Entities\Mahasiswa;

class MahasiswaController extends Controller   
{
    private MahasiswaRepository $repo;
    private ProdiRepository $prodiRepo;
    private MahasiswaService $service;   // <-- TAMBAHKAN INI

    public function __construct()
    {
        $db = Database::getInstance();
        $this->repo = new MahasiswaRepository($db);
        $this->prodiRepo = new ProdiRepository($db);
        $this->service = new MahasiswaService($this->repo, $this->prodiRepo);   // <-- TAMBAHKAN INI
    }

    public function index(): void
    {
        $flash = $_SESSION['flash'] ?? null;
        unset($_SESSION['flash']);

        $keyword = trim($_GET['q'] ?? '');
        $mahasiswa = $keyword !== '' ? $this->repo->search($keyword) : $this->repo->all();

        echo "<h3>Daftar Mahasiswa</h3>";

        if ($flash) {
            $class = $flash['type'] === 'success' ? '#198754' : '#dc3545';
            echo "<div style='padding:10px;border-radius:5px;margin-bottom:12px;color:#fff;background:{$class}'>"
                 . htmlspecialchars($flash['message']) . "</div>";
        }

        echo '<a href="' . BASE_PATH . '/mahasiswa/create">+ Tambah Mahasiswa</a><br><br>';

        echo '<form method="GET" action="' . BASE_PATH . '/mahasiswa">';
        echo '<input type="text" name="q" placeholder="Cari nama atau NIM..." value="' . htmlspecialchars($keyword) . '">';
        echo '<button type="submit">Cari</button>';
        echo '</form><br>';

        echo "<table border='1' cellpadding='8'>";
        echo "<tr><th>NIM</th><th>Nama</th><th>Email</th><th>Prodi</th><th>Angkatan</th><th>Aksi</th></tr>";

        foreach ($mahasiswa as $m) {
            echo "<tr>";
            echo "<td>{$m['nim']}</td><td>{$m['nama']}</td><td>{$m['email']}</td><td>{$m['prodi_nama']}</td><td>{$m['angkatan']}</td>";
            echo "<td>
                <a href='" . BASE_PATH . "/mahasiswa/{$m['id']}/edit'>Edit</a> |
                <form method='POST' action='" . BASE_PATH . "/mahasiswa/{$m['id']}/delete' style='display:inline' onsubmit='return confirmDelete()'>
                    <button type='submit'>Hapus</button>
                </form>
            </td>";
            echo "</tr>";
        }
        echo "</table>";
        echo "<script>function confirmDelete(){return confirm('Yakin ingin menghapus data ini?');}</script>";
    }

   public function create(): void
{
    $flash = $_SESSION['flash'] ?? null;
    unset($_SESSION['flash']);
    
    $daftarProdi = $this->prodiRepo->all();

    echo "<h3>Tambah Mahasiswa</h3>";

    if ($flash) {
        $class = $flash['type'] === 'success' ? '#198754' : '#dc3545';
        echo "<div style='padding:10px;border-radius:5px;margin-bottom:12px;color:#fff;background:{$class}'>"
             . htmlspecialchars($flash['message']) . "</div>";
    }

        echo '<form method="POST" action="' . BASE_PATH . '/mahasiswa">';
        echo 'NIM: <input type="text" name="nim" required><br>';
        echo 'Nama: <input type="text" name="nama" required><br>';
        echo 'Email: <input type="email" name="email" required><br>';
        echo 'Prodi: <select name="prodi_id">';
        foreach ($daftarProdi as $p) {
            echo "<option value='{$p['id']}'>{$p['nama']}</option>";
        }
        echo '</select><br>';
        echo 'Angkatan: <input type="number" name="angkatan" value="' . date('Y') . '"><br>';
        echo '<button type="submit">Simpan</button>';
        echo '</form>';
    }

    // GANTI SELURUH ISI store() DENGAN INI:
    public function store(): void
    {
        $result = $this->service->create($_POST);

        if ($result['success']) {
            $_SESSION['flash'] = ['type' => 'success', 'message' => 'Data mahasiswa berhasil ditambahkan'];
            $this->redirect('/mahasiswa');
        } else {
            $_SESSION['flash'] = ['type' => 'danger', 'message' => reset($result['errors'])];
            $this->redirect('/mahasiswa/create');
        }
    }

    public function edit(int $id): void
    {
        $flash = $_SESSION['flash'] ?? null;
        unset($_SESSION['flash']);

        $data = $this->repo->find($id);
        if (!$data) {
            http_response_code(404);
            echo "Mahasiswa tidak ditemukan";
            return;
        }

        $daftarProdi = $this->prodiRepo->all();

        echo "<h3>Edit Mahasiswa</h3>";

        if ($flash) {
            $class = $flash['type'] === 'success' ? '#198754' : '#dc3545';
            echo "<div style='padding:10px;border-radius:5px;margin-bottom:12px;color:#fff;background:{$class}'>"
                 . htmlspecialchars($flash['message']) . "</div>";
        }

        echo '<form method="POST" action="' . BASE_PATH . '/mahasiswa/' . $id . '/update">';
        echo 'NIM: <input type="text" name="nim" value="' . $data['nim'] . '" required><br>';
        echo 'Nama: <input type="text" name="nama" value="' . $data['nama'] . '" required><br>';
        echo 'Email: <input type="email" name="email" value="' . $data['email'] . '" required><br>';
        echo 'Prodi: <select name="prodi_id">';
        foreach ($daftarProdi as $p) {
            $selected = $p['id'] == $data['prodi_id'] ? 'selected' : '';
            echo "<option value='{$p['id']}' $selected>{$p['nama']}</option>";
        }
        echo '</select><br>';
        echo 'Angkatan: <input type="number" name="angkatan" value="' . $data['angkatan'] . '"><br>';
        echo '<button type="submit">Update</button>';
        echo '</form>';
    }

    // GANTI SELURUH ISI update() DENGAN INI:
    public function update(int $id): void
    {
        $result = $this->service->update($id, $_POST);

        if ($result['success']) {
            $_SESSION['flash'] = ['type' => 'success', 'message' => 'Data mahasiswa berhasil diubah'];
            $this->redirect('/mahasiswa');
        } else {
            $_SESSION['flash'] = ['type' => 'danger', 'message' => reset($result['errors'])];
            $this->redirect('/mahasiswa/' . $id . '/edit');
        }
    }

 public function destroy(int $id): void
{
    try {
        $this->repo->delete($id);
        $_SESSION['flash'] = ['type' => 'success', 'message' => 'Data mahasiswa berhasil dihapus'];
    } catch (\PDOException $e) {
        error_log(
            date('Y-m-d H:i:s') . ' - Gagal hapus mahasiswa: ' . $e->getMessage() . PHP_EOL,
            3,
            __DIR__ . '/../../storage/logs/app.log'
        );
        $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Data gagal dihapus'];
    }
    $this->redirect('/mahasiswa');
}

    public function show(int $id): void
    {
        $data = $this->repo->find($id);
        echo $data ? "Detail: {$data['nama']}" : "Tidak ditemukan";
    }
}