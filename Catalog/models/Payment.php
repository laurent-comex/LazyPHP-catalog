<?php

namespace Catalog\models;

use Core\Model;

class Payment extends Model
{
    protected $permittedColumns = array(
        'order_id',
        'payment_system',
        'payment_method',
        'amount',
        'bill'
    );

    public function getAssociations()
    {
        return array(
            'order' => array(
                'type' => '1',
                'model' => 'Order',
                'key' => 'order_id'
            )
        );
    }
}
