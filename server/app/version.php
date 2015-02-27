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

                if ($result) {
                    list($version, $this->time) = $result;

                    echo $version;
                }

                break;

            case 'mysql':
                $result = $db->get('mysql', 'http://dev.mysql.com/downloads/mysql/', 'regex', '/MySQL Community Server (\d\.\d{1,2}\.\d{1,3})/i');

                if ($result) {
                    list($version, $this->time) = $result;

                    echo $version;
                }

                break;

            case 'nginx':
                $result = $db->get('nginx', 'http://nginx.org/en/download.html', 'regex', '/nginx-(\d\.\d{1,2}\.\d{1,3})/i');

                if ($result) {
                    list($version, $this->time) = $result;

                    echo $version;
                }

                break;

            case 'phpmyadmin':
                $result = $db->get('phpmyadmin', 'http://www.phpmyadmin.net/home_page/version.json', 'json', 'version');

                if ($result) {
                    list($version, $this->time) = $result;

                    echo $version;
                }

                break;

            case 'hosts':
                $result = $db->get('hosts', 'http://freedom.txthinking.com/hosts');

                if ($result) {
                    list($hosts, $this->time) = $result;

                    if (isset($this->request[1]) && $this->request[1] == 'get') {
                        if (isset($this->request[2]) && $this->request[2] == 'dl')
                            header('Content-Disposition: attachment; filename="hosts"');

                        echo $hosts;
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

                if ($result) {
                    list($version, $this->time) = $result;

                    echo $version;
                }

                break;

            case 'czip':
                $data_dir = build_file_path([
                    DATA_PATH,
                    'czip'
                ], false);

                if (!file_exists($data_dir)) {
                    mkdir($data_dir, 0755, true);
                }

                $result = $db->get('czip', 'http://update.cz88.net/ip/copywrite.rar', 'callback', function ($copywrite) {
                    if (!$copywrite) return false;

                    $copywrite = unpack('Z4sign/Vversion/Vunknow0/Vsize/Vunknow1/Vkey/Z128text/Z128link', $copywrite);

                    $copywrite['text'] = iconv('GB18030', 'UTF-8', $copywrite['text']);

                    preg_match_all('/(\d{4})年(\d{1,2})月(\d{1,2})日/', $copywrite['text'], $matches);
                    $matches[2][0] = sprintf('%\'02s', $matches[2][0]);
                    $matches[3][0] = sprintf('%\'02s', $matches[3][0]);

                    $copywrite['date'] = $matches[1][0] . $matches[2][0] . $matches[3][0];

                    $qqwry = http::curl_get('http://update.cz88.net/ip/qqwry.rar', 30);

                    if (strlen($qqwry) != $copywrite['size']) return false;

                    $qqwry_header = str_split(substr($qqwry, 0, 0x200));
                    $qqwry = substr($qqwry, 0x200);

                    $key = $copywrite['key'];

                    for($i=0; $i<0x200; $i++)
                    {
                        $key *= 0x805;
                        $key++;
                        $key &= 0xFF;

                        $qqwry_header[$i] = chr(ord($qqwry_header[$i]) ^ $key);
                    }

                    $qqwry = gzuncompress(implode('', $qqwry_header) . $qqwry);

                    file_put_contents(build_file_path([
                        DATA_PATH,
                        'czip',
                        $copywrite['date'] . '.dat'
                    ], false), $qqwry);

                    return [
                        'version' => $copywrite['version'],
                        'date' => strtotime($copywrite['date'])
                    ];
                });

                if ($result) {
                    list($version, $this->time) = $result;

                    $qqwry = date('Ymd', $version['date']) . '.dat';

                    $data_list = scandir($data_dir);
                    foreach ($data_list as $data) {
                        if ($data == '.' || $data == '..') continue;

                        if ($data != $qqwry) unlink(build_file_path([
                            $data_dir,
                            $data
                        ]));
                    }

                    $qqwry = build_file_path([
                        $data_dir,
                        $qqwry
                    ], false);

                    if (isset($this->request[1])) {
                        switch ($this->request[1]) {
                            case 'date':
                                echo $version['date'];

                                break;

                            case 'dl':
                                header('Content-Disposition: attachment; filename="qqwry.dat"');
                                readfile($qqwry);

                                break;

                            case 'sha1':
                                echo sha1_file($qqwry);

                                break;

                            case 'md5':
                                echo md5_file($qqwry);

                                break;

                            default:
                                echo $version['version'];

                                break;
                        }
                    } else {
                        echo $version['version'];
                    }
                }

                break;

            default:
                error_code(400);

                break;
        }
    }
}
