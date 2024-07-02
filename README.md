# Use case

When you need to create and manage orders and create and export invoices, but you're not running a typical online store, so libraries like Shopify, WooCommerce, etc. would be an overkill.

# Installation

`composer require psys/order-invoice-manager-bundle`


## 1. Add contents from config files (see config folder) to your config files.

## 2. Run

```
symfony console make:migration
symfony console doctrine:migrations:migrate
```


## 3. Initiate DB settings by running

``` sql
INSERT INTO oimb_settings (option, value) VALUES ('invoice_proforma_sequential_number', '1');
INSERT INTO oimb_settings (option, value) VALUES ('invoice_final_sequential_number','1');
```

## 4. Define your method for exporting invoices (optional)

You can use whatever library you want. This example uses Mpdf. 
``` php
namespace App\Lib;

use Doctrine\ORM\EntityManagerInterface;
use Psys\OrderInvoiceManagerBundle\Entity\Order;
use Psys\OrderInvoiceManagerBundle\Model\InvoiceManager\InvoiceManager;
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

## 5. Define your own category for Order (mandatory) or Product (optional)

``` php
namespace App\Lib;

enum MyOrderCategory :int
{
    case FOO = 1;
    case BAR = 1;
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
use Psys\OrderInvoiceManagerBundle\Entity\Product;
use Psys\OrderInvoiceManagerBundle\Model\OrderManager\AmountType;
use Psys\OrderInvoiceManagerBundle\Model\OrderManager\PaymentMode;
use Psys\OrderInvoiceManagerBundle\Model\OrderManager\State;
use App\Lib\MyInvoiceManager;

public function create_order (OrderManager $orderManager, MyInvoiceManager $invoiceManager)
{       
    $ent_Order = new Order();
    $ent_Order->setCategory(MyOrderCategory::FOO);
    $ent_Order->setPaymentMode(PaymentMode::BANK_ACCOUNT_REGULAR);
    $ent_Order->setPaymentModeBankAccount('5465878565/6556');
    $ent_Order->setUser($this->getUser());
    $ent_Order->setCreatedAt(new \DateTimeImmutable());
    $ent_Order->setState(State::NEW);

    $ent_Order->addProducts(
        (new Product())
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
    $invoiceManager->resetSequentialNumbersEveryYear(); // Premade for cron. This cron needs to be run 1 to 10 minutes before a new year.
    $invoiceManager->resetSequentialNumbers();          // Use for resetting sequential numbers whenever you want
}
```