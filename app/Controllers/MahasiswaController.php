<?php
namespace App\Controllers;

use App\Core\Controller;   //ditambahkan ini untuk acara 10
use App\Repositories\MahasiswaRepository;
use App\Repositories\ProdiRepository;
use App\Core\Database;
use App\Entities\Mahasiswa;

class MahasiswaController extends Controller   
{
    private MahasiswaRepository $repo;
    private ProdiRepository $prodiRepo;

    public function __construct()
    {
        // Controller "meminta" Database::getInstance() SEKALI di sini,
        // lalu menyuntikkannya ke Repository
        $db = Database::getInstance();
        $this->repo = new MahasiswaRepository($db);
        $this->prodiRepo = new ProdiRepository($db);
    }

    public function index(): void
    {
        $keyword = trim($_GET['q'] ?? '');
        $mahasiswa = $keyword !== '' ? $this->repo->search($keyword) : $this->repo->all();

        echo "<h3>Daftar Mahasiswa</h3>";
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
        $daftarProdi = $this->prodiRepo->all();

        echo "<h3>Tambah Mahasiswa</h3>";
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

    public function store(): void
    {
        try {
            // Bikin OBJEK Mahasiswa, bukan array biasa lagi
            $mhs = new Mahasiswa();
            $mhs->setNim(trim($_POST['nim'] ?? ''));
            $mhs->setNama(trim($_POST['nama'] ?? ''));
            $mhs->setEmail(trim($_POST['email'] ?? ''));
            $mhs->setProdiId((int) ($_POST['prodi_id'] ?? 0));
            $mhs->setAngkatan((int) ($_POST['angkatan'] ?? date('Y')));

            $this->repo->create($mhs);
             $this->redirect('/mahasiswa');

        } catch (\InvalidArgumentException $e) {
            // Validasi dari setter gagal -> tampilkan pesan error
            echo "Gagal menyimpan: " . $e->getMessage();
            echo '<br><a href="' . BASE_PATH . '/mahasiswa/create">Kembali</a>';
        }
    }

    public function edit(int $id): void
    {
        $data = $this->repo->find($id);
        if (!$data) {
            http_response_code(404);
            echo "Mahasiswa tidak ditemukan";
            return;
        }

        $daftarProdi = $this->prodiRepo->all();

        echo "<h3>Edit Mahasiswa</h3>";
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

    public function update(int $id): void
    {
        try {
            $mhs = new Mahasiswa();
            $mhs->setNim(trim($_POST['nim'] ?? ''));
            $mhs->setNama(trim($_POST['nama'] ?? ''));
            $mhs->setEmail(trim($_POST['email'] ?? ''));
            $mhs->setProdiId((int) ($_POST['prodi_id'] ?? 0));
            $mhs->setAngkatan((int) ($_POST['angkatan'] ?? date('Y')));

            $this->repo->update($id, $mhs);
            $this->redirect('/mahasiswa');

        } catch (\InvalidArgumentException $e) {
            echo "Gagal update: " . $e->getMessage();
            echo '<br><a href="' . BASE_PATH . '/mahasiswa/' . $id . '/edit">Kembali</a>';
        }
    }

    public function destroy(int $id): void
    {
        $this->repo->delete($id);
        $this->redirect('/mahasiswa');
    }

    public function show(int $id): void
    {
        $data = $this->repo->find($id);
        echo $data ? "Detail: {$data['nama']}" : "Tidak ditemukan";
    }
}