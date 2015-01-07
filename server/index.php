<?php

define('API_Version', '0.01-alpha');

define('IN_SINOSKY', true);
define('ROOT_PATH', __DIR__);

require_once(ROOT_PATH . DIRECTORY_SEPARATOR . 'config.php');

if (DEBUG)
    error_reporting(E_ALL);
else
    error_reporting(E_ALL & ~E_NOTICE & ~E_STRICT & ~E_DEPRECATED);

require_once(ROOT_PATH . DIRECTORY_SEPARATOR . 'http.php');
require_once(ROOT_PATH . DIRECTORY_SEPARATOR . 'db.php');

$request = explode('/', $_SERVER['REQUEST_URI']);
$request = array_filter($request);
if (!$request) error_code(404);

$file = array_shift($request);
$file = realpath(ROOT_PATH . DIRECTORY_SEPARATOR . $file . '.php');
if (dirname($file) != ROOT_PATH || !file_exists($file)) error_code(404);

ob_start();

require_once($file);

$length = ob_get_length();
$output = ob_get_contents();
ob_end_clean();

header('Content-Type: text/plain; charset=UTF-8');
header('Cache-Control: public, max-age=0, s-maxage=0');
header('Date: ' . date('D, d M Y H:i:s', time()) . ' GMT');

if ($time) {
    header('Last-Modified: ' . date('D, d M Y H:i:s', $time) . ' GMT');
    header('Expires: ' . date('D, d M Y H:i:s', $time + 86400) . ' GMT');
}

header('Content-Length: ' . ob_get_length());
header('ETag: "' . md5($output) . '"');
header('X-SinoSky-API-Version: ' . API_Version);

exit($output);

function error_code($status_code) {
    switch ($status_code) {
        case 400:
            $status_code = '400 Bad Request';

            break;

        case 403:
            $status_code = '403 Forbidden';

            break;

        case 404:
            $status_code = '404 Not Found';

            break;
    }

    header('HTTP/1.1 ' . $status_code);
    header('Status: ' . $status_code);

    exit($status_code);
}

?>
