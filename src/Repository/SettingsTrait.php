<?php
namespace Psys\OrderInvoiceManagerBundle\Repository;


trait SettingsTrait
{
    public function getAll(): array
    {
        $nastaveni = $this->findAll();
        $r = [];
        
        foreach ($nastaveni as $nast)
        {
            $r[$nast->getMoznost()] = $nast->getHodnota();
        }
        
        return $r;
    }
}





?>