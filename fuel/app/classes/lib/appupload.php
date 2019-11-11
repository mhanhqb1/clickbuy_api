<?php

/**
 * Support functions for uploading
 *
 * @package Lib
 * @created 2014-11-25
 * @version 1.0
 * @author thailh
 * @copyright Oceanize INC
 */

namespace Lib;

class AppUpload extends \Upload {

    /**
     * Do something before uploading  
     *  
     * @author thailh
     */
    public static function _init() {
        // get the language file for this upload
        \Lang::load('upload', true);

        // get the config for this upload
        \Config::load('upload', true);

        // fetch the config
        $config = \Config::get('upload', array());

        // add the language callback to link into Fuel's Lang class
        $config['langCallback'] = '\\Upload::lang_callback';

        // get an upload instance
        if (class_exists('Fuel\Upload\Upload')) {
            static::$upload = new \Fuel\Upload\Upload($config);
        }

        // 1.6.1 fallback
        elseif (class_exists('FuelPHP\Upload\Upload')) {
            static::$upload = new \FuelPHP\Upload\Upload($config);
        } else {
            throw new \FuelException('Can not load \Fuel\Upload\Upload. Did you run composer to install it?');
        }

        // if auto-process is not enabled, load the uploaded files
        // thailh: fix upload no auto_process
        if (!$config['auto_process']) {
            if (!isset($config['selection'])) {
                static::$upload->processFiles();
            } else {
                static::$upload->processFiles($config['selection']);
            }
        }
    }

}
