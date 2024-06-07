<?php
namespace Psys\SimpleOrderInvoice\Objednavka;

use Psys\SimpleOrderInvoice\Entity\Objednavka as EntityObjednavka;

use Psys\SimpleOrderInvoice\Repository\NastaveniRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psys\SimpleOrderInvoice\Objednavka\FormaUhrady;
use Psr\Log\LoggerInterface;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Symfony\Component\Filesystem\Filesystem;

class Objednavka
{
    private array $zbozi;
    private array $cenyCelkem;
    
    public function __construct
    (
        private EntityManagerInterface $entityManager,
        private NastaveniRepository $nastaveniRepository,
        private LoggerInterface $vLogger,
        private Filesystem $filesystem,
        private $projectDir,
    )
    {}
    
    
    public function ziskatZbozi ()
    {
        return $this->zbozi;
    }
    public function odebratVeskereZbozi ()
    {
        $this->zbozi = [];
    }
    public function ziskatCenyCelkem ()
    {
        return $this->cenyCelkem;
    }
    
    
    function vytvorit ($kategorie, $uzivatelEntity, $fakturaEntity, FormaUhrady $formaUhrady, $formaUhradyBankovniUcet = null) : EntityObjednavka
    {   
        $ent_Objednavka = new EntityObjednavka();
        $ent_Objednavka->setKategorie($kategorie);
        $ent_Objednavka->setFaktura($fakturaEntity);
        $ent_Objednavka->setFormaUhrady($formaUhrady);
        $ent_Objednavka->setFormaUhradyBankovniUcet($formaUhradyBankovniUcet);
        $ent_Objednavka->setUzivatel($uzivatelEntity);
        $ent_Objednavka->setVytvorenoDatumCas();
        $ent_Objednavka->setStav(Stav::NEUHRAZENO);
        
        $ent_Objednavka->setCenaCelkemVcetneDph ($this->cenyCelkem['vcetneDPH']);
        $ent_Objednavka->setCenaCelkemBezDph ($this->cenyCelkem['bezDPH']);
        $ent_Objednavka->setCenaCelkemZakladDph ($this->cenyCelkem['zakladDPH']);
        $ent_Objednavka->setCenaCelkemVyseDph ($this->cenyCelkem['vyseDPH']);
        
        $ent_Objednavka->setZbozi($this->zbozi);
        
        $this->entityManager->persist($ent_Objednavka);        
        $this->entityManager->flush();

        return $ent_Objednavka;
    }

    function smazat (EntityObjednavka $ent_Objednavka)
    {                                                
        $entity_Soubor_k_vymazani = [];

        $ent_Faktura = $ent_Objednavka->getFaktura();

        // Soubory na disku
        $ent_FakturaProforma = $ent_Faktura->getFakturaProforma();        
        if ($ent_FakturaProforma)
        {
            $entity_Soubor_k_vymazani[] = $this->smazatSoubor($ent_FakturaProforma, 'ADRESAR_FAKTURA_PROFORMA');
        }
        $ent_FakturaKoncova = $ent_Faktura->getFakturaKoncova();        
        if ($ent_FakturaKoncova)
        {
            $entity_Soubor_k_vymazani[] = $this->smazatSoubor($ent_FakturaKoncova, 'ADRESAR_FAKTURA_KONCOVA');
        }      
        
        // Databáze
        foreach ($entity_Soubor_k_vymazani as $entita_Soubor_k_vymazani)
        {
            $this->entityManager->remove($entita_Soubor_k_vymazani);
        }
        
        $this->entityManager->remove($ent_Objednavka);        
        $this->entityManager->flush();
    }

    function smazatSoubor ($ent, $envVar)
    {
        $entita_Soubor = $ent->getSoubor();    
        $filenameAbsPath = $this->projectDir.$_ENV[$envVar].'/'.$entita_Soubor->getNameFileSystem();
        
        try
        {
            $this->filesystem->remove($filenameAbsPath);
        }
        catch (IOExceptionInterface $exception)
        {
            $this->vLogger->error("Výjimka Filesystem při odstraňování", [$exception->getMessage()]);
        }

        return $entita_Soubor;
    }
    
    
    public function pridatZbozi ($kategorie, $nazev, $popis, $mnozstvi = 1, $cenaVcetneDPH = null, $cenaBezDPH = null, $sazbaDPH = SazbaDPH::ZAKLADNI, $jednotka = 'KUS') //ZboziJednotka::KUS
    {
        if ($sazbaDPH === SazbaDPH::ZAKLADNI) {$moznost = 'sazba_dph_zakladni';}        
        $sazbaDPH_procento = $this->nastaveniRepository->findOneBy( ['moznost' => $moznost] )->getHodnota();
        
        $this->zbozi[] = new ZboziPolozka($kategorie, $nazev, $popis, $mnozstvi, $cenaVcetneDPH, $cenaBezDPH, $sazbaDPH_procento, $jednotka);
    }
    
    public function spocitatCeny ()
    {
        $celkemBezDPH = $celkemVcetneDPH = $celkemZakladDPH = 0;
        
//         dd($this->zbozi);
        foreach ($this->zbozi as $z)
        {
            $z->spocitatCeny();
            
            if ($z->getSazbaDPH() > 0) {$celkemZakladDPH += $z->getCenaBezDPH();}
            $celkemVcetneDPH += $z->getCenaVcetneDPH();
            $celkemBezDPH += $z->getCenaBezDPH();
        }
        
        $vyseDPH = $celkemVcetneDPH - $celkemBezDPH;
        
        $this->cenyCelkem =
        [
            'vcetneDPH' => $celkemVcetneDPH,
            'bezDPH' => $celkemBezDPH,
            'zakladDPH' => $celkemZakladDPH,
            'vyseDPH' => $vyseDPH,
        ];
    }
}

?>