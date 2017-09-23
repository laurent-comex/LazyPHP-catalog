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
use Core\Session;
use Core\Router;

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
    /**
     * @var Catalog\models\Product
     */
    private $product = null;

    /**
     * @var string
     */
    private $pageTitle = '<i class="fa fa-list"></i> Produits';

    public function indexAction()
    {
        $products = Product::findAll();

        $this->render(
            'catalog::products::index',
            array(
                'products' => $products,
                'pageTitle' => $this->pageTitle,
                'boxTitle' => 'Liste des produits'
            )
        );
    }

    public function newAction()
    {
        if ($this->product === null) {
            $this->product = new Product();
        }

        $categoriesOptions = Category::getOptions();

        $this->render(
            'catalog::products::edit',
            array(
                'id' => 0,
                'product' => $this->product,
                'categoriesOptions' => $categoriesOptions,
                'pageTitle' => $this->pageTitle,
                'boxTitle' => 'Nouveau produit',
                'formAction' => Router::url('cockpit_catalog_products_create')
            )
        );
    }

    public function editAction($id)
    {
        if ($this->product === null) {
            $this->product = Product::findById($id);
        }

        $categoriesOptions = Category::getOptions($this->product->category_id);

        $this->render(
            'catalog::products::edit',
            array(
                'id' => $id,
                'product' => $this->product,
                'categoriesOptions' => $categoriesOptions,
                'pageTitle' => $this->pageTitle,
                'titleBox' => 'Modification produit n°'.$id,
                'formAction' => Router::url('cockpit_catalog_products_update_'.$id)
            )
        );
    }

    public function createAction()
    {
        $this->product = new Product();

        if ($this->product->save($this->request->post)) {
            $this->addFlash('Produit ajouté', 'success');
            $this->redirect('cockpit_catalog_products');
        } else {
            $this->addFlash('Erreur(s) dans le formulaire', 'danger');
        }

        $this->newAction();
    }

    public function updateAction($id)
    {
        $this->product = Product::findById($id);

        if ($this->product->save($this->request->post)) {
            $this->addFlash('Produit modifié', 'success');
            $this->redirect('cockpit_catalog_products');
        } else {
            $this->addFlash('Erreur(s) dans le formulaire', 'danger');
        }

        $this->editAction($id);
    }

    public function deleteAction($id)
    {
        $product = Product::findById($id);
        $product->delete();
        $this->addFlash('Produit supprimé', 'success');
        $this->redirect('cockpit_catalog_products');
    }
}
