<?php
    $id_lang = (int) Tools::getValue('id_lang');
    $shops = explode(',', Tools::getValue('id_shop'));
    $without_sup = (int) Tools::getValue('products_without_sup', 0);
    $supplier_ids = Tools::getValue('id_supplier');
    $supplier_ids = explode(',', $supplier_ids);
    $products_by_sup = array();

    $nb_shop_entry = count($shops);
    $id_shop = $shops[0];
    if ($without_sup)
    {
        if ($nb_shop_entry > 1)
        {
            $sql = 'SELECT p.id_product,p.id_category_default,p.id_supplier,p.reference,p.active,"" as product_supplier_reference, pl.name
                    FROM '._DB_PREFIX_.'product p
                    LEFT JOIN '._DB_PREFIX_.'product_lang pl ON (pl.id_product = p.id_product '.(version_compare(_PS_VERSION_, '1.5.0.0', '>=') ? 'AND pl.id_shop = p.id_shop_default' : '').' AND pl.id_lang='.(int) $id_lang.')
                    WHERE p.id_supplier = 0 OR p.id_product NOT IN (SELECT id_product FROM '._DB_PREFIX_.'product_supplier)';
        }
        else
        {
            $sql = 'SELECT p.id_product,p.id_category_default,p.id_supplier,p.reference,p.active,"" as product_supplier_reference,pl.name
                    FROM '._DB_PREFIX_.'product p
                    '.(version_compare(_PS_VERSION_, '1.5.0.0', '>=') ? 'RIGHT JOIN '._DB_PREFIX_.'product_shop ps ON (ps.id_product = p.id_product AND ps.id_shop='.(int) $id_shop.') ' : '').'
                    LEFT JOIN '._DB_PREFIX_.'product_lang pl ON (pl.id_product = p.id_product '.(version_compare(_PS_VERSION_, '1.5.0.0', '>=') ? 'AND pl.id_shop = '.(int) $id_shop : '').' AND pl.id_lang='.(int) $id_lang.')
                    WHERE p.id_supplier = 0 OR p.id_product NOT IN (SELECT id_product FROM '._DB_PREFIX_.'product_supplier)';
        }
        $product_list = Db::getInstance()->executeS($sql);
        if (!empty($product_list))
        {
            $products_by_sup[] = $product_list;
        }
    }
    else
    {
        foreach ($supplier_ids as $id_supplier)
        {
            if ($nb_shop_entry > 1)
            {
                $sql = 'SELECT p.id_product,p.id_category_default,p.id_supplier,p.reference,ps.active,psup.product_supplier_reference,pl.name,s.name as supplier_name
                        FROM '._DB_PREFIX_.'product p
                        LEFT JOIN '._DB_PREFIX_.'product_lang pl ON (pl.id_product = p.id_product '.(version_compare(_PS_VERSION_, '1.5.0.0', '>=') ? 'AND pl.id_shop = p.id_shop_default' : '').' AND pl.id_lang='.(int) $id_lang.')
                        LEFT JOIN '._DB_PREFIX_.'supplier s ON s.id_supplier = '.(int) $id_supplier.'
                        LEFT JOIN '._DB_PREFIX_.'product_supplier psup ON psup.id_supplier = '.(int) $id_supplier.' AND psup.id_product = p.id_product
                        WHERE p.id_supplier = '.(int) $id_supplier;
            }
            else
            {
                $sql = 'SELECT p.id_product,p.id_category_default,p.id_supplier,p.reference,ps.active,psup.product_supplier_reference,pl.name,s.name as supplier_name
                        FROM '._DB_PREFIX_.'product p
                        '.(version_compare(_PS_VERSION_, '1.5.0.0', '>=') ? 'RIGHT JOIN '._DB_PREFIX_.'product_shop ps ON (ps.id_product = p.id_product) ' : '').'
                        LEFT JOIN '._DB_PREFIX_.'product_lang pl ON (pl.id_product = p.id_product '.(version_compare(_PS_VERSION_, '1.5.0.0', '>=') ? 'AND pl.id_shop = ps.id_shop' : '').' AND pl.id_lang='.(int) $id_lang.')
                        LEFT JOIN '._DB_PREFIX_.'supplier s ON s.id_supplier = '.(int) $id_supplier.'
                        LEFT JOIN '._DB_PREFIX_.'product_supplier psup ON psup.id_supplier = '.(int) $id_supplier.' AND psup.id_product = p.id_product
                        WHERE ps.id_shop='.(int) $id_shop.' AND p.id_supplier = '.(int) $id_supplier;
            }
            $product_list = Db::getInstance()->executeS($sql);

            if (!empty($product_list))
            {
                $products_by_sup[$id_supplier] = $product_list;
            }
        }
    }

    function supplierOptions($id_shop)
    {
        $arrSuppliers = array();
        $sql = 'SELECT s.id_supplier,s.name 
                FROM '._DB_PREFIX_.'supplier s 
                '.(SCMS ? ' INNER JOIN '._DB_PREFIX_.'supplier_shop ss ON ss.id_supplier = s.id_supplier WHERE ss.id_shop = '.(int) $id_shop : '').' 
                ORDER BY s.name';
        $res = Db::getInstance()->ExecuteS($sql);
        if (!empty($res))
        {
            foreach ($res as $row)
            {
                if ($row['name'] == '')
                {
                    $row['name'] = ' ';
                }
                $arrSuppliers[$row['id_supplier']] = $row['name'];
            }
        }
        $arrSuppliers[0] = '-';

        return $arrSuppliers;
    }

    function generateValue($products_by_sup)
    {
        $xml = '';
        foreach ($products_by_sup as $id_supplier => $product_list)
        {
            foreach ($product_list as $product)
            {
                $xml .= '<row id="'.$product['id_product'].'">';
                $xml .= '    <userdata name="id_product">'.(int) $product['id_product'].'</userdata>';
                $xml .= '    <userdata name="open_cat_grid">'.$product['id_category_default'].'-'.$product['id_product'].'</userdata>';
                $xml .= '    <userdata name="id_supplier">'.$product['id_supplier'].'</userdata>';
                $cols = array('id_product', 'reference', 'reference_supplier', 'id_supplier', 'product_name','active');
                foreach ($cols as $col)
                {
                    switch ($col){
                        case 'id_product':
                            $xml .= '<cell>'.$product['id_product'].'</cell>';
                            break;
                        case 'id_supplier':
                            $xml .= '<cell>'.(int) $product['id_supplier'].'</cell>';
                            break;
                        case 'reference':
                            $xml .= '<cell><![CDATA['.$product['reference'].']]></cell>';
                            break;
                        case 'reference_supplier':
                            $xml .= '<cell><![CDATA['.$product['product_supplier_reference'].']]></cell>';
                            break;
                        case 'product_name':
                            $xml .= '<cell><![CDATA['.$product['name'].']]></cell>';
                            break;
                        case 'active':
                            $xml .= '<cell>'.$product['active'].'</cell>';
                            break;
                    }
                }
                $xml .= '</row>';
            }
        }

        return $xml;
    }

