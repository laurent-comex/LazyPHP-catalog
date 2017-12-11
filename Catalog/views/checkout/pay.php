<h1 class="page-title">Paiement</h1>
<div calss="pay-amount-formatted">Total à payer : {{ amountFormatted }}</div>
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
{% button url="catalog_checkout_cart" type="secondary" icon="arrow-left" content="Retour au panier" %}