<?php

namespace VCAPI\Common;

class Error {
        
    public static $exitIfError = false;
    
    public static $errors = array();
    
    public static function exception($message, $code = 0) {
        self::$errors[] = array(
            'code' => $code,
            'message' => $message
        );
        
        if(self::$exitIfError) {
            throw new \ErrorException($message, $code);
        }
    }
    
    public static function getLastError() {
        return end(self::$errors);
    }
    
}