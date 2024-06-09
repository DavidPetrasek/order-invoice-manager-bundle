<?php

namespace Psys\OrderInvoiceManagerBundle\Entity;

use Psys\OrderInvoiceManagerBundle\Repository\InvoiceSellerRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: InvoiceSellerRepository::class)]
#[ORM\Table (name: 'oimb_invoice_seller')]
class InvoiceSeller
{
    use SubjectAddressTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(options:["unsigned" => true])]
    private ?int $id = null;

    #[ORM\OneToOne(inversedBy: 'invoice_seller', cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?Invoice $invoice = null;
}
