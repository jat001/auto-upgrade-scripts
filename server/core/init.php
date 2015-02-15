<?php
if (!defined('IN_SINOSKY')) exit();

require_once(build_file_path('config.php'));

if (DEBUG)
    error_reporting(E_ALL);
else
    error_reporting(E_ALL & ~E_NOTICE & ~E_STRICT & ~E_DEPRECATED);

spl_autoload_register(function ($class) {
    require_once(build_file_path('lib', $class . '.php'));
});

require_once(build_file_path('core', 'output.php'));

function build_file_path() {
    $args = func_get_args();
    array_unshift($args, ROOT_PATH);

    return implode(DIRECTORY_SEPARATOR, $args);
}
