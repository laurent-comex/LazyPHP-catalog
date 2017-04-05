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

    public function getValidations()
    {
        $validations = parent::getValidations();

        $validations = array_merge($validations, array(
            'name' => array(
                'type' => 'required',
                'filters' => 'trim',
                'error' => 'Nom obligatoire'
            )
        ));

        return $validations;
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
     * Get flat category tree
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
}
