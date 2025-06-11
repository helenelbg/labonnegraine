<?php

class FichesEnvoiClient extends Module
{

    private $_html = '';
    private $_query = '';
    private $_option = 0;
    private $_id_product = 0;
    private $statut_declenchement = 4; // En cours de livraison

    function __construct()
    {
        $this->name = 'fichesenvoiclient';
        $this->tab = 'dashboard';
        $this->version = 1.0;

        parent::__construct();

        $this->displayName = 'Envoi des fiches aux clients';
        $this->description = '';
    }

    public function install()
    {
        $queries = array(
                'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'fichiers_infos` (
                    `id_fichier` int(11) NOT NULL AUTO_INCREMENT,
                    `nom_fichier` varchar(500) NOT NULL,
                    `fichier` varchar(500) NOT NULL,
                    `date_add` datetime NOT NULL,
                    `date_maj` datetime NOT NULL,
                    PRIMARY KEY (`id_fichier`)
                ) ENGINE=' . (defined('ENGINE_TYPE') ? ENGINE_TYPE : 'MyISAM'),
			  
                'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'fichiers_categories_produits` (
                    `id_categorie_produit` int(11) NOT NULL AUTO_INCREMENT,
                    `id_fichier` int(11) NOT NULL,
                    `id_categorie` int(11) NOT NULL,
                    `id_produit` int(11) NOT NULL,
                    PRIMARY KEY (`id_categorie_produit`)
                ) ENGINE=' . (defined('ENGINE_TYPE') ? ENGINE_TYPE : 'MyISAM')
        );
        //$this->registerHook('updateOrderStatus');
        $this->registerHook('actionOrderStatusUpdate');
        
        foreach ($queries as $query)
        {
            if ( ! Db::getInstance()->Execute($query))
            {
                parent::uninstall();
                return false;
            }
        }     
        return (parent::install() AND $this->installModuleTab('FichesEnvoiClientTab', array(intval(Configuration::get('PS_LANG_DEFAULT')) => utf8_encode('Envoi des fiches aux clients')), Tab::getIdFromClassName('AdminCatalog')));
    }
    
    private function installModuleTab($tabClass, $tabName, $idTabParent) {
    	$tab = new Tab();
    	$tab->name = $tabName;
    	$tab->class_name = $tabClass;
    	$tab->module = $this->name;
    	$tab->id_parent = $idTabParent;
    	return $tab->save();
    }
    
    function uninstall()	{
   	return parent::uninstall() && $this->uninstallModuleTab('FichesEnvoiClientTab');
    }

  
    private function uninstallModuleTab($tabClass) {
    	$idTab = Tab::getIdFromClassName($tabClass);
     	if($idTab != 0) {
    		$tab = new Tab($idTab);
    		$tab->delete();
     		return true;
    	}
    	return false;
    }
    
