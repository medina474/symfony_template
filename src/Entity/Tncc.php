<?php declare(strict_types=1);

namespace App\Entity;

use App\Repository\TnccRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TnccRepository::class)]
class Tncc
{
    #[ORM\Id]
    #[ORM\Column(type: Types::SMALLINT)]
    protected int $id;

    #[ORM\Column(type: Types::TEXT)]
    private string $article;

    #[ORM\Column(type: Types::TEXT)]
    private string $charniere;

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): static
    {
        $this->id = $id;
        return $this;
    }

    public function getArticle(): ?string
    {
        return $this->article;
    }

    public function setArticle(string $article): static
    {
        $this->article = $article;
        return $this;
    }

    public function getCharniere(): ?string
    {
        return $this->charniere;
    }

    public function setCharniere(string $charniere): static
    {
        $this->charniere = $charniere;
        return $this;
    }

    public function __toString(): string
    {
        return $this->article;
    }
}
