<?php
namespace Psys\OrderInvoiceManagerBundle\Model\Order;


enum PaymentMode :int
{
    case BANK_ACCOUNT_REGULAR = 1;
    case BANK_ACCOUNT_ONLINE = 2;
    case CREDIT_CARD = 3;
        
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