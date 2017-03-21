<?php
/**
 * File Catalog\controllers\ProductsController.php
 *
 * @category System
 * @package  Netoverconsulting
 * @author   Loïc Dandoy <ldandoy@overconsulting.net>
 * @license  GNU
 * @link     http://overconsulting.net
 */

namespace Catalog\controllers;

use System\Controller;
use System\Session;
use System\Query;
use System\Router;

use Catalog\models\Product;

/**
 * Products controller
 *
 * @category Catalog
 * @package  Netoverconsulting
 * @author   Loïc Dandoy <ldandoy@overconsulting.net>
 * @license  GNU
 * @link     http://overconsulting.net
 */
class ProductsController extends Controller
{
    /*
     * @var Catalog\models\Product
     */
    public $product = null;

    public function indexAction()
    {
        $products = Product::findAll();

        $this->render('index', array(
            'products' => $products
        ));
    }

    public function newAction()
    {
        if ($this->product === null) {
            $this->product = new Product();
        }

        $this->render('edit', array(
            'id' => 0,
            'product' => $this->product,
            'pageTitle' => 'Nouveau produit',
            'formAction' => Router::url('cockpit_products_create')
        ));
    }

    public function editAction($id)
    {
        if ($this->product === null) {
            $this->product = Product::findById($id);
        }

        $this->render('edit', array(
            'id' => $id,
            'product' => $this->product,
            'pageTitle' => 'Modification produit n°'.$id,
            'formAction' => Router::url('cockpit_products_update_'.$id)
        ));
    }

    public function createAction()
    {
        $this->product = new Product();
        $this->product->setData($this->request->post);

        if ($this->product->valid()) {
            if ($this->product->create((array)$this->product)) {
                Session::addFlash('Produit ajouté', 'success');
                $this->redirect('cockpit_products');
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
        $this->product = Product::findById($id);
        $this->product->setData($this->request->post);

        if ($this->product->valid()) {
            if ($this->product->update((array)$this->product)) {
                Session::addFlash('Produit modifié', 'success');
                $this->redirect('cockpit_products');
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
        $product = Product::findById($id);
        $product->delete();
        Session::addFlash('Produit supprimé', 'success');
        $this->redirect('cockpit_products');
    }
}
