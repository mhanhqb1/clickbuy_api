<?php

use Fuel\Core\DB;
use Lib\Util;

/**
 * Any query in Model Company.
 *
 * @package Model
 * @version 1.0
 * @author AnhMH
 * @copyright Oceanize INC
 */
class Model_Company extends Model_Abstract {

    protected static $_properties = array(
        'id',
        'name',
        'logo',
        'seo_description',
        'seo_image',
        'seo_keyword',
        'email',
        'tel',
        'address',
        'facebook',
        'youtube',
        'instagram',
        'zalo',
        'google_plus',
        'twitter',
        'script_header',
        'script_body',
        'script_footer',
        'created',
        'updated'
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
    
    protected static $_table_name = 'companies';
    
    /**
     * Add update info.
     *
     * @author AnhMH
     * @param array $param Input data.
     * @return array|bool Returns the array or the boolean.
     */
    public static function add_update($param)
    {
        // Init
        $self = array();
        
        // Get company info
        $self = self::find('first');
        if (empty($self)) {
            $self = new self;
        }
        
        // Upload image
        if (!empty($_FILES)) {
            $uploadResult = \Lib\Util::uploadImage(); 
            if ($uploadResult['status'] != 200) {
                self::setError($uploadResult['error']);
                return false;
            }
            $param['logo'] = !empty($uploadResult['body']['logo']) ? $uploadResult['body']['logo'] : '';
            $param['seo_image'] = !empty($uploadResult['body']['seo_image']) ? $uploadResult['body']['seo_image'] : '';
        }
        
        // Set data
        if (isset($param['name'])) {
            $self->set('name', $param['name']);
        }
        if (isset($param['logo'])) {
            $self->set('logo', $param['logo']);
        }
        if (isset($param['address'])) {
            $self->set('address', $param['address']);
        }
        if (isset($param['tel'])) {
            $self->set('tel', $param['tel']);
        }
        if (isset($param['seo_image'])) {
            $self->set('seo_image', $param['seo_image']);
        }
        if (isset($param['seo_description'])) {
            $self->set('seo_description', $param['seo_description']);
        }
        if (isset($param['seo_keyword'])) {
            $self->set('seo_keyword', $param['seo_keyword']);
        }
        if (isset($param['facebook'])) {
            $self->set('facebook', $param['facebook']);
        }
        if (isset($param['twitter'])) {
            $self->set('twitter', $param['twitter']);
        }
        if (isset($param['instagram'])) {
            $self->set('instagram', $param['instagram']);
        }
        if (isset($param['google_plus'])) {
            $self->set('google_plus', $param['google_plus']);
        }
        if (isset($param['youtube'])) {
            $self->set('youtube', $param['youtube']);
        }
        if (isset($param['email'])) {
            $self->set('email', $param['email']);
        }
        if (isset($param['zalo'])) {
            $self->set('zalo', $param['zalo']);
        }
        if (isset($param['script_header'])) {
            $self->set('script_header', $param['script_header']);
        }
        if (isset($param['script_body'])) {
            $self->set('script_body', $param['script_body']);
        }
        if (isset($param['script_footer'])) {
            $self->set('script_footer', $param['script_footer']);
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
     * Get detail
     *
     * @author AnhMH
     * @param array $param Input data
     * @return array|bool
     */
    public static function get_detail($param)
    {
        
        $data = self::find('first');
        
        return $data;
    }
}
