<?php
if (!defined('IN_SINOSKY')) exit();

$request = explode('/', $_SERVER['REQUEST_URI']);
$request = array_filter($request);
if (!$request) error_code(404);

$class = array_shift($request);
$file = realpath(build_file_path('app', $class . '.php'));
if (dirname($file) != build_file_path('app') || !file_exists($file))
    error_code(403);

require_once($file);

ob_start();

$class = new $class($request);
$class->run();

$length = ob_get_length();
$output = ob_get_contents();
ob_end_clean();

header('Content-Type: text/plain; charset=UTF-8');
header('Cache-Control: public, max-age=0, s-maxage=0');
header('Date: ' . date('D, d M Y H:i:s', time()) . ' GMT');

if (isset($class->time) && $class->time) {
    header('Last-Modified: ' . date('D, d M Y H:i:s', $class->time) . ' GMT');
    header('Expires: ' . date('D, d M Y H:i:s', $class->time + 86400) . ' GMT');
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
