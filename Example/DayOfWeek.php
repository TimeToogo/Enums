<?php

require 'Enum.php';
/**
 * DayOfWeek
 * @method Monday
 */
final class DayOfWeek extends Enum {
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

final class Mood extends Enum {
    use Enum\Values {
        _ as Shitty;
        _ as Funny;
        _ as Silly;
        _ as Happy;
        _ as Sad;
        _ as Angry;
    }
}

echo '<pre>';
$Monday = DayOfWeek::Monday();
$Tuesday = DayOfWeek::Tuesday();

echo 'Monday dump: ';
var_dump($Monday);

echo 'Monday export: ';
var_export($Monday);
echo '<br />';
echo '<br />';

echo 'Monday serialized: ';
echo DayOfWeek::Serialize(DayOfWeek::Monday());
echo '<br />';
echo '<br />';

echo '$Monday === DayOfWeek::Monday(): ' . var_export($Monday === DayOfWeek::Monday(), true) . '<br />';
echo '$Monday == DayOfWeek::Monday(): ' . var_export($Monday == DayOfWeek::Monday(), true) . '<br />';

echo '$Monday === (string)DayOfWeek::Monday(): ' . var_export($Monday === (string)DayOfWeek::Monday(), true) . '<br />';
echo '$Monday == (string)DayOfWeek::Monday(): ' . var_export($Monday == (string)DayOfWeek::Monday(), true) . '<br />';

echo '$Monday === DayOfWeek::Parse((string)DayOfWeek::Monday()): ' . var_export($Monday === DayOfWeek::Parse((string)DayOfWeek::Monday()), true) . '<br />';

echo '$Monday === DayOfWeek::Parse(DayOfWeek::Serialize(DayOfWeek::Monday())): ' . var_export($Monday === DayOfWeek::Parse(DayOfWeek::Serialize(DayOfWeek::Monday())), true) . '<br />';

echo '$Monday === Enum::Parse(Enum::Serialize(DayOfWeek::Monday())): ' . var_export($Monday === Enum::Parse(Enum::Serialize(DayOfWeek::Monday())), true) . '<br />';

echo '$Monday === Tuesday: ' . var_export($Monday === $Tuesday, true) . '<br />';
echo '$Monday == Tuesday: ' . var_export($Monday == $Tuesday, true) . '<br />';

function Recurse(callable $Function, $Limit) {
    if($Limit <= 0) {
        return $Function();
    }
    return Recurse($Function, $Limit - 1);
}
$Start = microtime(true);
$Rounds = 10000;

for($Count = 0; $Count < $Rounds; $Count++) {
    DayOfWeek::Tuesday();
}

$End = microtime(true);
echo 'Took ' . ($End - $Start) . ' to execute ' . $Rounds . ' rounds<br />';
?>