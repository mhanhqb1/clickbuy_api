<?php

use Fuel\Core\DB;

/**
 * Any query in Model Version
 *
 * @package Model
 * @created 2017-10-29
 * @version 1.0
 * @author AnhMH
 */
class Model_Withdraw extends Model_Abstract {
    
    /** @var array $_properties field of table */
    protected static $_properties = array(
        'id',
        'user_id',
        'card_number',
        'bank_name',
        'name',
        'phone',
        'status',
        'created',
        'amount',
        'status'
    );

    protected static $_observers = array(
        'Orm\Observer_CreatedAt' => array(
            'events'          => array('before_insert'),
            'mysql_timestamp' => false,
        ),
        'Orm\Observer_UpdatedAt' => array(
            'events'          => array('before_update'),
            'mysql_timestamp' => false,
        ),
    );

    /** @var array $_table_name name of table */
    protected static $_table_name = 'withdraws';

    /**
     * Add update info
     *
     * @author AnhMH
     * @param array $param Input data
     * @return int|bool User ID or false if error
     */
    public static function add_update($param)
    {
        // Init
        $time = time();
        $userId = !empty($param['login_user_id']) ? $param['login_user_id'] : 0;
        
        // Get user detail
        $user = Model_Customer::find($userId);
        if (empty($user)) {
            self::errorOther('code', self::ERROR_CODE_EMAIL_NOT_EXIST, 'Khách hàng không tồn tại');
            return false;
        }
        // Check duplicate
        $check = self::find('first', array(
            'where' => array(
                'status' => 0,
                'user_id' => $userId
            )
        ));
        if (!empty($check)) {
            self::errorOther('code', self::ERROR_CODE_FIELD_DUPLICATE, 'Bạn đã gửi yêu cầu, vui lòng chờ duyệt');
            return false;
        }
        $totalAmount = !empty($user['total_amount']) ? $user['total_amount'] : 0;
        $withdrawAmount = !empty($user['withdraw_amount']) ? $user['withdraw_amount'] : 0;
        $amount = $totalAmount - $withdrawAmount;
        if ($amount <= 0) {
            self::errorOther('code', self::ERROR_CODE_OTHER_1, 'Số dư không đủ');
            return false;
        }
        
        // Set data
        $self = new self;
        $self->set('user_id', $userId);
        $self->set('created', $time);
        $self->set('status', 0);
        $self->set('amount', $amount);
        if (!empty($param['name'])) {
            $self->set('name', $param['name']);
        }
        if (!empty($param['phone'])) {
            $self->set('phone', $param['phone']);
        }
        if (!empty($param['card_number'])) {
            $self->set('card_number', $param['card_number']);
        }
        if (!empty($param['bank_name'])) {
            $self->set('bank_name', $param['bank_name']);
        }
        
        // Save data
        if ($self->save()) {
            if (empty($self->id)) {
                $self->id = self::cached_object($self)->_original['id'];
            }
            return $self->id;
        }
        
        return false;
    }
    
    /**
     * Get list
     *
     * @author AnhMH
     * @param array $param Input data
     * @return array|bool
     */
    public static function get_list($param)
    {
        // Query
        $query = DB::select(
                self::$_table_name.'.*'
            )
            ->from(self::$_table_name)
        ;
                        
        // Filter
        if (!empty($param['user_id'])) {
            $query->where(self::$_table_name.'.user_id', $param['user_id']);
        }
        if (!empty($param['name'])) {
            $query->where(self::$_table_name.'.name', 'LIKE', "%{$param['name']}%");
        }
        
        // Pagination
        if (!empty($param['page']) && $param['limit']) {
            $offset = ($param['page'] - 1) * $param['limit'];
            $query->limit($param['limit'])->offset($offset);
        }
        
        // Sort
        if (!empty($param['sort'])) {
            if (!self::checkSort($param['sort'])) {
                self::errorParamInvalid('sort');
                return false;
            }

            $sortExplode = explode('-', $param['sort']);
            if ($sortExplode[0] == 'created') {
                $sortExplode[0] = self::$_table_name . '.created';
            }
            $query->order_by($sortExplode[0], $sortExplode[1]);
        } else {
            $query->order_by(self::$_table_name . '.id', 'DESC');
        }
        
        // Get data
        $data = $query->execute()->as_array();
        $total = !empty($data) ? DB::count_last_query(self::$slave_db) : 0;
        
        return array(
            'total' => $total,
            'data' => $data
        );
    }
    
