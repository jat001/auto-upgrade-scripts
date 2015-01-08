<?php
if (!defined('IN_SINOSKY')) exit();

class http {
    private $curl_i = 1;

    public function curl_get($url, $timeout = 15, $retry = 5) {
        if ($retry && $this->curl_i > $retry) return false;

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
            $this->curl_i++;
            sleep(1);

            return $this->curl_get($url, $timeout, $retry);
        }

        return $result;
    }
}
