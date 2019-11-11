<?php

/**
 * Part of the Fuel framework.
 *
 * @package    Lib
 * @version    1.7
 * @author     Fuel Development Team
 * @license    MIT License
 * @copyright  2010 - 2013 Fuel Development Team
 * @link       http://fuelphp.com
 */

namespace Lib;

/**
 * Exception when no permission access a page
 */
class HttpPermissionDeniedException extends \HttpException {

    public function response() {
        return new \Response(\View::forge('403'), 403);
    }

}
