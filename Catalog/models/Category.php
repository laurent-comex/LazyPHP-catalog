<?php

namespace Catalog\models;

use System\Model;
use System\Query;
use System\Password;

class Category extends Model
{
    protected $permittedColumns = array(
        'parent',
        'name',
        'description',
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
