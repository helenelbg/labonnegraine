<?php
  include("../config/config.inc.php");

  $contextEC = new Context();
  $contextEC->employee = 17;
  $contextEC->shop = new Shop(1);
  $specific_price_output = array();

  $sql = 'SELECT distinct id_product FROM ps_category_product WHERE id_product = 11;';
  $produits = Db::getInstance()->executeS($sql);
  foreach ($produits as $rangee_c)
  {
      $tab = array();
      $tab_complet = array();
      $tab_exc = array();
      $productEC = new Product($rangee_c['id_product']);
      $attributesEC = $productEC->getAttributeCombinations();

      foreach ($attributesEC as $attEC)
	  {
        if ( trim($attEC['attribute_name']) == 'plant' )
        {
            $tab_exc[] = $attEC['id_product_attribute'];
        }
      }

      $close = '';
      if ( count($tab_exc) > 0 )
      {
        $close = ' AND pa.id_product_attribute NOT IN ('.implode(',',$tab_exc).')';
      }

	  $sql_attr = 'SELECT pa.id_product_attribute FROM ps_product_attribute pa WHERE pa.id_product = "'.$rangee_c['id_product'].'"'.$close.';';
      $attributs = Db::getInstance()->executeS($sql_attr);
      foreach ($attributs as $rangee)
	  {
        if ( $productEC->id_tax_rules_group == 1 )
        {
            $tva = 20;
        }
        elseif ( $productEC->id_tax_rules_group == 2 )
        {
            $tva = 10;
        }
        elseif ( $productEC->id_tax_rules_group == 3 )
        {
            $tva = 17.5;
        }
        elseif ( $productEC->id_tax_rules_group == 4 )
        {
            $tva = 5.5;
        }

        $priceTTC = Product::getPriceStatic(
            $rangee_c['id_product'],
            true,
            $rangee['id_product_attribute'],
            6,
            null,
            false,
            true,
            1,
            false,
            null,
            null,
            null,
            $specific_price_output,
            true,
            true,
            $contextEC);
        
        $priceHT = Product::getPriceStatic(
            $rangee_c['id_product'],
            false,
            $rangee['id_product_attribute'],
            6,
            null,
            false,
            true,
            1,
            false,
            null,
            null,
            null,
            $specific_price_output,
            true,
            true,
            $contextEC);

            //echo $rangee['id_product_attribute'].' > '.$priceTTC.' | '.$priceHT.' ('.$tva.'%)<br />';
            $tab[$rangee['id_product_attribute']] = $priceTTC;
            $tab_complet[$rangee['id_product_attribute']]['TTC'] = $priceTTC;
            $tab_complet[$rangee['id_product_attribute']]['HT'] = $priceHT;
            $tab_complet[$rangee['id_product_attribute']]['TVA'] = $tva;
            //Db::getInstance()->execute($query);
	  }
      asort($tab);
      $cpt = 0;
      $cas = 0;
      foreach($tab as $pa => $prixTest)
      {
        echo '$pa : '.$pa.'<br />';
        if ( $cpt == 0 )
        {
            if ( $prixTest <= 2.2 )
            {
                echo '+0.30<br />';
                $aug = 0.3;
                $cas = 1;
            }
            elseif ( $prixTest > 2.2 && $prixTest < 2.6 )
            {
                echo '+0.20<br />';
                $aug = 0.2;
                $cas = 2;
            }
            elseif ( $prixTest >= 2.6 )
            {
                echo '+0.10<br />';
                $aug = 0.1;
                $cas = 3;
            }
        }
        elseif ( $cpt == 1 )
        {
            if ( $cas == 1 )
            {
                echo '+1.20<br />';
                $aug = 1.2;
            }
            elseif ( $cas == 2 )
            {
                echo '+0.80<br />';
                $aug = 0.8;
            }
            elseif ( $cas == 3 )
            {
                echo '+0.40<br />';
                $aug = 0.4;
            }
        }
        elseif ( $cpt == 2 )
        {
            if ( $cas == 1 )
            {
                echo '+6.00<br />';
                $aug = 6;
            }
            elseif ( $cas == 2 )
            {
                echo '+4.00<br />';
                $aug = 4;
            }
            elseif ( $cas == 3 )
            {
                echo '+2.00<br />';
                $aug = 2;
            }
        }
        elseif ( $cpt == 3 )
        {
            if ( $cas == 1 )
            {
                echo '+30.00<br />';
                $aug = 30;
            }
            elseif ( $cas == 2 )
            {
                echo '+20.00<br />';
                $aug = 20;
            }
            elseif ( $cas == 3 )
            {
                echo '+10.00<br />';
                $aug = 10;
            }
        }
        $augHT = $aug / (1+($tab_complet[$pa]['TVA']/100));
        $newHT = $tab_complet[$pa]['HT'] + $augHT;
        $newTTC = $newHT * (1+($tab_complet[$pa]['TVA']/100));

        echo '$newHT'.$newHT.' ('.(1+($tab_complet[$pa]['TVA']/100)).')<br />';
        echo '$newTTC'.$newTTC.'<br />';

        $req = 'UPDATE ps_product_attribute SET price = "'.$newHT.'" WHERE id_product_attribute = "'.$pa.'";';
        echo $req.'<br />';

        $cpt++;
      }
  }
