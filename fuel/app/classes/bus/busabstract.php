<?php

namespace Bus;

use Exception;
use Fuel\Core\Input;
use Fuel\Core\Response;
use Fuel\Core\Config;

/**
 * BusAbstract
 *
 * @package 	Bus
 * @created 	2014-11-17
 * @version     1.0
 * @author      thailh
 * @copyright   Oceanize INC
 */
abstract class BusAbstract {

    /** @var int STATUS_OK */
    const STATUS_OK = 200;

    /** @var int ERROR_CODE_INVALID_JSON */
    const ERROR_CODE_INVALID_JSON = 100;

    /** @var int ERROR_CODE_INVALED_PARAMETER */
    const ERROR_CODE_INVALED_PARAMETER = 400;

    /** @var int ERROR_CODE_DB_ERROR */
    const ERROR_CODE_DB_ERROR = 500;

    /** @var int ERROR_CODE_AUTH_PERMISSION_ERROR */
    const ERROR_CODE_AUTH_PERMISSION_ERROR = 403;

    /** @var int ERROR_CODE_AUTH_ERROR */
    const ERROR_CODE_AUTH_ERROR = 401;

    /** @var int ERROR_CODE_LOGIN_ERROR */
    const ERROR_CODE_LOGIN_ERROR = 403;

    /** @var int ERROR_CODE_FIELD_REQUIRED */
    const ERROR_CODE_FIELD_REQUIRED = 1000;

    /** @var int ERROR_CODE_FIELD_LENGTH_MIN */
    const ERROR_CODE_FIELD_LENGTH_MIN = 1001;

    /** @var int ERROR_CODE_FIELD_LENGTH_MAX */
    const ERROR_CODE_FIELD_LENGTH_MAX = 1002;

    /** @var int ERROR_CODE_FIELD_LENGTH_EXACT */
    const ERROR_CODE_FIELD_LENGTH_EXACT = 1003;

    /** @var int ERROR_CODE_FIELD_FORMAT_DATE */
    const ERROR_CODE_FIELD_FORMAT_DATE = 1004;

    /** @var int ERROR_CODE_FIELD_FORMAT_EMAIL */
    const ERROR_CODE_FIELD_FORMAT_EMAIL = 1005;

    /** @var int ERROR_CODE_FIELD_FORMAT_URL */
    const ERROR_CODE_FIELD_FORMAT_URL = 1006;

    /** @var int ERROR_CODE_FIELD_NUMERIC_MIN */
    const ERROR_CODE_FIELD_NUMERIC_MIN = 1007;

    /** @var int ERROR_CODE_FIELD_NUMERIC_MAX */
    const ERROR_CODE_FIELD_NUMERIC_MAX = 1008;

    /** @var int ERROR_CODE_FIELD_NUMERIC_BETWEEN */
    const ERROR_CODE_FIELD_NUMERIC_BETWEEN = 1009;

    /** @var int ERROR_CODE_FIELD_NOT_EXIST */
    const ERROR_CODE_FIELD_NOT_EXIST = 1010;

    /** @var int ERROR_CODE_FIELD_DUPLICATE */
    const ERROR_CODE_FIELD_DUPLICATE = 1011;

    /** @var int ERROR_CODE_FIELD_FORMAT_NUMBER */
    const ERROR_CODE_FIELD_FORMAT_NUMBER = 1012;

    /** @var int ERROR_CODE_DENIED_ERROR */
    const ERROR_CODE_DENIED_ERROR = 1100;

    /** @var int ERROR_CODE_FIELD_FORMAT_KATAKANA */
    const ERROR_CODE_FIELD_FORMAT_KATAKANA = 1200;

    /** @var int ERROR_CODE_FIELD_FORMAT_PASSWORD */
    const ERROR_CODE_FIELD_FORMAT_PASSWORD = 1201;

    /** @var int ERROR_CODE_DENIED_ERROR */
    const ERROR_CODE_ONLY_N_PARAMETER = 1202;

    /** @var int ERROR_CODE_FIELD_FORMAT_HIRAGANA */
    const ERROR_CODE_FIELD_FORMAT_HIRAGANA = 1203;

    /** @var int ERROR_CODE_FIELD_FORMAT_JAPANNESE */
    const ERROR_CODE_FIELD_FORMAT_JAPANESE = 1204;
    
    /** @var int ERROR_CODE_UNKNOW */
    const ERROR_CODE_UNKNOW = 9999;

