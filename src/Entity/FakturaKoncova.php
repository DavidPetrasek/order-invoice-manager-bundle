<?php

namespace Psys\SimpleOrderInvoice\Entity;

use Psys\SimpleOrderInvoice\Repository\FakturaKoncovaRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: FakturaKoncovaRepository::class)]
class FakturaKoncova
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(options:["unsigned" => true])]
    private ?int $id = null;

    #[ORM\Column(type: Types::BIGINT, options:["unsigned" => true])]
    private ?string $cislo_poradove = null;

    #[ORM\Column(type: Types::BIGINT, options:["unsigned" => true])]
    private ?string $cislo_evidencni = null;

    // #[ORM\ManyToOne]
    // #[ORM\JoinColumn(nullable: false)]
    // private ?Soubor $soubor = null;
    
    #[ORM\OneToOne(mappedBy: 'faktura_koncova', cascade: ['persist', 'remove'])]
    private ?Faktura $faktura = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $vytvoreno_datum_cas = null;


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCisloPoradove(): ?string
    {
        return $this->cislo_poradove;
    }

    public function setCisloPoradove(string $cislo_poradove): self
    {
        $this->cislo_poradove = $cislo_poradove;

        return $this;
    }

    public function getCisloEvidencni(): ?string
    {
        return $this->cislo_evidencni;
    }

    public function setCisloEvidencni(string $cislo_evidencni): self
    {
        $this->cislo_evidencni = $cislo_evidencni;

        return $this;
    }

    // public function getSoubor(): ?Soubor
    // {
    //     return $this->soubor;
    // }

    // public function setSoubor(?Soubor $soubor): self
    // {
    //     $this->soubor = $soubor;

    //     return $this;
    // }

    public function getFaktura(): ?Faktura
    {
        return $this->faktura;
    }
    
    public function getVytvorenoDatumCas(): ?\DateTimeImmutable
    {
        return $this->vytvoreno_datum_cas;
    }

    public function setVytvorenoDatumCas(\DateTimeImmutable $vytvoreno_datum_cas = null): self
    {
        if ($vytvoreno_datum_cas === null)
        {
            $vytvoreno_datum_cas = new \DateTimeImmutable();
        }
        
        $this->vytvoreno_datum_cas = $vytvoreno_datum_cas;

        return $this;
    }
}