<?php

/**
 * Controller for actions on articles
 *
 * @package Controller
 * @created 2018-03-02
 * @version 1.0
 * @author AnhMH
 * @copyright Oceanize INC
 */
class Controller_Withdraws extends \Controller_App {

    /**
     * Get list
     */
    public function action_list() {
        return \Bus\Withdraws_List::getInstance()->execute();
    }
    
    /**
     * Get list
     */
    public function action_request() {
        return \Bus\Withdraws_Request::getInstance()->execute();
    }
    
    /**
     * Get list
     */
    public function action_updatestatus() {
        return \Bus\Withdraws_UpdateStatus::getInstance()->execute();
    }
}
