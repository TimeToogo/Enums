<?php

namespace Enum;

/**
 * The base class for enums.
 * All enums must inherit from this class.
 * 
 * @author Elliot Levin <elliot@aanet.com.au>
 */
abstract class Base {
    private $Value;
    private $SerializedValue;
    
    private static $IsInitialized = array();
    private static $Instances = array();
    private static $InstanceRepresentedValues = array();

    private function __construct($Value, $SerializedValue) {
        $this->Value = $Value;
        $this->SerializedValue = $SerializedValue;
    }
    
    /**
     * Gets the value represented by the current enum.
     * 
     * @return mixed The value represented by the enum
     */
    final public function GetValue() {
        return $this->Value;
    }

    /**
     * Gets the enum representing the supplied value.
     * NOTE: The supplied value is compared by value, two instances containing
     * the same data will be considered equal (By design).
     * 
     * @param mixed $Value The value represented by the enum.
     * @return static The enum instance
     */
    protected static function Representing($Value) {
        $EnumClassName = static::VerifyValidCalledClass(__FUNCTION__);
        
        static::InitializeIfNot($EnumClassName);
        static::VerifyValue($Value);        
        
        $SerializedValue = serialize($Value);
        if(!isset(self::$Instances[$EnumClassName][$SerializedValue])) {
            $EnumInstance = new static($Value, $SerializedValue);
            self::$Instances[$EnumClassName][$SerializedValue] = $EnumInstance;
            self::$InstanceRepresentedValues[$EnumClassName][$SerializedValue] = $Value;
            
            return $EnumInstance;
        }
        
        return self::$Instances[$EnumClassName][$SerializedValue];
    }
    private static function InitializeIfNot($EnumClassName) {
        if(!isset(self::$IsInitialized[$EnumClassName])) {
            
            self::$Instances[$EnumClassName] = array();
            self::$InstanceRepresentedValues[$EnumClassName] = array();
            self::$IsInitialized[$EnumClassName] = true;
            
            static::Initialize($EnumClassName);
        }
    }
    protected static function Initialize($EnumClassName) { }
    protected static function VerifyValue($Value) { }
    
    /**
     * @param mixed $RepresentedValue The value represented by the enum
     * @return boolean
     */
    final public static function HasValue($RepresentedValue) {
        $EnumClassName = static::VerifyValidCalledClass(__FUNCTION__);
        return array_search($RepresentedValue, self::$InstanceRepresentedValues[$EnumClassName]) !== false;
    }
    
    /**
     * @param mixed $RepresentedValue The value represented by the enum
     * @return Base|null
     */
    final public static function FromValue($RepresentedValue) {
        return static::HasValue($RepresentedValue) ?
                static::Representing($RepresentedValue) : null;
    }
    
    /**
     * Returns every enum instance for the called class.
     * 
     * @return Base[]
     */
    final public static function All() {
        $EnumClassName = static::VerifyValidCalledClass(__FUNCTION__);
        static::InitializeIfNot($EnumClassName);
        return array_values(self::$Instances[$EnumClassName]);
    }
        
    /**
     * Returns every represented value for the called class or the supplied array if not null.
     * 
     * @param array|null $EnumInstances
     * @return mixed[]
     */
    final public static function Values(array $EnumInstances = null) {
        $EnumClassName = static::VerifyValidCalledClass(__FUNCTION__);
        static::InitializeIfNot($EnumClassName);
        
        return array_values($EnumInstances === null ? 
                self::$InstanceRepresentedValues[$EnumClassName] :
                array_map(
                        function ($Enum) {
                            static::VerifyEnumValue($Enum, 'Cannot get value:', true);
                            return $Enum->Value;
                        },
                        $EnumInstances));
    }
    
    /**
     * Maps the enum instances with the supplied callback
     * 
     * @param callable $MappingCallback
     */
    final public static function Map(callable $MappingCallback) {
        return array_map(
                function (self $EnumInstance) use(&$MappingCallback) {
                    return $MappingCallback($EnumInstance);
                },
                static::All());
    }
    
    /**
     * Maps the enum values with the supplied callback
     * 
     * @param callable $MappingCallback
     */
    final public static function MapValues(callable $MappingCallback) {
        return array_map(
                function (self $EnumInstance) use(&$MappingCallback) {
                    return $MappingCallback($EnumInstance->Value);
                },
                static::All());
    }
    
    /**
     * Filters the enum instances with the supplied callback
     * 
     * @param callable $FilterCallback
     * @return Base[] The matching enums
     */
    final public static function Filter(callable $FilterCallback) {
        return array_filter(
                static::All(), 
                function (self $EnumInstance) use(&$FilterCallback) {
                    return $FilterCallback($EnumInstance);
                });
    }
    
    /**
     * Filters the enum values with the supplied callback
     * 
     * @param callable $FilterCallback
     * @return Base[] The matching enums
     */
    final public static function FilterByValue(callable $FilterCallback) {
        return array_filter(
                static::All(), 
                function (self $EnumInstance) use(&$FilterCallback) {
                    return $FilterCallback($EnumInstance->Value);
                });
    }
    
