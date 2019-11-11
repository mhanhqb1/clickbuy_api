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
class Controller_Customers extends \Controller_App {

    /**
     * Get list
     */
    public function action_list() {
        return \Bus\Customers_List::getInstance()->execute();
    }
    
    /**
     * Get list
     */
    public function action_addupdate() {
        return \Bus\Customers_AddUpdate::getInstance()->execute();
    }
    
    /**
     * Get list
     */
    public function action_detail() {
        return \Bus\Customers_Detail::getInstance()->execute();
    }
    
    /**
     * Disable
     */
    public function action_disable() {
        return \Bus\Customers_Disable::getInstance()->execute();
    }
    
    /**
     * Disable
     */
    public function action_all() {
        return \Bus\Customers_All::getInstance()->execute();
    }
}
