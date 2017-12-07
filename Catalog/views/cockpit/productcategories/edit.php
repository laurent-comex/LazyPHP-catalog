<h1 class="page-title">{{ pageTitle }}</h1>

<div class="box box-orange">
    <div class="box-header">
        <h3 class="box-title">{{ boxTitle }}</h3>

        <div class="box-tools pull-right">
            {% button url="cockpit_catalog_productcategories" type="secondary" size="sm" icon="arrow-left" hint="Retour" %}
        </div>
    </div>
    <div class="box-body">
        {% form_open id="formProductCategory" action="formAction" %}
<?php if ($selectSite): ?>
            {% input_select name="site_id" model="user.site_id" label="Site" options="siteOptions" %}
<?php endif; ?>
            {% input_select name="parent" model="productcategory.parent" options="productcategoryOptions" label="Catégorie parente" %}
            {% input_text name="label" model="productcategory.label" label="Nom" %}
            {% input_textarea name="description" model="productcategory.description" label="Description" rows="10" %}
            {% input_checkbox name="active" model="productcategory.active" label="Actif" %}
            {% input_submit name="submit" value="save" formId="formProductCategory" class="btn-primary" icon="save" label="Enregistrer" %}
        {% form_close %}
    </div>
</div>
