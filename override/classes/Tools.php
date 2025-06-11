<?php

class Tools extends ToolsCore {
	
	public static function getJardinHome() {
		/*$WP_DB_name="jardin_essai";
		$WP_DB_host="localhost";
        $WP_DB_user="jardin-essai";
        $WP_DB_pass="TA25rNXmTsjNrZ8QsbcHTAQp";
		
		$html = '';
		$html .=' <div class="WP_recents_posts modules-WP_recents_posts"><a href="https://www.jardin-essai.com/" target="_blank">';
		$html .=' <h2>Retrouvez les derniers articles du blog :</h2>';
		$html .=' <img id="logo_WP" src="/modules/AW_wordpressDBcontent/imgs/logo_noir.png" />';
		
		$WP_DB_object=mysqli_connect($WP_DB_host, $WP_DB_user, $WP_DB_pass, $WP_DB_name);
		$result_wp_posts = mysqli_query($WP_DB_object,"SELECT * FROM AW_recents_posts");
		if($result_wp_posts) {
			$T_wp_posts = mysqli_fetch_assoc($result_wp_posts);
			$html .= utf8_encode($T_wp_posts["content"]);
		}
		$html.='</a></div>';
		return utf8_decode($html);*/
	}
	
	public static function getHomeCategories()
    {
		$categories_ids = json_decode(Configuration::get('HOMECATEGORIES_CATEGORIES'));
        $categories = Category::getCategoryInformations($categories_ids);

        foreach($categories_ids as $id_category)
        {
            $cat = new Category($id_category);
            $categories[$id_category]['id_image'] = $cat->id_image;
        }
		       
		$html = '';			   
		$html .= '<div id="homecategories" class="block categories_block"><div class="homecategories_bloc">
			<div class="homecategories_case_pdt"><div class="homecategories_case_titre"><h4>NOS <br>PRODUITS<br> DE SAISON</h4></div></div>';
		
		foreach ($categories as $category) {
			
			$link = Context::getContext()->link;
			$image_link = $link->getCatImageLink($category['link_rewrite'], $category['id_image']);
			$cat_link = $link->getCategoryLink($category['id_category'], $category['link_rewrite']);

			$html .= '<div class="homecategories_case_pdt"><a href="'.$cat_link.'" title="'.$category['name'].'">
				   <div class="category_block">
					   <div class="image_container">';

			if ($category['id_image']) {
				$html .= '<img src="'.$image_link.'" alt="" />';
			}
				
			$html .= ' </div>
				   <div class="category_name"><p>'.
					   $category['name'].'
				   </p></div>
			   </div></a></div>';
		}
		$html .= '</div></div>';

        return $html;
    }

	public static function getCategoriesEnAvant()
    {
		$categories_ids = json_decode(Configuration::get('CATEGORIESENAVANT_CATEGORIES'));
        $categories = Category::getCategoryInformations($categories_ids);

        foreach($categories_ids as $id_category)
        {
            $cat = new Category($id_category);
            $categories[$id_category]['id_image'] = $cat->id_image;
        }
		       
		$html = '';			   
		$html .= '<div id="categoriesenavant" class="block categories_block"><div class="categoriesenavant_bloc">
			<div class="categoriesenavant_case_pdt"><div class="categoriesenavant_case_titre" style="background-color:'.Configuration::get('CATEGORIESENAVANT_COULEUR').'"><h4>'.str_replace('#', '<br />', Configuration::get('CATEGORIESENAVANT_TITRE')).'</h4></div></div>';
		
		krsort($categories);
		foreach ($categories as $category) {
			
			$link = Context::getContext()->link;
			$image_link = $link->getCatImageLink($category['link_rewrite'], $category['id_image']);
			$cat_link = $link->getCategoryLink($category['id_category'], $category['link_rewrite']);

			$html .= '<div class="categoriesenavant_case_pdt"><a href="'.$cat_link.'" title="'.$category['name'].'">
				   <div class="category_block">
					   <div class="image_container">';

			if ($category['id_image']) {
				$html .= '<img src="'.$image_link.'" alt="" />';
			}
				
			$html .= ' </div>
				   <div class="category_name" style="background-color:rgb(216 38 55 / 81%)"><p>'.
					   $category['name'].'
				   </p></div>
			   </div></a></div>';
		}
		$html .= '</div></div>';

        return $html;
    }
	
