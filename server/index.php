<?php
define('IN_SINOSKY', true);
define('API_Version', '0.0.3-stable');
define('ROOT_PATH', __DIR__);
define('DATA_PATH', build_file_path([
    'data'
]));

require_once(build_file_path([
    'core',
    'init.php'
]));

function build_file_path($path, $root = true) {
    if ($root) array_unshift($path, ROOT_PATH);

    return implode(DIRECTORY_SEPARATOR, $path);
}
