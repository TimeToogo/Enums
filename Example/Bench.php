<?php

require_once 'Country.php';

$Start = microtime(true);
$Rounds = 10000;

for($Count = 0; $Count < $Rounds; $Count++) {
    Country::USA();
}

$End = microtime(true);
echo 'Took ' . ($End - $Start) . ' to execute ' . $Rounds . ' rounds<br />';
 
?>