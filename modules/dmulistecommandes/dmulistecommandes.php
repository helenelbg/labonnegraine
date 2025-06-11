<?php
/**
 * NOTICE OF LICENSE.
 *
 * This source file is subject to a commercial license from SARL DREAM ME UP
 * Use, copy, modification or distribution of this source file without written
 * license agreement from the SARL DREAM ME UP is strictly forbidden.
 *
 *   .--.
 *   |   |.--..-. .--, .--.--.   .--.--. .-.   .  . .,-.
 *   |   ;|  (.-'(   | |  |  |   |  |  |(.-'   |  | |   )
 *   '--' '   `--'`-'`-'  '  `-  '  '  `-`--'  `--`-|`-'
 *        w w w . d r e a m - m e - u p . f r       '
 *
 *  @author    Dream me up <prestashop@dream-me-up.fr>
 *  @copyright 2007 - 2023 Dream me up
 *  @license   All Rights Reserved
 */
if (!defined('_PS_VERSION_')) {
    exit;
}

class DmuListeCommandes extends Module
{
    protected $tab_parent;
    protected $tab_controller;
    protected $tab_name;
    protected $module_tabs_config;
    protected $fields_form;

    public function __construct()
    {
        $this->name = 'dmulistecommandes';
        $this->tab = 'administration';
        $this->version = '4.0.3';
        $this->author = 'Dream me up';
        $this->module_key = 'de7c33c3571154b647570d9f184f67fe';
        $this->need_instance = 0;
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('DMU Improved Order List');
        $this->description = $this->l('This module installs a new order list with more opportunities and readability');

        $this->ps_versions_compliancy = ['min' => '1.7', 'max' => _PS_VERSION_];
        $this->confirmUninstall = $this->l('Are you sure you want to uninstall?');

        // Onglet d'Administration du module
        $this->tab_parent = [
            'default' => 'AdminOrders',
            '1.7' => 'AdminParentOrders',
        ];
        $this->tab_controller = 'AdminDmuListeCommandes';
        $this->tab_name = ['en' => 'Improved Order', 'fr' => 'Commandes Améliorées'];

        // Onglets à afficher dans la configuration du module
        $this->module_tabs_config = [
            'Views' => ['name' => $this->l('Views configuration'),
                                'content' => 'getViews',
                                'is_helper' => true, ],
            'Status' => ['name' => $this->l('Status colors'),
                                'content' => 'getStatusColorsForm',
                                'is_helper' => true, ],
            'Carriers' => ['name' => $this->l('Carriers colors'),
                                'content' => 'getCarriersColorsForm',
                                'is_helper' => true, ],
            'Options' => ['name' => $this->l('Options'),
                                'content' => 'getOptionsForm',
                                'is_helper' => true, ],
            'Columns' => ['name' => $this->l('Columns'),
                                'content' => 'getColumnsForm',
                                'is_helper' => true, ], ];

        // Mise à jour si version antérieur installé
        if (Module::isInstalled($this->name)) {
            $this->checkUpdates();
        }
    }

    public function install()
    {
        // Installation de l'onglet d'Administration du module
        reset($this->tab_parent);
        $tab_parent = current($this->tab_parent);
        foreach ($this->tab_parent as $version => $parent) {
            if ('default' != $version && version_compare(_PS_VERSION_, $version, '>=')) {
                $tab_parent = $parent;
            }
        }
        if (!$this->installModuleTab($this->tab_controller, $this->tab_name, $tab_parent, 1)) {
            return false;
        }

        // Création des tables en Base de données
        if (!$this->installDataBase()) {
            return false;
        }

        // Paramètres par défaut
        Configuration::updateGlobalValue('DMU_CARRIERS_COLORS', json_encode([]));
        Configuration::updateGlobalValue('DMU_SHOW_KPI', false);
        Configuration::updateGlobalValue('DMU_SHOW_VIEWS', true);
        Configuration::updateGlobalValue('DMU_STATUS_ON_LINE', true);
        Configuration::updateGlobalValue('DMU_SHOW_BUTTONS', true);
        Configuration::updateGlobalValue('DMU_SAVE_CHECKED_ORDERS', true);
        Configuration::updateGlobalValue('DMU_SHOW_COL_CART', false);
        Configuration::updateGlobalValue('DMU_SHOW_COL_INVOICE', false);
        Configuration::updateGlobalValue('DMU_SHOW_COL_NEW', true);
        Configuration::updateGlobalValue('DMU_SHOW_COL_GIFT', true);
        Configuration::updateGlobalValue('DMU_SHOW_COL_PAYMENT', true);
        Configuration::updateGlobalValue('DMU_SHOW_COL_CARRIER', true);
        Configuration::updateGlobalValue('DMU_SHOW_COL_COUNTRY', false);
        Configuration::updateGlobalValue('DMU_SHOW_COL_STATE', false);
        Configuration::updateGlobalValue('DMU_SHOW_COL_POSTCODE', false);

        // Version d'installation pour mises à jour éventuelles
        Configuration::updateGlobalValue('PS_' . Tools::strtoupper($this->name) . '_VERSION', $this->version);

        return parent::install();
    }

    public function uninstall()
    {
        // Création des tables en Base de données
        if (!$this->uninstallDataBase()) {
            return false;
        }

        // Désinstallation de l'onglet d'Administration du module
        $this->uninstallModuleTab($this->tab_controller);

        return parent::uninstall();
    }

