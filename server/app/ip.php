<?php
if (!defined('IN_SINOSKY')) exit(1);

class version {
    public $request;
    public $time;

    public function __construct($request) {
        if (!$request) error_code(400);

        $this->request = $request;
    }

    public function run() {
        if (!$_POST['token'] || !$_POST['time'] || sha1(IP_KEY . $_POST['time']) != $_POST['token'])
            error_code(403);

        $db = new db();

        switch ($request[0]) {
            case 'update':
                $db->redis->set('ip', $_SERVER['REMOTE_ADDR']);

                echo $_SERVER['REMOTE_ADDR'];

                break;

            case 'export':
                if ($db->redis->exists('ip')) echo $db->redis->get('ip');

                break;
        }
    }
}