	public static function getFacet($categoryID) {
		
		$html = '';
		
		$id_lang = (int)Context::getContext()->language->id;

		$sql = 'SELECT filters FROM ' . _DB_PREFIX_ . 'layered_filter';
			
		$filters = Db::getInstance()->executeS($sql);
		$feature_ids = [];
		
		foreach ($filters as $filterTemplate) {
			$data = Tools::unSerialize($filterTemplate['filters']);
			if(in_array($categoryID, $data['categories'])) {
				foreach($data as $key => $value){
					if(Tools::str_starts_with($key, 'layered_selection_feat_')) {
						$feature_id = str_replace('layered_selection_feat_','',$key);
						$feature_ids[] = (int)$feature_id;
					}
				}
			}
		}
		
		if(count($feature_ids)){
						
			$sql = 'SELECT
				f.id_feature,
				f.position,
				fv.id_feature_value,
				fl.name AS feature_name,
				fvl.value AS feature_value,
				COUNT(fp.id_product) as product_number
			FROM
				ps_feature AS f
			INNER JOIN
				ps_feature_lang AS fl ON f.id_feature = fl.id_feature
			LEFT JOIN
				ps_feature_value AS fv ON f.id_feature = fv.id_feature
			LEFT JOIN
				ps_feature_value_lang AS fvl ON fv.id_feature_value = fvl.id_feature_value
			LEFT JOIN
				ps_feature_product AS fp ON fv.id_feature_value = fp.id_feature_value
			LEFT JOIN
				ps_product AS p ON fp.id_product = p.id_product
			LEFT JOIN
				ps_category_product AS cp ON fp.id_product = cp.id_product
			WHERE
				fl.id_lang = '.$id_lang.'
				AND fvl.id_lang = '.$id_lang.'
				AND cp.id_category = '.(int)$categoryID.'
				AND p.active = 1
				AND f.id_feature IN ('.implode(',',$feature_ids).')
			GROUP BY fv.id_feature_value;';
				
			$features = Db::getInstance()->executeS($sql);
			
			// Regroupe par id_feature
			$features_group = Tools::group_by($features, 'id_feature');

			$html .= '<form action="#" id="layered_form"><div>';
			foreach($features_group as $id_feature => $fg){
				$html .= '<div class="layered_filter">
							<div class="selector" style="width: 200px;">
								<select class="select form-control open-sort">';
								if(count($fg)){			
									$html .= '<option value="">'.$fg[0]['feature_name'].'</option>';
									foreach($fg as $fv){
										$id_feature_value = $fv['id_feature_value'];
										$feature_value = $fv['feature_value'].' ('.$fv['product_number'].')';
										$value = $id_feature_value.'_'.$id_feature;
										$html .= '<option id="layered_id_feature_'.$id_feature_value.'" value="'.$value.'">'.$feature_value.'</option>';
									}
								}
				$html .=  '</select>
							</div>
						</div>';
			}
			
			$html .= '</div> 
					<input type="hidden" name="id_category_layered" value="'.(int)$categoryID.'">
				</form>';

		}

        return $html;
    }
	
	public static function group_by($array, $key) {
		$return = array();
		foreach($array as $val) {
			$return[$val[$key]][] = $val;
		}
		return $return;
	}
	
    public static function str_starts_with($haystack, $needle) {
        return (string)$needle !== '' && strncmp($haystack, $needle, strlen($needle)) === 0;
    }
	
	public static function getActualites() {
		$actualites = CMS::getCMSPages(1, 2, true);
        return $actualites;
    }
	
	public static function getMoisFr() {
		$mois = ['0','janvier','fevrier','mars','avril','mai','juin','juillet','aout','septembre','octobre','novembre','decembre'];
		$mois_fr = $mois[date('n')];
        return $mois_fr;
    }
	public static function getMoisFrAccent() {
		$mois = ['0','Janvier','Février','Mars','Avril','Mai','Juin','Juillet','Août','Septembre','Octobre','Novembre','Décembre'];
		$mois_fr = $mois[date('n')];
        return $mois_fr;
    }
	
	public static function jsonDecode($json, $assoc = false) {
		if (function_exists('json_decode'))
			return json_decode($json, $assoc);
		else
		{
			include_once(_PS_TOOL_DIR_.'json/json.php');
			$pear_json = new Services_JSON(($assoc) ? SERVICES_JSON_LOOSE_TYPE : 0);
			return $pear_json->decode($json);
		}
	}

	public static function jsonEncode($data) {
		if (function_exists('json_encode'))
			return json_encode($data);
		else
		{
			include_once(_PS_TOOL_DIR_.'json/json.php');
			$pear_json = new Services_JSON();
			return $pear_json->encode($data);
		}
	}
	