    /** @var array Array of error code */
    protected $_error_code = array();

    /** @var array Array of error message */
    protected $_error_message = array(
        self::ERROR_CODE_INVALID_JSON => 'Invalid json format',
        self::ERROR_CODE_INVALED_PARAMETER => 'Invalid parameter',
        self::ERROR_CODE_DB_ERROR => 'Db exception',
        self::ERROR_CODE_AUTH_ERROR => 'Access token is invalid',
        self::ERROR_CODE_LOGIN_ERROR => 'Invalid email or password',
        self::ERROR_CODE_AUTH_PERMISSION_ERROR => 'Permission denied',
    );

    /** @var array Array of validation's error code */
    protected $_error_code_validation = array();

    /** @var array Array of validation's error message */
    protected $_error_message_validation = array(
        self::ERROR_CODE_INVALED_PARAMETER => 'Invalid parameters',
        self::ERROR_CODE_AUTH_ERROR => 'Access token is invalid',
        self::ERROR_CODE_LOGIN_ERROR => 'Invalid email or password',
        self::ERROR_CODE_DB_ERROR => 'Db exception',
        self::ERROR_CODE_AUTH_PERMISSION_ERROR => 'Permission denied',
        self::ERROR_CODE_FIELD_REQUIRED => 'The %s is required and must contain a value',
        self::ERROR_CODE_FIELD_LENGTH_MIN => 'The %s has to contain at least %s characters',
        self::ERROR_CODE_FIELD_LENGTH_MAX => 'The %s may not contain more than %s characters',
        self::ERROR_CODE_FIELD_LENGTH_EXACT => 'The field %s must contain exactly %s characters',
        self::ERROR_CODE_FIELD_FORMAT_DATE => 'The %s must contain a valid formatted date',
        self::ERROR_CODE_FIELD_FORMAT_EMAIL => 'The %s must contain a valid email address',
        self::ERROR_CODE_FIELD_FORMAT_URL => 'The %s must contain a valid URL',
        self::ERROR_CODE_FIELD_FORMAT_NUMBER => 'The %s must contain a valid number',
        self::ERROR_CODE_FIELD_NUMERIC_MIN => 'The minimum numeric value of :label must be %s',
        self::ERROR_CODE_FIELD_NUMERIC_MAX => 'The maximum numeric value of %s must be %s',
        self::ERROR_CODE_FIELD_NUMERIC_BETWEEN => 'The %s may not contain more than %s characters',
        self::ERROR_CODE_FIELD_NOT_EXIST => 'The %s does not exist',
        self::ERROR_CODE_FIELD_DUPLICATE => 'The %s is duplicate data',
        self::ERROR_CODE_DENIED_ERROR => 'The action have been denied by system',
        self::ERROR_CODE_FIELD_FORMAT_KATAKANA => 'The %s must be a katakana string',
        self::ERROR_CODE_FIELD_FORMAT_HIRAGANA => 'The %s must be a hiragana string',
        self::ERROR_CODE_FIELD_FORMAT_JAPANESE => 'The %s must be a japanese string',
        self::ERROR_CODE_FIELD_FORMAT_PASSWORD => 'The %s must contain only alphabet or numeric',
        self::ERROR_CODE_ONLY_N_PARAMETER => 'Input only %s parameters',
    );

    /** @var array Array of output format */
    protected $_formats = array('json', 'php', 'html', 'xml', 'serialize');

    /** @var string Input format method */
    protected $_input_format = 'post';

    /** @var string Output format */
    protected $_output_format = 'json';

    /** @var mixed Success status */
    protected $_success = null;

    /** @var string Invalid parameter */
    protected $_invalid_parameter;

    /** @var mixed Exception */
    protected $_exception = null;

    /** @var array Array default value */
    protected $_default_value = array();

    /** @var array Array of required parameters */
    protected $_required = array();

    /** @var array Array of parameter's length */
    protected $_length = array();

    /** @var array Array of parameter's url format */
    protected $_url_format = array();

    /** @var array Array of parameter's email format */
    protected $_email_format = array();

    /** @var array Array of parameter's date format */
    protected $_date_format = array();

    /** @var array Array of parameter's number format */
    protected $_number_format = array();

    /** @var array Array of parameter's kana format */
    protected $_kana_format = array();

    /** @var array Array of parameter's japanese format */
    protected $_japanese_format = array();

