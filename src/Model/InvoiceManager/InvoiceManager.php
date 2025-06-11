<?php
namespace Psys\OrderInvoiceManagerBundle\Model\InvoiceManager;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query\ResultSetMapping;
use Psys\OrderInvoiceManagerBundle\Entity\Invoice;
use Symfony\Component\HttpFoundation\Response;

use Psys\OrderInvoiceManagerBundle\Entity\Order;
use Psys\OrderInvoiceManagerBundle\Entity\InvoiceProforma;
use Psys\OrderInvoiceManagerBundle\Entity\InvoiceFinal;


abstract class InvoiceManager
{   
    public function __construct 
    (
        private EntityManagerInterface $entityManager,
    )
    {}  
    
    public function setUniqueVariableSymbol (Invoice $invoice)
    {   
        $dbConn = $this->entityManager->getConnection();
        $this->entityManager->getConnection()->executeStatement('LOCK TABLES oim_invoice WRITE;');
        
        $variableSymbol = $this->generateUniqueVariableSymbol();
        
        $dbConn->executeStatement
        (
            "UPDATE oim_invoice SET variable_symbol = :variable_symbol WHERE id = :invoice_id;",
            [
                'variable_symbol' => $variableSymbol,
                'invoice_id' => $invoice->getId()
            ]
        );
        $invoice->setVariableSymbol($variableSymbol);
        
        $dbConn->executeStatement('UNLOCK TABLES;');
    }

    public function setSequentialNumber (InvoiceProforma|InvoiceFinal $invoiceSpecific)
    {        
        $dbConn = $this->entityManager->getConnection();
        $this->entityManager->getConnection()->executeStatement('LOCK TABLES oim_settings WRITE;');
        
        if      ($invoiceSpecific instanceof InvoiceProforma) {$type = 'proforma';}
        else if ($invoiceSpecific instanceof InvoiceFinal)    {$type = 'final';}

        $resultSet = $dbConn->executeQuery
        (
            "SELECT value FROM oim_settings WHERE option = :option;",
            [
                'option' => "invoice_{$type}_sequential_number"
            ]
        );
        $invoiceSpecific->setSequentialNumber($resultSet->fetchOne());
        
        $dbConn->executeStatement
        (
            "UPDATE oim_settings SET value = value+1 WHERE option = :option;",
            [
                'option' => "invoice_{$type}_sequential_number"
            ]
            );
        
        $dbConn->executeStatement('UNLOCK TABLES;');
    }  
        
    private function generateUniqueVariableSymbol ()
    {
        $variableSymbol = rand(1000000000, 9999999999);
        
        $rsm = new ResultSetMapping();
        $rsm->addScalarResult('variable_symbol', 'variable_symbol');
        $query = $this->entityManager->createNativeQuery
        ('
            SELECT variable_symbol FROM oim_invoice 
            WHERE variable_symbol = ?'
        , $rsm);
        $query->setParameter(1, $variableSymbol);
        $kodVarDB = $query->getResult();
        
        if (!empty($kodVarDB))
        {
            $variableSymbol = $this->generateUniqueVariableSymbol ();
        }
        
        return $variableSymbol;
    }
    

    /**
     * Resets the sequential numbers for invoices every year.
     *
     * This method checks if the current year is different from the next year (10 minutes in the future).
     * If the years are different, it waits until the next year and then calls the `resetSequentialNumbers()`
     * method to reset the sequential numbers for both proforma and final invoices.
     *
     * @return Response A debug response containing information about the year changes.
     */
    public function resetSequentialNumbersEveryYear()
    {
        $debug = '';

        $currYear = date("Y");
        $debug .= '<br> Current year: ' . $currYear;

        $now = time() + (60 * 10);
        $nextYear = date("Y", $now);
        $debug .= '<br> Next year (10 minutes in the future): ' . $nextYear;

        if ($currYear !== $nextYear) {
            // Wait for the next year
            sleep(60);
            while (date("Y") === $currYear) {
                sleep(10);
                $debug .= '<br> Waiting for the next year: ' . $nextYear;
            }

            $this->resetSequentialNumbers();
        }

        return new Response($debug);
    }

    public function resetSequentialNumbers()
    {            
        $dbConn = $this->entityManager->getConnection();
        $this->entityManager->getConnection()->executeStatement('LOCK TABLES oim_settings WRITE;');
                    
        $dbConn->executeStatement
        (
            "UPDATE oim_settings SET value = 1 WHERE option = 'invoice_proforma_sequential_number' OR option = 'invoice_final_sequential_number';"
        );
        
        $dbConn->executeStatement('UNLOCK TABLES;');
        $this->entityManager->flush();
    }
}
