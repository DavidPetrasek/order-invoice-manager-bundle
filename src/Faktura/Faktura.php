<?php
namespace Psys\SimpleOrderInvoice\Faktura;

use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Symfony\Component\Filesystem\Filesystem;

use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\Query\ResultSetMapping;
use Twig\Environment;
use Psr\Log\LoggerInterface;

use Psys\SimpleOrderInvoice\Entity\Uzivatel;
use Psys\SimpleOrderInvoice\Entity\Objednavka;
use Psys\SimpleOrderInvoice\Entity\FakturaOdberatel;
use Psys\SimpleOrderInvoice\Entity\FakturaDodavatel;
use Psys\SimpleOrderInvoice\Entity\FakturaProforma;
use Psys\SimpleOrderInvoice\Entity\FakturaKoncova;
use Psys\SimpleOrderInvoice\Entity\Soubor;
use Psys\SimpleOrderInvoice\Objednavka\FormaUhrady;


class Faktura
{   
    use pripravit;
    
    private Typ $typ; 
    private FakturaDodavatel $dodavatelEntita;
    private FakturaOdberatel $odberatelEntita;
   
    private int $splatnostDny;
    private string $datumCasVystaveniFormat;
    private string $datumCasSplatnostiFormat;
    
    private FormaUhrady $formaUhrady;
    private $formaUhradyBankovniUcet;
    private array $zbozi;
    private array $cenyCelkem;
    
    private $adresarPDF;
    private $sablonaPDF;
//     private $absCestaPDF;
//     private $qrKodPlatba = '';

    private $cisloPoradove;
    private $cisloEvidencniProforma;
    private $cisloEvidencniKoncova;
    
    private $symbolVariabilni;
    
    private $datumCasVystaveniText;
    private $datumCasVystaveniDB;
    private $datumCasSplatnostiText;
    private $datumCasSplatnostiDB;
    
    
    public function __construct 
    (
        private ManagerRegistry $doctrine, 
//         private FakturaNastaveniRepository $fakturaNastaveniRepository, 
//         private ContainerBagInterface $containerBag, 
        private RequestStack $requestStack,
        private $projectDir,
        private Filesystem $filesystem,
        private Environment $twig,
        private LoggerInterface $vLogger,
    )
    {}
    
    
    public function nastavitTyp (Typ $typ)
    {
        $this->typ = $typ;
    }
    public function nastavitDodavatele (FakturaDodavatel $dodavatelEntita)
    {
        $this->dodavatelEntita = $dodavatelEntita;
    }
    public function nastavitOdberatele (FakturaOdberatel $odberatelEntita)
    {
        $this->odberatelEntita = $odberatelEntita;
    }
    
    public function nastavitDatumCasVystaveniDB ($dt)
    {
        $this->datumCasVystaveniDB = $dt;
    }
    public function nastavitDatumCasSplatnostiDB ($dt)
    {
        $this->datumCasSplatnostiDB = $dt;
    }
    public function nastavitDatumCasVystaveniFormat ($format)
    {
        $this->datumCasVystaveniFormat = $format;
    }
    public function nastavitDatumCasSplatnostiFormat ($format)
    {
        $this->datumCasSplatnostiFormat = $format;
    }
    public function nastavitSplatnostDny ($splatnostDny)
    {
        $this->splatnostDny = $splatnostDny;
    }
    
    public function nastavitCisloEvidencniProforma ($cisloEvidencniProforma)
    {
        $this->cisloEvidencniProforma = $cisloEvidencniProforma;
    }
    public function nastavitCisloEvidencniKoncova ($cisloEvidencniKoncova)
    {
        $this->cisloEvidencniKoncova = $cisloEvidencniKoncova;
    }
    
    public function ziskatCisloPoradove ()
    {
        return $this->cisloPoradove;
    }
    public function nastavitFormuUhrady (FormaUhrady $formaUhrady)
    {
        $this->formaUhrady = $formaUhrady;
    }
    public function nastavitSymbolVariabilni ($symbolVariabilni)
    {
        $this->symbolVariabilni = $symbolVariabilni;
    }
    public function nastavitFormaUhradyBankovniUcet ($formaUhradyBankovniUcet)
    {
        $this->formaUhradyBankovniUcet = $formaUhradyBankovniUcet;
    }
    
    public function nastavitAdresarPDF ($adresarPDF)
    {
        $this->adresarPDF = $adresarPDF;
    }
    public function nastavitSablonaPDF ($sablonaPDF)
    {
        $this->sablonaPDF = $sablonaPDF;
    }
//     public function nastavitAbsCestaPDF ($absCesta)
//     {
//         $this->absCestaPDF = $absCesta;
//     }
    
