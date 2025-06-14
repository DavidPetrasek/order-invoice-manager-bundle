<?php
namespace Psys\OrderInvoiceManagerBundle\Model\Order;


enum State :int
{
    case NEW = 1;
    case PAID = 2;
    case PREPARE_FOR_SHIPPING = 3;
    case SHIPPING = 4;
    case DELIVERED = 5;
    case LOST = 6;
        
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