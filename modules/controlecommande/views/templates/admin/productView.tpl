<div class="product_prepare_container product_{$product.product_id}_{$product.product_attribute_id}">
    <div class="thumbnail">
        {*<a href="#" id="{$product.product_id}_{$product.product_attribute_id}_{$quantity}" class="prepare_product" >
            <img src="{$link->getImageLink($product.link_rewrite, $product.id_image, $imageType->name)}" alt="{$product.link_rewrite} - {$product.id_image}" class="img-thumbnail {$imageType->name}}">
        </a>*}
        <div class="caption">
            <table class="table table-responsive infos-products"> 
                <tr>
                    <td style="width:7%"><a href="#" id="{$product.product_id}_{$product.product_attribute_id}_{$nb_quantity}" class="prepare_product" >
                        <img src="{$link->getImageLink($product.link_rewrite, $product.id_image, $imageType->name)}" alt="{$product.link_rewrite} - {$product.id_image}" class="img-thumbnail {$imageType->name}}">
                    </a></td>
                    <td class="text-product-control" style="width:43%">{$product.product_name}<br />({$nb_quantity} / {$product.product_quantity}) </td>
                    <td style="width:10%;text-align:center;">{*l s='R&eacute;f&eacute;rence' mod='controlecommande'} : *}{$product.product_reference}</td>
                    <td style="width:10%;text-align:center;">{*l s='ean13' mod='controlecommande'} : *}{$product.product_ean13}</td>
                    <td style="width:15%"><a href="#" id="{$product.product_id}_{$product.product_attribute_id}_{$nb_quantity}" class="btn btn-success btn-lg btn-block prepare_product" role="button" onclick="$('#product_barcode').focus();">
                       {* <i class="icon-check" ></i>*} {l s='Contr&ocirc;ler'  mod='controlecommande'}
                    </a></td>
                    <td style="width:15%"><a class="btn btn-default btn-lg btn-block" href="{$link->getAdminLink('AdminProducts')}&amp;id_product={$product.product_id}&amp;updateproduct" target="_blank" ole="button">
                    {l s='Fiche Produit'  mod='controlecommande'}
                    </a></td>
                </tr>
            </table>
            {*<p class="list_buttons">
                <a href="#" id="{$product.product_id}_{$product.product_attribute_id}_{$quantity}" class="btn btn-success btn-lg btn-block prepare_product" role="button">
                   {l s='Contr&ocirc;ler'  mod='controlecommande'}
                </a>
            </p>
            <p class="list_buttons">
                <a class="btn btn-default btn-lg btn-block" href="{$link->getAdminLink('AdminProducts')}&amp;id_product={$product.product_id}&amp;updateproduct" target="_blank" ole="button">
                {l s='Fiche Produit'  mod='controlecommande'}
                </a>
            </p>*}
        </div>
    </div>
</div>
                 {* <ul class="list-group">
                <li class="list-group-item list-group-item-info">
                    {$product.product_name} ({$nb_quantity} / {$product.product_quantity})
                </li>

                <li class="list-group-item list-group-item-warning"><span class="badge">{$product.location}</span>{l s='Emplacement :' mod='controlecommande'}</li>
                <li class="list-group-item"><span class="badge">{$product.product_reference}</span>{l s='Reference :' mod='controlecommande'}</li>
                <li class="list-group-item"><span class="badge">{$product.product_ean13}</span>{l s='ean13 :' mod='controlecommande'}</li>
                <li class="list-group-item"><span class="badge">{$product.product_upc}</span>{l s='Upc :' mod='controlecommande'}</li>
            </ul>*}
