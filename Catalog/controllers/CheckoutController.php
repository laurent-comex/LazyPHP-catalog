<?php

namespace Catalog\controllers;

use app\controllers\FrontController;
use Core\Session;
use Core\Query;
use Core\Router;

use Catalog\models\Payment;
use Catalog\models\Cart;

class CheckoutController extends FrontController
{
    public function cartAction()
    {
        $cart = $this->session->get('cart');
        if ($cart === null) {
            $cart = new Cart();
        }

        $this->session->set('cart', $cart);

        $this->render(
            'catalog::checkout::cart',
            array(
                'cart' => $cart
            )
        );
    }

    public function payAction()
    {
        $cart = Cart::load();

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
                'catalog::checkout::pay',
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
