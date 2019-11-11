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
class Controller_Settings extends \Controller_App {

    /**
     * Get list
     */
    public function action_general() {
        return \Bus\Settings_General::getInstance()->execute();
    }
    
    /**
     * Get home data
     */
    public function action_gethomedata() {
        return \Bus\Settings_Gethomedata::getInstance()->execute();
    }
}
