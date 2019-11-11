<?php

use Fuel\Core\DB;

/**
 * Any query in Model Version
 *
 * @package Model
 * @created 2016-08-31
 * @version 1.0
 * @author KienNH
 * @copyright Oceanize INC
 */
class Model_Version extends Model_Abstract {

    /** @var array $_properties field of table */
    protected static $_properties = array(
        
    );
    
    protected static $_observers = array(
        'Orm\Observer_CreatedAt' => array(
            'events' => array('before_insert'),
            'mysql_timestamp' => false,
        ),
        'Orm\Observer_UpdatedAt' => array(
            'events' => array('before_update'),
            'mysql_timestamp' => false,
        ),
    );

    /** @var array $_table_name name of table */
    protected static $_table_name = 'versions';

    /**
     * 
     * @param type $param
     * @return boolean
     */
    public static function check($param) {
        return array(
            'latest_version_code' => 1,
            'version_name' => '1.0.0',
            'force_update' => 0,
            'app_store_url' => 'aaaa'
        );
    }
    
}
