<?php
if (!defined('IN_SINOSKY')) exit(1);

class http {
    public static function curl_get($url, $timeout = 15, $retry = 5) {
        if (is_array($url)) {
            foreach ($url as $k => $v) {
                $result[$k] = self::curl_get($v, $timeout, $retry);
            }

            return $result;
        }

        if (!isset($i)) {
            static $i;
            $i = 1;
        }

        if ($retry && $i > $retry) {
            $i = 1;
            return false;
        }

        $ch = curl_init($url);

        curl_setopt($ch, CURLOPT_AUTOREFERER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
        curl_setopt($ch, CURLOPT_SSLVERSION, CURL_SSLVERSION_TLSv1_2);

        curl_setopt($ch, CURLOPT_USERAGENT, 'SinoSky-API/' . API_Version . ' (+https://www.sinosky.org)');

        $result = curl_exec($ch);
        curl_close($ch);

        if (!$result && $retry) {
            $i++;
            sleep(1);

            return self::curl_get($url, $timeout, $retry);
        }

        $i = 1;
        return $result;
    }
}
