<?php

if (!defined('IN_SINOSKY')) exit();

if (!$request) error_code(400);

$db = new db();

switch ($request[0]) {
    case 'php':
        $result = $db->get('php', 'http://php.net/downloads.php', 'regex', '/Current Stable.+?PHP (\d\.\d{1,2}\.\d{1,3})/is');

        if ($result && is_array($result)) {
            list($version, $time) = $result;

            echo $version;
        }

        break;

    case 'mysql':
        $result = $db->get('mysql', 'http://dev.mysql.com/downloads/mysql/', 'regex', '/MySQL Community Server (\d\.\d{1,2}\.\d{1,3})/i');

        if ($result && is_array($result)) {
            list($version, $time) = $result;

            echo $version;
        }

        break;

    case 'nginx':
        $result = $db->get('nginx', 'http://nginx.org/en/download.html', 'regex', '/nginx-(\d\.\d{1,2}\.\d{1,3})/i');

        if ($result && is_array($result)) {
            list($version, $time) = $result;

            echo $version;
        }

        break;

    case 'phpmyadmin':
        $result = $db->get('phpmyadmin', 'http://www.phpmyadmin.net/home_page/version.json', 'json', 'version');

        if ($result && is_array($result)) {
            list($version, $time) = $result;

            echo $version;
        }

        break;

    case 'hosts':
        $result = $db->get('hosts', 'http://freedom.txthinking.com/hosts');
        if ($result && is_array($result)) {
            list($hosts, $time) = $result;

            if (isset($request[1]) && $request[1] == 'get') {
                echo $hosts;

                if (isset($request[2]) && $request[2] == 'dl')
                    header('Content-Disposition: attachment; filename="hosts"');
            } else {
                    preg_match('/# UPDATE: (.+)/i', $hosts, $matches);

                    if (isset($matches[1]) && $matches[1]) echo strtotime($matches[1]);
            }
        }

        break;

    default:
        error_code(400);

        break;
}

?>
