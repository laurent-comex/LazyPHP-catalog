<section>
    <div  class="container">
        <div class="row">

            <div class="col-md-12">
                <h1 class="page-title">1. Votre panier</h1>
            </div>

            <div class="col-md-12 cart-details">
                <div class="col-md-12 cart-list">
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
                                        '<th> </th>'.
                                    '</tr>';
                            foreach ($cart->items as $index => $item) {
                                
                                $price = number_format($item->product->getPrice(), 2);

                                echo
                                    '<tr>'.
                                        '<td class="cart">'.$item->product->label.'</td>'.
                                        '<td>'.$price.'</td>'.
                                        '<td>'.$item->quantity.'</td>'.
                                        '<td>'.$item->getTotal().'</td>'.
                                        '<td> {% button url="catalog_checkout_delete_'.($index+1).'" type="danger" size="sm" icon="trash-o" confirmation="Vous confirmer vouloir supprimer cette réservation ?" hint="Supprimer" %} </td>'.
                                    '</tr>';
                            }

                            echo '</table>';
                    ?>
                </div>

                <?php
                    foreach( $cart->items as $item) {
                        $total= $item->getTotal() + $total;
                    }
                ?>

                <div class="col-md-6 total">
                    <hr>
                    <h2>Total <?php echo $total; ?> €</h2>

                    <p>
                        <span>En cliquant sur le bouton, vous acceptez <a href="/pages/39" target="_blank">les conditions générales de FITNSS</a></span>
                        <br/><input type="checkbox" name="annulation"  id="checkAnnulation"> <span>J'ai lu et j'accepte les conditions d'annulation</span> </input>
                    </p>

                    <?php 
                            if(isset( $_POST['annulation'])  && $_POST['annulation']=="ok") {
                                $link = 'chemin 1';  
                            } else {
                                // Rester sur la page + erreur 
                                $link= '';
                            }


                        if ($isConnected) {
                            echo '{% button url="catalog_checkout_pay" type="success" icon="check" content="Valider la commande" class="validCart" %}<br /><br />';
                        } else {
                            echo '{% button url="catalog_checkout_login" type="success" icon="check" content="Valider la commande" class="validCart" %}<br /><br />';
                        }
                        /*
                        echo '{% button url="catalog_checkout_emptycart" type="danger" icon="trash" content="Vider le panier !!pour test à enlever!!" %}<br /><br />';
                        */
                    } else {
                        echo '<div class="empty-cart">Panier vide</div>';
                    }

                    ?>
                </div>
            </div>

        </div>
    </div>
</section>

<script>

    $( ".validCart" ).click(function( event) {

        if (!$('#checkAnnulation').is(":checked"))
            {
                event.preventDefault();
                alert('Merci d\'accepter les conditions d\'annulation');
            } 

    });


</script>
