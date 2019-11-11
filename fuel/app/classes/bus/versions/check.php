<?php

namespace Bus;

/**
 * Add and update info for User
 *
 * @package Bus
 * @created 2016-07-06
 * @version 1.0
 * @author KienNH
 * @copyright Oceanize INC
 */
class Versions_Check extends BusAbstract {

    public function operateDB($data) {
        try {
            $this->_response = \Model_Version::check($data);
            return $this->result(\Model_Version::error());
        } catch (\Exception $e) {
            $this->_exception = $e;
        }
        return false;
    }

}
