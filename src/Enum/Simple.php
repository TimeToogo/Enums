<?php

namespace Enum;
    
/**
 * Enums deriving from the strict enum must have a fixed set of
 * represented values defined from the class methods.
 * 
 * @author Elliot Levin <elliot@aanet.com.au>
 */
abstract class Simple extends Base {
    private static $IsInitializing = array();
    private static $RepresentedData = array();
        
    final protected static function VerifyValue($Value) {
        $EnumClassName = get_called_class();
        if(isset(self::$IsInitializing[$EnumClassName])) {
            self::$RepresentedData[$EnumClassName][] = $Value;
        }
        else if(array_search($Value, self::$RepresentedData[$EnumClassName]) === false) {
            throw new \InvalidArgumentException("Supplied value is not valid for $EnumClassName");
        }
    }
    
    /**
     * @param mixed $Value The value represented by the enum
     * @return Simple|null
     */
    final public static function Parse($Value) {
        $EnumClassName = get_called_class();
        if($EnumClassName === __CLASS__) {
            throw new \BadMethodCallException("Cannot parse value as $EnumClassName");
        }
        if(array_search($Value, self::$RepresentedData[$EnumClassName]) === false) {
            return null;
        }
        else {
            return static::Representing($Value);
        }
    }

    /**
     * Initialize defined enum instance by invoking all appropriate static methods.
     * 
     * @return void
     */
    final protected static function Initialize($EnumClassName) {
        if(isset(self::$IsInitializing[$EnumClassName])) {
            return;
        }
        
        self::$RepresentedData[$EnumClassName] = array();
        self::$IsInitializing[$EnumClassName] = true;
        
        $Reflection = new \ReflectionClass($EnumClassName);
        $Methods = array_diff(get_class_methods($EnumClassName), get_class_methods(__CLASS__));
        
        foreach($Methods as $MethodName) {
            $MethodReflection = $Reflection->getMethod($MethodName);
            if($MethodReflection->getDeclaringClass()->name === $EnumClassName && 
                    $MethodReflection->isPublic() && 
                    $MethodReflection->isStatic() &&
                    count($MethodReflection->getParameters()) === 0) {
                
                $MethodReflection->invoke(null);
            }
        }
        
        unset(self::$IsInitializing[$EnumClassName]);
    }
}

?>