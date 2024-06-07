<?php

namespace Psys\SimpleOrderInvoice\Entity;

use Psys\SimpleOrderInvoice\Repository\FakturaDodavatelRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: FakturaDodavatelRepository::class)]
class FakturaDodavatel
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(options:["unsigned" => true])]
    private ?int $id = null;

    #[ORM\OneToOne(inversedBy: 'fakturaDodavatel', cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?Faktura $faktura = null;
    
    #[ORM\Column(length: 200)]
    private ?string $nazev = null;
    
    #[ORM\Column(length: 50)]
    private ?string $ulice = null;
    
    #[ORM\Column(type: Types::SMALLINT, options:["unsigned" => true])]
    private ?int $cislo_popisne = null;
    
    #[ORM\Column(length: 4, nullable: true)]
    private ?string $cislo_orientacni = null;
    
    #[ORM\Column(length: 35)]
    private ?string $mesto = null;
    
    #[ORM\Column(type: 'integer', options:["unsigned" => true])]
    private ?int $psc = null;
    
    #[ORM\Column(length: 12, nullable: true)]
    private ?string $dic = null;
    
    #[ORM\Column(length: 8, options:["fixed" => true])]
    private ?string $ico = null;

    #[ORM\Column(length: 35, nullable: true)]
    private ?string $cast_obce = null;
    

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFaktura(): ?Faktura
    {
        return $this->faktura;
    }

    public function setFaktura(Faktura $faktura): self
    {
        $this->faktura = $faktura;

        return $this;
    }
    
    public function getNazev(): ?string
    {
        return $this->nazev;
    }
    
    public function setNazev(string $nazev): self
    {
        $this->nazev = $nazev;
        
        return $this;
    }
    
    public function getUlice(): ?string
    {
        return $this->ulice;
    }
    
    public function setUlice(string $ulice): self
    {
        $this->ulice = $ulice;
        
        return $this;
    }
    
    public function getCisloPopisne(): ?int
    {
        return $this->cislo_popisne;
    }
    
    public function setCisloPopisne(int $cislo_popisne): self
    {
        $this->cislo_popisne = $cislo_popisne;
        
        return $this;
    }
    
    public function getCisloOrientacni(): ?string
    {
        return $this->cislo_orientacni;
    }
    
    public function setCisloOrientacni(?string $cislo_orientacni): self
    {
        $this->cislo_orientacni = $cislo_orientacni;
        
        return $this;
    }
    
    public function getMesto(): ?string
    {
        return $this->mesto;
    }
    
    public function setMesto(string $mesto): self
    {
        $this->mesto = $mesto;
        
        return $this;
    }
    
    public function getPsc(): ?int
    {
        return $this->psc;
    }
    
    public function setPsc(int $psc): self
    {
        $this->psc = $psc;
        
        return $this;
    }
    
    public function getDic(): ?string
    {
        return $this->dic;
    }
    
    public function setDic(?string $dic): self
    {
        $this->dic = $dic;
        
        return $this;
    }
    
    public function getIco(): ?string
    {
        return $this->ico;
    }
    
    public function setIco(?string $ico): self
    {
        $this->ico = $ico;
        
        return $this;
    }

    public function getCastObce(): ?string
    {
        return $this->cast_obce;
    }

    public function setCastObce(?string $cast_obce): static
    {
        $this->cast_obce = $cast_obce;

        return $this;
    }
}
