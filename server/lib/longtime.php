<?php
if (!defined('IN_SINOSKY')) exit(1);

class longtime {
    public static $funcs = [];

    public static function call_longtime_func() {
        if (!self::$funcs) return true;

        foreach (self::$funcs as $func) {
            call_user_func_array(
                (isset($func['func'])) ? $func['func'] : [
                    $func['obj'],
                    $func['method']
                ],
                $func['args']
            );
        }
    }
}
