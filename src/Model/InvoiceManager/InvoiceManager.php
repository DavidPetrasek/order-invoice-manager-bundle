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
        $this->entityManager->getConnection()->executeStatement('LOCK TABLES oimb_invoice WRITE;');
        
        $variableSymbol = $this->generateUniqueVariableSymbol();
        
        $dbConn->executeStatement
        (
            "UPDATE oimb_invoice SET variable_symbol = :variable_symbol WHERE id = :invoice_id;",
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
        $this->entityManager->getConnection()->executeStatement('LOCK TABLES oimb_settings WRITE;');
        
        if      ($invoiceSpecific instanceof InvoiceProforma) {$type = 'proforma';}
        else if ($invoiceSpecific instanceof InvoiceFinal)    {$type = 'final';}

        $resultSet = $dbConn->executeQuery
        (
            "SELECT value FROM oimb_settings WHERE option = :option;",
            [
                'option' => "invoice_{$type}_sequential_number"
            ]
        );
        $invoiceSpecific->setSequentialNumber($resultSet->fetchOne());
        
        $dbConn->executeStatement
        (
            "UPDATE oimb_settings SET value = value+1 WHERE option = :option;",
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
            SELECT variable_symbol FROM oimb_invoice 
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
    

    public function invoice_kazdorocni_reset_poradoveho_cisla()
    {
        $debug = '';

        $aktRok = date("Y");  $debug .= '<br> Aktuální rok: '.$aktRok;

        $now = time() + (60*10);   
        $dalsiRok = date("Y", $now);    $debug .= '<br> Další rok (10 minut v budoucnosti): '.$dalsiRok;

        if ($aktRok !== $dalsiRok) 
        {
            // Počkat na další rok
            sleep (60);    
            while (date("Y") === $aktRok)
            {
                sleep (10); $debug .= '<br> Čeká se na další rok: '.$dalsiRok;
            }
            
            $dbConn = $this->entityManager->getConnection();
            $this->entityManager->getConnection()->executeStatement('LOCK TABLES invoice_nastaveni WRITE;');
                       
            $dbConn->executeStatement
            (
                "UPDATE invoice_nastaveni SET hodnota = 1 WHERE moznost = 'proforma_poradove_cislo' OR moznost = 'koncova_poradove_cislo';"
            );
            
            $dbConn->executeStatement('UNLOCK TABLES;');
        }
        
        $this->entityManager->flush();
        
        return new Response($debug);
    }
}




    //     public function vystavit ()
//     {
//         $this->pripravitDatumSplatnostiVystaveni();
        
//         if ($this->typ === Typ::PROFORMA) 
//         {
//             $this->nastavitAdresarPDF($_ENV['ADRESAR_FAKTURA_PROFORMA']);
//             return $this->vystavitProformu();
//         }
//         else if ($this->typ === Typ::KONCOVA)
//         {
//             $this->nastavitAdresarPDF($_ENV['ADRESAR_FAKTURA_KONCOVA']);
//             return $this->vystavitKoncovou();
//         }
//     }

        // public function vystavitKoncovou (Order $order, $zbozi = null)
    // {  
    //     $this->formaUhrady = $order->getPaymentMode();
    //     $this->formaUhradyBankovniUcet = $order->getPaymentModeBankovniUcet();
    //     $this->variableSymbol = $order->getInvoice()->getSymbolVariabilni();
    //     $this->cisloEvidencniProforma = $order->getInvoice()->getInvoiceProforma()->getCisloEvidencni();
        
    //     if ($zbozi === null) {$zbozi = $order->getZbozi();}
    //     else 
    //     {
    //         $order->setZbozi($zbozi);
    //         $this->entityManager->persist($order);
    //     }
    //     $this->zbozi = $zbozi;
        
    //     $this->dodavatelEntita = $order->getInvoice()->getInvoiceSeller();
    //     $this->odberatelEntita = $order->getInvoice()->getInvoiceBuyer();
    //     $this->pripravitDatumSplatnostiVystaveni();
        
    //     $cenyCelkem = $this->pripravitCenyCelkem ($order);
    //     $this->nastavitCenyCelkem($cenyCelkem);
        
    //     $koncovaInvoiceEntity = new InvoiceFinal();
    //     $koncovaInvoiceEntity->setCisloPoradove($this->cisloPoradove);
    //     $koncovaInvoiceEntity->setCisloEvidencni($this->cisloEvidencniFinal);
    //     $koncovaInvoiceEntity->setVytvorenoDatumCas($this->datumCasVystaveniDB);
        
    //     $fakturaEntita = $order->getInvoice();
    //     $fakturaEntita->setInvoiceFinal($koncovaInvoiceEntity);
        
    //     $this->entityManager->persist($koncovaInvoiceEntity);
    //     $this->entityManager->persist($fakturaEntita);
        
    //     $this->entityManager->flush();
        
    //     return
    //     [
    //         'fakturaEntita' => $fakturaEntita,
    //         'fakturaAbsCesta' => $fakturaAbsCesta
    //     ];
    // }


     // public function createInvoiceProforma ($fakturaEntity) : InvoiceProforma
    // {        
    //     $ent_InvoiceProforma = new InvoiceProforma();
    //     $ent_InvoiceProforma->setCisloPoradove($this->cisloPoradove);
    //     $ent_InvoiceProforma->setCisloEvidencni($this->cisloEvidencniProforma);
    //     $ent_InvoiceProforma->setVytvorenoDatumCas($this->datumCasVystaveniDB);
    //     $ent_InvoiceProforma->setDatumSplatnosti( $this->datumCasSplatnostiDB );        
        
    //     $fakturaEntity->setSymbolVariabilni( $this->variableSymbol );
    //     $fakturaEntity->setInvoiceProforma($ent_InvoiceProforma);
        
    //     $fakturaEntity->setInvoiceSeller($this->dodavatelEntita);
    //     $fakturaEntity->setInvoiceBuyer($this->odberatelEntita);
        
    //     $this->entityManager->persist($fakturaEntity);
    //     $this->entityManager->persist($ent_InvoiceProforma);
        
    //     $this->entityManager->flush();
        
    //     return $fakturaEntity;
    // }

    // // TODO: Jelikož šablona, formáty datumů, způsob generování var symbolu, a PDF adresář se neukládají v databázi a zboží se může změnit na faktuře, můžou se poslat jako argument
    // function pregenerovat (Order $ent_Order)
    // {
    //     $debug = '<br>PROFORMA :: ';
            
    //     // Lokální server
    //     // if ($ent_Order->getId() !== 205) {continue;}
    //     // Produkční server
    //     // if ($ent_Order->getId() !== 15) {continue;}

    //     $fakturaEntita = $ent_Order->getInvoice();
    //     $proformaInvoice = $fakturaEntita->getInvoiceProforma();
    //     $koncovaInvoice = $fakturaEntita->getInvoiceFinal();
        
    //     // PROFORMA
    //     $this->nastavitTyp(Typ::PROFORMA);
    //     $this->nastavitAdresarPDF($_ENV['ADRESAR_FAKTURA_PROFORMA']);
    //     $this->nastavitFormuUhrady ($ent_Order->getPaymentMode());
    //     $this->nastavitSymbolVariabilni($proformaInvoice->getCisloEvidencni());
    //     $this->nastavitZbozi($ent_Order->getZbozi());
        
    //     $cenyCelkem = $this->pripravitCenyCelkem ($ent_Order);            
    //     $this->nastavitCenyCelkem($cenyCelkem);
        
    //     $this->nastavitSablonaPDF('faktura/knp_snappy.html.twig');
    //     // $this->nastavitSablonaPDF('faktura/knp_snappy_test.html.twig');
        
    //     $rozdil = $proformaInvoice->getVytvorenoDatumCas()->diff($proformaInvoice->getDatumSplatnosti());
    //     $this->nastavitSplatnostDny($rozdil->days);
    //     $this->nastavitDatumCasSplatnostiFormat('d. MMMM y');
    //     $this->nastavitDatumCasVystaveniFormat('d. MMMM y');
    //     $this->nastavitDatumCasVystaveniDB($proformaInvoice->getVytvorenoDatumCas());
    //     $this->nastavitDatumCasSplatnostiDB($proformaInvoice->getDatumSplatnosti());
    //     $this->pripravitDatumSplatnostiVystaveni();
        
    //     $this->nastavitPaymentModeBankovniUcet($ent_Order->getPaymentModeBankovniUcet());
    //     $this->nastavitCisloEvidencniProforma($proformaInvoice->getCisloEvidencni());
       
    //     $this->nastavitDodavatele ($fakturaEntita->getInvoiceSeller());
    //     $this->nastavitOdberatele($fakturaEntita->getInvoiceBuyer());
        
    //     $proformaInvoiceNameFileSystem = $proformaInvoice->getSoubor()->getNameFileSystem();
    //     $debug .= 'NameFileSystem: '.$proformaInvoiceNameFileSystem;
        
    //     $absCestaPDF = $this->projectDir.$_ENV['ADRESAR_FAKTURA_PROFORMA'].$proformaInvoiceNameFileSystem;
       
    //     $this->vytvoritPDF($absCestaPDF);
        
        
    //     // KONCOVÁ
    //     if ($koncovaInvoice) 
    //     {
    //         $debug .= '<br>KONCOVÁ :: ';
            
    //         $this->nastavitTyp(Typ::KONCOVA);
    //         $this->nastavitCisloEvidencniFinal($koncovaInvoice->getCisloEvidencni());
    //         $this->nastavitDatumCasVystaveniDB($koncovaInvoice->getVytvorenoDatumCas());
    //         $this->pripravitDatumSplatnostiVystaveni();
            
    //         $koncovaInvoiceNameFileSystem = $koncovaInvoice->getSoubor()->getNameFileSystem();
    //         $debug .= 'NameFileSystem: '.$koncovaInvoiceNameFileSystem;
            
    //         $absCestaPDF = $this->projectDir.$_ENV['ADRESAR_FAKTURA_KONCOVA'].$koncovaInvoiceNameFileSystem;
            
    //         $this->vytvoritPDF($absCestaPDF); 
    //     }
        
    //     return $debug;
    // }