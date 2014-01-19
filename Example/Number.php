<?php

require_once '../vendor/autoload.php';

final class Number extends Enum\Dynamic {
    public static function One() {
        return self::Representing(1);
    }
}

$Two = Number::Representing(2);

?>