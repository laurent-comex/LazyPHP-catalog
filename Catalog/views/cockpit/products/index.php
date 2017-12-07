<h1 class="page-title">{{ pageTitle }}</h1>

<div class="box box-orange">
    <div class="box-header">
        <h3 class="box-title">{{ boxTitle }}</h3>
        <div class="box-tools pull-right">
            {% button url="cockpit_catalog_products_new" type="success" icon="plus" size="sm" hint="Ajouter" %}
        </div>
    </div>
    <div class="box-body">
        <table class="table table-hover table-sm datatable">
            <thead>
                <tr>
                    <th width="1%">#</th>
                    <th>Nom</th>
                    <th>Prix</th>
                    <th>Quantité</th>
                    <th>Catégorie</th>
                    <th>Actif</th>
                    <th width="10%">Actions</th>
                </tr>
            </thead>
            <tbody>
<?php
foreach ($params['products'] as $product) {
    if ($product->active == 1) {
        $active = '<span class="badge badge-success">Activé</span>';
    } else {
        $active = '<span class="badge badge-danger">Désactivé</span>';
    }

    $categoryName = $product->productcategory_id !== null ? $product->productcategory->label : '';

    $quantity = $product->quantity !== null ? $product->quantity : '∞';

    echo
        '<tr>'.
            '<td>'.$product->id.'</td>'.
            '<td>'.$product->label.'</td>'.
            '<td>'.number_format($product->price, 2).'</td>'.
            '<td>'.$quantity.'</td>'.
            '<td>'.$categoryName.'</td>'.
            '<td>'.$active.'</td>'.
            '<td>';?>
                {% button url="cockpit_catalog_products_edit_<?php echo $product->id ?>" type="info" size="sm" icon="pencil" hint="Modifier" %}
                {% button url="cockpit_catalog_products_delete_<?php echo $product->id ?>" type="danger" size="sm" icon="trash-o" confirmation="Vous confirmer vouloir supprimer ce produit?" hint="Supprimer" %}
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