    /** @var array Array of parameter's hiragana format */
    protected $_hira_format = array();

    /** @var array Array of response */
    protected $_response = array();

    /** @var bool Check if having parameter or not */
    protected $_has_parameter = true;

    /** @var Object Instance of BusAbstract */
    protected static $_instance = null;

    /**
     * Get instance of bus object
     *
     * @author thailh
     * @return object Instance of bus object
     */
    public final static function getInstance() {
        if (static::$_instance === null) {
            static::$_instance = new static();
        }
        return static::$_instance;
    }

    /**
     * Add error array
     *
     * @return void
     * @author thailh
     */
    protected function _addErrors($error = array()) {
        if (empty($error))
            return false;
        foreach ($error as $err) {
            $this->_addError($err['code'], $err['field'], $err['value']);
        }
    }

    /**
     * Add a error
     *
     * @return void
     * @author thailh
     */
    protected function _addError($code, $field, $value = '') {
        if (isset($this->_error_code_validation[$field])) {
            return true;
        }
        if (isset($this->_error_message_validation[$code])) {
            $message = sprintf($this->_error_message_validation[$code], $field, $value);
        } else {
            $message = $value;
        }
        $this->_error_code_validation[] = array(
            'field' => $field,
            'code' => $code,
            'message' => $message
        );
    }

    /**
     * Get validation error
     *
     * @return array Array of validation's error code
     * @author thailh
     */
    protected function _getError() {
        return $this->_error_code_validation;
    }

    /**
     * Get default value setting
     *
     * @return array Array of default value
     * @author thailh
     */
    public function getDefaultValue() {
        return $this->_default_value;
    }

    /**
     * Implements function get required files setting
     *
     * @author thailh
     * @returns array Array of required parameter
     */
    public function getRequired() {
        return $this->_required;
    }

    /**
     * Implements function get length of fields setting
     *
     * @author thailh
     * @returns array Array of parameter's length
     */
    public function getLength() {
        return $this->_length;
    }

    /**
     * Implements function setDefaultValue if empty
     *
     * @param  array $data
     * @return array Array with default value
     * @author thailh
     */
    public function setDefaultValue($data) {
        $defaultValue = $this->getDefaultValue();
        if (empty($defaultValue)) {
            return $data;
        }
        foreach ($defaultValue as $field => $value) {
            if (!isset($data[$field])) {
                $data[$field] = $value;
            }
        }
        foreach ($data as $field => $value) {
            if (($data[$field] == NULL || $data[$field] === '') && isset($defaultValue[$field])) {
                $data[$field] = $defaultValue[$field];
            }
        }
        return $data;
    }

    /**
     * Check required parameters
     *
     * @author  thailh
     * @param   array $data Input data
     * @param   array $requiredField Required parameter
     * @return boolean True if data is valid required, false if invalid
     */
    public function checkRequired($data, $requiredField = null) {
        if (!$requiredField) {
            $requiredField = $this->getRequired();
        }
        if (!isset($data[0])) {
            $data = array($data);
        }
        $ok = true;
        foreach ($data as $dt) {
            foreach ($requiredField as $key => $field) {
                if (!isset($dt[$field]) || (isset($dt[$field]) && trim($dt[$field]) == '')) {
                    $this->_addError(self::ERROR_CODE_FIELD_REQUIRED, $field);
                    $ok = false;
                }
            }
        }

        return $ok;
    }

    /**
     * Check parameter's length
     *
     * @author  thailh
     * @param   array $data Input data to check
     * @param   array $lengthOfField Length config for check length
     * @return  bool True if all field are valid, false if have one of fields invalid
     */
    public function checkLength($data, $lengthOfField = null) {
        if (!$lengthOfField) {
            $lengthOfField = $this->getLength();
        }
        if (!isset($data[0])) {
            $data = array($data);
        }
        $ok = true;
        foreach ($data as $d) {
            foreach ($lengthOfField as $field => $length) {
                if (isset($d[$field])) {
                    if (is_array($length)) {
                        if ((mb_strlen($d[$field]) < intval($length[0])) && !empty($d[$field])) {
                            $this->_addError(self::ERROR_CODE_FIELD_LENGTH_MIN, $field, $length[0]);
                            $ok = false;
                        }
                        if ((mb_strlen($d[$field]) > intval($length[1])) && !empty($d[$field])) {
                            $this->_addError(self::ERROR_CODE_FIELD_LENGTH_MAX, $field, $length[1]);
                            $ok = false;
                        }
                    } else {
                        if ((mb_strlen($d[$field]) != $length) && !empty($d[$field])) {
                            $this->_addError(self::ERROR_CODE_FIELD_LENGTH_MAX, $field, $length);
                            $ok = false;
                        }
                    }
                }
            }
        }
        return $ok;
    }

