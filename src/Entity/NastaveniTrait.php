<?php
namespace Psys\SimpleOrderInvoice\Entity;

use Doctrine\ORM\Mapping as ORM;


trait NastaveniTrait
{
    #[ORM\Column (options:["default" => "CURRENT_TIMESTAMP"]) ]
    private ?\DateTimeImmutable $zmeneno_datum_cas = null;

    
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMoznost(): ?string
    {
        return $this->moznost;
    }

    public function setMoznost(string $moznost): self
    {
        $this->moznost = $moznost;

        return $this;
    }

    public function getHodnota(): ?string
    {
        return $this->hodnota;
    }

    public function setHodnota(?string $hodnota): self
    {
        $this->hodnota = $hodnota;

        return $this;
    }

    public function getZmenenoDatumCas(): ?\DateTimeImmutable
    {
        return $this->zmeneno_datum_cas;
    }

    public function setZmenenoDatumCas(?\DateTimeImmutable $zmeneno_datum_cas = null): static
    {
        if ($zmeneno_datum_cas === null)
        {
            $zmeneno_datum_cas = new \DateTimeImmutable();
        }
        
        $this->zmeneno_datum_cas = $zmeneno_datum_cas;
        
        return $this;
    }
}





?>