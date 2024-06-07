<?php
namespace Psys\SimpleOrderInvoice\Objednavka;


enum SazbaDPH :int
{
    case ZAKLADNI = 1;
    case SNIZENA = 2;
    case DRUHA_SNIZENA = 3;
        
//     static function toString ($value) : string
//     {
//         if ( !($value instanceof \UnitEnum) ) {$value = Stav::from($value);}
        
//         return match ($value)
//         {
//             self::KONCEPT => 'koncept',
//             self::AKTIVNI => 'aktivní',
//             self::POZASTAVENO => 'pozastaveno',
//             self::DOKONCENO => 'dokončeno',
//         };
//     }
}
?>