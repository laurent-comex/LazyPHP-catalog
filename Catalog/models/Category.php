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
     * Set default properties values
     */
    public function setDefaultProperties()
    {
        parent::setDefaultProperties();

        $this->parent = null;
        $this->active = 1;
    }

    public static function getTableName()
    {        
        return 'categories';
    }

    /**
     * Get category list for options in a select input
     */
    public static function getOptions($selected)
    {
        $options = array(
            array(
                'value' => '',
                'label' => '---'
            )
        );

        $categories = Category::findAll();
        foreach ($categories as $category) {
            $options[] = array(
                'value' => $category->id,
                'label' => $category->name
            );
        }

        return $options;
    }

    /**
     * Validate the object and fill $this->errors with error messages
     *
     * @return bool
     */
    public function valid()
    {
        $this->errors = array();

        if (!isset($this->parent) || $this->parent == '') {
            $this->parent = null;
        }

        $this->name = trim($this->name);
        if ($this->name == '') {
            $this->errors['name'] = 'Nom obligatoire';
        }

        if (!isset($this->active) || $this->active == '') {
            $this->active = 0;
        }

        return empty($this->errors);
    }
}
