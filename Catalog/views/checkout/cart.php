<?php

echo '<h1 class="page-title">Votre panier</h1>';

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
        echo
            '<tr>'.
                '<td class="cart">'.$item->product->label.'</td>'.
                '<td>'.$item->product->price.'</td>'.
                '<td>'.$item->quantity.'</td>'.
                '<td>'.$item->getTotal().'</td>'.
            '</tr>';
    }
    echo '</table><br /><br />';
    if ($isConnected) {
        echo '{% button url="catalog_checkout_pay" type="success" icon="check" content="Valider la commande" %}<br /><br />';
    } else {
        echo '{% button url="catalog_checkout_login" type="success" icon="check" content="Valider la commande" %}<br /><br />';
    }
    echo '{% button url="catalog_checkout_emptycart" type="danger" icon="trash" content="Vider le panier !!pour test à enlever!!" %}<br /><br />';
} else {
    echo '<div class="empty-cart">Panier vide</div>';
}