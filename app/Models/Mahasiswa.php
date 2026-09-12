<?php
namespace App\Models;

use App\Core\Database;

class Mahasiswa
{
    private \PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }
   public function search(string $keyword): array
{
    $stmt = $this->db->prepare(
        "SELECT m.*, p.nama AS prodi_nama
         FROM mahasiswa m
         JOIN prodi p ON m.prodi_id = p.id
         WHERE m.nama LIKE :keyword1 OR m.nim LIKE :keyword2
         ORDER BY m.nim"
    );
    $stmt->execute([
        'keyword1' => '%' . $keyword . '%',
        'keyword2' => '%' . $keyword . '%',
    ]);
    return $stmt->fetchAll();
}

    // READ - semua data, JOIN dengan prodi
    public function all(): array
    {
        $stmt = $this->db->query(
            "SELECT m.*, p.nama AS prodi_nama
             FROM mahasiswa m
             JOIN prodi p ON m.prodi_id = p.id
             ORDER BY m.nim"
        );
        return $stmt->fetchAll();
    }

    // READ - satu data berdasarkan id
    public function find(int $id): array|false
    {
        $stmt = $this->db->prepare("SELECT * FROM mahasiswa WHERE id = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch();
    }

    // CREATE
    public function create(array $data): bool
    {
        $stmt = $this->db->prepare(
            "INSERT INTO mahasiswa (nim, nama, email, prodi_id, angkatan)
             VALUES (:nim, :nama, :email, :prodi_id, :angkatan)"
        );
        return $stmt->execute([
            'nim'      => $data['nim'],
            'nama'     => $data['nama'],
            'email'    => $data['email'],
            'prodi_id' => $data['prodi_id'],
            'angkatan' => $data['angkatan'],
        ]);
    }

    // UPDATE
    public function update(int $id, array $data): bool
    {
        $stmt = $this->db->prepare(
            "UPDATE mahasiswa 
             SET nim = :nim, nama = :nama, email = :email, prodi_id = :prodi_id, angkatan = :angkatan
             WHERE id = :id"
        );
        return $stmt->execute([
            'nim'      => $data['nim'],
            'nama'     => $data['nama'],
            'email'    => $data['email'],
            'prodi_id' => $data['prodi_id'],
            'angkatan' => $data['angkatan'],
            'id'       => $id,
        ]);
    }

    // DELETE
    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare("DELETE FROM mahasiswa WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }
}