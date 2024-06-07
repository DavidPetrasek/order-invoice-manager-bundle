<?php
namespace Psys\SimpleOrderInvoice\Objednavka;


enum ZboziJednotka :int
{
    case KUS = 1;
    case HODINA = 2;
        
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