    public function hookActionOrderStatusUpdate($params)
    {   
        if(intval($params['newOrderStatus']->id) == $this->statut_declenchement)
        {   
            $chemin_destination_mails = dirname(__FILE__)."/mails/";
            $chemin_destination_fichiers = dirname(__FILE__)."/fiches/";
            $products_ids = array();
            $products_name = array();
            $order = new Order($params['id_order']);  
            $produits_commande = $order->getProductsDetail();
			
			
            foreach($produits_commande as $product)
            {
				
				// Dorian - début
				// N'envoie pas la fiche si tous les produits d'une même famille sont des plants.
				
				$isCheckIsPlant = false;
				
				$product_attribute_id = $product['product_attribute_id'];
				$objproduct = new Product($product['product_id']);
				$id_lang = 1; // FR
				$declinaisons = $objproduct->getAttributeCombinationsById($product_attribute_id, $id_lang);

				foreach($declinaisons as $decl)
				{
					$id_attribute = $decl['id_attribute'];
					if ( in_array($id_attribute, Cart::getPlantIds()) )
					{
						$isCheckIsPlant = true;
						break;
					}
				}
				if($isCheckIsPlant){
					continue;
				}
				
				// Dorian - fin
				
                $products_ids[] = $product['product_id'];
                $products_name[intval($product['product_id'])] = $product['product_name'];
            }
            
            $attachement = array();
            $produits_name_array = array();
            
            $customer = new Customer((int)($order->id_customer));
            $sujet_mail = utf8_encode("Conseils pratiques suite à votre commande n°").$params['id_order'];
            
            $query = "SELECT GROUP_CONCAT(id_produit) as list_produits, id_fichier  FROM " . _DB_PREFIX_ . "fichiers_categories_produits WHERE id_produit IN (".implode(',',$products_ids).") GROUP BY id_fichier";
            $result = Db::getInstance()->ExecuteS($query);
            foreach($result as $res_prod)
            {     
                $produits_name = array();
                $ids_prod = explode(',',$res_prod['list_produits']);                
                $id_fichier = $res_prod['id_fichier'];
                foreach($ids_prod as $id)
                {
                    $produits_name[] = "&nbsp;&nbsp;&nbsp;- ".$products_name[intval($id)];
                }
                $details_fichier = self::fichier_details($id_fichier);
                
               
                $nom_fichier = $details_fichier['fichier'];   
                //Extension
                $exp_ext = explode('.', $nom_fichier);
                $extension = $exp_ext[count($exp_ext)-1];
                $fichier_source = self::clear_nom_fichier($details_fichier['nom_fichier']).".".$extension;    
                
                $produits_text_html = "<b><u><i>Le fichier \"".$fichier_source."\" concerne le(s) produit(s) suivant(s) : </i></u></b><br />";
                $produits_text_html .= implode('<br />', $produits_name); 
                $produits_name_array[] = $produits_text_html;
                       
                
                $array_pj = array();
                $array_pj['content'] = file_get_contents($chemin_destination_fichiers.$nom_fichier);
                $array_pj['name'] = $fichier_source;
                $array_pj['mime'] = mime_content_type($chemin_destination_fichiers.$nom_fichier);
                $attachement[] = $array_pj;
            }
            
            $produits_text_html_fin = implode('<br /><br />',$produits_name_array);
            
            $templateVars = array(
                '{firstname}' => $customer->firstname,
                '{lastname}' => $customer->lastname,
                '{id_order}' => $params['id_order'],
                '{products_name_html}' => $produits_text_html_fin
            );
            if(!empty($attachement))
            {
				Mail::Send(
					Context::getContext()->language->id,
					'envoifiche',
					$sujet_mail,
					$templateVars,
					$customer->email, 
					$customer->firstname.' '.$customer->lastname,
					null,
					null,
					$attachement, 
					null, 
					$chemin_destination_mails,
					true
				);

                /*self::SendAW(2, 'envoifiche', $sujet_mail, $templateVars,
                    $customer->email, $customer->firstname.' '.$customer->lastname, NULL, NULL, $attachement, NULL,
                    $chemin_destination_mails, true);*/
            }
        }
    }
    
    
    
