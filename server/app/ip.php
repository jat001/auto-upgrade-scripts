<?php
if (!defined('IN_SINOSKY')) exit(1);

class version extends output {
    public function run() {
        if (!$_POST['token'] || !$_POST['time'] || sha1(IP_KEY . $_POST['time']) != $_POST['token'])
            self::error_code(403);

        switch (self::$request[0]) {
            case 'update':
                $this->db->redis->set('ip', $_SERVER['REMOTE_ADDR']);

                echo $_SERVER['REMOTE_ADDR'];

                break;

            case 'export':
                if ($this->db->redis->exists('ip')) echo $this->db->redis->get('ip');

                break;

            default:
                self::error_code(400);

                break;
        }
    }
}
