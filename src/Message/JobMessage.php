<?php

namespace App\Message;

use Symfony\Component\Messenger\Attribute\AsMessage;
use Symfony\Component\Uid\Uuid;

#[AsMessage('async')]
class JobMessage
{
    /**
     * @param array<string, mixed> $payload
     */
    public function __construct(
        private Uuid $id,
        private array $payload = [],
        private ?string $data = null,
    ) {
    }

    public function getId(): Uuid { return $this->id; }
    /**
     * @return array<string, mixed>
     */
    public function getPayload(): array { return $this->payload; }
    public function getData(): ?string { return $this->data; }
}
