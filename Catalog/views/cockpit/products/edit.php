<h1 class="page-title">{{ pageTitle }}</h1>
<div class="actions">
    {% button url="cockpit_catalog_products" type="default" icon="arrow-left" content="Retour" %}
</div>
<form id="formProduct" method="post" action="<?php echo $params['formAction']; ?>" class="form form-horizontal">
    {% input_text name="name" model="product.name" label="Nom" %}
    {% input_textarea name="description" model="product.description" label="Description" rows="10" %}
    {% input_text name="price" model="product.price" label="Prix" %}
    {% input_checkbox name="active" model="product.active" label="Actif" %}
    {% input_submit name="submit" value="save" formId="formProduct" class="btn-primary" icon="save" label="Enregistrer" %}
</form>
