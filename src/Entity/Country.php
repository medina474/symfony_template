<?php declare(strict_types=1);

namespace App\Entity;

use App\Repository\CountryRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CountryRepository::class)]
#[ORM\Index(name: 'idx_country_country_code', columns: ['country', 'code'])]
#[ORM\Index(name: 'idx_country_search', columns: ['_country'])]
class Country
{
    #[ORM\Id]
    #[ORM\Column(type: Types::TEXT)]
    private string $code;

    #[ORM\Column(type: Types::TEXT)]
    private string $country;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $long = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $flag = null;

    #[ORM\Column(type: Types::BOOLEAN, options: ['default' => false])]
    private bool $sepa = false;

    #[ORM\Column(type: Types::BOOLEAN, options: ['default' => false])]
    private bool $intracommunity = false;

    #[ORM\Column(type: Types::SMALLINT, nullable: true)]
    protected ?int $phone_code = null;

    #[ORM\ManyToOne()]
    #[ORM\JoinColumn(nullable: false)]
    private Tncc $tncc;

    #[ORM\Column(type: Types::TEXT,
        insertable: false,
        updatable: false,
        generated: 'ALWAYS')]
    private string $_country;

    public function getCode(): string
    {
        return $this->code;
    }

    public function setCode(string $code): static
    {
        $this->code = $code;
        return $this;
    }

    public function getCountry(): string
    {
        return $this->country;
    }

    public function setCountry(string $country): static
    {
        $this->country = $country;
        return $this;
    }

    public function getLong(): ?string
    {
        return $this->long;
    }

    public function setLong(?string $long): static
    {
        $this->long = $long;
        return $this;
    }

    public function getFlag(): ?string
    {
        return $this->flag;
    }

    public function setFlag(?string $flag): static
    {
        $this->flag = $flag;
        return $this;
    }

    public function isSepa(): bool { return $this->sepa; }

    public function setSepa(bool $sepa): static
    {
        $this->sepa = $sepa;
        return $this;
    }

    public function isIntracommunity(): bool { return $this->intracommunity; }

    public function setIntracommunity(bool $intracommunity): static
    {
        $this->intracommunity = $intracommunity;
        return $this;
    }

    public function getPhoneCode(): ?int
    {
        return $this->phone_code;
    }

    public function setPhoneCode(int $phone_code): static
    {
        $this->phone_code = $phone_code;
        return $this;
    }

    public function getTncc(): Tncc
    {
        return $this->tncc;
    }

    public function setTncc(Tncc $tncc): static
    {
        $this->tncc = $tncc;
        return $this;
    }
    
    public function get_Country(): ?string
    {
        return $this->_country;
    }

    public function __toString(): string
    {
        return $this->country;
    }
}
