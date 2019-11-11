<?php

/**
 * Any query in Model Authenticate.
 *
 * @package Model
 * @version 1.0
 * @copyright Oceanize INC
 */
class Model_Authenticate extends Model_Abstract {

    protected static $_properties = array(
        'id',
        'user_id',
        'token',
        'expire_date',
        'regist_type',
        'created',
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
    
    protected static $_table_name = 'authenticates';
    
    /**
     * Addupdate info for authenticates.
     *
     * @author AnhMH
     * @param array $param Input data.
     * @return string Returns the string of token.
     */
    public static function addupdate($param) {
        if (empty($param['user_id']) || empty($param['regist_type'])) {
            self::errorParamInvalid('user_id_or_regist_type');
            return false;
        }
        $token = '';
        $query = DB::select(
                'id', 'user_id', 'token', 'expire_date', 'regist_type', 'created', DB::expr("UNIX_TIMESTAMP() AS systime")
            )
            ->from(self::$_table_name)
            ->where('user_id', '=', $param['user_id'])
            ->where('regist_type', '=', $param['regist_type'])
            ->limit(1);
        $data = $query->execute()->as_array();
        $authenticate = !empty($data[0]) ? $data[0] : array();
        if (empty($authenticate['id'])) {
            \LogLib::info('Create new token', __METHOD__, $param);
            $token = \Lib\Str::generate_token_for_api();
            $auth = new self;
            $auth->set('user_id', $param['user_id']);
            $auth->set('regist_type', $param['regist_type']);
            $auth->set('token', $token);
            $auth->set('expire_date', \Config::get('api_token_expire'));
            if (!$auth->create()) {
                \LogLib::warning('Can not create token', __METHOD__, $param);
            }
        } else {
            $auth = new self($authenticate, false);
            $auth->set('expire_date', \Config::get('api_token_expire'));
            $token = $authenticate['token'];
            if ($authenticate['expire_date'] < $authenticate['systime']) {
                \LogLib::info('Update new token', __METHOD__, $param);
                $token = \Lib\Str::generate_token_for_api();
                $auth->set('token', $token);
            }
            if (!$auth->update()) {
                \LogLib::warning('Can not update token', __METHOD__, $param);
            }
        }
        return $token;
    }

    /**
     * Check token.
     *
     * @author AnhMH
     * @return bool|array Returns the boolean or the array.	
     */
    public static function check_token() {
        $param = array(
            'user_id' => \Lib\Util::authUserId(),
            'token' => \Lib\Util::authToken(),
        );
        $query = DB::select(
                'id', 'user_id', 'token', 'expire_date', 'regist_type', 'created', DB::expr("UNIX_TIMESTAMP() AS systime")
            )
            ->from(self::$_table_name)
            ->where('user_id', '=', $param['user_id'])
            ->where('token', '=', $param['token'])
            ->limit(1);
        $data = $query->execute(self::$slave_db)->as_array();
        $data = !empty($data[0]) ? $data[0] : array();
        if (empty($data)) {
            self::errorNotExist('token');
            \LogLib::warning('Token does not exist', __METHOD__, $param);
            return false;
        }
        if ($data['expire_date'] < $data['systime']) {
            \LogLib::warning('Token have been already expired', __METHOD__, $param);
            self::errorOther(self::ERROR_CODE_AUTH_ERROR, 'token');
            return false;
        }
        return $data;
    }

}
