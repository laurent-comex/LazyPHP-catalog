<h1 class="page-title">{{ pageTitle }}</h1>
<div class="box box-success">
    <div class="box-header">
        <h3 class="box-title">Ajouter un produit</h3>
        <div class="box-tools pull-right">
            {% button url="cockpit_catalog_products" type="default" icon="arrow-left" size="xs" content="" %}
        </div>
    </div>
    <div class="box-body">
{% form_open id="formProduct" action="formAction" class="form-horizontal" %}
    {% input_select name="category_id" model="product.category_id" options="categoriesOptions" label="Catégorie" %}
    {% input_text name="name" model="product.name" label="Nom" %}
    {% input_textarea name="description" model="product.description" label="Description" rows="10" %}
    {% input_text name="price" model="product.price" label="Prix" %}
    {% input_text name="quantity" model="product.quantity" label="Quantité disponible" %}
    {% input_image name="image" model="product.image" label="Image" %}
    {% input_checkbox name="active" model="product.active" label="Actif" %}
    {% input_submit name="submit" value="save" formId="formProduct" class="btn-primary" icon="save" label="Enregistrer" %}
{% form_close %}
	</div>
</div>
