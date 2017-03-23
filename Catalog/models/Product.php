<?php

namespace Catalog\models;

use System\Model;
use System\Query;
use System\Password;

class Product extends Model
{
    protected $permittedColumns = array(
        'category_id',
        'name',
        'description',
        'price',
        'active'
    );

    /**
     * Validate the object and fill $this->errors with error messages
     *
     * @return bool
     */
    public function valid()
    {
        $this->errors = array();

        $this->name = trim($this->name);
        if ($this->name == '') {
            $this->errors['name'] = 'Nom obligatoire';
        }

        return empty($this->errors);
    }
}