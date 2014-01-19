<?php

require_once '../vendor/autoload.php';

final class DayOfWeek extends Enum\Strict {
    use Enum\Values {
        _ as Monday;
        _ as Tuesday;
        _ as Wednesday;
        _ as Thursday;
        _ as Friday;
        _ as Saturday;
        _ as Sunday;
    }
}

?>