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
class Controller_Reports extends \Controller_App {

    /**
     * Get list
     */
    public function action_general() {
        return \Bus\Reports_General::getInstance()->execute();
    }
}
