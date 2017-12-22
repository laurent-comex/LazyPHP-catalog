<?php

echo '<h1 class="page-title">Votre panier</h1>';


?>


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



        echo '<p>Total = ' . $total . ' € </p> ';
?>

    <p>En cliquant sur le bouton, vous acceptez les conditions générales de FITNSS</p>
    <input type="checkbox" name="annulation"  id="checkAnnulation"> J'ai lu et j'accepte les conditions d'annulation </input>
    <br/>

<?php 
        if(isset( $_POST['annulation'])  && $_POST['annulation']=="ok") {
            $link = 'chemin 1';  
        } else {
            // Rester sur la page + erreur 
            $link= '';
        }


    if ($isConnected) {
        echo '{% button url="catalog_checkout_pay" type="success" icon="check" content="Valider la commande" id="validCart" %}<br /><br />';
    } else {
        echo '{% button url="catalog_checkout_login" type="success" icon="check" content="Valider la commande" %}<br /><br />';
    }
    echo '{% button url="catalog_checkout_emptycart" type="danger" icon="trash" content="Vider le panier !!pour test à enlever!!" %}<br /><br />';
} else {
    echo '<div class="empty-cart">Panier vide</div>';
}

?>

<script>

    $( "#validCart" ).click(function( event) {

        if (!$('#checkAnnulation').is(":checked"))
            {
                event.preventDefault();
                alert('Merci d\'accepter les conditions d\'annulation');
            } 

    });


</script>
