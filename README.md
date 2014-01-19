Hipster enums for PHP 5.4+
==========================

Installation
============
Add package to `composer.json`.

```js
{
    "require": {
        "timetoogo/hipster-enums": "1.0.1"
    }
}
```

Summary
=======

- An enum is a group of possible values: [`DayOfWeek`](#day-of-week)
- An an instance of an enum represents an underlying value: `DayOfWeek::Thursday()->GetValue()`
- Two enum instances of the same type, representing the same value are [equal](#comparison-and-equality): `DayOfWeek::Thursday() === DayOfWeek::Thursday()`
- Enums are type safe: `function SetDay(DayOfWeek $DayOfWeek)`
- Enums are extensible: `DayOfWeek::Thursday()->GetTomorrow()`
- Enum instances are [serializable](#serialization): `DayOfWeek::Serialize(DayOfWeek::Thursday())`
- Enum values can be represented as a [string](#conversion-to-string): `(string)DayOfWeek::Thursday()`

An introduction
===============

```php
final class Boolean extends \Enum\Simple {

    public static function True() {
        return self::Representing('True');
    }
    
    public static function False() {
        return self::Representing('False');
    }
}
```

Congrats, we have successfully reimplemented the boolean using Hipster enums.

Lets test it out:

```php
var_export(Boolean::True() === Boolean::True()); //true
var_export(Boolean::True() === Boolean::False()); //false
var_export(Boolean::False() === Boolean::False()); //true
```

Great it works, 
Lets do something crazy and reimplement `!` operator.

```php
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
```

And test it out:

```php
Boolean::True()->Not() === Boolean::True(); //false
Boolean::True() === Boolean::False()->Not(); //true
Boolean::False()->Not()->Not() === Boolean::False(); //true
```

Cool, but pointless. 

Lets do something useful and create the days of the week (as in summary) :

```php
final class DayOfWeek extends Enum\Simple {
    
    public static function Monday() { return self::Representing('Monday'); }
    
    public static function Tuesday() { return self::Representing('Tuesday'); }
    
    public static function Wednesday() { return self::Representing('Wednesday'); }
    
    public static function Thursday() { return self::Representing('Thursday'); }
    
    public static function Friday() { return self::Representing('Friday'); }
    
    public static function Saturday() { return self::Representing('Saturday'); }
    
    public static function Sunday() {  return self::Representing('Sunday'); }
    
    public function GetTomorrow() {
        if($this === self::Sunday()) {
            return self::Monday();
        }
        else {
            $All = self::All();
            return $All[array_search($this, $All) + 1];
        }
    }
}
```

Notice something? The enum values are simply represented by their method name.

Well, lets keep *DRY* and see how we go:

```php
final class DayOfWeek extends Enum\Simple {
    
    public static function Monday() { return self::Representing(__FUNCTION__); }
    
    public static function Tuesday() { return self::Representing(__FUNCTION__); }
    
    public static function Wednesday() { return self::Representing(__FUNCTION__); }
    
    public static function Thursday() { return self::Representing(__FUNCTION__); }
    
    public static function Friday() { return self::Representing(__FUNCTION__); }
    
    public static function Saturday() { return self::Representing(__FUNCTION__); }
    
    public static function Sunday() {  return self::Representing(__FUNCTION__); }
    
    public function GetTomorrow() {
        if($this === self::Sunday()) {
            return self::Monday();
        }
        else {
            $All = self::All();
            return $All[array_search($this, $All) + 1];
        }
    }
}
```

Well that didn't do much, and it is still extremely ugly and verbose! 

Relax, There is a solution: 

<a name="day-of-week">
```php
final class DayOfWeek extends \Enum\Simple {
    use \Enum\Values {
        _ as Monday;
        _ as Tuesday;
        _ as Wednesday;
        _ as Thursday;
        _ as Friday;
        _ as Saturday;
        _ as Sunday;
    }
    
    public function GetTomorrow() {
        if($this === self::Sunday()) {
            return self::Monday();
        }
        else {
            $All = self::All();
            return $All[array_search($this, $All) + 1];
        }
    }
}
```

That's better! 

If your enums values are represented by the method names, you can utilise the `\Enum\Values` trait. This trait contains a single static method `_`. You can alias this method to the required enum values, and the aliased methods will return the enum representing their method name as a string.

Conversion To String
====================

Override the `protected string ToString(mixed $Value)` to customize the conversion to string:

```php
final class DayOfWeek extends \Enum\Simple {
    use \Enum\Values {
        _ as Monday;
        _ as Tuesday;
        _ as Wednesday;
        _ as Thursday;
        _ as Friday;
        _ as Saturday;
        _ as Sunday;
    }
    
    protected function ToString($Value) {
        return 'Today could be ' . strtolower($Value) . '.';
    }
}

echo DayOfWeek::Saturday(); //Today could be saturday.
```

Serialization
=============
Enums values can also be fully serialized/unserialized using the `Enum\Base::Serialize(Enum\Base $Enum)` and `Enum\Base::Unserialize(string $SerializedEnum)` repectively. This will work for any defined enum. If you want to un/serialize and verify an enum to be of a specific type, you can can call either method in the context of the enum to verify:

```php
$Monday = DayOfWeek::Monday();

$SerializedMonday = Enum\Base::Serialize($Monday);//Ok
$SerializedMonday = DayOfWeek::Serialize($Monday);//Ok
$SerializedMonday = MonthOfYear::Serialize($Monday);//ERROR!

$Monday = Enum\Base::Unserialize($SerializedMonday); //Ok
$Monday = DayOfWeek::Unserialize($SerializedMonday); //Ok
$Monday = MonthOfYear::Unserialize($SerializedMonday); //ERROR!
```

Comparison and Equality
=======================
An two enum instances of the same type, representing the same value must be equal. Comparison results using `===` operator can be seen below.

```php
$Monday = DayOfWeek::Monday();
$Tuesday = DayOfWeek::Tuesday();

$Monday === DayOfWeek::Monday(); //true
$Monday === DayOfWeek::FromValue(DayOfWeek::Monday()->GetValue()); //true
$Monday === DayOfWeek::Unserialize(DayOfWeek::Serialize(DayOfWeek::Monday())); //true
$Monday === $Tuesday; //false
```

As you can see, within a enum there is only ever one instance representing any given value.

Clean comprehension API
=======================
 - `Enum\Base::Map(callable $MappingCallback)` Maps the enum instances with the supplied callback
 - `Enum\Base::MapValues(callable $MappingCallback)` Maps the represented values with the supplied callback
 - `Enum\Base::Filter(callable $MappingCallback)` Filters the enum instances with the supplied callback
 - `Enum\Base::FilterByValue(callable $MappingCallback)` Filters the enums by their represented value with the supplied callback
 - `Enum\Base::FirstOrDefault(callable $MappingCallback)` Returns the first enum according to the filter callback or the default value if none is matched
 - `FirstOrDefaultByValue(callable $MappingCallback)` 

Usage
=====
```php
final class DayOfWeek extends \Enum\Simple {
    use \Enum\Values {
        _ as Monday;
        _ as Tuesday;
        _ as Wednesday;
        _ as Thursday;
        _ as Friday;
        _ as Saturday;
        _ as Sunday;
    }
    
    public static function IsWeekEnd(self $DayOfWeek) {
        return $DayOfWeek === self::Saturday() || $DayOfWeek === self::Sunday();
    }
    
    public static function GetWeekDays() {
        return self::Filter(function (self $DayOfWeek) {
            return !self::IsWeekEnd($DayOfWeek);
        });
    }
    
    public static function GetWeekEndDays() {
        return self::Filter([__CLASS__, 'IsWeekEnd']);
    }
}
```

A more sophisticated example
============================

If your requirements are more complex, hipster enums can represent any type of value (but `Closure` cannot be serialized):

```php
class Country extends Enum\Simple {
    
    public static function USA() {
        return self::Representing(
                [
                    'Name' => 'United States of America',
                    'Population' => 317500000,
                    'Area' => 9826675
                ]);
    }
    
    public static function Australia() {
        return self::Representing(
                [
                    'Name' => 'Australia',
                    'Population' => 23351119,
                    'Area' => 7692024
                ]);
    }
    
    public static function SouthAfrica() {
        return self::Representing(
                [
                    'Name' => 'South Africa',
                    'Population' => 52981991,
                    'Area' => 1221037
                ]);
    }
    
    public static function FromName($Name) {
        return self::FirstOrDefaultByValue(
                function ($Value) use ($Name) {
                    return $Value['Name'] === $Name;
                });
    }
    
    protected function ToString($Value) {
        return $Value['Name'];
    }
    
    public function PopulationDensity() {
        $Country = $this->GetValue();
        
        return $Country['Population'] / $Country['Area'];
    }
}
```

We have defined an enum representing some countries and have provided methods to determine information on that data.

Usage
=====

```php
$SouthAfrica = Country::SouthAfrica();
echo sprintf('%s has a population density of: %s/Km²',
        $SouthAfrica,
        $SouthAfrica->PopulationDensity());
```
