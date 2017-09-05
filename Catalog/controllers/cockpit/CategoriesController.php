<?php

namespace Catalog\controllers\cockpit;

use app\controllers\cockpit\CockpitController;
use Core\Session;
use Core\Router;

use Catalog\models\Category;

class CategoriesController extends CockpitController
{
    /**
     * @var string
     */
    private $pageTitle = '<i class="fa fa-object-group fa-orange"></i> Gestion des catégories de produit';

    /*
     * @var Catalog\models\Category
     */
    public $category = null;

    public function indexAction()
    {
        $categories = Category::getFlatCategories();

        $this->render(
            'catalog::categories::index',
            array(
                'categories' => $categories,
                'pageTitle' => $this->pageTitle,
                'boxTitle' => 'Liste des catégories'
            )
        );
    }

    public function newAction()
    {
        if ($this->category === null) {
            $this->category = new Category();
        }

        $categoriesOptions = Category::getOptions();

        $this->render(
            'catalog::categories::edit',
            array(
                'id' => 0,
                'category' => $this->category,
                'categoriesOptions' => $categoriesOptions,
                'pageTitle' => $this->pageTitle,
                'boxTitle' => 'Nouvelle catégorie',
                'formAction' => Router::url('cockpit_catalog_categories_create')
            ));
    }

    public function editAction($id)
    {
        if ($this->category === null) {
            $this->category = Category::findById($id);
        }

        $categoriesOptions = Category::getOptions();

        $this->render(
            'catalog::categories::edit',
            array(
                'id' => $id,
                'category' => $this->category,
                'categoriesOptions' => $categoriesOptions,
                'pageTitle' => $this->pageTitle,
                'boxTitle' => 'Modification catégorie n°'.$id,
                'formAction' => Router::url('cockpit_catalog_categories_update_'.$id)
            )
        );
    }

    public function createAction()
    {
        $this->category = new Category();

        if (!isset($this->request->post['active'])) {
            $this->request->post['active'] = 0;
        }

        if ($this->category->save($this->request->post)) {
            $this->addFlash('Catégorie ajoutée', 'success');
            $this->redirect('cockpit_catalog_categories');
        } else {
            $this->addFlash('Erreur(s) dans le formulaire', 'danger');
        }

        $this->newAction();
    }

    public function updateAction($id)
    {
        $this->category = Category::findById($id);

        if (!isset($this->request->post['active'])) {
            $this->request->post['active'] = 0;
        }

        if ($this->category->save($this->request->post)) {
            $this->addFlash('Catégorie modifiée', 'success');
            $this->redirect('cockpit_catalog_categories');
        } else {
            $this->addFlash('Erreur(s) dans le formulaire', 'danger');
        }

        $this->editAction($id);
    }

    public function deleteAction($id)
    {
        $category = Category::findById($id);
        $category->delete();
        $this->addFlash('Catégorie supprimé', 'success');
        $this->redirect('cockpit_catalog_categories');
    }
}
