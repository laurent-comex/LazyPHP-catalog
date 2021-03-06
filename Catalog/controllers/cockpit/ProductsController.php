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
use Catalog\models\ProductCategory;

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
        if ($this->site !== null) {
            $where = 'site_id = '.$this->site->id;
        } else {
            $where = '';
        }

        $productClass = $this->loadModel('Product');
        $products = $productClass::findAll($where);

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
        $productClass = $this->loadModel('Product');
        if ($this->product === null) {
            $this->product = new $productClass();
        }

        if ($this->site !== null) {
            $where = 'site_id = '.$this->site->id;
        } else {
            $where = '';
        }

        $productcategoryClass = $this->loadModel('ProductCategory');
        $productcategoryOptions = $productcategoryClass::getOptions(array('where' => $where));

        $siteClass = $this->loadModel('Site');
        $siteOptions = $siteClass::getOptions();

        $this->render(
            'catalog::products::edit',
            array(
                'id' => 0,
                'product' => $this->product,
                'productcategoryOptions' => $productcategoryOptions,
                'siteOptions' => $siteOptions,
                'selectSite' => $this->current_user->site_id === null,
                'pageTitle' => $this->pageTitle,
                'boxTitle' => 'Nouveau produit',
                'formAction' => Router::url('cockpit_catalog_products_create')
            )
        );
    }

    public function editAction($id)
    {
        $productClass = $this->loadModel('Product');
        if ($this->product === null) {
            $this->product = $productClass::findById($id);
        }

        if ($this->site !== null) {
            $where = 'site_id = '.$this->site->id;
        } else {
            $where = '';
        }

        $productcategoryClass = $this->loadModel('ProductCategory');
        $productcategoryOptions = $productcategoryClass::getOptions(array('where' => $where);

        $siteClass = $this->loadModel('Site');
        $siteOptions = $siteClass::getOptions();

        $this->render(
            'catalog::products::edit',
            array(
                'id' => $id,
                'product' => $this->product,
                'productcategoryOptions' => $productcategoryOptions,
                'siteOptions' => $siteOptions,
                'selectSite' => $this->current_user->site_id === null,
                'pageTitle' => $this->pageTitle,
                'titleBox' => 'Modification produit n°'.$id,
                'formAction' => Router::url('cockpit_catalog_products_update_'.$id)
            )
        );
    }

    public function createAction()
    {
        $productClass = $this->loadModel('Product');
        $this->product = new $productClass();

        if (!isset($this->request->post['site_id'])) {
            $this->request->post['site_id'] = $this->site->id;
        }

        if (!isset($this->request->post['active'])) {
            $this->request->post['active'] = 0;
        }

        if ($this->request->post['quantity'] == '') {
            $this->request->post['quantity'] = null;
        }

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
        $productClass = $this->loadModel('Product');
        $this->product = $productClass::findById($id);

        if (!isset($this->request->post['site_id'])) {
            $this->request->post['site_id'] = $this->site->id;
        }

        if (!isset($this->request->post['active'])) {
            $this->request->post['active'] = 0;
        }

        if ($this->request->post['quantity'] == '') {
            $this->request->post['quantity'] = null;
        }

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
        $productClass = $this->loadModel('Product');
        $product = $productClass::findById($id);
        $product->delete();
        $this->addFlash('Produit supprimé', 'success');
        $this->redirect('cockpit_catalog_products');
    }
}