    private function installDataBase()
    {
        $sql = [];
        $sql[] = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'dmu_vues_commandes` (
                `id_vue` INT( 4 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,
                `name` VARCHAR( 255 ) NOT NULL ,
                `statuts` VARCHAR( 255 ) NOT NULL ,
                `default` INT( 1 ) NOT NULL DEFAULT 0,
                `position` INT( 11 ) NOT NULL DEFAULT 0
                ) ENGINE = ' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8;';

        foreach ($sql as $query) {
            if (!Db::getInstance()->execute($query)) {
                return false;
            }
        }

        return true;
    }

    private function uninstallDataBase()
    {
        $sql = [];
        $sql[] = 'DROP TABLE IF EXISTS `' . _DB_PREFIX_ . 'dmu_vues_commandes`';

        foreach ($sql as $query) {
            if (!Db::getInstance()->execute($query)) {
                return false;
            }
        }

        return true;
    }

    private function installModuleTab($tab_class_name, $tab_name, $parent_class_name = 'AdminAdmin', $position = false)
    {
        // Vérification que l'onglet n'existe pas déjà !
        if (!Tab::getIdFromClassName($tab_class_name)) {
            // création du "tableau de langue" pour le Nom de l'onglet
            $tab_names = [];
            if (is_array($tab_name)) {
                reset($tab_name);
                $default_name = current($tab_name);
                foreach (Language::getLanguages(false) as $lang) {
                    $tab_names[$lang['id_lang']] = $default_name;
                    if (isset($tab_name[$lang['iso_code']])) {
                        $tab_names[$lang['id_lang']] = $tab_name[$lang['iso_code']];
                    }
                }
            } else {
                foreach (Language::getLanguages(false) as $lang) {
                    $tab_names[$lang['id_lang']] = $tab_name;
                }
            }

            // Récupération de l'ID de l'onglet parent
            $sql = 'SELECT id_tab FROM ' . _DB_PREFIX_ . 'tab WHERE class_name = \'' . pSQL($parent_class_name) . '\'';
            $tab_id_parent = (int) Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($sql);
            if (!$tab_id_parent) {
                return false;
            }

            // Création du nouvel onglet !
            $tab = new Tab();
            $tab->name = $tab_names;
            $tab->class_name = $tab_class_name;
            $tab->module = $this->name;
            $tab->id_parent = $tab_id_parent;
            if (!$tab->save()) {
                return false;
            }

            if ($position) {
                // Placement de l'onglet en première position (TODO : pouvoir choisir la position)
                $sql = 'SELECT `id_tab`,`position`
                        FROM `' . _DB_PREFIX_ . 'tab`
                        WHERE `id_parent` = ' . (int) $tab->id_parent . '
                        AND `id_tab` != ' . (int) $tab->id . '
                        ORDER BY `position`';
                $position = 2;
                foreach (Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql) as $sel_tab) {
                    Db::getInstance()->execute('UPDATE `' . _DB_PREFIX_ . 'tab`
                                                SET `position` = ' . ($position++) . '
                                                WHERE `id_tab` = ' . (int) $sel_tab['id_tab']);
                }
                Db::getInstance()->execute('UPDATE `' . _DB_PREFIX_ . 'tab`
                                            SET `position` = 1
                                            WHERE `id_tab` = ' . (int) $tab->id);
            }
        }

        return true;
    }

    private function uninstallModuleTab($tab_class_name)
    {
        $id_tab = Tab::getIdFromClassName($tab_class_name);
        if (0 != $id_tab) {
            // Récupération de l'ID de l'onglet parent et Suppression de l'onglet
            $tab = new Tab($id_tab);
            $tab_id_parent = $tab->id_parent;
            $tab->delete();

            // Rangement des onglets restants
            $sql = 'SELECT `id_tab`,`position`
                    FROM `' . _DB_PREFIX_ . 'tab`
                    WHERE `id_parent` = ' . (int) $tab_id_parent . '
                    ORDER BY `position`';
            $position = 1;
            foreach (Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql) as $sel_tab) {
                Db::getInstance()->execute('UPDATE `' . _DB_PREFIX_ . 'tab`
                                            SET `position` = ' . ($position++) . '
                                            WHERE `id_tab` = ' . (int) $sel_tab['id_tab']);
            }

            return true;
        }

        return false;
    }

    private function checkUpdates()
    {
        if (!Module::isInstalled($this->name)) {
            return;
        }
        $last_version = Configuration::getGlobalValue('PS_' . Tools::strtoupper($this->name) . '_VERSION');

        // version < 3.1.0
        if (version_compare($last_version, '3.1.0', '<')) {
            Configuration::updateGlobalValue('DMU_SHOW_COL_PAYMENT', true);
            Configuration::updateGlobalValue('DMU_SHOW_COL_CARRIER', true);
        }
        // version < 3.0.0
        if (version_compare($last_version, '3.0.0', '<')) {
            // Conversion de l'Admin Tab vers le ModuleAdminController
            @unlink(dirname(__FILE__) . '/AdminDmuListeCommandes.php');
            @unlink(dirname(__FILE__) . '/admindmulistecommandes.php');
            $sql = 'UPDATE `' . _DB_PREFIX_ . 'tab`
                    SET class_name = \'AdminDmuListeCommandes\'
                    WHERE class_name LIKE \'admindmulistecommandes\'';
            Db::getInstance()->execute($sql);
            // Changement de gestion des couleurs Transporteur
            if (Configuration::hasKey('dmu_carrier_couleurs')) {
                $dmu_carriers_colors = [];
                $carriers_colors = json_decode(Configuration::get('dmu_carrier_couleurs', null, 0, 0), true);
                foreach (Carrier::getCarriers($this->context->language->id, false) as $carrier) {
                    if (isset($carriers_colors[$carrier['name']])) {
                        $dmu_carriers_colors[$carrier['id_reference']] = $carriers_colors[$carrier['name']];
                    }
                }
                Configuration::updateGlobalValue('DMU_CARRIERS_COLORS', json_encode($dmu_carriers_colors));
            }
            // Ajout de l'ordre des Vues (position)
            $sql = 'ALTER TABLE `' . _DB_PREFIX_ . 'dmu_vues_commandes`
                    ADD COLUMN `position` INT(11) NOT NULL DEFAULT 0';
            Db::getInstance()->execute($sql);
            // Ajout des options
            Configuration::updateGlobalValue('DMU_SHOW_KPI', false);
            Configuration::updateGlobalValue('DMU_SHOW_VIEWS', true);
            Configuration::updateGlobalValue('DMU_STATUS_ON_LINE', true);
            Configuration::updateGlobalValue('DMU_SHOW_BUTTONS', true);
            Configuration::updateGlobalValue('DMU_SAVE_CHECKED_ORDERS', true);
        }

        Configuration::updateGlobalValue('PS_' . Tools::strtoupper($this->name) . '_VERSION', $this->version);
    }

    // Gestion des processus en post
    public function postProcess()
    {
        $this->context->controller->addJquery();
        $this->context->controller->addjQueryPlugin('tablednd');
        $this->context->controller->addCSS($this->_path . 'views/css/backoffice.css');
        $this->context->controller->addJS($this->_path . 'views/js/backoffice.js');

        require_once 'classes/VuesCommandes.php';

        if (Tools::isSubmit('submitViewsForm')) {
            $view = new VuesCommandes((int) Tools::getValue('id_vue'));
            $view->name = Tools::getValue('name');
            $view->statuts = [];

            $order_state_ids = [];
            foreach (array_keys(Tools::getAllValues()) as $key) {
                // Vérifie si $key commence par "order_states_"
                if (0 === strpos($key, 'order_states_')) {
                    // Retrouve l'id de $key en utilisant une regex
                    if (preg_match('/^order_states_([0-9]+)$/', $key, $matches)) {
                        // Ajoute l'id à l'array des id sélectionnés
                        $order_state_ids[] = $matches[1];
                    }
                }
            }

            // if (!empty(Tools::getValue('order_states'))) {
            //     foreach (array_keys(Tools::getValue('order_states')) as $id_order_status) {
            //         $view->statuts[] = $id_order_status;
            //     }
            // }
            if (!empty($order_state_ids)) {
                foreach ($order_state_ids as $id) {
                    $view->statuts[] = $id;
                }
            }
            $view->save();
            Tools::redirectAdmin(
                $this->context->link->getAdminLink('AdminModules', false)
                . '&configure=' . $this->name . '&module_tab=Views&conf=4&token=' . Tools::getAdminTokenLite('AdminModules')
            );
        }
        if (Tools::isSubmit('submitStatusColorsForm')) {
            foreach (Tools::getValue('order_state') as $id_order_state => $color) {
                $sql = 'UPDATE `' . _DB_PREFIX_ . 'order_state` SET color = \'' . $color . '\'
                        WHERE id_order_state = ' . (int) $id_order_state;
                Db::getInstance()->execute($sql);
            }
            Tools::redirectAdmin(
                $this->context->link->getAdminLink('AdminModules', false)
                . '&configure=' . $this->name . '&module_tab=Status&conf=6&token=' . Tools::getAdminTokenLite('AdminModules')
            );
        }
        if (Tools::isSubmit('submitCarriersColorsForm')) {
            $dmu_carriers_colors = [];
            foreach (Tools::getValue('carrier') as $id_reference => $color) {
                $dmu_carriers_colors[$id_reference] = $color;
            }
            Configuration::updateGlobalValue('DMU_CARRIERS_COLORS', json_encode($dmu_carriers_colors));
            Tools::redirectAdmin(
                $this->context->link->getAdminLink('AdminModules', false)
                . '&configure=' . $this->name . '&module_tab=Carriers&conf=6&token='
                . Tools::getAdminTokenLite('AdminModules')
            );
        }
        if (Tools::isSubmit('submitOptionsForm')) {
            $dmu_status_on_line = Tools::getValue('dmu_status_on_line') ? true : false;
            Configuration::updateGlobalValue('DMU_STATUS_ON_LINE', $dmu_status_on_line);
            Configuration::updateGlobalValue('DMU_SHOW_VIEWS', Tools::getValue('dmu_show_views') ? true : false);
            Configuration::updateGlobalValue('DMU_SHOW_KPI', Tools::getValue('dmu_show_kpi') ? true : false);
            Configuration::updateGlobalValue('DMU_SHOW_BUTTONS', Tools::getValue('dmu_show_buttons') ? true : false);
            Configuration::updateGlobalValue(
                'DMU_SAVE_CHECKED_ORDERS',
                Tools::getValue('dmu_save_checked_orders') ? true : false
            );
            Tools::redirectAdmin(
                $this->context->link->getAdminLink('AdminModules', false)
                . '&configure=' . $this->name . '&module_tab=Options&conf=6&token='
                . Tools::getAdminTokenLite('AdminModules')
            );
        }
        if (Tools::isSubmit('submitColumnsForm')) {
            Configuration::updateGlobalValue('DMU_SHOW_COL_CART', (bool) Tools::getValue('dmu_show_col_cart'));
            Configuration::updateGlobalValue('DMU_SHOW_COL_INVOICE', (bool) Tools::getValue('dmu_show_col_invoice'));
            Configuration::updateGlobalValue('DMU_SHOW_COL_NEW', (bool) Tools::getValue('dmu_show_col_new'));
            Configuration::updateGlobalValue('DMU_SHOW_COL_GIFT', (bool) Tools::getValue('dmu_show_col_gift'));
            Configuration::updateGlobalValue('DMU_SHOW_COL_PAYMENT', (bool) Tools::getValue('dmu_show_col_payment'));
            Configuration::updateGlobalValue('DMU_SHOW_COL_CARRIER', (bool) Tools::getValue('dmu_show_col_carrier'));
            Configuration::updateGlobalValue('DMU_SHOW_COL_COUNTRY', (bool) Tools::getValue('dmu_show_col_country'));
            Configuration::updateGlobalValue('DMU_SHOW_COL_STATE', (bool) Tools::getValue('dmu_show_col_state'));
            Configuration::updateGlobalValue('DMU_SHOW_COL_POSTCODE', (bool) Tools::getValue('dmu_show_col_postcode'));
            Tools::redirectAdmin(
                $this->context->link->getAdminLink('AdminModules', false)
                . '&configure=' . $this->name . '&module_tab=Columns&conf=6&token='
                . Tools::getAdminTokenLite('AdminModules')
            );
        }
    }

    // Configuration du module
    public function getContent()
    {
        $this->postProcess();

        // Récupération du contenu des onglets
        $content = [];
        foreach ($this->module_tabs_config as $tab => $vals) {
            $content[$tab] = $this->{$vals['content']}();
        }

        // Creation du chemin via le Menu
        $how_to_path = '';
        reset($this->tab_parent);
        $tab_parent = current($this->tab_parent);
        foreach ($this->tab_parent as $version => $parent) {
            if ('default' != $version && version_compare(_PS_VERSION_, $version, '>=')) {
                $tab_parent = $parent;
            }
        }
        foreach ([$tab_parent, $this->tab_controller] as $menu) {
            $sql = 'SELECT name FROM `' . _DB_PREFIX_ . 'tab` t
                    INNER JOIN `' . _DB_PREFIX_ . 'tab_lang` tl
                    ON tl.id_tab = t.id_tab AND tl.id_lang = ' . (int) $this->context->language->id . '
                    WHERE class_name = \'' . pSQL($menu) . '\'';
            $how_to_path .= (empty($how_to_path) ? '' : ' &gt; ') .
                Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($sql);
        }

        $file_documentation = 'readme_' . $this->context->language->iso_code . '.pdf';
        if (!file_exists(dirname(__FILE__) . '/' . $file_documentation)) {
            $file_documentation = 'readme_en.pdf';
        }

        $this->context->smarty->assign([
            'version_prestashop' => _PS_VERSION_,
            'version_module' => $this->version,
            'module_name' => $this->displayName,
            'module_description' => $this->description,
            'module_path' => '../modules/' . $this->name,
            'module_how_to' => $this->l('To use the module, you must use the menu to access it')
                . ' : <b>' . $how_to_path . '</b>',
            'documentation_pdf' => $file_documentation,
            'config_tabs' => $this->module_tabs_config,
            'txt_follow' => $this->l('Follow us'),
            'txt_follow_our' => $this->l('Follow our'),
            'txt_on' => $this->l('on'),
            'txt_and' => $this->l('and'),
            'txt_know_actu' => $this->l('to know all the news around our Addons'),
            'txt_to_have_details' => $this->l('to have all details on our new Addons versions and for every new launch of Addon'),
            'content' => $content,
        ]);

        $tpl_name = 'configure.tpl';
        if (version_compare(_PS_VERSION_, '1.6', '<')) {
            $tpl_name = 'configure-1.5.tpl';
        }

        return $this->context->smarty->fetch(dirname(__FILE__) . '/views/templates/admin/' . $tpl_name);
    }

    public function getViews()
    {
        $warnings = [];

        if (Tools::getIsset('adddmu_vues_commandes')) {
            $view = new VuesCommandes();
            $view->name = $this->l('New view') . date(' - jS F - H:i');
            $view->statuts = [];
            $view->position = $view->getNextPosition();
            if (!$view->position) {
                $view->default = 1;
            }
            if ($view->add()) {
                return $this->getViewsForm((int) $view->id);
            } else {
                $warnings[] = $this->displayError($this->l('Can not create a new View !'));
            }
        } elseif (Tools::getIsset('updatedmu_vues_commandes') && Tools::getIsset('id_vue')) {
            return $this->getViewsForm((int) Tools::getValue('id_vue'));
        } elseif (Tools::getIsset('deletedmu_vues_commandes') && Tools::getIsset('id_vue')) {
            $view = new VuesCommandes((int) Tools::getValue('id_vue'));
            if ($view->delete()) {
                $warnings[] = $this->displayConfirmation($this->l('The view was successfully deleted !'));
            } else {
                $warnings[] = $this->displayError($this->l('Can not delete the View !'));
            }
        } elseif (Tools::getIsset('defaultdmu_vues_commandes') && Tools::getIsset('id_vue')) {
            VuesCommandes::setDefaultView((int) Tools::getValue('id_vue'));
            $warnings[] = $this->displayConfirmation($this->l('Default view has been successfully changed !'));
        }

        return implode(' ', $warnings) . $this->getViewsList();
    }

    public function getViewsList()
    {
        $views = VuesCommandes::getViews();

        $helper = new HelperList();

        $fields_list = [
            'id_vue' => [
                'title' => $this->l('ID'),
                'type' => 'text',
                'class' => 'id_vue',
                'orderby' => false,
                'search' => false,
            ],
            'name' => [
                'title' => $this->l('Name'),
                'type' => 'text',
                'orderby' => false,
                'search' => false,
            ],
            'statuts' => [
                'title' => $this->l('Status'),
                'type' => 'text',
                'callback' => 'callbackStatus',
                'callback_object' => 'DmuListeCommandes',
                'orderby' => false,
                'search' => false,
            ],
            'default' => [
                'title' => $this->l('Default'),
                'type' => 'bool',
                'active' => 'default',
                'activeVisu' => 'default',
                'orderby' => false,
                'search' => false,
            ],
            'position' => [
                'title' => $this->l('Position'),
                'position' => 'position',
                'class' => 'dragHandle',
                'orderby' => false,
                'search' => false,
            ],
        ];

        $helper->shopLinkType = '';
        $helper->simple_header = false;
        $helper->identifier = 'id_vue';
        $helper->table = 'dmu_vues_commandes';
        $helper->actions = ['edit', 'delete'];
        $helper->show_toolbar = true;
        $helper->toolbar_btn['new'] = [
            'href' => AdminController::$currentIndex . '&configure=' . $this->name
                . '&module_tab=Views&adddmu_vues_commandes&token=' . Tools::getAdminTokenLite('AdminModules'),
            'desc' => $this->l('Add new'),
        ];
        $helper->module = $this;
        $helper->title = $this->l('Views');
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->currentIndex = AdminController::$currentIndex . '&configure=' . $this->name . '&module_tab=Views';
        /*
        $helper->position_identifier = 'position';
        $helper->orderBy = 'position';
        $helper->orderWay = 'ASC';
        $this->orderBy = 'position';
        */

        return $helper->generateList($views, $fields_list);
    }

    public function getViewsForm($id_vue = null)
    {
        // If id_vue undefined, get list of vues
        if (!$id_vue) {
            return $this->getViewsList();
        }

        // Get Vue with this id
        $view = new VuesCommandes($id_vue);

        // Get order states
        $order_states = OrderState::getOrderStates($this->context->language->id);

        // Get list of order state IDs for the view
        $view_order_state_ids = explode(',', $view->statuts);

        // Init form
        $form = [
            'form' => [
                'legend' => [
                    'title' => $this->l('Modification of the View'),
                    'icon' => 'icon-eye-open',
                ],
                'input' => [
                    [
                        'type' => 'hidden',
                        'name' => 'id_vue',
                        'value' => $view->id,
                    ],
                    [
                        'type' => 'text',
                        'label' => $this->l('Name'),
                        'name' => 'name',
                        'required' => true,
                        'value' => $view->name,
                    ],
                    [
                        'type' => 'checkbox',
                        'label' => $this->l('Status'),
                        'name' => 'order_states',
                        'values' => [
                            'query' => $order_states,
                            'id' => 'id_order_state',
                            'name' => 'name',
                        ],
                    ],
                ],
                'submit' => [
                    'title' => $this->l('Save'),
                    'class' => 'btn btn-default pull-right',
                ],
            ],
        ];

        $helper = new HelperForm();
        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $helper->module = $this;
        $helper->default_form_language = $this->context->language->id;
        $helper->allow_employee_form_lang = $this->context->language->id;
        $helper->identifier = $this->identifier;
        $helper->submit_action = 'submitViewsForm';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false)
            . '&configure=' . $this->name . '&module_tab=Views&token=' . Tools::getAdminTokenLite('AdminModules');
        $helper->token = Tools::getAdminTokenLite('AdminModules');

        $helper->tpl_vars = [
            'fields_value' => [
                'name' => $view->name,
                'id_vue' => $view->id,
            ],
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
        ];
        // Order states already saved in the DB are pre-checked
        foreach ($order_states as $feature) {
            if (in_array($feature['id_order_state'], $view_order_state_ids)) {
                $helper->tpl_vars['fields_value']['order_states_' . $feature['id_order_state']] = true;
            }
        }

        return $helper->generateForm([$form]);
    }

