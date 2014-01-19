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

    final protected static function VerifyValue($Value) {
        if(!isset(self::$IsInitializing[get_called_class()]) && !static::HasValue($Value)) {
            $EnumClassName = get_called_class();
            throw new \InvalidArgumentException(sprintf(
                    "Supplied value is not valid for $EnumClassName, Allowed values: %s. %s given",
                    
                    implode(', ', static::MapValues(function ($Value) { 
                        return var_export($Value, true); 
                    })),
                    var_export($Value, true)
                    ));
        }
        static::VerifyRepresentedValue($Value);
    }
    protected static function VerifyRepresentedValue($Value) {}

    /**
     * Initialize defined enum instance by invoking all appropriate static methods.
     * 
     * @return void
     */
    final protected static function Initialize($EnumClassName) {     
        if(isset(self::$IsInitializing[$EnumClassName])) {
            return;
        }
        
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