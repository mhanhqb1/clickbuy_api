<?php

/**
 * Support functions for Email
 *
 * @package Lib
 * @created 2014-11-25
 * @version 1.0
 * @author thailh
 * @copyright Oceanize INC
 */

namespace Lib;

use Fuel\Core\Config;

class Email {
    
    public static $sender = 'LyonaBeauty';

    public static function beforeSend() {
        $send = Config::get('send_email', true);
        if ($send == false) {
            return false;
        }
        
        // Has config?
        $email = \Email::forge('jis');
        $config_raw = $email->config;
        if (empty($config_raw[$config_raw['driver']])) {
            return false;
        }
        
        // Valid config?
        $config = $config_raw[$config_raw['driver']];
        if (empty($config['Host']) || empty($config['Username']) || empty($config['Password'])) {
            return false;
        }
        
        // Setting from address?
        if (empty(Config::get('system_email.noreply'))) {
            return false;
        }
        
        return true;
    }

    /**
     * Send email to test email (For testing)
     *
     * @author thailh
     * @param string email Email
     * @return string Real email | test email
     */
    public static function to($email) {
        $test_email = Config::get('test_email', '');
        return !empty($test_email) ? $test_email : $email;
    }
    
    /**
     * Send test
     *
     * @author thailh
     * @param array $param Information for sending email
     * @return bool Return true if successful ortherwise return false
     */
    public static function sendTest($param) {
        if (self::beforeSend() == false) {
            return true;
        }
        $to = !empty($param['to']) ? $param['to'] : '';
        if (empty($to)) {
            \LogLib::warning('Email is null or empty', __METHOD__, $to);
            return false;
        }
        $email = \Email::forge('jis');
        $email->from(Config::get('system_email.noreply'), '[Test] Bmaps No reply');
        $email->subject('Test at ' . date('Y-m-d H:i'));
        $body = 'This is message that sent from Bmaps.world.<br/><br/>';
        $email->html_body($body);
        $email->to(self::to($to));
        try {
            \LogLib::info("Resent email to {$to}", __METHOD__, $param);
            return $email->send();
        } catch (\EmailSendingFailedException $e) {
            \LogLib::warning($e, __METHOD__, $param);
            return false;
        } catch (\EmailValidationFailedException $e) {
            \LogLib::warning($e, __METHOD__, $param);
            return false;
        }
    }
    
    /**
     * Send register email
     *
     * @author AnhMH
     * @param array $param Information for sending email
     * @return bool Return true if successful ortherwise return false
     */
    public static function sendRegisterEmail($param) {
        if (self::beforeSend() == false) {
            return true;
        }
        $subject = '会員登録頂きまして誠に有難うございました。';
        $view = 'email/register';
    	$to = $param['email'];
        $baseUrl = \Uri::create('');
        
    	$email = \Email::forge('jis');
    	$email->from(Config::get('system_email.noreply'), self::$sender);
    	$email->subject($subject);
        $param['url']= $baseUrl . "users/active/{$param['token']}";
    	$body = \View::forge($view, $param);
    	$email->html_body($body);
    	$email->to(self::to($to));
        $ok = 0;
    	try {
            \LogLib::info("Sent email to {$to}", __METHOD__, $param);
            if ($email->send()) {
                $ok = 1;
            }
    	} catch (\EmailSendingFailedException $e) {
            \LogLib::warning($e, __METHOD__, $param);
    	} catch (\EmailValidationFailedException $e) {
            \LogLib::warning($e, __METHOD__, $param);
    	}
        return (boolean) $ok;
    }
    
    /**
     * Send order email
     *
     * @author AnhMH
     * @param array $param Information for sending email
     * @return bool Return true if successful ortherwise return false
     */
    public static function sendOrderEmail($param) {
        if (self::beforeSend() == false) {
            return true;
        }
        $subject = 'Đơn hàng từ - '.$param['name'].' - '.$param['phone'];
        $view = 'email/order';
        $company = \Model_Company::find('first');
    	$to = !empty($company['email']) ? $company['email'] : '';
        
        if (empty($to)) {
            return false;
        }
        
    	$email = \Email::forge('jis');
    	$email->from(Config::get('system_email.noreply'), self::$sender);
    	$email->subject($subject);
    	$body = \View::forge($view, $param);
    	$email->html_body($body);
    	$email->to(self::to($to));
        $ok = 0;
    	try {
            \LogLib::info("Sent email to {$to}", __METHOD__, $param);
            if ($email->send()) {
                $ok = 1;
            }
    	} catch (\EmailSendingFailedException $e) {
            \LogLib::warning($e, __METHOD__, $param);
    	} catch (\EmailValidationFailedException $e) {
            \LogLib::warning($e, __METHOD__, $param);
    	}
        return (boolean) $ok;
    }
    
}
