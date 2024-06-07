<?php
namespace Psys\SimpleOrderInvoice\Objednavka;


enum FormaUhrady :int
{
    case BANKOVNI_PREVOD = 1;
    case BANKOVNI_PREVOD_ONLINE = 2;
    case PLATEBNI_KARTA = 3;
        
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