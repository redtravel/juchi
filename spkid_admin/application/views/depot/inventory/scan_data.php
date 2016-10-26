<?php foreach ($products as $product): ?>
    
    <?php $quantity_id = str_replace(' ', '-', $product->provider_barcode); ?>

    <table name="<?php print $quantity_id; ?>" class="dataTable" barcode="<?php print $product->provider_barcode; ?>" scannum="0">
        <tr class="row">
            <td align="left">条码</td>
            <td><?php print $product->provider_barcode; ?></td>
        </tr>
        <tr class="row">
            <td align="left">商品</td>
            <td><?php print $product->product_name; ?>|<?php print $product->color_name; ?>|<?php print $product->size_name; ?></td>
        </tr>
        <tr class="row">
            <td align="left">数量</td>
            <td>待入:<?php print $product->num_dairu; ?>&nbsp;待出:<?php print $product->num_daichu; ?>&nbsp;实际:<span style="color:red;"><?php print $product->num_shiji; ?></span></td>
        </tr>
    </table>

<?php endforeach; ?>

