<?php
namespace Psys\OrderInvoiceManagerBundle\Model\OrderManager;

use Psys\OrderInvoiceManagerBundle\Entity\Order;

use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Psys\OrderInvoiceManagerBundle\Entity\Invoice;
use Psys\OrderInvoiceManagerBundle\Entity\Product;
use Psys\OrderInvoiceManagerBundle\Model\UserInterface;
use Symfony\Component\Filesystem\Filesystem;


class OrderManager
{    
    public function __construct
    (
        private EntityManagerInterface $entityManager,
        private LoggerInterface $vLogger,
        private Filesystem $filesystem,
        private $projectDir,
    )
    {}    
    
    function processAndSave (Order $ent_Order)
    {    
        $orderTotals = $this->calculateOrderTotals($ent_Order);

        $ent_Order->setPriceVatIncluded ($orderTotals['vatIncluded']);
        $ent_Order->setPriceVatExcluded ($orderTotals['vatExcluded']);
        $ent_Order->setPriceVatBase ($orderTotals['vatBase']);
        $ent_Order->setPriceVat ($orderTotals['vat']);
        
        $this->entityManager->persist($ent_Order);        
        $this->entityManager->flush();
    }

    function remove (Order $ent_Order)
    {                                                
        // $entity_Soubor_k_vymazani = [];

        // $ent_Invoice = $ent_Order->getInvoice();

        // // Soubory na disku
        // $ent_InvoiceProforma = $ent_Invoice->getInvoiceProforma();        
        // if ($ent_InvoiceProforma)
        // {
        //     $entity_Soubor_k_vymazani[] = $this->smazatSoubor($ent_InvoiceProforma, 'ADRESAR_FAKTURA_PROFORMA');
        // }
        // $ent_InvoiceFinal = $ent_Invoice->getInvoiceFinal();        
        // if ($ent_InvoiceFinal)
        // {
        //     $entity_Soubor_k_vymazani[] = $this->smazatSoubor($ent_InvoiceFinal, 'ADRESAR_FAKTURA_KONCOVA');
        // }      
        
        // Databáze
        // foreach ($entity_Soubor_k_vymazani as $entita_Soubor_k_vymazani)
        // {
        //     $this->entityManager->remove($entita_Soubor_k_vymazani);
        // }
        
        $this->entityManager->remove($ent_Order);        
        $this->entityManager->flush();
    }

    // function smazatSoubor ($ent, $envVar)
    // {
    //     $entita_Soubor = $ent->getSoubor();    
    //     $filenameAbsPath = $this->projectDir.$_ENV[$envVar].'/'.$entita_Soubor->getNameFileSystem();
        
    //     try
    //     {
    //         $this->filesystem->remove($filenameAbsPath);
    //     }
    //     catch (IOExceptionInterface $exception)
    //     {
    //         $this->vLogger->error("Výjimka Filesystem při odstraňování", [$exception->getMessage()]);
    //     }

    //     return $entita_Soubor;
    // }
    
    public function calculateOrderTotals (Order $ent_Order)
    {
        $priceVatExcludedTotal = $priceVatIncludedTotal = $vatBase = 0;
        
        foreach ($ent_Order->getProducts() as $product)
        {
            $productTotals = $this->calculateProductTotals($product);
            
            if ($product->getVatRate() > 0) {$vatBase += $productTotals['priceVatExcluded'];}
            $priceVatIncludedTotal += $productTotals['priceVatIncluded'];
            $priceVatExcludedTotal += $productTotals['priceVatExcluded'];
        }
        
        $vatTotal = $priceVatIncludedTotal - $priceVatExcludedTotal;
        
        return
        [
            'vatIncluded' => $priceVatIncludedTotal,
            'vatExcluded' => $priceVatExcludedTotal,
            'vatBase' => $vatBase,
            'vat' => $vatTotal,
        ];
    }

    private function calculateProductTotals (Product $product) : array
    {                
        $priceVatIncluded = $product->getPriceVatIncluded();
        $amountType = $product->getAmountType();

        if ( !is_null($priceVatIncluded) ) 
        {
            if ($amountType === AmountType::ITEM)
            {
                $priceVatIncludedRes = $priceVatIncluded * $product->getAmount();
            }
            
            $priceVatExcludedRes = $this->subtractPercentage ($priceVatIncludedRes, $product->getVatRate());
            $product->setPriceVatExcluded($priceVatExcludedRes);
        }

        // TODO
        // else if ( !is_null($priceVatExcluded) ) 
        // {

        // }
        
        $product->setVat($priceVatIncludedRes - $priceVatExcludedRes);

        return 
        [
            'priceVatIncluded' => $priceVatIncludedRes,
            'priceVatExcluded' => $priceVatExcludedRes,
        ];
    }
    
    private function subtractPercentage ($number, $percentage)
    {
        return $number / ('1.'.$percentage);
    }
}

?>