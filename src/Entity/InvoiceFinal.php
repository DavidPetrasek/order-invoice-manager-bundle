<?php

namespace Psys\OrderInvoiceManagerBundle\Entity;

use Psys\OrderInvoiceManagerBundle\Repository\InvoiceFinalRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: InvoiceFinalRepository::class)]
#[ORM\Table (name: 'oim_invoice_final')]
class InvoiceFinal
{
    use InvoiceTrait;

    // #[ORM\ManyToOne]
    // #[ORM\JoinColumn(nullable: false)]
    // private ?Soubor $soubor = null;
    
    #[ORM\OneToOne(mappedBy: 'invoice_final', cascade: ['persist', 'remove'])]
    private ?Invoice $invoice = null;

    // public function getSoubor(): ?Soubor
    // {
    //     return $this->soubor;
    // }

    // public function setSoubor(?Soubor $soubor): self
    // {
    //     $this->soubor = $soubor;

    //     return $this;
    // }
}
