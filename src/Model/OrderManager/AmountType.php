<?php
namespace Psys\OrderInvoiceManagerBundle\Model\OrderManager;


enum AmountType :int
{
    case ITEM = 1;
    case HOUR = 2;
    case KILOGRAM = 3;
        
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