<?php

require_once '../vendor/autoload.php';

final class Numbers extends \Enum\Simple {
    const Base = 1;
    
    use \Enum\OrdinalValues {
        _ as One;
        _ as Two;
        _ as Three;
        _ as Four;
        _ as Five;
        _ as Five;
    }
}

var_dump(Numbers::One() === Numbers::Two());
?>