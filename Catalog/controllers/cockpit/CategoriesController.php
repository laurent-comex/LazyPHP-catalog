<?php

namespace Catalog\controllers\cockpit;

use System\Controller;
use System\Session;
use System\Router;

use Catalog\models\Category;

class CategoriesController extends Controller
{
    /*
     * @var Catalog\models\Category
     */
    public $category = null;

    public function indexAction()
    {
        $categories = Category::findAll();

        $this->render('index', array(
            'categories' => $categories,
            'pageTitle' => 'Catégories'
        ));
    }

    public function newAction()
    {
        if ($this->category === null) {
            $this->category = new Category();
        }

        $this->render('edit', array(
            'id' => 0,
            'category' => $this->category,
            'pageTitle' => 'Nouvelle catégorie',
            'formAction' => Router::url('cockpit_catalog_categories_create')
        ));
    }

    public function editAction($id)
    {
        if ($this->category === null) {
            $this->category = Category::findById($id);
        }

        $this->render('edit', array(
            'id' => $id,
            'category' => $this->category,
            'pageTitle' => 'Modification catégorie n°'.$id,
            'formAction' => Router::url('cockpit_catalog_categories_update_'.$id)
        ));
    }

    public function createAction()
    {
        if (!isset($this->request->post['active'])) {
            $this->request->post['active'] = 0;
        }

        $this->category = new Category();
        $this->category->setData($this->request->post);

        if ($this->category->valid()) {
            if ($this->category->create((array)$this->category)) {
                Session::addFlash('Catégorie ajoutée', 'success');
                $this->redirect('cockpit_catalog_categories');
            } else {
                Session::addFlash('Erreur insertion base de données', 'danger');
            };
        } else {
            Session::addFlash('Erreur(s) dans le formulaire', 'danger');
        }

        $this->newAction();
    }

    public function updateAction($id)
    {
        if (!isset($this->request->post['active'])) {
            $this->request->post['active'] = 0;
        }

        $this->category = Category::findById($id);
        $this->category->setData($this->request->post);

        if ($this->category->valid()) {
            if ($this->category->update((array)$this->category)) {
                Session::addFlash('Catégorie modifiée', 'success');
                $this->redirect('cockpit_catalog_categories');
            } else {
                Session::addFlash('Erreur mise à jour base de données', 'danger');
            }
        } else {
            Session::addFlash('Erreur(s) dans le formulaire', 'danger');
        }

        $this->editAction($id);
    }

    public function deleteAction($id)
    {
        $category = Category::findById($id);
        $category->delete();
        Session::addFlash('Catégorie supprimé', 'success');
        $this->redirect('cockpit_catalog_categories');
    }
}
