<?php

/**
 * class Str - Support functions for String
 *
 * @package Lib
 * @created 2014-11-25
 * @version 1.0
 * @author thailh
 * @copyright Oceanize INC
 */

namespace Lib;

use Fuel\Core\Security;

class Str {

    /**
     * Create a random string
     *
     * @author thailh
     * @param int $length Length of random string
     * @param string $chars String for random
     * @return string Random string
     */
    public static function random($length = 5, $chars = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ') {
        $chars .= $chars . time();
        $cnt = 0;
        $result = '';
        do {
            $result .= substr($chars, rand(0, strlen($chars) - 1), 1);
            $cnt++;
        } while ($cnt < $length);
        return $result;
    }

    /**
     * Create a random string (without time())
     *
     * @author thailh
     * @param int $length Length of random string
     * @param string $chars String for random
     * @return string Random string
     */
    public static function randomStr($length = 2, $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz') {
        $cnt = 0;
        $result = '';
        do {
            $result .= substr($chars, rand(0, strlen($chars) - 1), 1);
            $cnt++;
        } while ($cnt < $length);
        return $result;
    }

    /**
     * Generate token for api   
     *  
     * @author thailh
     * @return string Token string
     */
    public static function generate_token_for_api() {
        return Security::generate_token();
    }

    /**
     * Generate token    
     *  
     * @author thailh
     * @return string Token string
     */
    public static function generate_token() {
        return Security::generate_token();
    }

    /**
     * Generate token if forgetting password for mobile   
     *  
     * @author thailh
     * @return string Token string
     */
    public static function generate_token_forget_password_for_mobile() {
        return static::random(6, 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz');
    }

    /**
     * Generate app id
     *  
     * @author thailh
     * @param int $userId Id of user
     * @return string App id
     */
    public static function generate_app_id($userId) {
        return $userId . static::random(2, 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz');
    }

    /**
     * Truncate a string by characters
     *  
     * @author thailh
     * @param string $string String for truncating
     * @param int $limit Limit character for string after truncating
     * @param string $continuation Continuation
     * @param bool $is_html If true will return html format
     * @return string String after truncating
     */
    public static function truncate($string, $limit, $continuation = '...', $is_html = false) {
        return \Fuel\Core\Str::truncate($string, $limit, $continuation, $is_html);
    }

    /**
     * Method startsWith - check if start with   
     *  
     * @author thailh
     * @param string $haystack A string to search
     * @param string $needle If needle is not a string, it is converted to an integer and applied as the ordinal value of a character.
     * @return bool Return true if successful ortherwise return false
     */
    public static function startsWith($haystack, $needle) {
        return $needle === "" || strrpos($haystack, $needle, -strlen($haystack)) !== FALSE;
    }

    /**
     * Method endsWith - check if end with   
     *  
     * @author thailh
     * @param string $haystack A string to search
     * @param string $needle If needle is not a string, it is converted to an integer and applied as the ordinal value of a character.
     * @return bool Return true if successful ortherwise return false
     */
    public static function endsWith($haystack, $needle) {
        return $needle === "" || strpos($haystack, $needle, strlen($haystack) - strlen($needle)) !== FALSE;
    }

    /**
     * Generate password
     *  
     * @author thailh
     * @param int $userId Id of user
     * @return string App id
     */
    public static function generate_password() {
        return static::random(6, 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz');
    }

    /**
     * Truncate a string by words
     *
     * @author thailh
     * @param string $string String for truncating
     * @param int $limit Limit word for string after truncating
     * @param string $continuation Continuation
     * @return string String after truncating
     */
    public static function truncate_word($string, $limit, $continuation = '...') {
        if (str_word_count($string, 0) > $limit) {
            $words = str_word_count($string, 2);
            $pos = array_keys($words);
            $string = mb_substr($string, 0, $pos[$limit], 'UTF-8') . $continuation;
        }
        return $string;
    }

    /**
     * Detect japanese
     *
     * @author thailh
     * @param string $string String for truncating     
     * @return boolean True/False
     */
    public static function is_japanese($string) {
        return preg_match('/[\x{4E00}-\x{9FBF}\x{3040}-\x{309F}\x{30A0}-\x{30FF}]/u', $string);
    }

    /**
     * Convert number 1 byte to 2 byte for japanese
     *
     * @author thailh
     * @param string $string String for convert     
     * @return string
     */
    public static function number2Bytes($string) {
        if (static::is_japanese($string)) {
            $f = array(
                '0', '1', '2', '3', '4', '5', '6', '7', '8', '9', '-'
            );
            $r = array(
                '０', '１', '２', '３', '４', '５', '６', '７', '８', '９', 'ー'
            );
            $string = str_replace($f, $r, $string);
        }
        return $string;
    }
    
    /**
     * Convert string to url
     *
     * @author thailh
     * @param string $string String for convert     
     * @return string
     */
    public static function convertURL($str)
    {
        $str = preg_replace("/(\,|-|\.|\'|\+)/", '', $str);
        $str = preg_replace("/(à|á|ạ|ả|ã|â|ầ|ấ|ậ|ẩ|ẫ|ă|ằ|ắ|ặ|ẳ|ẵ)/", 'a', $str);
        $str = preg_replace("/(è|é|ẹ|ẻ|ẽ|ê|ề|ế|ệ|ể|ễ)/", 'e', $str);
        $str = preg_replace("/(ì|í|ị|ỉ|ĩ)/", 'i', $str);
        $str = preg_replace("/(ò|ó|ọ|ỏ|õ|ô|ồ|ố|ộ|ổ|ỗ|ơ|ờ|ớ|ợ|ở|ỡ)/", 'o', $str);
        $str = preg_replace("/(ù|ú|ụ|ủ|ũ|ư|ừ|ứ|ự|ử|ữ)/", 'u', $str);
        $str = preg_replace("/(ỳ|ý|ỵ|ỷ|ỹ)/", 'y', $str);
        $str = preg_replace("/(đ)/", 'd', $str);
        $str = preg_replace("/(À|Á|Ạ|Ả|Ã|Â|Ầ|Ấ|Ậ|Ẩ|Ẫ|Ă|Ằ|Ắ|Ặ|Ẳ|Ẵ)/", 'A', $str);
        $str = preg_replace("/(È|É|Ẹ|Ẻ|Ẽ|Ê|Ề|Ế|Ệ|Ể|Ễ)/", 'E', $str);
        $str = preg_replace("/(Ì|Í|Ị|Ỉ|Ĩ)/", 'I', $str);
        $str = preg_replace("/(Ò|Ó|Ọ|Ỏ|Õ|Ô|Ồ|Ố|Ộ|Ổ|Ỗ|Ơ|Ờ|Ớ|Ợ|Ở|Ỡ)/", 'O', $str);
        $str = preg_replace("/(Ù|Ú|Ụ|Ủ|Ũ|Ư|Ừ|Ứ|Ự|Ử|Ữ)/", 'U', $str);
        $str = preg_replace("/(Ỳ|Ý|Ỵ|Ỷ|Ỹ)/", 'Y', $str);
        $str = preg_replace("/(Đ)/", 'D', $str);
        $str = preg_replace('/\s+/', '-', $str);
        $str = str_replace("/", "-", $str);
        $str = str_replace(" ", "-", $str);
        $str = str_replace("?", "", $str);
        
        return strtolower($str);
    }
}
