<?php

namespace Catalog\controllers;

use app\controllers\FrontController;
use Core\Session;
use Core\Query;
use Core\Router;

use Catalog\models\Payment;

class CheckoutController extends FrontController
{
    public function indexAction()
    {
        $cart = new \Catalog\models\Cart();
        $this->session->set('cart', $cart); 

        $cart = $this->session->get('cart');
        $cart->addItem(
            new \Catalog\models\Product(
                array('id' => 1, 'name' => 'P1', 'description' => 'Product 1', 'price' => 111.11)
            ),
            1
        );

        if ($cart != null) {
            $amount = $cart->getTotal();
            $stripeAmount = (int)($amount * 100);

            $email = $this->current_user !== null ? $this->current_user->email : '';

            if (isset($this->request->post['stripeToken'])) {
                \Stripe\Stripe::setApiKey(STRIPE_SECRET_KEY);

                // $customer = \Stripe\Customer::create(
                //     array(
                //         'email' => $this->request->post['stripeEmail'],
                //         'token' => $this->request->post['stripeToken']
                //     )
                // );

                try {
                    $charge = \Stripe\Charge::create(
                        array(
//                            'customer' => $customer->id,
                            'amount' => $stripeAmount,
                            'currency' => 'eur',
                            'description' => 'Test',
                            'source' => $this->request->post['stripeToken']
                        )
                    );
                    \Core\debug($charge, false);
                } catch(\Exception $e) {
                    var_dump($e);
                }
            }

            $this->render(
                'catalog::checkout::index',
                array(
                    'stripePublishableKey' => STRIPE_PUBLISHABLE_KEY,
                    'email' => $email,
                    'amount' => $amount,
                    'stripeAmount' => $stripeAmount
                )
            );
        } else {
            throw new \Exception('Cart is empty');
        }
    }
}
