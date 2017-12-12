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

use Core\Controller;
use Core\Session;
use Core\Query;
use Core\Router;

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
    /**
     * @var Catalog\models\Product
     */
    public $product = null;

    public function indexAction()
    {
        if ($this->site !== null) {
            $where = 'site_id = '.$this->controller->site->id;
        } else {
            $where = '';
        }

        $productClass = $this->load('Product');
        $products = $productClass::findAll($where);

        $this->render(
            'catalog::products::index',
            array(
                'products' => $products
            )
        );
    }

    public function showAction()
    {
        $productClass = $this->load('Product');
        if ($this->product === null) {
            $this->product = $productClass::findById($id);
        }

        $this->render(
            'catalog::products::show',
            array(
                'id' => $id,
                'product' => $this->product,
                'pageTitle' => 'Produit n°'.$id
            )
        );
    }
}
