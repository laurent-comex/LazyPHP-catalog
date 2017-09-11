<h1 class="page-title">{{ pageTitle }}</h1>

<div class="box box-orange">
    <div class="box-header">
        <h3 class="box-title">{{ boxTitle }}</h3>
        <div class="box-tools pull-right">
            {% button url="cockpit_catalog_products_new" type="success" icon="plus" size="sm" hint="Retour" %}
        </div>
    </div>
    <div class="box-body">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th width="1%">ID</th>
                    <th>Catégorie</th>
                    <th>Nom</th>
                    <th>Prix</th>
                    <th>Actif</th>
                    <th width="10%">Actions</th>
                </tr>
            </thead>
            <tbody>
<?php
foreach ($params['products'] as $product) {
    if ($product->active == 1) {
        $active = '<i class="fa fa-check"></i>';
    } else {
        $active = '<i class="fa fa-times"></i>';
    }

    $categoryName = $product->category != null ? $product->category->name : '';

    echo
        '<tr>'.
            '<td>'.$product->id.'</td>'.
            '<td>'.$categoryName.'</td>'.
            '<td>'.$product->name.'</td>'.
            '<td>'.$product->price.'</td>'.
            '<td>'.$active.'</td>'.
            '<td>';?>
                {% button url="cockpit_catalog_products_edit_<?php echo $product->id ?>" type="info" size="sm" icon="pencil" hint="Retour" %}
                {% button url="cockpit_catalog_products_delete_<?php echo $product->id ?>" type="danger" size="sm" icon="trash-o" hint="Retour" confirmation="Vous confirmer vouloir supprimer ce produit?" %}
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