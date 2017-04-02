<h1 class="page-title"><i class="fa fa-product-hunt"></i> {{ pageTitle }}</h1>

<div class="box box-success">
    <div class="box-header">
        <h3 class="box-title">Liste des Produits</h3>

        <div class="box-tools pull-right">
            {% button url="cockpit_catalog_products_new" type="success" icon="plus" class="btn-xs" content="" %}
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
                {% button url="cockpit_catalog_products_edit_<?php echo $product->id ?>" type="primary" size="xs" icon="pencil" content="" %}
                {% button url="cockpit_catalog_products_delete_<?php echo $product->id ?>" type="danger" size="xs" icon="trash-o" confirmation="Vous confirmer vouloir supprimer ce produit?" %}
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