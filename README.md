PHP-Enum
========

Hipster enums for PHP 5.4+

Hipster Summary
===============

- An enum is a group of possible values: `DayOfWeek`
- An an value of an enum represents an underlying value: `DayOfWeek::Thursday()->GetValue()`
- Two enums of the same type, representing the same value are [equal](#comparisons-and-equality): `DayOfWeek::Thursday() === DayOfWeek::Thursday()`
- Enums are type safe: `function SetDay(DayOfWeek $DayOfWeek)`
- Enums are extendable: `DayOfWeek::Thursday()->GetTomorrow()`
- Enums are [serializabe](#serialization): `DayOfWeek::Serialize(DayOfWeek::Thursday())`
- Enums can be represented as a [string](#to-string): `(string)DayOfWeek::Thursday()`

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

Great so it works, Lets do something crazy and reimplement `!` operator.

```php
//Add this method to Boolean
public function Not() {
    return $this === self::True() ? self::False() : self::True();
}
```

And test it out:
```php
Boolean::True()->Not() === Boolean::True();// false
```


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
            return $All[array_search($this, $All)++];
        }
    }
}
```

The above example is an enum representing the days of the week. 
We can see that this enum has made use of the trait `\Enum\Values`.
This trait contains a single static method `_`, this method can be aliased

