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
        $userClass = $this->loadModel('User');
        $user = new $userClass();

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
                    $user = $userClass::findById($res->id);
                    $this->session->set($this->sessionKey, $user);
                    $this->redirect("catalog_checkout_pay");

                } else {
                    $this->addFlash('Identifiant ou mot de passe incorrect', 'danger');
                }
            }
        }


        $params = array(
            'pageTitle'     => 'Accédez à votre espace',
            'formAction'    => Router::url('catalog_checkout_login'),
            'formAction2'   => Router::url('usersauth_signup'),
            'return2'       => Router::url('catalog_checkout_pay'),
            'signupURL'     => '/users',
            'altImageLogin' => 'Default Image Login',
            'imageLogin'    => '/assets/images/default_image_login.png',
            'errors'        => $errors,
            'coach'         => $user
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
                // var_dump($payment);die;
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
                    $confirmation_sentence = "<strong>Vous recevrez un mail de confirmation ou d’annulation de séance 24h avant le début de celle-ci (La séance est annulée s’il y a moins de 3 participants inscrits)</strong>.<br/>";
                }else{
                    $confirmation_sentence="Vous recevrez un mail de confirmation ou d’annulation de séance 24h avant le début de celle-ci (La séance est annulée s’il y a moins de 3 participants inscrits).<br/>";
                }



                $contents=  ' <body>
                   <table style="background-color:rgb(42, 55, 78)" width="100%" cellspacing="0" cellpadding="0" border="0">
        <tbody>
            <tr>
                <td align="center" bgcolor="#000">
                    <table cellspacing="0" cellpadding="0" border="0">
                        <tbody>                            
                            <tr>
                                <td class="w640" width="640" height="10"></td>
                            </tr>
                            <tr>
                                <td class="w640" width="640" height="20" align="center"> <a style="color:#ffffff; font-size:12px;" href="#"><span style="color:#ffffff; font-size:12px;">
                                    Voir le contenu de ce mail en ligne</span></a> 
                                </td>
                            </tr>
                            <tr>
                                <td class="w640" width="640" height="10"></td>
                            </tr>

                            <!-- logo -->
                            <tr class="pagetoplogo">
                                <td class="w640" width="640">
                                    <table class="w640" width="640" cellspacing="0" cellpadding="0" border="0" bgcolor="#F2F0F0">
                                        <tbody>
                                            <tr>
                                                <td class="w30" width="30"></td>
                                                <td class="w580" width="580" valign="middle" align="left">
                                                    <div class="pagetoplogo-content">
                                                        <img class="w580" style="text-decoration: none; display: block; color:#476688; font-size:30px;" src="/uploads/media/1/0/10_image.jpg" alt="Mon Logo" width="580" height="108">
                                                    </div>
                                                </td> 
                                                <td class="w30" width="30"></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </td>
                            </tr>
                                
                            <!-- separateur horizontal -->
                            <tr>
                                <td class="w640" width="640" height="1" bgcolor="#d7d6d6"></td>
                            </tr>

                             <!-- contenu -->
                            <tr class="content">
                                <td class="w640" width="640" bgcolor="#ffffff">
                                    <table class="w640" width="640" cellspacing="2" cellpadding="10" border="0">
                                        <tbody>
                                            <tr>
                                                <td class="w30" width="30"></td>
                                                <td class="w580" width="580">
                                                    Bonjour ' .  $this->current_user->firstname .', <br/><br/> 
                                                    Nous vous confirmons la réservation de votre séance de ' . $item->product->activity->label . ' - '. $label_slot .  ' avec le coach ' . $item->product->coach->firstname . '.<br/><br/>            
                                                </td>
                                                <td class="w30" width="30"></td>
                                            </tr>
 
                                            <tr>
                                                <td class="w30" width="30"></td>
                                                <td class="w580" width="580" bgcolor="#F7BF17">
                                                    <br/>
                                                    <strong>Infos séance :</strong> <br/>'
                                                    //$datetimeInfos['startFormatted']
                                                    . 'Le [Date] à [Heure] <br/>
                                                     Rendez-vous au ' . $item->product->location->address . ' ' . $item->product->location->zip_code . ' ' . $item->product->location->city . '<br/> 
                                                    <br/>
                                                    </td>
                                                   <td class="w30" width="30"></td>
                                            </tr>
                                            
                                            <tr>
                                                    <td class="w30" width="30"></td>
                                                    <td class="w580" width="580">
                                                        <br/>
                                                        Il est recommandé d’arriver en tenue adaptée 5 minutes avant le début de la séance. <br/><br/> 

                                                        Vous pouvez contacter le coach ' . $item->product->coach->firstname . ' au ' . $item->product->coach->phone . ' pour faciliter votre rencontre ou poser des questions sur la séance. <br/><br/> 
                                                         ' .$confirmation_sentence . ' <br/>  <br/>
                    
                                                        <a href="http://fitnss.fr/pages/39" target="_blank">Consultez nos conditions générales d’utilisation</a> <br/><br/>
                    
                                                        Sportivement, <br/>
                                                        L’équipe FITNSS <br/>
                                                        <img style="text-decoration: none; display: block; color:#476688; font-size:30px;" src="/uploads/media/fitnss-logo_black-small.png" alt="Logo Fitnss" width="50" height="30">
                                                    </td>
                                                    <td class="w30" width="30"></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </td>
                            </tr>

                            <!--  separateur horizontal de 15px de  haut-->
                            <tr>
                                <td class="w640" width="640" height="15" bgcolor="#ffffff"></td>
                            </tr>

                            <!-- pied de page -->
                            <tr class="pagebottom">
                                <td class="w640" width="640">
                                    <table class="w640" width="640" cellspacing="0" cellpadding="0" border="0" bgcolor="#c7c7c7">
                                        <tbody>
                                            <tr>
                                                <td colspan="5" height="10"></td>
                                            </tr>
                                            <tr>
                                                <td class="w30" width="30"></td>
                                                <td class="w580" width="580" valign="top">
                                                    <p class="pagebottom-content-left" align="right">
                                                        <a style="color:#255D5C;" href="http://www.fitnss.fr"><span style="color:#255D5C;">www.fitnss.fr</span></a>
                                                    </p>
                                                </td>

                                                <td class="w30" width="30"></td>
                                            </tr>
                                            <tr>
                                                <td colspan="5" height="10"></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </td>
                            </tr>
                            <tr>
                                <td class="w640" width="640" height="60"></td>
                            </tr>
                        </tbody>
                    </table>
                </td>
            </tr>
        </tbody>
    </table>

