<?php

namespace Bus;

/**
 * Login for admin
 *
 * @package Bus
 * @created 2018-03-02
 * @version 1.0
 * @author AnhMH
 * @copyright Oceanize INC
 */
class Admins_Login extends BusAbstract {

    // check require
    protected $_required = array(
        'email',
        'password'
    );
    
    // check length
    protected $_length = array(
        'email' => array(0, 40),
        'password' => array(0, 40)
    );
    
    /**
     * Login action
     */
    public function operateDB($data) {
        try {
            $result = \Model_Admin::login($data);
            if (!empty($result)) {
                $result['token'] = \Model_Authenticate::addupdate(array(
                        'user_id' => $result['id'],
                        'regist_type' => 'admin'
                ));
                $this->_response = $result;
            } else {
                \Model_Admin::errorNotExist('admin_infomation', 'information_of_admin');
                $this->_response = false;
            }
            return $this->result(\Model_Admin::error());
        } catch (\Exception $e) {
            $this->_exception = $e;
        }
        return false;
    }

}
