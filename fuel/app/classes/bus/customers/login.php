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
class Customers_Login extends BusAbstract {

    // check require
    protected $_required = array(
        'account',
        'password'
    );
    
    // check length
    protected $_length = array(
        'account' => array(0, 40),
        'password' => array(0, 40)
    );
    
    /**
     * Login action
     */
    public function operateDB($data) {
        try {
            $this->_response = \Model_Customer::login($data);
            return $this->result(\Model_Customer::error());
        } catch (\Exception $e) {
            $this->_exception = $e;
        }
        return false;
    }

}
