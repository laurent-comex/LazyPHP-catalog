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
        'image',
        'active'
    );

    /**
     * Get list of associed table(s)
     *
     * @return mixed
     */
    public function getAssociations()
    {
        return array(
            'category' => array(
                'type' => '1',
                'model' => 'Catalog\\models\\Category',
                'key' => 'category_id'
            )
        );
    }

    /**
     * Set default properties values
     */
    public function setDefaultProperties()
    {
        parent::setDefaultProperties();

        $this->category_id = null;
        $this->price = 0.0;
        $this->active = 1;
    }

    /**
     * Validate the object and fill $this->errors with error messages
     *
     * @return bool
     */
    public function valid()
    {
        $this->errors = array();

        if (!isset($this->category_id) || $this->category_id == '') {
            $this->category_id = null;
        }

        $this->name = trim($this->name);
        if ($this->name == '') {
            $this->errors['name'] = 'Nom obligatoire';
        }

        if (!isset($this->price) || $this->price == '') {
            $this->price = 0.0;
        }

        if (!isset($this->active) || $this->active == '') {
            $this->active = 0;
        }

        $errorFile = $this->validFile($this->image, 'image');
        if ($errorFile !== true) {
            $this->errors['image'] = $errorFile;
        }

        return empty($this->errors);
    }
}
