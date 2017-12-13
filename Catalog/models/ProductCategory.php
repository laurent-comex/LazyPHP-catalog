<?php

namespace Catalog\models;

use Core\Model;

class ProductCategory extends Model
{
    protected $permittedColumns = array(
        'site_id',
        'label',
        'description',
        'parent',
        'position',
        'active'
    );

    public static function getTableName()
    {
        return 'productcategories';
    }

    public function setDefaultProperties()
    {
        parent::setDefaultProperties();

        $this->active = 1;
    }

    public function getValidations()
    {
        return array_merge(
            parent::getValidations(),
            array(
                'label' => array(
                    'type' => 'required',
                    'filters' => array('trim'),
                    'error' => 'Nom obligatoire'
                )
            )
        );
    }

    /**
     * Get category tree
     */
    public static function getNestedCategories($where = '')
    {
        return self::getChildren(null, true, 0, false, $where);
    }

    /**
     * Get flat category tree
     */
    public static function getFlatCategories($where = '')
    {
        return self::getChildren(null, true, 0, true, $where);
    }

    /**
     * Get category list for options in a select input
     */
    public static function getOptions($params = array())
    {
        $options = array(
            0 => array(
                'value' => '',
                'label' => '---'
            )
        );

        $where = isset($params['where']) ? $params['where'] : '';
        $categories = self::getFlatCategories($where);

        foreach ($categories as $category) {
            $options[$category->id] = array(
                'value' => $category->id,
                'label' => str_repeat('&nbsp;', $category->level * 8).$category->label
            );
        }

        return $options;
    }
}
