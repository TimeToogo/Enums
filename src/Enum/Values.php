<?php

namespace Enum;

trait Values {
    /**
     * @return Enum|null
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