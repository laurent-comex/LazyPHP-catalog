<?php

namespace Catalog\models;

use Core\Model;

class Product extends Model
{
    protected $permittedColumns = array(
        'site_id',
        'productcategory_id',
        'label',
        'description',
        'price',
        'quantity',
        'media_id',
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
            'site' => array(
                'type' => '1',
                'model' => 'Core\\models\\Site',
                'key' => 'site_id'
            ),
            'productcategory' => array(
                'type' => '1',
                'model' => 'Catalog\\models\\Category',
                'key' => 'productcategory_id'
            ),
            'media' => array(
                'type' => '1',
                'model' => 'Media\\models\\Media',
                'key' => 'media_id'
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
            'quantity' => array(
                'type' => 'float',
                'error' => 'Quantité invalide'
            )
        ));

        return $validations;
    }
}
