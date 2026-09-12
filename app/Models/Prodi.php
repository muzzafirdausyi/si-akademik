<?php
namespace App\Models;

use App\Core\Database;

class Prodi
{
    private \PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function all(): array
    {
        $stmt = $this->db->query("SELECT * FROM prodi ORDER BY nama");
        return $stmt->fetchAll();
    }

    public function find(int $id): array|false
    {
        $stmt = $this->db->prepare("SELECT * FROM prodi WHERE id = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch();
    }

    public function create(array $data): bool
    {
        $stmt = $this->db->prepare(
            "INSERT INTO prodi (kode, nama) VALUES (:kode, :nama)"
        );
        return $stmt->execute([
            'kode' => $data['kode'],
            'nama' => $data['nama'],
        ]);
    }

    public function update(int $id, array $data): bool
    {
        $stmt = $this->db->prepare(
            "UPDATE prodi SET kode = :kode, nama = :nama WHERE id = :id"
        );
        return $stmt->execute([
            'kode' => $data['kode'],
            'nama' => $data['nama'],
            'id'   => $id,
        ]);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare("DELETE FROM prodi WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }
}