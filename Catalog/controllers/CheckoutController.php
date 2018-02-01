<?php

namespace Catalog\controllers;

use app\controllers\FrontController;
use Core\Model;
use Core\Session;
use Core\Query;
use Core\Router;
use Core\Password;
use Core\Mail;

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

        //TEST
        $cartClass = $this->loadModel('Cart');
        $cart = $cartClass::load();
        $books = $cart->items;
        //$datetimeInfos = $books->product->getDatetimeInfos();
        //$datetimeInfos['startFormatted'];
        //var_export($books->getDatetimeInfos());


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
                    $book->save();
                }

                // On vide le panier
                $cart->clean();
                $cart->save();

                //envoie d'email
                $datetimeInfos = $item->product->getDatetimeInfos();
                $datetimeInfos['startFormatted'];
                if($item->product->label_slot != null) {
                   $label_slot =    $item->product->label_slot ;
                } else {
                    $label_slot = "";
                }

                //phrase en gras si scéance fitnss
                if($item->product->fitnss == 1) {
                    $confirmation_sentence = "<strong>Vous recevrez un mail de confirmation ou d’annulation de séance 24h avant le début de celle-ci (La séance est annulée s’il y a moins de 3 participants inscrits)</strong>.";
                }else{
                    $confirmation_sentence="Vous recevrez un mail de confirmation ou d’annulation de séance 24h avant le début de celle-ci (La séance est annulée s’il y a moins de 3 participants inscrits).";
                }



                $contents="Bonjour " .  $this->current_user->firstname .", <br/>

                    Nous vous confirmons la réservation de votre séance de " . $item->product->activity->label . " - ". $label_slot .  " avec le coach " . $item->product->coach->firstname . ".

                    Infos séance : ."
                    //$datetimeInfos['startFormatted']
                    . "Le [Date] à [Heure] <br/>
                    Rendez-vous au " . $item->product->location->address . ' ' . $item->product->location->zip_code . ' ' . $item->product->location->city . "
                    Il est recommandé d’arriver en tenue adaptée 5 minutes avant le début de la séance. <br/>

                    Vous pouvez contacter le coach " . $item->product->coach->firstname ." au " . $item->product->coach->phone . " pour faciliter votre rencontre ou poser des questions sur la séance. <br/> " .

                     $confirmation_sentence  . "<br/>

                     Une erreur ? Un empêchement ? Vous pouvez à tout moment annuler votre séance en cliquant ici (Lien vers page d’annulation)
                    Consultez nos conditions générales d’utilisation (Lien vers CGU/CGV) <br/>

                    Sportivement,
                    L’équipe FITNSS.";

//var_dump( $contents);die();
                //mail pour les utilisateurs ayant réservé
                Mail::send('contact@fitnss.fr', 'Contact', $this->current_user->email, $this->current_user->fullname, 'FITNSS, réservation de votre séance de '. $item->product->label . ' - ' . $item->product->label_slot , $contents);

                Mail::send('contact@fitnss.fr', 'Contact', 'contact@fitnss.fr', 'Contact' , 'FITNSS, réservation d\'un séance de '. $item->product->label . ' - ' . $item->product->label_slot , 'Une séance a été réservée par' . $this->current_user->fullname .' Pour obtenir plus d\'information, rendez-vous dans le back office de votre site.');

                Mail::send('contact@fitnss.fr', 'Contact', $item->product->coach->email, $item->product->coach->fullname, 'FITNSS, réservation d\'une place sur votre séance de '. $item->product->label . ' - ' . $item->product->label_slot , 'Une place de votre ' . $item->product->label . ' - ' . $item->product->label_slot .' séance a été réservée par' . $this->current_user->fullname .' ');



                // On revoit vers l'accueil ou ailleurs ?
                $this->redirect("user");
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


    public function deleteAction($index)
    {
        //session > supprimer dedans ?
        //vardump($_SESSION);

        $cartClass = $this->loadModel('Cart');
        $cart = $cartClass::load();
       /* > ok

        $cart->clean();
        $cart->save();
        >a changer pour le for each

        $this->render(
            'catalog::checkout::cart',
            array(
                'cart' => $cart
            )
        );*/
        $index = $index - 1 ;

        foreach ($cart->items as $idx => $item) {
            echo $item->product->label . '<br />';
            if ($idx == $index) {
                unset($item[$idx]);
            }
            echo $item->product->label . '<br />';
        }



        echo $index;

        $this->addFlash('Réservation supprimé', 'success');
        // $this->redirect('user');

    }
}
