<?php
/**
 * Cart Reminder
 *
 * @author    EIRL Timactive De Véra
 * @copyright Copyright (c) TIMACTIVE 2015 -EIRL Timactive De Véra
 * @license   Commercial license
 *
 * @category pricing_promotion
 *
 * @version 1.1.0
 *************************************
 **         CART REMINDER            *
 **          V 1.0.0                 *
 *************************************
 *  _____ _            ___       _   _
 * |_   _(_)          / _ \     | | (_)
 *   | |  _ _ __ ___ / /_\ \ ___| |_ ___   _____
 *   | | | | '_ ` _ \|  _  |/ __| __| \ \ / / _ \
 *   | | | | | | | | | | | | (__| |_| |\ V /  __/
 *   \_/ |_|_| |_| |_\_| |_/\___|\__|_| \_/ \___|
 *
 * +
 * + Languages: EN, FR
 * + PS version: 1.5,1.6
 * + Cloud compatible & tested
 */
if (!defined('_PS_VERSION_')) {
    exit;
}

require_once dirname(__FILE__) . '/models/TACartReminderRule.php';
require_once dirname(__FILE__) . '/models/TACartReminderJournal.php';
require_once dirname(__FILE__) . '/models/TACartReminderMailTemplate.php';
require_once dirname(__FILE__) . '/models/TACartReminderMessage.php';
require_once dirname(__FILE__) . '/models/TACartReminderCustomerUnsubscribe.php';
require_once dirname(__FILE__) . '/models/TAStackedStat.php';
require_once dirname(__FILE__) . '/models/TAStatPieLine.php';
require_once dirname(__FILE__) . '/models/TAEgMail.php';
require_once dirname(__FILE__) . '/models/TAEgMailImage.php';
require_once dirname(__FILE__) . '/models/TAEgMailSuggestion.php';
require_once dirname(__FILE__) . '/models/TAEgMailVariable.php';
require_once dirname(__FILE__) . '/models/TACartReminderRuleMatchCache.php';
require_once dirname(__FILE__) . '/tools/TACartReminderStats.php';
require_once dirname(__FILE__) . '/tools/TACartReminderCleans.php';
require_once dirname(__FILE__) . '/tools/TACartReminderTools.php';
require_once dirname(__FILE__) . '/tools/TACRHtml2TextException.php';
require_once dirname(__FILE__) . '/tools/TAHelper.php';

if (file_exists(__DIR__ . '/vendor/autoload.php')) {
    require_once __DIR__ . '/vendor/autoload.php';
}

class TACartReminder extends Module
{
    const INSTALL_SQL_FILE = 'install.sql';
    /**
     * use for setting & concatenate the smarty fetch for all part
     *
     * @var string
     */
    private $html_return = '';
    /**
     * setting errors for all process
     *
     * @var array
     */
    private $post_errors = [];
    /**
     * tab selected by user
     * the configuration display depend at this param
     *
     * @var string
     */
    private $tab_configure = 'mail';
    /**
     * @var string
     */
    private $form_submit = ''; /* mail_template, rule */
    /**
     * contain successful message
     *
     * @var string
     */
    private $success_conf = '';
    /**
     * contain filters indicated in grid list
     *
     * @var array
     */
    private $orders_filters = [];
    /**
     * base url use for admin setting
     *
     * @var string
     */
    private $base_url;
    /**
     * If true the directory is accessible
     *
     * @var bool
     */
    private $accesslog = false;
    /**
     * The file name for log
     *
     * @var string
     */
    private $log_fic_name = '';
    /**
     * Assure process is not already performed
     *
     * @var bool
     */
    private $batch_running = false;
    /**
     * Use only for the developper on the module to audit performance(cache setting)
     *
     * @var bool
     */
    private $performance_audit = false;
    /**
     * If true the module is in debug
     *
     * @var bool
     */
    private $ta_ca_debug = false;

    /**
     * @see Module __construct()
     */
    public function __construct()
    {
        $this->name = 'tacartreminder';
        $this->tab = 'advertising_marketing';
        $this->version = '1.0.85';
        $this->author = 'Timactive';
        $this->module_key = '56a612d572b399ea26b14d06b310c02a';
        $this->need_instance = 0;
        $this->bootstrap = true;
        $this->log_directory = dirname(__FILE__) . '/logs/';
        $this->img_url = __PS_BASE_URI__ . basename(_PS_MODULE_DIR_) . '/' . $this->name . '/views/img/';
        $this->accesslog = false;
        $this->ta_ca_debug = (int) Configuration::get('TA_CARTR_DEBUG') ? true : false;
        parent::__construct();
        $this->ps_versions_compliancy = ['min' => '1.5.0.0', 'max' => _PS_VERSION_];
        $this->displayName = $this->l('Smart Cart Reminder');
        $this->description =
            $this->l('The advanced cart reminder solution. Create rules with criteria to remind the customer.');
        if ((Configuration::get('TA_CARTR_VERSION') != $this->version) && Configuration::get('TA_CARTR_VERSION')) {
            $this->runUpgrades(true);
        }
    }

    /**
     * Only Use by the developer module
     * Set performance audit
     * need Xhprof & Xdebug(Kcachegrind)
     *
     * @param bool|false $performance_audit
     */
    public function setPerformanceAudit($performance_audit = false)
    {
        if ($performance_audit && function_exists('xhprof_enable')) {
            $this->performance_audit = (bool) $performance_audit;
        } else {
            $this->performance_audit = false;
        }
    }

    /**
     * Module installation
     *
     * @param bool|true $delete_params use for reinitialisation data
     *
     * @return bool
     *
     * @see Module->install
     */
    public function install($delete_params = true)
    {
        $this->accesslog = $this->accessLogsDirectory();
        $this->log('************ INSTALLATION ****************');
        if (Shop::isFeatureActive()
            && !(Context::getContext()->shop->getContext() == Shop::CONTEXT_ALL)) {
            $this->_errors[] = $this->l('Please select the context All Shops before install the module');

            return false;
        }
        if ($delete_params) {
            if (!file_exists(dirname(__FILE__) . '/' . self::INSTALL_SQL_FILE)) {
                $this->log('Error file sql not found');

                return false;
            } else {
                if (!$sql = Tools::file_get_contents(dirname(__FILE__) . '/' . self::INSTALL_SQL_FILE)) {
                    $this->log('Cannot get file check right directory');

                    return false;
                }
            }
            $sql = str_replace(['PREFIX_', 'ENGINE_TYPE'], [_DB_PREFIX_, _MYSQL_ENGINE_], $sql);
            $sql = preg_split("/;\s*[\r\n]+/", trim($sql));
            foreach ($sql as $query) {
                if (!Db::getInstance()->execute(trim($query))) {
                    $this->log('Error when executing this query :' . $query);

                    return false;
                }
            }
            if (!Configuration::updateValue('TA_CARTR_TOKEN', $this->generateCode('', 15))
                || !Configuration::updateValue('TA_CARTR_KEY', $this->generateCode('', 15))
                || !Configuration::updateValue('TA_CARTR_ABANDONNED_NB_HOUR', 24)
                || !Configuration::updateValue('TA_CARTR_BATCHLASTDATE', 0)
                || !Configuration::updateValue('TA_CARTR_STOPREMINDER_NB_HOUR', 96)
                || !Configuration::updateValue('TA_CARTR_AFTERREMINDER_NB_DAY', 15)
                || !Configuration::updateValue('TA_CARTR_CR_PREFIX', 'CRE-')
                || !Configuration::updateValue('TA_CARTR_CODE_FORMAT', 'LLLNLNLNLNLNLNLNLNLL')
                || !Configuration::updateValue('TA_CARTR_AUTO_ADD_CR', 0)
                || !Configuration::updateValue('TA_CARTR_CLEANCARTRULE_NB_DAY', 30)
                || !Configuration::updateValue('TA_CARTR_DEBUG', 0)
                || !Configuration::updateValue('TA_CARTR_SHOPLOGO', 1)
            ) {
                $this->log('Error when updating configuration value');

                return false;
            }
        }
        if (parent::install() == false
            || !$this->registerHook('displayHeader')
            || !$this->registerHook('actionValidateOrder')
            || !$this->registerHook('actionCartSave')
            || !$this->registerHook('actionObjectCartDeleteAfter')
            || !$this->registerHook('actionObjectCartRuleDeleteAfter')
            || !$this->registerHook('actionCronJob')
            || !$this->registerHook('actionDeleteGDPRCustomer')
            || !$this->installModuleTab()
            || !Configuration::updateValue('TA_CARTR_VERSION', $this->version)
        ) {
            $this->log('Error when register hook or install module tab');

            return false;
        }
        $this->log('Installation successful');

        return true;
    }

    /**
     * Installation module tab
     *
     * @return bool true if tab saved
     */
    private function installModuleTab()
    {
        $admin_tab_parent_id = Tab::getIdFromClassName('AdminParentCustomer');
        $tab = new Tab();
        $languages = Language::getLanguages(true);
        foreach ($languages as $language) {
            if ($language['iso_code'] == 'fr') {
                $tab->name[(int) $language['id_lang']] = 'Relance panier';
            } else {
                $tab->name[(int) $language['id_lang']] = 'Cart reminder';
            }
        }
        $tab->class_name = 'AdminLiveCartReminder';
        $tab->module = $this->name;
        $tab->id_parent = $admin_tab_parent_id;
        if (!$tab->save()) {
            $this->log('Error when saving tab');

            return false;
        }

        return true;
    }

    /**
     * Uninstall the module
     *
     * @param bool|true $delete_params is use for delete all data in db
     *
     * @return bool
     *
     * @see
     */
    public function uninstall($delete_params = true)
    {
        $this->accesslog = $this->accessLogsDirectory();
        $this->log('************ UNINSTALLATION ****************');
        if (!parent::uninstall() || !$this->uninstallModuleTab()) {
            $this->log('Error when uninstall module tab');

            return false;
        }
        if ($delete_params && (!$this->deleteTables()
                || !Configuration::deleteByName('TA_CARTR_TOKEN')
                || !Configuration::deleteByName('TA_CARTR_ABANDONNED_NB_HOUR')
                || !Configuration::deleteByName('TA_CARTR_STOPREMINDER_NB_HOUR')
                || !Configuration::deleteByName('TA_CARTR_AFTERREMINDER_NB_DAY')
                || !Configuration::deleteByName('TA_CARTR_CLEANCARTRULE_NB_DAY')
                || !Configuration::deleteByName('TA_CARTR_DEBUG')
                || !Configuration::deleteByName('TA_CARTR_SHOPLOGO')
                || !Configuration::deleteByName('TA_CARTR_CR_PREFIX')
                || !Configuration::deleteByName('TA_CARTR_CODE_FORMAT')
                || !Configuration::deleteByName('TA_CARTR_AUTO_ADD_CR')
                || !Configuration::deleteByName('TA_CARTR_BATCHLASTDATE')
                || !Configuration::deleteByName('TA_CARTR_VERSION'))
        ) {
            $this->log('Error when delete table or update configuration');

            return false;
        }

        return true;
    }

    /**
     * Uninstall all tab installed by the module
     *
     * @return bool
     */
    private function uninstallModuleTab()
    {
        $id_tab = Tab::getIdFromClassName('AdminLiveCartReminder');
        if ($id_tab != 0) {
            $tab = new Tab($id_tab);
            $tab->delete();

            return true;
        }

        return false;
    }

    /**
     * check if upgrade script is not installed
     *
     * @param bool|false $install
     */
    public function runUpgrades($install = false)
    {
        if (Configuration::get('TA_CARTR_VERSION') != $this->version) {
            foreach (['0.0.5', '0.0.6', '0.0.7', '0.0.8'] as $version) {
                $file = dirname(__FILE__) . '/upgrade/install-' . $version . '.php';
                if (version_compare($version, Configuration::get('TA_CARTR_VERSION')) > 0 && file_exists($file)) {
                    include_once $file;
                    call_user_func('upgrade_module_' . str_replace('.', '_', $version), $this, $install);
                }
            }
        }
        Configuration::updateValue('TA_CARTR_VERSION', $this->version);
    }

    /**
     * reset the module, no data is deleted
     *
     * @return bool
     */
    public function reset()
    {
        if (!$this->uninstall(false)) {
            return false;
        }
        if (!$this->install(false)) {
            return false;
        }

        return true;
    }

    /**
     * Delete all table installed by the module
     *
     * @return bool
     */
    public function deleteTables()
    {
        return Db::getInstance()->execute(
            '
			DROP TABLE IF EXISTS
			`' . _DB_PREFIX_ . 'ta_cartreminder_rule`,
			`' . _DB_PREFIX_ . 'ta_cartreminder_rule_match_cache`,
			`' . _DB_PREFIX_ . 'ta_cartreminder_rule_shop`,
			`' . _DB_PREFIX_ . 'ta_cartreminder_rule_reminder`,
			`' . _DB_PREFIX_ . 'ta_cartreminder_customer_unsubscribe`,
			`' . _DB_PREFIX_ . 'ta_cartreminder_journal`,
			`' . _DB_PREFIX_ . 'ta_cartreminder_journal_reminder`,
			`' . _DB_PREFIX_ . 'ta_cartreminder_journal_message`,
			`' . _DB_PREFIX_ . 'ta_cartreminder_unscriber`,
			`' . _DB_PREFIX_ . 'ta_cartreminder_rule_groupcondition`,
			`' . _DB_PREFIX_ . 'ta_cartreminder_rule_condition`,
			`' . _DB_PREFIX_ . 'ta_cartreminder_rule_condition_value`,
			`' . _DB_PREFIX_ . 'ta_cartreminder_mail_template`,
			`' . _DB_PREFIX_ . 'ta_cartreminder_mail_template_lang`,
			`' . _DB_PREFIX_ . 'ta_cartreminder_mail_template_shop`,
			`' . _DB_PREFIX_ . 'ta_cartreminder_reassigned_cart`
		'
        );
    }

    /**
     * Hook when cart rule is deleted
     * If this cart rule is used on a rule, the rule is updated
     *
     * @param $params
     */
    public function hookActionObjectCartRuleDeleteAfter($params)
    {
        if ($this->ta_ca_debug) {
            $this->accesslog = $this->accessLogsDirectory();
            $this->log('************ hookActionObjectCartRuleDeleteAfter ************');
        }
        $cart_rule = $params['object'];
        if ($cart_rule && $cart_rule->id) {
            $rules = TACartReminderRule::getRulesUseCartRule((int) $cart_rule->id);
            foreach ($rules as $rule) {
                if ($this->ta_ca_debug) {
                    $this->log('Cart Rule in Rule ' . (int) $rule['id_rule'] . ' will be remove');
                }
                $id_rule = (int) $rule['id_rule'];
                $rule = new TACartReminderRule($id_rule);
                $rule->create_cart_rule = false;
                $rule->id_cart_rule = 0;
                $rule->cart_rule_nbday_validity = 0;
                $rule->update();
                if ($this->ta_ca_debug) {
                    $this->log('Cart Rule in Rule ' . (int) $rule['id_rule'] . ' removed');
                }
            }
        }
        if ($this->ta_ca_debug) {
            $this->log('************ END ************');
        }
    }

    /**
     * When a cart is deleted => close the journal
     *
     * @param $params
     */
    public function hookActionObjectCartDeleteAfter($params)
    {
        if ($this->ta_ca_debug) {
            $this->accesslog = $this->accessLogsDirectory();
            $this->log('************ hookActionObjectCartDeleteAfter ************');
        }
        $cart = $params['object'];
        if ($cart && (int) $cart->id) {
            if ($this->ta_ca_debug) {
                $this->log('Cache for the cart will be remove');
            }
            TACartReminderRuleMatchCache::clean($cart->id);
            if ($this->ta_ca_debug) {
                $this->log('Cache for the cart removed');
            }
            $journal = TACartReminderJournal::getWithCart($cart->id);
            if ($journal && $journal->id && $journal->state == 'RUNNING') {
                if ($this->ta_ca_debug) {
                    $this->log('The cart id ' . (int) $cart->id . ' has bean deleted, the reminder will be cancel.');
                }
                $message = (string) sprintf(
                    $this->l('The cart id %1$s has bean deleted, the reminder is canceled.'),
                    (int) $cart->id
                );
                $journal->close($message);
                if ($this->ta_ca_debug) {
                    $this->log('The journal ' . (int) $journal->id . ' is closed.');
                }
            }
        }
        if ($this->ta_ca_debug) {
            $this->log('************ END ************');
        }
    }

    /**
     * When order is validate, indicate it is the reminder permit
     * journal is close
     *
     * @param $params
     */
    public function hookActionValidateOrder($params)
    {
        if ($this->ta_ca_debug) {
            $this->log('************ hookActionValidateOrder ************');
        }
        $cart = $params['cart'];
        $order = $params['order'];
        $id_cart = (int) $cart->id;
        if ($this->ta_ca_debug) {
            $this->log('Search Journal for id_cart:' . $id_cart);
        }
        $journal = TACartReminderJournal::getWithCart($id_cart);
        if ($journal && $journal->id && (!isset($journal->id_order) || !(int) $journal->id_order)) {
            if ($this->ta_ca_debug) {
                $this->log('Journal found : ' . $journal->id . ' clean cache for id_cart:' . $id_cart);
            }
            TACartReminderRuleMatchCache::clean($id_cart);
            if ($this->ta_ca_debug) {
                $this->log('Cache is cleaned');
            }
            $journal->toOrdered((int) $order->id);
            if ($this->ta_ca_debug) {
                $this->log('Journal is ordered');
            }
        }
        if ($this->ta_ca_debug) {
            $this->log('************ END ************');
        }
    }

