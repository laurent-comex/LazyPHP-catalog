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
        'position',
        'active'
    );

    /**
     * Set default properties values
     */
    public function setDefaultProperties()
    {
        parent::setDefaultProperties();

        $this->parent = null;
        $this->position = 0;
        $this->active = 1;
    }

    public static function getTableName()
    {
        return 'categories';
    }

    /**
     * Get category tree
     */
    public static function getNestedCategories()
    {
        return self::getChildren(null, true, 0, false);
    }

    /**
     * Get categories
     */
    public static function getFlatCategories()
    {
        return self::getChildren(null, true, 0, true);
    }

    /**
     * Get category list for options in a select input
     */
    public static function getOptions()
    {
        $options = array(
            0 => array(
                'value' => '',
                'label' => '---'
            )
        );

        $categories = self::getFlatCategories();

        foreach ($categories as $category) {
            $options[$category->id] = array(
                'value' => $category->id,
                'label' => str_repeat('&nbsp;', $category->level * 8).$category->name
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

        if (!isset($this->position) || $this->position == '') {
            $this->position = 0;
        }

        return empty($this->errors);
    }
}
