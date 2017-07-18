<h1 class="page-title">{{ titlePage }}</h1>

<div class="box box-orange">
    <div class="box-header">
        <h3 class="box-title">{{ titleBox }}</h3>

        <div class="box-tools pull-right">
            {% button url="cockpit_catalog_categories_new" type="success" icon="plus" size="sm" content="" %}
        </div>
    </div>
    <div class="box-body">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th width="10%"></th>
                    <th>Nom</th>
                    <th>Position</th>
                    <th>Actif</th>
                    <th width="10%">Actions</th>
                </tr>
            </thead>
            <tbody>
<?php
foreach ($params['categories'] as $category) {
    $level = '<span style="font-family: monospace;">'.str_repeat('&nbsp;', $category->level * 4).'|__</span>';

    if ($category->active == 1) {
        $active = '<span class="label label-success">Activé</span>';
    } else {
        $active = '<span class="label label-danger">Désactivé</span>';
    }

    $position = '{% button id="category_"'.$category->id.'_down" class="btn-position-down" size="sm" icon="arrow-up" %}{% button id="category_"'.$category->id.'_up" class="btn-postion-up" size="sm" icon="arrow-down" %}';

    echo
        '<tr>'.
            '<td>'.$level.'</td>'.
            '<td>'.$category->name.'</td>'.
            '<td>'.$position.'</td>'.
            '<td>'.$active.'</td>'.
            '<td>';?>
                {% button url="cockpit_catalog_categories_edit_<?php echo $category->id ?>" type="info" size="sm" icon="pencil" content="" %}
                {% button url="cockpit_catalog_categories_delete_<?php echo $category->id ?>" type="danger" size="sm" icon="trash-o" confirmation="Vous confirmer vouloir supprimer cette category?" %}
<?php
    echo
            '</td>'.
        '</tr>';
}
?>
            </tbody>
        </table>
    </div>
</div>