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
class Model_Setting extends Model_Abstract {
    
    /** @var array $_properties field of table */
    protected static $_properties = array(
        'id',
        'cate_id',
        'name',
        'description',
        'content',
        'image',
        'is_default',
        'is_home_slide',
        'is_hot',
        'type',
        'created',
        'updated',
        'disable'
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
    protected static $_table_name = 'settings';

    /**
     * Get general
     *
     * @author AnhMH
     * @param array $param Input data
     * @return int|bool User ID or false if error
     */
    public static function get_general($param)
    {
        // Init
        $result = array();
        
        // Get company info
        $result['company'] = Model_Company::find('first');
        
        // Get cates
        $result['product_cates'] = Model_Cate::get_all(array(
            'type' => 1
        ));
        $result['blog_cates'] = Model_Cate::get_all(array(
            'type' => 2
        ));
                
        // Return
        return $result;
    }
    
    /**
     * Get homedata
     *
     * @author AnhMH
     * @param array $param Input data
     * @return int|bool User ID or false if error
     */
    public static function get_homedata($param)
    {
        // Init
        $result = array();
        
        // Get company info
        $result['sliders'] = Model_Banner::get_all(array());
        
        // Get hot product
        $result['hot_products'] = Model_Product::get_all(array(
            'is_hot' => 1,
            'page' => 1,
            'limit' => 12
        ));
        // Get posts data
        $result['cate_products'] = DB::select(
                'products.*',
                array('cates.name', 'cate_name'),
                array('cates.url', 'cate_url')
            )
            ->from(DB::expr("
                (
                    SELECT
			products.*,
			@rn :=
                            IF (@prev = cate_id, @rn + 1, 1) AS rn,
                        @prev := cate_id
                    FROM
                        products
                    JOIN (SELECT @prev := NULL, @rn := 0) AS vars
                    WHERE
                        disable = 0
                    ORDER BY
                        cate_id
                ) AS products
            "))
            ->join('cates', 'LEFT')
            ->on('cates.id', '=', 'products.cate_id')
            ->where(DB::expr("rn <= 10"))
            ->execute()
            ->as_array()
        ;
        
        // Get news
        $result['posts'] = Model_Post::get_all(array(
            'page' => 1,
            'limit' => 12
        ));
                
        // Return
        return $result;
    }
    
    /**
     * Get general
     *
     * @author AnhMH
     * @param array $param Input data
     * @return int|bool User ID or false if error
     */
    public static function get_admin_general($param)
    {
        // Init
        $result = array();
        
        $posts = DB::select('*')->from('posts')->where('disable', 0)->execute();
        $result['post_count'] = count($posts);
        
        $products = DB::select('*')->from('products')->where('disable', 0)->execute();
        $result['product_count'] = count($products);
        
        $orders = DB::select('*')->from('orders')->where('disable', 0)->execute();
        $result['order_count'] = count($orders);
        
        $contacts = DB::select('*')->from('contacts')->execute();
        $result['contact_count'] = count($contacts);
        
        // Return
        return $result;
    }
}
