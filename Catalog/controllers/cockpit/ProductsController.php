<?php
/**
 * File Catalog\controllers\cockpit\ProductsController.php
 *
 * @category Catalog
 * @package  Netoverconsulting
 * @author   Loïc Dandoy <ldandoy@overconsulting.net>
 * @license  GNU
 * @link     http://overconsulting.net
 */

namespace Catalog\controllers\cockpit;

use app\controllers\cockpit\CockpitController;
use System\Session;
use System\Router;

use Catalog\models\Product;
use Catalog\models\Category;

/**
 * Products controller
 *
 * @category Catalog
 * @package  Netoverconsulting
 * @author   Loïc Dandoy <ldandoy@overconsulting.net>
 * @license  GNU
 * @link     http://overconsulting.net
 */
class ProductsController extends CockpitController
{
    /*
     * @var Catalog\models\Product
     */
    public $product = null;

    public function indexAction()
    {
        $products = Product::findAll();

        $this->render('index', array(
            'products' => $products,
            'pageTitle' => 'Produits'
        ));
    }

    public function newAction()
    {
        if ($this->product === null) {
            $this->product = new Product();
        }

        $categoriesOptions = Category::getOptions();

        $this->render('edit', array(
            'id' => 0,
            'product' => $this->product,
            'categoriesOptions' => $categoriesOptions,
            'pageTitle' => 'Nouveau produit',
            'formAction' => Router::url('cockpit_catalog_products_create')
        ));
    }

    public function editAction($id)
    {
        if ($this->product === null) {
            $this->product = Product::findById($id);
        }

        $categoriesOptions = Category::getOptions($this->product->category_id);

        $this->render('edit', array(
            'id' => $id,
            'product' => $this->product,
            'categoriesOptions' => $categoriesOptions,
            'pageTitle' => 'Modification produit n°'.$id,
            'formAction' => Router::url('cockpit_catalog_products_update_'.$id)
        ));
    }

    public function createAction()
    {
        $this->product = new Product();

        if ($this->product->save($this->request->post)) {
            Session::addFlash('Produit ajouté', 'success');
            $this->redirect('cockpit_catalog_products');
        } else {
            Session::addFlash('Erreur(s) dans le formulaire', 'danger');
        }

        $this->newAction();
    }

    public function updateAction($id)
    {
        $this->product = Product::findById($id);

        if ($this->product->save($this->request->post)) {
            Session::addFlash('Produit modifié', 'success');
            $this->redirect('cockpit_catalog_products');
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
        $this->redirect('cockpit_catalog_products');
    }
}
