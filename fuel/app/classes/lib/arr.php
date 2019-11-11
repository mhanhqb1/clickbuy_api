<?php

/**
 * class Arr - support functions for array
 *
 * @package Lib
 * @created 2014-11-25
 * @version 1.0
 * @author thailh
 * @copyright Oceanize INC
 */

namespace Lib;

class Arr {

    /**
     * Method key_value - filter array with key and value   
     *  
     * @author thailh
     * @param array $arr Array need to filter
     * @param string $key Key to filter
     * @param string $value Value to filter
     * @return array Array after filtering
     */
    public static function key_value($arr, $key, $value) {
        $result = array();
        if ($arr) {
            foreach ($arr as $item) {
                $result[$item[$key]] = $item[$value];
            }
        }
        return $result;
    }

    /**
     * Method key_values - filter array with key   
     *  
     * @author thailh
     * @param array $arr Array need to filter
     * @param string $key Key to filter
     * @return array Array after filtering
     */
    public static function key_values($arr, $key) {
        $result = array();
        if ($arr) {
            foreach ($arr as $item) {
                $result[$item[$key]] = $item;
            }
        }
        return $result;
    }

    /**
     * Method field - filter array by field   
     *  
     * @author thailh
     * @param array $arr Array need to filter
     * @param string $field Field need to filter
     * @param bool $toString If true will return string, otherwise return an array
     * @return array/string Array/String after filtering
     */
    public static function field($arr, $field, $toString = false) {
        if (empty($arr)) {
            return array();
        }
        $result = array();
        if ($arr) {
            foreach ($arr as $item) {
                $result[] = $item[$field];
            }
        }
        $result = array_unique($result);
        if ($toString) {
            $result = implode(',', $result);
        }
        return $result;
    }

    /**
     * Method filter - filter array by field and value   
     *  
     * @author thailh
     * @param array $arr Array need to filter
     * @param string $field Field need to filter
     * @param string $value Value need to filter
     * @param bool $count If true will return array which including count number
     * @return array Array after filtering
     */
    public static function filter($arr, $field, $value, $count = false, $keepKey = true) {
        $result = array();
        $lenght = 0;
        if ($arr) {
            if ($keepKey) {
                foreach ($arr as $key => $item) {
                    if ($item[$field] == $value) {
                        $result[$key] = $item;
                        $lenght++;
                    }
                }
            } else {
                foreach ($arr as $item) {
                    if ($item[$field] == $value) {
                        $result[] = $item;
                        $lenght++;
                    }
                }
            }
        }
        if ($count) {
            return array($lenght, $result);
        }
        return $result;
    }

    /**
     * Method search - check if found an array by field and value   
     *  
     * @author thailh
     * @param array $arr Array need to search
     * @param string $field Field need to filter
     * @param string $value Value need to filter
     * @return bool
     */
    public static function search($arr, $field, $value) {
        return !empty(static::filter($arr, $field, $value)) ? true : false;
    }

    /**
     * Method count - count if found an array by field and value   
     *  
     * @author thailh
     * @param array $arr Array need to count
     * @param string $field Field to filter
     * @param string $value Value to filter
     * @return int
     */
    public static function count($arr, $field, $value) {
        $result = static::filter($arr, $field, $value);
        return !empty($result) ? count($result) : 0;
    }

    /**
     * Convert array to value array
     *    
     * @author thailvn
     * @param array $arr 2D input array
     * @param string $key Field key  
     * @return array  
     */
    public static function arrayValues($arr, $key) {
        $result = array();
        if ($arr) {
            foreach ($arr as $item) {
                $result[] = $item[$key];
            }
        }
        return $result;
    }

    /**
     * Method filter - filter array by field and value   
     *  
     * @author thailh
     * @param array $arr Array need to filter
     * @param string $field Field need to filter
     * @param string $value Value need to filter
     * @param bool $count If true will return array which including count number
     * @return array Array after filtering
     */
    public static function multi_search($arr, $arrayCheck) {
        $result = array();
        $lenght = 0;
        if ($arr) {
            foreach ($arr as $item) {
                $lenght = 0;
                foreach ($arrayCheck as $key => $value) {
                    if ($item[$key] == $value) {
                        $lenght++;
                    }
                }
                if ($lenght == sizeof($arrayCheck)) {
                    return true;
                }
            }
        }
        return false;
    }

    public static function rand($arr, $num = 0) {
        if (empty($arr)) {
            return array();
        }
        if ($num == 0) {
            $num = count($arr);
        }
        if (count($arr) <= $num) {
            return $arr;
        }
        $keys = array_keys($arr);
        shuffle($keys);
        $r = array();
        for ($i = 0; $i < $num; $i++) {
            $r[] = $arr[$keys[$i]];
        }
        return $r;
    }

    public static function array_sort($array, $on, $order = SORT_ASC) {
        $new_array = array();
        $sortable_array = array();

        if (count($array) > 0) {
            foreach ($array as $k => $v) {
                if (is_array($v)) {
                    foreach ($v as $k2 => $v2) {
                        if ($k2 == $on) {
                            $sortable_array[$k] = $v2;
                        }
                    }
                } else {
                    $sortable_array[$k] = $v;
                }
            }

            switch ($order) {
                case SORT_ASC:
                    asort($sortable_array);
                    break;
                case SORT_DESC:
                    arsort($sortable_array);
                    break;
            }

            foreach ($sortable_array as $k => $v) {
                $new_array[$k] = $array[$k];
            }
        }

        return $new_array;
    }

}
