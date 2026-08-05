<?php declare(strict_types=1);

namespace App\Entity;

use App\Enum\AuditAction;
use App\Repository\AuditRepository;
use App\Entity\User;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\DBAL\Schema\DefaultExpression\CurrentTimestamp;
use MartinGeorgiev\Doctrine\DBAL\Type;

#[ORM\Entity(repositoryClass: AuditRepository::class)]
class Audit
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    #[ORM\Column(type: Types::BIGINT)]
    private ?int $id = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    #[ORM\Column(type: Types::TEXT, enumType: AuditAction::class)]
    private AuditAction $action = AuditAction::AUDIT;

    #[ORM\ManyToOne()]
    #[ORM\JoinColumn(nullable: true)]
    private ?User $user = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $entity = null;

    #[ORM\Column(type: Types::BIGINT, nullable: true)]
    private ?int $entityId = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $userAgent = null;

    #[ORM\Column(type: Type::INET, nullable: true)]
    private ?string $ipAddress = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $message = null;

    /**
     * @var array<string, mixed>
     */
    #[ORM\Column(type: Types::JSONB, nullable: true)]
    private array $data = [];

    #[ORM\Column(type: Types::DATETIMETZ_IMMUTABLE,
        insertable: false,
        updatable: false,
        options: ['default' => new CurrentTimestamp()])]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column(type: Types::STRING,
    length: 32,
    nullable: true,
    options: [
        'fixed' => true,
    ])]
    private ?string $trace_id = null;

    #[ORM\Column(type: Types::STRING,
    length: 16,
    nullable: true,
    options: [
        'fixed' => true,
    ])]
    private ?string $span_id = null;

    // Constructeur
    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
    }

    // Getters
    public function getUser(): ?User { return $this->user; }
    public function getCreatedAt(): \DateTimeImmutable { return $this->createdAt; }
    public function getAction(): AuditAction { return $this->action; }
    public function getEntity(): ?string { return $this->entity; }
    public function getTraceId(): ?string { return $this->trace_id; }
    public function getSpanId(): ?string { return $this->span_id; }
    public function getEntityId(): ?int { return $this->entityId; }
    public function getIpAddress(): ?string { return $this->ipAddress; }
    
    public function setUser(?User $user): static
    {
        $this->user = $user;
        return $this;
    }

    public function setAction(AuditAction $action): static
    {
        $this->action = $action;
        return $this;
    }

    public function setEntity(string $entity): static
    {
        $this->entity = $entity;
        return $this;
    }

    public function setEntityId(int $entityId): static
    {
        $this->entityId = $entityId;
        return $this;
    }

    public function setIpAddress(string $ipAddress): static
    {
        $this->ipAddress = $ipAddress;
        return $this;
    }

    public function getUserAgent(): ?string
    {
        return $this->userAgent;
    }

    public function setUserAgent(string $userAgent): static
    {
        $this->userAgent = $userAgent;
        return $this;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function setMessage(string $message): static
    {
        $this->message = $message;
        return $this;
    }

    /**
     * @return array<string, mixed>
     */
    public function getData(): ?array
    {
        return $this->data;
    }

    /**
     * @param array<string, mixed> $data
     */
    public function setData(array $data): static
    {
        $this->data = $data;
        return $this;
    }

    public function setSpanId(?string $span_id): static
    {
        $this->span_id = $span_id;
        return $this;
    }

    public function setTraceId(?string $trace_id): static
    {
        $this->trace_id = $trace_id;
        return $this;
    }

    public function __toString(): string
    {
        return $this->message;
    }
}
