<?php

if (!defined('IN_SINOSKY')) exit();

$key = 'AtGAUajSng795E6B9SAsWUzCPcxBXUa67VxecY67Fq2yhpuc6ZjmKKreB5rWZq3W';

if (!$_POST['token'] || !$_POST['time'] || !$_POST['action'] || sha1($key . $_POST['time']) != $_POST['token'])
    error_code(403);

switch ($request[0]) {
    case 'update':
        $redis->set('ip', $_SERVER['REMOTE_ADDR'])

        echo $_SERVER['REMOTE_ADDR'];

        break;

    case 'export':
        if ($redis->exists('ip')) echo $redis->get('ip');

        break;
}

?>
