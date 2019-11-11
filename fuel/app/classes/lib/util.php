<?php

/**
 * class Util - support functions for Util
 *
 * @package Lib
 * @created 2014-11-25
 * @version 1.0
 * @author thailh
 * @copyright Oceanize INC
 */

namespace Lib;

use Fuel\Core\Image;
use Fuel\Core\Config;
use Fuel\Core\Input;
use Fuel\Core\Crypt;

class Util {

    /**
     * Method getShortUrl - get short url  
     *  
     * @author thailh
     * @param string $longUrl Long url
     * @return string/bool
     */
    public static function getShortUrl($longUrl = '') {
        $bitlyConfig = Config::get('bitly');
        $postFields = $bitlyConfig['auth'];
        $postFields['longUrl'] = $longUrl;
        $ch = curl_init($bitlyConfig['url']);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_TIMEOUT, $bitlyConfig['timeout']);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
        $response = curl_exec($ch);
        $response = json_decode($response, true);
        if ($response['status_code'] == 200 && isset($response['data']['url'])) {
            curl_close($ch);
            return $response['data']['url'];
        } else {
            \LogLib::warning(curl_error($ch), __METHOD__, $response);
            curl_close($ch);
        }
        return false;
    }

    public static function googleShortUrl($longUrl = '') {
        $message = 'System error';

        try {
            $url = Config::get('google_urlshortener')['url'] . '?key=' . Config::get('google_urlshortener')['key'];
            $param['longUrl'] = $longUrl;
            $ch = curl_init();
            $options = array(
                CURLOPT_URL => $url,
                CURLOPT_HEADER => false,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_SAFE_UPLOAD => false,
                CURLOPT_HTTPHEADER => array('Content-Type: application/json'),
                CURLOPT_POST => true,
                CURLOPT_POSTFIELDS => json_encode($param),
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_VERBOSE => false,
                CURLOPT_TIMEOUT => \Config::get('google_urlshortener.timeout', 30),
            );
            curl_setopt_array($ch, $options);
            $jsonResponse = curl_exec($ch);
            $response = json_decode($jsonResponse, true);
            curl_close($ch);

            if (isset($response['id'])) {
                return $response['id'];
            }

            if (isset($response['error']['errors']['message'])) {
                $message = $response['error']['errors']['message'];
            }
        } catch (Exception $ex) {
            $message = $ex->getMessage();
        }

        \LogLib::error($message, __METHOD__);

        return $longUrl;
    }

    /**
     * Method uploadImage - upload image  
     *  
     * @author thailh
     * @param array $selection
     * @return array
     */
    public static function uploadImage($thumb = '', $mix_ext = null) {
        if (empty($thumb)) {
            $thumb = Input::param('thumb');
        }
        $thumbConfig = Config::get('thumbs');
        if (!empty($thumb) && !empty($thumbConfig[$thumb])) {
            $thumbSize = $thumbConfig[$thumb];
        }
        try {
            $uploadConfig = Config::load('upload', true);
            $uploadConfig['ext_whitelist'] = Config::get('image_ext');
            if (!empty($mix_ext)) {
                $uploadConfig['ext_whitelist'] = array_merge($uploadConfig['ext_whitelist'], $mix_ext);
            }
            AppUpload::process($uploadConfig);
            if (!AppUpload::is_valid()) {
                $uploadError = array();
                foreach (AppUpload::get_errors() as $errors) {
                    if (!empty($errors['errors'])) {
                        foreach ($errors['errors'] as $error) {
                            if ($error['error'] == 101 && isset($uploadConfig['max_size'])) {
                                $error['message'] .= ' [' . $uploadConfig['max_size'] . 'kb]';
                            }
                            $uploadError[] = array(
                                'field' => $errors['field'],
                                'code' => $error['error'],
                                'value' => $error['message'],
                            );
                        }
                    }
                }
                return array('status' => 400, 'error' => $uploadError);
            } else {
                AppUpload::save();
                $result = array();
                foreach (AppUpload::get_files() as $file) {
                    $savedAs = explode('.', $file['saved_as']);
                    $fileName = $savedAs[0];
                    if (!empty($thumbSize)) {
                        foreach ($thumbSize as $size) {
                            list($w, $h) = explode('x', $size);
                            if (!empty($w) && !empty($h)) {
                                Image::load($file['saved_to'] . '/' . $fileName . '.' . strtolower($file['extension']))
                                        ->crop_resize($w, $h)
                                        ->save($file['saved_to'] . '/' . $fileName . "_{$size}." . strtolower($file['extension']));
                            }
                        }
                    }
                    $result[$file['field']] = Config::get('img_url') . date('Y/m/d') . '/' . $fileName . '.' . strtolower($file['extension']);
                }
                return array('status' => 200, 'body' => $result);
            }
        } catch (Exception $e) {
            \LogLib::error(sprintf("Upload image Exception\n"
                            . " - Message : %s\n"
                            . " - Code : %s\n"
                            . " - File : %s\n"
                            . " - Line : %d\n"
                            . " - Stack trace : \n"
                            . "%s", $e->getMessage(), $e->getCode(), $e->getFile(), $e->getLine(), $e->getTraceAsString()), __METHOD__, $_FILES);
            return array('status' => 500, 'error' => $e->getTraceAsString());
        }
    }

    /**
     * Method uploadVideo - upload video   
     *  
     * @author thailh
     * @param array $selection
     * @return array
     */
    public static function uploadVideo($selection = array()) {
        try {
            if ($selection) {
                Config::set('upload.selection', $selection);
            }
            $uploadConfig = Config::load('upload', true);
            $uploadConfig['ext_whitelist'] = Config::get('video_ext');
            AppUpload::process($uploadConfig);
            if (!AppUpload::is_valid()) {
                return array('status' => 400, 'error' => AppUpload::get_errors());
            } else {
                AppUpload::save(array('video'));
                $result = array();
                foreach (AppUpload::get_files() as $file) {
                    $savedAs = explode('.', $file['saved_as']);
                    $fileName = $savedAs[0];
                    $result[$file['field']] = date('Y/m/d') . '/' . $fileName . '.' . $file['extension'];
                }
                return array('status' => 200, 'body' => $result);
            }
        } catch (Exception $e) {
            \LogLib::error(sprintf("Upload video Exception\n"
                            . " - Message : %s\n"
                            . " - Code : %s\n"
                            . " - File : %s\n"
                            . " - Line : %d\n"
                            . " - Stack trace : \n"
                            . "%s", $e->getMessage(), $e->getCode(), $e->getFile(), $e->getLine(), $e->getTraceAsString()), __METHOD__, $_FILES);
            return array('status' => 500, 'error' => $e->getTraceAsString());
        }
    }

    /**
     * Method os - get operating system type   
     *  
     * @author thailh
     * @return string Type name of OS
     */
    public static function os() {
        preg_match("/campusan|iphone|android|ipad|ipod|webos/", strtolower(Input::user_agent()), $matches);
        $os = current($matches);
        switch ($os) {
            case 'campusan':
            case 'iphone':
            case 'ipad':
            case 'ipod':
                $os = Config::get('os')['ios'];
                break;
            case 'android':
                $os = Config::get('os')['android'];
                break;
            default:
                $os = Config::get('os')['webos'];
                break;
        }
        return $os;
    }

    /**
     * Method deviceId - get device type   
     *  
     * @author thailh
     * @param string $os Name of operating system
     * @return int Type of device 
     */
    public static function deviceId($os) {
        if (isset($os)) {
            if ($os == \Config::get('os')['ios']) {
                return 1; //ios
            } elseif ($os == \Config::get('os')['android']) {
                return 2; //android
            } else {
                return 3; // webos
            }
        }
        return 0;
    }

    /**
     * Method authUserId - Fetch User-Id from the HTTP request headers   
     *  
     * @author thailh
     * @return array Information of User-Id
     */
    public static function authUserId() {
        return Input::headers('User-Id');
    }

    /**
     * Method authToken - Fetch Authorization from the HTTP request headers   
     *  
     * @author thailh
     * @return array Information of Authorization
     */
    public static function authToken() {
        return Input::headers('Authorization');
    }
    
    /**
     * Upload from image url   
     *  
     * @author thailh
     * @param string $sUrl Image url
     * @return array 
     */
    public static function uploadFromImageUrl($sUrl = '') {
        if (empty($sUrl))
            return false;
        $uploadConfig = Config::load('upload', true);
        $sUploadDir = $uploadConfig['path'];
        //Ecore_Function::mkDirectory($sUploadDir);
        $fImage = @file_get_contents($sUrl, FILE_USE_INCLUDE_PATH);
        $aFileInfo = pathinfo($sUrl);
        if (is_array($aFileInfo) && count($aFileInfo) >= 4) {
            $sExt = strtolower(strrchr($aFileInfo["basename"], '.'));
            $sImage = uniqid() . time();
            $sFilename = $sUploadDir . $sImage . $sExt;
            @mkdir($sUploadDir, 0777, true);
            if (file_put_contents($sFilename, $fImage) !== false) {
                $aSize = @getimagesize($sFilename);
                if (is_array($aSize) && count($aSize)) {
                    $img_url = Config::get('img_url');
                    $img_url = str_replace($uploadConfig['img_dir'], $img_url, $sFilename);
                    return array($sFilename, $img_url);
                }
            }
        }
        return array();
    }

    /**
     * Check if url is valid   
     *  
     * @author thailh
     * @param string $url An url
     * @return bool Return true if successful ortherwise return false
     */
    public static function url_exists($url) {
        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            return false;
        }
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_NOBODY, true);
        curl_exec($ch);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        return ($code == 200);
    }

    /**
     * Encode password 
     *  
     * @author thailh
     * @param string $pwd Password
     * @param string $email Email
     * @return string Password after encoding
     */
    public static function encodePassword($pwd, $email = '') {
        return Crypt::encode($email . ':;' . $pwd);
    }

    /**
     * Decode password 
     *  
     * @author thailh
     * @param string $pwd Password
     * @return string Password after decoding
     */
    public static function decodePassword($pwd) {
        return Crypt::decode($pwd);
    }

    /**
     * Check if is mobie
     *  
     * @author thailh
     * @return bool Return true if mobile ortherwise return false
     */
    public static function isMobile() {
        $os = static::os();
        if ($os == \Config::get('os')['ios'] || $os == \Config::get('os')['android']) {
            return true;
        }
        return false;
    }

    /**
     * Get time
     *  
     * @author thailh
     * @param string $d string of time
     * @return int A timestamp on success, false otherwise
     */
    public static function gmtime($d) {
        if (empty($d)) {
            return false;
        }
        return strtotime(gmdate("M d Y H:i:s", strtotime($d)));
    }

    /**
     * Delete avatar / cover file
     * @param string $url
     */
    public static function deleteImage($url) {
        $uploadConfig = Config::load('upload', true);
        $imgUrl = Config::get('img_url');
        if (!empty($url) && !empty($uploadConfig['img_dir']) && !empty($imgUrl) && Str::startsWith($url, $imgUrl)) {
            $imgPath = str_replace($imgUrl, '', $url);
            $imgPath = $uploadConfig['img_dir'] . $imgPath;
            if (is_file($imgPath)) {
                @unlink($imgPath);
            }
        }
    }

    /**
     * Check lock filename for Tasks
     * 
     * @param string $lock_filename
     * @return boolean true: lock, false: not lock
     */
    public static function checkTaskLock($lock_filename) {
        $tasks_lock_dir = APPPATH . 'tmp' . DS;
        $tasks_lock_time = 30 * 60; // 30 minute
        // Valid param?
        if (empty($lock_filename)) {
            return false;
        }

        // Check lock directory
        @mkdir($tasks_lock_dir, 0777, true);
        if (!is_dir($tasks_lock_dir)) {
            // Cannot check Task-Lock: Directory not exist
            return false;
        }

        // Check lock file exist?
        if (!is_file($tasks_lock_dir . $lock_filename)) {
            // Create file
            try {
                $fp = fopen($tasks_lock_dir . $lock_filename, 'wb');
                fwrite($fp, time() . "\n");
                fclose($fp);
            } catch (Exception $ex) {
                // Cannot create lock file, send report mail
                $message = sprintf("Exception\n"
                        . " - Message : %s\n"
                        . " - Code : %s\n"
                        . " - File : %s\n"
                        . " - Line : %d\n"
                        . " - Stack trace : \n"
                        . "%s", $ex->getMessage(), $ex->getCode(), $ex->getFile(), $ex->getLine(), $ex->getTraceAsString());

                \LogLib::error($message, __METHOD__, 'Cannot create lock file: ' . $lock_filename);

                // Try close resource
                try {
                    if ($fp) {
                        fclose($fp);
                    }
                } catch (Exception $ex) {
                    
                }
            }

            return false;
        }

        // Check lock file expired?
        try {
            // Get first line
            $fp = fopen($tasks_lock_dir . $lock_filename, 'r');
            $start_time = trim(fgets($fp));
            fclose($fp);

            // Valid data? Expired? Compare with max_lock_time
            if (empty($start_time) || !is_numeric($start_time) || (time() - $start_time > $tasks_lock_time)) {
                // Update new data
                file_put_contents($tasks_lock_dir . $lock_filename, time() . "\n");
                return false;
            }
        } catch (Exception $ex) {
            // Cannot get first line of lock file
            $message = sprintf("Exception\n"
                    . " - Message : %s\n"
                    . " - Code : %s\n"
                    . " - File : %s\n"
                    . " - Line : %d\n"
                    . " - Stack trace : \n"
                    . "%s", $ex->getMessage(), $ex->getCode(), $ex->getFile(), $ex->getLine(), $ex->getTraceAsString());

            \LogLib::error($message, __METHOD__, 'Canot read lock file: ' . $lock_filename);

            // Try close resource
            try {
                if ($fp) {
                    fclose($fp);
                }
            } catch (Exception $ex) {
                
            }
        }

        return true;
    }

    /**
     * Delete task lock file
     * 
     * @param string $lock_filename
     * @return boolean true: delete ok, false: ng
     */
    public static function deleteTaskLock($lock_filename) {
        $tasks_lock_dir = APPPATH . 'tmp' . DS;
        @unlink($tasks_lock_dir . $lock_filename);
        $deleted = !is_file($tasks_lock_dir . $lock_filename);

        if (!$deleted) {
            // Cannot delete lock file
            $message = 'Canot delete lock file: ' . $lock_filename;
            \LogLib::error($message);
        }

        return $deleted;
    }

    /**
     * Delete all task lock file
     */
    public static function deleteTaskLocks() {
        $tasks_lock_dir = APPPATH . 'tmp' . DS;
        $dh = opendir($tasks_lock_dir);
        while (false !== ($filename = readdir($dh))) {
            $is_task_temp_file = is_file($tasks_lock_dir . $filename) && \Lib\Str::startsWith($filename, 'tasks_');
            if (!$is_task_temp_file) {
                continue;
            }
            @unlink($tasks_lock_dir . $filename);
        }
    }

    /**
     * List all file in task lock directory
     * 
     * @return array
     */
    public static function getTaskLocks() {
        $files = array();
        $tasks_lock_dir = APPPATH . 'tmp' . DS;
        $dh = opendir($tasks_lock_dir);
        while (false !== ($filename = readdir($dh))) {
            $is_task_temp_file = is_file($tasks_lock_dir . $filename) && \Lib\Str::startsWith($filename, 'tasks_');
            if (!$is_task_temp_file) {
                continue;
            }
            // Get first line
            $fp = fopen($tasks_lock_dir . $filename, 'r');
            $start_time = trim(fgets($fp));
            fclose($fp);
            if (is_numeric($start_time)) {
                $start_time = date('Y-m-d H:i', $start_time);
            }
            $files[] = $filename . ' - ' . $start_time;
        }
        return $files;
    }
    
    /**
     * Upload image Base64
     * 
     * @return array
     */
    public static function uploadImageBase64($base64code) {
        $uploadConfig = Config::load('upload', true);
        $upload_dir = $uploadConfig['path'];
        if (!is_dir($upload_dir)) {
            @mkdir($upload_dir, 0777, true);
        }
        $img = $base64code;
        $img = str_replace('data:image/png;base64,', '', $img);
        $img = str_replace(' ', '+', $img);
        $data = base64_decode($img);
        $time = str_replace('.', '', microtime(true));
        $fileName = $time.".png";
        $filePath = $upload_dir . $fileName;
        $fileUrl = \Config::get('img_url') . date('Y/m/d') . '/' . $fileName;
        $success = file_put_contents($filePath, $data);
        return $success ? $fileUrl : '';
    }
    
    /**
     * Get all links from website
     * 
     * @return array
     */
    public static function getWebsiteUrls($website) {
        // Init
        $urls = array();
        if (empty($website)) {
            return $urls;
        }
        
        // Get website content
        $str = file_get_contents($website);

        // Gets Webpage Title
        /*if (strlen($str) > 0) {
            $str = trim(preg_replace('/\s+/', ' ', $str)); // supports line breaks inside <title>
            preg_match("/\<title\>(.*)\<\/title\>/i", $str, $title); // ignore case
            $title = $title[1];
        }*/

        // Gets Webpage Description
        /*$b = $website;
        @$url = parse_url($b);
        @$tags = get_meta_tags($url['scheme'] . '://' . $url['host']);
        $description = $tags['description'];
        */
        // Gets Webpage Internal Links
        $doc = new \DOMDocument;
        @$doc->loadHTML($str);

        $items = $doc->getElementsByTagName('a');
        foreach ($items as $value) {
            $attrs = $value->attributes;
            $urls[] = $attrs->getNamedItem('href')->nodeValue;
        }
        
        return $urls;
    }
    
    /**
     * Get page data
     * 
     * @return array
     */
    public static function getPageData($url, $element = 'div', $className) {
        // Init
        $data = '';
        if (empty($url)) {
            return $data;
        }
        
        // Get website content
        $dom = new \DOMDocument;
        $str = file_get_contents($url);
        
        @$dom->loadHTML($str);

        $xpath = new \DOMXPath($dom);

//        $title = $xpath->query("//{$element}[@class='{$className}']");
//
//        $data['title'] = $title->item(0)->nodeValue;
        
        $description = $xpath->query("//{$element}[@class='{$className}']");
        $description = $description->item(0);
        $description = htmlentities($dom->saveXML($description));
        preg_match_all("/<div class=\'DetailContent\'>(.*?)<\/div>/", $description, $output);
        $data['content'] = $output;
        
        return $data;
    }
}
