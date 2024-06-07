<?php

namespace Psys\SimpleOrderInvoice\Entity;

use Psys\SimpleOrderInvoice\Repository\ObjednavkaRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Normalizer\PropertyNormalizer;

use Psys\SimpleOrderInvoice\Lib\ObjednavkaKategorie;
use Psys\SimpleOrderInvoice\Objednavka\FormaUhrady;
use Psys\SimpleOrderInvoice\Objednavka\Stav;
use Psys\SimpleOrderInvoice\Objednavka\ZboziPolozka;


#[ORM\Entity(repositoryClass: ObjednavkaRepository::class)]
class Objednavka
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(options:["unsigned" => true])]
    private ?int $id = null;

    #[ORM\Column(nullable: true)]
    private array $zbozi = [];
    
    #[ORM\Column(type: Types::SMALLINT, options:["unsigned" => true])]
    private ?int $forma_uhrady = null;
    
    #[ORM\Column(nullable: true, type: Types::DECIMAL, precision: 14, scale: 2)]
    private ?string $cena_celkem_vcetne_dph = '0.00';
    
    #[ORM\Column(nullable: true, type: Types::DECIMAL, precision: 14, scale: 2)]
    private ?string $cena_celkem_bez_dph = '0.00';
    
    #[ORM\Column(nullable: true, type: Types::DECIMAL, precision: 14, scale: 2)]
    private ?string $cena_celkem_zaklad_dph = '0.00';
    
    #[ORM\Column(nullable: true, type: Types::DECIMAL, precision: 14, scale: 2)]
    private ?string $cena_celkem_vyse_dph = '0.00';
    
    #[ORM\Column]
    private ?\DateTimeImmutable $vytvoreno_datum_cas = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $uhrazeno_datum_cas = null;

    #[ORM\OneToOne(inversedBy: 'objednavka', cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?Faktura $faktura = null;

    #[ORM\Column(targetEntity: '%simple_order_invoice.kategorie%', type: Types::SMALLINT, options:["unsigned" => true])]
    private $kategorie = null;

    #[ORM\ManyToOne(targetEntity: '%simple_order_invoice.user_class%', inversedBy: 'objednavky')]
    #[ORM\JoinColumn(nullable: true, onDelete: 'SET NULL')]
    private $uzivatel = null;

    #[ORM\Column(type: Types::SMALLINT, options:["unsigned" => true])]
    private ?int $stav = null;

    #[ORM\Column(length: 20, nullable: true)]
    private ?string $forma_uhrady_bankovni_ucet = null;

    
    
    public function getId(): ?int
    {
        return $this->id;
    }
    
    public function getZbozi(): array
    {
        $zboziJSON = [];
        
        $normalizers = [new PropertyNormalizer()];
        $serializer = new Serializer($normalizers, []);
        
        foreach ($this->zbozi as $z)
        {
            $zboziJSON[] = $serializer->denormalize($z, ZboziPolozka::class, 'json');
        }
        
        return $zboziJSON;
    }
    
    public function setZbozi(array $zbozi): self
    {
        $zboziJSON = [];
        
        $normalizers = [new PropertyNormalizer()];
        $serializer = new Serializer($normalizers, []);
        
        foreach ($zbozi as $z)
        {
            $zboziJSON[] = $serializer->normalize($z, 'json');
        }
        
        $this->zbozi = $zboziJSON;
        
        return $this;
    }
    
    public function getFormaUhrady(): ?FormaUhrady
    {
//         return $this->forma_uhrady;
        return FormaUhrady::from($this->forma_uhrady);
    }
    
    public function setFormaUhrady(int|FormaUhrady $forma_uhrady): self
    {
        if ($forma_uhrady instanceof FormaUhrady) {$forma_uhrady = $forma_uhrady->value;}
        
        $this->forma_uhrady = $forma_uhrady;
        
        return $this;
    }
    
    public function getCenaCelkemVcetneDph(): ?float
    {
        return $this->cena_celkem_vcetne_dph;
    }
    
    public function setCenaCelkemVcetneDph(?float $cena_celkem_vcetne_dph): self
    {
        $this->cena_celkem_vcetne_dph = $cena_celkem_vcetne_dph;
        
        return $this;
    }
    
    public function getCenaCelkemBezDph(): ?float
    {
        return $this->cena_celkem_bez_dph;
    }
    
    public function setCenaCelkemBezDph(?float $cena_celkem_bez_dph): self
    {
        $this->cena_celkem_bez_dph = $cena_celkem_bez_dph;
        
        return $this;
    }
    
    public function getCenaCelkemZakladDph(): ?float
    {
        return $this->cena_celkem_zaklad_dph;
    }
    
    public function setCenaCelkemZakladDph(?float $cena_celkem_zaklad_dph): self
    {
        $this->cena_celkem_zaklad_dph = $cena_celkem_zaklad_dph;
        
        return $this;
    }
    
    public function getCenaCelkemVyseDph(): ?float
    {
        return $this->cena_celkem_vyse_dph;
    }
    
    public function setCenaCelkemVyseDph(?float $cena_celkem_vyse_dph): self
    {
        $this->cena_celkem_vyse_dph = $cena_celkem_vyse_dph;
        
        return $this;
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
    

    public function getUhrazenoDatumCas(): ?\DateTimeImmutable
    {
        return $this->uhrazeno_datum_cas;
    }

    public function setUhrazenoDatumCas(?\DateTimeImmutable $uhrazeno_datum_cas = null): self
    {
        if ($uhrazeno_datum_cas === null)
        {
            $uhrazeno_datum_cas = new \DateTimeImmutable();
        }
        
        $this->uhrazeno_datum_cas = $uhrazeno_datum_cas;

        return $this;
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

    public function getKategorie() //?ObjednavkaKategorie
    {
        return $this->kategorie; //return ObjednavkaKategorie::from($this->kategorie);
    }

    public function setKategorie($kategorie): self  //|ObjednavkaKategorie
    {
       // if ($kategorie instanceof ObjednavkaKategorie) {$kategorie = $kategorie->value;}
        
        $this->kategorie = $kategorie;

        return $this;
    }

    public function getUzivatel() //: ?Uzivatel
    {
        return $this->uzivatel;
    }

    public function setUzivatel($uzivatel): self    //?Uzivatel
    {
        $this->uzivatel = $uzivatel;

        return $this;
    }

    public function getStav(): ?int
    {
        return $this->stav;
    }

    public function setStav(int|Stav $stav): self
    {
        if ($stav instanceof Stav) {$stav = $stav->value;}
        
        $this->stav = $stav;

        return $this;
    }

    public function getFormaUhradyBankovniUcet(): ?string
    {
        return $this->forma_uhrady_bankovni_ucet;
    }

    public function setFormaUhradyBankovniUcet(?string $forma_uhrady_bankovni_ucet): self
    {
        $this->forma_uhrady_bankovni_ucet = $forma_uhrady_bankovni_ucet;

        return $this;
    }

  
}
