<?php

class ProductController extends ProductControllerCore
{
	public function initContent()
	{
		FrontController::initContent();

		if (_PS_CYRIL_) {
			$product_cyril = Db::getInstance()->executeS('SELECT id_product FROM '._DB_PREFIX_.'feature_product WHERE id_feature = 34 AND id_product = '.$this->product->id);
			$this->context->smarty->assign('product_cyril', $product_cyril);
		}

        $idParentSerres = 273;
        $tabCategSerres = [];
        $categSerres = new Category($idParentSerres);
        foreach($categSerres->getSubCategories($this->context->language->id) as $subCateg){
            $tabCategSerres[] = $subCateg["id_category"];
            $subcategSerres = new Category($subCateg["id_category"]);
            foreach($subcategSerres->getSubCategories($this->context->language->id) as $subsubCateg){
                $tabCategSerres[] = $subsubCateg["id_category"];
            }
        }
        $tabCategSerres[] = $idParentSerres;

		if(isset($this->category->id)){
			$this->context->smarty->assign('isSerres', in_array($this->category->id, $tabCategSerres));
		}

        $isNATURA = false;
        foreach(Db::getInstance()->executeS("SELECT * FROM `"._DB_PREFIX_."category_product` WHERE id_product = ".$this->product->id) as $categ){
            if($categ["id_category"] == 274 || $categ["id_category"] == 281)
                $isNATURA = true;
        }

        $this->context->smarty->assign('isNATURA', $isNATURA);
		
        // plants
        $semaines = Db::getInstance()->executeS("SELECT semaines FROM `"._DB_PREFIX_."aw_custom_category` WHERE id_category = ".$this->product->id_category_default);
        if ( !isset($semaines[0]['semaines']) || empty($semaines[0]['semaines']) )
        {
            $expedProd = 'Immédiate';
            $expedProdDesc = '';
        }
        else 
        {
            $moisFr = array(
                '01' => 'janvier',
                '02' => 'février',
                '03' => 'mars',
                '04' => 'avril',
                '05' => 'mai',
                '06' => 'juin',
                '07' => 'juillet',
                '08' => 'août',
                '09' => 'septembre',
                '10' => 'octobre',
                '11' => 'novembre',
                '12' => 'décembre'
            );

            $semaines_explode = explode(';', $semaines[0]['semaines']);
            $debut = $semaines_explode[0];
            $fin = $semaines_explode[(count($semaines_explode)-1)];
            if ( $debut == $fin)
            {
                $year = date('Y');
                if ( $debut < date('W') )
                {
                    $year++;
                }
                $week = $debut;
                
                if ( ($week-2) < 0 )
                {
                    $start = date("d/m/Y", strtotime("January ".$year." first monday - ".abs($week-2)." weeks"));
                }
                else
                {
                    $start = date("d/m/Y", strtotime("January ".$year." first monday + ".($week-2)." weeks"));
                }
                $end = date("d/m/Y", strtotime(substr($start, 6, 4)."/".substr($start, 3, 2)."/".substr($start, 0, 2)." + 4 days"));
                if ( substr($start, 3, 2) == substr($end, 6, 4) )
                {
                    $debut_final = substr($start, 0, 2).' '.$moisFr[substr($start, 3, 2)];
                }
                else 
                {
                    $debut_final = substr($start, 0, 2).' '.$moisFr[substr($start, 3, 2)].' '.substr($start, 6, 4);
                }
                $fin_final = substr($end, 0, 2).' '.$moisFr[substr($end, 3, 2)].' '.substr($end, 6, 4);

                $expedProd = 'En précommande, expédition sem '.$debut;
                $expedProdDesc = 'En précommande, semaine d\'expédition<br />entre le '.$debut_final.' et le '.$fin_final.' (sem '.$debut.').';
            }
            else 
            {
                // premiere semaine
                $year = date('Y');
                if ( $debut < date('W') )
                {
                    $year++;
                }
                $week = $debut;
                
                if ( ($week-2) < 0 )
                {
                    $start = date("d/m/Y", strtotime("January ".$year." first monday - ".abs($week-2)." weeks"));
                }
                else
                {
                    $start = date("d/m/Y", strtotime("January ".$year." first monday + ".($week-2)." weeks"));
                }
                // derniere semaine
                $year2 = date('Y');
                if ( $fin < date('W') )
                {
                    $year2++;
                }
                $week2 = $fin;
                
                if ( ($week2-2) < 0 )
                {
                    $start2 = date("d/m/Y", strtotime("January ".$year2." first monday - ".abs($week2-2)." weeks"));
                }
                else
                {
                    $start2 = date("d/m/Y", strtotime("January ".$year2." first monday + ".($week2-2)." weeks"));
                }
                $end = date("d/m/Y", strtotime(substr($start2, 6, 4)."/".substr($start2, 3, 2)."/".substr($start2, 0, 2)." + 4 days"));

                if ( substr($start, 6, 4) == substr($end, 6, 4) )
                {
                    $debut_final = substr($start, 0, 2).' '.$moisFr[substr($start, 3, 2)];
                }
                else 
                {
                    $debut_final = substr($start, 0, 2).' '.$moisFr[substr($start, 3, 2)].' '.substr($start, 6, 4);
                }
                $fin_final = substr($end, 0, 2).' '.$moisFr[substr($end, 3, 2)].' '.substr($end, 6, 4);

                $expedProd = 'En précommande, expédition entre sem '.$debut.' et sem '.$fin;
                $expedProdDesc = 'En précommande, choisissez votre semaine d\'expédition<br />entre le '.$debut_final.' (sem '.$debut.') et le '.$fin_final.' (sem '.$fin.').';
            }
        }
        $this->context->smarty->assign('expedProd', $expedProd);
        $this->context->smarty->assign('expedProdDesc', $expedProdDesc);

		// Box
		if (isset($this->product) && $this->product->id_category_default==227) {
			$this->setTemplate('product-box.tpl');
        }

		parent::initContent();
	}
	
	
	/**
     * Assign template vars related to attribute groups and colors.
     */
    protected function assignAttributesGroups($product_for_template = null)
    {
        $colors = [];
        $groups = [];
        $this->combinations = [];

        /** @todo (RM) should only get groups and not all declination ? */
        $attributes_groups = $this->product->getAttributesGroups($this->context->language->id);
        if (is_array($attributes_groups) && $attributes_groups) {
            $combination_images = $this->product->getCombinationImages($this->context->language->id);
            $combination_prices_set = [];
            foreach ($attributes_groups as $k => $row) {
                // Color management
                if (isset($row['is_color_group']) && $row['is_color_group'] && (isset($row['attribute_color']) && $row['attribute_color']) || (file_exists(_PS_COL_IMG_DIR_ . $row['id_attribute'] . '.jpg'))) {
                    $colors[$row['id_attribute']]['value'] = $row['attribute_color'];
                    $colors[$row['id_attribute']]['name'] = $row['attribute_name'];
                    if (!isset($colors[$row['id_attribute']]['attributes_quantity'])) {
                        $colors[$row['id_attribute']]['attributes_quantity'] = 0;
                    }
                    $colors[$row['id_attribute']]['attributes_quantity'] += (int) $row['quantity'];
                }
                if (!isset($groups[$row['id_attribute_group']])) {
                    $groups[$row['id_attribute_group']] = [
                        'group_name' => $row['group_name'],
                        'name' => $row['public_group_name'],
                        'group_type' => $row['group_type'],
                        'default' => -1,
                    ];
                }

                $groups[$row['id_attribute_group']]['attributes'][$row['id_attribute']] = [
                    'name' => $row['attribute_name'],
                    'html_color_code' => $row['attribute_color'],
                    'texture' => (@filemtime(_PS_COL_IMG_DIR_ . $row['id_attribute'] . '.jpg')) ? _THEME_COL_DIR_ . $row['id_attribute'] . '.jpg' : '',
                    'selected' => (isset($product_for_template['attributes'][$row['id_attribute_group']]['id_attribute']) && $product_for_template['attributes'][$row['id_attribute_group']]['id_attribute'] == $row['id_attribute']) ? true : false,
                ];

                //$product.attributes.$id_attribute_group.id_attribute eq $id_attribute
                if ($row['default_on'] && $groups[$row['id_attribute_group']]['default'] == -1) {
                    $groups[$row['id_attribute_group']]['default'] = (int) $row['id_attribute'];
                }
                if (!isset($groups[$row['id_attribute_group']]['attributes_quantity'][$row['id_attribute']])) {
                    $groups[$row['id_attribute_group']]['attributes_quantity'][$row['id_attribute']] = 0;
                }
                $groups[$row['id_attribute_group']]['attributes_quantity'][$row['id_attribute']] += (int) $row['quantity'];

                $this->combinations[$row['id_product_attribute']]['attributes_values'][$row['id_attribute_group']] = $row['attribute_name'];
                $this->combinations[$row['id_product_attribute']]['attributes'][] = (int) $row['id_attribute'];
                $this->combinations[$row['id_product_attribute']]['price'] = (float) $row['price'];

                // Call getPriceStatic in order to set $combination_specific_price
                if (!isset($combination_prices_set[(int) $row['id_product_attribute']])) {
                    $combination_specific_price = null;
                    Product::getPriceStatic((int) $this->product->id, false, $row['id_product_attribute'], 6, null, false, true, 1, false, null, null, null, $combination_specific_price);
                    $combination_prices_set[(int) $row['id_product_attribute']] = true;
                    $this->combinations[$row['id_product_attribute']]['specific_price'] = $combination_specific_price;
                }
                $this->combinations[$row['id_product_attribute']]['ecotax'] = (float) $row['ecotax'];
                $this->combinations[$row['id_product_attribute']]['weight'] = (float) $row['weight'];
                $this->combinations[$row['id_product_attribute']]['quantity'] = (int) $row['quantity'];
                $this->combinations[$row['id_product_attribute']]['reference'] = $row['reference'];
                $this->combinations[$row['id_product_attribute']]['ean13'] = $row['ean13'];
                $this->combinations[$row['id_product_attribute']]['mpn'] = $row['mpn'];
                $this->combinations[$row['id_product_attribute']]['upc'] = $row['upc'];
                $this->combinations[$row['id_product_attribute']]['isbn'] = $row['isbn'];
                $this->combinations[$row['id_product_attribute']]['unit_impact'] = $row['unit_price_impact'];
                $this->combinations[$row['id_product_attribute']]['minimal_quantity'] = $row['minimal_quantity'];
                if ($row['available_date'] != '0000-00-00' && Validate::isDate($row['available_date'])) {
                    $this->combinations[$row['id_product_attribute']]['available_date'] = $row['available_date'];
                    $this->combinations[$row['id_product_attribute']]['date_formatted'] = Tools::displayDate($row['available_date']);
                } else {
                    $this->combinations[$row['id_product_attribute']]['available_date'] = $this->combinations[$row['id_product_attribute']]['date_formatted'] = '';
                }

                if (!isset($combination_images[$row['id_product_attribute']][0]['id_image'])) {
                    $this->combinations[$row['id_product_attribute']]['id_image'] = -1;
                } else {
                    $this->combinations[$row['id_product_attribute']]['id_image'] = $id_image = (int) $combination_images[$row['id_product_attribute']][0]['id_image'];
                    if ($row['default_on']) {
                        foreach ($this->context->smarty->tpl_vars['product']->value['images'] as $image) {
                            if ($image['cover'] == 1) {
                                $current_cover = $image;
                            }
                        }
                        if (!isset($current_cover)) {
                            $current_cover = array_values($this->context->smarty->tpl_vars['product']->value['images'])[0];
                        }

                        if (is_array($combination_images[$row['id_product_attribute']])) {
                            foreach ($combination_images[$row['id_product_attribute']] as $tmp) {
                                if ($tmp['id_image'] == $current_cover['id_image']) {
                                    $this->combinations[$row['id_product_attribute']]['id_image'] = $id_image = (int) $tmp['id_image'];

                                    break;
                                }
                            }
                        }

                        if ($id_image > 0) {
                            if (isset($this->context->smarty->tpl_vars['images']->value)) {
                                $product_images = $this->context->smarty->tpl_vars['images']->value;
                            }
                            if (isset($product_images) && is_array($product_images) && isset($product_images[$id_image])) {
                                $product_images[$id_image]['cover'] = 1;
                                $this->context->smarty->assign('mainImage', $product_images[$id_image]);
                                $this->context->smarty->assign('images', $product_images);
                            }

                            $cover = $current_cover;

                            if (isset($cover) && is_array($cover) && isset($product_images) && is_array($product_images)) {
                                $product_images[$cover['id_image']]['cover'] = 0;
                                if (isset($product_images[$id_image])) {
                                    $cover = $product_images[$id_image];
                                }
                                $cover['id_image'] = (Configuration::get('PS_LEGACY_IMAGES') ? ($this->product->id . '-' . $id_image) : (int) $id_image);
                                $cover['id_image_only'] = (int) $id_image;
                                $this->context->smarty->assign('cover', $cover);
                            }
                        }
                    }
                }
            }

            // wash attributes list depending on available attributes depending on selected preceding attributes
            $current_selected_attributes = [];
            $count = 0;
            foreach ($groups as &$group) {
                ++$count;
                if ($count > 1) {
                    //find attributes of current group, having a possible combination with current selected
                    $id_product_attributes = [0];
                    $query = 'SELECT pac.`id_product_attribute`
                        FROM `' . _DB_PREFIX_ . 'product_attribute_combination` pac
                        INNER JOIN `' . _DB_PREFIX_ . 'product_attribute` pa ON pa.id_product_attribute = pac.id_product_attribute
                        WHERE id_product = ' . $this->product->id . ' AND id_attribute IN (' . implode(',', array_map('intval', $current_selected_attributes)) . ')
                        GROUP BY id_product_attribute
                        HAVING COUNT(id_product) = ' . count($current_selected_attributes);
                    if ($results = Db::getInstance()->executeS($query)) {
                        foreach ($results as $row) {
                            $id_product_attributes[] = $row['id_product_attribute'];
                        }
                    }
                    $id_attributes = Db::getInstance()->executeS('SELECT pac2.`id_attribute` FROM `' . _DB_PREFIX_ . 'product_attribute_combination` pac2' .
                        ((!Product::isAvailableWhenOutOfStock($this->product->out_of_stock) && 0 == Configuration::get('PS_DISP_UNAVAILABLE_ATTR')) ?
                        ' INNER JOIN `' . _DB_PREFIX_ . 'stock_available` pa ON pa.id_product_attribute = pac2.id_product_attribute
                        WHERE pa.quantity > 0 AND ' :
                        ' WHERE ') .
                        'pac2.`id_product_attribute` IN (' . implode(',', array_map('intval', $id_product_attributes)) . ')
                        AND pac2.id_attribute NOT IN (' . implode(',', array_map('intval', $current_selected_attributes)) . ')');
                    foreach ($id_attributes as $k => $row) {
                        $id_attributes[$k] = (int) $row['id_attribute'];
                    }
                    foreach ($group['attributes'] as $key => $attribute) {
                        if (!in_array((int) $key, $id_attributes)) {
                            unset(
                                $group['attributes'][$key],
                                $group['attributes_quantity'][$key]
                            );
                        }
                    }
                }
                //find selected attribute or first of group
                $index = 0;
                $current_selected_attribute = 0;
                foreach ($group['attributes'] as $key => $attribute) {
                    if ($index === 0) {
                        $current_selected_attribute = $key;
                    }
                    if ($attribute['selected']) {
                        $current_selected_attribute = $key;

                        break;
                    }
                }
                if ($current_selected_attribute > 0) {
                    $current_selected_attributes[] = $current_selected_attribute;
                }
            }

            // wash attributes list (if some attributes are unavailables and if allowed to wash it)
            if (!Product::isAvailableWhenOutOfStock($this->product->out_of_stock) && Configuration::get('PS_DISP_UNAVAILABLE_ATTR') == 0) {
                foreach ($groups as &$group) {
                    foreach ($group['attributes_quantity'] as $key => $quantity) {
                        if ($quantity <= 0) {
                            unset($group['attributes'][$key]);
                        }
                    }
                }

                foreach ($colors as $key => $color) {
                    if ($color['attributes_quantity'] <= 0) {
                        unset($colors[$key]);
                    }
                }
            }
            foreach ($this->combinations as $id_product_attribute => $comb) {
                $attribute_list = '';
                foreach ($comb['attributes'] as $id_attribute) {
                    $attribute_list .= '\'' . (int) $id_attribute . '\',';
                }
                $attribute_list = rtrim($attribute_list, ',');
                $this->combinations[$id_product_attribute]['list'] = $attribute_list;
            }
            unset($group);
			
			// Par Dorian, BERRY-WEB, novembre 2023
			// Plants en précommande - Début
			$categories = Product::getProductCategories($this->product->id);
			$categories_list = implode(',', array_map('intval', $categories));
			$date_iterateur = 0;
			$plant_precommande = false;
			foreach($groups as $group){
				foreach ($group['attributes'] as $key => $group_attribute) {
					$name = $group_attribute['name'] ?? '';
					//$this->product->id_category_default
					if (Product::isPlantEnPrecommande($name, $categories)){
						$plant_precommande = true;
						break;
					}
				}
			}

            $equivalent_plant = false;
            $lien_plant = 0;
            $cible_plant = false;
            if (Product::equivalentEnplant($categories)){
                $cible_plant = Product::getCategoriesPlantsCorresp($categories);
                if ( in_array($cible_plant, $categories) )
                {
                    $equivalent_plant = true;
                    $lien_plant = $cible_plant;
                }
            }

			if($plant_precommande || $equivalent_plant){
				foreach($groups as &$group){
					foreach ($group['attributes'] as &$group_attribute) {
						$name = $group_attribute['name'] ?? '';
						if (Product::isPlantEnPrecommande($name, $categories)) {
							$group_attribute['type'] = 'plant';
							// (int) $this->category->id;
							$query = 'SELECT date_precommande, date_precommande_b, date_precommande_date
							FROM ' . _DB_PREFIX_ . 'aw_custom_category
							WHERE id_category IN ('. $categories_list . ')';
							if ($results = Db::getInstance()->executeS($query)) {
								foreach ($results as $row) {
									
									$date = $row['date_precommande'];
									if(!$date){
										continue;
									}
									if($date_iterateur == 0){
										// Ajoute la réduction
										$group_attribute['discount'] = '20';
									}
									if($date_iterateur == 1){
										$date = $row['date_precommande_b'];
									}
									
									$group_attribute['name'] = trim($group_attribute['name']). ' ' .$date;
									
									if(isset($row['date_precommande_date']) && $row['date_precommande_date']){
										$datetime_compare = date_create_from_format('Y-m-d',$row['date_precommande_date']);
										$datetime_compare_str = date_format($datetime_compare, 'Ymd');
										$datetime_now_compare_str = (new \DateTime())->format('Ymd');

										if($date_iterateur == 0 && $datetime_now_compare_str >= $datetime_compare_str){
											$group_attribute['disabled'] = true;
										}
										if($date_iterateur == 1 && $datetime_now_compare_str < $datetime_compare_str){
											$group_attribute['disabled'] = true;
										}
									}
									
									
								}
							}
							$date_iterateur++;
						}else{
							$group_attribute['type'] = 'graine';
						}
					}
				}
			}

            $this->context->smarty->assign([
                'plant_precommande' => $plant_precommande,
                'equivalent_plant' => $equivalent_plant,
                'cible_plant' => $cible_plant,
                'groups' => $groups,
                'colors' => (count($colors)) ? $colors : false,
                'combinations' => $this->combinations,
                'combinationImages' => $combination_images,
                'lien_plant' => $lien_plant
            ]);
			// Plants en précommande - Fin
        } else {
            $this->context->smarty->assign([
                'groups' => [],
                'colors' => false,
                'combinations' => [],
                'combinationImages' => [],
            ]);
        }
    }
	
	
	
}
