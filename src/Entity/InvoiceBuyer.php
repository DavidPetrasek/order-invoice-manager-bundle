<?php

namespace Psys\OrderInvoiceManagerBundle\Entity;

use Psys\OrderInvoiceManagerBundle\Repository\InvoiceBuyerRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: InvoiceBuyerRepository::class)]
#[ORM\Table (name: 'oimb_invoice_buyer')]
class InvoiceBuyer
{
    use SubjectAddressTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(options:["unsigned" => true])]
    private ?int $id = null;

    #[ORM\OneToOne(inversedBy: 'invoice_buyer', cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?Invoice $invoice = null;
}