$supplier_products = generateValue($products_by_sup);
$supplier_products_options = supplierOptions($id_shop);

//XML HEADER
if (stristr($_SERVER['HTTP_ACCEPT'], 'application/xhtml+xml'))
{
    header('Content-type: application/xhtml+xml');
}
else
{
    header('Content-type: text/xml');
}
echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
?>
<rows id="0">
    <head>
        <beforeInit>
            <call command="attachHeader"><param><![CDATA[#numeric_filter,#text_filter,#text_filter,#select_filter,#text_filter,#select_filter]]></param></call>
        </beforeInit>
        <column id="id_product" width="80" type="ro" align="left" sort="str"><?php echo _l('ID Product'); ?></column>
        <column id="reference" width="100" type="ed" align="left" sort="str"><?php echo _l('Reference'); ?></column>
        <column id="supplier_reference" width="100" type="ed" align="left" sort="str"><?php echo _l('Supplier reference'); ?></column>
        <column id="id_supplier" width="100" type="coro" align="left" sort="int"><?php echo _l('Supplier'); ?>
            <?php
            foreach ($supplier_products_options as $id_sup => $name)
            {
                ?>
            <option value="<?php echo $id_sup; ?>"><![CDATA[<?php echo $name; ?>]]></option>
            <?php
            } ?>
        </column>
        <column id="product_name" width="200" type="ro" align="left" sort="str" color=""><?php echo _l('Product name'); ?></column>
        <column id="active" width="200" type="co" align="left" sort="int" color=""><?php echo _l('Active'); ?>
            <option value="1"><?php echo _l('Yes'); ?></option>
            <option value="0"><?php echo _l('No'); ?></option>
        </column>
        <afterInit>
            <call command="enableMultiselect"><param>1</param></call>
        </afterInit>
    </head>
    <?php
    echo '<userdata name="uisettings">'.uisettings::getSetting('sup_products').'</userdata>'."\n";
    echo $supplier_products;
    ?>
</rows>

