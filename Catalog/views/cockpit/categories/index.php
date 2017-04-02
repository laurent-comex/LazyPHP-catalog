<h1 class="page-title"><i class="fa fa-object-group"></i> {{ pageTitle }}</h1>

<div class="box box-success">
    <div class="box-header">
        <h3 class="box-title">Liste des Catégories</h3>

        <div class="box-tools pull-right">
            {% button url="cockpit_catalog_categories_new" type="success" icon="plus" content="" class="btn-xs" %}
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
    $level = '<span style="font-family: monospace;">'.str_repeat('&nbsp;', $category->level * 4).'|___</span>';

    if ($category->active == 1) {
        $active = '<i class="fa fa-check"></i>';
    } else {
        $active = '<i class="fa fa-times"></i>';
    }

    $position = '{% button id="category_"'.$category->id.'_down" class="btn-position-down" size="xs" icon="caret-up" %}{% button id="category_"'.$category->id.'_up" class="btn-postion-up" size="xs" icon="caret-down" %}';

    echo
        '<tr>'.
            '<td>'.$level.'</td>'.
            '<td>'.$category->name.'</td>'.
            '<td>'.$position.'</td>'.
            '<td>'.$active.'</td>'.
            '<td>';?>
                {% button url="cockpit_catalog_categories_edit_<?php echo $category->id ?>" type="primary" size="xs" icon="pencil" content="" %}
                {% button url="cockpit_catalog_categories_delete_<?php echo $category->id ?>" type="danger" size="xs" icon="trash-o" confirmation="Vous confirmer vouloir supprimer cette category?" %}
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