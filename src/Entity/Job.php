<?php declare(strict_types=1);

namespace App\Entity;

use App\Repository\JobRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\DBAL\Types\Types;
use Symfony\Component\Uid\Uuid;
use Doctrine\DBAL\Schema\DefaultExpression\CurrentTimestamp;

#[ORM\Entity(repositoryClass: JobRepository::class)]
class Job
{
    const STATUS_PENDING    = 'pending';
    const STATUS_PROCESSING = 'processing';
    const STATUS_DONE       = 'done';
    const STATUS_FAILED     = 'failed';

    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    private Uuid $id;

    #[ORM\Column(type: Types::TEXT)]
    private string $action;

    #[ORM\Column(type: Types::TEXT)]
    private string $status = self::STATUS_PENDING;

    /**
     * @var array<string, mixed>
     */
    #[ORM\Column(type: Types::JSONB)]
    private array $payload = [];

    /**
     * @var array<string, mixed>
     */
    #[ORM\Column(type: Types::JSONB, nullable: true)]
    private array $result = [];

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $errorMessage = null;

    #[ORM\Column(type: Types::SMALLINT, options: ['default' => 0])]
    private int $retryCount = 0;

    #[ORM\Column(type: Types::DATETIMETZ_IMMUTABLE,
        insertable: false,
        updatable: false,
        options: ['default' => new CurrentTimestamp()])]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column(type: Types::DATETIMETZ_IMMUTABLE, nullable: true)]
    private ?\DateTimeImmutable $handledAt = null;

    #[ORM\Column(type: Types::DATETIMETZ_IMMUTABLE, nullable: true)]
    private ?\DateTimeImmutable $completedAt = null;

    /**
     * @param array<string, mixed> $payload
     */
    public function __construct(string $action, array $payload = [])
    {
        $this->id = Uuid::v7();
        $this->action = $action;
        $this->payload = $payload;
        $this->createdAt = new \DateTimeImmutable();
    }

    // Getters
    public function getId(): Uuid { return $this->id; }
    public function getAction(): string { return $this->action; }
    public function getStatus(): string { return $this->status; }
    /**
     * @return array<string, mixed>
     */
    public function getPayload(): ?array  { return $this->payload; }
    /**
     * @return array<string, mixed>
     */
    public function getResult(): ?array  { return $this->result; }
    public function getErrorMessage(): ?string { return $this->errorMessage; }
    public function getRetryCount(): int { return $this->retryCount; }
    public function getCreatedAt(): \DateTimeImmutable { return $this->createdAt; }
    public function getHandledAt(): ?\DateTimeImmutable { return $this->handledAt; }
    public function getCompletedAt(): ?\DateTimeImmutable { return $this->completedAt; }
    
    // Setters
    public function setRetryCount(int $retryCount): static { $this->retryCount = $retryCount; return $this; }

    // Mark
    public function markProcessing(): static
    {
        $this->status = self::STATUS_PROCESSING;
        $this->handledAt = new \DateTimeImmutable();
        return $this;
    }

    /**
     * @param ?array<string, mixed> $result
     */
    public function markDone(?array $result): static
    {
        $this->status      = self::STATUS_DONE;
        $this->result      = $result;
        $this->completedAt = new \DateTimeImmutable();
        return $this;
    }

    public function markFailed(string $error): static
    {
        $this->status       = self::STATUS_FAILED;
        $this->errorMessage = $error;
        $this->completedAt  = new \DateTimeImmutable();
        //$this->retryCount++;
        return $this;
    }
}