    public function hookActionDeleteGDPRCustomer($customer)
    {
        if (!empty($customer['email'])
            && Validate::isEmail($customer['email'])
            && (int) $customer['id']) {
            $result = TACartReminderTools::removePersonalDataPSGDPR(
                $customer['email'],
                (int) $customer['id']
            );
            if ($result) {
                return json_encode(true);
            } else {
                return json_encode($this->l('Smart Cart Reminder : Unable to delete customer using email.'));
            }
        }
    }

    /**
     * this hook is call many times update the cart
     * The module catch only cart have customer logged
     * After many test not impact on the performance
     * If cart ID change for the customer update the relation journal => id_cart
     * When cart is save  :
     *  clean cache Match : rule <-> cart(not impact of performance)
     */
    public function hookActionCartSave()
    {
        if ($this->ta_ca_debug) {
            $this->accesslog = $this->accessLogsDirectory();
            $this->log('************ hookActionCartSave ************');
        }
        $cart = $this->context->cart;
        if (isset($cart) && (int) $cart->id && (int) $cart->id_customer) {
            if ($this->ta_ca_debug) {
                $this->log('Cache will be clean for the cart : ' . (int) $cart->id);
            }
            TACartReminderRuleMatchCache::clean($cart->id);
            if ($this->ta_ca_debug) {
                $this->log('Cache will cleaned');
                $this->log('Check Cart Running for a customer');
            }
            $customer = new Customer((int) $cart->id_customer);
            $journal_running = TACartReminderJournal::getRunningByCustomer($customer->email, (int) $cart->id_shop);
            if ($journal_running && (int) $journal_running->id
                && (int) $journal_running->id_cart != (int) $cart->id && !(int) $journal_running->id_order) {
                if ($this->ta_ca_debug) {
                    $this->log('Running Cart is found for a customer and cart id is changed');
                }
                $mess = new TACartReminderMessage();
                $mess->id_journal = (int) $journal_running->id;
                $mess->is_system = true;
                $mess->message = (string) sprintf(
                    $this->l('Verification process last cart update, old cart id %1$s to new cart id %2$s.'),
                    (int) $journal_running->id_cart,
                    (int) $cart->id
                );
                $journal_running->id_cart = $cart->id;
                /* if cart is deleted no check older cart */
                $journal_running->date_upd_cart = $cart->date_upd;
                if ($this->ta_ca_debug) {
                    $this->log('journal will be update');
                }
                $journal_running->update();
                if ($this->ta_ca_debug) {
                    $this->log('journal updated');
                }
                $mess->add();
                if ($this->ta_ca_debug) {
                    $this->log('Message add');
                }
            } else {
                if ($this->ta_ca_debug) {
                    $this->log('Search journal to update last date cart updated');
                }
                $journal_to_update = TACartReminderJournal::getWithCart((int) $cart->id);
                if ($journal_to_update && $journal_to_update->id) {
                    if ($this->ta_ca_debug) {
                        $this->log('Journal found');
                    }
                    $journal_to_update->date_upd_cart = $cart->date_upd;
                    $journal_to_update->update();
                    if ($this->ta_ca_debug) {
                        $this->log('Journal updated');
                    }
                }
            }
        }
        if ($this->ta_ca_debug) {
            $this->log('************ END ************');
        }
    }

    /**
     * Validate the rule form
     *
     * @param bool|true $die is use for ajax validate
     */
    public function validateRuleForm($die = true)
    {
        $return = [
            'has_error' => false,
        ];
        $name = Tools::getValue('name');
        $create_cart_rule = (int) Tools::getValue('create_cart_rule');
        $id_cart_rule = (int) Tools::getValue('id_cart_rule');
        $cart_rule_nbday_validity = (int) Tools::getValue('cart_rule_nbday_validity');
        $date_from = Tools::getValue('date_from');
        $date_to = Tools::getValue('date_to');
        if (empty($name)) {
            $return['errors'][] = sprintf($this->l('Field %s is required'), $this->l('Rule name'));
        }
        if ($create_cart_rule && !$id_cart_rule) {
            $return['errors'][] = $this->l('Create discount is selected. You must select a cart rule.');
        }
        if (!Validate::isInt($cart_rule_nbday_validity)) {
            $return['errors'][] = sprintf($this->l('Field %s is not in a valid format'), $this->l('Nb day validity'));
        }
        if (!empty($date_from) && !Validate::isDateFormat($date_from)) {
            $return['errors'][] = sprintf($this->l('Field %s is not in a valid format'), $this->l('Date from'));
        }
        if (!empty($date_to) && !Validate::isDateFormat($date_to)) {
            $return['errors'][] = sprintf($this->l('Field %s is not in a valid format'), $this->l('Date to'));
        }
        if (Tools::isSubmit('reminder_1_id')) {
            $i = 1;
            while (Tools::isSubmit('reminder_' . $i . '_id')) {
                /* $id_reminder = (int)Tools::getValue('reminder_'.$i.'_id'); */
                $id_mail_template = (int) Tools::getValue('reminder_' . $i . '_id_mail_template');
                $nb_hour = (float) Tools::getValue('reminder_' . $i . '_nb_hour');
                $manual_process = (int) Tools::getValue('reminder_' . $i . '_manual_process');
                $admin_mails = Tools::getValue('reminder_' . $i . '_admin_mails');
                if ((int) $manual_process && strpos($admin_mails, ',') !== false) {
                    $admin_mails = explode(',', $admin_mails);
                    foreach ($admin_mails as $key => $admin_mail) {
                        $admin_mail = trim($admin_mail);
                        if (!Validate::isEmail($admin_mail)) {
                            $return['errors'][] = sprintf(
                                $this->l('Admin email %1$s is not a valid address for reminder %2$s'),
                                $admin_mail,
                                $i
                            );
                        }
                    }
                }
                if (!(int) $manual_process && !((int) $id_mail_template > 0)) {
                    $return['errors'][] = sprintf($this->l('The email template is required for reminder %s'), $i);
                }
                if ((int) $manual_process && (empty($admin_mails))) {
                    $return['errors'][] = sprintf(
                        $this->l('Admin email address is empty or invalid for reminder %s'),
                        $i
                    );
                }
                if ((int) $manual_process && (!empty($admin_mails)) && !is_array($admin_mails)
                    && !Validate::isEmail($admin_mails)
                ) {
                    $return['errors'][] = sprintf(
                        $this->l('Admin email %1$s is not a valid address for reminder %2$s'),
                        $admin_mails,
                        $i
                    );
                }
                if (!((float) $nb_hour > 0)) {
                    $return['errors'][] = sprintf($this->l('Nb time for reminder %s is not in a valid format'), $i);
                }
                ++$i;
            }
        } else {
            $return['errors'][] = $this->l('The rule must contain a reminder.');
        }
        if (isset($return['errors']) && count($return['errors']) && $die) {
            $return['has_error'] = true;
            exit(json_encode($return));
        }
    }

