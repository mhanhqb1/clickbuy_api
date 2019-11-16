<?php

namespace Bus;

/**
 * Enable/Disable
 *
 * @package Bus
 * @created 2017-10-29
 * @version 1.0
 * @author AnhMH
 */
class Withdraws_UpdateStatus extends BusAbstract
{
    /** @var array $_required field require */
    protected $_required = array(
        'id',
        'status'
    );

    /** @var array $_length Length of fields */
    protected $_length = array(
        
    );

    /** @var array $_email_format field email */
    protected $_email_format = array(
        
    );

    /**
     * Call function disable() from model Order
     *
     * @author AnhMH
     * @param array $data Input data
     * @return bool Success or otherwise
     */
    public function operateDB($data)
    {
        try {
            $this->_response = \Model_Withdraw::update_status($data);
            return $this->result(\Model_Withdraw::error());
        } catch (\Exception $e) {
            $this->_exception = $e;
        }
        return false;
    }
}
