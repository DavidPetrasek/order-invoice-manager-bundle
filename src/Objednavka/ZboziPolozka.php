<?php
namespace Psys\SimpleOrderInvoice\Objednavka;


class ZboziPolozka
{        
    private $cenaVyseDPH;
    
    public function __construct 
    (
        private $kategorie, 
        private $nazev, 
        private $popis, 
        private $mnozstvi, 
        private $cenaVcetneDPH, 
        private $cenaBezDPH, 
        private int $sazbaDPH, 
        private $jednotka //ZboziJednotka
    )
    {}
    
    
    public function getMnozstvi ()
    {
        return $this->mnozstvi;
    }
    public function getKategorie ()
    {
        return $this->kategorie;
    }
    public function getNazev ()
    {
        return $this->nazev;
    }
    
    public function getPopis ()
    {
        return $this->popis;
    }
    public function setPopis ($popis)
    {
        $this->popis = $popis;
    }
    
    public function getCenaBezDPH ()
    {
        return $this->cenaBezDPH;
    }
    
//     public function nastavitCenuVcetneDPH ($cenaVcetneDPH)
//     {
//         $this->cenaVcetneDPH = $cenaVcetneDPH;
//     }
    public function getCenaVcetneDPH ()
    {
        return $this->cenaVcetneDPH;
    }
    
    public function getCenaVyseDPH ()
    {
        return $this->cenaVyseDPH;
    }
    public function getSazbaDPH ()
    {
        return $this->sazbaDPH;
    }
    
    
    public function spocitatCeny ()
    {                
        if ( isset($this->cenaVcetneDPH) ) 
        {
            if ($this->jednotka === 'KUS') //ZboziJednotka::KUS
            {
                $this->cenaVcetneDPH *= $this->mnozstvi;
            }
            
            $this->cenaBezDPH = $this->castkaOdecistProc ($this->cenaVcetneDPH, $this->sazbaDPH);
        }
        
        $this->cenaVyseDPH = $this->cenaVcetneDPH - $this->cenaBezDPH;
    }
    
    private function castkaOdecistProc ($celkem, $proc)
    {
        return $celkem / ('1.'.$proc);
    }
}

?>