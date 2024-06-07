<?php
namespace Psys\SimpleOrderInvoice\Repository;


trait NastaveniTrait
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