    /**
     * Returns the first enum according to the filter callback or the 
     * default value if none is matched
     * 
     * @param callable $FilterCallback
     * @return Base[] The matching enums
     */
    final public static function FirstOrDefault(callable $FilterCallback, $Default = null) {
        foreach($Enums as $EnumInstance) {
            if($FilterCallback($EnumInstance)) {
                return $EnumInstance;
            }
        }
        
        return $Default;
    }
    
    /**
     * Returns the first enum according to the filter callback or the 
     * default value if none is matched
     * 
     * @param callable $FilterCallback
     * @return Base[] The matching enums
     */
    final public static function FirstOrDefaultByValue(callable $FilterCallback, $Default = null) {
        foreach(static::All() as $EnumInstance) {
            if($FilterCallback($EnumInstance->Value)) {
                return $EnumInstance;
            }
        }
        
        return $Default;
    }
    
    private static function VerifyValidCalledClass($Method) {
        $CalledClass = get_called_class();
        if($CalledClass === __CLASS__ || (new \ReflectionClass($CalledClass))->isAbstract()) {
            throw new \BadMethodCallException("Static calls to $CalledClass::$Method must be peformed in a non-abstract context.");
        }
        
        return $CalledClass;
    }
    
    final public function __clone() {
        throw new \Exception('Enum cannot be cloned');
    }
    
    final public function __sleep() {
        $CalledClassName = get_called_class();
        throw new Exception("Serialize Enum via $CalledClassName::Serialize(Enum \$Enum)");
    }
    
    public function __wakeup() {
        $CalledClassName = get_called_class();
        throw new \Exception("Unserialize Enum with $CalledClassName::Unserialize(string \$SerializedEnum)");
    }
    
    public static function __set_state($Data) {
        return static::Representing($Data['Value']);
    }
    
    final public function __toString() {
        return $this->ToString($this->Value);
    }
    protected function ToString($Value) {
        return (string)$Value;
    }

    private static function IsValidEnumValueClass(self $EnumValue, $AllowSubclasses) {
        $CalledClassName = get_called_class();
        
        return $AllowSubclasses ?
                get_class($EnumValue) === $CalledClassName :
                $EnumValue instanceof $CalledClassName;
    }

    private static function VerifyEnumValue(self $EnumValue, $MessagePrefix, $AllowSubclasses) {
        if(!static::IsValidEnumValueClass($EnumValue, $AllowSubclasses)) {
            $EnumClassName = get_class($EnumValue);
            $CalledClassName = get_called_class();
            throw new \InvalidArgumentException($MessagePrefix . ": '$EnumClassName' is not a valid '$CalledClassName'");
        }
    }
    
    final static public function Serialize(self $EnumValue, $AllowSubclasses = false) {
        static::VerifyEnumValue($EnumValue, 'Cannot serialize supplied enum', $AllowSubclasses);
        
        return  get_class($EnumValue) . '::{' . $EnumValue->SerializedValue . '}';
    }
    
    /**
     * @param string $SerializedEnum The serialized enum
     * @param boolean $AllowSubclasses Whether or not to allow subclasses, 
     * this is ignored if the called class is abstract
     * @return Enum
     */
    final public static function Unserialize($SerializedEnum, $AllowSubclasses = false) {
        $CalledClassName = get_called_class();
        
        if(!is_string($SerializedEnum)) {
            throw new \InvalidArgumentException("Cannot unserialize enum as $CalledClassName: \$SerializedEnum must be a valid string");
        }
        
        $EnumParts = explode('::', $SerializedEnum, 2);

        if(count($EnumParts) !== 2 || 
                substr($EnumParts[1], 0, 1) !== '{' ||
                substr($EnumParts[1], -1) !== '}') {
            throw new \InvalidArgumentException("Cannot unserialize enum: Enum string format is invalid");
        }

        $EnumClassName = $EnumParts[0];

        if(!class_exists($EnumClassName)) {
            throw new \InvalidArgumentException("Cannot unserialize enum: '$EnumClassName' is not a valid class");
        }
        if($EnumClassName !== $CalledClassName) {
            $IsAbstract = (new \ReflectionClass($CalledClassName))->isAbstract();
            $AllowSubclasses = $AllowSubclasses ?: $IsAbstract;
            if(!$AllowSubclasses) {
                throw new \InvalidArgumentException("Cannot unserialize enum: $EnumClassName is not $CalledClassName and subclasses is disallowed");
            }
            else if(!is_subclass_of($EnumClassName, $CalledClassName)) {
                throw new \InvalidArgumentException("Cannot unserialize enum: $EnumClassName is not a valid subclass of $CalledClassName");
            }
        }
        
        $Value = unserialize(substr($EnumParts[1], 1, -1));
        $EnumInstance = $EnumClassName::Representing($Value);
        
        return $EnumInstance;
    }
}

?>