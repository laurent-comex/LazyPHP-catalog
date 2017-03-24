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

    public function showAction()
    {
        if ($this->product === null) {
            $this->product = Product::findById($id);
        }

        $this->render('edit', array(
            'id' => $id,
            'product' => $this->product,
            'pageTitle' => 'Produit n°'.$id
        ));
    }
}
