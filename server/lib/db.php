<?php
if (!defined('IN_SINOSKY')) exit(1);

class db {
    public $redis;

    public function __construct() {
        $this->redis = new Redis();
        $this->redis->connect(REDIS_SOCKET);
        $this->redis->select(REDIS_DB_INDEX);
    }

    public function __destruct() {
//      $this->redis->bgsave();
        $this->redis->close();
    }

    private function _get($name, $url, $type = null, $expression = null, $expires = 604800) {
        $result = http::curl_get($url);

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

            case 'callback':
                $result = call_user_func($expression, $result);

                if (empty($result)) return false;

                break;
        }

        $this->redis->setex($name, $expires, serialize($result));

        return $result;
    }

    public function get($name, $url, $type = null, $expression = null, $expires = 604800) {
        $ttl = $this->redis->ttl($name);

        if ($ttl == -2) {
            $result = $this->_get($name, $url, $type, $expression, $expires);

            if (!$result) return false;

            $time = time();
        } else {
            $result = unserialize($this->redis->get($name));

            if (!$result) return false;

            $time = time() - ($expires - $ttl);

            if ($ttl < $expires - 86400) {
                longtime::$funcs[] = [
                    'obj' => $this,
                    'method' => 'update',

                    'args' => [
                        $name,
                        $url,
                        $type,
                        $expression,
                        $expires
                    ]
                ];
            }
        }

        return [
            $result,
            $time
        ];
    }

    public function update($name, $url, $type, $expression, $expires)
    {
        if ($this->redis->exists('updating')) return false;

        $this->redis->setex('updating', 60, '1');

        $this->_get($name, $url, $type, $expression, $expires);

        $this->redis->del('updating');

        return true;
    }
}
