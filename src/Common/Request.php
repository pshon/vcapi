<?php
namespace VCAPI\Common;

class Request
{

    public static $domain = 'https://api3.vircities.com';

    public static $proxy = false; // 127.0.0.1:8080

    public static $apiVersion = '1.10';

    public static $os = 'android';

    public static $userAgent = 'Mozilla/5.0 (Linux; Android 4.4.4; Custom Phone - 4.4.4 - API 19 - 768x1280 Build/KTU84P) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/33.0.0.0 Safari/537.36';

    public static $debug = false;
    
    public static $cookieFileName = './cookie_';
    
    public static $initiated = false;

    /**
     * @param string $url
     * @param array $data
     * @param string $instanceIdentifier
     * @return mixed
     * @throws \Exception
     */
    public static function post($url = '/', $data = array(), $instanceIdentifier = '')
    {
        if(!self::$initiated) {
            self::checkWritableCookiePath($instanceIdentifier);
        }
        
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($data));
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_COOKIEJAR, self::$cookieFileName . $instanceIdentifier . '.txt');
        curl_setopt($curl, CURLOPT_COOKIEFILE, self::$cookieFileName . $instanceIdentifier . '.txt');
        
        if (self::$proxy) {
            curl_setopt($curl, CURLOPT_PROXY, self::$proxy);
        }
        
        curl_setopt($curl, CURLOPT_URL, self::makeUrl($url));
        curl_setopt($curl, CURLOPT_USERAGENT, self::$userAgent);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array(
            'X-Requested-With' => 'com.vircities.vc_mobile',
            'Content-type' => 'application/x-www-form-urlencoded'
        ));
        $result = curl_exec($curl);
        $realQuery = curl_getinfo($curl, CURLINFO_EFFECTIVE_URL);
        
        if (self::$debug) {
            echo 'HTTPS POST: ' . self::makeUrl($url) . " \n";
            echo 'DATA: ' . "\n";
            print_r($data);
            echo 'Response: ' . "\n";
            print_r(json_decode($result));
            echo "\n\n";
        }
        
        return json_decode($result);
    }

    /**
     * @param string $url
     * @param string $instanceIdentifier
     * @return mixed
     * @throws \Exception
     */
    public static function get($url = '/', $instanceIdentifier = '')
    {
        if(!self::$initiated) {
            self::checkWritableCookiePath($instanceIdentifier);
        }
        
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_COOKIEJAR, self::$cookieFileName . $instanceIdentifier . '.txt');
        curl_setopt($curl, CURLOPT_COOKIEFILE, self::$cookieFileName . $instanceIdentifier . '.txt');
        
        if (self::$proxy) {
            curl_setopt($curl, CURLOPT_PROXY, self::$proxy);
        }
        
        curl_setopt($curl, CURLOPT_URL, self::makeUrl($url));
        curl_setopt($curl, CURLOPT_USERAGENT, self::$userAgent);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array(
            'X-Requested-With' => 'com.vircities.vc_mobile',
            'Content-type' => 'application/x-www-form-urlencoded'
        ));
        $result = curl_exec($curl);
        $realQuery = curl_getinfo($curl, CURLINFO_EFFECTIVE_URL);
        
        if (self::$debug) {
            echo 'HTTPS GET: ' . self::makeUrl($url) . " \n";
            echo 'Response: ' . "\n";
            print_r(json_decode($result));
            echo "\n\n";
        }
        
        return json_decode($result);
    }

    /**
     * @param $instanceIdentifier
     * @throws \Exception
     */
    public static function removeCookie($instanceIdentifier)
    {
        if(!self::$initiated) {
            self::checkWritableCookiePath($instanceIdentifier);
        }
        @unlink(self::$cookieFileName . $instanceIdentifier . '.txt');
    }

    /**
     * @param $path
     * @return string
     */
    public static function makeUrl($path)
    {
        if (strpos($path, '?') === false) {
            $path .= '?os=' . self::$os . '&v=' . self::$apiVersion;
        } else {
            if (strpos($path, 'os=') === false) {
                $path .= '&os=' . self::$os . '&v=' . self::$apiVersion;
            }
        }
        
        return self::$domain . $path;
    }

    /**
     * @param string $instanceIdentifier
     * @throws \Exception
     */
    public static function checkWritableCookiePath($instanceIdentifier = '') {
        if(!file_exists(self::$cookieFileName . $instanceIdentifier . '.txt')) {
            if(!@touch(self::$cookieFileName . $instanceIdentifier . '.txt')) {
                throw new \Exception('Cookie file path is not writable, or do not have permission. Current cookie path: ' . self::$cookieFileName . $instanceIdentifier . '.txt' . '. Change: \VCAPI\Common\Request::$cookieFileName = "/cookie/writable/path/";');
            }
        } else {
            if(!is_writable(self::$cookieFileName . $instanceIdentifier . '.txt')) {
                throw new \Exception('Cookie file is not writable, or do not have permission. Current cookie path: ' . self::$cookieFileName . $instanceIdentifier . '.txt');
            }
        }
    }
}