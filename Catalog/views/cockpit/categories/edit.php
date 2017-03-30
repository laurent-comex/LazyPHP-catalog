<h1 class="page-title">{{ pageTitle }}</h1>
<div class="actions">
    {% button url="cockpit_catalog_categories" type="default" icon="arrow-left" content="Retour" %}
</div>
{% form_open id="formCategory" action="formAction" class="form-horizontal" %}
    {% input_select name="parent" model="category.parent" options="categoriesOptions" label="Catégorie parente" %}
    {% input_text name="name" model="category.name" label="Nom" %}
    {% input_textarea name="description" model="category.description" label="Description" rows="10" %}
    {% input_checkbox name="active" model="category.active" label="Actif" %}
    {% input_submit name="submit" value="save" formId="formCategory" class="btn-primary" icon="save" label="Enregistrer" %}
{% form_close %}