    public static function SendAW($id_lang, $template, $subject, $templateVars, $to,
		$toName = null, $from = null, $fromName = null, $fileAttachment = null, $modeSMTP = null, $templatePath = _PS_MAIL_DIR_, $die = false)
	{
		$configuration = Configuration::getMultiple(array('PS_SHOP_EMAIL', 'PS_MAIL_METHOD', 'PS_MAIL_SERVER', 'PS_MAIL_USER', 'PS_MAIL_PASSWD', 'PS_SHOP_NAME', 'PS_MAIL_SMTP_ENCRYPTION', 'PS_MAIL_SMTP_PORT', 'PS_MAIL_METHOD', 'PS_MAIL_TYPE'));
		if (!isset($configuration['PS_MAIL_SMTP_ENCRYPTION'])) $configuration['PS_MAIL_SMTP_ENCRYPTION'] = 'off';
		if (!isset($configuration['PS_MAIL_SMTP_PORT'])) $configuration['PS_MAIL_SMTP_PORT'] = 'default';

		if (!isset($from)) $from = $configuration['PS_SHOP_EMAIL'];
		if (!isset($fromName)) $fromName = $configuration['PS_SHOP_NAME'];

		if (!empty($from) && !Validate::isEmail($from))
		{
 			Tools::dieOrLog(Tools::displayError('Error: parameter "from" is corrupted'), $die);
 			return false;
		}
		if (!empty($fromName) && !Validate::isMailName($fromName))
		{
	 		Tools::dieOrLog(Tools::displayError('Error: parameter "fromName" is corrupted'), $die);
	 		return false;
		}
		if (!is_array($to) && !Validate::isEmail($to))
		{
	 		Tools::dieOrLog(Tools::displayError('Error: parameter "to" is corrupted'), $die);
	 		return false;
		}

		if (!is_array($templateVars))
		{
	 		Tools::dieOrLog(Tools::displayError('Error: parameter "templateVars" is not an array'), $die);
	 		return false;
		}

		// Do not crash for this error, that may be a complicated customer name
		if (is_string($toName))
		{
			if (!empty($toName) && !Validate::isMailName($toName))
	 			$toName = null;
		}

		if (!Validate::isTplName($template))
		{
	 		Tools::dieOrLog(Tools::displayError('Error: invalid email template'), $die);
	 		return false;
		}

		if (!Validate::isMailSubject($subject))
		{
	 		Tools::dieOrLog(Tools::displayError('Error: invalid email subject'), $die);
	 		return false;
		}

		/* Construct multiple recipients list if needed */
		if (isset($to) && is_array($to))
		{
			$to_list = new Swift_RecipientList();
			foreach ($to as $key => $addr)
			{
				$to_name = null;
				$addr = trim($addr);
				if (!Validate::isEmail($addr))
				{
					Tools::dieOrLog(Tools::displayError('Error: invalid email address'), $die);
					return false;
				}
				if (is_array($toName))
				{
					if ($toName && is_array($toName) && Validate::isGenericName($toName[$key]))
						$to_name = $toName[$key];
				}
				if ($to_name == null)
					$to_name = $addr;
                /* Encode accentuated chars */
				$to_list->addTo($addr, '=?UTF-8?B?'.base64_encode($to_name).'?=');
			}
			$to_plugin = $to[0];
			$to = $to_list;
		} else {
			/* Simple recipient, one address */
			$to_plugin = $to;
			if ($toName == null)
				$toName = $to;
            /* Encode accentuated chars */
			$to = new Swift_Address($to, '=?UTF-8?B?'.base64_encode($toName).'?=');
		}
		try {
			/* Connect with the appropriate configuration */
			if ($configuration['PS_MAIL_METHOD'] == 2)
			{
				if (empty($configuration['PS_MAIL_SERVER']) || empty($configuration['PS_MAIL_SMTP_PORT']))
				{
					Tools::dieOrLog(Tools::displayError('Error: invalid SMTP server or SMTP port'), $die);
					return false;
				}
				$connection = new Swift_Connection_SMTP($configuration['PS_MAIL_SERVER'], $configuration['PS_MAIL_SMTP_PORT'],
								($configuration['PS_MAIL_SMTP_ENCRYPTION'] == 'ssl') ? Swift_Connection_SMTP::ENC_SSL :
								(($configuration['PS_MAIL_SMTP_ENCRYPTION'] == 'tls') ? Swift_Connection_SMTP::ENC_TLS : Swift_Connection_SMTP::ENC_OFF));
				$connection->setTimeout(20);
				if (!$connection)
				{
					return false;
				}
				if (!empty($configuration['PS_MAIL_USER']))
					$connection->setUsername($configuration['PS_MAIL_USER']);
				if (!empty($configuration['PS_MAIL_PASSWD']))
					$connection->setPassword($configuration['PS_MAIL_PASSWD']);
			}
			else
				$connection = new Swift_Connection_NativeMail();

			if (!$connection)
			{
				return false;
			}
			$swift = new Swift($connection, Configuration::get('PS_MAIL_DOMAIN'));
			/* Get templates content */
			$iso = Language::getIsoById((int)($id_lang));
			if (!$iso)
			{
				Tools::dieOrLog(Tools::displayError('Error - No ISO code for email'), $die);
				return false;
			}
			$template = $iso.'/'.$template;

			$moduleName = false;
			$overrideMail = false;

			// get templatePath
			if (preg_match('#'.__PS_BASE_URI__.'modules/#', $templatePath) && preg_match('#modules/([a-z0-9_-]+)/#ui', $templatePath, $res))
				$moduleName = $res[1];

			if ($moduleName !== false && (file_exists(_PS_THEME_DIR_.'modules/'.$moduleName.'/mails/'.$template.'.txt') ||
				file_exists(_PS_THEME_DIR_.'modules/'.$moduleName.'/mails/'.$template.'.html')))
				$templatePath = _PS_THEME_DIR_.'modules/'.$moduleName.'/mails/';
			else if (file_exists(_PS_THEME_DIR_.'mails/'.$template.'.txt') || file_exists(_PS_THEME_DIR_.'mails/'.$template.'.html'))
			{
				$templatePath = _PS_THEME_DIR_.'mails/';
				$overrideMail  = true;
			}
			else if (!file_exists($templatePath.$template.'.txt') || !file_exists($templatePath.$template.'.html'))
			{
				Tools::dieOrLog(Tools::displayError('Error - The following email template is missing:').' '.$templatePath.$template.'.txt', $die);
				return false;
			}
			$templateHtml = file_get_contents($templatePath.$template.'.html');
			$templateTxt = strip_tags(html_entity_decode(file_get_contents($templatePath.$template.'.txt'), null, 'utf-8'));

			if ($overrideMail && file_exists($templatePath.$iso.'/lang.php'))
					include_once($templatePath.$iso.'/lang.php');
			else if ($moduleName && file_exists($templatePath.$iso.'/lang.php'))
				include_once(_PS_THEME_DIR_.'mails/'.$iso.'/lang.php');
			else
				include_once(dirname(__FILE__).'/../../mails/'.$iso.'/lang.php');

			/* Create mail && attach differents parts */
			$message = new Swift_Message('['.Configuration::get('PS_SHOP_NAME').'] '.$subject);
			$message->headers->setEncoding('Q');
			$templateVars['{shop_logo}'] = (file_exists(_PS_IMG_DIR_.'logo_mail.jpg')) ?
				$message->attach(new Swift_Message_Image(new Swift_File(_PS_IMG_DIR_.'logo_mail.jpg'))) : ((file_exists(_PS_IMG_DIR_.'logo.jpg')) ?
					$message->attach(new Swift_Message_Image(new Swift_File(_PS_IMG_DIR_.'logo.jpg'))) : '');
			$templateVars['{shop_name}'] = Tools::safeOutput(Configuration::get('PS_SHOP_NAME'));
			$templateVars['{shop_url}'] = Tools::getShopDomain(true, true).__PS_BASE_URI__;
			$swift->attachPlugin(new Swift_Plugin_Decorator(array($to_plugin => $templateVars)), 'decorator');
			if ($configuration['PS_MAIL_TYPE'] == 3 || $configuration['PS_MAIL_TYPE'] == 2)
				$message->attach(new Swift_Message_Part($templateTxt, 'text/plain', '8bit', 'utf-8'));
			if ($configuration['PS_MAIL_TYPE'] == 3 || $configuration['PS_MAIL_TYPE'] == 1)
				$message->attach(new Swift_Message_Part($templateHtml, 'text/html', '8bit', 'utf-8'));
                        
                        if ($fileAttachment && isset($fileAttachment['content']) && isset($fileAttachment['name']) && isset($fileAttachment['mime']))
				$message->attach(new Swift_Message_Attachment($fileAttachment['content'], $fileAttachment['name'], $fileAttachment['mime']));
                        elseif ($fileAttachment && isset($fileAttachment[0]['content']) && isset($fileAttachment[0]['name']) && isset($fileAttachment[0]['mime']))
                        {
                            foreach($fileAttachment as $file_item)
                            {
                                $message->attach(new Swift_Message_Attachment($file_item['content'], $file_item['name'], $file_item['mime']));
                            }
                        }
			/* Send mail */
			$send = $swift->send($message, $to, new Swift_Address($from, $fromName));
			$swift->disconnect();
			return $send;
		}

		catch (Swift_ConnectionException $e)
		{
			return false;
		}
	}
        public static function fichier_details($id_fichier)
        {
            $query = "SELECT * FROM " . _DB_PREFIX_ . "fichiers_infos WHERE id_fichier = '" . $id_fichier . "';";

            $result = Db::getInstance()->ExecuteS($query);
            return $result[0];
        }
        
