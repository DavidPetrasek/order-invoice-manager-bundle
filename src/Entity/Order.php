<?php

namespace Psys\OrderInvoiceManagerBundle\Entity;

use BackedEnum;
use Doctrine\Common\Collections\ArrayCollection;
use Psys\OrderInvoiceManagerBundle\Repository\OrderRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;

use Psys\OrderInvoiceManagerBundle\Model\OrderManager\PaymentMode;
use Psys\OrderInvoiceManagerBundle\Model\OrderManager\State;
use Psys\OrderInvoiceManagerBundle\Model\UserInterface;


#[ORM\Entity(repositoryClass: OrderRepository::class)]
#[ORM\Table (name: 'oimb_order')]
class Order
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(options:["unsigned" => true])]
    private ?int $id = null;

    #[ORM\OneToMany(mappedBy: 'order', targetEntity: Product::class, orphanRemoval: true, cascade: ['persist'])]
    private Collection $products;
    
    #[ORM\Column(type: Types::SMALLINT, options:["unsigned" => true])]
    private ?int $payment_mode = null;
    
    #[ORM\Column(nullable: true, type: Types::DECIMAL, precision: 14, scale: 2)]
    private ?string $price_vat_included = '0.00';
    
    #[ORM\Column(nullable: true, type: Types::DECIMAL, precision: 14, scale: 2)]
    private ?string $price_vat_excluded = '0.00';
    
    #[ORM\Column(nullable: true, type: Types::DECIMAL, precision: 14, scale: 2)]
    private ?string $price_vat_base = '0.00';
    
    #[ORM\Column(nullable: true, type: Types::DECIMAL, precision: 14, scale: 2)]
    private ?string $price_vat = '0.00';
    
    #[ORM\Column]
    private \DateTimeImmutable $created_at;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $paid_at = null;

    #[ORM\OneToOne(inversedBy: 'order', cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?Invoice $invoice = null;

    #[ORM\Column(type: Types::SMALLINT, options:["unsigned" => true])]
    private ?int $category = null;

    #[ORM\ManyToOne(targetEntity: UserInterface::class)] //, inversedBy: 'orders'
    #[ORM\JoinColumn(nullable: true, onDelete: 'SET NULL')]
    private ?UserInterface $user = null;

    #[ORM\Column(type: Types::SMALLINT, options:["unsigned" => true])]
    private ?int $state = null;

    #[ORM\Column(length: 20, nullable: true)]
    private ?string $payment_mode_bank_account = null;


    public function __construct()
    {
        $this->products = new ArrayCollection();
    }
    
    public function getId(): ?int
    {
        return $this->id;
    }
    
    /**
     * @return Collection<int, Product>
     */
    public function getProducts(): Collection
    {
        return $this->products;
    }

    public function addProducts(Product $products): static
    {
        if (!$this->products->contains($products)) {
            $this->products->add($products);
            $products->setOrder($this);
        }

        return $this;
    }

    public function removeProducts(Product $products): static
    {
        if ($this->products->removeElement($products)) {
            // set the owning side to null (unless already changed)
            if ($products->getOrder() === $this) {
                $products->setOrder(null);
            }
        }

        return $this;
    }
    
    public function getPaymentMode(): ?PaymentMode
    {
        return PaymentMode::from($this->payment_mode);
    }
    
    public function setPaymentMode(int|PaymentMode $payment_mode): self
    {
        if ($payment_mode instanceof PaymentMode) {$payment_mode = $payment_mode->value;}
        
        $this->payment_mode = $payment_mode;
        
        return $this;
    }
    
    public function getPriceVatIncluded(): ?float
    {
        return $this->price_vat_included;
    }
    
    public function setPriceVatIncluded(?float $price_vat_included): self
    {
        $this->price_vat_included = $price_vat_included;
        
        return $this;
    }
    
    public function getPriceVatExcluded(): ?float
    {
        return $this->price_vat_excluded;
    }
    
    public function setPriceVatExcluded(?float $price_vat_excluded): self
    {
        $this->price_vat_excluded = $price_vat_excluded;
        
        return $this;
    }
    
    public function getPriceVatBase(): ?float
    {
        return $this->price_vat_base;
    }
    
    public function setPriceVatBase(?float $price_vat_base): self
    {
        $this->price_vat_base = $price_vat_base;
        
        return $this;
    }
    
    public function getPriceVat(): ?float
    {
        return $this->price_vat;
    }
    
    public function setPriceVat(?float $price_vat): self
    {
        $this->price_vat = $price_vat;
        
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
    

    public function getPaidAt(): ?\DateTimeImmutable
    {
        return $this->paid_at;
    }

    public function setPaidAt(?\DateTimeImmutable $paid_at): self
    {        
        $this->paid_at = $paid_at;

        return $this;
    }

    public function getInvoice(): ?Invoice
    {
        return $this->invoice;
    }

    public function setInvoice(Invoice $invoice): self
    {
        $this->invoice = $invoice;

        return $this;
    }

    public function getCategory(): ?BackedEnum
    {
        return BackedEnum::from($this->category);
    }

    public function setCategory(int|BackedEnum $category): self
    {
        if ($category instanceof BackedEnum) {$category = $category->value;}
        
        $this->category = $category;

        return $this;
    }

    public function getUser(): ?UserInterface
    {
        return $this->user;
    }

    public function setUser(?UserInterface $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getState(): State
    {
        return State::from($this->state);
    }

    public function setState(int|State $state): self
    {
        if ($state instanceof State) {$state = $state->value;}
        
        $this->state = $state;

        return $this;
    }

    public function getPaymentModeBankAccount(): ?string
    {
        return $this->payment_mode_bank_account;
    }

    public function setPaymentModeBankAccount(?string $payment_mode_bank_account): self
    {
        $this->payment_mode_bank_account = $payment_mode_bank_account;

        return $this;
    }

  
}
