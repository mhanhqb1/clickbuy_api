<?php

use Orm\Model;
use Lib\Cache;

/**
 * Model_Abstract - Model to create common functions or constants.
 *
 * @package Model
 * @version 1.0
 * @author thailh
 * @copyright Oceanize INC
 */
class Model_Abstract extends Model {

    public $disable = 0;
    public static $error_code_validation = array();

    const ERROR_CODE_INVALED_PARAMETER = 400;
    const ERROR_CODE_AUTH_ERROR = 403;
    const ERROR_CODE_PERMISSION_ERROR = 403;
    const ERROR_CODE_FIELD_NOT_EXIST = 1010;
    const ERROR_CODE_FIELD_DUPLICATE = 1011;
    const ERROR_CODE_EMAIL_NOT_EXIST = 1012;
    const ERROR_CODE_OTHER_1 = 1021;
    const ERROR_CODE_OTHER_2 = 1022;
    const ERROR_CODE_OTHER_3 = 1023;
    const ERROR_CODE_OTHER_4 = 1024;
    const ERROR_CODE_OTHER_5 = 1025;

    public static $slave_db = 'default'; // KienNH 2016/04/08: Default if slave not setted

    /**
     * <init - function to inital properties>   
     *
     * @author thailh 
     */

    public static function _init() {
        if (\Lib\Util::os() != \Config::get('os')['webos'] && !empty(static::$_mobile_properties)) {
            static::$_properties = static::$_mobile_properties;
        }

        // KienNH 2016/04/08 begin set slave db for reading
        $db_read_use = \Config::get('db_read.use');
        if (!empty($db_read_use)) {
            // Set Slave DB
            if (is_array($db_read_use)) {
                $rand_key = array_rand($db_read_use, 1);
                $slave_db = $db_read_use[$rand_key];
            } else {
                $slave_db = $db_read_use;
            }

            // Load DB config
            if (empty(\Config::get('db'))) {
                \Config::load('db', true);
            }
            $db_config = \Config::get('db');

            // Check Slave has been configured
            if (!empty($db_config[$slave_db])) {
                static::$slave_db = $slave_db;
            } else {
                // Try select other setting
                if (is_array($db_read_use)) {
                    foreach ($db_read_use as $slave_db) {
                        if (!empty($db_config[$slave_db])) {
                            static::$slave_db = $slave_db;
                            break;
                        }
                    }
                }
            }
        }
        // KienNH end
    }

    /**
     * Function to set value for error_code cause INVALED_PARAMETER.
     * @param string $user_id Field of data (or not use this argument).
     * @author s.kino
     */
    public static function isMyUserid($user_id = '') {
        if (\Config::get('authorize') == true && \Lib\Util::authUserId() != $user_id) {
            return false;
        }
        return true;
    }

    /**
     * Function to set value for error_code cause INVALED_PARAMETER.
     * @param string $field Field of data (or not use this argument).
     * @param string $value The value of field.
     * @author thailh 
     */
    public static function errorParamInvalid($field = '', $value = '') {
        static::$error_code_validation[] = array(
            'code' => self::ERROR_CODE_INVALED_PARAMETER,
            'field' => $field,
            'value' => $value,
        );
    }

    /**
     * Function to set value for error_code cause FIELD_NOT_EXIST.
     * @param string $field Field of data.
     * @param string $value The value of field (or not use this argument).
     * @author thailh 
     */
    public static function errorNotExist($field, $value = '') {
        static::$error_code_validation[] = array(
            'code' => self::ERROR_CODE_FIELD_NOT_EXIST,
            'field' => $field,
            'value' => $value,
        );
    }

    /**
     * Function to set value for error_code cause FIELD_DUPLICATE.
     * @param string $field Field of data.
     * @param string $value The value of field (or not use this argument).
     * @author thailh 
     */
    public static function errorDuplicate($field, $value = '') {
        static::$error_code_validation[] = array(
            'code' => self::ERROR_CODE_FIELD_DUPLICATE,
            'field' => $field,
            'value' => $value,
        );
    }

    /**
     * Function to set value for error_code cause PERMISSION_ERROR.
     * @param string $field Field of data (or not use this argument).
     * @param string $value The value of field.
     * @author thailh 
     */
    public static function errorPermission($field = '', $value = '') {
        static::$error_code_validation[] = array(
            'code' => self::ERROR_CODE_PERMISSION_ERROR,
            'field' => $field,
            'value' => $value,
        );
    }

