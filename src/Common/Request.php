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
    
    public static $cookieFileName = './cookie.txt';

    public static function post($url = '/', $data = array())
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($data));
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_COOKIEJAR, self::$cookieFileName);
        curl_setopt($curl, CURLOPT_COOKIEFILE, self::$cookieFileName);
        
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

    public static function get($url = '/')
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_COOKIEJAR, self::$cookieFileName);
        curl_setopt($curl, CURLOPT_COOKIEFILE, self::$cookieFileName);
        
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

    public static function removeCookie()
    {
        @unlink(self::$cookieFileName);
    }

    /**
     * @param string $path
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
}