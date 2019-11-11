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
class Model_Customer extends Model_Abstract {

    /** @var array $_properties field of table */
    protected static $_properties = array(
        'id',
        'account',
        'password',
        'name',
        'address',
        'phone',
        'email',
        'code',
        'order_count',
        'created'
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
    protected static $_table_name = 'users';

    /**
     * Add update info
     *
     * @author AnhMH
     * @param array $param Input data
     * @return int|bool User ID or false if error
     */
    public static function add_update($param) {
        // Init
        $self = array();
        $isNew = false;
        $time = time();

        // Check if exist User
        if (!empty($param['id'])) {
            $self = self::find($param['id']);
            if (empty($self)) {
                self::errorNotExist('customer_id');
                return false;
            }
        } else {
            $self = new self;
            $isNew = true;
        }

        // Set data
        if (!empty($param['name'])) {
            $self->set('name', $param['name']);
        }
        if (!empty($param['address'])) {
            $self->set('address', $param['address']);
        }
        if (!empty($param['phone'])) {
            $self->set('phone', $param['phone']);
        }
        if (!empty($param['user_name'])) {
            $self->set('user_name', $param['user_name']);
        }
        if (!empty($param['pass'])) {
            $self->set('pass', $param['pass']);
        }
        if (!empty($param['email'])) {
            $self->set('email', $param['email']);
        }
        if (isset($param['note'])) {
            $self->set('note', $param['note']);
        }
        $self->set('updated', $time);
        if ($isNew) {
            $self->set('created', $time);
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
     * Add update info
     *
     * @author AnhMH
     * @param array $param Input data
     * @return int|bool User ID or false if error
     */
    public static function register($param) {
        // Init
        $time = time();
        $self = new self;
        if (!empty($param['account'])) {
            $check = self::find('first', array(
                        'where' => array(
                            'phone' => $param['account']
                        )
            ));
            if (!empty($check)) {
                self::errorDuplicate('account');
                return false;
            }
            $self->set('account', $param['account']);
        }
        if (!empty($param['email'])) {
            $check = self::find('first', array(
                        'where' => array(
                            'email' => $param['email']
                        )
            ));
            if (!empty($check)) {
                self::errorDuplicate('email');
                return false;
            }
            $self->set('email', $param['email']);
        }
        if (!empty($param['phone'])) {
            $check = self::find('first', array(
                        'where' => array(
                            'phone' => $param['phone']
                        )
            ));
            if (!empty($check)) {
                self::errorDuplicate('phone');
                return false;
            }
            $self->set('phone', $param['phone']);
            $self->set('code', $param['phone']);
        }

        // Set data
        if (!empty($param['name'])) {
            $self->set('name', $param['name']);
        }
        if (!empty($param['address'])) {
            $self->set('address', $param['address']);
        }
        if (!empty($param['password'])) {
            $pass = \Lib\Util::encodePassword($param['password'], $param['account']);
            $self->set('password', $pass);
        }
        $self->set('created', $time);

        // Save data
        if ($self->save()) {
            if (empty($self->id)) {
                $self->id = self::cached_object($self)->_original['id'];
            }
            $self['token'] = \Model_Authenticate::addupdate(array(
                        'user_id' => $self->id,
                        'regist_type' => 'user'
            ));
            return $self;
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
    public static function get_list($param) {
        // Init
        $adminId = !empty($param['admin_id']) ? $param['admin_id'] : '';

        // Query
        $query = DB::select(
                        self::$_table_name . '.*'
                )
                ->from(self::$_table_name)
        ;

        // Filter
        if (!empty($param['name'])) {
            $query->where(self::$_table_name . '.name', 'LIKE', "%{$param['name']}%");
        }
        if (!empty($param['address'])) {
            $query->where(self::$_table_name . '.address', 'LIKE', "%{$param['address']}%");
        }
        if (!empty($param['phone'])) {
            $query->where(self::$_table_name . '.phone', 'LIKE', "%{$param['phone']}%");
        }
        if (!empty($param['email'])) {
            $query->where(self::$_table_name . '.email', 'LIKE', "%{$param['email']}%");
        }

        if (isset($param['disable']) && $param['disable'] != '') {
            $disable = !empty($param['disable']) ? 1 : 0;
            $query->where(self::$_table_name . '.disable', $disable);
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
    public static function get_detail($param) {
        $id = !empty($param['id']) ? $param['id'] : '';

        $query = DB::select(
                        self::$_table_name . '.*'
                )
                ->from(self::$_table_name)
                ->where(self::$_table_name . '.id', $id)
        ;
        $data = $query->execute()->offsetGet(0);
        if (empty($data)) {
            self::errorNotExist('customer_id');
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
    public static function disable($param) {
        $ids = !empty($param['id']) ? $param['id'] : '';
        $disable = !empty($param['disable']) ? $param['disable'] : 0;
        if (!is_array($ids)) {
            $ids = explode(',', $ids);
        }
        foreach ($ids as $id) {
            $self = self::find($id);
            if (!empty($self)) {
                $self->set('disable', $disable);
                $self->save();
            }
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
    public static function get_all($param) {
        // Query
        $query = DB::select(
                        self::$_table_name . '.*'
                )
                ->from(self::$_table_name)
        ;

        // Filter
        if (!empty($param['name'])) {
            $query->where(self::$_table_name . '.name', 'LIKE', "%{$param['name']}%");
        }
        if (!empty($param['code'])) {
            $query->where(self::$_table_name . '.code', 'LIKE', "%{$param['code']}%");
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

        return $data;
    }
}
