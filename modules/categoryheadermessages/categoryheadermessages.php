<?php
/**
 * Module de Messages Commerciaux pour PrestaShop 8
 *
 * @author  Claude
 * @copyright  2025
 * @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

class CategoryHeaderMessages extends Module
{
    public function __construct()
    {
        $this->name = 'categoryheadermessages';
        $this->tab = 'front_office_features';
        $this->version = '1.0.0';
        $this->author = 'Claude';
        $this->need_instance = 0;
        $this->ps_versions_compliancy = [
            'min' => '8.0.0',
            'max' => _PS_VERSION_
        ];
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('Messages commerciaux en entête de catégorie');
        $this->description = $this->l('Permet d\'ajouter des messages commerciaux en entête des pages catégories');
        $this->confirmUninstall = $this->l('Êtes-vous sûr de vouloir désinstaller ce module?');
    }

    public function install()
    {
        return parent::install() &&
            $this->registerHook('displayHeaderCategory') &&
            $this->registerHook('actionAdminControllerSetMedia') &&
            $this->installDb() &&
            $this->installTab();
    }

    public function uninstall()
    {
        return parent::uninstall() &&
            $this->uninstallDb() &&
            $this->uninstallTab();
    }

    /**
     * Installation de la base de données
     */
    private function installDb()
    {
        $return = true;
    $return &= Db::getInstance()->execute('
        CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'category_header_message` (
            `id_message` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
            `active` TINYINT(1) UNSIGNED NOT NULL DEFAULT 1,
            `date_add` DATETIME NOT NULL,
            `date_upd` DATETIME NOT NULL,
            `date_start` DATETIME NOT NULL,
            `date_end` DATETIME NOT NULL,
            `position` INT(11) UNSIGNED NOT NULL DEFAULT 0,
            `type` VARCHAR(50) NOT NULL DEFAULT "standard", /* Ajout du champ type */
            `id_product` INT(11) UNSIGNED DEFAULT NULL, /* Ajout du champ id_product */
            `id_product_attribute` INT(11) UNSIGNED DEFAULT NULL, /* Ajout du champ id_product_attribute */
            PRIMARY KEY (`id_message`)
        ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8;
    ');

    // Le reste du code reste inchangé
    $return &= Db::getInstance()->execute('
        CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'category_header_message_lang` (
            `id_message` INT(11) UNSIGNED NOT NULL,
            `id_lang` INT(11) UNSIGNED NOT NULL,
            `title` VARCHAR(255) NOT NULL,
            `content` TEXT NOT NULL,
            `cta_text` VARCHAR(255) NOT NULL,
            `cta_link` VARCHAR(255) NOT NULL,
            PRIMARY KEY (`id_message`, `id_lang`)
        ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8;
    ');

    $return &= Db::getInstance()->execute('
        CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'category_header_message_category` (
            `id_message` INT(11) UNSIGNED NOT NULL,
            `id_category` INT(11) UNSIGNED NOT NULL,
            PRIMARY KEY (`id_message`, `id_category`)
        ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8;
    ');
    
    $return &= Db::getInstance()->execute('
        CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'category_header_message_image` (
            `id_image` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
            `id_message` INT(11) UNSIGNED NOT NULL,
            `name` VARCHAR(255) NOT NULL,
            PRIMARY KEY (`id_image`)
        ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8;
    ');

    return $return;
    }

    /**
     * Désinstallation de la base de données
     */
    private function uninstallDb()
    {
        $return = true;
        $return &= Db::getInstance()->execute('DROP TABLE IF EXISTS `' . _DB_PREFIX_ . 'category_header_message`');
        $return &= Db::getInstance()->execute('DROP TABLE IF EXISTS `' . _DB_PREFIX_ . 'category_header_message_lang`');
        $return &= Db::getInstance()->execute('DROP TABLE IF EXISTS `' . _DB_PREFIX_ . 'category_header_message_category`');
        
        // Supprimer les entrées dans la table image standard si elle existe
        $image_table_exists = Db::getInstance()->executeS('SHOW TABLES LIKE "' . _DB_PREFIX_ . 'image"');
        if (!empty($image_table_exists)) {
            $entity_field_exists = Db::getInstance()->executeS('SHOW COLUMNS FROM `' . _DB_PREFIX_ . 'image` LIKE "entity"');
            if (!empty($entity_field_exists)) {
                $return &= Db::getInstance()->execute('
                    DELETE FROM `' . _DB_PREFIX_ . 'image` 
                    WHERE `entity` = "category_header_message"
                ');
            }
        } else {
            // Supprimer la table image personnalisée si elle existe
            $return &= Db::getInstance()->execute('DROP TABLE IF EXISTS `' . _DB_PREFIX_ . 'category_header_message_image`');
        }
        
        return $return;
    }

    /**
     * Installation de l'onglet d'administration
     */
    private function installTab()
    {
        $tab = new Tab();
        $tab->active = 1;
        $tab->class_name = 'AdminCategoryHeaderMessages';
        $tab->name = array();
        foreach (Language::getLanguages(true) as $lang) {
            $tab->name[$lang['id_lang']] = 'Messages commerciaux';
        }
        $tab->id_parent = (int) Tab::getIdFromClassName('AdminCatalog');
        $tab->module = $this->name;
        return $tab->add();
    }

    /**
     * Désinstallation de l'onglet d'administration
     */
    private function uninstallTab()
    {
        $id_tab = (int) Tab::getIdFromClassName('AdminCategoryHeaderMessages');
        if ($id_tab) {
            $tab = new Tab($id_tab);
            return $tab->delete();
        }
        return true;
    }

    /**
     * Hook pour ajouter des scripts/styles dans l'administration
     */
    public function hookActionAdminControllerSetMedia()
    {
        $controller = Context::getContext()->controller;
        if ($controller->controller_name == 'AdminCategoryHeaderMessages') {
            $this->context->controller->addJS($this->_path . 'views/js/admin.js');
            $this->context->controller->addCSS($this->_path . 'views/css/admin.css');
            Media::addJsDef([
                'categoryHeaderMessages' => [
                    'deleteConfirmation' => $this->l('Êtes-vous sûr de vouloir supprimer ce message?')
                ]
            ]);
        }
    }

    /**
     * Hook pour afficher le message en entête de catégorie
     */
    public function hookDisplayHeaderCategory($params)
    {
        // Si nous ne sommes pas sur une page catégorie, sortir
    if (!isset($params['category']) || !$params['category']) {
        return '';
    }
    $this->context->controller->addCSS($this->_path . 'views/css/front.css');

    $id_category = (int) $params['category']['id'];
    $id_lang = (int) Context::getContext()->language->id;

    // Récupérer les messages actifs pour cette catégorie
    $messages = $this->getActiveMessagesForCategory($id_category, $id_lang);
    
    // Vérification explicite que $messages est un tableau
    if (!is_array($messages) || empty($messages)) {
        $req_coeur = 'SELECT * FROM ps_category_product cp LEFT JOIN awpf a ON cp.id_product = a.id_product LEFT JOIN ps_product_attribute pa ON cp.id_product = pa.id_product LEFT JOIN ps_product_lang pl ON cp.id_product = pl.id_product WHERE pl.id_lang = 1 AND cp.id_category = "'.$id_category.'" AND pa.default_on = 1 AND a.coeur = 1 ORDER BY RAND() LIMIT 0,1;';
        $rangee_coeur = Db::getInstance()->executeS($req_coeur);
        if ( count($rangee_coeur) > 0 )
        {
            $messages[0]['id_message'] = 0;
            $messages[0]['active'] = 1;
            $messages[0]['date_start'] = "2000-01-01 00:00:00";
            $messages[0]['date_end'] = "2099-12-31 23:59:59";
            $messages[0]['position'] = 1;
            $messages[0]['type'] = "produit_phare";
            $messages[0]['id_product'] = $rangee_coeur[0]['id_product'];
            $messages[0]['id_product_attribute'] = $rangee_coeur[0]['id_product_attribute'];
            $messages[0]['title'] = $rangee_coeur[0]['name'];
            $messages[0]['content'] = $rangee_coeur[0]['description_short'];
            $messages[0]['cta_text'] = "add_cart";
            $messages[0]['cta_link'] = "add_cart";

            // Ajout des informations produit si un produit est associé
            if (!empty($messages[0]['id_product'])) {
                $product = new Product((int)$messages[0]['id_product'], false, $id_lang);
                
                // Récupérer l'image du produit
                $images = $product->getImages($id_lang);
                $img_prod = $this->context->link->getImageLink(
                    $product->link_rewrite, 
                    $images[0]['id_image'], 
                    'large_default'
                );

                $messages[0]['image'] = $img_prod;
                $messages[0]['image_url'] = $img_prod;

                if (Validate::isLoadedObject($product)) {
                    $messages[0]['product'] = [
                        'id' => $product->id,
                        'name' => $product->name,
                        'link' => $this->context->link->getProductLink($product),
                        'price' => Tools::displayPrice(
                            Product::getPriceStatic(
                                (int)$messages[0]['id_product'], 
                                true, 
                                (int)$messages[0]['id_product_attribute']
                            )
                        ),
                        'price_without_reduction' => Tools::displayPrice(
                            Product::getPriceStatic(
                                (int)$messages[0]['id_product'], 
                                true, 
                                (int)$messages[0]['id_product_attribute'], 
                                6, 
                                null, 
                                false, 
                                false
                            )
                        ),
                        'has_discount' => $product->specificPrice
                    ];
                    
                    // Récupérer l'image du produit
                    if (!empty($images)) {
                        $messages[0]['product']['image_url'] = $img_prod;
                    }
                }
            }
        }
        else 
        {
            return '';
        }
    }
    else 
    {
        // Ajouter les URLs pour les images et les infos produits
        foreach ($messages as &$message) {
            if (!empty($message['image'])) {
                $message['image_url'] = $this->context->link->getMediaLink(_PS_IMG_.'chm/'.$message['image']);
            }
            
            // Ajout des informations produit si un produit est associé
            if (!empty($message['id_product'])) {
                $product = new Product((int)$message['id_product'], false, $id_lang);
                if (Validate::isLoadedObject($product)) {
                    $message['product'] = [
                        'id' => $product->id,
                        'name' => $product->name,
                        'link' => $this->context->link->getProductLink($product),
                        'price' => Tools::displayPrice(
                            Product::getPriceStatic(
                                (int)$message['id_product'], 
                                true, 
                                (int)$message['id_product_attribute']
                            )
                        ),
                        'price_without_reduction' => Tools::displayPrice(
                            Product::getPriceStatic(
                                (int)$message['id_product'], 
                                true, 
                                (int)$message['id_product_attribute'], 
                                6, 
                                null, 
                                false, 
                                false
                            )
                        ),
                        'has_discount' => $product->specificPrice
                    ];
                    
                    // Récupérer l'image du produit
                    $images = $product->getImages($id_lang);
                    if (!empty($images)) {
                        $message['product']['image_url'] = $this->context->link->getImageLink(
                            $product->link_rewrite, 
                            $images[0]['id_image'], 
                            'home_default'
                        );
                    }
                }
            }
        }
    }

    $this->context->smarty->assign([
        'category_header_messages' => $messages,
        'module_dir' => $this->_path
    ]);

    return $this->display(__FILE__, 'views/templates/hook/header_category.tpl');
    }

    /**
     * Récupère les messages actifs pour une catégorie donnée
     */
    private function getActiveMessagesForCategory($id_category, $id_lang)
    {
        try {
            $now = date('Y-m-d H:i:s');
            
            // Utiliser la table image personnalisée
            $sql = 'SELECT m.`id_message`, m.`active`, m.`date_start`, m.`date_end`, m.`position`, 
                        m.`type`, m.`id_product`, m.`id_product_attribute`, /* Nouveaux champs */
                        ml.`title`, ml.`content`, ml.`cta_text`, ml.`cta_link`, 
                        i.`name` as image
                    FROM `' . _DB_PREFIX_ . 'category_header_message` m
                    LEFT JOIN `' . _DB_PREFIX_ . 'category_header_message_lang` ml 
                        ON (m.`id_message` = ml.`id_message` AND ml.`id_lang` = ' . (int) $id_lang . ')
                    LEFT JOIN `' . _DB_PREFIX_ . 'category_header_message_category` mc 
                        ON (m.`id_message` = mc.`id_message`)
                    LEFT JOIN `' . _DB_PREFIX_ . 'category_header_message_image` i 
                        ON (m.`id_message` = i.`id_message`)
                    WHERE m.`active` = 1
                    AND m.`date_start` <= "' . pSQL($now) . '"
                    AND m.`date_end` >= "' . pSQL($now) . '"
                    AND mc.`id_category` = ' . (int) $id_category . '
                    ORDER BY m.`position` ASC';
            
            $result = Db::getInstance()->executeS($sql);
            
            if ($result === false) {
                PrestaShopLogger::addLog(
                    'CategoryHeaderMessages::getActiveMessagesForCategory - Error SQL: ' . Db::getInstance()->getMsgError(),
                    3
                );
                return [];
            }
            
            return $result;
        } catch (Exception $e) {
            PrestaShopLogger::addLog(
                'CategoryHeaderMessages::getActiveMessagesForCategory - Exception: ' . $e->getMessage(),
                3
            );
            return [];
        }
    }

    /**
     * Page de configuration du module
     */
    public function getContent()
    {
        Tools::redirectAdmin($this->context->link->getAdminLink('AdminCategoryHeaderMessages'));
    }
}