<h1 class="page-title">{{ pageTitle }}</h1>
<div class="box box-orange">
    <div class="box-header">
        <h3 class="box-title">{{ boxTitle }}</h3>
        <div class="box-tools pull-right">
            {% button url="cockpit_catalog_products" type="secondary" icon="arrow-left" size="sm" hint="Retour" %}
        </div>
    </div>
    <div class="box-body">
        {% form_open id="formProduct" action="formAction" %}
<?php if ($selectSite): ?>
            {% input_select name="site_id" model="user.site_id" label="Site" options="siteOptions" %}
<?php endif; ?>
            {% input_select name="productcategory_id" model="product.productcategory_id" options="productcategoryOptions" label="Catégorie" %}
            {% input_text name="label" model="product.label" label="Nom" %}
            {% input_textarea name="description" model="product.description" label="Description" rows="10" %}
            {% input_text name="price" model="product.price" label="Prix" %}
            {% input_text name="quantity" model="product.quantity" label="Quantité disponible" %}
            {% input_media name="media_id" model="user.media_id" label="Image" mediaType="image" mediaCategory="product" %}
            {% input_checkbox name="active" model="product.active" label="Actif" %}
            {% input_submit name="submit" value="save" formId="formProduct" class="btn-primary" icon="save" label="Enregistrer" %}
        {% form_close %}
	</div>
</div>
