<?php
if (!defined('IN_SINOSKY')) exit();

class version {
    public $request;
    public $time;

    public function __construct($request) {
        if (!$request) error_code(400);

        $this->request = $request;
    }

    public function run() {
        $db = new db();

        switch ($this->request[0]) {
            case 'php':
                $result = $db->get('php', 'http://php.net/downloads.php', 'regex', '/Current Stable.+?PHP (\d\.\d{1,2}\.\d{1,3})/is');

                if ($result && is_array($result)) {
                    list($version, $this->time) = $result;

                    echo $version;
                }

                break;

            case 'mysql':
                $result = $db->get('mysql', 'http://dev.mysql.com/downloads/mysql/', 'regex', '/MySQL Community Server (\d\.\d{1,2}\.\d{1,3})/i');

                if ($result && is_array($result)) {
                    list($version, $this->time) = $result;

                    echo $version;
                }

                break;

            case 'nginx':
                $result = $db->get('nginx', 'http://nginx.org/en/download.html', 'regex', '/nginx-(\d\.\d{1,2}\.\d{1,3})/i');

                if ($result && is_array($result)) {
                    list($version, $this->time) = $result;

                    echo $version;
                }

                break;

            case 'phpmyadmin':
                $result = $db->get('phpmyadmin', 'http://www.phpmyadmin.net/home_page/version.json', 'json', 'version');

                if ($result && is_array($result)) {
                    list($version, $this->time) = $result;

                    echo $version;
                }

                break;

            case 'hosts':
                $result = $db->get('hosts', 'http://freedom.txthinking.com/hosts');

                if ($result && is_array($result)) {
                    list($hosts, $this->time) = $result;

                    if (isset($this->request[1]) && $this->request[1] == 'get') {
                        echo $hosts;

                        if (isset($this->request[2]) && $this->request[2] == 'dl')
                            header('Content-Disposition: attachment; filename="hosts"');
                    } else {
                            preg_match('/# UPDATE: (.+)/i', $hosts, $matches);

                            if (!empty($matches[1])) echo strtotime($matches[1]);
                    }
                }

                break;

            case 'hhvm':
                $result = $db->get('hhvm', 'https://api.github.com/repos/facebook/hhvm/tags', 'callback', function ($tags) {
                    $tags = json_decode($tags, true);

                    if (!$tags) return false;

                    foreach ($tags as $tag) {
                        if (substr($tag['name'], 0, 5) != 'HHVM-') continue;

                        $tag['version'] = substr($tag['name'], 5);
                        if (empty($version) || version_compare($tag['version'], $version, '>')) $version = $tag['version'];
                    }

                    return $version;
                });

                if ($result && is_array($result)) {
                    list($version, $this->time) = $result;

                    echo $version;
                }

                break;

            default:
                error_code(400);

                break;
        }
    }
}