        public static function clear_nom_fichier($nom_fichier)
        {
             $a = array("'",' ', 'À', 'Á', 'Â', 'Ã', 'Ä', 'Å', 'Æ', 'Ç', 'È', 'É', 'Ê', 'Ë', 'Ì', 'Í', 'Î', 'Ï', 'Ð',
                'Ñ', 'Ò', 'Ó', 'Ô', 'Õ', 'Ö', 'Ø', 'Ù', 'Ú', 'Û', 'Ü', 'Ý', 'ß', 'à', 'á', 'â', 'ã',
                'ä', 'å', 'æ', 'ç', 'è', 'é', 'ê', 'ë', 'ì', 'í', 'î', 'ï', 'ñ', 'ò', 'ó', 'ô', 'õ',
                'ö', 'ø', 'ù', 'ú', 'û', 'ü', 'ý', 'ÿ', '?', '?', '?', '?', '?', '?', '?', '?', '?',
                '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?',
                '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?',
                '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?',
                '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '¼',
                '½', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '¦', '¨', '?', '?', '?', 
                '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', 
                '?', '¾', '?', '?', '?', '?', '´', '¸', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?',
                '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?');

            $b = array("_", '_','A', 'A', 'A', 'A', 'A', 'A', 'AE', 'C', 'E', 'E', 'E', 'E', 'I', 'I', 'I', 'I', 'D', 'N', 'O',
                'O', 'O', 'O', 'O', 'O', 'U', 'U', 'U', 'U', 'Y', 's', 'a', 'a', 'a', 'a', 'a', 'a', 'ae', 'c',
                'e', 'e', 'e', 'e', 'i', 'i', 'i', 'i', 'n', 'o', 'o', 'o', 'o', 'o', 'o', 'u', 'u', 'u', 'u',
                'y', 'y', 'A', 'a', 'A', 'a', 'A', 'a', 'C', 'c', 'C', 'c', 'C', 'c', 'C', 'c', 'D', 'd', 'D',
                'd', 'E', 'e', 'E', 'e', 'E', 'e', 'E', 'e', 'E', 'e', 'G', 'g', 'G', 'g', 'G', 'g', 'G', 'g',
                'H', 'h', 'H', 'h', 'I', 'i', 'I', 'i', 'I', 'i', 'I', 'i', 'I', 'i', 'IJ', 'ij', 'J', 'j', 'K',
                'k', 'L', 'l', 'L', 'l', 'L', 'l', 'L', 'l', 'L', 'l', 'N', 'n', 'N', 'n', 'N', 'n', 'n', 'O', 'o',
                'O', 'o', 'O', 'o', 'OE', 'oe', 'R', 'r', 'R', 'r', 'R', 'r', 'S', 's', 'S', 's', 'S', 's', 'S',
                's', 'T', 't', 'T', 't', 'T', 't', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'W',
                'w', 'Y', 'y', 'Y', 'Z', 'z', 'Z', 'z', 'Z', 'z', 's', 'f', 'O', 'o', 'U', 'u', 'A', 'a', 'I', 'i',
                'O', 'o', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'A', 'a', 'AE', 'ae', 'O', 'o');
            
            
            
            $nom_fichier = str_replace($a, $b, $nom_fichier);
            return $nom_fichier;
        }
}


    ?>
