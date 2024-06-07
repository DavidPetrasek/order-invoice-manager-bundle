<?php

namespace Psys\SimpleOrderInvoice\Entity;

use Psys\SimpleOrderInvoice\Repository\FakturaNastaveniRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: FakturaNastaveniRepository::class)]
class FakturaNastaveni
{
    use NastaveniTrait;
    
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::SMALLINT, options:["unsigned" => true])]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    private ?string $moznost = null;

    #[ORM\Column(length: 750, nullable: true)]
    private ?string $hodnota = null;
}
