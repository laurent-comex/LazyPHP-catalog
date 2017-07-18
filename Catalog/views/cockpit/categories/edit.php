<h1 class="page-title">{{ titlePage }}</h1>

<div class="box box-orange">
    <div class="box-header">
        <h3 class="box-title">{{ titleBox }}</h3>

        <div class="box-tools pull-right">
            {% button url="cockpit_catalog_categories" type="secondary" size="sm" icon="arrow-left" content="" %}
        </div>
    </div>
    <div class="box-body">
        {% form_open id="formCategory" action="formAction" %}
            {% input_select name="parent" model="category.parent" options="categoriesOptions" label="Catégorie parente" %}
            {% input_text name="name" model="category.name" label="Nom" %}
            {% input_textarea name="description" model="category.description" label="Description" rows="10" %}
            {% input_checkbox name="active" model="category.active" label="Actif" %}
            {% input_submit name="submit" value="save" formId="formCategory" class="btn-primary" icon="save" label="Enregistrer" %}
        {% form_close %}
    </div>
</div>