	public static function aw_subscribe($email, $list_ID, $action = 'addnoforce'){

		/*
		Action to be performed on the contacts for this list:

		addforce - Add the contacts to this list and subscribe all of them. Any contacts already present in the list and unsubscribed from it will be forcibly subscribed once again.

		addnoforce - Add the contacts to this list and subscribe them to it. Any contacts already present will retain their subscription status, i.e. if a contact is part of the list, but unsubscribed, it will not be forcibly subscribed again.

		remove - Remove the contacts from this list.

		unsub - Unsubscribe the contacts from this list.
		*/
		
		//$user = '34b10e378c3e0fa97459c5c143f5ec58';  
		//$pass = '548160ec9d9e64da604c578c68636f08';
		
		$user = Configuration::get('AW_MAILSMAILJET_LOGIN');
		$pass =  Configuration::get('AW_MAILSMAILJET_KEY');
			
		$url = 'https://api.mailjet.com/v3/REST/contactslist/' .$list_ID. '/managecontact';


		$auth_key = $user.":".$pass;
		$encoded_auth_key = base64_encode($auth_key);
		$headers = array();
		$headers[] = 'Authorization: Basic '.$encoded_auth_key;

		$body = [
		  'Action' => $action,
		  'Email' => $email 
		];

		$ch = curl_init();
		$opt = array(
		  CURLOPT_POSTFIELDS => $body,
		  CURLOPT_HTTPHEADER => $headers,
		  CURLOPT_URL => $url,
		  CURLOPT_FRESH_CONNECT => 1,
		  CURLOPT_RETURNTRANSFER => 1,
		  CURLOPT_FORBID_REUSE => 1,
		  CURLOPT_TIMEOUT => 4,
		  CURLOPT_SSL_VERIFYHOST => 0,
		  CURLOPT_SSL_VERIFYPEER => 0
		);
		curl_setopt_array($ch, $opt);
		  
		$result = curl_exec($ch);
		
		//error_log(print_r($result,true));
		
		if ($result == false) {
			echo  "error :".curl_error($ch);
		}
		curl_close($ch);
	}
	
	public static function get_id_newsletter_bonsplans(){
		return 2074282; // l'id de la liste Mailjet
	}
	
	public static function get_id_newsletter_cyril(){
		return 10287721; // l'id de la liste Mailjet
	}
	
	// Tri par meilleures ventes
	public static function getProductsOrder($type, $value = null, $prefix = false)
    {
        switch ($type) {
            case 'by':
                $list = [0 => 'name', 1 => 'price', 2 => 'date_add', 3 => 'date_upd', 4 => 'position', 5 => 'manufacturer_name', 6 => 'quantity', 7 => 'reference', 8 => 'sales'];
                $value = (null === $value || $value === false || $value === '') ? (int) Configuration::get('PS_PRODUCTS_ORDER_BY') : $value;
                $value = (isset($list[$value])) ? $list[$value] : ((in_array($value, $list)) ? $value : 'position');
                $order_by_prefix = '';
                if ($prefix) {
                    if ($value == 'id_product' || $value == 'date_add' || $value == 'date_upd' || $value == 'price' || $value == 'sales') {
                        $order_by_prefix = 'p.';
                    } elseif ($value == 'name') {
                        $order_by_prefix = 'pl.';
                    } elseif ($value == 'manufacturer_name' && $prefix) {
                        $order_by_prefix = 'm.';
                        $value = 'name';
                    } elseif ($value == 'position' || empty($value)) {
                        $order_by_prefix = 'cp.';
                    }
                }
                return $order_by_prefix . $value;

            break;

            case 'way':
                $value = (null === $value || $value === false || $value === '') ? (int) Configuration::get('PS_PRODUCTS_ORDER_WAY') : $value;
                $list = [0 => 'asc', 1 => 'desc'];

                return (isset($list[$value])) ? $list[$value] : ((in_array($value, $list)) ? $value : 'asc');

            break;
        }
    }
	
	public static function getWishlists(){
		$res = [
			'wishlists' => 0,
			'logged' => 0,
		];
		$context = Context::getContext();	
		if(!isset($context->customer->id)){
			return $res;
		}
		
		$res['logged'] = 1;
		$id_customer = $context->customer->id;
			
		$sql = 'SELECT id_wishlist, name, counter, `default` FROM ps_wishlist WHERE id_customer = '. (int) $id_customer;

		$wishlists = Db::getInstance()->executeS($sql);
		
		if(is_array($wishlists) && count($wishlists)){
			$res['wishlists'] = $wishlists;
		}
		return $res;
	}
	        
}
