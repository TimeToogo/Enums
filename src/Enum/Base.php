<?php

namespace Enum;

/**
 * The base class for enums.
 * All enums must inherit from this class.
 * 
 * @author Elliot Levin <elliot@aanet.com.au>
 */
abstract class Base implements \Serializable {
    private $Value;
    private $SerializedValue;
    
    private static $Instances = array();

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
        static::VerifyValue($Value);
        
        $EnumClassName = static::VerifyValidCalledClass();
        
        if(!isset(self::$Instances[$EnumClassName])) {
            self::$Instances[$EnumClassName] = array();
        }
        
        $SerializedValue = serialize($Value);
        if(!isset(self::$Instances[$EnumClassName][$SerializedValue])) {
            $EnumInstance = new static($Value, $SerializedValue);
            self::$Instances[$EnumClassName][$SerializedValue] = $EnumInstance;
            
            return $EnumInstance;
        }
        
        return self::$Instances[$EnumClassName][$SerializedValue];
    }
    
    protected static function VerifyValue($Value) { }
        
    /**
     * @return Base[]
     */
    final public static function All() {
        return self::$Instances[static::VerifyValidCalledClass()];
    }
    
    /**
     * 
     * @param callable $FilterCallback
     * @return Base[] The matching enums
     */
    final protected static function Filter(callable $FilterCallback) {
        return array_filter(static::All(), 
                function (self $EnumInstance) use(&$FilterCallback) {
                    return $FilterCallback($EnumInstance->Value);
                });
    }
    
    /**
     * Returns the first 
     * 
     * @param callable $FilterCallback
     * @return Base[] The matching enums
     */
    final protected static function FirstOrDefault(callable $FilterCallback, $Default = null) {
        foreach(static::All() as $EnumInstance) {
            if($FilterCallback($EnumInstance->Value)) {
                return $EnumInstance;
            }
        }
        
        return $Default;
    }
    
    final public function __clone() {
        throw new \Exception('Enum cannot be cloned');
    }
    
    public function Serialize() {
        return self::SerializeEnum($this);
    }
    
    private static function VerifyValidCalledClass() {
        $CalledClass = get_called_class();
        if($CalledClass === __CLASS__ || (new \ReflectionClass($CalledClass))->isAbstract()) {
            throw new \BadMethodCallException("Static calls to $CalledClass are disallowed.");
        }
        
        return $CalledClass;
    }
    
    /**
     * DO NOT CALL:
     * Unserialize Enum with {Enum}::UnserializeEnum(\$EnumValue)
     */
    public function unserialize($SerializedEnum) {
        $CalledClassName = get_called_class();
        throw new \Exception("Unserialize Enum with $CalledClassName::UnserializeEnum(\$SerializedEnum)");
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

    private static function IsValidEnumValueClass(self $EnumInstance) {
        $CalledClassName = get_called_class();
        $IsGlobalEnumValue = $CalledClassName === __CLASS__;
        if($IsGlobalEnumValue) {
            return true;
        }
        
        return $EnumInstance instanceof $CalledClassName;
    }

    private static function VerifyEnumValue(self $EnumInstance, $MessagePrefix) {
        if(!static::IsValidEnumValueClass($EnumInstance)) {
            $EnumClassName = get_class($EnumInstance);
            $CalledClassName = get_called_class();
            throw new \InvalidArgumentException($MessagePrefix . ": '$EnumClassName' is not a valid '$CalledClassName'");
        }
    }

    final public static function SerializeEnum(self $EnumValue) {
        static::VerifyEnumValue($EnumValue, 'Cannot serialize supplied enum');
        
        return  get_class($EnumValue) . '::{' . $EnumValue->SerializedValue . '}';
    }
    
    /**
     * @param string The serialized enum
     * @return Enum
     */
    final public static function UnserializeEnum($SerializedEnum) {
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
        if(!is_subclass_of($EnumClassName, $CalledClassName)) {
            throw new \InvalidArgumentException("Cannot unserialize enum: '$EnumClassName' is not a valid " . $CalledClassName);
        }
        
        $Value = unserialize(substr($EnumParts[1], 1, -1));
        $EnumInstance = $EnumClassName::Representing($Value);
        
        return $EnumInstance;
    }
}

?>