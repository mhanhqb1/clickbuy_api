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
class Controller_Posts extends \Controller_App {

    /**
     * Get list
     */
    public function action_list() {
        return \Bus\Posts_List::getInstance()->execute();
    }
    
    /**
     * Get list
     */
    public function action_addupdate() {
        return \Bus\Posts_AddUpdate::getInstance()->execute();
    }
    
    /**
     * Get list
     */
    public function action_detail() {
        return \Bus\Posts_Detail::getInstance()->execute();
    }
    
    /**
     * Disable
     */
    public function action_disable() {
        return \Bus\Posts_Disable::getInstance()->execute();
    }
    
    /**
     * Disable
     */
    public function action_all() {
        return \Bus\Posts_All::getInstance()->execute();
    }
    
    /**
     * Get home data
     */
    public function action_gethomedata() {
        return \Bus\Posts_GetHomeData::getInstance()->execute();
    }
}
