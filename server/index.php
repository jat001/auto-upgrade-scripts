<?php
define('IN_SINOSKY', true);
define('API_Version', '0.01-beta');
define('ROOT_PATH', __DIR__);
define('DATA_PATH', implode(DIRECTORY_SEPARATOR, [
    ROOT_PATH,
    'data'
]));

require_once(implode(DIRECTORY_SEPARATOR, [
    ROOT_PATH,
    'core',
    'init.php'
]));
