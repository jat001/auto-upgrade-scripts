<?php

define('IN_SINOSKY', true);

header('X-SinoSky-API-Version: 20140918');
header('Content-Type: text/text; charset=UTF-8');

$now = time();

if ($_SERVER['HTTP_IF_MODIFIED_SINCE'] && strtotime($_SERVER['HTTP_IF_MODIFIED_SINCE']) + 3600 > $now)
    error_code(304);

$request = explode('/', $_SERVER['REQUEST_URI']);
$request = array_filter($request);

$file = str_replace('.', '', array_shift($request));
if (!$file) error_code(404);

$file = __DIR__ . DIRECTORY_SEPARATOR . $file . '.php';

if (!file_exists($file)) error_code(404);

$redis = new Redis();
$redis->connect('/tmp/redis.sock');

ob_start();

include_once($file);

//$redis->save();
$redis->close();

$output = ob_get_contents();
ob_end_clean();

$etag = '"' . md5($output) . '"';

if ($_SERVER['HTTP_IF_NONE_MATCH'] && $_SERVER['HTTP_IF_NONE_MATCH'] == $etag)
    error_header(304);

header('Cache-Control: public, max-age=3600');
header('Last-Modified: ' . gmdate('D, d M Y H:i:s', $now) . ' GMT');
header('Expires: ' . gmdate('D, d M Y H:i:s', $now + 3600) . ' GMT');
header('ETag: ' . $etag);

exit($output);

function error_code($status_code) {
    switch ($status_code) {
        case 304:
            $status_code .= ' Not Modified';

            break;

        case 403:
            $status_code .= ' Forbidden';

            break;

        case 404:
            $status_code .= ' Not Found';

            break;
    }

    header('HTTP/1.1 ' . $status_code);
    header('Status: ' . $status_code);

    exit($status_code);
}

?>
