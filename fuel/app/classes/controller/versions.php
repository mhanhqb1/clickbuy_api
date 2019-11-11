<?php

/**
 * Controller for actions on Version
 *
 * @package Controller
 * @created 2016-08-31
 * @version 1.0
 * @author KienNH
 * @copyright Oceanize INC
 */
class Controller_Versions extends \Controller_App {

    /**
     * Check current version for updating app
     */
    public function action_check() {
        return \Bus\Versions_Check::getInstance()->execute();
    }

}
