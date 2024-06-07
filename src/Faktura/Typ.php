<?php
namespace Psys\SimpleOrderInvoice\Faktura;


enum Typ :int
{
    case ZALOHOVA = 1;
    case PROFORMA = 2;
    case KONCOVA = 3;
        
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