    public function getStatusColorsForm()
    {
        $order_states = OrderState::getOrderStates($this->context->language->id);

        // Création des champs du formulaire
        $inputs_arrays = [];
        $inputs_values = [];
        foreach ($order_states as $order_state) {
            $inputs_arrays[] = [
                'type' => 'color',
                'label' => $order_state['name'],
                'name' => 'order_state[' . $order_state['id_order_state'] . ']', ];
            $color = VuesCommandes::str2hexColor($order_state['color']);
            $inputs_values['order_state[' . $order_state['id_order_state'] . ']'] = $color;
        }

        // Création du formulaire
        $helper = new HelperForm();

        $this->fields_form[0]['form'] = [
            'legend' => [
                'title' => $this->l('Status'),
                'icon' => 'icon-list',
            ],
            'input' => $inputs_arrays,
            'submit' => [
                'title' => $this->l('Save'),
                'name' => 'submitStatusColorsForm',
                'class' => 'button btn btn-default pull-right',
            ],
        ];

        $helper->fields_value = $inputs_values;

        // Module, Title, toolbar, token and currentIndex
        $helper->module = $this;
        $helper->name_controller = $this->name;
        $helper->title = $this->displayName;
        $helper->show_toolbar = false;
        $helper->toolbar_scroll = false;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->currentIndex = AdminController::$currentIndex . '&configure=' . $this->name . '&module_tab=Status';
        $helper->submit_action = 'submitStatusColorsForm';

        return $helper->generateForm($this->fields_form);
    }

