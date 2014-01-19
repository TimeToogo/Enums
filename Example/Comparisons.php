<?php

require_once 'DayOfWeek.php';

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
echo DayOfWeek::Monday()->Serialize();
echo '<br />';
echo '<br />';

echo '$Monday === DayOfWeek::Monday(): ' . var_export($Monday === DayOfWeek::Monday(), true) . '<br />';
echo '$Monday == DayOfWeek::Monday(): ' . var_export($Monday == DayOfWeek::Monday(), true) . '<br />';

echo '$Monday === DayOfWeek::Parse(DayOfWeek::Monday()->GetValue()): ' . var_export($Monday === DayOfWeek::Parse(DayOfWeek::Monday()->GetValue()), true) . '<br />';

echo '$Monday === Enum\Base::Parse(Enum\Base::SerializeEnum(DayOfWeek::Monday())): ' . var_export($Monday === Enum\Base::UnserializeEnum(Enum\Base::SerializeEnum(DayOfWeek::Monday())), true) . '<br />';

echo '$Monday === $Tuesday: ' . var_export($Monday === $Tuesday, true) . '<br />';
echo '$Monday == $Tuesday: ' . var_export($Monday == $Tuesday, true) . '<br />';


?>