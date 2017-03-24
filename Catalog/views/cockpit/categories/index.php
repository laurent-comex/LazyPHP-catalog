<h1 class="page-title">{{ pageTitle }}</h1>
<div class="actions">
    {% button url="cockpit_catalog_categories_new" type="success" icon="plus" content="Ajouter une catégorie" %}
</div>
<table class="table table-hover">
    <thead>
        <tr>
            <th width="1%">ID</th>
            <th>Parent</th>
            <th>Nom</th>
            <th>Actif</th>
            <th width="10%">Action</th>
        </tr>
    </thead>
    <tbody>
<?php
foreach ($params['categories'] as $category) {
    if ($category->active == 1) {
        $active = '<i class="fa fa-check"></i>';
    } else {
        $active = '<i class="fa fa-times"></i>';
    }
    echo 
        '<tr>'.
            '<td>'.$category->id.'</td>'.
            '<td>'.$category->parent.'</td>'.
            '<td>'.$category->name.'</td>'.
            '<td>'.$active.'</td>'.
            '<td>';?>
                {% button url="cockpit_catalog_categories_edit_<?php echo $category->id ?>" type="primary" size="xs" icon="pencil" content="" %}
                {% button url="cockpit_catalog_categories_delete_<?php echo $category->id ?>" type="danger" size="xs" icon="trash-o" confirmation="Vous confirmer vouloir supprimer cette category?" %}<?php
    echo 
            '</td>'.
        '</tr>';
}
?>
    </tbody>
</table>