<?php

namespace Enum;
    
abstract class Base {
    private $Name;
    private $GlobalName;
    private static $EnumMethods = null;
    private static $IsInitialized = array();
    private static $StaticMethods = array();
    private static $Values = array();

    private function __construct($Name) {
        $this->Name = $Name;
        $this->GlobalName = $this->GetGlobalName($Name);
    }

    private static function GetGlobalName($Name) {
        return get_called_class() . '::' . $Name;
    }

    /**
     * @return Enum
     */
    final protected static function Value($Name = null) {
        if($Name === null) {
            $Name = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2)[1]['function'];
        }
        if(!is_string($Name)) {
            throw new \InvalidArgumentException('Cannot create enum from ' . gettype($Name) . ': string expected');
        }
        $EnumClassName = get_called_class();
        self::Initialize($EnumClassName);

        return self::$Values[$EnumClassName][$Name];
    }

    private static function Initialize($EnumClassName) {
        if(isset(self::$IsInitialized[$EnumClassName])) {
            return;
        }
        if(self::$EnumMethods === null) {
            self::$EnumMethods = get_class_methods(__CLASS__);
        }

        $Reflection = new ReflectionClass($EnumClassName);
        $Methods = array_diff(get_class_methods($EnumClassName), self::$EnumMethods);
        $StaticMethods = array();
        $EnumValues = array();
        foreach($Methods as $MethodName) {
            $MethodReflection = $Reflection->getMethod($MethodName);
            if($MethodReflection->getDeclaringClass()->name === $EnumClassName && $MethodReflection->isPublic() && $MethodReflection->isStatic()) {
                $StaticMethods[$MethodName] = $MethodReflection;

                $EnumValues[$MethodName] = new static($MethodName);
            }
        }
        self::$Values[$EnumClassName] = $EnumValues;
        self::$StaticMethods[$EnumClassName] = $StaticMethods;
        self::$IsInitialized[$EnumClassName] = true;
    }

    final public function __clone() {
        throw new Exception('Enum cannot be cloned');
    }

    final public function __sleep() {
        $CalledClassName = get_called_class();
        throw new Exception("Serialize Enum via $CalledClassName::Serialize(Enum \$Enum)");
    }

    final public function __wakeup() {
        $CalledClassName = get_called_class();
        throw new Exception("Unserialize Enum using $CalledClassName::Parse(\$Name)");
    }

    final public static function __set_state($Data) {
        return static::Parse(isset($Data['GlobalName']) ? $Data['GlobalName'] : '');
    }

    final public function __toString() {
        return $this->Name;
    }

    final public static function Iterate() {
        $EnumClassName = get_called_class();
        self::Initialize($EnumClassName);
        return new ArrayIterator(self::$Values[$EnumClassName]);
    }

    private static function IsValidEnumValueClass(Enum $EnumValue) {
        $CalledClassName = get_called_class();
        $IsGlobalEnumValue = $CalledClassName === __CLASS__;
        if($IsGlobalEnumValue) {
            return true;
        }
        $EnumClassName = get_class($EnumValue);
        return $EnumClassName === $CalledClassName;
    }

    private static function VerifyEnumValue(Enum $EnumValue, $MessagePrefix) {
        if(!static::IsValidEnumValueClass($EnumValue)) {
            $EnumClassName = get_class($EnumValue);
            $CalledClassName = get_called_class();
            throw new \InvalidArgumentException($MessagePrefix . ": '$EnumClassName' is not a valid '$CalledClassName'");
        }
    }

    private static function ParseEnumValueName($EnumClassName, $ValueName) {
        self::Initialize($EnumClassName);
        if(!isset(self::$StaticMethods[$EnumClassName][$ValueName])) {
            return null;
        }
        $ReturnedValue = $EnumClassName::{$ValueName}();
        if(!is_object($ReturnedValue) || get_class($ReturnedValue) !== $EnumClassName) {
            return null;
        }

        return $ReturnedValue;
    }

    final public static function Serialize(Enum $EnumValue) {
        static::VerifyEnumValue($EnumValue, 'Cannot serialize supplied Enum');
        return $EnumValue->GlobalName;
    }

    /**
     * @return Enum
     */
    final public static function Parse($Name) {
        $CalledClassName = get_called_class();
        $IsGlobalEnumParse = $CalledClassName === __CLASS__;

        if(!is_string($Name)) {
            throw new \InvalidArgumentException("Cannot parse \$Name as enum $CalledClassName: \$Name must be a valid string");
        }

        $EnumParts = explode('::', $Name);

        if(!$IsGlobalEnumParse && count($EnumParts) === 1) {
            $EnumClassName = $CalledClassName;
            $ValueName = $EnumParts[0];
        }
        else {
            if(count($EnumParts) !== 2) {
                throw new \InvalidArgumentException("Cannot parse '$Name': Enum string format is invalid");
            }

            $EnumClassName = $EnumParts[0];

            if(!class_exists($EnumClassName)) {
                throw new \InvalidArgumentException("Cannot parse '$Name': '$EnumClassName' is not a valid class");
            }
            if(!is_subclass_of($EnumClassName, __CLASS__)) {
                throw new \InvalidArgumentException("Cannot parse '$Name': '$EnumClassName' is not a valid subclass of " . __CLASS__);
            }
            if(!$IsGlobalEnumParse && $EnumClassName !== $CalledClassName) {
                throw new \InvalidArgumentException("Cannot parse '$Name': '$EnumClassName' is not a valid '$CalledClassName'");
            }

            $ValueName = $EnumParts[1];
        }
        $EnumValue = self::ParseEnumValueName($EnumClassName, $ValueName);

        if(!$EnumValue) {
            throw new \InvalidArgumentException("Cannot parse '$Name' as enum $EnumClassName: $EnumClassName does not contain value '$ValueName'");
        }

        return $EnumValue;
    }
}

?>