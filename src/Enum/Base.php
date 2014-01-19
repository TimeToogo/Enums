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
    
    private static $HashCache = array();
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
     * the same data will be considered equal.
     * 
     * @param mixed $Value The value represented by the enum.
     * @return static The enum instance
     */
    protected static function Representing($Value) {
        static::VerifyValue($Value);
        
        $EnumClassName = get_called_class();        
        
        if($EnumClassName === __CLASS__ || (new \ReflectionClass($EnumClassName))->isAbstract()) {
            throw new \BadMethodCallException("Cannot create enum instance of $EnumClassName: enum class cannot be abstract");
        }
        
        if(!isset(self::$Instances[$EnumClassName])) {
            self::$Instances[$EnumClassName] = array();
            self::$HashCache[$EnumClassName] = array();
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
     * Gets the value associated with the current enum.
     * 
     * @return mixed The value associated with the enum
     */
    final protected static function Instances() {
        return self::$Instances[get_called_class()];
    }

    final public function __clone() {
        throw new \Exception('Enum cannot be cloned');
    }
    
    public function Serialize() {
        return self::SerializeEnum($this);
    }
    
    /**
     * DO NOT CALL:
     * Unserialize Enum with {Enum}::UnserializeEnum(\$EnumValue)
     */
    public function unserialize($SerializedEnum) {
        $CalledClassName = get_called_class();
        throw new \Exception("Unserialize Enum with $CalledClassName::UnserializeEnum(\$EnumValue)");
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