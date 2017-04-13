<?php

namespace Catalog\models;

use System\Model;

class Category extends Model
{
    protected $permittedColumns = array(
        'parent',
        'label',
        'description',
        'position',
        'active'
    );

    public static function getTableName()
    {
        return 'categories';
    }
}
