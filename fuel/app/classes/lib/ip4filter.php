<?php

/*
 * http://www.php.net/manual/en/function.ip2long.php#82397
 * https://gist.github.com/tott/7684443
 */

namespace Lib;

class Ip4filter {

    private static $_IP_TYPE_SINGLE = 'single';
    private static $_IP_TYPE_WILDCARD = 'wildcard';
    private static $_IP_TYPE_MASK = 'mask';
    private static $_IP_TYPE_CIDR = 'CIDR';
    private static $_IP_TYPE_SECTION = 'section';

    /**
     * Check IP in range
     *
     * @param string $ip
     * @param array $ipsCheck
     * @return boolean
     */
    public static function check($ip, $ipsCheck) {
        if (empty($ip) || !self::_validIp($ip) || empty($ipsCheck)) {
            return false;
        }

        if (!is_array($ipsCheck)) {
            $ipsCheck = array($ipsCheck);
        }

        foreach ($ipsCheck as $ipCheck) {
            $type = self::_getIpType($ipCheck);

            switch ($type) {
                case self::$_IP_TYPE_SINGLE:
                    $check = self::_checkerSingle($ipCheck, $ip);
                    break;
                case self::$_IP_TYPE_WILDCARD:
                    $check = self::_checkerWildcard($ipCheck, $ip);
                    break;
                case self::$_IP_TYPE_MASK:
                    $check = self::_checkerMask($ipCheck, $ip);
                    break;
                case self::$_IP_TYPE_SECTION:
                    $check = self::_checkerSection($ipCheck, $ip);
                    break;
                case self::$_IP_TYPE_CIDR:
                    $check = self::_checkerCidr($ipCheck, $ip);
                    break;
                default:
                    $check = false;
                    break;
            }

            if ($check) {
                return true;
            }
        }
        return false;
    }

    /**
     * Get IP type
     *
     * @param string $ip
     * @return boolean | enum
     */
    private static function _getIpType($ip) {
        if (empty($ip)) {
            return false;
        }

        if (strpos($ip, '*')) {
            return self :: $_IP_TYPE_WILDCARD;
        }

        if (strpos($ip, '/')) {
            $tmp = explode('/', $ip);
            if (strpos($tmp[1], '.')) {
                return self :: $_IP_TYPE_MASK;
            } else {
                return self :: $_IP_TYPE_CIDR;
            }
        }

        if (strpos($ip, '-')) {
            return self :: $_IP_TYPE_SECTION;
        }

        if (ip2long($ip)) {
            return self :: $_IP_TYPE_SINGLE;
        }

        return false;
    }

    /**
     * IP format : xxx.xxx.xxx.xxx
     *
     * @param x.x.x.x $ipCheck
     * @param x.x.x.x $ip
     * @return boolean
     */
    private static function _checkerSingle($ipCheck, $ip) {
        if (!self::_validIp($ipCheck)) {
            return false;
        }
        return (ip2long($ipCheck) == ip2long($ip));
    }

    /**
     * Wildcard format : xxx.xxx.*.*
     *
     * @param x.x.x.* $ipCheck
     * @param x.x.x.x $ip
     * @return boolean
     */
    private static function _checkerWildcard($ipCheck, $ip) {
        if (!self::_validIp($ipCheck)) {
            return false;
        }
        $ipCheckArr = explode('.', $ipCheck);
        $ipArr = explode('.', $ip);

        for ($i = 0; $i < count($ipCheckArr); $i++) {
            if ($ipCheckArr[$i] == '*') {
                // OK
            } else {
                if (false == ($ipCheckArr[$i] == $ipArr[$i])) {
                    return false;
                }
            }
        }
        return true;
    }

    /**
     * ?
     *
     * @param ? $ipCheck
     * @param x.x.x.x $ip
     * @return boolean
     */
    private static function _checkerMask($ipCheck, $ip) {
        list($ipCheck, $ipCheckMask) = explode('/', $ipCheck);
        $begin = (ip2long($ipCheck) & ip2long($ipCheckMask)) + 1;
        $end = (ip2long($ipCheck) | (~ ip2long($ipCheckMask))) + 1;
        $ipDecimal = ip2long($ip);
        return ($ipDecimal >= $begin && $ipDecimal <= $end);
    }

    /**
     * IP range format : xxx.xxx.xxx.xxx-yyy.yyy.yyy.yyy
     *
     * @param x.x.x.x-y.y.y.y $ipCheck
     * @param x.x.x.x $ip
     * @return boolean
     */
    private static function _checkerSection($ipCheck, $ip) {
        list($begin, $end) = explode('-', $ipCheck);
        if (!self::_validIp($begin) || !self::_validIp($end)) {
            return false;
        }
        $beginDecimal = ip2long($begin);
        $endDecimal = ip2long($end);
        $ipDecimal = ip2long($ip);
        return ($ipDecimal >= $beginDecimal && $ipDecimal <= $endDecimal);
    }

    /**
     * CIDR format : xxx.xxx.xxx.xxx/yy
     *
     * @param x.x.x.x/y $Cidp
     * @param x.x.x.x $ip
     * @return boolean
     */
    private static function _checkerCidr($Cidp, $ip) {
        //list ($net, $mask) = explode('/', $CIDR);
        //return ( ip2long($ip) & ~((1 << (32 - $mask)) - 1) ) == ip2long($net);
        // https://gist.github.com/tott/7684443
        list($range, $netmask) = explode('/', $Cidp, 2);
        if (!self::_validIp($range)) {
            return false;
        }
        $rangeDecimal = ip2long($range);
        $ipDecimal = ip2long($ip);
        $wildcardDecimal = pow(2, ( 32 - $netmask)) - 1;
        $netmaskDecimal = ~ $wildcardDecimal;
        return ( ( $ipDecimal & $netmaskDecimal ) == ( $rangeDecimal & $netmaskDecimal ) );
    }

    /**
     * Validate IP address
     *
     * @param string $ip
     * @return boolean
     */
    private static function _validIp($ip) {
        if (empty($ip)) {
            return false;
        }

        $ipItems = explode('.', $ip);
        if (count($ipItems) != 4) {
            return false;
        }

        foreach ($ipItems as $ipItem) {
            if (!is_numeric($ipItem) && $ipItem != '*') {
                return false;
            } else if ($ipItem > 255 || $ipItem < 0) {
                return false;
            }
        }

        return true;
    }

    /**
     * Get Client Ip
     * @return string
     */
    public static function getClientIp() {
        $ipaddress = '';
        if (getenv('HTTP_CLIENT_IP')) {
            $ipaddress = getenv('HTTP_CLIENT_IP');
        } else if (getenv('HTTP_X_FORWARDED_FOR')) {
            $ipaddress = getenv('HTTP_X_FORWARDED_FOR');
        } else if (getenv('HTTP_X_FORWARDED')) {
            $ipaddress = getenv('HTTP_X_FORWARDED');
        } else if (getenv('HTTP_FORWARDED_FOR')) {
            $ipaddress = getenv('HTTP_FORWARDED_FOR');
        } else if (getenv('HTTP_FORWARDED')) {
            $ipaddress = getenv('HTTP_FORWARDED');
        } else if (getenv('REMOTE_ADDR')) {
            $ipaddress = getenv('REMOTE_ADDR');
        } else {
            $ipaddress = 'UNKNOWN';
        }
        return $ipaddress;
    }

}
