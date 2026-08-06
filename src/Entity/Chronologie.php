<?php declare(strict_types=1);

namespace App\Entity;

use App\Repository\ChronologieRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ChronologieRepository::class)]
#[ORM\Cache(usage: 'READ_ONLY')]
class Chronologie
{    
    #[ORM\Id]
    #[ORM\Column(type: Types::INTEGER, options: ['default' => 2440588, 'comment' => 'Jour julien'])]
    private int $jj = 0;

    #[ORM\Column(type: Types::BIGINT, options: ['default' => 0])]
    private int $epoch = 0;

    #[ORM\Column(type: Types::DATE_IMMUTABLE, options: ['default' => '1970-01-01'])]
    private \DateTimeImmutable $jour;

    #[ORM\Column(type: Types::SMALLINT, options: ['default' => 0])]
    private int $annee = 0;

    #[ORM\Column(type: Types::SMALLINT, options: ['default' => 0])]
    private int $mois = 0;

    #[ORM\Column(type: Types::SMALLINT, options: ['default' => 0])]
    private int $jmois = 0;

    #[ORM\Column(type: Types::SMALLINT, options: ['default' => 0])]
    private int $semestre = 0;

    #[ORM\Column(type: Types::SMALLINT, options: ['default' => 0])]
    private int $quadrimestre = 0;

    #[ORM\Column(type: Types::SMALLINT, options: ['default' => 0])]
    private int $trimestre = 0;

    #[ORM\Column(type: Types::SMALLINT, options: ['default' => 0])]
    private int $bimestre = 0;

    #[ORM\Column(type: Types::SMALLINT, options: ['default' => 0])]
    private int $semaine = 0;

    #[ORM\Column(type: Types::SMALLINT, options: ['default' => 0])]
    private int $jsemaine = 0;

    #[ORM\Column(type: Types::SMALLINT, options: ['default' => 0])]
    private int $jannee = 0;

    #[ORM\Column(type: Types::FLOAT, options: ['default' => 0.0])]
    private float $frac_mois = 0.0;

    #[ORM\Column(type: Types::FLOAT, options: ['default' => 0.0])]
    private float $frac_annee = 0.0;

    public function getJj(): int { return $this->jj; }
    public function getEpoch(): int { return $this->epoch; }
    public function getJour(): \DateTimeImmutable { return $this->jour; }
    public function getAnnee(): int { return $this->annee; }
    public function getMois(): int { return $this->mois; }
    public function getJmois(): int { return $this->jmois; }
    public function getSemestre(): int { return $this->semestre; }
    public function getQuadrimestre(): int { return $this->quadrimestre; }
    public function getTrimestre(): int { return $this->trimestre; }
    public function getBimestre(): int { return $this->bimestre; }
    public function getSemaine(): int { return $this->semaine; }
    public function getJsemaine(): int { return $this->jsemaine; }
    public function getJannee(): int { return $this->jannee; }
    public function getFracMois(): float { return $this->frac_mois; }
    public function getFracAnnee(): float { return $this->frac_annee; }

    public function __construct() {
        $this->jour = new \DateTimeImmutable('1970-01-01');
    }
}
