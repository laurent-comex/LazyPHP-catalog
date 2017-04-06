<?php

namespace Catalog\models;

use System\Model;

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
    }

    public function getAttachedFiles()
    {
        return array_merge(
            parent::getAttachedFiles(),
            array(
                'image' => array(
                    'type' => 'image'
                )
            )
        );
    }

    public function getValidations()
    {
        $validations = parent::getValidations();

        $validations = array_merge($validations, array(
            'name' => array(
                'type' => 'required',
                'filters' => 'trim',
                'error' => 'Nom obligatoire'
            ),
            'category_id' => array(
                'type' => 'required',
                'defaultValue' => null
            ),
            'price' => array(
                array(
                    'type' => 'required',
                    'defaultValue' => 0.0
                ),
                array(
                    'type' => 'float',
                    'error' => 'Prix invalide'
                )
            ),
            'image' => array(
                'type' => 'required',
                'error' => 'Image obligatoire'
            )
        ));

        return $validations;
    }
}
