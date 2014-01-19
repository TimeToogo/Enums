<?php

require_once 'DayOfWeek.php';

echo '<pre>';
$Monday = DayOfWeek::Monday();
$Tuesday = DayOfWeek::Tuesday();

echo 'Monday dump: ';
var_dump($Monday);

echo 'Monday exported: ';
var_export($Monday);
echo '<br />';
echo '<br />';

echo 'Monday serialized: ';
echo DayOfWeek::Serialize(DayOfWeek::Monday());
echo '<br />';
echo '<br />';

echo '$Monday === DayOfWeek::Monday(): ' . var_export($Monday === DayOfWeek::Monday(), true) . '<br />';

echo '$Monday === DayOfWeek::FromValue(DayOfWeek::Monday()->GetValue()): ' . var_export($Monday === DayOfWeek::FromValue(DayOfWeek::Monday()->GetValue()), true) . '<br />';

echo '$Monday === DayOfWeek::Unserialize(DayOfWeek::Serialize(DayOfWeek::Monday())): ' . var_export($Monday === DayOfWeek::Unserialize(DayOfWeek::Serialize(DayOfWeek::Monday())), true) . '<br />';

echo '$Monday === $Tuesday: ' . var_export($Monday === $Tuesday, true) . '<br />';


?>