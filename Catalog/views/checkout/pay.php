<br />
<h1 class="page-title">Paiement</h1>
Total à payer : {{ amount }}
<br />
<br />
<br />
<form action="/checkout" method="post">
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
<br />
