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
                'model' => 'Core\\models\\Site',
                'key' => 'site_id'
            ),
            'user' => array(
                'type' => '1',
                'model' => 'Auth\\models\\User',
                'key' => 'user_id'
            ),
            'orderdetails' => array(
                'type' => '*',
                'model' => 'Catalog\\models\\OrderDetail',
                'key' => 'order_id'
            )
        );
    }
}
