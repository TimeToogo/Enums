<?php

namespace Enum;
    
/**
 * Enums deriving from the strict enum must have a fixed set of
 * represented values defined from the class methods.
 * 
 * @author Elliot Levin <elliot@aanet.com.au>
 */
abstract class Strict extends Base {
    private static $IsInitializing = array();
    private static $IsInitialized = array();
    private static $RepresentedData = array();
    
    /**
     * {@inheritDoc}
     * @return static
     */
    protected static function Representing($Value) {
        self::Initialize(get_called_class());
        return forward_static_call(['parent', __FUNCTION__], $Value);
    }
    
    protected static function VerifyValue($Value) {
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
     * @return Strict|null
     */
    public static function Parse($Value) {
        $EnumClassName = get_called_class();
        if($EnumClassName === __CLASS__) {
            throw new \BadMethodCallException("Cannot parse value as $EnumClassName");
        }
        if(array_search($Value, self::$RepresentedData[$EnumClassName]) === false) {
            return false;
        }
        else {
            return static::Representing($Value);
        }
    }
    
    /**
     * 
     * @param callable $FilterCallback
     * @return Strict[] The matching enums
     */
    final protected static function Filter(callable $FilterCallback) {
        return array_filter(static::Instances(), 
                function (self $EnumInstance) use(&$FilterCallback) {
                    return $FilterCallback($EnumInstance->GetValue());
                });
    }
    
    /**
     * 
     * @param callable $FilterCallback
     * @return Strict[] The matching enums
     */
    final protected static function FirstOrDefault(callable $FilterCallback, $Default = null) {
        $FilteredEnums = static::Filter($FilterCallback);
        
        return count($FilteredEnums) > 0 ? reset($FilteredEnums) : $Default;
    }

    /**
     * Initialize defined enum values by invoking all static methods.
     * 
     * @return void
     */
    private static function Initialize($EnumClassName) {
        if($EnumClassName === __CLASS__ ||
                isset(self::$IsInitialized[$EnumClassName]) || 
                isset(self::$IsInitializing[$EnumClassName])) {
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
        self::$IsInitialized[$EnumClassName] = true;
    }
}

?>