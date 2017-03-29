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
     * Get children categories
     *
     * @param int $category_id
     * @param bool $recursive
     * @param int $level
     * @param bool $flat
     *
     * @return Catalog\models\Category[]
     */
    public static function getChildrenCategories($category_id = null, $recursive = true, $level = 0, $flat = false)
    {
        $categories = array();

        $query = new Query();
        $query->select('*');
        if ($category_id === null) {
            $query->where('parent is null');
        } else {
            $query->where('parent = '.$category_id);
        }
        $query->order('position');
        $query->from(self::getTableName());
        $categories = $query->executeAndFetchAll();

        foreach ($categories as &$category) {
            $category->level = $level;
        }

        if ($recursive) {
            if ($flat) {
                $i = 0;
                while ($i < count($categories)) {
                    $children = self::getChildrenCategories($categories[$i]->id, true, $level + 1, true);
                    if (!empty($children)) {
                        array_splice($categories, $i + 1, 0, $children);
                        $i = $i + count($children);
                    }
                    $i++;
                }
            } else {
                foreach ($categories as &$category) {
                    $category->children = self::getChildrenCategories($category->id, true, $level + 1, false);
                }
            }
        }

        return $categories;
    }

    /**
     * Get category tree
     */
    public static function getNestedCategories()
    {
        return self::getChildrenCategories(null, true, 0, false);
    }

    /**
     * Get categories
     */
    public static function getFlatCategories()
    {
        return self::getChildrenCategories(null, true, 0, true);
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
