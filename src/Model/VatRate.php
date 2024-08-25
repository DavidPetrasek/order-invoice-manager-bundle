<?php
namespace Psys\OrderInvoiceManagerBundle\Model;


enum VatRate :int
{
    case STANDARD = 1;
    case REDUCED = 2;
    case SECOND_REDUCED = 3;
    case ZERO = 4;
        
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