    /**
     * Post process
     * all action process for setting throw this method :
     *
     * @throws TACRHtml2TextException
     */
    protected function postProcess()
    {
        /* RULE ADMINISTRATION */
        if (Tools::isSubmit('deleteta_cartreminder_rule')) {
            $this->tab_configure = 'rule';
            $id_rule = (int) Tools::getValue('id_rule');
            $rule = new TACartReminderRule($id_rule);
            $rule->delete();
        } elseif (Tools::isSubmit('statusta_cartreminder_rule')) {
            $this->tab_configure = 'rule';
            $rule = new TACartReminderRule((int) Tools::getValue('id_rule'));
            if ($rule->id) {
                $rule->status = (int) !$rule->status;
                $rule->save();
            }
        } elseif (Tools::isSubmit('check_reminder_befor_delete')) {
            $id_reminder = Tools::getValue('id_reminder');
            $info_jreminders = TACartReminderJournal::getJRRunningByExecuted($id_reminder);
            $return = [
                'has_error' => false,
            ];
            if ($info_jreminders && count($info_jreminders) > 0) {
                $this->context->smarty->assign('info_jreminders', $info_jreminders);
                $this->context->smarty->assign('id_reminder', $id_reminder);
                $page_check_cartreminders = $this->context->smarty->fetch(
                    $this->local_path . 'views/templates/admin/_configure/check_cartreminders.tpl'
                );
                $return = [
                    'has_error' => true,
                    'page_content' => $page_check_cartreminders,
                ];
            }
            exit(json_encode($return));
        } elseif (Tools::isSubmit('submitEditRule')) {
            $this->tab_configure = 'rule';
            $this->validateRuleForm();
            $return = [
                'has_error' => false,
            ];
            $rule = new TACartReminderRule((int) Tools::getValue('id_rule'));
            $rule->name = Tools::getValue('name');
            $rule->status = (int) Tools::getValue('status');
            $rule->create_cart_rule = (int) Tools::getValue('create_cart_rule', 0);
            $rule->force_reminder = (int) Tools::getValue('force_reminder', 0);
            if ($rule->create_cart_rule) {
                $rule->id_cart_rule = (int) Tools::getValue('id_cart_rule');
                $rule->cart_rule_nbday_validity = (int) Tools::getValue('cart_rule_nbday_validity');
            }
            $rule->date_from = Tools::getValue('date_from');
            $rule->date_to = Tools::getValue('date_to');
            if (isset($rule->id) && (int) $rule->id) {
                Db::getInstance()->execute(
                    'DELETE FROM `' . _DB_PREFIX_ . 'ta_cartreminder_rule_groupcondition` WHERE `id_rule` = ' . $rule->id
                );
                Db::getInstance()->execute(
                    'DELETE FROM `' . _DB_PREFIX_ . 'ta_cartreminder_rule_condition`
						WHERE `id_groupcondition`
						NOT IN (SELECT `id_groupcondition` FROM `' . _DB_PREFIX_ . 'ta_cartreminder_rule_groupcondition`)'
                );
                Db::getInstance()->execute(
                    'DELETE FROM `' . _DB_PREFIX_ . 'ta_cartreminder_rule_condition_value`
						WHERE `id_condition`
						NOT IN (SELECT `id_condition` FROM `' . _DB_PREFIX_ . 'ta_cartreminder_rule_condition`)'
                );
            }
            if (($rule->id && $rule->update()) || (!$rule->id && $rule->add())) {
                $this->updateAssoShop('ta_cartreminder_rule', $rule->id, 'id_rule');
                if (Tools::getValue('condition_group')
                    && is_array($condition_group_array = Tools::getValue('condition_group'))
                    && count($condition_group_array)) {
                    foreach ($condition_group_array as $condition_group_id) {
                        Db::getInstance()->execute(
                            'INSERT INTO `' . _DB_PREFIX_ . 'ta_cartreminder_rule_groupcondition` (`id_rule`)
							VALUES (' . (int) $rule->id . ')'
                        );
                        $id_group = Db::getInstance()->Insert_ID();
                        if (is_array($condition_array = Tools::getValue('condition_' . $condition_group_id))
                            && count($condition_array)
                        ) {
                            foreach ($condition_array as $condition_id) {
                                $type_condition = Tools::getValue(
                                    'condition_' . $condition_group_id . '_' . $condition_id . '_type'
                                );
                                Db::getInstance()->execute(
                                    'INSERT INTO `' . _DB_PREFIX_ . 'ta_cartreminder_rule_condition`
                                    (`id_groupcondition`, `type`)
                                    VALUES (' . (int) $id_group . ', "' . pSQL($type_condition) . '")'
                                );
                                $id_condition = Db::getInstance()->Insert_ID();
                                if (Tools::getValue('condition_' . $condition_group_id . '_' . $condition_id . '_typevalue')
                                    == 'list') {
                                    $values = [];
                                    $cond_select_param = 'condition_select_' . $condition_group_id . '_' . $condition_id;
                                    $values_params = Tools::getValue($cond_select_param);
                                    foreach ($values_params as $id) {
                                        $values[] = '(' . (int) $id_condition . ',' . (int) $id . ',\'list\')';
                                    }
                                    $values = array_unique($values);
                                    if (count($values)) {
                                        Db::getInstance()->execute(
                                            'INSERT INTO `' . _DB_PREFIX_ . 'ta_cartreminder_rule_condition_value`' .
                                            '(`id_condition`, `id_item`,`typevalue`) VALUES ' . implode(',', $values)
                                        );
                                    }
                                } else {
                                    $value = Tools::getValue(
                                        'condition_' . $condition_group_id . '_' . $condition_id . '_value'
                                    );
                                    $typevalue = Tools::getValue(
                                        'condition_' . $condition_group_id . '_' . $condition_id . '_typevalue'
                                    );
                                    $sign = Tools::getValue('condition_' . $condition_group_id . '_' . $condition_id . '_sign');
                                    if ($typevalue == 'bool' && !Validate::isBool($value)) {
                                        $return['errors'][] = sprintf(
                                            $this->l('Condition %1$s value is not in a valid format, group number %2s'),
                                            $type_condition,
                                            $condition_group_id
                                        );
                                    } elseif ($typevalue == 'integer' && !Validate::isInt($value)) {
                                        $return['errors'][] = sprintf(
                                            $this->l('Condition %1$s value is not in a valid format, group number %2s'),
                                            $type_condition,
                                            $condition_group_id
                                        );
                                    } elseif ($typevalue == 'price' && !Validate::isPrice($value)) {
                                        $return['errors'][] = sprintf(
                                            $this->l('Condition %1$s value is not in a valid format, group number %2s'),
                                            $type_condition,
                                            $condition_group_id
                                        );
                                    } elseif ($typevalue == 'string' && empty($value)) {
                                        $return['errors'][] = sprintf(
                                            $this->l('Condition %1$s is required, group number %2$s'),
                                            $type_condition,
                                            $condition_group_id
                                        );
                                    } else {
                                        Db::getInstance()->execute(
                                            'INSERT INTO `' . _DB_PREFIX_ . 'ta_cartreminder_rule_condition_value`
                                            (`id_condition`, `value`,`typevalue`,`sign`)
											VALUES (' . (int) $id_condition . ',\'' . $value . '\',
													\'' . $typevalue . '\',\'' . $sign . '\')'
                                        ); // pSQL($sign, true) html:true because html
                                    }
                                }
                            }
                        }
                    }
                }
                $reminders = $rule->getReminders();
                $reminder_id_deletes = [];
                // check reminder and delete
                foreach ($reminders as $reminder) {
                    $i = 1;
                    $foundreminder = false;
                    while (Tools::isSubmit('reminder_' . $i . '_id')) {
                        if ((int) $reminder['id_reminder'] == (int) Tools::getValue('reminder_' . $i . '_id')) {
                            $foundreminder = true;
                        }
                        ++$i;
                    }
                    if (!$foundreminder) {
                        $reminder_id_deletes[] = (int) $reminder['id_reminder'];
                    }
                }
                // MARK CLOSED REMINDER because is deleted
                foreach ($reminder_id_deletes as $reminder_id_delete) {
                    $jrs_to_closed = TACartReminderJournal::getJRRunningByExecuted($reminder_id_delete);
                    foreach ($jrs_to_closed as $jr_to_closed) {
                        $journal = new TACartReminderJournal((int) $jr_to_closed['id_journal']);
                        if (Validate::isLoadedObject($journal) && $journal->id && $journal->state == 'RUNNING') {
                            $journal->state = 'CANCELED';
                            $journal->update();
                            $mess = new TACartReminderMessage();
                            $mess->id_journal = (int) $journal->id;
                            $mess->message =
                                $this->l('The cart reminder has been closed because the rule is updated and launched reminder has been deleted by employee');
                            $mess->id_employee = $this->context->employee->id;
                            $mess->add();
                        }
                    }
                }
                if (count($reminder_id_deletes)) {
                    Db::getInstance()->execute(
                        'DELETE FROM `' . _DB_PREFIX_ . 'ta_cartreminder_rule_reminder`
							WHERE `id_rule`=' . $rule->id . '
							AND `id_reminder` IN (' . implode(',', $reminder_id_deletes) . ')'
                    );
                }
                if (Tools::isSubmit('reminder_1_id')) {
                    $reminder_update_ids = [];
                    $pos = 1;
                    while (Tools::isSubmit('reminder_' . $pos . '_id')) {
                        $id_reminder = (int) Tools::getValue('reminder_' . $pos . '_id');
                        $data_reminder = [
                            'id_mail_template' => (int) Tools::getValue('reminder_' . $pos . '_id_mail_template'),
                            'nb_hour' => (float) Tools::getValue('reminder_' . $pos . '_nb_hour'),
                            'manual_process' => (int) Tools::getValue('reminder_' . $pos . '_manual_process'),
                            'admin_mails' => (string) Tools::getValue('reminder_' . $pos . '_admin_mails'),
                            'position' => $pos,
                            'id_rule' => (int) $rule->id,
                        ];
                        if ($id_reminder) {
                            Db::getInstance()->update(
                                'ta_cartreminder_rule_reminder',
                                $data_reminder,
                                '`id_reminder` = ' . (int) $id_reminder
                            );
                            $reminder_update_ids[] = $id_reminder;
                        } else {
                            Db::getInstance()->insert('ta_cartreminder_rule_reminder', $data_reminder);
                            $reminder_update_ids[] = Db::getInstance()->Insert_ID();
                        }
                        ++$pos;
                    }
                    Db::getInstance()->execute(
                        'DELETE FROM `' . _DB_PREFIX_ . 'ta_cartreminder_rule_reminder`
                        WHERE `id_rule` = ' . $rule->id .
                        (count($reminder_update_ids) ?
                            ' AND id_reminder not in (' . implode(',', $reminder_update_ids) . ')' : '')
                    );
                }
                TACartReminderCleans::noReminderToLaunch();
            }
            if (isset($return['errors']) && count($return['errors'])) {
                $return['has_error'] = true;
            }
            exit(json_encode($return));
        } elseif (Tools::isSubmit('rule') && Tools::isSubmit('action')
            && Tools::getValue('action') == 'updatePositions'
        ) {
            $this->ajaxProcessRulePositions();
        } elseif (Tools::isSubmit('convertHTMLTOTXT')) {
            $id_lang = (int) Tools::getValue('id_lang');
            $content_html = Tools::getValue('content_html_' . $id_lang);
            $content_html = str_replace('{cart_products}', '{cart_products_txt}', $content_html);
            if (empty($content_html)) {
                $language = new Language($id_lang);

                return sprintf($this->l('The email content for the lang %s is empty'), $language->iso_code);
            }
            exit(TACartReminderTools::convertHtmlToText($content_html));
        } elseif (Tools::isSubmit('getExampleMailTemplate')) {
            $template_file_path = _PS_ROOT_DIR_ . '/modules/tacartreminder/data/mail_templates/' .
                Tools::getValue('getExampleMailTemplate') . '.html';
            if (file_exists($template_file_path)) {
                exit(Tools::file_get_contents($template_file_path));
            } else {
                exit('');
            }
        } elseif (Tools::isSubmit('submitEditMailTemplate')) {
            $this->tab_configure = 'mail';
            $this->form_submit = 'mail_template';
            $mail_template = new TACartReminderMailTemplate((int) Tools::getValue('id_mail_template'));
            $languages = Language::getLanguages(false);
            $lang_def = new Language((int) Configuration::get('PS_LANG_DEFAULT'));
            $subject = [];
            $title = [];
            $content_html = [];
            $content_txt = [];
            foreach ($languages as $key => $value) {
                $subject[$value['id_lang']] = Tools::getValue('subject_' . $value['id_lang']);
                $title[$value['id_lang']] = Tools::getValue('title_' . $value['id_lang']);
                $content_html[$value['id_lang']] = Tools::getValue('content_html_' . $value['id_lang']);
                $content_txt[$value['id_lang']] = Tools::getValue('content_txt_' . $value['id_lang']);
            }
            $content_html_safe = trim(Tools::safeOutput($content_html[(int) $lang_def->id]));
            if (!isset($content_html[(int) $lang_def->id]) || empty($content_html[(int) $lang_def->id])
                || empty($content_html_safe)
            ) {
                $this->post_errors[] = sprintf(
                    $this->l('Field %1$s is required in default lang %2$s'),
                    $this->l('Content html'),
                    $lang_def->iso_code
                );
            }
            if (!isset($content_txt[(int) $lang_def->id]) || empty($content_txt[(int) $lang_def->id])) {
                $this->post_errors[] = sprintf(
                    $this->l('Field %1$s is required in default lang %2$s'),
                    $this->l('Content txt'),
                    $lang_def->iso_code
                );
            }
            if (!isset($subject[(int) $lang_def->id]) || empty($subject[(int) $lang_def->id])) {
                $this->post_errors[] = sprintf(
                    $this->l('Field %1$s is required in default lang %2$s'),
                    $this->l('Subject'),
                    $lang_def->iso_code
                );
            }
            if (!isset($title[(int) $lang_def->id]) || empty($title[(int) $lang_def->id])) {
                $this->post_errors[] = sprintf(
                    $this->l('Field %1$s is required in default lang %2$s'),
                    $this->l('Title'),
                    $lang_def->iso_code
                );
            }
            if (!count($this->post_errors)) {
                $mail_template->name = Tools::getValue('name');
                $mail_template->subject = $subject;
                $mail_template->title = $title;
                $mail_template->content_html = $content_html;
                $mail_template->content_txt = $content_txt;
                if ($mail_template->save()
                    && (!Shop::isFeatureActive()
                        || $this->updateAssoShop('ta_cartreminder_mail_template', $mail_template->id, 'id_mail_template'))) {
                    $this->success_conf = $this->l('Settings updated');
                } else {
                    $this->context->smarty->assign('mt_error', $this->l('The email template could not be saved.'));
                }
            }
        } elseif (Tools::isSubmit('deleteta_cartreminder_mail_template')) {
            $this->tab_configure = 'mail';
            $id_mail_template = (int) Tools::getValue('id_mail_template');
            $rules_use_mail = TACartReminderRule::getRulesByMailTemplate($id_mail_template);
            if ($rules_use_mail && count($rules_use_mail) > 0) {
                $error_delete = $this->l('You cannot delete this email template because it is in use.') . '<br/>';
                $error_delete .=
                    $this->l('If you want to delete this email template, you must first exchange this email with another one for this/these rule(s)') .
                    '<br/>';
                $error_delete .= '<ul>';
                foreach ($rules_use_mail as $rule_use_mail) {
                    $error_delete .= '<li>' . $rule_use_mail['name'] . '</li>';
                }
                $error_delete .= '</ul>';
                $this->post_errors[] = $error_delete;
            } else {
                $mail_template = new TACartReminderMailTemplate($id_mail_template);
                $mail_template->delete();
                $this->success_conf = $this->l('Settings updated');
            }
        } elseif (Tools::isSubmit('submitSettings')) {
            $this->tab_configure = 'configuration';
            if (!Validate::isFloat(Tools::getValue('TA_CARTR_ABANDONNED_NB_HOUR'))
                || (float) Tools::getValue('TA_CARTR_ABANDONNED_NB_HOUR') < 0
            ) {
                $this->post_errors[] = sprintf(
                    $this->l('Field %s is not in a valid format'),
                    $this->l('Number of hours after which to consider a cart abandoned')
                );
            } elseif ((float) Tools::getValue('TA_CARTR_ABANDONNED_NB_HOUR') <
                (float) Tools::getValue('TA_CARTR_STOPREMINDER_NB_HOUR')
            ) {
                Configuration::updateValue(
                    'TA_CARTR_ABANDONNED_NB_HOUR',
                    (float) Tools::getValue('TA_CARTR_ABANDONNED_NB_HOUR')
                );
            }
            if (!Validate::isInt(Tools::getValue('TA_CARTR_STOPREMINDER_NB_HOUR'))) {
                $this->post_errors[] = sprintf(
                    $this->l('Field %s is not in a valid format'),
                    $this->l('Number of hours after which to stop the reminder')
                );
            } elseif ((int) Tools::getValue('TA_CARTR_ABANDONNED_NB_HOUR') >
                (int) Tools::getValue('TA_CARTR_STOPREMINDER_NB_HOUR')
            ) {
                $this->post_errors[] = $this->l('Cancel time-lapse must exceed cart abandoned time-lapse');
            } else {
                Configuration::updateValue(
                    'TA_CARTR_STOPREMINDER_NB_HOUR',
                    (int) Tools::getValue('TA_CARTR_STOPREMINDER_NB_HOUR')
                );
            }
            $cr_prefix = Tools::getValue('TA_CARTR_CR_PREFIX');
            if (empty($cr_prefix)) {
                $this->post_errors[] = sprintf($this->l('Field %s is required'), $this->l('Coupon prefix'));
            } else {
                Configuration::updateValue('TA_CARTR_CR_PREFIX', $cr_prefix);
            }
            if (!count($this->post_errors)) {
                $this->success_conf = $this->l('Settings updated');
            }
            if (!Validate::isInt(Tools::getValue('TA_CARTR_AFTERREMINDER_NB_DAY'))) {
                $this->post_errors[] = sprintf(
                    $this->l('Field %s is not in a valid format'),
                    $this->l('Number of days since last reminder')
                );
            } else {
                Configuration::updateValue(
                    'TA_CARTR_AFTERREMINDER_NB_DAY',
                    (int) Tools::getValue('TA_CARTR_AFTERREMINDER_NB_DAY')
                );
            }
            Configuration::updateValue('TA_CARTR_AUTO_ADD_CR', (int) Tools::getValue('TA_CARTR_AUTO_ADD_CR'));
            $code_format = (string) Tools::getValue('TA_CARTR_CODE_FORMAT');
            if (empty($code_format)) {
                $this->post_errors[] = $this->l('Format code is required');
            } else {
                if (Tools::strlen($code_format) < 10) {
                    $this->post_errors[] = $this->l('Format code min length is 10');
                }
                if (Tools::strlen($code_format) > 70) {
                    $this->post_errors[] = $this->l('Format code max length is 70');
                } else {
                    if (!preg_match('/^[NL]+$/', $code_format)) {
                        $this->post_errors[] = $this->l('Format code is invalid');
                    } else {
                        Configuration::updateValue('TA_CARTR_CODE_FORMAT', $code_format);
                    }
                }
            }
            if (!Validate::isInt(Tools::getValue('TA_CARTR_CLEANCARTRULE_NB_DAY'))) {
                $this->post_errors[] = sprintf(
                    $this->l('Field %s is not in a valid format'),
                    $this->l('Number of days after which to delete expired coupons')
                );
            } else {
                Configuration::updateValue(
                    'TA_CARTR_CLEANCARTRULE_NB_DAY',
                    (int) Tools::getValue('TA_CARTR_CLEANCARTRULE_NB_DAY')
                );
            }
            if (!Validate::isInt(Tools::getValue('TA_CARTR_DEBUG'))) {
                $this->post_errors[] = sprintf(
                    $this->l('Field %s is not in a valid format'),
                    $this->l('Debug')
                );
            } else {
                Configuration::updateValue(
                    'TA_CARTR_DEBUG',
                    (int) Tools::getValue('TA_CARTR_DEBUG')
                );
            }
            if (!Validate::isInt(Tools::getValue('TA_CARTR_SHOPLOGO'))) {
                $this->post_errors[] = sprintf(
                    $this->l('Field %s is not in a valid format'),
                    $this->l('Shop logo')
                );
            } else {
                Configuration::updateValue(
                    'TA_CARTR_SHOPLOGO',
                    (int) Tools::getValue('TA_CARTR_SHOPLOGO')
                );
            }
        }
    }

    /**
     * setting form
     *
     * @return string
     */
    public function getContent()
    {
        // default assign
        $this->context->smarty->assign(
            [
                'module_url' => $this->context->link->getAdminLink('AdminModules', false) .
                    '&configure=' . $this->name . '&tab_module=' . $this->tab . '&module_name=' . $this->name .
                    '&token=' . Tools::getAdminTokenLite(
                        'AdminModules'
                    ),
            ]
        );
        if (Tools::isSubmit('ajax_action')) {
            if (Tools::getValue('ajax_action') == 'searchItemList'
                && ($condition_type = Tools::getValue('condition_type'))
                && ($search = Tools::getValue('search'))
            ) {
                $result = $this->searchItemList($search, $condition_type);
                exit(json_encode($result));
            } elseif (Tools::getValue('ajax_action') == 'render_egmail_custom') {
                exit($this->renderEgMailCustom((string) Tools::getValue('mode')));
            } elseif (Tools::getValue('ajax_action') == 'send_egmail_custom') {
                exit($this->sendEgMailCustom());
            } elseif (Tools::getValue('ajax_action') == 'preview_egmail_custom') {
                $withoutinfo = Tools::getValue('withoutinfo', 0);
                if ((int) $withoutinfo) {
                    exit($this->previewEgMailCustom(false));
                } else {
                    exit($this->previewEgMailCustom());
                }
            } elseif (Tools::getValue('ajax_action') == 'preview_mail') {
                $cart = new Cart((int) Tools::getValue('id_cart'));
                $type_render = Tools::getValue('type_render');
                $content = ($type_render == 'html' ? Tools::getValue('content_html_' . $cart->id_lang) : Tools::getValue(
                    'content_txt_' . $cart->id_lang
                ));
                $content = TACartReminderTools::cartCSSToInline($content);
                $title = (string) Tools::getValue('title_' . $cart->id_lang, '');
                exit(TACartReminderTools::renderMail(0, (int) $cart->id, $content, $title, '########', true));
            } elseif (Tools::getValue('ajax_action') == 'send_mail') {
                exit($this->sendMail());
            } else {
                $this->ajaxAdminCall();
            }
        }
        $this->postProcess();
        $this->context->smarty->assign('ps_version', _PS_VERSION_);
        $this->context->controller->addCSS($this->_path . 'views/css/admin.css');
        $this->context->controller->addCSS($this->_path . 'views/css/icons/flaticon.css');
        $this->context->controller->addCSS($this->_path . 'views/css/admin-ta-common.css');
        if (version_compare(_PS_VERSION_, '1.6.0', '<') === true) {
            $this->context->controller->addjQueryPlugin(
                [
                    'autocomplete',
                    'date',
                    'fancybox',
                ]
            );
            $this->context->controller->addJqueryUI(
                [
                    'ui.core',
                    'ui.widget',
                    'ui.slider',
                    'ui.datepicker',
                ]
            );
            $this->context->controller->addJS($this->_path . 'views/js/ta-ps15.js');
            $this->context->controller->addCSS($this->_path . 'views/css/admin-ta-commonps15.css');
            $this->context->controller->addJS(_PS_JS_DIR_ . 'jquery/plugins/timepicker/jquery-ui-timepicker-addon.js');
            $this->context->controller->addCSS(_PS_JS_DIR_ . 'jquery/plugins/timepicker/jquery-ui-timepicker-addon.css');
        }
        if (Tools::isSubmit('updateta_cartreminder_rule') && !Tools::isSubmit('submitEditRule')) {
            $this->context->controller->addJqueryPlugin(
                [
                    'typewatch',
                    'fancybox',
                    'autocomplete',
                ]
            );
            $this->context->controller->addJS($this->_path . 'views/js/rule_form.js');
            $this->html_return .= $this->renderMenuTop();
            $this->html_return .= $this->renderRuleView();
        } elseif ((!Tools::isSubmit('previewmail') && Tools::isSubmit('updateta_cartreminder_mail_template'))
            || (count($this->post_errors) && $this->form_submit == 'mail_template')
        ) {
            foreach ($this->post_errors as $err) {
                $this->html_return .= $this->displayError($err);
            }
            if (!empty($this->success_conf)) {
                $this->html_return .= $this->displayConfirmation($this->success_conf);
            }
            $this->context->controller->addCSS($this->_path . 'views/css/vendor/elastislide.css');
            $this->context->controller->addJS($this->_path . 'views/js/mail_template_form.js');
            $this->context->controller->addJS($this->_path . 'views/js/vendor/modernizr.custom.js');
            $this->context->controller->addJS($this->_path . 'views/js/vendor/jquerypp.custom.js');
            $this->context->controller->addJS($this->_path . 'views/js/vendor/jquery.elastislide.js');
            $this->context->controller->addJS($this->_path . 'views/js/vendor/jresize.js');
            $this->html_return .= $this->renderMailView();
        } else {
            $this->tab_configure = Tools::getValue('tab_configure', $this->tab_configure);
            $this->context->smarty->assign(
                'currentIndex',
                $this->context->link->getAdminLink('AdminModules', false) .
                '&configure=' . $this->name . '&tab_module=' . $this->tab . '&module_name=' . $this->name
            );
            $this->html_return .= $this->renderMenuTop();
            foreach ($this->post_errors as $err) {
                $this->html_return .= $this->displayError($err);
            }
            if (!empty($this->success_conf)) {
                $this->html_return .= $this->displayConfirmation($this->success_conf);
            }
            if (Tools::isSubmit('saverule_sucess')) {
                $this->context->smarty->assign('saverule_sucess', true);
            }

            $this->html_return .= $this->renderHeaderList();
            if ($this->tab_configure == 'mail') {
                $this->html_return .= $this->renderMailTemplateList();
            } elseif ($this->tab_configure == 'rule') {
                $this->html_return .= $this->renderRuleList();
            } elseif ($this->tab_configure == 'configuration') {
                $this->html_return .= $this->renderConfigForm();
            } elseif ($this->tab_configure == 'cronjob') {
                $this->html_return .= $this->renderCronJobView();
            } elseif ($this->tab_configure == 'supervising') {
                $this->html_return .= $this->renderSuppervisingView();
            }
            $this->html_return .= $this->renderFooterList();
            $this->html_return .= $this->renderFooterModule();
        }
        $this->setBaseUrl();

        return $this->html_return;
    }

    /**
     * set base Url attribute
     */
    private function setBaseUrl()
    {
        $this->base_url = 'index.php?';
        foreach ($_GET as $k => $value) {
            if (!in_array(
                $k,
                [
                    'saverule_sucess',
                ]
            )
            ) {
                $this->base_url .= $k . '=' . $value . '&';
            }
        }
        $this->base_url = rtrim($this->base_url, '&');
    }

    /**
     * Render the main menu display in top
     *
     * @return string html content
     */
    public function renderMenuTop()
    {
        $html_menu = '';
        $this->context->smarty->assign(
            [
                'ta_cr_tab_select' => 'settings',
                'count_manual' => TACartReminderJournal::getManualToDo(true),
                'link' => $this->context->link,
            ]
        );
        $this->context->smarty->assign('ta_cr_tab_select', 'settings');
        $html_menu = $this->context->smarty->fetch($this->local_path . 'views/templates/admin/menu-top.tpl');

        return $html_menu;
    }

    /**
     * @return mixed html
     */
    public function renderHeaderList()
    {
        $html = '';
        $this->context->smarty->assign(
            [
                'tab_configure' => $this->tab_configure,
                'link' => $this->context->link,
            ]
        );
        $html = $this->context->smarty->fetch(
            $this->local_path . 'views/templates/admin/_configure/header-configure.tpl'
        );

        return $html;
    }

    /**
     * display the footer on the list
     *
     * @return string
     */
    public function renderFooterList()
    {
        $html = '';
        $this->context->smarty->assign(
            [
                'tab_configure' => $this->tab_configure,
                'link' => $this->context->link,
            ]
        );
        $html = $this->context->smarty->fetch(
            $this->local_path . 'views/templates/admin/_configure/footer-configure.tpl'
        );

        return $html;
    }

    /**
     * Display the page footer module(documentation, and information on the module)
     *
     * @return string html content
     */
    public function renderFooterModule()
    {
        $html = '';
        $this->context->smarty->assign(
            [
                'tab_configure' => $this->tab_configure,
                'link' => $this->context->link,
            ]
        );
        $html = $this->context->smarty->fetch($this->local_path . 'views/templates/admin/footer-module.tpl');

        return $html;
    }

    /**
     * Return the html for Display the rule form
     *
     * @return mixed
     */
    public function renderRuleView()
    {
        if (version_compare(_PS_VERSION_, '1.6.0', '<') === true) {
            $this->context->controller->addCSS($this->_path . 'views/css/vendor/ui.fancytree.css');
            $this->context->controller->addJs(
                [
                    _PS_JS_DIR_ . 'jquery/plugins/treeview-categories/jquery.treeview-categories.js',
                    $this->_path . 'views/js/vendor/jquery.fancytree-all.min.js',
                    _PS_JS_DIR_ . 'admin-categories-tree.js',
                    $this->_path . 'views/js/admin-tree-ps15.js',
                ]
            );
        } else {
            $this->context->controller->addJs(
                [
                    $this->_path . 'views/js/treeps16.js',
                ]
            );
        }
        $helper = new HelperView();
        $wizard_steps = [
            'name' => 'rule_wizard',
            'steps' => [
                [
                    'title' => $this->l('General'),
                ],
                [
                    'title' => $this->l('Discount'),
                ],
                [
                    'title' => $this->l('Condition'),
                ],
                [
                    'title' => $this->l('Reminder'),
                ],
            ],
        ];
        if (Tools::getValue('id_rule')) {
            $rule = new TACartReminderRule((int) Tools::getValue('id_rule'));
        } else {
            $rule = new TACartReminderRule();
        }
        $helper->module = $this;
        $helper->tpl_vars = [
            'type_render' => 'rule',
            'tacartreminder_configure_url' => $this->context->link->getAdminLink('AdminModules', false) .
                '&configure=' . $this->name . '&tab_module=' . $this->tab .
                '&module_name=' . $this->name . '&token=' . Tools::getAdminTokenLite(
                    'AdminModules'
                ),
            'wizard_steps' => $wizard_steps,
            'validate_url' => Tools::getProtocol(Tools::usingSecureMode()) . $_SERVER['HTTP_HOST'] . $this->getPathUri(),
            'wizard_contents' => [
                'contents' => [
                    0 => $this->renderRuleStep1($rule),
                    1 => $this->renderRuleStep2($rule),
                    2 => $this->renderRuleStep3($rule),
                    3 => $this->renderRuleStep4($rule),
                ],
            ],
        ];

        return $helper->generateView();
    }

    /**
     * Return the html for Display the mail form
     *
     * @return mixed
     */
    public function renderMailView()
    {
        $helper = new HelperView();
        $helper->module = $this;
        $helper->tpl_vars = [
            'type_render' => 'mail_template',
            'validate_url' => Tools::getProtocol(Tools::usingSecureMode()) . $_SERVER['HTTP_HOST'] . $this->getPathUri(),
            'content' => $this->renderMenuTop() . $this->renderMailTemplateForm((int) Tools::getValue('id_mail_template')),
        ];

        return $helper->generateView();
    }

    /**
     * For email preview use in email template form
     *
     * @param string $mode
     *
     * @return mixed
     */
    public function renderEgMailCustom($mode = 'egmail')
    {
        $cart_mails = self::getCartPreview();
        $bgpatterns = TAEgMail::getBgPatterns();
        $this->context->smarty->assign(
            [
                'cart_mails' => $cart_mails,
                'bgpatterns' => $bgpatterns,
                'ta_img_url' => $this->img_url,
                'mode' => $mode,
            ]
        );
        if ($mode == 'egmail') {
            $egmail_id = (int) Tools::getValue('egmail_id');
            if ($egmail_id) {
                $this->context->smarty->assign(
                    [
                        'cart_mails' => $cart_mails,
                        'custom' => '1',
                        'egmail' => TAEgMail::getEgMail((int) Tools::getValue('egmail_id')),
                    ]
                );
            }
        }

        return $this->context->smarty->fetch(
            $this->local_path . 'views/templates/admin/_configure/egmail-form-custom.tpl'
        );
    }

    /**
     * To test & preview email directly in the client messagery
     * Use in egmail preview
     */
    public function sendEgMailCustom()
    {
        $content_html = $this->previewEgMailCustom();
        $admin_mails = (string) Tools::getValue('emails');
        $subject = (string) Tools::getValue('subject');
        $title = (string) Tools::getValue('title');
        $id_cart = (int) Tools::getValue('id_cart');
        $cart = new Cart($id_cart);
        $customer = new Customer((int) $cart->id_customer);
        $subject = str_replace('{customer_firstname}', $customer->firstname, $subject);
        $subject = str_replace('{customer_lastname}', $customer->lastname, $subject);
        $title = str_replace('{customer_firstname}', $customer->firstname, $title);
        $title = str_replace('{customer_lastname}', $customer->lastname, $title);
        $content_html = str_replace('{message_title}', $title, $content_html);
        if (strpos($admin_mails, ',') !== false) {
            $admin_mails = explode(',', $admin_mails);
        }
        if (TACartReminderTools::FORCE_USE_STD_PRESTASHOP_FUNCTION) {
            try {
                $content_txt = TACartReminderTools::convertHtmlToText($content_html);
            } catch (Exception $e) {
                $content_txt = '';
            }
            $mailVars = [
                'content_html' => TACartReminderTools::cartCSSToInline($content_html),
                'content_txt' => $content_txt,
            ];
            $mail_sended = Mail::Send(
                (int) $cart->id_lang,
                'generic_template',
                empty($subject) ? ' Test smart cart reminder' : $subject,
                $mailVars,
                $admin_mails,
                null,
                null,
                null,
                null,
                null,
                $this->local_path . 'mails/',
                false,
                (int) $cart->id_shop
            );
        } else {
            $mail_sended = @TACartReminderTools::send(
                $cart->id_lang,
                '',
                $content_html,
                empty($subject) ? ' Test smart cart reminder' : $subject,
                [],
                $admin_mails,
                (string) $customer->firstname,
                (int) $cart->id_shop
            );
        }
        if (!$mail_sended) {
            exit('ko');
        }
        exit('ok');
    }

    /**
     * send email for test
     */
    public function sendMail()
    {
        $cart = new Cart((int) Tools::getValue('id_cart'));
        $content_html = Tools::getValue('content_html_' . $cart->id_lang);
        $content_txt = Tools::getValue('content_txt_' . $cart->id_lang);
        $title = (string) Tools::getValue('title_' . $cart->id_lang, '');
        $subject = (string) Tools::getValue('subject_' . $cart->id_lang, '');
        $admin_mails = (string) Tools::getValue('emails');
        if (strpos($admin_mails, ',') !== false) {
            $admin_mails = explode(',', $admin_mails);
        }
        $customer = new Customer((int) $cart->id_customer);
        $subject = str_replace('{customer_firstname}', $customer->firstname, $subject);
        $subject = str_replace('{customer_lastname}', $customer->lastname, $subject);
        $title = str_replace('{customer_firstname}', $customer->firstname, $title);
        $title = str_replace('{customer_lastname}', $customer->lastname, $title);
        $content_html = str_replace('{message_title}', $title, $content_html);
        $content_html = TACartReminderTools::renderMail(0, (int) $cart->id, $content_html, $title);
        if (TACartReminderTools::FORCE_USE_STD_PRESTASHOP_FUNCTION) {
            $mailVars = [
                'content_html' => TACartReminderTools::cartCSSToInline($content_html),
                'content_txt' => $content_txt,
            ];
            $mail_sended = Mail::Send(
                (int) $cart->id_lang,
                'generic_template',
                empty($subject) ? ' Test smart cart reminder' : $subject,
                $mailVars,
                $admin_mails,
                null,
                null,
                null,
                null,
                null,
                $this->local_path . 'mails/',
                false,
                (int) $cart->id_shop
            );
        } else {
            $mail_sended = @TACartReminderTools::send(
                $cart->id_lang,
                '',
                $content_html,
                empty($subject) ? ' Test smart cart reminder' : $subject,
                [],
                $admin_mails,
                (string) $customer->firstname,
                (int) $cart->id_shop
            );
        }
        if (!$mail_sended) {
            exit('ko');
        }
        exit('ok');
    }

    /**
     * Preview HTML email with template id
     *
     * @param bool|true $withinfo
     *
     * @return mixed
     */
    public function previewEgMailCustom($withinfo = true)
    {
        $egmail_id = (int) Tools::getValue('egmail_id');
        if ($egmail_id) {
            $egmail = TAEgMail::getEgMail((int) Tools::getValue('egmail_id'));
            $suggestion_custom = new TAEgMailSuggestion();
            $suggestion_egmail = $egmail->suggestions[0];
            foreach ($suggestion_egmail->variables as $variable) {
                $variable_custom = new TAEgMailVariable();
                $variable_custom->id = (string) $variable->id;
                $variable_custom->value = (string) Tools::getValue($variable->id);
                $suggestion_custom->variables[] = $variable_custom;
            }
            $suggestion_custom->bgpattern = Tools::getValue('bgpattern');
            $lang_iso = (string) Tools::getValue('lang_iso');
            $content = $egmail->getContent($lang_iso, $suggestion_custom);
            $content = TACartReminderTools::cartCSSToInline($content);
            if ($withinfo) {
                $id_cart = (int) Tools::getValue('id_cart');

                return TACartReminderTools::renderMail(0, (int) $id_cart, $content, '', '#########', true);
            } else {
                return $content;
            }
        }
    }

    /**
     * Return html content for step 1 : General in rule form
     *
     * @param $rule
     *
     * @return mixed
     */
    private function renderRuleStep1($rule)
    {
        $module_url = Tools::getProtocol(Tools::usingSecureMode()) . $_SERVER['HTTP_HOST'] . $this->getPathUri();
        $fields_form_1 = [
            'form' => [
                'legend' => [
                    'title' => $this->l('General'),
                    'icon' => 'icon-cogs',
                ],
                'input' => [
                    [
                        'type' => 'hidden',
                        'name' => 'id_rule',
                    ],
                    [
                        'type' => 'text',
                        'label' => $this->l('Rule name'),
                        'name' => 'name',
                    ],
                    [
                        'type' => (version_compare(_PS_VERSION_, '1.6.0', '<') === true ? 'radio' : 'switch'),
                        'is_bool' => true, // retro compat 1.5
                        'label' => $this->l('Status'),
                        'name' => 'status',
                        'class' => 't',
                        'values' => [
                            [
                                'id' => 'status_on',
                                'value' => 1,
                                'label' => $this->l('Enabled'),
                            ],
                            [
                                'id' => 'status_off',
                                'value' => 0,
                                'label' => $this->l('Disabled'),
                            ],
                        ],
                    ],
                ],
            ],
        ];
        if (Shop::isFeatureActive()) {
            $fields_form_1['form']['input'][] = [
                'type' => 'shop',
                'label' => $this->l('Shop association'),
                'name' => 'checkBoxShopAsso',
            ];
        }
        $tpl_vars = [
            'languages' => $this->context->controller->getLanguages(),
            'tacartreminder_ajax_url' => $module_url . 'tacartreminder_ajax_admin_call.php?token=' .
                Tools::substr(Tools::encrypt('tacartreminder/index'), 0, 10),
            'id_language' => $this->context->language->id,
        ];

        return $this->renderGenericForm(
            [
                $fields_form_1,
            ],
            $this->getRuleFieldsValues($rule->id),
            $tpl_vars
        );
    }

    /**
     * Return html content for step 2 : Voucher in Rule Form
     *
     * @param $rule
     *
     * @return mixed
     */
    private function renderRuleStep2($rule)
    {
        $module_url = Tools::getProtocol(Tools::usingSecureMode()) . $_SERVER['HTTP_HOST'] . $this->getPathUri();
        $fields_form_1 = [
            'form' => [
                'legend' => [
                    'title' => $this->l('Discount'),
                    'icon' => 'icon-tag',
                ],
                'input' => [
                    [
                        'type' => (version_compare(_PS_VERSION_, '1.6.0', '<') === true ? 'radio' : 'switch'),
                        'class' => 't',
                        'is_bool' => true, // retro compat 1.5
                        'label' => $this->l('Create discount'),
                        'name' => 'create_cart_rule',
                        'values' => [
                            [
                                'id' => 'create_cart_rule_on',
                                'value' => 1,
                                'label' => $this->l('Enabled'),
                            ],
                            [
                                'id' => 'create_cart_rule_off',
                                'value' => 0,
                                'label' => $this->l('Disabled'),
                            ],
                        ],
                    ],
                    [
                        'type' => 'discountselect',
                        'name' => 'discountselect',
                    ],
                ],
            ],
        ];
        $tpl_vars = [
            'fields_value' => $this->getRuleFieldsValues($rule->id),
            'tacartreminder_ajax_url' => $module_url . 'tacartreminder_ajax_admin_call.php?token=' .
                Tools::substr(Tools::encrypt('tacartreminder/index'), 0, 10),
            'id_language' => $this->context->language->id,
        ];

        return $this->renderGenericForm(
            [
                $fields_form_1,
            ],
            $this->getRuleFieldsValues($rule->id),
            $tpl_vars
        );
    }

    /**
     * Return html content for step 3 : Condition in Rule Form
     *
     * @param $rule
     *
     * @return mixed
     */
    private function renderRuleStep3($rule)
    {
        $module_url = Tools::getProtocol(Tools::usingSecureMode()) . $_SERVER['HTTP_HOST'] . $this->getPathUri();
        $fields_form_1 = [
            'form' => [
                'legend' => [
                    'title' => $this->l('Condition'),
                    'icon' => 'icon-magic',
                ],
                'input' => [
                    [
                        'type' => 'datetime',
                        'label' => $this->l('From'),
                        'name' => 'date_from',
                    ],
                    [
                        'type' => 'datetime',
                        'label' => $this->l('to'),
                        'name' => 'date_to',
                    ],
                    [
                        'type' => 'condition',
                        'name' => 'condition',
                        'class' => 'fixed-width-xxl',
                    ],
                ],
            ],
        ];
        if (version_compare(_PS_VERSION_, '1.6.0', '<')) {
            unset($fields_form_1['form']['input'][0]['label']);
            unset($fields_form_1['form']['input'][1]['label']);
        }
        $condition_groups = $this->getGroupConditionsDisplay($rule);
        $tpl_vars = [
            'fields_value' => $this->getRuleFieldsValues($rule->id),
            'condition_groups' => $condition_groups,
            'condition_groups_counter' => count($condition_groups),
            'languages' => $this->context->controller->getLanguages(),
            'tacartreminder_ajax_url' => $module_url . 'tacartreminder_ajax_admin_call.php?token=' .
                Tools::substr(Tools::encrypt('tacartreminder/index'), 0, 10),
            'id_language' => $this->context->language->id,
        ];

        return $this->renderGenericForm(
            [
                $fields_form_1,
            ],
            $this->getRuleFieldsValues($rule->id),
            $tpl_vars
        );
    }

    /**
     * Return html content for step 4 : Reminder in Rule Form
     *
     * @param $rule
     *
     * @return mixed
     */
    private function renderRuleStep4($rule)
    {
        $module_url = Tools::getProtocol(Tools::usingSecureMode()) . $_SERVER['HTTP_HOST'] . $this->getPathUri();
        $fields_form_1 = [
            'form' => [
                'id_form' => 'step_rule_reminder',
                'legend' => [
                    'title' => $this->l('Reminder'),
                    'icon' => 'icon-time',
                ],
                'input' => [
                    [
                        'type' => 'reminder',
                        'name' => 'reminder',
                    ],
                    [
                        'type' => 'force_reminder',
                        'name' => 'force_reminder',
                    ],
                ],
            ],
        ];
        $reminders = $this->getRemindersDisplay($rule);
        $tpl_vars = [
            'fields_value' => $this->getRuleFieldsValues($rule->id),
            'reminders' => $reminders,
            'reminders_counter' => count($reminders),
            'languages' => $this->context->controller->getLanguages(),
            'tacartreminder_ajax_url' => $module_url . 'tacartreminder_ajax_admin_call.php?token=' .
                Tools::substr(Tools::encrypt('tacartreminder/index'), 0, 10),
            'id_language' => $this->context->language->id,
        ];

        return $this->renderGenericForm(
            [
                $fields_form_1,
            ],
            $this->getRuleFieldsValues($rule->id),
            $tpl_vars
        );
    }

    /**
     * Return html content for config form
     *
     * @param $rule
     *
     * @return mixed
     */
    public function renderConfigForm()
    {
        $module_url = Tools::getProtocol(Tools::usingSecureMode()) . $_SERVER['HTTP_HOST'] . $this->getPathUri();
        $fields_form = [];
        $fields_form[0]['form'] = [
            'legend' => [
                'title' => $this->l('Configuration'),
                'icon' => 'icon-cogs',
            ],
            'input' => [
                [
                    'type' => 'text',
                    'label' => $this->l('Abandoned cart time-lapse'),
                    'name' => 'TA_CARTR_ABANDONNED_NB_HOUR',
                    'class' => 'fixed-width-xs',
                    'suffix' => $this->l('Hour'),
                    'desc' => $this->l('The cart will be considered abandoned after this many hours (8 hours is a recommended value)'),
                ],
                [
                    'type' => 'text',
                    'label' => $this->l('Time-lapse for cancel or do not remind'),
                    'name' => 'TA_CARTR_STOPREMINDER_NB_HOUR',
                    'class' => 'fixed-width-xs',
                    'suffix' => $this->l('Hour'),
                    'desc' => $this->l('Do not remind a cart if the last cart update exceeds this time, or if reminders exist and have not yet been executed for a cart. After that time it will be canceled; 96 hours is a recommended value'),
                ],
                [
                    'type' => 'text',
                    'label' => $this->l('Tim-lapse after last cart reminder'),
                    'name' => 'TA_CARTR_AFTERREMINDER_NB_DAY',
                    'class' => 'fixed-width-xs',
                    'suffix' => $this->l('Day'),
                    'desc' => $this->l('If the customer has already received a cart reminder, it is the number of days after which he/she can receive a new cart reminder.'),
                ],
                [
                    'type' => 'text',
                    'label' => $this->l('Time-lapse after which to delete expired coupons'),
                    'name' => 'TA_CARTR_CLEANCARTRULE_NB_DAY',
                    'class' => 'fixed-width-xs',
                    'suffix' => $this->l('Day'),
                    'desc' => $this->l('Delete expired cart rules and not used after n days'),
                ],
                [
                    'type' => 'text',
                    'label' => $this->l('Coupon prefix'),
                    'name' => 'TA_CARTR_CR_PREFIX',
                    'desc' => $this->l('Prefix used for coupon code (e.g. CREMINDER-****************)'),
                ],
                [
                    'type' => 'text',
                    'label' => $this->l('Code format'),
                    'name' => 'TA_CARTR_CODE_FORMAT',
                    'desc' => $this->l('Use L for a Letter, N for a Number (e.g. LLLNLNNNLLL)'),
                    'size' => 50,
                ],
                [
                    'type' => (version_compare(_PS_VERSION_, '1.6.0', '<') === true ? 'radio' : 'switch'),
                    'is_bool' => true, // retro compat 1.5
                    'label' => $this->l('Auto add coupon'),
                    'name' => 'TA_CARTR_AUTO_ADD_CR',
                    'class' => 't',
                    'desc' => $this->l('Add the coupon to the shopping cart when the customer click on the link to complete the order.'),
                    'values' => [
                        [
                            'id' => 'TA_CARTR_AUTO_ADD_CR_ON',
                            'value' => 1,
                            'label' => $this->l('Yes'),
                        ],
                        [
                            'id' => 'TA_CARTR_AUTO_ADD_CR_OFF',
                            'value' => 0,
                            'label' => $this->l('No'),
                        ],
                    ],
                ],
                [
                    'type' => (version_compare(_PS_VERSION_, '1.6.0', '<') === true ? 'radio' : 'switch'),
                    'is_bool' => true, // retro compat 1.5
                    'label' => $this->l('Shop logo'),
                    'name' => 'TA_CARTR_SHOPLOGO',
                    'class' => 't',
                    'desc' => $this->l('Indicate here if you want attach the logo in email send to your customer'),
                    'values' => [
                        [
                            'id' => 'TA_CARTR_SHOPLOGO_ON',
                            'value' => 1,
                            'label' => $this->l('Yes'),
                        ],
                        [
                            'id' => 'TA_CARTR_SHOPLOGO_OFF',
                            'value' => 0,
                            'label' => $this->l('No'),
                        ],
                    ],
                ],
                [
                    'type' => (version_compare(_PS_VERSION_, '1.6.0', '<') === true ? 'radio' : 'switch'),
                    'is_bool' => true, // retro compat 1.5
                    'label' => $this->l('Debug mode'),
                    'name' => 'TA_CARTR_DEBUG',
                    'class' => 't',
                    'desc' => $this->l('Only to be used to analyze (developer support)'),
                    'values' => [
                        [
                            'id' => 'TA_CARTR_DEBUG_ON',
                            'value' => 1,
                            'label' => $this->l('Yes'),
                        ],
                        [
                            'id' => 'TA_CARTR_DEBUG_OFF',
                            'value' => 0,
                            'label' => $this->l('No'),
                        ],
                    ],
                ],
            ],
            'submit' => [
                'title' => $this->l('Save'),
                'class' => 'btn btn-default pull-right',
                'name' => 'submitSettings',
            ],
        ];
        $helper = new HelperForm();
        $helper->show_toolbar = false;
        $helper->table = $this->name;
        $lang = new Language((int) Configuration::get('PS_LANG_DEFAULT'));
        $helper->default_form_language = $lang->id;
        $helper->module = $this;
        $helper->allow_employee_form_lang =
            Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ?
                Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') : 0;
        $helper->identifier = $this->identifier;
        $helper->submit_action = 'submitTACartReminderConfiguration';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false) .
            '&configure=' . $this->name . '&tab_module=' . $this->tab . '&module_name=' . $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->tpl_vars = [
            'fields_value' => $this->getConfigFieldsValues(),
            'tacartreminder_ajax_url' => $module_url . 'tacartreminder_ajax_admin_call.php?token=' .
                Tools::substr(Tools::encrypt('tacartreminder/index'), 0, 10),
            'currencies' => Currency::getCurrencies(),
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
        ];

        return $helper->generateForm($fields_form);
    }

