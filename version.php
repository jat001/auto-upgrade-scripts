<?php

switch ($_GET['pro']) {
    case 'php':
        $html = file_get_contents('http://php.net/downloads.php');
        preg_match('/Current Stable.+?PHP (\d\.\d{1,2}\.\d{1,3})/is', $html, $matches);
        exit($matches[1]);

        break;

    case 'mysql':
        $html = file_get_contents('http://dev.mysql.com/downloads/mysql/');
        preg_match('/MySQL Community Server (\d\.\d{1,2}\.\d{1,3})/i', $html, $matches);
        exit($matches[1]);

        break;

    case 'nginx':
        $html = file_get_contents('http://nginx.org/en/download.html');
        preg_match('/nginx-(\d\.\d{1,2}\.\d{1,3})/i', $html, $matches);
        exit($matches[1]);

        break;

    case 'phpmyadmin':
        $html = file_get_contents('http://sourceforge.net/projects/phpmyadmin/files/');
        preg_match('/Download phpMyAdmin-(\d\.\d{1,2}\.\d{1,3})-all-languages/i', $html, $matches);
        exit($matches[1]);

        break;

    default:
        exit();

        break;
}

?>
