<?php
namespace App\Repositories;

use PDO;

class ProdiRepository
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function all(): array
    {
        $stmt = $this->db->query("SELECT * FROM prodi ORDER BY nama");
        return $stmt->fetchAll();
    }
}