    /**
     * Render html content for cron job setting
     *
     * @return mixed
     */
    public function renderCronJobView()
    {
        $cronjobs = Module::getInstanceByName('cronjobs');
        $cronjobs_installed = false;
        // Cloud url test Ok
        $base_link = Tools::getShopDomainSsl(true);
        $cron_url = $base_link .
            '/modules/tacartreminder/cron.php?token=' . Configuration::get('TA_CARTR_TOKEN');
        if ($cronjobs && $cronjobs->active) {
            $cronjobs_installed = true;
        }
        $cron_url =
            Context::getContext()->link->getModuleLink(
                'tacartreminder',
                'cron',
                ['token' => Configuration::get('TA_CARTR_TOKEN')],
                true
            );
        $this->context->smarty->assign(
            [
                'cronjobs_installed' => $cronjobs_installed,
                'cron_url' => $cron_url,
                'module_cronjobs_url' => $this->context->link->getAdminLink('AdminModules', false) .
                    '&configure=cronjobs&module_name=cronjobs&token=' . Tools::getAdminTokenLite('AdminModules'),
                'admin_module_url' => $this->context->link->getAdminLink('AdminModules', false) .
                    '&token=' . Tools::getAdminTokenLite('AdminModules'),
            ]
        );

        return $this->context->smarty->fetch(
            $this->local_path . 'views/templates/admin/_configure/cronjob-configure.tpl'
        );
    }

