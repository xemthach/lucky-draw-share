<?php

declare(strict_types=1);

namespace App\Repository;

use App\Database\Connection;
use App\Model\SharedDraw;
use PDO;

class SharedDrawRepository
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = Connection::getInstance();
    }

    public function create(string $shareId, array $winners): SharedDraw
    {
        $sql = "INSERT INTO shared_draws (share_id, winners, created_at) VALUES (:share_id, :winners, NOW())";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'share_id' => $shareId,
            'winners' => json_encode($winners),
        ]);

        $id = (int) $this->pdo->lastInsertId();

        return new SharedDraw(
            id: $id,
            shareId: $shareId,
            winners: $winners,
            createdAt: new \DateTime()
        );
    }

    public function findByShareId(string $shareId): ?SharedDraw
    {
        $sql = "SELECT * FROM shared_draws WHERE share_id = :share_id";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['share_id' => $shareId]);

        $data = $stmt->fetch();

        if (!$data) {
            return null;
        }

        return SharedDraw::fromArray($data);
    }

    public function deleteExpired(int $daysLimit = 30): int
    {
        $sql = "DELETE FROM shared_draws WHERE created_at < DATE_SUB(NOW(), INTERVAL :days DAY)";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['days' => $daysLimit]);

        return $stmt->rowCount();
    }

    public function countExpired(int $daysLimit = 30): int
    {
        $sql = "SELECT COUNT(*) FROM shared_draws WHERE created_at < DATE_SUB(NOW(), INTERVAL :days DAY)";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['days' => $daysLimit]);

        return (int) $stmt->fetchColumn();
    }

    public function shareIdExists(string $shareId): bool
    {
        $sql = "SELECT COUNT(*) FROM shared_draws WHERE share_id = :share_id";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['share_id' => $shareId]);

        return (int) $stmt->fetchColumn() > 0;
    }
}
