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
class Controller_Cates extends \Controller_App {

    /**
     * Get list
     */
    public function action_list() {
        return \Bus\Cates_List::getInstance()->execute();
    }
    
    /**
     * Get list
     */
    public function action_addupdate() {
        return \Bus\Cates_AddUpdate::getInstance()->execute();
    }
    
    /**
     * Get list
     */
    public function action_detail() {
        return \Bus\Cates_Detail::getInstance()->execute();
    }
    
    /**
     * Get all
     */
    public function action_all() {
        return \Bus\Cates_All::getInstance()->execute();
    }
    
    /**
     * Disable
     */
    public function action_disable() {
        return \Bus\Cates_Disable::getInstance()->execute();
    }
}