    /**
     * Set error
     *
     * @param array $errorCode Array of error code
     * @return string Response format of api
     * @author thailh
     */
    private function _error($errorCode) {
        return $this->getResponse($errorCode);
    }

    /**
     * Do all check, and then call operateDB if data is good
     *
     * @param array $json Input format
     * @param array $moreParam More parameter if having
     * @return string Response of api
     * @author thailh
     */
    public final function execute($json = null, $moreParam = array()) {
        $data = array();
        \LogLib::info('headers:', __METHOD__, Input::headers());
        \LogLib::info('user_agent:', __METHOD__, Input::user_agent());
        
        // if have authToken || require authorize
        if ((\Lib\Util::authToken() || !in_array(\Uri::string(), \Config::get('unauthorize_url'))) && \Config::get('authorize') == true) {
            $authDetail = \Model_Authenticate::check_token();
            if (empty($authDetail)) {
                $this->_addError(self::ERROR_CODE_AUTH_ERROR, 'token');
                return $this->getResponse(self::ERROR_CODE_AUTH_ERROR);
            }
            if ($authDetail['regist_type'] == 'admin') {
                $data['admin_id'] = $authDetail['user_id'];
            } else {
                $data['admin_id'] = 0;
                $data['login_user_id'] = $authDetail['user_id'];
            }
            if (in_array(\Uri::string(), \Config::get('admin_authorize_url')) && \Config::get('authorize') == true) {
                if ($data['admin_id'] == 0) {
                    $this->_addError(self::ERROR_CODE_AUTH_PERMISSION_ERROR, 'user_id');
                    return $this->getResponse(self::ERROR_CODE_AUTH_PERMISSION_ERROR);
                }
            }
        }
        
        if ($this->_has_parameter) {
            if ($this->_input_format == 'json') {
                if ($json === null) {
                    $json = file_get_contents("php://input");
                }
                $data = \Format::forge($json, 'json')->to_array();
                if (!empty($json) && empty($data)) {
                    return $this->_error(self::ERROR_CODE_INVALID_JSON);
                }
            } elseif ($this->_input_format == 'get' || $this->_input_format == 'post') {
                if ($json === null) {
                    $data = array_merge(Input::param(), $data);
                }
            }
            if (!empty($moreParam)) {
                $data = array_merge($data, $moreParam);
            }
            
            // update output from base on output parameter in data
            if (!isset($data['output']) || !in_array($data['output'], self::$_formats)) {
                $this->_output_format = 'json';
            }

            $data = $this->setDefaultValue($data);

            // check required
            $checkRequired = $this->checkRequired($data);
            if (!$checkRequired) {
                return $this->getResponse(self::ERROR_CODE_INVALED_PARAMETER);
            }

            // check length
            $checkLength = $this->checkLength($data);
            if (!$checkLength) {
                return $this->getResponse(self::ERROR_CODE_INVALED_PARAMETER);
            }

            // check url
            $checkUrl = $this->checkUrlFormat($data);
            if (!$checkUrl) {
                return $this->getResponse(self::ERROR_CODE_INVALED_PARAMETER);
            }

            // check email
            $checkEmail = $this->checkEmailFormat($data);
            if (!$checkEmail) {
                return $this->getResponse(self::ERROR_CODE_INVALED_PARAMETER);
            }

            // check date
            $checkDate = $this->checkDateFormat($data);
            if (!$checkDate) {
                return $this->getResponse(self::ERROR_CODE_INVALED_PARAMETER);
            }

            // check number
            $checkNumber = $this->checkNumberFormat($data);
            if (!$checkNumber) {
                return $this->getResponse(self::ERROR_CODE_INVALED_PARAMETER);
            }

            // check data format
            $checkFormat = $this->checkDataFormat($data);
            if (!$checkFormat) {
                return $this->getResponse(self::ERROR_CODE_INVALED_PARAMETER);
            }
        } else {
            $data = $json;
        }

        // get operation system (iOS, android, webos)
        $data['os'] = \Lib\Util::os();

        // check security by date + key if config api_check_security = true      
        // generate key for test
        // echo api_auth_key = hash('md5', Config::get('api_secret_key') . \Lib\Util::gmtime(date('Y/m/d H:i:s')));
        // echo api_auth_date = \Lib\Util::gmtime(date('Y/m/d H:i:s'));        
        if (Config::get('api_check_security') == true && !in_array(\Uri::string(), \Config::get('unauthorize_basic_token_url'))) {
            // check valid param
            if (empty($data['api_auth_date']) || empty($data['api_auth_key'])) {
                $this->_addError(self::ERROR_CODE_INVALED_PARAMETER, 'auth_date_or_auth_key');
                return $this->getResponse(self::ERROR_CODE_INVALED_PARAMETER);
            }
            // check valid date 
            // if request date before n minutes (n = Config::get('api_request_minute','))
            if ($data['api_auth_date'] < \Lib\Util::gmtime(date("Y-m-d H:i:s", strtotime('-' . Config::get('api_request_minute', 10) . 'minutes')))) {
                $this->_addError(self::ERROR_CODE_AUTH_ERROR, 'api_auth_date');
                return $this->getResponse(self::ERROR_CODE_AUTH_ERROR);
            }
            // check valid key
            // api_auth_key = md5(api_secret_key + api_auth_date)
            if (hash('md5', Config::get('api_secret_key') . $data['api_auth_date']) != $data['api_auth_key']) {
                $this->_addError(self::ERROR_CODE_AUTH_ERROR, 'api_auth_key');
                return $this->getResponse(self::ERROR_CODE_AUTH_ERROR);
            }
        }

        // global process
        if (empty($data['page']) && !empty($data['limit'])) {
            $data['page'] = 1;
        }
        
        // save log data
//        \LogLib::info('input:', __METHOD__, $data);
        
        $operateDB = $this->operateDB($data);
        if ($operateDB === false) {
            if ($this->_exception != null) {
                \LogLib::error(sprintf("Exception\n"
                                . " - Message : %s\n"
                                . " - Code : %s\n"
                                . " - File : %s\n"
                                . " - Line : %d\n"
                                . " - Stack trace : \n"
                                . "%s", $this->_exception->getMessage(), $this->_exception->getCode(), $this->_exception->getFile(), $this->_exception->getLine(), $this->_exception->getTraceAsString()), __METHOD__, $data);
            }
            if (self::_getError()) {
                return $this->getResponse(self::ERROR_CODE_INVALED_PARAMETER);
            } else {
                return $this->getResponse(self::ERROR_CODE_DB_ERROR);
            }
        }
        return $this->getResponse(self::STATUS_OK);
    }

