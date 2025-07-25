<?php

declare(strict_types=1);

namespace App\Service;

use App\Model\SharedDraw;
use App\Repository\SharedDrawRepository;

class SharedDrawService
{
    public function __construct(
        private SharedDrawRepository $repository
    ) {}

    public function createSharedDraw(array $winners): SharedDraw
    {
        $shareId = $this->generateUniqueShareId();

        return $this->repository->create($shareId, $winners);
    }

    public function getSharedDraw(string $shareId): ?SharedDraw
    {
        return $this->repository->findByShareId($shareId);
    }

    public function cleanupExpired(int $daysLimit = 30): int
    {
        return $this->repository->deleteExpired($daysLimit);
    }

    private function generateUniqueShareId(): string
    {
        $maxAttempts = 10;
        $attempts = 0;

        do {
            $shareId = $this->generateRandomShareId();
            $attempts++;
        } while ($this->repository->shareIdExists($shareId) && $attempts < $maxAttempts);

        if ($attempts >= $maxAttempts) {
            throw new \RuntimeException('Unable to generate unique share ID after maximum attempts');
        }

        return $shareId;
    }

    private function generateRandomShareId(): string
    {
        $characters = 'abcdefghijklmnopqrstuvwxyz0123456789';
        $length = 8;
        $shareId = '';

        for ($i = 0; $i < $length; $i++) {
            $shareId .= $characters[random_int(0, strlen($characters) - 1)];
        }

        return $shareId;
    }
}
