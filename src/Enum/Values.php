<?php

namespace Enum;

trait Values {
    
    /**
     * Provides a template method for enum values representing the method name.
     * This can be aliased in an enum class.
     * 
     * @return static|null
     */
    final public static function _() {
        $MethodName = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 1)[0]['function'];
        if($MethodName === __FUNCTION__) {
            return null;
        }
        return self::Representing($MethodName);
    }
}

?>
