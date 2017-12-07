<?php

namespace Catalog\models;

use Core\Model;

class Orders extends Model
{
    protected $permittedColumns = array(
        'site_id',
        'user_id'
    );

    public function getAssociations()
    {
        return array(
            'site' => array(
                'type' => '1',
                'model' => 'Site',
                'key' => 'site_id'
            ),
            'user' => array(
                'type' => '1',
                'model' => 'User',
                'key' => 'user_id'
            ),
            'orderdetails' => array(
                'type' => '*',
                'model' => 'OrderDetail',
                'key' => 'order_id'
            )
        );
    }
}
