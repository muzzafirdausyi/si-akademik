<?php

namespace App\Services;

use App\Repositories\MahasiswaRepository;
use App\Repositories\ProdiRepository;
use App\Entities\Mahasiswa;

class MahasiswaService
{
    private MahasiswaRepository $repo;
    private ProdiRepository $prodiRepo;

    public function __construct(
        MahasiswaRepository $repo,
        ProdiRepository $prodiRepo
    ) {
        $this->repo = $repo;
        $this->prodiRepo = $prodiRepo;
    }

    public function create(array $input): array
    {
        $errors = $this->validate($input);
        if (!empty($errors)) {
            return ['success' => false, 'errors' => $errors];
        }

        try {
            $mhs = $this->buildEntity($input);
            $this->repo->create($mhs);
            return ['success' => true];
        } catch (\InvalidArgumentException $e) {
            return ['success' => false, 'errors' => ['general' => 'Data gagal disimpan:Nim sudah Terdaftar!']];
        } catch (\PDOException $e) {
            $this->logError('Gagal create mahasiswa: ' . $e->getMessage());
            return ['success' => false, 'errors' => ['general' => 'Data gagal disimpan. Silakan coba lagi.']];
        }
    }

    public function update(int $id, array $input): array
    {
        $errors = $this->validate($input, $id);
        if (!empty($errors)) {
            return ['success' => false, 'errors' => $errors];
        }

        try {
            $mhs = $this->buildEntity($input);
            $this->repo->update($id, $mhs);
            return ['success' => true];
        } catch (\InvalidArgumentException $e) {
            return ['success' => false, 'errors' => ['general' => 'Data gagal disimpan:Nim sudah Terdaftar!']];
        } catch (\PDOException $e) {
            $this->logError('Gagal update mahasiswa: ' . $e->getMessage());
            return ['success' => false, 'errors' => ['general' => 'Data gagal disimpan. Silakan coba lagi.']];
        }
    }

    private function buildEntity(array $input): Mahasiswa
    {
        $mhs = new Mahasiswa();
        $mhs->setNim(trim($input['nim'] ?? ''));
        $mhs->setNama(trim($input['nama'] ?? ''));
        $mhs->setEmail(trim($input['email'] ?? ''));
        $mhs->setProdiId((int) ($input['prodi_id'] ?? 0));
        $mhs->setAngkatan((int) ($input['angkatan'] ?? date('Y')));
        return $mhs;
    }

    private function validate(array $input, ?int $excludeId = null): array
    {
        $errors = [];

        if (empty($input['nim'])) {
            $errors['nim'] = 'NIM wajib diisi';
        } elseif ($this->repo->existsByNim(trim($input['nim']), $excludeId)) {
            $errors['nim'] = 'Data gagal disimpan:Nim sudah Terdaftar!';
        }

        if (empty(trim($input['nama'] ?? ''))) {
            $errors['nama'] = 'Nama wajib diisi';
        }

        if (empty($input['email'])) {
            $errors['email'] = 'Email wajib diisi';
        } elseif (!filter_var($input['email'], FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Format email tidak valid';
        }

        return $errors;
    }

    private function logError(string $message): void
    {
        error_log(
            date('Y-m-d H:i:s') . ' - ' . $message . PHP_EOL,
            3,
            __DIR__ . '/../../storage/logs/app.log'
        );
    }
}