<?php

namespace Catalog\controllers;

use app\controllers\FrontController;
use Core\Model;
use Core\Session;
use Core\Query;
use Core\Router;
use Core\Password;

use Catalog\models\Order;
use Catalog\models\Payment;
use Catalog\models\Cart;

class PaymentController extends FrontController
{


    public function mangopayAction()
    {
        $api = new MangoPay\MangoPayApi();

        
    }

}
