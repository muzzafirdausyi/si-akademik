<?php
namespace App\Controllers;

use App\Models\Matakuliah;
use App\Models\Prodi;

class MatakuliahController
{
    public function index(): void
    {
        $model = new Matakuliah();
        $matkul = $model->all();

        echo "<h3>Daftar Mata Kuliah</h3>";
        echo '<a href="' . BASE_PATH . '/matakuliah/create">+ Tambah Mata Kuliah</a><br><br>';
        echo "<table border='1' cellpadding='8'>";
        echo "<tr><th>Kode</th><th>Nama</th><th>SKS</th><th>Prodi</th><th>Aksi</th></tr>";

        foreach ($matkul as $mk) {
            echo "<tr>";
            echo "<td>{$mk['kode']}</td>";
            echo "<td>{$mk['nama']}</td>";
            echo "<td>{$mk['sks']}</td>";
            echo "<td>{$mk['prodi_nama']}</td>";
            echo "<td>
                <a href='" . BASE_PATH . "/matakuliah/{$mk['id']}/edit'>Edit</a> |
                <form method='POST' action='" . BASE_PATH . "/matakuliah/{$mk['id']}/delete' style='display:inline' onsubmit='return confirmDelete()'>
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
        $prodiModel = new Prodi();
        $daftarProdi = $prodiModel->all();

        echo "<h3>Tambah Mata Kuliah</h3>";
        echo '<form method="POST" action="' . BASE_PATH . '/matakuliah">';
        echo 'Kode: <input type="text" name="kode" required><br>';
        echo 'Nama: <input type="text" name="nama" required><br>';
        echo 'SKS: <input type="number" name="sks" required><br>';
        echo 'Prodi: <select name="prodi_id">';
        foreach ($daftarProdi as $p) {
            echo "<option value='{$p['id']}'>{$p['nama']}</option>";
        }
        echo '</select><br>';
        echo '<button type="submit">Simpan</button>';
        echo '</form>';
    }

    public function store(): void
    {
        $kode     = trim($_POST['kode'] ?? '');
        $nama     = trim($_POST['nama'] ?? '');
        $sks      = (int) ($_POST['sks'] ?? 0);
        $prodi_id = (int) ($_POST['prodi_id'] ?? 0);

        if ($kode === '' || $nama === '') {
            redirect('/matakuliah/create');
        }

        $model = new Matakuliah();
        $model->create([
            'kode'     => $kode,
            'nama'     => $nama,
            'sks'      => $sks,
            'prodi_id' => $prodi_id,
        ]);

        redirect('/matakuliah');
    }

    public function edit(int $id): void
    {
        $model = new Matakuliah();
        $data = $model->find($id);

        if (!$data) {
            http_response_code(404);
            echo "Mata kuliah tidak ditemukan";
            return;
        }

        $prodiModel = new Prodi();
        $daftarProdi = $prodiModel->all();

        echo "<h3>Edit Mata Kuliah</h3>";
        echo '<form method="POST" action="' . BASE_PATH . '/matakuliah/' . $id . '/update">';
        echo 'Kode: <input type="text" name="kode" value="' . $data['kode'] . '" required><br>';
        echo 'Nama: <input type="text" name="nama" value="' . $data['nama'] . '" required><br>';
        echo 'SKS: <input type="number" name="sks" value="' . $data['sks'] . '" required><br>';
        echo 'Prodi: <select name="prodi_id">';
        foreach ($daftarProdi as $p) {
            $selected = $p['id'] == $data['prodi_id'] ? 'selected' : '';
            echo "<option value='{$p['id']}' $selected>{$p['nama']}</option>";
        }
        echo '</select><br>';
        echo '<button type="submit">Update</button>';
        echo '</form>';
    }

    public function update(int $id): void
    {
        $kode     = trim($_POST['kode'] ?? '');
        $nama     = trim($_POST['nama'] ?? '');
        $sks      = (int) ($_POST['sks'] ?? 0);
        $prodi_id = (int) ($_POST['prodi_id'] ?? 0);

        $model = new Matakuliah();
        $model->update($id, [
            'kode'     => $kode,
            'nama'     => $nama,
            'sks'      => $sks,
            'prodi_id' => $prodi_id,
        ]);

        redirect('/matakuliah');
    }

    public function destroy(int $id): void
    {
        $model = new Matakuliah();
        $model->delete($id);
        redirect('/matakuliah');
    }
}