    public function renderSuppervisingView()
    {
        $count_s_message = (int) TACartReminderCleans::countJournalMessageSystem();
        $count_s_message_cart_expirate = (int) TACartReminderCleans::countJournalMessageSystem('cart_expirate');
        $this->context->smarty->assign(
            [
                'total_journal_system_message' => $count_s_message,
                'total_journal_system_message_expirate' => $count_s_message_cart_expirate,
                'admin_module_url' => $this->context->link->getAdminLink('AdminModules', false) .
                    '&token=' . Tools::getAdminTokenLite('AdminModules'),
            ]
        );

        return $this->context->smarty->fetch(
            $this->local_path . 'views/templates/admin/_configure/supervising.tpl'
        );
    }

    /**
     * Get all configuration
     *
     * @return array
     */
    public function getConfigFieldsValues()
    {
        return [
            'TA_CARTR_ABANDONNED_NB_HOUR' => Tools::getValue(
                'TA_CARTR_ABANDONNED_NB_HOUR',
                Configuration::get('TA_CARTR_ABANDONNED_NB_HOUR')
            ),
            'TA_CARTR_STOPREMINDER_NB_HOUR' => Tools::getValue(
                'TA_CARTR_STOPREMINDER_NB_HOUR',
                Configuration::get('TA_CARTR_STOPREMINDER_NB_HOUR')
            ),
            'TA_CARTR_CR_PREFIX' => Tools::getValue('TA_CARTR_CR_PREFIX', Configuration::get('TA_CARTR_CR_PREFIX')),
            'TA_CARTR_CODE_FORMAT' => Tools::getValue(
                'TA_CARTR_CODE_FORMAT',
                Configuration::get('TA_CARTR_CODE_FORMAT')
            ),
            'TA_CARTR_AUTO_ADD_CR' => (int) Tools::getValue(
                'TA_CARTR_AUTO_ADD_CR',
                Configuration::get('TA_CARTR_AUTO_ADD_CR')
            ),
            'TA_CARTR_AFTERREMINDER_NB_DAY' => Tools::getValue(
                'TA_CARTR_AFTERREMINDER_NB_DAY',
                Configuration::get('TA_CARTR_AFTERREMINDER_NB_DAY')
            ),
            'TA_CARTR_CLEANCARTRULE_NB_DAY' => Tools::getValue(
                'TA_CARTR_CLEANCARTRULE_NB_DAY',
                Configuration::get('TA_CARTR_CLEANCARTRULE_NB_DAY')
            ),
            'TA_CARTR_DEBUG' => Tools::getValue(
                'TA_CARTR_DEBUG',
                Configuration::get('TA_CARTR_DEBUG')
            ),
            'TA_CARTR_SHOPLOGO' => Tools::getValue(
                'TA_CARTR_SHOPLOGO',
                Configuration::get('TA_CARTR_SHOPLOGO')
            ),
        ];
    }

    /**
     * @param $fields_form
     * @param $fields_value
     * @param array $tpl_vars
     *
     * @return mixed
     */
    public function renderGenericForm($fields_form, $fields_value, $tpl_vars = [])
    {
        $helper = new HelperForm();
        $helper->show_toolbar = false;
        $helper->default_form_language = $this->context->language->id;
        $helper->module = $this;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ?
            Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') : 0;
        $this->fields_form = [];
        $helper->id = (int) Tools::getValue('id_rule');
        $helper->table = 'ta_cartreminder_rule';
        $helper->identifier = 'id_rule';
        $helper->submit_action = 'submitEditRule';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false) .
            '&configure=' . $this->name . '&tab_module=' . $this->tab . '&module_name=' . $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->tpl_vars = array_merge(
            [
                'fields_value' => $fields_value,
                'ta_img_url' => $this->img_url,
                'languages' => $this->context->controller->getLanguages(),
                'id_language' => $this->context->language->id,
            ],
            $tpl_vars
        );

