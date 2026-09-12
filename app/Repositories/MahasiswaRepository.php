<?php
namespace App\Repositories;

use PDO;
use App\Entities\Mahasiswa;

class MahasiswaRepository
{
    private PDO $db;

    // Constructor Injection: PDO "disuntikkan" dari luar, bukan dibuat sendiri
    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

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

    public function find(int $id): array|false
    {
        $stmt = $this->db->prepare("SELECT * FROM mahasiswa WHERE id = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch();
    }

    // Menerima OBJEK Mahasiswa, bukan array biasa
    public function create(Mahasiswa $mhs): bool
    {
        $stmt = $this->db->prepare(
            "INSERT INTO mahasiswa (nim, nama, email, prodi_id, angkatan)
             VALUES (:nim, :nama, :email, :prodi_id, :angkatan)"
        );
        return $stmt->execute([
            'nim'      => $mhs->getNim(),
            'nama'     => $mhs->getNama(),
            'email'    => $mhs->getEmail(),
            'prodi_id' => $mhs->getProdiId(),
            'angkatan' => $mhs->getAngkatan(),
        ]);
    }

    public function update(int $id, Mahasiswa $mhs): bool
    {
        $stmt = $this->db->prepare(
            "UPDATE mahasiswa 
             SET nim = :nim, nama = :nama, email = :email, prodi_id = :prodi_id, angkatan = :angkatan
             WHERE id = :id"
        );
        return $stmt->execute([
            'nim'      => $mhs->getNim(),
            'nama'     => $mhs->getNama(),
            'email'    => $mhs->getEmail(),
            'prodi_id' => $mhs->getProdiId(),
            'angkatan' => $mhs->getAngkatan(),
            'id'       => $id,
        ]);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare("DELETE FROM mahasiswa WHERE id = :id");
        return $stmt->execute(['id' => $id]);
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
}