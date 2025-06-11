<?php

if (!defined('_PS_VERSION_')) {
    exit;
}

use PrestaShop\PrestaShop\Core\Grid\Filter\Filter;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\DataColumn;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class Utm extends Module
{
    public function __construct()
    {
        $this->name = 'utm';
        $this->version = '1.0.0';
        $this->author = 'Andy - Anjou Web';
        $this->need_instance = 0;
        $this->ps_versions_compliancy = [
            'min' => '1.6',
            'max' => _PS_VERSION_
        ];
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('UTM');
        $this->description = $this->l('Gestion des flux UTM');

        $this->confirmUninstall = $this->l('Êtes-vous sûr de vouloir désinstaller ce module ?');
    }

    public function install()
    {
        if (Shop::isFeatureActive()) {
            Shop::setContext(Shop::CONTEXT_ALL);
        }

        if (
            !parent::install() || !$this->registerHook('header') || !$this->registerHook('actionAuthentication') || !$this->registerHook('actionCustomerAccountAdd') || !$this->registerHook('actionValidateOrder')
            || !$this->registerHook('displayAdminOrder') || !$this->registerHook('displayAdminCustomers')
            || !Configuration::updateValue('UTM_COOKIE_DURATION', 7) || !Configuration::updateValue('UTM_COOKIE_EXPIRE', "no")
            || !Configuration::updateValue("UTM_SOURCE_VALUE", "yes") || !Configuration::updateValue("UTM_MEDIUM_VALUE", "yes") || !Configuration::updateValue("UTM_CAMPAIGN_VALUE", "yes")
            || !Configuration::updateValue("UTM_CRON_VALUE", $this->generateRandomString(30))
            || !$this->createTabLink()
        ) {
            return false;
        }

        Db::getInstance()->execute('ALTER TABLE `' . _DB_PREFIX_ . 'customer` ADD `utm_source` VARCHAR(255) NOT NULL DEFAULT "";');
        Db::getInstance()->execute('ALTER TABLE `' . _DB_PREFIX_ . 'customer` ADD `utm_medium` VARCHAR(255) NOT NULL DEFAULT "";');
        Db::getInstance()->execute('ALTER TABLE `' . _DB_PREFIX_ . 'customer` ADD `utm_campaign` VARCHAR(255) NOT NULL DEFAULT "";');
        Db::getInstance()->execute('ALTER TABLE `' . _DB_PREFIX_ . 'customer` ADD `utm_expire` datetime NULL DEFAULT NULL;');

        Db::getInstance()->execute('ALTER TABLE `' . _DB_PREFIX_ . 'orders` ADD `utm_source` VARCHAR(255) NOT NULL DEFAULT "";');
        Db::getInstance()->execute('ALTER TABLE `' . _DB_PREFIX_ . 'orders` ADD `utm_medium` VARCHAR(255) NOT NULL DEFAULT "";');
        Db::getInstance()->execute('ALTER TABLE `' . _DB_PREFIX_ . 'orders` ADD `utm_campaign` VARCHAR(255) NOT NULL DEFAULT "";');

        return true;
    }

    public function uninstall()
    {
        if (
            !parent::uninstall() || !Configuration::deleteByName('UTM_COOKIE_DURATION') || !Configuration::deleteByName('UTM_COOKIE_EXPIRE')
            || !Configuration::deleteByName('UTM_SOURCE_VALUE') || !Configuration::deleteByName('UTM_MEDIUM_VALUE') || !Configuration::deleteByName('UTM_CAMPAIGN_VALUE')
        ) {
            return false;
        }

        Db::getInstance()->execute('ALTER TABLE `' . _DB_PREFIX_ . 'customer` DROP `utm_source`;');
        Db::getInstance()->execute('ALTER TABLE `' . _DB_PREFIX_ . 'customer` DROP `utm_medium`;');
        Db::getInstance()->execute('ALTER TABLE `' . _DB_PREFIX_ . 'customer` DROP `utm_campaign`;');
        Db::getInstance()->execute('ALTER TABLE `' . _DB_PREFIX_ . 'customer` DROP `utm_expire`;');

        Db::getInstance()->execute('ALTER TABLE `' . _DB_PREFIX_ . 'orders` DROP `utm_source`;');
        Db::getInstance()->execute('ALTER TABLE `' . _DB_PREFIX_ . 'orders` DROP `utm_medium`;');
        Db::getInstance()->execute('ALTER TABLE `' . _DB_PREFIX_ . 'orders` DROP `utm_campaign`;');

        //On reset le main Menu 
        $data = Db::getInstance()->executeS('SELECT id_tab FROM `' . _DB_PREFIX_ . 'tab` WHERE `class_name` = "AdminUTMTab"');
        Db::getInstance()->execute('DELETE FROM `' . _DB_PREFIX_ . 'tab` WHERE id_tab ='.$data[0]["id_tab"]);
        Db::getInstance()->execute('DELETE FROM `' . _DB_PREFIX_ . 'tab_lang` WHERE id_tab ='.$data[0]["id_tab"]);

        return true;
    }

    //Fonction qui s'éxécute lorsqu'on appuie sur "Configurer" dans la page "Module"
    public function getContent()
    {
        $output = null;

        if (Tools::isSubmit('submitCookie')) {
            $cookieDuration = strval(Tools::getValue('UTM_COOKIE_DURATION'));
            $cookieExpiration = strval(Tools::getValue('UTM_COOKIE_EXPIRE'));

            if (!$cookieDuration || empty($cookieDuration) || !$cookieExpiration || empty($cookieExpiration)) {
                $output .= $this->displayError($this->l('Invalid Configuration value'));
            } else {
                Configuration::updateValue('UTM_COOKIE_DURATION', $cookieDuration);
                Configuration::updateValue('UTM_COOKIE_EXPIRE', $cookieExpiration);
                $output .= $this->displayConfirmation($this->l('Settings updated'));
            }
        }

        if (Tools::isSubmit('submitDisplay')) {
            $utm_source = strval(Tools::getValue('UTM_SOURCE_VALUE'));
            $utm_medium = strval(Tools::getValue('UTM_MEDIUM_VALUE'));
            $utm_campaign = strval(Tools::getValue('UTM_CAMPAIGN_VALUE'));

            if (!$utm_source || empty($utm_source) || !$utm_medium || empty($utm_medium) || !$utm_campaign || empty($utm_campaign)) {
                $output .= $this->displayError($this->l('Informations non valides'));
            } else {
                Configuration::updateValue('UTM_SOURCE_VALUE', $utm_source);
                Configuration::updateValue('UTM_MEDIUM_VALUE', $utm_medium);
                Configuration::updateValue('UTM_CAMPAIGN_VALUE', $utm_campaign);
                $output .= $this->displayConfirmation($this->l('Paramètres mis à jour'));
            }
        }

        return $output . $this->displayForm();
    }

    public function displayForm()
    {
        $this->context->smarty->assign([
            'CRON_URL' => "https://" . Configuration::get("PS_SHOP_DOMAIN_SSL") . "/modules/utm/cron/cron.php?token=".Configuration::get("UTM_CRON_VALUE"),
            'UTM_COOKIE_DURATION' => Configuration::get("UTM_COOKIE_DURATION"),
            'UTM_SOURCE_VALUE' => Configuration::get("UTM_SOURCE_VALUE"),
            'UTM_MEDIUM_VALUE' => Configuration::get("UTM_MEDIUM_VALUE"),
            'UTM_CAMPAIGN_VALUE' => Configuration::get("UTM_CAMPAIGN_VALUE"),
            'UTM_COOKIE_EXPIRE' => Configuration::get("UTM_COOKIE_EXPIRE"),
        ]);

        return $this->display(__FILE__, 'views/templates/admin/manage.tpl');
    }

    //Fonction pour ajouter le moduleAdminController au main menu
    public function createTabLink()
    {
        //On créer le parent UTM
        $tab = new Tab;
        foreach(Language::getLanguages() as $lang){ $tab->name[$lang["id_lang"]] = $this->l("UTM"); }
        $tab->class_name = 'AdminUTMTab';
        $tab->id_parent = 0;
        $tab->add();

        //On recupere l'id du parent
        $data = Db::getInstance()->executeS('SELECT id_tab FROM `' . _DB_PREFIX_ . 'tab` WHERE `class_name` = "AdminUTMTab"');

        //On créer l'enfant StatsUtm
        $tab = new Tab;
        foreach(Language::getLanguages() as $lang){ $tab->name[$lang["id_lang"]] = $this->l("StatsUTM");}
        $tab->class_name = 'AdminUtmStats';
        $tab->module = $this->name;
        $tab->id_parent = $data[0]["id_tab"];
        $tab->icon = 'settings';
        $tab->add();
        return true;
    }

    // Hook du header présent sur toutes les pages 
    public function hookDisplayHeader()
    {
        //Si l'utilisateur est connecté 
        if ($this->context->customer->id > 0) {
            //on ajoute les datas dans le client et on reset les cookies
            if (isset($_GET["utm_source"]) || isset($_GET["utm_medium"]) || isset($_GET["utm_campaign"])) {
                $this->resetUtmCustomer($this->context->customer->id);
                //Si on conserve les date d'expiration, alors l'ajoute dans le customer
                if (Configuration::get("UTM_COOKIE_EXPIRE") == "yes")
                    Db::getInstance()->execute('UPDATE `' . _DB_PREFIX_ . 'customer` SET `utm_expire` = "' . date("Y-m-d H:i:s", time() + 60 * 60 * 24 * Configuration::get('UTM_COOKIE_DURATION')) . '" WHERE `id_customer` = ' . $this->context->customer->id);
            }

            if (isset($_GET["utm_source"]))
                Db::getInstance()->execute('UPDATE `' . _DB_PREFIX_ . 'customer` SET `utm_source` = "' . pSQL($_GET["utm_source"]) . '" WHERE `id_customer` = ' . $this->context->customer->id);

            if (isset($_GET["utm_medium"]))
                Db::getInstance()->execute('UPDATE `' . _DB_PREFIX_ . 'customer` SET `utm_medium` = "' . pSQL($_GET["utm_medium"]) . '" WHERE `id_customer` = ' . $this->context->customer->id);

            if (isset($_GET["utm_campaign"]))
                Db::getInstance()->execute('UPDATE `' . _DB_PREFIX_ . 'customer` SET `utm_campaign` = "' . pSQL($_GET["utm_campaign"]) . '" WHERE `id_customer` = ' . $this->context->customer->id);

            $this->resetCookies();
        } else {
            //sinon on ajoute les datas en cookie
            if (isset($_GET["utm_source"]) || isset($_GET["utm_medium"]) || isset($_GET["utm_campaign"])) {
                $this->resetCookies();
                if (Configuration::get("UTM_COOKIE_EXPIRE") == "yes")
                    setcookie("utm_expire", time() + 60 * 60 * 24 * Configuration::get('UTM_COOKIE_DURATION'), time() + 60 * 60 * 24 * Configuration::get('UTM_COOKIE_DURATION'), "/");
            }

            if (isset($_GET["utm_source"]))
                setcookie("utm_source", $_GET["utm_source"], time() + 60 * 60 * 24 * Configuration::get('UTM_COOKIE_DURATION'), "/");

            if (isset($_GET["utm_medium"]))
                setcookie("utm_medium", $_GET["utm_medium"], time() + 60 * 60 * 24 * Configuration::get('UTM_COOKIE_DURATION'), "/");

            if (isset($_GET["utm_campaign"]))
                setcookie("utm_campaign", $_GET["utm_campaign"], time() + 60 * 60 * 24 * Configuration::get('UTM_COOKIE_DURATION'), "/");
        }
    }

    //Hook qui s'éxécute juste apres la connexion d'un utilisateur
    public function hookActionAuthentication(array $params)
    {
        $idUser = ($params["cookie"])->id_customer;
        
        if (isset($_COOKIE["utm_source"]) || isset($_COOKIE["utm_medium"]) || isset($_COOKIE["utm_campaign"]) || isset($_COOKIE["utm_expire"])){
            $this->resetUtmCustomer($idUser);
        }

        if (isset($_COOKIE["utm_source"])){
            Db::getInstance()->execute('UPDATE `' . _DB_PREFIX_ . 'customer` SET `utm_source` = "' . pSQL($_COOKIE["utm_source"]) . '" WHERE `id_customer` = ' . $idUser);
        }

        if (isset($_COOKIE["utm_medium"])){
            Db::getInstance()->execute('UPDATE `' . _DB_PREFIX_ . 'customer` SET `utm_medium` = "' . pSQL($_COOKIE["utm_medium"]) . '" WHERE `id_customer` = ' . $idUser);
        }

        if (isset($_COOKIE["utm_campaign"])){
            Db::getInstance()->execute('UPDATE `' . _DB_PREFIX_ . 'customer` SET `utm_campaign` = "' . pSQL($_COOKIE["utm_campaign"]) . '" WHERE `id_customer` = ' . $idUser);
        }

        if (isset($_COOKIE["utm_expire"])){
            Db::getInstance()->execute('UPDATE `' . _DB_PREFIX_ . 'customer` SET `utm_expire` = "' . date("Y-m-d H:i:s", pSQL($_COOKIE["utm_expire"])) . '" WHERE `id_customer` = ' . $idUser);
        }

        $this->resetCookies();
    }

    //Hook qui s'éxécute juste après la création d'un compte utilisateur
    public function hookActionCustomerAccountAdd(array $params)
    {
        $idUser = ($params["newCustomer"])->id;

        if (isset($_COOKIE["utm_source"]))
            Db::getInstance()->execute('UPDATE `' . _DB_PREFIX_ . 'customer` SET `utm_source` = "' . pSQL($_COOKIE["utm_source"]) . '" WHERE `id_customer` = ' . $idUser);

        if (isset($_COOKIE["utm_medium"]))
            Db::getInstance()->execute('UPDATE `' . _DB_PREFIX_ . 'customer` SET `utm_medium` = "' . pSQL($_COOKIE["utm_medium"]) . '" WHERE `id_customer` = ' . $idUser);

        if (isset($_COOKIE["utm_campaign"]))
            Db::getInstance()->execute('UPDATE `' . _DB_PREFIX_ . 'customer` SET `utm_campaign` = "' . pSQL($_COOKIE["utm_campaign"]) . '" WHERE `id_customer` = ' . $idUser);

        if (isset($_COOKIE["utm_expire"]))
            Db::getInstance()->execute('UPDATE `' . _DB_PREFIX_ . 'customer` SET `utm_expire` = "' . date("Y-m-d H:i:s", pSQL($_COOKIE["utm_expire"])) . '" WHERE `id_customer` = ' . $idUser);

        $this->resetCookies();
    }

    //Hook qui s'éxécute lors de l'ajout d'une commande / remplaçable par celui ci : actionPaymentConfirmation qui s'éxécute lorsque le paiement est validé
    public function hookActionValidateOrder(array $params)
    {
        $id_cart = ($params["cart"])->id;

        $data = Db::getInstance()->executeS('SELECT * FROM `' . _DB_PREFIX_ . 'orders` WHERE `id_cart` = ' . $id_cart);

        //On récupere les id de l'utilisateur et de la commande
        $idOrder = $data[0]["id_order"];
        $idUser = $data[0]["id_customer"];
        
        //On récupere les informations UTM stockés dans l'utilisateur
        $userData = Db::getInstance()->executeS('SELECT * FROM `' . _DB_PREFIX_ . 'customer` WHERE `id_customer` = ' . $idUser);

        $utm_source = $userData[0]["utm_source"];
        $utm_medium = $userData[0]["utm_medium"];
        $utm_campaign = $userData[0]["utm_campaign"];

        //On ajoute les données UTM de l'utilisateur à la commande
        Db::getInstance()->execute('UPDATE `' . _DB_PREFIX_ . 'orders` SET `utm_source` = "' . $utm_source . '", `utm_medium` = "' . $utm_medium . '", `utm_campaign` = "' . $utm_campaign . '" WHERE `id_order` = ' . $idOrder);

        //On enleve les données UTM de l'utilisateur
        $this->resetUtmCustomer($idUser);
    }

    //Hook qui s'éxécute lors de l'affichage d'un detail commande
    public function hookDisplayAdminOrder($params)
    {
        $data = Db::getInstance()->executeS('SELECT * FROM `' . _DB_PREFIX_ . 'orders` WHERE `id_order` = ' . $params["id_order"]);

        $this->context->smarty->assign([
            "utm_source" => (empty($data[0]["utm_source"])) ? $this->l('Vide') : $data[0]["utm_source"],
            "utm_medium" => (empty($data[0]["utm_medium"])) ? $this->l('Vide') : $data[0]["utm_medium"],
            "utm_campaign" => (empty($data[0]["utm_campaign"])) ? $this->l('Vide') : $data[0]["utm_campaign"]
        ]);

        return $this->context->smarty->fetch($this->local_path . 'views/templates/hook/admin/displayAdminOrder.tpl');
    }

    //Hook qui s'éxécute lors de l'affichage d'un detail client
    public function hookDisplayAdminCustomers($params)
    {

        $data = Db::getInstance()->executeS('SELECT * FROM `' . _DB_PREFIX_ . 'customer` WHERE `id_customer` = ' . $params["id_customer"]);

        $this->context->smarty->assign([
            "utm_source" => (empty($data[0]["utm_source"])) ? $this->l('Vide') : $data[0]["utm_source"],
            "utm_medium" => (empty($data[0]["utm_medium"])) ? $this->l('Vide') : $data[0]["utm_medium"],
            "utm_campaign" => (empty($data[0]["utm_campaign"])) ? $this->l('Vide') : $data[0]["utm_campaign"],
            "utm_expire" => (empty($data[0]["utm_expire"])) ? $this->l('Vide') : (new DateTime($data[0]["utm_expire"]))->format("d/m/Y H:i:s"),
            "idCustomer" => $params["id_customer"]
        ]);

        return $this->context->smarty->fetch($this->local_path . 'views/templates/hook/admin/displayAdminCustomers.tpl');
    }

    private function resetCookies()
    {
        setcookie("utm_source", "", 0, "/");
        setcookie("utm_medium", "", 0, "/");
        setcookie("utm_campaign", "", 0, "/");
        setcookie("utm_expire", "", 0, "/");
    }

    private function resetUtmCustomer($idUser)
    {
        Db::getInstance()->execute('UPDATE `' . _DB_PREFIX_ . 'customer` SET `utm_source` = "", `utm_medium` = "", `utm_campaign` = "", `utm_expire` = NULL WHERE `id_customer` = ' . $idUser);
    }

    private function generateRandomString($length = 10) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }
	
	public function hookActionOrderGridDefinitionModifier(array $params)
    {
		
		$utm = [];
		
		if(Configuration::get("UTM_SOURCE_VALUE") == 'yes'){
			$utm['utm_source'] = 'UTM SOURCE';
		}
		
		if(Configuration::get("UTM_MEDIUM_VALUE") == 'yes'){
			$utm['utm_medium'] = 'UTM MEDIUM';
		}
		
		if(Configuration::get("UTM_CAMPAIGN_VALUE") == 'yes'){
			$utm['utm_campaign'] = 'UTM CAMPAIGN';
		}
		
		if (_PS_VERSION_ >= '1.7.7.0') {
			foreach($utm as $key => $name){
				$definition = $params['definition'];
				$definition
					->getColumns()
					->addAfter(
						'osname',
						(new DataColumn($key))
							->setName($this->l($name))
							->setOptions(
								[
									'field' => $key
								]
							)
					);
				$filters = $definition->getFilters();
				$filters->add(
					(new Filter($key, TextType::class))
						->setTypeOptions([
							'required' => false,
							'attr' => [
								'placeholder' => '',
							],
						])
						->setAssociatedColumn($key)
				);
			}
		}
    }
	
	public function hookActionOrderGridQueryBuilderModifier(array $params)
    {
		$utm = [];
		
		if(Configuration::get("UTM_SOURCE_VALUE") == 'yes'){
			$utm['utm_source'] = 'UTM SOURCE';
		}
		
		if(Configuration::get("UTM_MEDIUM_VALUE") == 'yes'){
			$utm['utm_medium'] = 'UTM MEDIUM';
		}
		
		if(Configuration::get("UTM_CAMPAIGN_VALUE") == 'yes'){
			$utm['utm_campaign'] = 'UTM CAMPAIGN';
		}
		
        if (_PS_VERSION_ >= '1.7.7.0') {
			$searchQueryBuilder = $params['search_query_builder'];
			$searchCriteria = $params['search_criteria'];
			foreach($utm as $key => $name){
				$searchQueryBuilder->addSelect(
					'o.`'.$key.'`
					AS `'.$key.'`'
				);
				foreach ($searchCriteria->getFilters() as $filterName => $filterValue) {
					if ($key === $filterName) {
						$searchQueryBuilder->andWhere('o.`'.$key.'` LIKE "%'.$filterValue.'%"');
						$searchQueryBuilder->setParameter($key, $filterValue);
					}
				}
			}
        }
    }
}