    /**
     * Return format as XML
     *
     * @author  thailh
     * @return  string Xml format
     */
    public function getXMLResponse() {
        return \Lib\Format::forge($this->_response)->to_xml();
    }

    /**
     * Return format as PHP variable
     *
     * @author  thailh
     * @return  string PHP format
     */
    public function getPhpResponse() {
        return \Lib\Format::forge($this->_response)->to_php();
    }

    /**
     * Return format as HTML variable
     *
     * @author  thailh
     * @return  string Html format
     */
    public function getHtmlResponse() {
        return \Lib\Format::forge($this->_response)->to_html();
    }

    /**
     * Return format as serialize variable
     *
     * @author  thailh
     * @return  string Serialized format
     */
    public function getSerializeResponse() {
        return \Lib\Format::forge($this->_response)->to_serialized();
    }

    /**
     * Return format as json variable
     *
     * @author  thailh
     * @return  string Json format
     */
    public function getJsonResponse() {
        return \Lib\Format::forge($this->_response)->to_json();
    }

    /**
     * Get response of api
     *
     * @author  thailh
     * @return  String Response of api
     */
    public function getResponse($httpStatus = null) {
        $response = new Response();
        if (!empty($httpStatus)) {
            $this->_error_code = $httpStatus;
            $response->set_status($httpStatus);
        }
        if ($httpStatus != self::STATUS_OK) {
            if ($httpStatus == self::ERROR_CODE_DB_ERROR) {
                $this->_response = array(
                    'status' => $this->_error_code,
                    'error' => array(
                        $this->_exception->getMessage()
                    )
                );
            } else {
                $this->_response = array(
                    'status' => $this->_error_code,
                    'error' => $this->_getError()
                );
                \LogLib::warning("Validation error (400)", __METHOD__, $this->_response);
            }
        } else {
            $this->_response = array(
                'status' => self::STATUS_OK,
                'body' => $this->_response,
            );
        }
        switch ($this->_output_format) {
            case 'xml':
                $response->set_header('Content-Type', 'text/xml');
                $function = 'getXmlResponse';
                break;
            case 'php':
                $response->set_header('Content-Type', 'text/plain');
                $function = 'getPhpResponse';
                break;
            case 'html':
                $response->set_header('Content-Type', 'text/html');
                $function = 'getHtmlResponse';
                break;
            case 'serialize':
                $response->set_header('Content-Type', 'text/html');
                $function = 'getSerializeResponse';
                break;
            default:
                $response->set_header('Content-Type', 'application/json');
                $function = 'getJsonResponse';
        }
        $body = call_user_func(array($this, $function));
        $response->body($body);
        return $response;
    }

