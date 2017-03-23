<h1 class="page-title">Produits</h1>
<br />
<div class="">
    {% button url="catalog_products_new" type="success" icon="plus" content="Ajouter un produit" %}
</div>
<br />
<table class="table table-hover">
    <thead>
        <tr>
            <th width="1%">ID</th>
            <th>Catégorie</th>
            <th>Nom</th>
            <th>Prix</th>
            <th>Actif</th>
            <th width="10%">Action</th>
        </tr>
    </thead>
    <tbody>
<?php
foreach ($params['products'] as $product) {
    echo '<tr>';
    echo '<td>'.$product->id.'</td>';
    echo '<td>'.$product->category_id.'</td>';
    echo '<td>'.$product->name.'</td>';
    echo '<td>'.$product->price.'</td>';
    echo '<td>';?>
    {% button link="catalog_products_edit" type="success" size="xs" icon="plus" content="" %}
    {% button link="catalog_products_delete" type="success" icon="trash-o" confirmation="Vous confirmer vouloir supprimer ce produit?" %}<?php
    echo '</td>';
    echo '</tr>';
}
?>
    </tbody>
</table>