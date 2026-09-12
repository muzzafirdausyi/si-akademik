<?php
namespace App\Models;

use App\Core\Database;

class Matakuliah
{
    private \PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function all(): array
    {
        $stmt = $this->db->query(
            "SELECT mk.*, p.nama AS prodi_nama
             FROM matakuliah mk
             JOIN prodi p ON mk.prodi_id = p.id
             ORDER BY mk.kode"
        );
        return $stmt->fetchAll();
    }

    public function find(int $id): array|false
    {
        $stmt = $this->db->prepare("SELECT * FROM matakuliah WHERE id = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch();
    }

    public function create(array $data): bool
    {
        $stmt = $this->db->prepare(
            "INSERT INTO matakuliah (kode, nama, sks, prodi_id) 
             VALUES (:kode, :nama, :sks, :prodi_id)"
        );
        return $stmt->execute($data);
    }

    public function update(int $id, array $data): bool
    {
        $stmt = $this->db->prepare(
            "UPDATE matakuliah SET kode = :kode, nama = :nama, sks = :sks, prodi_id = :prodi_id WHERE id = :id"
        );
        $data['id'] = $id;
        return $stmt->execute($data);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare("DELETE FROM matakuliah WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }
}