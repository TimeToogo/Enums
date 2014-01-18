<?php

namespace Enum;

trait Values {
    final public static function _() {
        $FunctionName = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 1)[0]['function'];
        if($FunctionName === __FUNCTION__) {
            throw new \BadMethodCallException('Invalid trait value');
        }
        return self::Value($FunctionName);
    }
}

?>