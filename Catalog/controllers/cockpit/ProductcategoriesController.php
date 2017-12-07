<?php

namespace Catalog\controllers\cockpit;

use app\controllers\cockpit\CockpitController;
use Core\Session;
use Core\Router;

use Catalog\models\ProductCategory;

class ProductcategoriesController extends CockpitController
{
    /**
     * @var Catalog\models\ProductCategory
     */
    private $productcategory = null;

    /**
     * @var string
     */
    private $pageTitle = '<i class="fa fa-object-group fa-orange"></i> Gestion des catégories de produit';

    public function indexAction()
    {
        $productcategoryClass = $this->loadModel('ProductCategory');
        $productcategories = $productcategoryClass::getFlatCategories();

        $this->render(
            'catalog::productcategories::index',
            array(
                'productcategories' => $productcategories,
                'pageTitle' => $this->pageTitle,
                'boxTitle' => 'Liste des catégories'
            )
        );
    }

    public function newAction()
    {
        $productcategoryClass = $this->loadModel('ProductCategory');
        if ($this->productcategory === null) {
            $this->productcategory = new $productcategoryClass();
        }

        $productcategoryOptions = $productcategoryClass::getOptions();

        $siteClass = $this->loadModel('Site');
        $siteOptions = $siteClass::getOptions();

        $this->render(
            'catalog::productcategories::edit',
            array(
                'id' => 0,
                'productcategory' => $this->productcategory,
                'productcategoryOptions' => $productcategoryOptions,
                'siteOptions' => $siteOptions,
                'selectSite' => $this->current_user->site_id === null,
                'pageTitle' => $this->pageTitle,
                'boxTitle' => 'Nouvelle catégorie',
                'formAction' => Router::url('cockpit_catalog_productcategories_create')
            ));
    }

    public function editAction($id)
    {
        $productcategoryClass = $this->loadModel('ProductCategory');
        if ($this->productcategory === null) {
            $this->productcategory = $productcategoryClass::findById($id);
        }

        $productcategoryOptions = $productcategoryClass::getOptions();

        $siteClass = $this->loadModel('Site');
        $siteOptions = $siteClass::getOptions();

        $this->render(
            'catalog::productcategories::edit',
            array(
                'id' => $id,
                'productcategory' => $this->productcategory,
                'productcategoryOptions' => $productcategoryOptions,
                'siteOptions' => $siteOptions,
                'selectSite' => $this->current_user->site_id === null,
                'pageTitle' => $this->pageTitle,
                'boxTitle' => 'Modification catégorie n°'.$id,
                'formAction' => Router::url('cockpit_catalog_productcategories_update_'.$id)
            )
        );
    }

    public function createAction()
    {
        $productcategoryClass = $this->loadModel('ProductCategory');
        $this->productcategory = new $productcategoryClass();

        if (!isset($this->request->post['active'])) {
            $this->request->post['active'] = 0;
        }

        if ($this->productcategory->save($this->request->post)) {
            $this->addFlash('Catégorie ajoutée', 'success');
            $this->redirect('cockpit_catalog_productcategories');
        } else {
            $this->addFlash('Erreur(s) dans le formulaire', 'danger');
        }

        $this->newAction();
    }

    public function updateAction($id)
    {
        $productcategoryClass = $this->loadModel('ProductCategory');
        $this->productcategory = $productcategoryClass::findById($id);

        if (!isset($this->request->post['active'])) {
            $this->request->post['active'] = 0;
        }

        if ($this->productcategory->save($this->request->post)) {
            $this->addFlash('Catégorie modifiée', 'success');
            $this->redirect('cockpit_catalog_productcategories');
        } else {
            $this->addFlash('Erreur(s) dans le formulaire', 'danger');
        }

        $this->editAction($id);
    }

    public function deleteAction($id)
    {
        $productcategoryClass = $this->loadModel('ProductCategory');
        $productcategory = $productcategoryClass::findById($id);
        $productcategory->delete();
        $this->addFlash('Catégorie supprimé', 'success');
        $this->redirect('cockpit_catalog_productcategories');
    }
}
