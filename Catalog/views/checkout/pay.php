

<h1 class="page-title">Paiement</h1>


    <h1 >Veuillez confirmer les détails</h1>
    <hr/>
    <h2>Informations Utilisateur</h2>
    <p> Nom : <?php echo $this->current_user->lastname; ?></p> 
    <p> Prénom : <?php echo $this->current_user->firstname; ?> </p>
    <p> Email : <?php echo $this->current_user->email; ?></p>
    <p> Téléphone : <?php echo $this->current_user->phone; ?></p>
    <hr/>
    <h2>Informations Séances</h2>

<?php

    $total = 0;
    if (!empty($cart->items)) {
        echo 
            '<table class="table cart">'.
                '<tr>'.
                    '<th>Produit</th>'.
                    '<th>Prix unit.</th>'.
                    '<th>Quantité</th>'.
                    '<th>Prix</th>'.
                '</tr>';

        foreach ($cart->items as $item) {
            if($item->product->price != '') {
                    $price = $item->product->price;
                } else {
                    $price = $item->product->coach->price;
                }
            $price = $price/3 +3;
            echo
                '<tr>'.
                    '<td class="cart">'.$item->product->label.'</td>'.
                    '<td>'.round($price, 2).'</td>'.
                    '<td>'.$item->quantity.'</td>'.
                    '<td>'.$item->getTotal().'</td>'.
                '</tr>';
        }


        echo '</table><br /><br />';
    }
        

?>

<hr/>
    <h2>Total</h2>

<?php
        foreach( $cart->items as $item) {

            echo $item->product->activity->label . '/' . $item->product->location->name . ' ';
            echo  $item->getTotal().' €';
            echo '<br />';

            $total= $item->getTotal() + $total;
        }



        echo '<p>Total = {{ amountFormatted }} € </p> ';
?>



<br />
<form action="/catalog/checkout/pay" method="post">
    <script
        src="https://checkout.stripe.com/checkout.js" class="stripe-button"
        data-key="{{ stripePublishableKey }}"
        data-amount="{{ stripeAmount }}"
        data-name="Fitnss"
        data-description="Widget"
        data-image="https://stripe.com/img/documentation/checkout/marketplace.png"
        data-locale="fr"
        data-zip-code="false"
        data-currency="eur"
        data-email="{{ email }}"
        data-label="Payer par carte">
    </script>
</form>
<br />
<br />

{% button url="catalog_payment_mangopay" type="primary" content="Mango Paiement" %}

{% button url="catalog_checkout_cart" type="secondary" icon="arrow-left" content="Retour au panier" %}