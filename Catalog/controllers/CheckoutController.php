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

class CheckoutController extends FrontController
{
    /**
     * @var string
     */
    public $tableName = 'users';

    /**
     * @var string
     */
    public $idField = 'email';

    /**
     * @var string
     */
    public $passwordField = 'password';

    /**
     * @var string
     */
    public $sessionKey = 'current_user';


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
        $cart->clean();
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
        $errors = array();
        $post = $this->request->post;

        if (!empty($post) && isset($post[$this->idField]) && isset($post[$this->passwordField])) {
            $id = trim($post[$this->idField]);
            $password = trim($post[$this->passwordField]);

            if ($id == '') {
                $errors[$this->idField] = 'Identifiant obligatoire';
            } else if (!filter_var($id, FILTER_VALIDATE_EMAIL)) {
                $errors[$this->idField] = 'Email invalide';
            }

            if ($password == '') {
                $errors[$this->passwordField] = 'Mot de passe obligatoire';
            }

            if (empty($errors)) {
                $query = new Query();
                $query->select('*');
                $query->where($this->idField.' = :idField');
                $query->from($this->tableName);
                $res = $query->executeAndFetch(array('idField' => $id));

                if ($res && Password::check($password, $res->password)) {
                    $userClass = $this->loadModel('User');
                    $user = $userClass::findById($res->id);
                    $this->session->set($this->sessionKey, $user);
                    $this->redirect("catalog_checkout_pay");

                } else {
                    $this->addFlash('Identifiant ou mot de passe incorrect', 'danger');
                }
            }
        }


        $params = array(
            'pageTitle' => 'Accédez à votre espace',
            'formAction' => Router::url('catalog_checkout_login'),
            'signupURL' => '/users',
            'altImageLogin' => 'Default Image Login',
            'imageLogin' => '/assets/images/default_image_login.png',
            'errors' => $errors
        );

        if (isset($id)) {
            $params[$this->idField] = $id;
        }

        $this->render(
            'catalog::checkout::login',
            $params
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

                $paymentClass = $this->loadModel('Payment');
                $order = $cart->createOrder();

                try {
                    $charge = \Stripe\Charge::create(
                        array(
                            'amount' => $stripeAmount,
                            'currency' => 'eur',
                            'description' => 'Test',
                            'source' => $this->request->post['stripeToken']
                        )
                    );
                    $status = 'success';

                } catch(\Exception $e) {
                    var_dump($e);
                    $status = 'error';
                }

                $payment = new $paymentClass();
                $payment->order_id = $order->id;
                $payment->payment_system = 'stripe';
                $payment->payment_method = 'card';
                $payment->amount = $amount;
                $payment->status = $status;
                $payment->code = $charge->id;
                $payment->site_id = $this->site->id;
                $payment->save();

                $order->status = 'paid';
                $order->save();

                // var_dump($order);

                $books = $cart->items;
                $bookClass = $this->loadModel('Book');

                foreach($books as $item) {
                    $book = new $bookClass();
                    $book->site_id = $this->site->id;
                    $book->slot_id = $item->product->id;
                    $book->user_id = $this->current_user->id;
                    $book->payment_id = $payment->id;
                    $book->cancelled = 0;
                    $book->order_id = $order->id;
                    var_dump($book);
                    $book->save();
                }

                // On vide le panier
                $cart->clean();
                $cart->save();

                // On revoit vers l'accueil ou ailleurs ?
                // $this->redirect("/");
            }

            $this->render(
                'catalog::checkout::pay',
                array(
                    'stripePublishableKey' => STRIPE_PUBLISHABLE_KEY,
                    'email' => $email,
                    'amount' => $amount,
                    'cart' => $cart,
                    'amountFormatted' => $amountFormatted,
                    'stripeAmount' => $stripeAmount
                )
            );
        } else {
            throw new \Exception('Cart is empty');
        }
    }
}
