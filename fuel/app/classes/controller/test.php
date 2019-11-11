<?php

/**
 * Controller_Test
 *
 * @package Controller
 * @created 2016-08-31
 * @version 1.0
 * @author KienNH
 * @copyright Oceanize INC
 */
class Controller_Test extends \Controller_Rest {

    /**
     * 
     */
    public function action_index() {
        $param = array(
            'name' => 'test',
            'phone' => '12345',
            'address' => 'sadasdsa',
            'detail' => '[{"id":"4","url":"sua-duong-the-lam-sang-da-grapefruits-body-lotion","image":"http:\/\/img.lyonabeauty.localhost\/2019\/04\/28\/89ecc0c9b1a5d0c78d204d24ef422b5e.jpg","name":"S\u1eefa D\u01b0\u1ee1ng Th\u1ec3 L\u00e0m S\u00e1ng Da GRAPEFRUITS BODY LOTION","price":23123000,"qty":"10"},{"id":"3","url":"sua-duong-the-chong-lao-hoa-raspberry-body-lotion","image":"http:\/\/img.lyonabeauty.localhost\/2019\/04\/28\/7a2c1159fb960f1b6419163e57218878.jpg","name":"S\u1eefa D\u01b0\u1ee1ng Th\u1ec3 Ch\u1ed1ng L\u00e3o H\u00f3a RASPBERRY BODY LOTION","price":264000,"qty":"11"},{"id":"2","url":"san-pham-2","image":"http:\/\/img.lyonabeauty.localhost\/2019\/04\/26\/11c4c69616e88c82df0441925fd4f8a9.png","name":"san pham 2","price":203,"qty":"12"}]'
        );
        $param['detail'] = !empty($param['detail']) ? json_decode($param['detail'], true) : array();
        
        $mail = \Lib\Email::sendOrderEmail($param);
        echo '<pre>';
        print_r($mail);
        die();
        $url = 'http://conlatatca.vn/be-1-thang-tuoi/nhiet-do-nuoc-tam-cho-be-bao-nhieu-la-chuan/';
        $className = 'DetailContent';//'Detail-title';
        $element = 'div';//'h1';
        $data = \Lib\Util::getPageData($url, $element, $className);
        
        echo '<pre>';
        print_r($data);
        die();
        echo date('Y-m-d H:i:s');
        echo '<br/>';
        echo date_default_timezone_get();
        exit;
    }
    
    /**
     * Show PHP info
     */
    public function action_phpinfo() {
        phpinfo();
        exit;
    }
      /**
     *  
     * @return boolean Action Conf of TestController
     */
    public function action_conf($config = 'upload') {
        include_once APPPATH . "/config/auth.php";
        echo '<pre>';
        print_r( \Config::load($config, true));
        echo '</pre>';
    }
    
    /**
     * Test mail
     */
    public function action_mail() {
        if (empty($_GET['to'])) {
            die('Missing TO address: ?to=xxx@yyy.zzz');
        }
        echo !extension_loaded('openssl')? "openssl not available" : "openssl available";
        $to = $_GET['to'];
        $email = \Email::forge('jis');
        
        echo '<pre>';
        print_r($email->config['phpmailer']);
        echo '</pre>';
        
        $email->from(Config::get('system_email.noreply'), 'SmartTablet No reply');
        $email->subject('[SmartTablet test SMTP]Subject');
        $email->html_body('[SmartTablet test SMTP]Body');
        $email->to($to);
        try {
            if ($email->send()) {
                echo 'OK';
            } else {
                echo 'NG';
            }
        } catch (\EmailSendingFailedException $e) {
            echo '<pre>';
            print_r($e);
            echo '</pre>';
        } catch (\EmailValidationFailedException $e) {
    		echo '<pre>';
            print_r($e);
            echo '</pre>';
    	} catch (Exception $e) {
            echo '<pre>';
            print_r($e);
            echo '</pre>';
        }
    }
}