    /**
     * Get detail
     *
     * @author AnhMH
     * @param array $param Input data
     * @return array|bool
     */
    public static function get_detail($param)
    {
        $id = !empty($param['id']) ? $param['id'] : '';
        
        $query = DB::select(
                self::$_table_name.'.*'
            )
            ->from(self::$_table_name)
            ->where(self::$_table_name.'.id', $id)
        ;
        
        $data = $query->execute()->offsetGet(0);
        
        if (empty($data)) {
            self::errorNotExist('order_id');
            return false;
        }
        
        return $data;
    }
    
    /**
     * Enable/Disable
     *
     * @author AnhMH
     * @param array $param Input data
     * @return int|bool User ID or false if error
     */
    public static function disable($param)
    {
        $ids = !empty($param['id']) ? $param['id'] : '';
        $disable = !empty($param['disable']) ? $param['disable'] : 0;
        if (!is_array($ids)) {
            $ids = explode(',', $ids);
        }
        foreach ($ids as $id) {
            $self = self::del(array('id' => $id));
        }
        return true;
    }
    
    /**
     * Get all
     *
     * @author AnhMH
     * @param array $param Input data
     * @return array|bool
     */
    public static function get_all($param)
    {
        // Init
        $adminId = !empty($param['admin_id']) ? $param['admin_id'] : '';
        
        if (!empty($param['product_url'])) {
            $cate = Model_Cate::find('first', array(
                'where' => array(
                    'url' => $param['cate_url']
                )
            ));
            if (!empty($cate['id'])) {
                $param['cate_id'] = $cate['id'];
            }
        }
        
        // Query
        $query = DB::select(
                self::$_table_name.'.*',
                array('cates.name', 'cate_name'),
                array('cates.url', 'cate_url')
            )
            ->from(self::$_table_name)
            ->join('cates', 'LEFT')
            ->on('cates.id', '=', self::$_table_name.'.cate_id')
            ->where(self::$_table_name.'.disable', 0)
        ;
                        
        // Filter
        if (!empty($param['name'])) {
            $query->where(self::$_table_name.'.name', 'LIKE', "%{$param['name']}%");
        }
        if (!empty($param['cate_id'])) {
            if (!is_array($param['cate_id'])) {
                $param['cate_id'] = explode(',', $param['cate_id']);
            }
            $query->where(self::$_table_name.'.cate_id', 'IN', $param['cate_id']);
        }
        if (isset($param['is_hot']) && $param['is_hot'] != '') {
            $query->where(self::$_table_name.'.is_hot', $param['is_hot']);
        }
        if (isset($param['is_home_slide']) && $param['is_home_slide'] != '') {
            $query->where(self::$_table_name.'.is_home_slide', $param['is_home_slide']);
        }
        if (isset($param['type']) && $param['type'] != '') {
            $query->where(self::$_table_name.'.type', $param['type']);
        }
        
        // Pagination
        if (!empty($param['page']) && $param['limit']) {
            $offset = ($param['page'] - 1) * $param['limit'];
            $query->limit($param['limit'])->offset($offset);
        }
        
        // Sort
        if (!empty($param['sort'])) {
            if (!self::checkSort($param['sort'])) {
                self::errorParamInvalid('sort');
                return false;
            }

            $sortExplode = explode('-', $param['sort']);
            if ($sortExplode[0] == 'created') {
                $sortExplode[0] = self::$_table_name . '.created';
            }
            $query->order_by($sortExplode[0], $sortExplode[1]);
        } else {
            $query->order_by(self::$_table_name . '.created', 'DESC');
        }
        
        // Get data
        $data = $query->execute()->as_array();
        
        return $data;
    }
    
    /**
     * Delete
     *
     * @author AnhMH
     * @param array $param Input data
     * @return Int|bool
     */
    public static function del($param)
    {
        $delete = self::deleteRow(self::$_table_name, array(
            'id' => $param['id']
        ));
        if ($delete) {
            return $param['id'];
        } else {
            return 0;
        }
    }
}
