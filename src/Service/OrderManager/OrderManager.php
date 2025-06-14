<?php
namespace Psys\OrderInvoiceManagerBundle\Service\OrderManager;

use Psys\OrderInvoiceManagerBundle\Entity\Order;

use Doctrine\ORM\EntityManagerInterface;
use Psys\OrderInvoiceManagerBundle\Entity\OrderItem;
use Psys\Utils\Math;


class OrderManager
{    
    public function __construct
    (
        private EntityManagerInterface $entityManager,
        private Math $math
    )
    {}    
    
    function processAndSaveNewOrder (Order $ent_Order)
    {    
        $orderTotals = $this->calculateOrderTotals($ent_Order);

        $ent_Order->setPriceVatIncluded ($orderTotals['vatIncluded']);
        $ent_Order->setPriceVatExcluded ($orderTotals['vatExcluded']);
        $ent_Order->setPriceVatBase ($orderTotals['vatBase']);
        $ent_Order->setPriceVat ($orderTotals['vat']);
        
        $this->entityManager->persist($ent_Order);        
        $this->entityManager->flush();
    }

    // TODO
    // function remove(Order $ent_Order)
    // {
    //     $this->entityManager->remove($ent_Order);        
    //     $this->entityManager->flush();
    // }
    
    public function calculateOrderTotals (Order $ent_Order)
    {
        $priceVatExcludedTotal = $priceVatIncludedTotal = $vatBase = 0;
        
        foreach ($ent_Order->getOrderItems() as $orderItem)
        {
            $orderItemTotals = $this->calculateOrderItemTotals($orderItem);
            $amount = $orderItem->getAmount();
            
            if ($orderItem->getVatRate() > 0) {$vatBase += $orderItemTotals['priceVatExcluded'] * $amount;}
            $priceVatIncludedTotal += $orderItemTotals['priceVatIncluded'] * $amount;
            $priceVatExcludedTotal += $orderItemTotals['priceVatExcluded'] * $amount;
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

    private function calculateOrderItemTotals (OrderItem $orderItem) : array
    {                
        $priceVatIncluded = $orderItem->getPriceVatIncluded();
        $priceVatExcluded = $orderItem->getPriceVatExcluded();

        // Calculate price exclusive of VAT from price inclusive of VAT
        if (!empty($priceVatIncluded)) 
        {            
            $priceVatExcludedRes = $this->math->subtractPercentage($priceVatIncluded, $orderItem->getVatRate());
            $orderItem->setPriceVatExcluded($priceVatExcludedRes);
        }

        // Calculate price inclusive of VAT from price exclusive of VAT
        else if (!empty($priceVatExcluded)) 
        {
            $priceVatIncluded = $this->math->addPercentage($priceVatExcluded, $orderItem->getVatRate());
            $orderItem->setPriceVatIncluded($priceVatIncluded);
        }
        
        $orderItem->setVat($priceVatIncluded - $priceVatExcluded);

        return 
        [
            'priceVatIncluded' => $priceVatIncluded,
            'priceVatExcluded' => $priceVatExcluded,
        ];
    }
}

?>