    public function getCarriersColorsForm()
    {
        $dmu_carriers_colors = json_decode(Configuration::getGlobalValue('DMU_CARRIERS_COLORS'), true);

        // Création des champs du formulaire
        $inputs_arrays = [];
        $inputs_values = [];
        $id_lang = (int) $this->context->language->id;
        $carriers = Carrier::getCarriers($id_lang, false, false, false, null, Carrier::ALL_CARRIERS);
        foreach ($carriers as $carrier) {
            $inputs_arrays[] = [
                'type' => 'color',
                'label' => $carrier['name'],
                'name' => 'carrier[' . $carrier['id_reference'] . ']', ];
            $color = '#ffffff';
            if (isset($dmu_carriers_colors[$carrier['id_reference']])) {
                $color = $dmu_carriers_colors[$carrier['id_reference']];
            }
            $inputs_values['carrier[' . $carrier['id_reference'] . ']'] = $color;
        }

        // Création du formulaire
        $helper = new HelperForm();

        $this->fields_form[0]['form'] = [
            'legend' => [
                'title' => $this->l('Carriers'),
                'icon' => 'icon-truck',
            ],
            'input' => $inputs_arrays,
            'submit' => [
                'title' => $this->l('Save'),
                'name' => 'submitCarriersColorsForm',
                'class' => 'button btn btn-default pull-right',
            ],
        ];

        $helper->fields_value = $inputs_values;

        // Module, Title, toolbar, token and currentIndex
        $helper->module = $this;
        $helper->name_controller = $this->name;
        $helper->title = $this->displayName;
        $helper->show_toolbar = false;
        $helper->toolbar_scroll = false;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->currentIndex = AdminController::$currentIndex . '&configure=' . $this->name . '&module_tab=Carriers';
        $helper->submit_action = 'submitCarriersColorsForm';

        return $helper->generateForm($this->fields_form);
    }

