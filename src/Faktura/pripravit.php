<?php
namespace Psys\SimpleOrderInvoice\Faktura;

trait pripravit
{    
    public function pripravitDatumSplatnostiVystaveni ()
    {        
        if ( !isset($this->datumCasVystaveniDB) )
        {
            $datumVystaveni = "now";            
            $this->datumCasVystaveniDB = new \DateTimeImmutable($datumVystaveni);
        }

        if ( $this->typ === Typ::PROFORMA )
        {
            if ( !isset($this->datumCasSplatnostiDB) )
            {
                $datumSplatnosti = "now + $this->splatnostDny days";                
                $this->datumCasSplatnostiDB = new \DateTimeImmutable($datumSplatnosti);
            }
            
            $formatterVystaveni = new \IntlDateFormatter('cs_CZ', \IntlDateFormatter::LONG, \IntlDateFormatter::NONE, null, null, $this->datumCasVystaveniFormat);
            $this->datumCasVystaveniText = $formatterVystaveni->format($this->datumCasVystaveniDB);
            
            $formatterSplatnosti = new \IntlDateFormatter('cs_CZ', \IntlDateFormatter::LONG, \IntlDateFormatter::NONE, null, null, $this->datumCasSplatnostiFormat);
            $this->datumCasSplatnostiText = $formatterSplatnosti->format($this->datumCasSplatnostiDB);
        }
        
        else if ( $this->typ === Typ::KONCOVA )
        {            
            $formatterVystaveni = new \IntlDateFormatter('cs_CZ', \IntlDateFormatter::LONG, \IntlDateFormatter::NONE, null, null, $this->datumCasVystaveniFormat);
            $this->datumCasVystaveniText = $formatterVystaveni->format($this->datumCasVystaveniDB);
        }
    }
}







?>