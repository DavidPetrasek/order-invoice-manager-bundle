<?php

namespace Psys\SimpleOrderInvoice\Entity;

use Psys\SimpleOrderInvoice\Repository\FakturaRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: FakturaRepository::class)]
class Faktura
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(options:["unsigned" => true])]
    private ?int $id = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'], inversedBy: 'faktura')]
    #[ORM\JoinColumn(nullable: true)]
    private ?FakturaProforma $faktura_proforma = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'], inversedBy: 'faktura')]
    private ?FakturaKoncova $faktura_koncova = null;



    #[ORM\Column(type: Types::BIGINT, nullable: true, options:["unsigned" => true])]
    private ?string $symbol_variabilni = null;

    #[ORM\OneToOne(mappedBy: 'faktura', cascade: ['persist', 'remove'])]
    private ?Objednavka $objednavka = null;

    #[ORM\OneToOne(mappedBy: 'faktura', cascade: ['persist', 'remove'])]
    private ?FakturaOdberatel $fakturaOdberatel = null;

    #[ORM\OneToOne(mappedBy: 'faktura', cascade: ['persist', 'remove'])]
    private ?FakturaDodavatel $fakturaDodavatel = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFakturaProforma(): ?FakturaProforma
    {
        return $this->faktura_proforma;
    }

    public function setFakturaProforma(FakturaProforma $faktura_proforma): self
    {
        $this->faktura_proforma = $faktura_proforma;

        return $this;
    }

    public function getFakturaKoncova(): ?FakturaKoncova
    {
        return $this->faktura_koncova;
    }

    public function setFakturaKoncova(?FakturaKoncova $faktura_koncova): self
    {
        $this->faktura_koncova = $faktura_koncova;

        return $this;
    }

  

    public function getSymbolVariabilni(): ?string
    {
        return $this->symbol_variabilni;
    }

    public function setSymbolVariabilni(?string $symbol_variabilni): self
    {
        $this->symbol_variabilni = $symbol_variabilni;

        return $this;
    }

    public function getObjednavka(): ?Objednavka
    {
        return $this->objednavka;
    }

    public function setObjednavka(Objednavka $objednavka): self
    {
        // set the owning side of the relation if necessary
        if ($objednavka->getFaktura() !== $this) {
            $objednavka->setFaktura($this);
        }

        $this->objednavka = $objednavka;

        return $this;
    }

    public function getFakturaOdberatel(): ?FakturaOdberatel
    {
        return $this->fakturaOdberatel;
    }

    public function setFakturaOdberatel(FakturaOdberatel $fakturaOdberatel): self
    {
        // set the owning side of the relation if necessary
        if ($fakturaOdberatel->getFaktura() !== $this) {
            $fakturaOdberatel->setFaktura($this);
        }

        $this->fakturaOdberatel = $fakturaOdberatel;

        return $this;
    }

    public function getFakturaDodavatel(): ?FakturaDodavatel
    {
        return $this->fakturaDodavatel;
    }

    public function setFakturaDodavatel(FakturaDodavatel $fakturaDodavatel): self
    {
        // set the owning side of the relation if necessary
        if ($fakturaDodavatel->getFaktura() !== $this) {
            $fakturaDodavatel->setFaktura($this);
        }

        $this->fakturaDodavatel = $fakturaDodavatel;

        return $this;
    }
}