    public function getOptionsForm()
    {
        // Création du formulaire
        $helper = new HelperForm();

        $this->fields_form[0]['form'] = [
            'legend' => [
                'title' => $this->l('Options'),
                'icon' => 'icon-cog',
            ],
            'input' => [
                [
                    'type' => (version_compare(_PS_VERSION_, '1.6', '<') ? 'radio' : 'switch'),
                    'label' => $this->l('Key performance indicators'),
                    'name' => 'dmu_show_kpi',
                    'desc' => $this->l('Show KPI icons located above the orders list.'),
                    'values' => [
                        [
                            'label' => $this->l('Enabled'),
                            'id' => 'dmu_show_kpi_on',
                            'value' => 1,
                        ],
                        [
                            'label' => $this->l('Disabled'),
                            'id' => 'dmu_show_kpi_off',
                            'value' => 0,
                        ],
                    ],
                ],
                [
                    'type' => (version_compare(_PS_VERSION_, '1.6', '<') ? 'radio' : 'switch'),
                    'label' => $this->l('Views selection'),
                    'name' => 'dmu_show_views',
                    'desc' => $this->l('Show Views selection above the orders list.'),
                    'values' => [
                        [
                            'label' => $this->l('Enabled'),
                            'id' => 'dmu_show_views_on',
                            'value' => 1,
                        ],
                        [
                            'label' => $this->l('Disabled'),
                            'id' => 'dmu_show_views_off',
                            'value' => 0,
                        ],
                    ],
                ],
                [
                    'type' => (version_compare(_PS_VERSION_, '1.6', '<') ? 'radio' : 'switch'),
                    'label' => $this->l('Enlarge Status colors'),
                    'name' => 'dmu_status_on_line',
                    'desc' => $this->l('Color all the order line with its status color.'),
                    'values' => [
                        [
                            'label' => $this->l('Enabled'),
                            'id' => 'dmu_status_on_line_on',
                            'value' => 1,
                        ],
                        [
                            'label' => $this->l('Disabled'),
                            'id' => 'dmu_status_on_line_off',
                            'value' => 0,
                        ],
                    ],
                ],
                [
                    'type' => (version_compare(_PS_VERSION_, '1.6', '<') ? 'radio' : 'switch'),
                    'label' => $this->l('Action buttons'),
                    'name' => 'dmu_show_buttons',
                    'desc' => $this->l('Show action buttons. ( Search, Show order,... )'),
                    'values' => [
                        [
                            'label' => $this->l('Enabled'),
                            'id' => 'dmu_dmu_show_buttons_on',
                            'value' => 1,
                        ],
                        [
                            'label' => $this->l('Disabled'),
                            'id' => 'dmu_dmu_show_buttons_off',
                            'value' => 0,
                        ],
                    ],
                ],
                [
                    'type' => (version_compare(_PS_VERSION_, '1.6', '<') ? 'radio' : 'switch'),
                    'label' => $this->l('Save Checked Orders'),
                    'name' => 'dmu_save_checked_orders',
                    'desc' => $this->l('You can choose to save the selected checkbox for orders after changin order state.'),
                    'values' => [
                        [
                            'label' => $this->l('Enabled'),
                            'id' => 'dmu_dmu_save_checked_orders_on',
                            'value' => 1,
                        ],
                        [
                            'label' => $this->l('Disabled'),
                            'id' => 'dmu_save_checked_orders_off',
                            'value' => 0,
                        ],
                    ],
                ],
            ],
            'submit' => [
                'title' => $this->l('Save'),
                'name' => 'submitOptionsForm',
                'class' => 'button btn btn-default pull-right',
            ],
        ];

        $helper->fields_value = [
            'dmu_show_kpi' => Configuration::getGlobalValue('DMU_SHOW_KPI'),
            'dmu_show_views' => Configuration::getGlobalValue('DMU_SHOW_VIEWS'),
            'dmu_status_on_line' => Configuration::getGlobalValue('DMU_STATUS_ON_LINE'),
            'dmu_show_buttons' => Configuration::getGlobalValue('DMU_SHOW_BUTTONS'),
            'dmu_save_checked_orders' => Configuration::getGlobalValue('DMU_SAVE_CHECKED_ORDERS'),
        ];

        // Module, Title, toolbar, token and currentIndex
        $helper->module = $this;
        $helper->name_controller = $this->name;
        $helper->title = $this->displayName;
        $helper->show_toolbar = false;
        $helper->toolbar_scroll = false;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->currentIndex = AdminController::$currentIndex . '&configure=' . $this->name . '&module_tab=Options';
        $helper->submit_action = 'submitOptionsForm';

        return $helper->generateForm($this->fields_form);
    }