    public function nastavitZbozi ($zbozi)
    {
        $this->zbozi = $zbozi;
    }
    public function nastavitCenyCelkem ($cenyCelkem)
    {
        $this->cenyCelkem = $cenyCelkem;
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
    
    public function pripravitKoncovou ()
    {
        $entityManager = $this->doctrine->getManager();
        $this->typ = Typ::KONCOVA;
        $this->adresarPDF = $_ENV['ADRESAR_FAKTURA_KONCOVA'];
        
        $dbConn = $entityManager->getConnection();
        $entityManager->getConnection()->executeStatement('LOCK TABLES faktura_nastaveni WRITE;');
        
        // Získat a navýšit pořadové číslo
        $resultSet = $dbConn->executeQuery
        (
            "SELECT hodnota FROM faktura_nastaveni WHERE moznost = :moznost;",
            [
                'moznost' => 'koncova_poradove_cislo'
            ]
            );
        $this->cisloPoradove = $resultSet->fetchOne();    //
        
        $dbConn->executeStatement
        (
            "UPDATE faktura_nastaveni SET hodnota = hodnota+1 WHERE moznost = :moznost;",
            [
                'moznost' => 'koncova_poradove_cislo'
            ]
            );
        
        $dbConn->executeStatement('UNLOCK TABLES;');
    }

    public function pripravitCenyCelkem (Objednavka $objednavka)
    {        
        return
        [
            'vcetneDPH' => $objednavka->getCenaCelkemVcetneDPH(),
            'bezDPH' => $objednavka->getCenaCelkemBezDPH(),
            'zakladDPH' => $objednavka->getCenaCelkemZakladDPH(),
            'vyseDPH' => $objednavka->getCenaCelkemVyseDPH(),
        ];
    }
    
    public function vystavitKoncovou (Objednavka $objednavka, $zbozi = null)
    {  
        $entityManager = $this->doctrine->getManager();
        $this->formaUhrady = $objednavka->getFormaUhrady();
        $this->formaUhradyBankovniUcet = $objednavka->getFormaUhradyBankovniUcet();
        $this->symbolVariabilni = $objednavka->getFaktura()->getSymbolVariabilni();
        $this->cisloEvidencniProforma = $objednavka->getFaktura()->getFakturaProforma()->getCisloEvidencni();
        
        if ($zbozi === null) {$zbozi = $objednavka->getZbozi();}
        else 
        {
            $objednavka->setZbozi($zbozi);
            $entityManager->persist($objednavka);
        }
        $this->zbozi = $zbozi;
        
        $this->dodavatelEntita = $objednavka->getFaktura()->getFakturaDodavatel();
        $this->odberatelEntita = $objednavka->getFaktura()->getFakturaOdberatel();
        $this->pripravitDatumSplatnostiVystaveni();
        
        $cenyCelkem = $this->pripravitCenyCelkem ($objednavka);
        $this->nastavitCenyCelkem($cenyCelkem);

        $fakturaAbsCesta = $this->vytvoritPDF(null);
        
        // Dokončit uložení do DB
        $koncovaFakturaEntity = new FakturaKoncova();
        $koncovaFakturaEntity->setCisloPoradove($this->cisloPoradove);
        $koncovaFakturaEntity->setCisloEvidencni($this->cisloEvidencniKoncova);
        $koncovaFakturaEntity->setVytvorenoDatumCas($this->datumCasVystaveniDB);
        
        $fakturaEntita = $objednavka->getFaktura();
        $fakturaEntita->setFakturaKoncova($koncovaFakturaEntity);
        
        $entityManager->persist($koncovaFakturaEntity);
        $entityManager->persist($fakturaEntita);
        
        $entityManager->flush();
        
        return
        [
            'fakturaEntita' => $fakturaEntita,
            'fakturaAbsCesta' => $fakturaAbsCesta
        ];
    }
    
    
    public function pripravitProformu ($symbolVariabilniNahodny = true)
    {
        $entityManager = $this->doctrine->getManager();
        $this->typ = Typ::PROFORMA;
        $this->adresarPDF = $_ENV['ADRESAR_FAKTURA_PROFORMA'];
        $this->pripravitDatumSplatnostiVystaveni();
        
        // získat ID nové faktury
        $fakturaEntity = new \Psys\SimpleOrderInvoice\Entity\Faktura();
        $entityManager->persist($fakturaEntity);
        $entityManager->flush();
        $fakturaNovaID = $fakturaEntity->getId();
                
        
        $dbConn = $entityManager->getConnection();
        $entityManager->getConnection()->executeStatement('LOCK TABLES faktura WRITE, faktura_nastaveni WRITE;');
        
        // Variabilní symbol        
        if ($symbolVariabilniNahodny)
        {     
            $this->symbolVariabilni = $this->generovatSymbolVariabilni($entityManager);  //
            
            $dbConn->executeStatement
            (
                "UPDATE faktura SET symbol_variabilni = :symbol_variabilni WHERE id = :faktura_id;",
                [
                    'symbol_variabilni' => $this->symbolVariabilni,
                    'faktura_id' => $fakturaNovaID
                ]
            );
        }
        
        // Získat a navýšit pořadové číslo
        $resultSet = $dbConn->executeQuery
        (
            "SELECT hodnota FROM faktura_nastaveni WHERE moznost = :moznost;",
            [
                'moznost' => 'proforma_poradove_cislo'
            ]
            );
        $this->cisloPoradove = $resultSet->fetchOne();    //
        
        $dbConn->executeStatement
        (
            "UPDATE faktura_nastaveni SET hodnota = hodnota+1 WHERE moznost = :moznost;",
            [
                'moznost' => 'proforma_poradove_cislo'
            ]
            );
        
        $dbConn->executeStatement('UNLOCK TABLES;');
        
        return $fakturaEntity;
    }
    
    public function vystavitProformu ($fakturaEntity)
    {   
        $entityManager = $this->doctrine->getManager();
        $fakturaAbsCesta = $this->vytvoritPDF(null);        
        
        // Dokončit uložení do DB
        $proformaFakturaEntity = new FakturaProforma();
        $proformaFakturaEntity->setCisloPoradove($this->cisloPoradove);
        $proformaFakturaEntity->setCisloEvidencni($this->cisloEvidencniProforma);
        $proformaFakturaEntity->setVytvorenoDatumCas($this->datumCasVystaveniDB);
        $proformaFakturaEntity->setDatumSplatnosti( $this->datumCasSplatnostiDB );        
        
        $fakturaEntity->setSymbolVariabilni( $this->symbolVariabilni );
        $fakturaEntity->setFakturaProforma($proformaFakturaEntity);
        
        $fakturaEntity->setFakturaDodavatel($this->dodavatelEntita);
        $fakturaEntity->setFakturaOdberatel($this->odberatelEntita);
        
        $entityManager->persist($fakturaEntity);
        $entityManager->persist($proformaFakturaEntity);
        
        $entityManager->flush();
        
        return
        [
            'fakturaEntita' => $fakturaEntity,
            'fakturaAbsCesta' => $fakturaAbsCesta
        ];
    }
    
    private function generovatSymbolVariabilni ($entityManager)
    {
        $symbolVariabilni = rand(1000000000, 9999999999);
        
        $rsm = new ResultSetMapping();
        $rsm->addScalarResult('symbol_variabilni', 'symbol_variabilni');
        $query = $entityManager->createNativeQuery
        ('
            SELECT symbol_variabilni FROM faktura 
            WHERE symbol_variabilni = ?'
        , $rsm);
        $query->setParameter(1, $symbolVariabilni);
        $kodVarDB = $query->getResult();     //
        
        if (!empty($kodVarDB))
        {
            $symbolVariabilni = $this->generovatSymbolVariabilni ($entityManager);
        }
        
        return $symbolVariabilni;
    }   
    
    public function vytvoritPDF ($absCestaPDF = null)
    {        
//         dd($this->zbozi);
        // $this->twig->enableAutoReload();
        $html = $this->twig->render($this->sablonaPDF,
            [
                'typ'  => $this->typ->value,
                
                'cisloEvidencniProforma'  => $this->cisloEvidencniProforma,
                'cisloEvidencniKoncova'  => $this->cisloEvidencniKoncova,
                'cisloPoradove'  => $this->cisloPoradove,
                
                'dodavatel'  => $this->dodavatelEntita,
                'odberatel'  => $this->odberatelEntita,
                
                'zbozi'  => $this->zbozi,
                'cenyCelkem'  => $this->cenyCelkem,
                
                'formaUhrady'  => $this->formaUhrady->value,
                'formaUhradyBankovniUcet'  => $this->formaUhradyBankovniUcet,
                'symbolVariabilni'  => $this->symbolVariabilni,
                
                'datumCasVystaveniText'  => $this->datumCasVystaveniText,
                'datumCasSplatnostiText'  => $this->datumCasSplatnostiText,
//                 'datumCasDuzpText'  => $this->datumCasDuzpText,
            ]);
        
        if ( !isset($absCestaPDF) )
        {
            try
            {
                $absCestaPDF = $this->filesystem->tempnam($this->projectDir.$this->adresarPDF, '', '.pdf');
            }
            catch (IOExceptionInterface $exception)
            {
                $this->vLogger->error("Výjimka Filesystem při generování nového unikátního souboru", [$exception->getMessage()]);
            }
        }
        


        return $absCestaPDF;
    }  

    public function faktura_kazdorocni_reset_poradoveho_cisla()
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
            $this->entityManager->getConnection()->executeStatement('LOCK TABLES faktura_nastaveni WRITE;');
                       
            $dbConn->executeStatement
            (
                "UPDATE faktura_nastaveni SET hodnota = 1 WHERE moznost = 'proforma_poradove_cislo' OR moznost = 'koncova_poradove_cislo';"
            );
            
            $dbConn->executeStatement('UNLOCK TABLES;');
        }
        
