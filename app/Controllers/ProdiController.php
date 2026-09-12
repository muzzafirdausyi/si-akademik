<?php
namespace App\Controllers;

use App\Models\Prodi;

class ProdiController
{
    public function index(): void
    {
        $model = new Prodi();
        $prodi = $model->all();

        echo "<h3>Daftar Prodi</h3>";
        echo '<a href="' . BASE_PATH . '/prodi/create">+ Tambah Prodi</a><br><br>';
        echo "<table border='1' cellpadding='8'>";
        echo "<tr><th>Kode</th><th>Nama</th><th>Aksi</th></tr>";

        foreach ($prodi as $p) {
            echo "<tr>";
            echo "<td>{$p['kode']}</td>";
            echo "<td>{$p['nama']}</td>";
            echo "<td>
                <a href='" . BASE_PATH . "/prodi/{$p['id']}/edit'>Edit</a> |
                <form method='POST' action='" . BASE_PATH . "/prodi/{$p['id']}/delete' style='display:inline' onsubmit='return confirmDelete()'>
                    <button type='submit'>Hapus</button>
                </form>
            </td>";
            echo "</tr>";
        }
        echo "</table>";

        echo "<script>
            function confirmDelete() {
                return confirm('Yakin ingin menghapus data ini?');
            }
        </script>";
    }

    public function create(): void
    {
        echo "<h3>Tambah Prodi</h3>";
        echo '<form method="POST" action="' . BASE_PATH . '/prodi">';
        echo 'Kode: <input type="text" name="kode" required><br>';
        echo 'Nama: <input type="text" name="nama" required><br>';
        echo '<button type="submit">Simpan</button>';
        echo '</form>';
    }

    public function store(): void
    {
        $kode = trim($_POST['kode'] ?? '');
        $nama = trim($_POST['nama'] ?? '');

        if ($kode === '' || $nama === '') {
            redirect('/prodi/create');
        }

        $model = new Prodi();
        $model->create(['kode' => $kode, 'nama' => $nama]);

        redirect('/prodi');
    }

    public function edit(int $id): void
    {
        $model = new Prodi();
        $data = $model->find($id);

        if (!$data) {
            http_response_code(404);
            echo "Prodi tidak ditemukan";
            return;
        }

        echo "<h3>Edit Prodi</h3>";
        echo '<form method="POST" action="' . BASE_PATH . '/prodi/' . $id . '/update">';
        echo 'Kode: <input type="text" name="kode" value="' . $data['kode'] . '" required><br>';
        echo 'Nama: <input type="text" name="nama" value="' . $data['nama'] . '" required><br>';
        echo '<button type="submit">Update</button>';
        echo '</form>';
    }

    public function update(int $id): void
    {
        $kode = trim($_POST['kode'] ?? '');
        $nama = trim($_POST['nama'] ?? '');

        $model = new Prodi();
        $model->update($id, ['kode' => $kode, 'nama' => $nama]);

        redirect('/prodi');
    }

    public function destroy(int $id): void
    {
        $model = new Prodi();
        $model->delete($id);
        redirect('/prodi');
    }
}