    public function getColumnsForm()
    {
        // Création du formulaire
        $helper = new HelperForm();

        $this->fields_form[0]['form'] = [
            'legend' => [
                'title' => $this->l('Columns'),
                'icon' => 'icon-columns',
            ],
            'input' => [
                [
                    'type' => (version_compare(_PS_VERSION_, '1.6', '<') ? 'radio' : 'switch'),
                    'label' => $this->l('Cart ID'),
                    'name' => 'dmu_show_col_cart',
                    'desc' => $this->l('Show the Cart ID column.'),
                    'values' => [
                        [
                            'label' => $this->l('Enabled'),
                            'id' => 'dmu_show_col_cart_on',
                            'value' => 1,
                        ],
                        [
                            'label' => $this->l('Disabled'),
                            'id' => 'dmu_show_col_cart_off',
                            'value' => 0,
                        ],
                    ],
                ],
                [
                    'type' => (version_compare(_PS_VERSION_, '1.6', '<') ? 'radio' : 'switch'),
                    'label' => $this->l('Invoice Number'),
                    'name' => 'dmu_show_col_invoice',
                    'desc' => $this->l('Show the Invoice Number column.'),
                    'values' => [
                        [
                            'label' => $this->l('Enabled'),
                            'id' => 'dmu_show_col_invoice_on',
                            'value' => 1,
                        ],
                        [
                            'label' => $this->l('Disabled'),
                            'id' => 'dmu_show_col_invoice_off',
                            'value' => 0,
                        ],
                    ],
                ],
                [
                    'type' => (version_compare(_PS_VERSION_, '1.6', '<') ? 'radio' : 'switch'),
                    'label' => $this->l('New customer'),
                    'name' => 'dmu_show_col_new',
                    'desc' => $this->l('Show the new customer column.'),
                    'values' => [
                        [
                            'label' => $this->l('Enabled'),
                            'id' => 'dmu_show_col_new_on',
                            'value' => 1,
                        ],
                        [
                            'label' => $this->l('Disabled'),
                            'id' => 'dmu_show_col_new_off',
                            'value' => 0,
                        ],
                    ],
                ],
                [
                    'type' => (version_compare(_PS_VERSION_, '1.6', '<') ? 'radio' : 'switch'),
                    'label' => $this->l('Gifts & Messages'),
                    'name' => 'dmu_show_col_gift',
                    'desc' => $this->l('Show the Gifts and messages column.'),
                    'values' => [
                        [
                            'label' => $this->l('Enabled'),
                            'id' => 'dmu_show_col_gift_on',
                            'value' => 1,
                        ],
                        [
                            'label' => $this->l('Disabled'),
                            'id' => 'dmu_show_col_gift_off',
                            'value' => 0,
                        ],
                    ],
                ],
                [
                    'type' => (version_compare(_PS_VERSION_, '1.6', '<') ? 'radio' : 'switch'),
                    'label' => $this->l('Payment'),
                    'name' => 'dmu_show_col_payment',
                    'desc' => $this->l('Show the Payment column.'),
                    'values' => [
                        [
                            'label' => $this->l('Enabled'),
                            'id' => 'dmu_show_col_payment_on',
                            'value' => 1,
                        ],
                        [
                            'label' => $this->l('Disabled'),
                            'id' => 'dmu_show_col_payment_off',
                            'value' => 0,
                        ],
                    ],
                ],
                [
                    'type' => (version_compare(_PS_VERSION_, '1.6', '<') ? 'radio' : 'switch'),
                    'label' => $this->l('Carrier'),
                    'name' => 'dmu_show_col_carrier',
                    'desc' => $this->l('Show the Carrier column.'),
                    'values' => [
                        [
                            'label' => $this->l('Enabled'),
                            'id' => 'dmu_show_col_carrier_on',
                            'value' => 1,
                        ],
                        [
                            'label' => $this->l('Disabled'),
                            'id' => 'dmu_show_col_carrier_off',
                            'value' => 0,
                        ],
                    ],
                ],
                [
                    'type' => (version_compare(_PS_VERSION_, '1.6', '<') ? 'radio' : 'switch'),
                    'label' => $this->l('Delivery country'),
                    'name' => 'dmu_show_col_country',
                    'desc' => $this->l('Show the delivery country column.'),
                    'values' => [
                        [
                            'label' => $this->l('Enabled'),
                            'id' => 'dmu_show_col_country_on',
                            'value' => 1,
                        ],
                        [
                            'label' => $this->l('Disabled'),
                            'id' => 'dmu_show_col_country_off',
                            'value' => 0,
                        ],
                    ],
                ],
                [
                    'type' => (version_compare(_PS_VERSION_, '1.6', '<') ? 'radio' : 'switch'),
                    'label' => $this->l('Delivery state'),
                    'name' => 'dmu_show_col_state',
                    'desc' => $this->l('Show the delivery state column.'),
                    'values' => [
                        [
                            'label' => $this->l('Enabled'),
                            'id' => 'dmu_show_col_state_on',
                            'value' => 1,
                        ],
                        [
                            'label' => $this->l('Disabled'),
                            'id' => 'dmu_show_col_state_off',
                            'value' => 0,
                        ],
                    ],
                ],
                [
                    'type' => (version_compare(_PS_VERSION_, '1.6', '<') ? 'radio' : 'switch'),
                    'label' => $this->l('Delivery zip code'),
                    'name' => 'dmu_show_col_postcode',
                    'desc' => $this->l('Show the delivery zip code column.'),
                    'values' => [
                        [
                            'label' => $this->l('Enabled'),
                            'id' => 'dmu_show_col_postcode_on',
                            'value' => 1,
                        ],
                        [
                            'label' => $this->l('Disabled'),
                            'id' => 'dmu_show_col_postcode_off',
                            'value' => 0,
                        ],
                    ],
                ],
            ],
            'submit' => [
                'title' => $this->l('Save'),
                'name' => 'submitColumnsForm',
                'class' => 'button btn btn-default pull-right',
            ],
        ];

        $helper->fields_value = [
            'dmu_show_col_cart' => Configuration::getGlobalValue('DMU_SHOW_COL_CART'),
            'dmu_show_col_invoice' => Configuration::getGlobalValue('DMU_SHOW_COL_INVOICE'),
            'dmu_show_col_new' => Configuration::getGlobalValue('DMU_SHOW_COL_NEW'),
            'dmu_show_col_gift' => Configuration::getGlobalValue('DMU_SHOW_COL_GIFT'),
            'dmu_show_col_payment' => Configuration::getGlobalValue('DMU_SHOW_COL_PAYMENT'),
            'dmu_show_col_carrier' => Configuration::getGlobalValue('DMU_SHOW_COL_CARRIER'),
            'dmu_show_col_country' => Configuration::getGlobalValue('DMU_SHOW_COL_COUNTRY'),
            'dmu_show_col_state' => Configuration::getGlobalValue('DMU_SHOW_COL_STATE'),
            'dmu_show_col_postcode' => Configuration::getGlobalValue('DMU_SHOW_COL_POSTCODE'),
        ];

        // Module, Title, toolbar, token and currentIndex
        $helper->module = $this;
        $helper->name_controller = $this->name;
        $helper->title = $this->displayName;
        $helper->show_toolbar = false;
        $helper->toolbar_scroll = false;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->currentIndex = AdminController::$currentIndex . '&configure=' . $this->name . '&module_tab=Columns';
        $helper->submit_action = 'submitColumnsForm';

        return $helper->generateForm($this->fields_form);
    }

    public static function callbackStatus($order_status_list)
    {
        $result = '';
        foreach (OrderState::getOrderStates(Context::getContext()->language->id) as $order_state) {
            if (in_array($order_state['id_order_state'], explode(',', $order_status_list))) {
                $background = $order_state['color'];
                $color = VuesCommandes::textColor($background);

                $result .= Context::getContext()->smarty->fetch(_PS_MODULE_DIR_ . '/dmulistecommandes/views/templates/admin/dmu_liste_commandes/status-colors.tpl', [
                    'background' => $background,
                    'color' => $color,
                    'order_state_name' => $order_state['name'],
                ]);
            }
        }

        return $result;
    }

    public function ajaxProcessMovePositions()
    {
        if (Tools::getIsset('id_vue_list')) {
            $positions = explode(',', Tools::getValue('id_vue_list'));

            $pos = 0; // on en profite pour faire un peu de rangement au cas où !
            foreach ($positions as $position) {
                $sql = 'UPDATE `' . _DB_PREFIX_ . 'dmu_vues_commandes` SET position = ' . (int) ($pos++) . '
                        WHERE id_vue = ' . (int) $position;
                Db::getInstance()->execute($sql);
            }
            exit(json_encode(['success' => true]));
        }
    }
}
