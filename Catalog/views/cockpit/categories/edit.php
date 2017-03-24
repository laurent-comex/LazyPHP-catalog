<h1 class="page-title">{{ pageTitle }}</h1>
<div class="actions">
    {% button url="cockpit_catalog_cateogries" type="default" icon="arrow-left" content="Retour" %}
</div>
<form id="formCategory" method="post" action="{{ formAction }}" class="form form-horizontal">
    {% input_text name="name" model="category.name" label="Nom" %}
    {% input_textarea name="description" model="category.description" label="Description" rows="10" %}
    {% input_checkbox name="active" model="category.active" label="Actif" %}
    {% input_submit name="submit" value="save" formId="formCategory" class="btn-primary" icon="save" label="Enregistrer" %}
</form>
