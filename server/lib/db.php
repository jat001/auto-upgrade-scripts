<?php
if (!defined('IN_SINOSKY')) exit();

class db {
    public $redis;

    public function __construct() {
        $this->redis = new Redis();
        $this->redis->connect(REDIS_SOCKET);
    }

    public function __destruct() {
        //$this->redis->bgsave();
        $this->redis->close();
    }

    public function _get($name, $url, $type = null, $expression = null, $expires = 604800) {
        $curl = new http();

        $result = $curl->curl_get($url);

        if (!$result) return false;

        switch ($type) {
            case 'regex':
                preg_match($expression, $result, $matches);

                if (empty($matches[1])) return false;

                $result = $matches[1];

                break;

            case 'json':
                $result = json_decode($result, true);

                if (empty($result[$expression])) return false;

                $result = $result[$expression];

                break;
        }

        $this->redis->setex($name, $expires, $result);

        return $result;
    }

    public function get($name, $url, $type = null, $expression = null, $expires = 604800) {
        $ttl = $this->redis->ttl($name);

        if ($ttl == -2) {
            $result = $this->_get($name, $url, $type, $expression, $expires);

            if (!$result) return false;

            $time = time();
        } else {
            $result = $this->redis->get($name);

            if (!$result) return false;

            $time = time() - ($expires - $ttl);

            if ($ttl < $expires - 86400 && !$this->redis->exists('updating')) {
                $this->redis->setex('updating', 60, '1');

                $this->__destruct();

                $pid = pcntl_fork();

                $this->__construct();

                if ($pid === 0) {
                    $this->_get($name, $url, $type, $expression, $expires);

                    $this->redis->del('updating');

                    exit();
                }
            }

        }

        return [$result, $time];
    }
}
