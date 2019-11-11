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
class Controller_Companies extends \Controller_App {
    
    /**
     * Get list
     */
    public function action_addupdate() {
        return \Bus\Companies_AddUpdate::getInstance()->execute();
    }
    
    /**
     * Get list
     */
    public function action_detail() {
        return \Bus\Companies_Detail::getInstance()->execute();
    }
}
