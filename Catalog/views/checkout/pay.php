<section>
    <div class="container">
        <div class ="row">

            <div class="col-md-12">
               <h1 class="page-title">3. Paiement</h1>
                <p><strong>Veuillez confirmer les détails :</strong></p>
            </div>

            <div class="col-md-8">
                <div class="box-seance">
                    <h3>Informations Séances</h3>

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

                            foreach ($cart->items as $index => $item) {
                                
                                $price = number_format($item->product->getPrice(), 2, ',', ' ');

                                echo
                                    '<tr>'.
                                        '<td class="cart">'.$item->product->label.'</td>'.
                                        '<td>'.$price.'</td>'.
                                        '<td>'.$item->quantity.'</td>'.
                                        '<td>'.$item->getTotal().'</td>'.
                                    '</tr>';
                            }

                            echo '</table>';
                        }

                    ?>
                </div>
            </div>


            <div class="col-md-4">
                <div class="box-account">
                    <h3>Informations Utilisateur</h3>
                    <p><strong>Nom</strong> : <?php echo $this->current_user->lastname; ?></p>
                    <p><strong>Prénom</strong> : <?php echo $this->current_user->firstname; ?> </p>
                    <p><strong>Email</strong> : <?php echo $this->current_user->email; ?></p>
                    <p><strong>Téléphone</strong> : <?php echo $this->current_user->phone; ?></p>
                </div>
            </div>

                <?php
                    foreach( $cart->items as $item) {
                        $total= $item->getTotal() + $total;
                    }
                ?>

            <div class="col-md-8">
                <div class="box-total">
                     <h2>Total <?php echo $total; ?> €</h2>

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
                    {% button url="catalog_checkout_cart" type="secondary" icon="arrow-left" content="Retour au panier" %}
                    <!--{% button url="catalog_payment_mangopay" type="primary" class="btn-blue" content="PAYER" %}-->

                </div>
            </div>

        </div>
    </div>
</section>
