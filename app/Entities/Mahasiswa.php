<?php
namespace App\Entities;

class Mahasiswa
{
    private ?int $id = null;
    private string $nim;
    private string $nama;
    private string $email;
    private int $prodiId;
    private int $angkatan;

    // ---------------- GETTER ----------------
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNim(): string
    {
        return $this->nim;
    }

    public function getNama(): string
    {
        return $this->nama;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getProdiId(): int
    {
        return $this->prodiId;
    }

    public function getAngkatan(): int
    {
        return $this->angkatan;
    }

    // ---------------- SETTER (dengan validasi) ----------------
    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    public function setNim(string $nim): void
    {
        if (!ctype_digit($nim)) {
            throw new \InvalidArgumentException("NIM harus berupa angka.");
        }
        $this->nim = $nim;
    }

    public function setNama(string $nama): void
    {
        $nama = trim($nama);
        if ($nama === '') {
            throw new \InvalidArgumentException("Nama mahasiswa tidak boleh kosong.");
        }
        $this->nama = $nama;
    }

    public function setEmail(string $email): void
    {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new \InvalidArgumentException("Format email tidak valid.");
        }
        $this->email = $email;
    }

    public function setProdiId(int $prodiId): void
    {
        $this->prodiId = $prodiId;
    }

    public function setAngkatan(int $angkatan): void
    {
        $this->angkatan = $angkatan;
    }
}