        return $helper->generateForm($fields_form);
    }

    /**
     * Return html content to display rule list
     *
     * @return mixed
     */
    public function renderRuleList()
    {
        $table = 'ta_cartreminder_rule';
        $fields_list = [
            'id_rule' => [
                'title' => $this->l('ID'),
                'type' => 'text',
                'class' => 'fixed-width-xs',
            ],
            'position' => [
                'title' => $this->l('Position'),
                'filter_key' => 'a!position',
                'position' => 'position',
                'align' => 'center',
                'class' => 'fixed-width-xs',
            ],
            'name' => [
                'title' => $this->l('Name'),
                'type' => 'text',
            ],
            'date_from' => [
                'title' => $this->l('Date From'),
                'type' => 'datetime',
            ],
            'date_to' => [
                'title' => $this->l('Date To'),
                'type' => 'datetime',
            ],
            'create_cart_rule' => [
                'title' => $this->l('Coupon'),
                'type' => 'bool',
            ],
            'status' => [
                'title' => $this->l('Status'),
                'active' => 'status',
                'type' => 'bool',
            ],
        ];
        $rules = [];
        if (Tools::isSubmit('submitResetta_cartreminder_rule')) {
            self::processResetFilters($table, $fields_list);
            $rules = TACartReminderRule::getRules(false);
        } else {
            $this->setOrdersAndFilters('ta_cartreminder_rule', $fields_list);
            $rules = TACartReminderRule::getRules(
                false,
                $this->orders_filters[$table]['order_by'],
                $this->orders_filters[$table]['order_way'],
                $this->orders_filters[$table]['filters']
            );
        }
        $helper = new HelperList();
        $helper->position_identifier = 'id_rule';
        $helper->shopLinkType = '';
        $helper->listTotal = count($rules);
        $helper->simple_header = false;
        $helper->actions = [
            'edit',
            'delete',
        ];
        $helper->orderBy = (!empty($this->orders_filters[$table]['order_by']) ?
            $this->orders_filters[$table]['order_by'] : 'position');
        $helper->orderWay = (!empty($this->orders_filters[$table]['order_way']) ? Tools::strtoupper(
            $this->orders_filters[$table]['order_way']
        ) : 'ASC');
        $helper->show_toolbar = true;
        $helper->toolbar_btn['new'] = [
            'href' => $this->context->link->getAdminLink('AdminModules') .
                '&configure=' . $this->name . '&module_name=' . $this->name . '&updateta_cartreminder_rule&tab_configure=rule',
            'desc' => $this->l('Add New Rule', null, null, false),
        ];
        $helper->tpl_vars['ta_table_list'] = $table;
        $helper->module = $this;
        $helper->identifier = 'id_rule';
        $helper->title = $this->l('Rules');
        $helper->table = $table;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->currentIndex = AdminController::$currentIndex . '&configure=' . $this->name . '&tab_configure=rule';
        $helper->tpl_vars['current'] = $helper->currentIndex;

        return $helper->generateList($rules, $fields_list);
    }

    /**
     * Return html content to display all email template saved
     *
     * @return mixed
     */
    public function renderMailTemplateList()
    {
        $table = 'ta_cartreminder_mail_template';
        $fields_list = [
            'id_mail_template' => [
                'title' => $this->l('ID'),
                'type' => 'text',
            ],
            'name' => [
                'title' => $this->l('Name'),
                'type' => 'text',
            ],
            'subject' => [
                'title' => $this->l('Subject'),
                'type' => 'text',
            ],
            'date_add' => [
                'title' => $this->l('Date'),
                'type' => 'datetime',
                'filter' => false,
                'search' => false,
            ],
        ];
        $mails = [];
        if (Tools::isSubmit('submitReset' . $table)) {
            self::processResetFilters($table, $fields_list);
            $mails = TACartReminderMailTemplate::getMailTemplates($this->context->language->id);
        } else {
            $this->setOrdersAndFilters($table, $fields_list);
            $mails = TACartReminderMailTemplate::getMailTemplates(
                $this->context->language->id,
                $this->orders_filters[$table]['order_by'],
                $this->orders_filters[$table]['order_way'],
                $this->orders_filters[$table]['filters']
            );
        }
        $helper = new HelperList();
        $helper->shopLinkType = '';
        $helper->listTotal = count($mails);
        $helper->simple_header = false;
        $helper->actions = [
            'edit',
            'delete',
        ];
        $helper->orderBy = (!empty($this->orders_filters[$table]['order_by']) ?
            $this->orders_filters[$table]['order_by'] : 'position');
        $helper->orderWay = (!empty($this->orders_filters[$table]['order_way']) ? Tools::strtoupper(
            $this->orders_filters[$table]['order_way']
        ) : 'ASC');
        $helper->show_toolbar = true;
        $helper->toolbar_btn['new'] = [
            'href' => $this->context->link->getAdminLink('AdminModules') . '&configure=' . $this->name .
                '&module_name=' . $this->name . '&updateta_cartreminder_mail_template',
            'desc' => $this->l('Add New Email', null, null, false),
        ];
        $helper->module = $this;
        $helper->identifier = 'id_mail_template';
        $helper->title = $this->l('Email templates');
        $helper->tpl_vars['ta_table_list'] = $table;
        $helper->table = $table;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->currentIndex = AdminController::$currentIndex . '&configure=' . $this->name;
        $helper->tpl_vars['current'] = $helper->currentIndex;

        return $helper->generateList($mails, $fields_list);
    }

    /**
     * Keep and parse the rule fields for display in rule form
     *
     * @param $id_rule
     *
     * @return array
     */
    public function getRuleFieldsValues($id_rule)
    {
        $rule = new TACartReminderRule((int) $id_rule);
        $cart_rule_filter = '';
        if ($rule->id && $rule->id_cart_rule) {
            $cart_rule = new CartRule((int) $rule->id_cart_rule, $this->context->language->id);
            $cart_rule_filter = $cart_rule->id . ' ' . $cart_rule->name;
        }

        return [
            'name' => $rule->name,
            'date_from' => $rule->date_from,
            'date_to' => $rule->date_to,
            'create_cart_rule' => $rule->create_cart_rule,
            'id_cart_rule' => $rule->id_cart_rule,
            'cart_rule_nbday_validity' => $rule->cart_rule_nbday_validity,
            'cart_rule_filter' => $cart_rule_filter,
            'force_reminder' => $rule->force_reminder,
            'status' => $rule->status,
            'id_rule' => $rule->id,
        ];
    }

    /**
     * List all cart for email form
     * User select a cart to preview email with this cart
     *
     * @return mixed
     */
    public static function getCartPreview()
    {
        $cart_mails = Db::getInstance()->executeS(
            '
				SELECT c.date_add,c.id_cart,cu.firstname,cu.lastname,l.iso_code,c.id_lang
				FROM ' . _DB_PREFIX_ . 'cart c
				INNER JOIN ' . _DB_PREFIX_ . 'customer cu on cu.id_customer = c.id_customer
				INNER JOIN ' . _DB_PREFIX_ . 'lang l on l.id_lang = c.id_lang
				WHERE id_cart not in (SELECT id_cart from ' . _DB_PREFIX_ . 'orders)
				GROUP BY c.`id_customer`
				ORDER BY c.`date_upd` DESC
				LIMIT 30'
        );

        return $cart_mails;
    }

    /**
     * Return html content for display email template form
     *
     * @param int $id_mail_template
     *
     * @return mixed
     */
    public function renderMailTemplateForm($id_mail_template = 0)
    {
        $this->context->controller->addjQueryPlugin(
            [
                'ajaxfileupload',
            ]
        );
        $this->context->controller->addJS($this->_path . 'views/js/vendor/bootstrap-colorpicker.min.js');
        $this->context->controller->addCSS($this->_path . 'views/css/vendor/bootstrap-colorpicker.min.css');
        $module_url = Tools::getProtocol(Tools::usingSecureMode()) . $_SERVER['HTTP_HOST'] . $this->getPathUri();
        $cart_mails = self::getCartPreview();
        $fields_form_1 = [
            'form' => [
                'legend' => [
                    'title' => $this->l('Add new'),
                    'icon' => 'icon-envelope',
                ],
                'input' => [
                    [
                        'type' => 'hidden',
                        'name' => 'id_mail_template',
                    ],
                    [
                        'type' => 'text',
                        'label' => $this->l('Template name'),
                        'name' => 'name',
                    ],
                    [
                        'type' => 'text',
                        'lang' => true,
                        'label' => $this->l('Subject'),
                        'name' => 'subject',
                    ],
                    [
                        'type' => 'text',
                        'lang' => true,
                        'label' => $this->l('Title'),
                        'name' => 'title',
                    ],
                    [
                        'type' => 'example_mails',
                        'name' => 'example_mails',
                    ],
                    [ // important is 3 because remove autoload_rte
                        'type' => 'textarea',
                        'label' => $this->l('Email html'),
                        'name' => 'content_html',
                        'class' => 'rte autoload_rte',
                        'autoload_rte' => true,
                        'lang' => true,
                        'rows' => 5,
                        'cols' => 40,
                        'hint' => $this->l('Invalid characters:') . ' <>;=#',
                        'desc' => $this->l('Below are the variables you can include in your email.') . '<br/>' .
                            $this->l('The following variables will be replaced by the customer and cart information.') .
                            '<br/><b>{customer_firstname}</b> : ' . $this->l('customer first name') .
                            '<br/><b>{customer_lastname}</b> : ' . $this->l('customer last name') .
                            '<br/><b>{cart_products}</b> : ' . $this->l('customer cart contents') .
                            '<br/><b>{cart_products_txt}</b> : ' . $this->l('customer cart contents - formatted txt') .
                            '<br/><b>{shop_link_start} {shop_link_end}</b> : ' . $this->l(
                                'this is the link to your store'
                            ) .
                            $this->l('i.e : Click') . ' {shop_link_start}' .
                            $this->l('here') . '{shop_link_end} ' . $this->l('to access our shop') .
                            '<br/><b>{shop_link_url}</b> : ' . $this->l('this is the URL to your store') .
                            $this->l('i.e : Click') . ' &lt;a href="{shop_link_url}"&gt;' .
                            $this->l('here') . '&lt;/a&gt; ' . $this->l('to access our shop') .
                            '<br/><b>{cart_link_start} {cart_link_end}</b> : ' .
                            $this->l('Link to your store to complete the order') .
                            '' . $this->l('i.e : Click') . ' {cart_link_start}' .
                            $this->l('here') . '{cart_link_end}' . $this->l('to complete your order') .
                            '<br/><b>{cart_url}</b> : ' . $this->l('URL to your store to complete the order (step 3)') .
                            '<br/><b>{cart_url_s1}</b> : ' . $this->l('URL to your store to complete the order (step 1)') .
                            '<br/><b>{cart_url_s2}</b> : ' . $this->l('URL to your store to complete the order (step 2)') .
                            '<br/><b>{unscribe_link_start} {unscribe_link_end}</b> :' .
                            $this->l('i.e : Click ') . '{unscribe_link_start}' . $this->l('here') .
                            '{unscribe_link_end} ' . $this->l('to receive no further reminders') .
                            '<br/><b>{unscribe_url}</b> : ' . $this->l('Unsubscribe URL') .
                            '<br/><b>{voucher_code}</b> : ' .
                            $this->l('Coupon code (only if the rule create a coupon)') . '' .
                            '<br/><b>{voucher_expirate_date}</b> : ' . $this->l('Coupon expiry date') . '<br/>',
                    ],
                    [
                        'type' => 'convert_html_to_txt',
                        'name' => 'convert_html_to_txt',
                    ],
                    [
                        'type' => 'textarea',
                        'label' => $this->l('Email txt'),
                        'name' => 'content_txt',
                        'lang' => true,
                        'rows' => 16,
                        'cols' => 100,
                        'hint' => $this->l('Invalid characters:') . ' <>;=#{}',
                    ],
                    [
                        'type' => 'preview_mail',
                        'name' => 'preview_mail',
                    ],
                ],
                'submit' => [
                    'title' => $this->l('Save'),
                    'class' => 'btn btn-default pull-right',
                    'name' => 'submitMailTemplate',
                ],
            ],
        ];
        if (Shop::isFeatureActive()) {
            $fields_form_1['form']['input'][] = [
                'type' => 'shop',
                'label' => $this->l('Shop association'),
                'name' => 'checkBoxShopAsso',
            ];
        }
        $helper = new HelperForm();
        $helper->show_toolbar = false;
        $helper->table = 'ta_cartreminder_mail_template';
        $lang = new Language((int) Configuration::get('PS_LANG_DEFAULT'));
        $helper->default_form_language = $lang->id;
        $helper->module = $this;
        $helper->id = $id_mail_template;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ?
            Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') : 0;
        $helper->identifier = 'id_mail_template';
        $helper->submit_action = 'submitEditMailTemplate';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false) .
            '&configure=' . $this->name . '&tab_module=' . $this->tab . '&module_name=' . $this->name . '&tab_configure=mail';
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->tpl_vars = [
            'type_render' => 'mail_template',
            'ps_version' => _PS_VERSION_,
            'egmails' => TAEgMail::getEgMails(),
            'cart_mails' => $cart_mails,
            'ta_img_url' => $this->img_url,
            'fields_value' => $this->getMailTemplateFieldsValues($id_mail_template),
            'tacartreminder_ajax_url' => $module_url . 'tacartreminder_ajax_admin_call.php?token=' .
                Tools::substr(Tools::encrypt('tacartreminder/index'), 0, 10),
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
        ];

        return $helper->generateForm(
            [
                $fields_form_1,
            ]
        );
    }

    /**
     * Keep & parse all field in array use for mail template form
     *
     * @param $id_mail_template
     *
     * @return array
     */
    public function getMailTemplateFieldsValues($id_mail_template)
    {
        $mail_template = new TACartReminderMailTemplate((int) $id_mail_template);
        $languages = Language::getLanguages(true);
        $subject = [];
        $title = [];
        $content_html = [];
        $content_txt = [];
        if ($this->form_submit == 'mail_template') {
            foreach ($languages as $key => $value) {
                $subject[$value['id_lang']] = Tools::getValue('subject_' . $value['id_lang']);
                $title[$value['id_lang']] = Tools::getValue('title_' . $value['id_lang']);
                $content_html[$value['id_lang']] = Tools::getValue('content_html_' . $value['id_lang']);
                $content_txt[$value['id_lang']] = Tools::getValue('content_txt_' . $value['id_lang']);
            }
        } elseif (Validate::isLoadedObject($mail_template)) {
            $subject = $mail_template->subject;
            $content_txt = $mail_template->content_txt;
            $content_html = $mail_template->content_html;
            $title = $mail_template->title;
        } else {
            foreach ($languages as $key => $value) {
                $subject[$value['id_lang']] = '';
                $title[$value['id_lang']] = '';
                $content_html[$value['id_lang']] = '';
                $content_txt[$value['id_lang']] = '';
            }
        }

        return [
            'name' => Tools::getValue('name', $mail_template->name),
            'subject' => $subject,
            'content_html' => $content_html,
            'content_txt' => $content_txt,
            'title' => $title,
            'id_mail_template' => $mail_template->id,
        ];
    }

    /**
     * @param $table
     * @param $id_object
     * @param $identifier
     */
    public function updateAssoShop($table, $id_object, $identifier)
    {
        if (!Shop::isFeatureActive()) {
            return;
        }
        $assos_data = $this->getSelectedAssoShop($table, $identifier, $id_object);
        // Get list of shop id we want to exclude from asso deletion
        $exclude_ids = $assos_data;
        foreach (Db::getInstance()->executeS('SELECT id_shop FROM ' . _DB_PREFIX_ . 'shop') as $row) {
            if (!$this->context->employee->hasAuthOnShop($row['id_shop'])) {
                $exclude_ids[] = $row['id_shop'];
            }
        }
        Db::getInstance()->delete(
            $table . '_shop',
            '`' . $identifier . '` = ' . (int) $id_object .
            ($exclude_ids ? ' AND id_shop NOT IN (' . implode(', ', $exclude_ids) . ')' : '')
        );
        $insert = [];
        foreach ($assos_data as $id_shop) {
            $insert[] = [
                $identifier => $id_object,
                'id_shop' => (int) $id_shop,
            ];
        }

        return Db::getInstance()->insert($table . '_shop', $insert, false, true, Db::INSERT_IGNORE);
    }

    /**
     * @param $table
     *
     * @return array
     */
    public function getSelectedAssoShop($table)
    {
        if (!Shop::isFeatureActive()) {
            return [];
        }
        $shops = Shop::getShops(true, null, true);
        if (count($shops) == 1 && isset($shops[0])) {
            return [
                $shops[0],
                'shop',
            ];
        }
        $assos = [];
        if (Tools::isSubmit('checkBoxShopAsso_' . $table)) {
            foreach (Tools::getValue('checkBoxShopAsso_' . $table) as $id_shop => $value) {
                $assos[] = (int) $id_shop;
            }
        } else {
            if (Shop::getTotalShops(false) == 1) {
                $assos[] = (int) Shop::getContextShopID();
            }
        }

        return $assos;
    }

    /**
     * this function is use for all ajax admin call
     * the call security with token the token is checked before
     * this function will be deprecated in 1.1.0
     */
    public function ajaxAdminCall()
    {
        if (Tools::isSubmit('cart_rule_filter')) {
            $search_query = trim(Tools::getValue('q'));
            $cart_rules = Db::getInstance()->executeS(
                '
			SELECT c.`id_cart_rule`, cl.`name`, c.`code`
			FROM `' . _DB_PREFIX_ . 'cart_rule` c
			INNER JOIN `' . _DB_PREFIX_ . 'cart_rule_lang` cl
			ON cl.id_cart_rule = c.id_cart_rule AND cl.id_lang = ' . $this->context->language->id . '
			WHERE c.`id_cart_rule` = ' . (int) $search_query . '
					OR cl.`name` LIKE "%' . pSQL($search_query) . '%"
					OR c.`code` LIKE "%' . pSQL($search_query) . '%"
			ORDER BY cl.`name` ASC
			LIMIT 50'
            );
            exit(json_encode($cart_rules));
        }
        if (Tools::isSubmit('newGroupCondition') && $condition_group_id = Tools::getValue('condition_group_id')) {
            exit($this->getGroupConditionDisplay($condition_group_id, null));
        }
        if (Tools::isSubmit('newCondition')) {
            exit($this->getConditionDisplay(
                Tools::getValue('condition_group_id'),
                Tools::getValue('condition_id'),
                Tools::getValue('condition_type')
            ));
        }
        if (Tools::isSubmit('newReminder')) {
            exit($this->getReminderDisplay(0, Tools::getValue('reminder_position'), 0, 0, 0, '', true));
        }
    }

    /**
     * Search a product in list
     *
     * @param $search
     * @param $condition_type
     *
     * @return array
     */
    public function searchItemList($search, $condition_type)
    {
        switch ($condition_type) {
            case 'cart_product':
                $results = Db::getInstance()->executeS(
                    '
				SELECT DISTINCT name, p.id_product as id, p.reference
				FROM ' . _DB_PREFIX_ . 'product p
				LEFT JOIN `' . _DB_PREFIX_ . 'product_lang` pl
					ON (p.`id_product` = pl.`id_product`
					AND pl.`id_lang` = ' . (int) Context::getContext()->language->id . Shop::addSqlRestrictionOnLang('pl') . ')
				' . Shop::addSqlAssociation('product', 'p') . '
				LEFT JOIN `' . _DB_PREFIX_ . 'product_attribute` pa
					ON (p.`id_product` = pa.`id_product`)
				WHERE ' . (Validate::isInt($search) ? 'p.id_product = ' . $search . ' OR ' : '') .
                    ' pl.`name` like \'%' . $search . '%\'
						OR p.`reference` like \'%' . $search . '%\' OR pa.`reference` like \'%' . $search . '%\'
						OR p.`ean13` like \'%' . $search . '%\' OR pa.`ean13` like \'%' . $search . '%\'
				GROUP BY p.id_product
				ORDER BY name
				LIMIT 0,150'
                );

                return $results;
            default:
                return [];
        }
    }

    /**
     * Return reminders array to display in rule
     *
     * @param $rule
     *
     * @return array
     */
    public function getRemindersDisplay($rule)
    {
        $reminders_array = [];
        if (is_array($reminder_positions = Tools::getValue('reminder_positions'))) {
            foreach ($reminder_positions as $reminder_position) {
                $delete_avalable = (count($reminder_positions) == (int) $reminder_position ? true : false);
                $reminders_array[] = $this->getReminderDisplay(
                    Tools::getValue('reminder_' . $reminder_position . '_id'),
                    Tools::getValue('reminder_' . $reminder_position . '_position'),
                    Tools::getValue('reminder_' . $reminder_position . '_id_mail_template', 0),
                    Tools::getValue('reminder_' . $reminder_position . '_nb_hour'),
                    Tools::getValue('reminder_' . $reminder_position . '_manual_process', 0),
                    Tools::getValue('reminder_' . $reminder_position . '_admin_mails', 0),
                    $delete_avalable
                );
            }
        } else {
            $reminders = $rule->getReminders();
            foreach ($reminders as $reminder) {
                $delete_avalable = (count($reminders) == (int) $reminder['position'] ? true : false);
                $reminders_array[] = $this->getReminderDisplay(
                    $reminder['id_reminder'],
                    $reminder['position'],
                    $reminder['id_mail_template'],
                    $reminder['nb_hour'],
                    (int) $reminder['manual_process'],
                    (string) $reminder['admin_mails'],
                    $delete_avalable
                );
            }
        }

        return $reminders_array;
    }

    /**
     * Return reminder in html
     *
     * @param int $id
     * @param int $position
     * @param int $id_mail_template
     * @param int $nb_hour
     * @param int $manual_process
     * @param string $admin_mails
     * @param bool|false $delete_avalable
     *
     * @return mixed
     */
    public function getReminderDisplay(
        $id = 0,
        $position = 0,
        $id_mail_template = 0,
        $nb_hour = 0,
        $manual_process = 0,
        $admin_mails = '',
        $delete_avalable = false
    ) {
        Context::getContext()->smarty->assign(
            [
                'id_reminder' => (int) $id,
                'position' => (int) $position,
                'manual_process' => (int) $manual_process,
                'admin_mails' => (string) $admin_mails,
                'id_mail_template' => $id_mail_template,
                'nb_hour' => $nb_hour,
                'mail_templates' => TACartReminderMailTemplate::getMailTemplates($this->context->language->id),
                'delete_avalable' => $delete_avalable,
            ]
        );
        if (version_compare(_PS_VERSION_, '1.6.0', '>=') === true) {
            return $this->context->smarty->fetch(
                $this->local_path . 'views/templates/admin/_configure/reminder_line.tpl'
            );
        } else {
            return $this->context->smarty->fetch(
                $this->local_path . 'views/templates/admin/_configure/reminder_line.tpl'
            );
        }
    }

    /**
     * Return a array of group conditions to display in rule form
     *
     * @param $rule
     *
     * @return array
     */
    public function getGroupConditionsDisplay($rule)
    {
        $groups_array = [];
        if (is_array($array = Tools::getValue('condition_group')) && count($array)) {
            $i = 1;
            foreach ($array as $group_id) {
                $conditions_array = [];
                if (is_array($array = Tools::getValue('condition_' . $group_id)) && count($array)) {
                    foreach ($array as $rule_id) {
                        $conditions_array[] = $this->getGroupConditionDisplay(
                            $group_id,
                            $rule_id,
                            Tools::getValue('condition_' . $group_id . '_' . $rule_id . '_type'),
                            Tools::getValue('condition_select_' . $group_id . '_' . $rule_id),
                            Tools::getValue('condition_' . $group_id . '_' . $rule_id . '_sign'),
                            Tools::getValue('condition_' . $group_id . '_' . $rule_id . '_value'),
                            Tools::getValue('condition_' . $group_id . '_' . $rule_id . '_typevalue')
                        );
                    }
                }
                $groups_array[] = $this->getGroupConditionDisplay($i++, $conditions_array);
            }
        } else {
            $i = 1;
            foreach ($rule->getGroupConditions() as $group) {
                $j = 1;
                $conditions_display = [];
                foreach ($group['conditions'] as $id_condition => $condition) {
                    $conditions_display[] = $this->getConditionDisplay(
                        $i,
                        $j++,
                        $condition['type'],
                        $condition['values'],
                        $condition['sign'],
                        $condition['value'],
                        $condition['typevalue']
                    );
                }
                $groups_array[] = $this->getGroupConditionDisplay($i++, $conditions_display);
            }
        }

        return $groups_array;
    }

    /**
     * Return the form for a single cart rule group either with or without product_rules set up
     *
     * @param $group_id
     * @param null $conditions
     *
     * @return mixed
     */
    public function getGroupConditionDisplay($group_id, $conditions = null)
    {
        $this->context->smarty->assign('condition_group_id', $group_id);
        $this->context->smarty->assign('conditions', $conditions ?? []);
        if (version_compare(_PS_VERSION_, '1.6.0', '>=') === true) {
            return $this->context->smarty->fetch(
                $this->local_path . 'views/templates/admin/_configure/condition_group.tpl'
            );
        } else {
            return $this->context->smarty->fetch(
                $this->local_path . 'views/templates/admin/_configure/condition_group.tpl'
            );
        }
    }

    /**
     * Display the condition in html
     *
     * @param $group_id
     * @param $condition_id
     * @param $condition_type
     * @param array $selected
     * @param string $condition_sign
     * @param string $condition_value
     * @param string $condition_typevalue
     *
     * @return mixed
     */
    public function getConditionDisplay(
        $group_id,
        $condition_id,
        $condition_type,
        $selected = [],
        $condition_sign = '',
        $condition_value = '',
        $condition_typevalue = ''
    ) {
        $currency = new Currency(Configuration::get('PS_CURRENCY_DEFAULT'));
        $this->context->smarty->assign(
            [
                'condition_group_id' => (int) $group_id,
                'condition_id' => (int) $condition_id,
                'condition_type' => $condition_type,
                'condition_sign' => $condition_sign,
                'condition_value' => $condition_value,
                'condition_typevalue' => $condition_typevalue,
                'module_url' => $this->context->link->getAdminLink('AdminModules', false) .
                    '&configure=' . $this->name . '&tab_module=' . $this->tab .
                    '&module_name=' . $this->name . '&token=' . Tools::getAdminTokenLite('AdminModules'),
                'currency_sign' => $currency->sign,
            ]
        );
        $condition_type_value = TACartReminderRule::$rel_condition_typevalue[$condition_type];
        $this->context->smarty->assign('condition_type_value', $condition_type_value);
        switch ($condition_type) {
            case 'cart_product':
                $products = [
                    'selected' => [],
                    'unselected' => [],
                ];
                $results = Db::getInstance()->executeS(
                    '
				SELECT DISTINCT name, p.id_product as id, p.reference
				FROM ' . _DB_PREFIX_ . 'product p
				LEFT JOIN `' . _DB_PREFIX_ . 'product_lang` pl
					ON (p.`id_product` = pl.`id_product`
					AND pl.`id_lang` = ' . (int) Context::getContext()->language->id .
                    Shop::addSqlRestrictionOnLang('pl') . ')' .
                    Shop::addSqlAssociation('product', 'p') . '
				WHERE id_lang = ' . (int) Context::getContext()->language->id . '
				ORDER BY name'
                );
                foreach ($results as $row) {
                    $products[in_array($row['id'], $selected) ? 'selected' : 'unselected'][] = $row;
                }
                Context::getContext()->smarty->assign('condition_itemlist', $products);
                $choose_content = $this->context->smarty->fetch(
                    $this->local_path . 'views/templates/admin/_configure/condition_itemlist.tpl'
                );
                Context::getContext()->smarty->assign('condition_choose_content', $choose_content);
                break;
            case 'address_country':
                $countries = [
                    'selected' => [],
                    'unselected' => [],
                ];
                $results = Db::getInstance()->executeS(
                    '
					SELECT DISTINCT name, c.id_country as id
					FROM ' . _DB_PREFIX_ . 'country c
					LEFT JOIN `' . _DB_PREFIX_ . 'country_lang` cl
						ON (c.`id_country` = cl.`id_country`)
					' . Shop::addSqlAssociation('country', 'c') . '
					WHERE id_lang = ' . (int) Context::getContext()->language->id . ' AND c.active = 1
					ORDER BY name'
                );
                foreach ($results as $row) {
                    $countries[in_array($row['id'], $selected) ? 'selected' : 'unselected'][] = $row;
                }
                Context::getContext()->smarty->assign('condition_itemlist', $countries);
                if (version_compare(_PS_VERSION_, '1.6.0', '>=') === true) {
                    $choose_content = $this->context->smarty->fetch(
                        $this->local_path . 'views/templates/admin/_configure/condition_itemlist.tpl'
                    );
                } else {
                    $choose_content = $this->context->smarty->fetch(
                        $this->local_path . 'views/templates/admin/_configure/condition_itemlist.tpl'
                    );
                }
                Context::getContext()->smarty->assign('condition_choose_content', $choose_content);
                break;
            case 'customer_lang':
                $langs = [
                    'selected' => [],
                    'unselected' => [],
                ];
                $results = Db::getInstance()->executeS(
                    '
				SELECT DISTINCT l.id_lang as id, CONCAT( name, \' - \', iso_code ) as name
				FROM ' . _DB_PREFIX_ . 'lang l
				' . Shop::addSqlAssociation('lang', 'l') . '
				WHERE l.active = 1
				ORDER BY name'
                );
                foreach ($results as $row) {
                    $langs[in_array($row['id'], $selected) ? 'selected' : 'unselected'][] = $row;
                }
                Context::getContext()->smarty->assign('condition_itemlist', $langs);
                if (version_compare(_PS_VERSION_, '1.6.0', '>=') === true) {
                    $choose_content = $this->context->smarty->fetch(
                        $this->local_path . 'views/templates/admin/_configure/condition_itemlist.tpl'
                    );
                } else {
                    $choose_content = $this->context->smarty->fetch(
                        $this->local_path . 'views/templates/admin/_configure/condition_itemlist.tpl'
                    );
                }
                Context::getContext()->smarty->assign('condition_choose_content', $choose_content);
                break;
            case 'customer_group':
                $groups = [
                    'selected' => [],
                    'unselected' => [],
                ];
                $results = Db::getInstance()->executeS(
                    '
					SELECT DISTINCT name, g.id_group as id
					FROM `' . _DB_PREFIX_ . 'group` g
					LEFT JOIN `' . _DB_PREFIX_ . 'group_lang` gl
						ON (g.`id_group` = gl.`id_group`)
					' . Shop::addSqlAssociation('group', 'g') . '
					WHERE id_lang = ' . (int) Context::getContext()->language->id . '
					ORDER BY name'
                );
                foreach ($results as $row) {
                    $groups[in_array($row['id'], $selected) ? 'selected' : 'unselected'][] = $row;
                }
                Context::getContext()->smarty->assign('condition_itemlist', $groups);
                if (version_compare(_PS_VERSION_, '1.6.0', '>=') === true) {
                    $choose_content = $this->context->smarty->fetch(
                        $this->local_path . 'views/templates/admin/_configure/condition_itemlist.tpl'
                    );
                } else {
                    $choose_content = $this->context->smarty->fetch(
                        $this->local_path . 'views/templates/admin/_configure/condition_itemlist.tpl'
                    );
                }
                Context::getContext()->smarty->assign('condition_choose_content', $choose_content);
                break;
            case 'customer_gender':
                $genders = [
                    'selected' => [],
                    'unselected' => [],
                ];
                $results = Db::getInstance()->executeS(
                    '
					SELECT DISTINCT name, g.id_gender as id
					FROM ' . _DB_PREFIX_ . 'gender g
					LEFT JOIN `' . _DB_PREFIX_ . 'gender_lang` gl
						ON (g.`id_gender` = gl.`id_gender`)
					WHERE id_lang = ' . (int) Context::getContext()->language->id . '
					ORDER BY name'
                );
                foreach ($results as $row) {
                    $genders[in_array($row['id'], $selected) ? 'selected' : 'unselected'][] = $row;
                }
                Context::getContext()->smarty->assign('condition_itemlist', $genders);
                $choose_content = $this->context->smarty->fetch(
                    $this->local_path . 'views/templates/admin/_configure/condition_itemlist.tpl'
                );
                Context::getContext()->smarty->assign('condition_choose_content', $choose_content);
                break;
            case 'cart_product_manufacturer':
                $manufacturers = [
                    'selected' => [],
                    'unselected' => [],
                ];
                $results = Db::getInstance()->executeS(
                    '
                     SELECT DISTINCT m.`name`, m.id_manufacturer as id
                     FROM ' . _DB_PREFIX_ . 'manufacturer m
                     ORDER BY m.`name`'
                );
                foreach ($results as $row) {
                    $manufacturers[in_array($row['id'], $selected) ? 'selected' : 'unselected'][] = $row;
                }
                Context::getContext()->smarty->assign('condition_itemlist', $manufacturers);
                $choose_content = $this->context->smarty->fetch(
                    $this->local_path . 'views/templates/admin/_configure/condition_itemlist.tpl'
                );
                Context::getContext()->smarty->assign('condition_choose_content', $choose_content);
                break;
            case 'cart_product_supplier':
                $suppliers = [
                    'selected' => [],
                    'unselected' => [],
                ];
                $results = Db::getInstance()->executeS(
                    '
                    SELECT DISTINCT s.`name`, s.id_supplier as id
                    FROM ' . _DB_PREFIX_ . 'supplier s
                    ORDER BY s.`name`'
                );
                foreach ($results as $row) {
                    $suppliers[in_array($row['id'], $selected) ? 'selected' : 'unselected'][] = $row;
                }
                Context::getContext()->smarty->assign('condition_itemlist', $suppliers);
                $choose_content = $this->context->smarty->fetch(
                    $this->local_path . 'views/templates/admin/_configure/condition_itemlist.tpl'
                );
                Context::getContext()->smarty->assign('condition_choose_content', $choose_content);
                break;
            case 'cart_category':
                $category_tree = '';
                $fancy_tree = false;
                if (version_compare(_PS_VERSION_, '1.6.0', '>=') === true) {
                    $tree = new HelperTreeCategories(
                        'categories-tree-' . $group_id . '-' . $condition_id,
                        $this->l('Filter by category')
                    );
                    $tree->setUseCheckBox(true);
                    $tree->setInputName('condition_select_' . $group_id . '_' . $condition_id);
                    $tree->setAttribute('is_category_filter', true);
                    $tree->setSelectedCategories($selected);
                    if (version_compare(_PS_VERSION_, '1.6.1.0', '<=') === true) {
                        $tree->setRootCategory((int) Category::getRootCategory()->id);
                    }
                    /*$tree->setUseCheckBox(true)
                      ->setInputName('condition_select_'.$group_id.'_'.$condition_id)
                      ->setAttribute('is_category_filter', true)
                      ->setUseSearch(true)
                      ->setSelectedCategories($selected)
                      ->setFullTree(true)
                      ->setLang((int)$this->context->language->id)
                      ->setChildrenOnly(true)
                      ->setNoJS(true)
                      ->setRootCategory((int)Category::getRootCategory()->id);*/
                    $category_tree = $tree->render();
                } else {
                    $helper = new TAHelper();
                    $fancy_tree = true;
                    $category_tree = $helper->renderCatTree(
                        (int) $this->context->language->id,
                        $selected,
                        'categories-tree-' . $group_id . '-' . $condition_id,
                        'condition_select_' . $group_id . '_' . $condition_id
                    );
                }
                Context::getContext()->smarty->assign('category_tree', $category_tree);
                Context::getContext()->smarty->assign('fancy_tree', $fancy_tree);
                $choose_content = $this->context->smarty->fetch(
                    $this->local_path . 'views/templates/admin/_configure/condition_tree.tpl'
                );
                $this->context->smarty->assign('condition_choose_content', $choose_content);
                break;
            default:
                $this->context->smarty->assign(
                    'condition_itemlist',
                    [
                        'selected' => [],
                        'unselected' => [],
                    ]
                );
                $this->context->smarty->assign('condition_choose_content', '');
        }
        if (version_compare(_PS_VERSION_, '1.6.0', '>=') === true) {
            return $this->context->smarty->fetch($this->local_path . 'views/templates/admin/_configure/condition.tpl');
        } else {
            return $this->context->smarty->fetch($this->local_path . 'views/templates/admin/_configure/condition.tpl');
        }
    }

    /**
     * Ajax process to update rule position
     * function call when user move a position
     */
    public function ajaxProcessRulePositions()
    {
        $way = (int) Tools::getValue('way');
        $id_rule = (int) Tools::getValue('id');
        $positions = Tools::getValue('rule');
        $new_positions = [];
        foreach ($positions as $k => $v) {
            if (count(explode('_', $v)) == 4) {
                $new_positions[] = $v;
            }
        }
        foreach ($new_positions as $position => $value) {
            $pos = explode('_', $value);
            if (isset($pos[2]) && (int) $pos[2] === $id_rule) {
                if ($rule = new TACartReminderRule((int) $pos[2])) {
                    if (isset($position) && $rule->updatePosition($way, $position)) {
                        exit('ok position ' . (int) $position . ' for rule ' . (int) $pos[2] . '\r\n');
                    } else {
                        exit('{"hasError" : true, "errors" : "Can not update the ' .
                            (int) $id_rule . ' attribute group to position ' . (int) $position . ' "}');
                    }
                } else {
                    exit('{"hasError" : true, "errors" : "The (' . (int) $id_rule . ') rule cannot be loaded."}');
                }
                break;
            }
        }
        TACartReminderRuleMatchCache::cleanAll();
        exit('no position');
    }

    /**
     * build oreders(sorts) and filters for render list
     *
     * @param $table
     * @param $fieldlist
     */
    private function setOrdersAndFilters($table, $fieldlist)
    {
        $prefix = '';
        $this->orders_filters[$table] = [];
        $orders_key = [
            'Orderby' => 'order_by',
            'Orderway' => 'order_way',
        ];
        foreach ($orders_key as $order_key => $order_value) {
            $value = '';
            if (!Tools::isSubmit($table . $order_key) && isset($this->context->cookie->{$table . $order_key})) {
                $value = $this->context->cookie->{$table . $order_key};
            } elseif (Tools::isSubmit($table . $order_key)) {
                $value = (string) Tools::getValue($table . $order_key, '');
                $this->context->cookie->{$table . $order_key} = $value;
            }
            $this->orders_filters[$table][$order_value] = $value;
        }
        $this->orders_filters[$table]['filters'] = [];
        if (!Tools::isSubmit('submitFilter')) {
            foreach ($fieldlist as $keyfield => $value) {
                if (isset($this->context->cookie->{$table . 'Filter_' . $keyfield})) {
                    $value = $this->context->cookie->{$table . 'Filter_' . $keyfield};
                    $this->orders_filters[$table]['filters'][$keyfield] = self::isJson(
                        $value
                    ) ? json_decode($value) : $value;
                }
            }
        }
        foreach ($_POST as $key => $value) {
            if (strstr($key, $table . 'Filter_')) {
                $field = str_replace($table . 'Filter_', '', $key);
                if (isset($fieldlist[$field])) {
                    if ($value === '') {
                        unset($this->context->cookie->{$prefix . $key});
                    } elseif (stripos($key, $table . 'Filter_') === 0) {
                        $this->context->cookie->{$prefix . $key} = !is_array($value) ? $value : json_encode($value);
                    } elseif (stripos($key, 'submitFilter') === 0) {
                        $this->context->cookie->{$key} = !is_array($value) ? $value : json_encode($value);
                    }
                    $this->orders_filters[$table]['filters'][$field] = $value;
                }
            }
        }
    }

    private static function isJson($string)
    {
        json_decode($string);

        return json_last_error() == JSON_ERROR_NONE;
    }

    /**
     * Reset cookie filter
     *
     * @param $table
     * @param $fields_list
     */
    private function processResetFilters($table, $fields_list)
    {
        $filters = $this->context->cookie->getFamily($table . 'Filter_');
        foreach ($filters as $cookie_key => $filter) {
            if (strncmp($cookie_key, $table . 'Filter_', 7 + Tools::strlen($this->table)) == 0) {
                $key = Tools::substr($cookie_key, 7 + Tools::strlen($table));
                /* Table alias could be specified using a ! eg. alias!field */
                $tmp_tab = explode('!', $key);
                $key = (count($tmp_tab) > 1 ? $tmp_tab[1] : $tmp_tab[0]);
                if (is_array($fields_list) && array_key_exists($key, $fields_list)) {
                    unset($this->context->cookie->$cookie_key);
                }
            }
        }
        if (isset($this->context->cookie->{'submitFilter' . $table})) {
            unset($this->context->cookie->{'submitFilter' . $table});
        }
        if (isset($this->context->cookie->{$table . 'Orderby'})) {
            unset($this->context->cookie->{$table . 'Orderby'});
        }
        if (isset($this->context->cookie->{$table . 'Orderway'})) {
            unset($this->context->cookie->{$table . 'Orderway'});
        }
        // unset($_POST);
    }

    /**
     * generate a uniq code for a cart rule
     *
     * @param string $prefix
     * @param int $length
     * @param bool|false $cardrulecheck
     *
     * @return string
     */
    public static function generateCode($prefix = '', $length = 8, $cardrulecheck = false)
    {
        $code = '';
        $possible = '123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $maxlength = Tools::strlen($possible);
        if ($length > $maxlength) {
            $length = $maxlength;
        }
        $i = 0;
        while ($i < $length) {
            $char = Tools::substr($possible, mt_rand(0, $maxlength - 1), 1);
            if (!strstr($code, $char)) {
                $code .= $char;
                ++$i;
            }
        }
        if ($cardrulecheck) {
            $id = CartRule::getIdByCode($prefix . $code);
            /* test si le code existe */
            if ($id && (int) $id > 0) {
                ++$length;

                return self::generateCode($prefix, $length, $cardrulecheck);
            }
        }

        return $prefix . $code;
    }

    /**
     * Indicated if log directory is accessible
     *
     * @return bool
     */
    public function accessLogsDirectory()
    {
        if (!is_readable(_PS_ROOT_DIR_ . '/modules/tacartreminder/logs')
            || !is_writable(_PS_ROOT_DIR_ . '/modules/tacartreminder/logs')) {
            return false;
        }

        return true;
    }

    /**
     * @param $id_shop
     *
     * @return bool
     *
     * @throws PrestaShopException
     */
    public function launchBatch($id_shop)
    {
        $this->accesslog = $this->accessLogsDirectory();
        // only use for developper, not use in production
        if ($this->performance_audit) {
            // xhgui + xhproof use param performance_audit=1 in url
        }
        if ($this->batch_running) {
            $this->log('ALREADY RUNNING');

            return false;
        } else {
            $this->batch_running = true;
        }
        if ($this->accesslog) {
            $this->purgeLog();
        }
        $this->log('SHOP                   : ' . $id_shop);
        $this->log('************ STEP CHECK NEW CART REMINDER ****************');
        $this->log('**** START                  : ' . $this->udate('H:i:s.u'));
        $this->log('MAIN SQL               START: ' . $this->udate('H:i:s.u'));
        $results = TACartReminderTools::getCartsToCheck($id_shop);
        $this->log('MAIN SQL               END: ' . $this->udate('H:i:s.u'));
        $this->log('Carts to check  : ' . count($results));
        foreach ($results as $row) {
            $this->log('*** CART ' . $row['id_cart'] . ' ***');
            $id_cart = (int) $row['id_cart'];
            try {
                $this->log('Cart update          : ' . (string) $row['date_upd']);
                $rule = TACartReminderRule::getApplicableRule((int) $id_cart, null, false, $row['date_upd']);
                if ($rule && $rule->id) {
                    $this->log('Rule                : ' . $rule->name);
                    $reminder = $rule->getFirstReminder();
                    if ($reminder) {
                        $time_now = time();
                        $nb_hour_fire_reminder = (float) Configuration::get('TA_CARTR_ABANDONNED_NB_HOUR')
                            + (float) $reminder['nb_hour'];
                        $nb_min_fire_reminder = (float) $nb_hour_fire_reminder * 60;
                        $time_abandonned_cart = strtotime(
                            '+' . $nb_min_fire_reminder . ' min',
                            strtotime($row['date_upd'])
                        );
                        $time_left = ($time_abandonned_cart - $time_now);
                        $this->log('Preview to launch         : ' . (string) date('Y-m-d H:i:s', $time_abandonned_cart));
                        $this->log('Reminder position         : ' . (string) $reminder['position']);
                        $this->log('Reminder nb hours         : ' . (string) $reminder['nb_hour']);
                        $this->log('Reminder mail             : ' . (string) $reminder['mail_template_name']);
                        $this->log('Reminder manual           : ' . (string) $reminder['manual_process']);
                        $this->log('Time left                 : ' . $time_left);
                        if ($time_left <= 0) {
                            try {
                                $is_already_launched = TACartReminderJournal::isPresentINJR(
                                    (int) $id_cart,
                                    (int) $reminder['id_reminder']
                                );
                                if (!$is_already_launched) {
                                    TACartReminderJournal::performReminder(
                                        (int) $id_cart,
                                        (int) $reminder['id_reminder']
                                    );
                                    $this->log('Reminder launched   : YES');
                                } else {
                                    $this->log('Reminder launched   : Already launched');
                                }
                            } catch (Exception $e) {
                                $this->log('Reminder launched   : NO, because ERROR');
                                $this->log('Attention error when to launch  : ' . (int) $reminder['id_reminder']);
                                $this->log('Error stack trace:');
                                $this->log($e->getMessage());
                            }
                        } else {
                            $this->log(
                                'Reminder launched   : NO, time not exceeded, date preview to launch :' .
                                (string) date(
                                    'Y-m-d H:i:s',
                                    $time_abandonned_cart
                                )
                            );
                        }
                    } else {
                        $this->log('Reminder to launch  : No reminder found');
                    }
                } else {
                    $this->log('Rule                : No rule applicable');
                }
            } catch (PrestaShopException $e) {
                $this->log($e->getMessage());
            }
        }
        $this->log('END                  : ' . $this->udate('H:i:s.u'));
        $this->log('************ STEP CLEANING  ****************');
        TACartReminderCleans::cleanIsOrdered();
        TACartReminderCleans::noReminderToLaunch();
        TACartReminderCleans::cartNotExist();
        TACartReminderCleans::customerNotExist();
        TACartReminderCleans::cartExpirate();
        TACartReminderCleans::cartRule();
        TACartReminderRuleMatchCache::cleanCacheTTL();
        $this->log('END                  : ' . $this->udate('H:i:s.u'));
        $this->log('************ STEP CHECK RUNNING REMINDER  ****************');
        $this->log('**** START                  : ' . $this->udate('H:i:s.u'));
        $journals = TACartReminderJournal::getRunnings($id_shop);
        foreach ($journals as $journal_row) {
            $this->log('*** CART ' . $journal_row['id_cart'] . ' ***');
            $journal_running = new TACartReminderJournal((int) $journal_row['id_journal']);
            $reminder = $journal_running->getReminderToLaunch();
            if ($reminder && (int) $reminder['id_reminder']) {
                $rule = new TACartReminderRule((int) $journal_running->id_rule);
                if (Validate::isLoadedObject($rule) && $rule->id && $rule->status) {
                    $to_launch = false;
                    if ((int) $rule->force_reminder) {
                        $to_launch = true;
                    } else {
                        if (!TACartReminderRule::isApplicableRule($rule->id, $journal_running->id_cart)) {
                            $message = (string) sprintf(
                                $this->l('The rule name %1$s is no longer applicable, the reminder is canceled.'),
                                (string) $rule->name
                            );
                            $journal_running->close($message);
                        } else {
                            $to_launch = true;
                        }
                    }
                    if ($to_launch) {
                        $this->log('Reminder position         : ' . (string) $reminder['position']);
                        $this->log('Reminder nb hours         : ' . (string) $reminder['nb_hour']);
                        $this->log('Reminder mail             : ' . (string) $reminder['mail_template_name']);
                        $this->log('Reminder manual           : ' . (string) $reminder['manual_process']);
                        $is_already_launched = TACartReminderJournal::isPresentINJR(
                            (int) $journal_running->id_cart,
                            (int) $reminder['id_reminder']
                        );
                        if (!$is_already_launched) {
                            TACartReminderJournal::performReminder(
                                (int) $journal_running->id_cart,
                                (int) $reminder['id_reminder']
                            );
                            $this->log('Reminder launched   : YES');
                        } else {
                            $this->log('Reminder launched   : No is already launched');
                        }
                    }
                } else {
                    $message = (string) sprintf(
                        $this->l('The rule id %1$s does not exist or is not active, the reminder is canceled.'),
                        (int) $rule->id
                    );
                    $journal_running->close($message);
                }
            } else {
                $this->log('Execution date not reached');
            }
        }
        $this->log('END                  : ' . $this->udate('H:i:s.u'));
        $this->batch_running = false;
        if ($this->performance_audit) {
        }

        return true;
    }

    /**
     * log a message in file
     *
     * @param $log_message
     */
    public function log($log_message)
    {
        if ($this->accesslog) {
            if ($this->log_fic_name == '') {
                $this->log_fic_name = 'tacartreminder-' . date('Ymd') . '.log';
            }
            $fp = @fopen($this->log_directory . $this->log_fic_name, 'a');
            @fwrite($fp, date('H\Hi') . ' ' . $log_message . chr(13) . chr(10));
            @fclose($fp);
        }
    }

    /**
     * Log long lone separation
     *
     * @param $log_message
     */
    public function loglongline($log_message)
    {
        if ($this->accesslog) {
            if ($this->log_fic_name == '') {
                $this->log_fic_name = 'tacartreminder-' . date('Ymd') . '.log';
            }
            $fp = @fopen($this->log_directory . '/' . $this->log_fic_name, 'a');
            if ($log_message && !empty($log_message)) {
                @fwrite($fp, $log_message . chr(13) . chr(10));
            }
            @fwrite($fp, '============================================================================');
            @fclose($fp);
        }
    }

    /**
     * Purge all log if timestamp exeed 2592000
     */
    public function purgeLog()
    {
        $folder = new DirectoryIterator(_PS_ROOT_DIR_ . '/modules/tacartreminder/logs');
        foreach ($folder as $file) {
            if ($file->isFile() && !$file->isDot() && (time() - $file->getMTime() > 2592000)) {
                unlink($file->getPathname());
            }
        }
    }

    /**
     * Check param in url to recover cart, trace click and add cart rule auto
     */
    public function hookDisplayHeader()
    {
        // this part will be deleted in next version
        if (Tools::isSubmit('tacartreminder_clk') && (int) Tools::getValue('id_reminder')
            && (int) Tools::getValue('recover_cart')
        ) {
            $cart_id = (int) Tools::getValue('recover_cart');
            $journal = TACartReminderJournal::getWithCart($cart_id);
            if ($journal && $journal->id) {
                TACartReminderJournal::markReminderIsClick((int) $journal->id, (int) Tools::getValue('id_reminder'));
            }
        }
        if (Tools::isSubmit('cadd') && (int) Tools::getValue('recover_cart')) {
            $cart_id = (int) Tools::getValue('recover_cart');
            $journal = TACartReminderJournal::getWithCart($cart_id);
            $cadd = (int) Tools::getValue('cadd');
            if (isset($this->context->cart) && (int) $this->context->cart->id
                && (int) $cadd && $journal && (int) $journal->id_cart_rule) {
                if (($cart_rule = new CartRule((int) $journal->id_cart_rule))
                    && Validate::isLoadedObject($cart_rule)) {
                    if ($error = $cart_rule->checkValidity($this->context, false, true)) {
                        // $this->errors[] = $error;
                    } else {
                        $this->context->cart->addCartRule($cart_rule->id);
                    }
                }
            }
        }
    }

    /**
     * Exception handler
     *
     * @param $errno
     * @param $errstr
     * @param $errfile
     * @param $errline
     *
     * @throws ErrorException
     */
    public function tacartreminderErrorHandler($errno, $errstr, $errfile, $errline)
    {
        throw new ErrorException($errstr, $errno, 0, $errfile, $errline);
    }

    /* Run cron tasks */
    public function hookActionCronJob()
    {
        self::launchBatchAllShops();
    }

    /**
     * Launch the main batch to remind cart and realize clean process)
     */
    public static function launchBatchAllShops()
    {
        $tacartreminder = Module::getInstanceByName('tacartreminder');
        $time_last_launched = Configuration::get('TA_CARTR_BATCHLASTDATE');
        $time_last_launched_t = (float) $time_last_launched;
        $curtime = time();
        $f = fopen('cronlock', 'w');
        if (!$f) {
            exit('Cannot create lock file');
        } elseif (flock($f, LOCK_EX | LOCK_NB)) {
            if (($curtime - $time_last_launched_t) > 50) {
                Configuration::updateValue('TA_CARTR_BATCHLASTDATE', $curtime);
                $tacartreminder->accesslog = $tacartreminder->accessLogsDirectory();
                if ($tacartreminder->accesslog) {
                    $tacartreminder->purgeLog();
                }
                $tacartreminder->log('Launch batch');
                $shops = Shop::getShops(true, null, true);
                foreach ($shops as $shop_id) {
                    $tacartreminder->loglongline('SHOP ID:' . $shop_id);
                    $context = Context::getContext();
                    $context->shop = new Shop((int) $shop_id);
                    Shop::setContext(Shop::CONTEXT_SHOP, (int) $shop_id);
                    if (((version_compare(_PS_VERSION_, '1.5.6.0', '>') === true)
                            && $tacartreminder->isEnabledForShopContext())
                        || ((version_compare(_PS_VERSION_, '1.5.6.1', '<') === true)
                            && $tacartreminder->taIsEnabledForShopID(
                                $shop_id
                            ))
                    ) {
                        $tacartreminder->log('SHOP ID:' . $shop_id);
                        $tacartreminder->launchBatch($shop_id);
                    } else {
                        $tacartreminder->log('Disable for this shop');
                    }
                }
            } else {
                echo $tacartreminder->l('The batch has already been launched', 'tacartreminder');
            }
        }
    }

    /**
     * PS compatibility
     *
     * @param int $shop_id
     *
     * @return bool
     */
    public function taIsEnabledForShopID($shop_id)
    {
        $active = false;
        $id_module = Db::getInstance()->getValue(
            'SELECT `id_module` FROM `' . _DB_PREFIX_ . 'module` WHERE `name` = \'' . pSQL($this->name) . '\''
        );
        if (Db::getInstance()->getValue(
            'SELECT `id_module` FROM `' . _DB_PREFIX_ . 'module_shop` WHERE `id_module` = ' . (int) $id_module .
            ' AND `id_shop` = ' . (int) $shop_id
        )
        ) {
            $active = true;
        }

        return $active;
    }

    /* Return cron job execution frequency */
    public function getCronFrequency()
    {
        return [
            'hour' => -1,
            'day' => -1,
            'month' => -1,
            'day_of_week' => -1,
        ];
    }

    /**
     * Get date with micro second
     * interesting for performance testing
     *
     * @param $format
     * @param null $utimestamp
     *
     * @return bool|string
     */
    private function udate($format, $utimestamp = null)
    {
        if (is_null($utimestamp)) {
            $utimestamp = microtime(true);
        }
        $timestamp = floor($utimestamp);
        $milliseconds = round(($utimestamp - $timestamp) * 1000000);

        return date(preg_replace('`(?<!\\\\)u`', $milliseconds, $format), $timestamp);
    }
}
