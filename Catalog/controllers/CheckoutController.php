<?php

namespace Catalog\controllers;

use app\controllers\FrontController;
use Core\Model;
use Core\Session;
use Core\Query;
use Core\Router;

use Catalog\models\Order;
use Catalog\models\Payment;
use Catalog\models\Cart;

class CheckoutController extends FrontController
{
    public function cartAction()
    {
        $cartClass = $this->loadModel('Cart');
        $cart = $cartClass::load();

        $this->render(
            'catalog::checkout::cart',
            array(
                'cart' => $cart,
                'isConnected' => $this->current_user !== null
            )
        );
    }

    public function emptycartAction()
    {
        $cartClass = $this->loadModel('Cart');
        $cart = $cartClass::load();
        $cart->empty();
        $cart->save();

        $this->render(
            'catalog::checkout::cart',
            array(
                'cart' => $cart
            )
        );
    }

    public function loginAction()
    {
        $this->render(
            'catalog::checkout::cart',
            array(
                'cart' => $cart
            )
        );
    }

    public function payAction()
    {
        $cartClass = $this->loadModel('Cart');
        $cart = $cartClass::load();

        if ($cart != null) {
            $amount = $cart->getTotal();
            $amountFormatted = number_format($amount, 2, ',', ' ').' €';
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

                    $order = $cart->createOrder();

                    $paymentClass = $this->loadModel('Payment');
                    $payment = new $paymentClass();

                    $payment->order_id = $order->id;
                    $payment->save();
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
                    'amountFormatted' => $amountFormatted,
                    'stripeAmount' => $stripeAmount
                )
            );
        } else {
            throw new \Exception('Cart is empty');
        }
    }
}
