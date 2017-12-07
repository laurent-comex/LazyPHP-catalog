<?php

if (!empty($cart->items)) {
	foreach ($cart->items as $item) {
		echo $item->product->label.' / '.$item->product->price.' / '.$item->quantity.' / Total: '.$item->product->price * $item->quantity.' / <br /><br />';
	}

	echo '<br /><br /><a class="btn btn-success" href="/catalog/checkout/pay">Valider la commande</a>';
} else {
	echo 'Panier vide';
}