    /**
     * Function to set value for error_code cause others.
     * @param string $code Input code.
     * @param string $field Field of data (or not use this argument).
     * @param string $value The value of field (or not use this argument).
     * @author thailh 
     */
    public static function errorOther($code, $field = null, $value = '') {
        static::$error_code_validation[] = array(
            'code' => $code,
            'field' => $field,
            'value' => $value,
        );
    }

    public static function setError($error) {
        static::$error_code_validation = $error;
    }

    /**
     * Function to set value for error_code_validation.
     *
     * @author thailh 
     * @return array Returns the array.
     */
    public static function error($reset = false) {
        $errors = static::$error_code_validation;
        if ($reset === true) {
            static::$error_code_validation = array();
        }
        return $errors;
    }

    /**
     * Function to format date.
     * @param int $date Input date.
     * @author thailh 
     * @return int Returns integer.
     */
    public static function date_from_val($date) {
        return strtotime($date);
    }

    /**
     * Function to format date time.
     * @param int $date Input date.
     * @author thailh 
     * @return int Returns the integer.
     */
    public static function date_to_val($date) {
        return strtotime($date . '23:59:59');
    }

    /**
     * Function to format date time.
     *
     * @param string $time Input time.
     * @author Le Tuan Tu
     * @return int Returns the integer.
     */
    public static function time_to_val($time) {
        return strtotime($time);
    }

    /**
     * batchInsert
     *
     * @param string $table Table name
     * @param array $data Data for insert/update
     * @param array $updates Data for update if duplicate keys
     * @param boolean $ignore Ignore duplicate or not
     * @author thailh
     * @return boolean True if success otherwise false
     */
    public static function batchInsert($table, $data, $updates = array(), $ignore = true) {
        if (empty($data)) {
            return false;
        }
        if (empty($data[0])) {
            $data = array($data);
        }
        if (!empty($ignore)) {
            $ignore = 'IGNORE';
        }
        $inserts = $field = array();
        $data = DB::quote($data);
        foreach ($data as $i => $row) {
            $insert = array();
            foreach ($row as $key => $val) {
                if ($i == 0) {
                    $field[] = "`{$key}`";
                }
                $insert[] = $val;
            }
            $inserts[] = "(" . implode(',', $insert) . ")";
        }
        if (!empty($inserts)) {
            $sql = " INSERT {$ignore} INTO {$table}(" . implode(",", $field) . ")";
            $sql .= " VALUES " . implode(",", $inserts);
            if (!empty($updates)) {
                $updates = DB::quote($updates);
                $updateSQL = array();
                foreach ($updates as $field => $value) {
                    $updateSQL[] = "`{$field}`={$value}";
                }
                $sql .= " ON DUPLICATE KEY UPDATE " . implode(",", $updateSQL);
            }
            return DB::query($sql)->execute();
        }
        return false;
    }

    /**
     * deleteRow
     * 
     * @param type $table
     * @param type $condition
     * @author thailh
     * @return boolean True if success otherwise false
     */
    public static function deleteRow($table, $condition = array()) {
        if (empty($table) || empty($condition)) {
            return false;
        }
        $condition = DB::quote($condition);
        $cond = array();
        foreach ($condition as $key => $val) {
            $cond[] = "{$key} = {$val}";
        }
        $cond = implode(' AND ', $cond);
        $sql = "DELETE FROM {$table} WHERE {$cond}";
        return DB::query($sql)->execute();
    }

    /**
     * Check has error
     *
     * @param string $code Error code
     * @author thailh
     * @return boolean True if success otherwise false
     */
    public static function hasError($code, $errors = array()) {
        if (empty($errors)) {
            $errors = self::error();
        }
        if ($errors) {
            foreach ($errors as $error) {
                if ($error['code'] == $code) {
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * 
     * @param string $sort with format field-condition (id-asc)
     * @return boolean
     */
    public static function checkSort($sort, $_properties = array(), $custom_field = array()) {
        if (empty($sort)) {
            return true;
        }

        // Valid format
        $sortExplode = explode('-', $sort);
        if (count($sortExplode) != 2) {
            return false;
        }

        // Build array properties
        if (empty($_properties)) {
            $_properties = static::$_properties;
        } else if (!is_array($_properties)) {
            $_properties = array($_properties);
        }

        if (!empty($custom_field)) {
            if (is_array($custom_field)) {
                $_properties = array_merge($_properties, $custom_field);
            } else {
                $_properties[] = $custom_field;
            }
        }

        // Check valid
        if (!in_array(strtolower($sortExplode[0]), $_properties) ||
                !in_array(strtolower($sortExplode[1]), array('asc', 'desc'))) {
            return false;
        }

        return true;
    }

}