</body>';


/*
                'Bonjour ' .  $this->current_user->firstname .', <br/><br/>

                    Nous vous confirmons la réservation de votre séance de ' . $item->product->activity->label . ' - '. $label_slot .  ' avec le coach ' . $item->product->coach->firstname . '.<br/><br/>

                    Infos séance : <br/>'
                    //$datetimeInfos['startFormatted']
                    . 'Le [Date] à [Heure] <br/>
                    Rendez-vous au ' . $item->product->location->address . ' ' . $item->product->location->zip_code . ' ' . $item->product->location->city . '<br/>
                    Il est recommandé d’arriver en tenue adaptée 5 minutes avant le début de la séance. <br/><br/>

                    Vous pouvez contacter le coach ' . $item->product->coach->firstname . ' au ' . $item->product->coach->phone . ' pour faciliter votre rencontre ou poser des questions sur la séance. <br/><br/> ' .

                    $confirmation_sentence . ' <br/>  '

                    //Une erreur ? Un empêchement ? Vous pouvez à tout moment annuler votre séance en cliquant ici (Lien vers page d’annulation)
                    .'<br/>
                    <a href="http://fitnss.fr/pages/39" target="_blank">Consultez nos conditions générales d’utilisation</a> <br/><br/>

                    Sportivement, <br/>
                    L’équipe FITNSS ';*/

                //echo $contents;die();

                //mail pour les utilisateurs ayant réservé
                Mail::send('hello@fitnss.fr', 'Contact', $this->current_user->email, $this->current_user->fullname, 'FITNSS, réservation de votre séance de '. $cart->items->product->label . ' - ' . $cart->items->product->label_slot , $contents);

                Mail::send('hello@fitnss.fr', 'Contact', 'hello@fitnss.fr', 'Contact' , 'FITNSS, réservation d\'un séance de '. $cart->items->product->label . ' - ' . $cart->items->product->label_slot , 'Une séance a été réservée par' . $this->current_user->fullname .' Pour obtenir plus d\'information, rendez-vous dans le back office de votre site.');

               /* Mail::send('hello@fitnss.fr', 'Contact', $cart->items->product->coach->email, $item->product->coach->fullname, 'FITNSS, réservation d\'une place sur votre séance de '. $cart->items->product->label . ' - ' . $item->product->label_slot , 'Une place de votre ' . $cart->items->product->label . ' - ' . $item->product->label_slot .' séance a été réservée par' . $this->current_user->fullname .' ');
                */


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
            if ($idx == $index) {
                // verifiez les dates
                $delete = true;

                if ($delete) {
                    if($item->quantity > 1){
                        $item->quantity--;
                        $cart->save();
                    } else {
                       unset($cart->items[$idx]); 
                    }

/*
                    if(isset($this->current_user->firstname)) {
                        $this->addFlash('Réservation supprimé', 'success');

                    Mail::send('hello@fitnss.fr', 'Contact', $this->current_user->email, $this->current_user->fullname, 'FITNSS, annulation de votre réservation' , 'Bonjour ' . $this->current_user->firstname . ',<br/><br/>

                        Nous avons pris en compte votre annulation pour la séance de ' . $item->product->label . ' - ' . $item->product->label_slot . '  le [Date] à [Heure].<br/>

                        Votre remboursement sera effectué suivant nos conditions générales d’utilisation dans un délai de 10 jours ouvrés. <br/>

                        Pour réserver une autre séance FITNSS, <a href="http://fitnss.fr/slots/search" target="_blank"> cliquez ici</a> <br/><br/>

                        Sportivement,<br/>
                        L’équipe FITNSS.');

                Mail::send('hello@fitnss.fr', 'Contact', 'hello@fitnss.fr', 'Contact' , 'FITNSS, annulation d\'une réservation', 'La séance de ' . $item->product->label . ' - ' . $item->product->label_slot . '  a été annulé par' . $this->current_user->fullname .' N\'oubliez pas de le recontacter pour le remboursement. ');

                Mail::send('hello@fitnss.fr', 'Contact', $item->coach->email, $item->coach->fullname, 'FITNSS, annulation d\'une réservation',  'Une place de la  séance de ' . $item->product->label . ' - ' . $item->product->label_slot . '  a été annulé par' . $this->current_user->fullname .' ');
*/




                } else {
                    $this->addFlash('Réservation non supprimé', 'error');
                }
            }
        }
        $this->redirect('catalog_checkout_cart');
    }
}
