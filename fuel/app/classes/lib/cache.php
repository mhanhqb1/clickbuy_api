<?php

/**
 * Support functions for cache
 *
 * @package Lib
 * @created 2014-11-25
 * @version 1.0
 * @author thailh
 * @copyright Oceanize INC
 */

namespace Lib;

class Cache {

    const CACHE_PREFIX = 'api_';
    const CACHE_DEFAULT_SECONDS = 3600; // seconds   
    const CACHE_OFF = false;

    public static $_cache = NULL;
    public static $_cache_key = NULL;

    /**
     * Init cache
     *
     * @author      thailh 
     * @return      object Cache object
     */
    public static function _init() {
        return self::$_cache = new \Cache();
    }

    /**
     * Method seconds - get seconds for cache   
     *  
     * @author thailh
     * @param int $id Id of cache
     * @param int $seconds Time for cache
     * @return int Seconds for cache
     */
    public static function seconds($id, $seconds = null) {
        $key = \Config::get('cache.key');
        if (empty($seconds)) {
            $seconds = self::CACHE_DEFAULT_SECONDS;
        }
        return !empty($key[$id]) ? $key[$id] : $seconds;
    }

    /**
     * Method _create_key - create key for cache   
     *  
     * @author thailh
     * @param int $id Id of cache
     * @return string Key of cache
     */
    public static function _create_key($id) {
        return self::CACHE_PREFIX . $id;
    }

    /**
     * Method get - get data for cache   
     *  
     * @author thailh
     * @param int $id Id of cache
     * @return object|bool Object (array, string, int, ...) if successful ortherwise return false
     */
    public static function get($id = null) {
        if (self::CACHE_OFF || empty($id)) {
            return false;
        }
        try {
            $result = self::$_cache->get(self::_create_key($id));
            return $result;
        } catch (\CacheNotFoundException $e) {
            // cache not found            
        }
        return false;
    }

    /**
     * Method set - set data for cache   
     *  
     * @author thailh
     * @param int $id Id of cache
     * @param string/int/array $data Data for cache
     * @param int $seconds Time for cache
     * @return bool Return true if successful ortherwise return false
     */
    public static function set($id, $data, $seconds = null) {
        if (self::CACHE_OFF) {
            return true;
        }
        return self::$_cache->set(self::_create_key($id), $data, self::seconds($id, $seconds));
    }

    /**
     * Method delete - delete cache   
     *  
     * @author thailh
     * @param int $id Id of cache
     * @return bool Return true if successful ortherwise return false
     */
    public static function delete($id) {
        if (self::CACHE_OFF) {
            return true;
        }
        return self::$_cache->delete(self::_create_key($id));
    }

}
