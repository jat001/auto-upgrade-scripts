<?php

if (!defined('IN_SINOSKY')) exit();

switch ($request[0]) {
    case 'php':
        if ($redis->exists('php')) {
            echo $redis->get('php');
        } else {
            $html = file_get_contents('http://php.net/downloads.php');
            preg_match('/Current Stable.+?PHP (\d\.\d{1,2}\.\d{1,3})/is', $html, $matches);

            $redis->setex('php', 3600, $matches[1]);

            echo $matches[1];
        }

        break;

    case 'mysql':
        if ($redis->exists('mysql')) {
            echo $redis->get('mysql');
        } else {
            $html = file_get_contents('http://dev.mysql.com/downloads/mysql/');
            preg_match('/MySQL Community Server (\d\.\d{1,2}\.\d{1,3})/i', $html, $matches);

            $redis->setex('mysql', 3600, $matches[1]);

            echo $matches[1];
        }

        break;

    case 'nginx':
        if ($redis->exists('nginx')) {
            echo $redis->get('nginx');
        } else {
            $html = file_get_contents('http://nginx.org/en/download.html');
            preg_match('/nginx-(\d\.\d{1,2}\.\d{1,3})/i', $html, $matches);

            $redis->setex('nginx', 3600, $matches[1]);

            echo $matches[1];
        }

        break;

    case 'phpmyadmin':
        if ($redis->exists('phpmyadmin')) {
            echo $redis->get('phpmyadmin');
        } else {
            $html = file_get_contents('http://www.phpmyadmin.net/home_page/version.json');
            $version = json_decode($html, true);

            $redis->setex('phpmyadmin', 3600, $version['version']);

            echo $version['version'];
        }

        break;

    case 'hosts':
        if ($redis->exists('hosts')) {
            $hosts = $redis->get('hosts');
        } else {
            $hosts = file_get_contents('https://www.dropbox.com/sh/lw0ljk3sllmimpz/AADvmg0wxOXHAtLQ9WhPlvAva/imouto.host.txt?dl=1');

            $redis->setex('hosts', 3600, $hosts);
        }

        if (isset($request[1]) && $request[1] == 'get') {
            echo $hosts;

            if (isset($request[2]) && $request[2] == 'dl') header('Content-Disposition: attachment; filename="imouto.host.txt"');
        } else {
                preg_match('/#\+UPDATE_TIME (\d{4}(-\d{2}){2} (\d{2}:){2}\d{2})/i', $hosts, $matches);

                echo $matches[1];
        }

        break;

    default:
        error_code(404);

        break;
}

?>
