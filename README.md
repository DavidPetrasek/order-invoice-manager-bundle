# Use case

- You're not running a typical online store — full-featured e-commerce platforms would be overkill.
- You need to manage orders and associated invoices.
- You need to export invoices with any PDF library.

# Installation

`composer req psys/order-invoice-manager-bundle`

## 1. Set your customer entity

``` yaml
# config/packages/doctrine.yaml
  orm:
    resolve_target_entities:                                                              
      Psys\OrderInvoiceManagerBundle\Model\CustomerInterface: App\Entity\YourCustomerEntity
```

## 2. Init database

``` command
symfony console make:migration
```
Then rename the `migrations/VersionOimbInit.php` (also the class inside), so it runs just after the migration you've just created.
``` command
symfony console doctrine:migrations:migrate
```

# Optional steps after installation

## 1. Define categories for orders and/or its items

``` php
namespace App\Lib;

enum MyOrderItemCategory :int
{
    case FOO = 1;
    case BAR = 2;
}
```

## 2. Define your method for exporting invoices

You can use whatever library you want. This example uses Mpdf. 
``` php
namespace App\Lib;

use Doctrine\ORM\EntityManagerInterface;
use Psys\OrderInvoiceManagerBundle\Entity\Order;
use Psys\OrderInvoiceManagerBundle\Service\InvoiceManager\InvoiceManager;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;

class MyInvoiceManager extends InvoiceManager
{    
    public function __construct
    (
        private Environment $twig,
        private EntityManagerInterface $entityManager,
    )
    {
        parent::__construct($entityManager);
    }

    public function createPDF (Order $order, $type, string $outputMode)
    {        
        $html = $this->twig->render('invoice/index.html.twig', 
            [
                'order'  => $order,
                'type'  => $type,
            ]);

            $mpdf = new \Mpdf\Mpdf();
            $mpdf->WriteHTML($html);

            if ($outputMode === 'HttpInline') 
            {
                return $mpdf->OutputHttpInline();
            }
    }
}
```


# Example usage

## Creating a new order a and its proforma invoice:
``` php
use Psys\OrderInvoiceManagerBundle\Entity\Invoice;
use Psys\OrderInvoiceManagerBundle\Entity\InvoiceBuyer;
use Psys\OrderInvoiceManagerBundle\Entity\InvoiceProforma;
use Psys\OrderInvoiceManagerBundle\Entity\InvoiceSeller;
use Psys\OrderInvoiceManagerBundle\Entity\Order;
use Psys\OrderInvoiceManagerBundle\Entity\OrderItem;
use Psys\OrderInvoiceManagerBundle\Model\OrderItem\AmountType;
use Psys\OrderInvoiceManagerBundle\Model\Order\PaymentMode;
use Psys\OrderInvoiceManagerBundle\Model\Order\State;
use Symfony\Bundle\SecurityBundle\Security;
use App\Lib\MyInvoiceManager;


public function create_order (OrderManager $orderManager, MyInvoiceManager $invoiceManager, Security $security)
{       
    $ent_Order = new Order();
    $ent_Order->setCategory(MyOrderCategory::FOO);
    $ent_Order->setPaymentMode(PaymentMode::BANK_ACCOUNT_REGULAR);
    $ent_Order->setPaymentModeBankAccount('5465878565/6556');
    $ent_Order->setCustomer($security->getUser()); // Customer can be null
    $ent_Order->setCreatedAt(new \DateTimeImmutable());
    $ent_Order->setState(State::NEW);

    $ent_Order->addOrderItem(
        (new OrderItem())
            ->setName('Foo')
            ->setPriceVatIncluded(1599)    // If not set, it will be automatically calculated from price exclusive of VAT
            ->setPriceVatExcluded(1300)    // If not set, it will be automatically calculated from price inclusive of VAT
            ->setVatRate(21)
            ->setAmount(1)
            ->setAmountType(AmountType::ITEM)
    );

    $ent_InvoiceProforma = (new InvoiceProforma())
    ->setCreatedAt(new \DateTimeImmutable())
    ->setDueDate(new \DateTimeImmutable('+14 days'));
    $invoiceManager->setSequentialNumber($ent_InvoiceProforma);

    $ent_InvoiceProforma->setReferenceNumber(date('Y').$ent_InvoiceProforma->getSequentialNumber()); // Use custom formatting for the reference number

    $ent_Invoice = (new Invoice())
        ->setInvoiceProforma($ent_InvoiceProforma)
        ->setInvoiceBuyer
        (
            (new InvoiceBuyer())
            ->setName('Some Buyer')
            ->setStreetAddress1('Street 123')
            ->setCity('Some City')
            ->setPostcode('25689')
            ->setVatIdentificationNumber('5468484')
            ->setCompanyIdentificationNumber('5655')
        )
        ->setInvoiceSeller
        (
            (new InvoiceSeller())
            ->setName('Some Seller')
            ->setStreetAddress1('Street 123')
            ->setCity('Some City')
            ->setPostcode('25689')
            ->setVatIdentificationNumber('5468484')
            ->setCompanyIdentificationNumber('5655')
        );

    $invoiceManager->setUniqueVariableSymbol($ent_Invoice);
    $ent_Order->setInvoice($ent_Invoice);
    $orderManager->processAndSaveNewOrder($ent_Order);
}
```

## Reseting sequential numbers:
``` php
use App\Lib\MyInvoiceManager;

public function reset_sequential_numbers (MyInvoiceManager $invoiceManager)
{       
    // This method is meant to be used inside a cron. 
    // This cron needs to be run 1 to 10 minutes before a new year.
    $invoiceManager->resetSequentialNumbersEveryYear();

    // Use this method for resetting sequential numbers whenever you want.
    $invoiceManager->resetSequentialNumbers();
}
```