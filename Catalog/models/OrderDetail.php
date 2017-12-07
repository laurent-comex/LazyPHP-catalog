<?php

namespace Catalog\models;

use Core\Model;

class OrderDetails extends Model
{
    protected $permittedColumns = array(
        'order_id',
        'product_id',
        'quantity'
    );

    public function getAssociations()
    {
        return array(
            'order' => array(
                'type' => '1',
                'model' => 'Catalog\\models\\Order',
                'key' => 'order_id'
            ),
            'product' => array(
                'type' => '1',
                'model' => 'Catalog\\models\\Product',
                'key' => 'product_id'
            )
        );
    }
}
