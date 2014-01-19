<?php

require_once '../vendor/autoload.php';

//I cannot believe this is valid php
final class Boolean extends \Enum\Simple {

    public static function True() {
        return self::Representing('True');
    }
    
    public static function False() {
        return self::Representing('False');
    }
    
    public function Not() {
        return $this === self::True() ? self::False() : self::True();
    }
}
/*
echo 'Boolean::True() === Boolean::True(): ' . var_export(Boolean::True() === Boolean::True(), true) . '<br />';
echo 'Boolean::True() === Boolean::False(): ' . var_export(Boolean::True() === Boolean::False(), true) . '<br />';
echo 'Boolean::False() === Boolean::False(): ' . var_export(Boolean::False() === Boolean::False(), true) . '<br />';
                

echo 'Boolean::True()->Not() === Boolean::True(): ' . var_export(Boolean::True()->Not() === Boolean::True(), true) . '<br />';
echo 'Boolean::True() === Boolean::False()->Not(): ' . var_export(Boolean::True() === Boolean::False()->Not(), true) . '<br />';
echo 'Boolean::False()->Not()->Not() === Boolean::False(): ' . var_export(Boolean::False()->Not()->Not() === Boolean::False(), true) . '<br />';
*/              
?>