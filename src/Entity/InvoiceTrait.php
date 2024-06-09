<?php
namespace Psys\OrderInvoiceManagerBundle\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;


trait InvoiceTrait
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(options:["unsigned" => true])]
    private ?int $id = null;

    #[ORM\Column(type: Types::BIGINT)]
    private ?string $sequential_number = null;

    #[ORM\Column(type: Types::BIGINT)]
    private ?string $reference_number = null;

    #[ORM\Column]
    private \DateTimeImmutable $created_at;


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getInvoice(): ?Invoice
    {
        return $this->invoice;
    }

    public function getSequentialNumber(): ?string
    {
        return $this->sequential_number;
    }

    public function setSequentialNumber(string $sequential_number): self
    {
        $this->sequential_number = $sequential_number;

        return $this;
    }

    public function getReferenceNumber(): ?string
    {
        return $this->reference_number;
    }

    public function setReferenceNumber(string $reference_number): self
    {
        $this->reference_number = $reference_number;

        return $this;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->created_at;
    }
    
    public function setCreatedAt(\DateTimeImmutable $created_at): self
    {        
        $this->created_at = $created_at;
        
        return $this;
    }
}





?>