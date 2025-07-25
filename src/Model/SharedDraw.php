<?php

declare(strict_types=1);

namespace App\Model;

use DateTime;

class SharedDraw
{
    public function __construct(
        public readonly ?int $id,
        public readonly string $shareId,
        public readonly array $winners,
        public readonly DateTime $createdAt
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'] ?? null,
            shareId: $data['share_id'],
            winners: json_decode($data['winners'], true),
            createdAt: new DateTime($data['created_at'])
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'share_id' => $this->shareId,
            'winners' => json_encode($this->winners),
            'created_at' => $this->createdAt->format('Y-m-d H:i:s'),
        ];
    }

    public function isExpired(int $daysLimit = 30): bool
    {
        $expiryDate = (new DateTime())->modify("-{$daysLimit} days");
        return $this->createdAt < $expiryDate;
    }

    public function getAgeInDays(): int
    {
        return (new DateTime())->diff($this->createdAt)->days;
    }
}
