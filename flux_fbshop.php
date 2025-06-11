<?php

include(dirname(__FILE__) . '/config/config.inc.php');
include(dirname(__FILE__) . '/init.php');

/*$req_texte_exclus = 'SELECT * FROM AW_google_shooping_exclus';
$resu_texte_exclus = Db::getInstance()->ExecuteS($req_texte_exclus);*/
$resu_texte_exclus = array();

$tabsaut = array( CHR(13) => " ", CHR(10) => " " );

$req_product = 'SELECT * FROM ' . _DB_PREFIX_ . 'product WHERE active = "1" AND id_product NOT IN (SELECT id_pack FROM ' . _DB_PREFIX_ . 'pm_advancedpack);';
$resu_product = Db::getInstance()->ExecuteS($req_product);

$csv = 'id,gtin,title,description,availability,inventory,condition,price,link,image_link,brand,google_product_category,fb_product_category,sale_price,sale_price_effective_date,item_group_id,gender,color,size,age_group,material,pattern,product_type,shipping,shipping_weight,rich_text_description'."\n";

foreach ($resu_product as $rangee_product)
{
    $categories_en_cours = Product::getProductCategories($rangee_product['id_product']);

    $tax_id = $rangee_product['id_tax_rules_group'];

    $req_taxe = 'SELECT * FROM ' . _DB_PREFIX_ . 'tax WHERE id_tax = "' . $tax_id . '"';
    $rangee_taxe = Db::getInstance()->ExecuteS($req_taxe);

    $req_lang = 'SELECT * FROM ' . _DB_PREFIX_ . 'product_lang WHERE id_product = "' . $rangee_product['id_product'] . '" AND id_lang = "2"';
    $rangee_lang = Db::getInstance()->getRow($req_lang);

    $category = Category::getLinkRewrite($rangee_product['id_category_default'], 2);

    $link = new Link();
    $objet_product = new Product($rangee_lang['id_product']);
    $lien = str_replace('.html', '.html?utm=fb_ins', $link->getProductLink($objet_product));

    // On récupere le stock disponible
    $req_p_stock = 'SELECT * FROM ' . _DB_PREFIX_ . 'stock_available WHERE id_product = "' . $rangee_product['id_product'] . '"';
    $rangee_p_stock = Db::getInstance()->ExecuteS($req_p_stock);

    $rangee_product['quantity'] = $rangee_p_stock[0]['quantity'];

    if ($rangee_product['quantity'] > 0)
    {
        $availability = 'in stock';
    }
    else
    {
        $availability = 'out of stock';
    }

    $req_image = 'SELECT * FROM ' . _DB_PREFIX_ . 'image WHERE id_product = "' . $rangee_product['id_product'] . '" AND cover = 1;';
    $rangee_image = Db::getInstance()->ExecuteS($req_image);

    $req_manufacturer = 'SELECT * FROM ' . _DB_PREFIX_ . 'manufacturer WHERE id_manufacturer = "' . $rangee_product['id_manufacturer'] . '"';
    $rangee_manufacturer = Db::getInstance()->getRow($req_manufacturer);

    $parent = $rangee_product['id_category_default'];
    $arbo = '';

    while ($parent != 0)
    {
        $req_category = 'SELECT * FROM ' . _DB_PREFIX_ . 'category WHERE id_category = "' . $parent . '"';
        $rangee_category = Db::getInstance()->getRow($req_category);

        $req_langC = 'SELECT * FROM ' . _DB_PREFIX_ . 'category_lang WHERE id_lang = "2" AND id_category = "' . $rangee_category['id_category'] . '"';
        $rangee_langC = Db::getInstance()->getRow($req_langC);

        if (empty($arbo))
        {
            $arbo = $rangee_langC['name'];
        }
        else
        {
            if ($rangee_category['id_category'] != 1)
            {
                $arbo = $rangee_langC['name'] . ' &gt; ' . $arbo;
            }
        }
        $parent = $rangee_category['id_parent'];
    }

    $rangee_lang['description_short'] = str_replace('&nbsp;', ' ', $rangee_lang['description_short']);
    $rangee_lang['description'] = str_replace('&nbsp;', ' ', $rangee_lang['description']);

    foreach($resu_texte_exclus as $T_un_exclus)
    {
        if($T_un_exclus["tout_le_flux"]=="X" ||       in_array($rangee_lang['id_product'],explode(",",$T_un_exclus["produits"]))       )
        {
            if ( $T_un_exclus["split"] == 0 )
            {
                $rangee_lang['description']          = str_replace($T_un_exclus["texte"], "", $rangee_lang['description']);
                $rangee_lang['description_short']    = str_replace($T_un_exclus["texte"], "", $rangee_lang['description_short']);
            }
            else
            {
                $explode1 = explode($T_un_exclus["texte"], strip_tags($rangee_lang['description']));
                $explode2 = explode($T_un_exclus["texte"], strip_tags($rangee_lang['description_short']));

                $rangee_lang['description'] = $explode1[0];
                $rangee_lang['description_short'] = $explode2[0];
            }
        }
    }

    $rangee_lang['description_short'] = str_replace('&eacute;', 'é', $rangee_lang['description_short']);
    $rangee_lang['description_short'] = str_replace('&egrave;', 'è', $rangee_lang['description_short']);
    $rangee_lang['description_short'] = str_replace('&ecirc;', 'ê', $rangee_lang['description_short']);
    $rangee_lang['description_short'] = str_replace('&agrave;', 'à', $rangee_lang['description_short']);
    $rangee_lang['description_short'] = str_replace('&acirc;', 'a', $rangee_lang['description_short']);
    $rangee_lang['description_short'] = str_replace('&iuml;', ' ', $rangee_lang['description_short']);
    $rangee_lang['description_short'] = str_replace('&oelig;', 'oe', $rangee_lang['description_short']);
    $rangee_lang['description_short'] = str_replace('&ucirc;', 'u', $rangee_lang['description_short']);
    $rangee_lang['description_short'] = str_replace('&ocirc;', 'o', $rangee_lang['description_short']);
    $rangee_lang['description_short'] = str_replace('&Eacute;', 'E', $rangee_lang['description_short']);
    $rangee_lang['description_short'] = str_replace('&deg;', 'o', $rangee_lang['description_short']);
    $rangee_lang['description_short'] = str_replace('&icirc;', 'i', $rangee_lang['description_short']);
    $rangee_lang['description_short'] = str_replace('&rsquo;', "'", $rangee_lang['description_short']);
    $rangee_lang['description_short'] = str_replace('&raquo;', '"', $rangee_lang['description_short']);
    $rangee_lang['description_short'] = str_replace('&laquo;', '"', $rangee_lang['description_short']);
    $rangee_lang['description_short'] = str_replace('&ugrave;', 'u', $rangee_lang['description_short']);
    $rangee_lang['description_short'] = str_replace('&ccedil;', 'c', $rangee_lang['description_short']);
    $rangee_lang['description_short'] = str_replace('&Agrave;', 'A', $rangee_lang['description_short']);
    $rangee_lang['description_short'] = str_replace('&euml;', 'e', $rangee_lang['description_short']);
    $rangee_lang['description_short'] = str_replace('&sup2;', '2', $rangee_lang['description_short']);
    $rangee_lang['description_short'] = str_replace('&euro;', 'EUR.', $rangee_lang['description_short']);
    $rangee_lang['description_short'] = str_replace('&trade;', '™', $rangee_lang['description_short']);
    $rangee_lang['description_short'] = str_replace('&Ocirc;', 'Ô', $rangee_lang['description_short']);
    $rangee_lang['description_short'] = str_replace('&Oslash;', 'Ø', $rangee_lang['description_short']);
    $rangee_lang['description_short'] = str_replace('&uuml;', 'ü', $rangee_lang['description_short']);
    $rangee_lang['description_short'] = str_replace('&ndash;', '-', $rangee_lang['description_short']);
    $rangee_lang['description_short'] = str_replace('&ouml;', 'ö', $rangee_lang['description_short']);
    $rangee_lang['description_short'] = str_replace('&reg;', '®', $rangee_lang['description_short']);
    $rangee_lang['description_short'] = str_replace('&micro;', 'µ', $rangee_lang['description_short']);
    $rangee_lang['description_short'] = str_replace('"', "'", $rangee_lang['description_short']);


    $rangee_lang['description'] = str_replace('&eacute;', 'é', $rangee_lang['description']);
    $rangee_lang['description'] = str_replace('&egrave;', 'è', $rangee_lang['description']);
    $rangee_lang['description'] = str_replace('&ecirc;', 'ê', $rangee_lang['description']);
    $rangee_lang['description'] = str_replace('&agrave;', 'à', $rangee_lang['description']);
    $rangee_lang['description'] = str_replace('&acirc;', 'a', $rangee_lang['description']);
    $rangee_lang['description'] = str_replace('&iuml;', ' ', $rangee_lang['description']);
    $rangee_lang['description'] = str_replace('&oelig;', 'oe', $rangee_lang['description']);
    $rangee_lang['description'] = str_replace('&ucirc;', 'u', $rangee_lang['description']);
    $rangee_lang['description'] = str_replace('&ocirc;', 'o', $rangee_lang['description']);
    $rangee_lang['description'] = str_replace('&Eacute;', 'E', $rangee_lang['description']);
    $rangee_lang['description'] = str_replace('&deg;', 'o', $rangee_lang['description']);
    $rangee_lang['description'] = str_replace('&icirc;', 'i', $rangee_lang['description']);
    $rangee_lang['description'] = str_replace('&rsquo;', "'", $rangee_lang['description']);
    $rangee_lang['description'] = str_replace('&raquo;', '"', $rangee_lang['description']);
    $rangee_lang['description'] = str_replace('&laquo;', '"', $rangee_lang['description']);
    $rangee_lang['description'] = str_replace('&ugrave;', 'u', $rangee_lang['description']);
    $rangee_lang['description'] = str_replace('&ccedil;', 'c', $rangee_lang['description']);
    $rangee_lang['description'] = str_replace('&Agrave;', 'A', $rangee_lang['description']);
    $rangee_lang['description'] = str_replace('&euml;', 'e', $rangee_lang['description']);
    $rangee_lang['description'] = str_replace('&sup2;', '2', $rangee_lang['description']);
    $rangee_lang['description'] = str_replace('&euro;', 'EUR.', $rangee_lang['description']);
    $rangee_lang['description'] = str_replace('&trade;', '™', $rangee_lang['description']);
    $rangee_lang['description'] = str_replace('&Ocirc;', 'Ô', $rangee_lang['description']);
    $rangee_lang['description'] = str_replace('&Oslash;', 'Ø', $rangee_lang['description']);
    $rangee_lang['description'] = str_replace('&uuml;', 'ü', $rangee_lang['description']);
    $rangee_lang['description'] = str_replace('&ndash;', '-', $rangee_lang['description']);
    $rangee_lang['description'] = str_replace('&ouml;', 'ö', $rangee_lang['description']);
    $rangee_lang['description'] = str_replace('&reg;', '®', $rangee_lang['description']);
    $rangee_lang['description'] = str_replace('&micro;', 'µ', $rangee_lang['description']);
    $rangee_lang['description'] = str_replace('"', "'", $rangee_lang['description']);

    $rangee_manufacturer['name'] = str_replace('&', ' et ', $rangee_manufacturer['name']);

    $attribute = false;

    $req_att = 'SELECT sa.quantity, GROUP_CONCAT(al.name SEPARATOR ", ") as name, pa.id_product_attribute, pa.ean13 FROM ' . _DB_PREFIX_ . 'product_attribute pa, ' . _DB_PREFIX_ . 'product_attribute_combination pac, ' . _DB_PREFIX_ . 'attribute a, ' . _DB_PREFIX_ . 'attribute_lang al, ' . _DB_PREFIX_ . 'attribute_group_lang agl, ' . _DB_PREFIX_ . 'stock_available sa WHERE pa.id_product_attribute = pac.id_product_attribute AND pa.id_product = '.$rangee_product['id_product'].' AND pac.id_attribute = a.id_attribute AND a.id_attribute = al.id_attribute AND al.id_lang = 2 AND agl.id_attribute_group = a.id_attribute_group AND agl.id_lang = 2 AND sa.id_product_attribute = pa.id_product_attribute GROUP BY pa.id_product_attribute';
    //$req_att = 'SELECT sa.quantity, GROUP_CONCAT(CONCAT(agl.public_name," - ", al.name) SEPARATOR ", ") as name, pa.id_product_attribute, pa.ean13 FROM ' . _DB_PREFIX_ . 'product_attribute pa, ' . _DB_PREFIX_ . 'product_attribute_combination pac, ' . _DB_PREFIX_ . 'attribute a, ' . _DB_PREFIX_ . 'attribute_lang al, ' . _DB_PREFIX_ . 'attribute_group_lang agl, ' . _DB_PREFIX_ . 'stock_available sa WHERE pa.id_product_attribute = pac.id_product_attribute AND pa.id_product = '.$rangee_product['id_product'].' AND pac.id_attribute = a.id_attribute AND a.id_attribute = al.id_attribute AND al.id_lang = 2 AND agl.id_attribute_group = a.id_attribute_group AND agl.id_lang = 2 AND sa.id_product_attribute = pa.id_product_attribute GROUP BY pa.id_product_attribute';
    $resu_att = Db::getInstance()->ExecuteS($req_att);

    $link_img = new Link(null, "https://");
    if(isset($rangee_image[0]['id_image']))
    {
        $image_link = $link_img->getImageLink($rangee_lang['link_rewrite'], $rangee_image[0]['id_image'], "large_default");
    }
    else
    {
        $image_link = "";
    }

    foreach ($resu_att as $rangee_att)
    {
        $attribute = true;

        if(intval($rangee_att['quantity'])>0)
        {
            $availability = "in stock";
        }
        else
        {
            $availability = "out of stock";
        }

        $id = 'LGB_'.$rangee_lang['id_product'] ."_". $rangee_att['id_product_attribute'];
        $gtin = $rangee_att['ean13'];

        $prix_prod = $objet_product -> getPriceStatic($rangee_product['id_product'], true, $rangee_att['id_product_attribute'], 6, null, false, false);
        $prix_prod_disc = $objet_product -> getPriceStatic($rangee_product['id_product'], true, $rangee_att['id_product_attribute'], 6, null, false, true);

        $lien = str_replace('.html', '.html?utm=fb_ins', $link->getProductLink($objet_product, null, $category, null, null, null, $rangee_att['id_product_attribute'], false));

        $csv .= $id . ','.$gtin.',"' . substr(utf8_decode(str_replace('&', ' ',str_replace('&euro;', 'EUR.', $rangee_lang['name']." - ".$rangee_att['name']))),0,150) . '","' . substr(utf8_decode(strip_tags(strtr($rangee_lang['description_short'], $tabsaut))), 0, 5000) . '",' . $availability . ',' . $rangee_att['quantity'] . ',new,' . number_format($prix_prod, 2, '.', '') . ' EUR,' . $lien . ',' . $image_link . ',' . utf8_decode($rangee_manufacturer['name']) . ',';
                if ( $rangee_product['id_category_default'] == 234 )
                {
                  $csv .= '5632,115,';
                }
                elseif ( $rangee_product['id_category_default'] == 91 )
                {
                  $csv .= '2802,112,';
                }
                else
                {
                  $csv .= '2962,112,';
                }
        $csv .= number_format($prix_prod_disc, 2, '.', '') . ' EUR,,LGB_'.$rangee_lang['id_product'].',,,,,,,,0.0,,"' . strtr(utf8_decode($rangee_lang['description']), $tabsaut).'"';
        $csv .= "\n";
    }
    if ($attribute == false)
    {
      $prix_prod = $objet_product -> getPriceStatic($rangee_product['id_product'], true, null, 6, null, false, false);
      $prix_prod_disc = $objet_product -> getPriceStatic($rangee_product['id_product'], true, null, 6, null, false, true);

      $id = 'LGB_'.$rangee_lang['id_product'];
      $gtin = $rangee_product['ean13'];

      $csv .= $id . ','.$gtin.',"' . substr(utf8_decode(str_replace('&', ' ',str_replace('&euro;', 'EUR.', $rangee_lang['name']))),0,150) . '","' . substr(utf8_decode(strip_tags(strtr($rangee_lang['description_short'], $tabsaut))), 0, 5000) . '",' . $availability . ',' . $rangee_product['quantity'] . ',new,' . number_format($prix_prod, 2, '.', '') . ' EUR,' . $lien . ',' . $image_link . ',' . utf8_decode($rangee_manufacturer['name']) . ',';
              if ( $rangee_product['id_category_default'] == 234 )
              {
                $csv .= '5632,115,';
              }
              elseif ( $rangee_product['id_category_default'] == 91 )
              {
                $csv .= '2802,112,';
              }
              else
              {
                $csv .= '2962,112,';
              }
      $csv .= number_format($prix_prod_disc, 2, '.', '') . ' EUR,,,,,,,,,,0.0,,"' . strtr(utf8_decode($rangee_lang['description']), $tabsaut).'"';
      $csv .= "\n";
    }
}

$conn_id = ftp_connect('dev.labonnegraine.com', 21);
$login_result = ftp_login($conn_id, 'devlbg', 'ZB#8ggihe');
// Ouverture du répertoire en écriture via une commande FTP
$permission = decoct(0664);

$chmod_cmd = 'CHMOD ' . $permission . ' /web/flux_fbshop.csv';
$chmod = ftp_site($conn_id, $chmod_cmd);

// écriture dans le fichier
$fp = fopen("flux_fbshop.csv", 'w+');
fwrite($fp, utf8_encode($csv));
fclose($fp);

// Fermeture des droits d'écriture du répertoire
$permission = decoct(0644);

$chmod_cmd2 = 'CHMOD ' . $permission . ' /web/flux_fbshop.csv';
$chmod2 = ftp_site($conn_id, $chmod_cmd2);

// Fermeture de la connexion FTP
ftp_close($conn_id);
?>