        $this->entityManager->flush();
        
        return new Response($debug);
    }

    // // TODO: Jelikož šablona, formáty datumů, způsob generování var symbolu, a PDF adresář se neukládají v databázi a zboží se může změnit na faktuře, můžou se poslat jako argument
    // function pregenerovat (Objednavka $ent_Objednavka)
    // {
    //     $debug = '<br>PROFORMA :: ';
            
    //     // Lokální server
    //     // if ($ent_Objednavka->getId() !== 205) {continue;}
    //     // Produkční server
    //     // if ($ent_Objednavka->getId() !== 15) {continue;}

    //     $fakturaEntita = $ent_Objednavka->getFaktura();
    //     $proformaFaktura = $fakturaEntita->getFakturaProforma();
    //     $koncovaFaktura = $fakturaEntita->getFakturaKoncova();
        
    //     // PROFORMA
    //     $this->nastavitTyp(Typ::PROFORMA);
    //     $this->nastavitAdresarPDF($_ENV['ADRESAR_FAKTURA_PROFORMA']);
    //     $this->nastavitFormuUhrady ($ent_Objednavka->getFormaUhrady());
    //     $this->nastavitSymbolVariabilni($proformaFaktura->getCisloEvidencni());
    //     $this->nastavitZbozi($ent_Objednavka->getZbozi());
        
    //     $cenyCelkem = $this->pripravitCenyCelkem ($ent_Objednavka);            
    //     $this->nastavitCenyCelkem($cenyCelkem);
        
    //     $this->nastavitSablonaPDF('faktura/knp_snappy.html.twig');
    //     // $this->nastavitSablonaPDF('faktura/knp_snappy_test.html.twig');
        
    //     $rozdil = $proformaFaktura->getVytvorenoDatumCas()->diff($proformaFaktura->getDatumSplatnosti());
    //     $this->nastavitSplatnostDny($rozdil->days);
    //     $this->nastavitDatumCasSplatnostiFormat('d. MMMM y');
    //     $this->nastavitDatumCasVystaveniFormat('d. MMMM y');
    //     $this->nastavitDatumCasVystaveniDB($proformaFaktura->getVytvorenoDatumCas());
    //     $this->nastavitDatumCasSplatnostiDB($proformaFaktura->getDatumSplatnosti());
    //     $this->pripravitDatumSplatnostiVystaveni();
        
    //     $this->nastavitFormaUhradyBankovniUcet($ent_Objednavka->getFormaUhradyBankovniUcet());
    //     $this->nastavitCisloEvidencniProforma($proformaFaktura->getCisloEvidencni());
       
    //     $this->nastavitDodavatele ($fakturaEntita->getFakturaDodavatel());
    //     $this->nastavitOdberatele($fakturaEntita->getFakturaOdberatel());
        
    //     $proformaFakturaNameFileSystem = $proformaFaktura->getSoubor()->getNameFileSystem();
    //     $debug .= 'NameFileSystem: '.$proformaFakturaNameFileSystem;
        
    //     $absCestaPDF = $this->projectDir.$_ENV['ADRESAR_FAKTURA_PROFORMA'].$proformaFakturaNameFileSystem;
       
    //     $this->vytvoritPDF($absCestaPDF);
        
        
    //     // KONCOVÁ
    //     if ($koncovaFaktura) 
    //     {
    //         $debug .= '<br>KONCOVÁ :: ';
            
    //         $this->nastavitTyp(Typ::KONCOVA);
    //         $this->nastavitCisloEvidencniKoncova($koncovaFaktura->getCisloEvidencni());
    //         $this->nastavitDatumCasVystaveniDB($koncovaFaktura->getVytvorenoDatumCas());
    //         $this->pripravitDatumSplatnostiVystaveni();
            
    //         $koncovaFakturaNameFileSystem = $koncovaFaktura->getSoubor()->getNameFileSystem();
    //         $debug .= 'NameFileSystem: '.$koncovaFakturaNameFileSystem;
            
    //         $absCestaPDF = $this->projectDir.$_ENV['ADRESAR_FAKTURA_KONCOVA'].$koncovaFakturaNameFileSystem;
            
    //         $this->vytvoritPDF($absCestaPDF); 
    //     }
        
    //     return $debug;
    // }
}