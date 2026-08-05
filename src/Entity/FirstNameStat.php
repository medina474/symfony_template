<?php

namespace App\Entity;

use App\Repository\FirstNameStatRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: FirstNameStatRepository::class)]
final readonly class FirstNameStat
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid')]
    public Uuid $id;

    public function __construct(
        #[ORM\Column()]
        public int $gender,
        #[ORM\Column()]
        public string $firstName,
        #[ORM\Column(nullable: true)]
        public ?string $yearOfBirth,
        #[ORM\Column()]
        public int $count,
    ) {
        $this->id = Uuid::v4();
    }

    public function getId(): string
    {
        return $this->id;
    }
}
