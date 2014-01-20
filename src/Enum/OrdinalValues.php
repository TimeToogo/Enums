<?php

namespace Enum;

trait OrdinalValues {
    private static $AliasedMethodOrdinalMap = array();
    
    /**
     * Provides a template method for enum values representing the index of the method.
     * This can be aliased in an enum class.
     * 
     * @return Enum|null
     */
    final public static function _() {
        $MethodName = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 1)[0]['function'];
        
        if($MethodName === __FUNCTION__) {
            return null;
        }
        
        $CalledEnumClass = get_called_class();
        
        if(!isset(self::$AliasedMethodOrdinalMap[$CalledEnumClass])) {
            
            self::$AliasedMethodOrdinalMap[$CalledEnumClass] = array();
            $BaseConstant = $CalledEnumClass . '::' . 'Base';
            
            $OrdinalBase = constant($BaseConstant) ?: 0;
            
            $Reflection = new \ReflectionClass($CalledEnumClass);
            $Aliases = $Reflection->getTraitAliases();
            
            $Count = $OrdinalBase;
            foreach($Aliases as $Alias => $OriginalName) {
                if($OriginalName === __METHOD__) {
                    self::$AliasedMethodOrdinalMap[$CalledEnumClass][$Count] = $Alias;
                    $Count++;
                } 
            }
            
            self::$AliasedMethodOrdinalMap[$CalledEnumClass] = array_flip(self::$AliasedMethodOrdinalMap[$CalledEnumClass]);
        }
        
        return self::Representing(self::$AliasedMethodOrdinalMap[$CalledEnumClass][$MethodName]);
    }
}

?>