    /**
     * Get response result
     *
     * @author  thailh
     * @param   $error Array of errors
     * @return  bool True if successful, otherwise false
     */
    public function result($error = array()) {
        if (!empty($error)) {
            $this->_addErrors($error);
            return false;
        }
        if ($this->_response !== false) {
            return true;
        }
        
        // Set default error
        if (!self::_getError()) {
            self::_addError(self::ERROR_CODE_UNKNOW, 'unknow', 'Unknow error!!!');
        }
        
        return false;
    }

    /**
     * Check data format, will be override at child class if need
     *
     * @author  thailh
     * @param   $data Input data
     * @return  bool True if successful, otherwise false
     */
    public function checkDataFormat($data) {
        return true;
    }

    /**
     * Check url format, will be override at child class if need
     *
     * @author Le Tuan Tu
     * @param $data Input data
     * @return bool True if successful, otherwise false
     */
    public function checkUrlFormat($data) {
        foreach ($this->_url_format as $field) {
            if (!empty($data[$field]) && !filter_var($data[$field], FILTER_VALIDATE_URL)) {
                $this->_addError(self::ERROR_CODE_FIELD_FORMAT_URL, $field, $data[$field]);
                $this->_invalid_parameter = $field;
                return false;
            }
        }

        return true;
    }

    /**
     * Check email format, will be override at child class if need
     *
     * @author Le Tuan Tu
     * @param $data Input data
     * @return bool True if successful, otherwise false
     */
    public function checkEmailFormat($data) {
        foreach ($this->_email_format as $field) {
            if (!empty($data[$field])) {
                $pattern = "/^[A-Za-z0-9._%+-]+@([A-Za-z0-9-]+\.)+([A-Za-z0-9]{2,4}|museum)$/";
                if (!preg_match($pattern, $data[$field])) {
                    $this->_addError(self::ERROR_CODE_FIELD_FORMAT_EMAIL, $field, $data[$field]);
                    $this->_invalid_parameter = $field;
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * Check date format, will be override at child class if need
     *
     * @author Le Tuan Tu
     * @param $data Input data
     * @return bool True if successful, otherwise false
     */
    public function checkDateFormat($data) {
        foreach ($this->_date_format as $field => $value) {
            if (!empty($data[$field])) {
                $dt = \DateTime::createFromFormat($value, $data[$field]);
                if (!($dt !== false && !array_sum($dt->getLastErrors()))) {
                    $this->_addError(self::ERROR_CODE_FIELD_FORMAT_DATE, $field, $data[$field]);
                    $this->_invalid_parameter = $field;
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * Check number format, will be override at child class if need
     *
     * @author Le Tuan Tu
     * @param $data Input data
     * @return bool True if successful, otherwise false
     */
    public function checkNumberFormat($data) {
        // KienNH 2016/04/20: Alway check language_type, page and limit
        $_number_format = array_merge($this->_number_format, array('language_type', 'page', 'limit'));

        foreach ($_number_format as $field) {

            if (!empty($data[$field]) && !is_numeric($data[$field])) {
                $this->_addError(self::ERROR_CODE_FIELD_FORMAT_NUMBER, $field, $data[$field]);
                $this->_invalid_parameter = $field;
                return false;
            }
        }

        return true;
    }

    /**
     * Will be override by child class
     *
     * @author  thailh
     * @param   $data Input data
     * @return  void
     */
    public abstract function operateDB($data);
}
