<?php

namespace App\Repositories;

use App\Models\Matakuliah;
use PDO;

class MatakuliahRepository
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function all(): array
    {
        $stmt = $this->pdo->query(
            "SELECT mk.*, p.nama AS prodi_nama
             FROM matakuliah mk
             JOIN prodi p ON mk.prodi_id = p.id
             ORDER BY mk.kode"
        );
        return array_map(fn($row) => Matakuliah::fromArray($row), $stmt->fetchAll());
    }

    public function find(int $id): ?Matakuliah
    {
        $stmt = $this->pdo->prepare(
            "SELECT mk.*, p.nama AS prodi_nama
             FROM matakuliah mk
             JOIN prodi p ON mk.prodi_id = p.id
             WHERE mk.id = :id"
        );
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();
        return $row ? Matakuliah::fromArray($row) : null;
    }

    public function create(Matakuliah $mk): int
    {
        $stmt = $this->pdo->prepare(
            "INSERT INTO matakuliah (kode, nama, sks, prodi_id) VALUES (:kode, :nama, :sks, :prodi_id)"
        );
        $stmt->execute([
            'kode' => $mk->getKode(), 'nama' => $mk->getNama(),
            'sks' => $mk->getSks(), 'prodi_id' => $mk->getProdiId(),
        ]);
        return (int) $this->pdo->lastInsertId();
    }

    public function update(int $id, Matakuliah $mk): void
    {
        $stmt = $this->pdo->prepare(
            "UPDATE matakuliah SET kode = :kode, nama = :nama, sks = :sks, prodi_id = :prodi_id WHERE id = :id"
        );
        $stmt->execute([
            'kode' => $mk->getKode(), 'nama' => $mk->getNama(),
            'sks' => $mk->getSks(), 'prodi_id' => $mk->getProdiId(), 'id' => $id,
        ]);
    }

    public function delete(int $id): void
    {
        $stmt = $this->pdo->prepare("DELETE FROM matakuliah WHERE id = :id");
        $stmt->execute(['id' => $id]);
    }
}
