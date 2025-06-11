<?php
/**
 * GIFT CARD
 *
 *    @author    EIRL Timactive De Véra
 *    @copyright Copyright (c) TIMACTIVE 2015 -EIRL Timactive De Véra
 *    @license   Commercial license
 *
 *    @category pricing_promotion
 *
 *    @version 1.1.0
 *************************************
 **         GIFT CARD                *
 **          V 1.0.0                 *
 *************************************
 * +
 * + Languages: EN, FR, ES
 * + PS version: 1.5,1.6,1.7
 */

namespace TimActive\Module\SmartCartReminder\Command;

use Configuration;
use Doctrine\ORM\EntityManagerInterface;
use PrestaShop\PrestaShop\Adapter\ServiceLocator;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

if (!defined('_PS_VERSION_')) {
    exit;
}
class SetUpShopDemoCommand extends ContainerAwareCommand
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    private $metadata;

    private $dbName;

    private $dbPrefix;

    protected function configure()
    {
        $this
            ->setName('tacartreminder:setup_shop_demo')
            ->setDescription('Set up demo shop data');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        require_once $this->getContainer()->get('kernel')->getRootDir() . '/../config/config.inc.php';
        $config = include _PS_ROOT_DIR_ . '/app/config/parameters.php';
        $this->initContext($output);
        \Configuration::updateValue('PS_COOKIE_SAMESITE', 'None');
        $idLang = (int) \Configuration::get('PS_LANG_DEFAULT');
        $shop_id = (int) \Configuration::get('PS_SHOP_DEFAULT');
        $m_i = \Module::getInstanceByName('tacartreminder');
        $id_lang_en = (int) \Language::getIdByIso('en');
        $id_lang_fr = (int) \Language::getIdByIso('fr');
        $output->writeln('CREATE Profile...');
        $profile = new \Profile();
        $profile->name = [(int) \Configuration::get('PS_LANG_DEFAULT') => 'demo'];
        $profile->add();
        $access = new \Access();
        $access->updateLgcModuleAccess((int) $profile->id, $m_i->id, 'configure', 1);
        $access->updateLgcModuleAccess((int) $profile->id, $m_i->id, 'view', 1);
        $admin_tab_sell_id = \Tab::getIdFromClassName('SELL');
        $admin_tab_order_id = \Tab::getIdFromClassName('AdminParentOrders');
        $admin_tab_o_id = \Tab::getIdFromClassName('AdminOrders');
        $admin_tab_pcust_id = \Tab::getIdFromClassName('AdminParentCustomer');
        $admin_tab_cust_id = \Tab::getIdFromClassName('AdminCustomers');
        $admin_tab_cart_id = \Tab::getIdFromClassName('AdminCarts');
        $admin_tab_live_cr_id = \Tab::getIdFromClassName('AdminLiveCartReminder');
        $admin_tab_pm_id = \Tab::getIdFromClassName('AdminParentModulesSf');
        $admin_tab_msf_id = \Tab::getIdFromClassName('AdminModulesSf');
        $admin_tab_m_id = \Tab::getIdFromClassName('AdminModules');
        $admin_tab_i_id = \Tab::getIdFromClassName('IMPROVE');
        $access->updateLgcAccess((int) $profile->id, $admin_tab_sell_id, 'view', 1);
        $access->updateLgcAccess((int) $profile->id, $admin_tab_order_id, 'view', 1);
        $access->updateLgcAccess((int) $profile->id, $admin_tab_pcust_id, 'view', 1);
        $access->updateLgcAccess((int) $profile->id, $admin_tab_cust_id, 'view', 1);
        $access->updateLgcAccess((int) $profile->id, $admin_tab_cart_id, 'view', 1);
        $access->updateLgcAccess((int) $profile->id, $admin_tab_live_cr_id, 'all', 1);
        $access->updateLgcAccess((int) $profile->id, $admin_tab_o_id, 'all', 1);
        $access->updateLgcAccess((int) $profile->id, $admin_tab_i_id, 'view', 1);
        $access->updateLgcAccess((int) $profile->id, $admin_tab_m_id, 'edit', 1);
        $access->updateLgcAccess((int) $profile->id, $admin_tab_pm_id, 'edit', 1);
        $access->updateLgcAccess((int) $profile->id, $admin_tab_msf_id, 'edit', 1);
        $access->updateLgcAccess((int) $profile->id, $admin_tab_msf_id, 'view', 1);
        $crypto = ServiceLocator::get('\\PrestaShop\\PrestaShop\\Core\\Crypto\\Hashing');
        $output->writeln('CREATE EMPLOYEES...');
        $e1 = new \Employee();
        $e1->lastname = 'scremployeeen';
        $e1->firstname = 'scremployeeen';
        $e1->default_tab = $admin_tab_live_cr_id;
        $e1->id_lang = $id_lang_en;
        $e1->passwd = $crypto->hash('demodemo');
        $e1->active = true;
        $e1->id_profile = (int) $profile->id;
        $e1->email = 'demo@demo.com';
        $e1->add();

        $e2 = new \Employee();
        $e2->lastname = 'scremployeefr';
        $e2->firstname = 'scremployeefr';
        $e2->default_tab = $admin_tab_live_cr_id;
        $e2->id_lang = $id_lang_fr;
        $e2->passwd = $crypto->hash('demodemo');
        $e2->active = true;
        $e2->id_profile = (int) $profile->id;
        $e2->email = 'demo@demo.fr';
        $e2->add();

        $output->writeln('CREATE SCRENARIO RULES...');
        $id_country_usa = (int) \Db::getInstance()->getValue('SELECT id_country FROM ' . _DB_PREFIX_ . 'country where iso_code = \'us\'');
        $pro_group = new \Group();
        $pro_group->name = [
            $id_lang_en => 'Professional',
            $id_lang_fr => 'Professionnel',
        ];
        $pro_group->price_display_method = 1;
        $pro_group->add();
        // $id_shop = Shop::getContext()->shop->id;
        /** START EMAIL TEMPLATE **/
        $mt_offer_fdp = $this->addEmailTemplate([
            'name' => 'Free shipping',
            'subject' => [
                $id_lang_en => '{customer_firstname} free shipping',
                $id_lang_fr => '{customer_firstname} vos frais de port offert',
            ],
            'content_file' => '1_offer_fdp',
        ], $id_lang_en, $id_lang_fr);
        $mt_christmas = $this->addEmailTemplate([
            'name' => 'Christmas / surprise gift',
            'subject' => [
                $id_lang_en => '{customer_firstname}, celebrate Christmas Free Gift Offer',
                $id_lang_fr => '{customer_firstname}, joyeux noël cadeau offert',
            ],
            'content_file' => '2_christmas',
        ], $id_lang_en, $id_lang_fr);
        $mt_no_wait_longer = $this->addEmailTemplate([
            'name' => 'Do not wait longer to take advantage of this limited time offer',
            'subject' => [
                $id_lang_en => '{customer_firstname}  do not wait longer to take advantage of this limited time offer',
                $id_lang_fr => '{customer_firstname}  n\'attendez pas plus longtemps pour profiter de cette offre d\'une durée limitée',
            ],
            'content_file' => '3_nowaitlonger',
        ], $id_lang_en, $id_lang_fr);
        $mt_newcustomer = $this->addEmailTemplate([
            'name' => 'New customer 5% off your order',
            'subject' => [
                $id_lang_en => '{customer_firstname} enjoy 5% discount on your first order',
                $id_lang_fr => '{customer_firstname} 5% de déduction pour votre première commande',
            ],
            'content_file' => '4_newcustomer',
        ], $id_lang_en, $id_lang_fr);
        $mt_loyalcustomer = $this->addEmailTemplate([
            'name' => 'Loyal customer 5% off your order',
            'subject' => [
                $id_lang_en => '{customer_firstname} 5% for your loyalty',
                $id_lang_fr => '{customer_firstname} 5% pour votre fidélité',
            ],
            'content_file' => '5_loyalcustomer',
        ], $id_lang_en, $id_lang_fr);
        $mt_professionalcustomer = $this->addEmailTemplate([
            'name' => 'Unique offer for professional',
            'subject' => [
                $id_lang_en => '{customer_firstname}, you are a professional take advantage of this unique offer for you',
                $id_lang_fr => '{customer_firstname}, vous êtes professionnel profitez de cette offre unique!',
            ],
            'content_file' => '6_professional',
        ], $id_lang_en, $id_lang_fr);
        /** START CART RULE **/
        $cart_rule_free_ship = $this->initCartRule();
        $cart_rule_free_ship->name = [
            $id_lang_en => 'Free Shipping',
            $id_lang_fr => 'Frais de port offer',
        ];
        $cart_rule_free_ship->description = 'Model for smart cart reminder module, not delete';
        $cart_rule_free_ship->code = '256NL6SX';
        $cart_rule_free_ship->minimum_amount = 90;
        $cart_rule_free_ship->free_shipping = true;
        $cart_rule_free_ship->add();

        $cart_rule_5_off_order = $this->initCartRule();
        $cart_rule_5_off_order->name = [
            $id_lang_en => '5% off order',
            $id_lang_fr => '5% de réduction',
        ];
        $cart_rule_5_off_order->description = 'Model for smart cart reminder module, not delete';
        $cart_rule_5_off_order->code = 'D23BC78H';
        $cart_rule_5_off_order->active = false;
        $cart_rule_5_off_order->apply_discount = 'percent';
        $cart_rule_5_off_order->reduction_percent = 5;
        $cart_rule_5_off_order->add();
        $product = new \Product();
        $product->name = [
            $id_lang_en => 'Chocolate gift',
            $id_lang_fr => 'Chocolat cadeau',
        ];
        $product->link_rewrite = [
            $id_lang_en => 'chocolate-gift',
            $id_lang_fr => 'chocolat-cadeau',
        ];
        $product->visibility = 'none';
        $product->price = 10;
        $product->qty = 9999;
        $product->out_of_stock = 1;
        $product->add();
        $cart_rule_gift = $this->initCartRule();
        $cart_rule_gift->name = [
            $id_lang_en => 'Christmas gift',
            $id_lang_fr => 'Cadeau de noel',
        ];
        $cart_rule_gift->description = 'Model for smart cart reminder module, not delete';
        $cart_rule_gift->code = 'TJLUTBTB';
        $cart_rule_gift->active = false;
        $cart_rule_gift->free_gift = true;
        $cart_rule_gift->gift_product = (int) $product->id;
        $cart_rule_gift->add();
        /** END INIT CART RULE **/

        /** INIT RULE **/
        // PROFESSIONAL CUSTOMER
        $rule = $this->initRule();
        $rule->name = 'Professionnal / Cart amount > $200';
        $rule->create_cart_rule = true;
        $rule->id_cart_rule = (int) $cart_rule_5_off_order->id;
        $rule->cart_rule_nbday_validity = 7;
        $rule->add();
        $id_group = $this->addGroup((int) $rule->id);
        $id_condition = $this->addCondition([
            'id_groupcondition' => (int) $id_group,
            'type' => 'customer_group',
        ]);
        $this->addConditionValue([
            'id_condition' => $id_condition,
            'typevalue' => 'list',
            'id_item' => (int) $pro_group->id,
        ]);
        $this->addReminder([
            'id_mail_template' => (int) $mt_professionalcustomer->id,
            'nb_hour' => 1,
            'manual_process' => 0,
            'admin_mails' => '',
            'position' => 1,
            'id_rule' => (int) $rule->id,
        ]);
        $this->addReminder([
            'id_mail_template' => (int) $mt_no_wait_longer->id,
            'nb_hour' => 24,
            'manual_process' => 0,
            'admin_mails' => '',
            'position' => 2,
            'id_rule' => (int) $rule->id,
        ]);
        $this->addReminder([
            'id_mail_template' => 0,
            'nb_hour' => 12,
            'manual_process' => 1,
            'admin_mails' => 'myemail@prestashop.com',
            'position' => 3,
            'id_rule' => (int) $rule->id,
        ]);
        // CHRISTMAS
        $rule = $this->initRule();
        $rule->name = 'Christmas / amount > 30$ / gift offer';
        $rule->date_from = date('Y') . '-11-15 00:00:00';
        $rule->date_to = date('Y') . '-12-31 23:59:59';
        $rule->create_cart_rule = true;
        $rule->id_cart_rule = (int) $cart_rule_gift->id;
        $rule->cart_rule_nbday_validity = 7;
        $rule->add();
        $id_group = $this->addGroup((int) $rule->id);
        $id_condition = $this->addCondition([
            'id_groupcondition' => (int) $id_group,
            'type' => 'cart_amount',
        ]);
        $this->addConditionValue([
            'id_condition' => $id_condition,
            'typevalue' => 'integer',
            'sign' => '>=',
            'value' => '90',
        ]);
        $this->addReminder([
            'id_mail_template' => (int) $mt_christmas->id,
            'nb_hour' => 1,
            'manual_process' => 0,
            'admin_mails' => '',
            'position' => 1,
            'id_rule' => (int) $rule->id,
        ]);
        $this->addReminder([
            'id_mail_template' => (int) $mt_no_wait_longer->id,
            'nb_hour' => 24,
            'manual_process' => 0,
            'admin_mails' => '',
            'position' => 2,
            'id_rule' => (int) $rule->id,
        ]);
        // RULE FREE SHIP USA
        $rule = $this->initRule();
        $rule->name = 'United States / Cart Amount > $90 / Free Shipping';
        $rule->create_cart_rule = true;
        $rule->id_cart_rule = (int) $cart_rule_free_ship->id;
        $rule->cart_rule_nbday_validity = 7;
        $rule->add();
        $id_group = $this->addGroup((int) $rule->id);
        $id_condition = $this->addCondition([
            'id_groupcondition' => (int) $id_group,
            'type' => 'cart_amount',
        ]);
        $this->addConditionValue([
            'id_condition' => $id_condition,
            'typevalue' => 'integer',
            'sign' => '>=',
            'value' => '90',
        ]);
        $id_condition = $this->addCondition([
            'id_groupcondition' => (int) $id_group,
            'type' => 'address_country',
        ]);
        $this->addConditionValue([
            'id_condition' => $id_condition,
            'typevalue' => 'list',
            'id_item' => $id_country_usa,
        ]);
        $this->addReminder([
            'id_mail_template' => (int) $mt_offer_fdp->id,
            'nb_hour' => 1,
            'manual_process' => 0,
            'admin_mails' => '',
            'position' => 1,
            'id_rule' => (int) $rule->id,
        ]);
        $this->addReminder([
            'id_mail_template' => (int) $mt_no_wait_longer->id,
            'nb_hour' => 24,
            'manual_process' => 0,
            'admin_mails' => '',
            'position' => 2,
            'id_rule' => (int) $rule->id,
        ]);
        // RULE NEW CUSTOMER
        $rule = $this->initRule();
        $rule->name = 'New customer / 5% off your order';
        $rule->create_cart_rule = true;
        $rule->id_cart_rule = (int) $cart_rule_5_off_order->id;
        $rule->cart_rule_nbday_validity = 7;
        $rule->add();
        $id_group = $this->addGroup((int) $rule->id);
        $id_condition = $this->addCondition([
            'id_groupcondition' => (int) $id_group,
            'type' => 'customer_order_count',
        ]);
        $this->addConditionValue([
            'id_condition' => $id_condition,
            'typevalue' => 'integer',
            'sign' => '=',
            'value' => '0',
        ]);
        $this->addReminder([
            'id_mail_template' => (int) $mt_newcustomer->id,
            'nb_hour' => 0.5,
            'manual_process' => 0,
            'admin_mails' => '',
            'position' => 1,
            'id_rule' => (int) $rule->id,
        ]);
        $this->addReminder([
            'id_mail_template' => (int) $mt_no_wait_longer->id,
            'nb_hour' => 24,
            'manual_process' => 0,
            'admin_mails' => '',
            'position' => 2,
            'id_rule' => (int) $rule->id,
        ]);
        // LOYAL CUSTOMER
        $rule = $this->initRule();
        $rule->name = 'Loyal customer / 5% off your order';
        $rule->create_cart_rule = true;
        $rule->id_cart_rule = (int) $cart_rule_5_off_order->id;
        $rule->cart_rule_nbday_validity = 7;
        $rule->add();
        $id_group = $this->addGroup((int) $rule->id);
        $id_condition = $this->addCondition([
            'id_groupcondition' => (int) $id_group,
            'type' => 'customer_order_count',
        ]);
        $this->addConditionValue([
            'id_condition' => $id_condition,
            'typevalue' => 'integer',
            'sign' => '>',
            'value' => '2',
        ]);
        $this->addReminder([
            'id_mail_template' => (int) $mt_loyalcustomer->id,
            'nb_hour' => 1,
            'manual_process' => 0,
            'admin_mails' => '',
            'position' => 1,
            'id_rule' => (int) $rule->id,
        ]);
        $this->addReminder([
            'id_mail_template' => (int) $mt_no_wait_longer->id,
            'nb_hour' => 24,
            'manual_process' => 0,
            'admin_mails' => '',
            'position' => 2,
            'id_rule' => (int) $rule->id,
        ]);

        /*Configuration::updateValue('PS_SSL_ENABLED', 1);
        Configuration::updateValue('PS_SSL_ENABLED_EVERYWHERE', 1);
        $localization_pack = new LocalizationPack();
        $version = str_replace('.', '', _PS_VERSION_);
        $version = substr($version, 0, 2);
        $iso_localization_pac
            /** END EMAIL TEMPLATE **/

        /**$localization_pack = new \LocalizationPack();
        $version = str_replace('.', '', _PS_VERSION_);
        $version = substr($version, 0, 2);
        $output->writeln('INSTALL PACK FR...');
        $iso_localization_pack = "fr";
        $packfr = @\Tools::file_get_contents(_PS_API_URL_.'/localization/'.$version.'/'.$iso_localization_pack.'.xml');
        $localization_pack->loadLocalisationPack($packfr, array(), false, $iso_localization_pack);**/
        $output->writeln('INSTALL PAYMENT MODULE...');
        $wire_module = \Module::getInstanceByName('ps_wirepayment');
        if (!\Module::isInstalled('ps_wirepayment')) {
            $wire_module->install();
        }
        $check_module = \Module::getInstanceByName('ps_checkpayment');
        if (!\Module::isInstalled('ps_checkpayment')) {
            $check_module->install();
        }
        $ps_contactinfo = \Module::getInstanceByName('ps_contactinfo');
        if (\Module::isInstalled('ps_contactinfo')) {
            $ps_contactinfo->disable(true);
        }
        \Configuration::updateValue('PS_REWRITING_SETTINGS', 1);
        \Configuration::updateValue('BANK_WIRE_OWNER', 'TimActive');
        \Configuration::updateValue('BANK_WIRE_DETAILS', 'TEST');
        \Configuration::updateValue('BANK_WIRE_ADDRESS', '44000 Nantes, France');
        \Configuration::updateValue('CHEQUE_NAME', 'TimActive');
        \Configuration::updateValue('CHEQUE_ADDRESS', '44000 Nantes, France');
        $sql = 'INSERT IGNORE INTO ' . _DB_PREFIX_ . 'module_country(id_module, id_shop, id_country)
    SELECT ' . $wire_module->id . ', 1, id_country from ' . _DB_PREFIX_ . 'country';
        \Db::getInstance()->execute($sql);
        $sql = 'INSERT IGNORE INTO ' . _DB_PREFIX_ . 'module_country(id_module, id_shop, id_country)
    SELECT ' . $check_module->id . ', 1, id_country from ' . _DB_PREFIX_ . 'country';
        \Db::getInstance()->execute($sql);
        // Création d'un client fictif
        $customer = new \Customer();
        $customer->firstname = 'John';
        $customer->lastname = 'Doe';
        $customer->email = 'johndoe@example.com';
        $customer->passwd = \Tools::hash('demopassword'); // utilisez une fonction de hachage pour le mot de passe
        $customer->is_guest = 0;
        $customer->active = 1;
        $customer->add();

        // Création d'un panier fictif
        $cart = new \Cart();
        $cart->id_customer = $customer->id;
        $cart->id_currency = (int) \Configuration::get('PS_CURRENCY_DEFAULT'); // utilisez l'ID de la devise par défaut
        $cart->add();

        // Ajout d'un produit fictif au panier
        $id_product = 1; // Remplacez par l'ID du produit que vous souhaitez ajouter
        $product_quantity = 1; // La quantité du produit à ajouter
        $product = new \Product($id_product);
        $combinations = $product->getAttributesResume((int) \Configuration::get('PS_LANG_DEFAULT'));
        if (!empty($combinations)) {
            $first_combination = reset($combinations);
            $cart->updateQty($product_quantity, $id_product, $first_combination['id_product_attribute']);
        } else {
            $cart->updateQty($product_quantity, $id_product);
        }
        $update_quantity = $cart->updateQty($product_quantity, $id_product);
        if ($update_quantity) {
            $output->writeln('Product added to cart successfully.');
        } else {
            $output->writeln('Failed to add product to cart.');
        }

        $output->writeln('GENERATE HTACCESS...');
        \Tools::generateHtaccess(_PS_ROOT_DIR_ . '/.htaccess', null, null, '', 0, false, 0);
        /**
         * Add this line to file .htaccess
         * <ifmodule mod_headers.c>
         * Header always edit Set-Cookie ^(.*)$ $1;SameSite=None;Secure
         * </ifmodule>
         */
        $fileHtaccess = _PS_ROOT_DIR_ . '/.htaccess';
        $content = \Tools::file_get_contents($fileHtaccess);
        $newContent = "<IfModule mod_headers.c>\nHeader always edit Set-Cookie ^(.*)$ $1;SameSite=None;Secure\n</IfModule>\n\n";
        $content = $newContent . $content;
        file_put_contents($fileHtaccess, $content);
        $output->writeln('ENABLE & RESET CACHE...');
        \Tools::enableCache();
        \Tools::clearCache(\Context::getContext()->smarty);
        \Tools::restoreCacheSettings();

        return true;
    }

    private function initContext($output)
    {
        /** @var LegacyContext $legacyContext */
        $legacyContext = $this->getContainer()->get('prestashop.adapter.legacy.context');
        // We need to have an employee or the module hooks don't work
        // see LegacyHookSubscriber
        if (!$legacyContext->getContext()->employee) {
            // Even a non existing employee is fine
            $legacyContext->getContext()->employee = new \Employee(1);
            $output->writeln('Employee set' . $legacyContext->getContext()->employee->firstname);
        }
    }

    private function initRule()
    {
        $rule = new \TACartReminderRule();
        $rule->force_reminder = 1;
        $rule->status = true;

        return $rule;
    }

    private function addGroup($id_rule)
    {
        \Db::getInstance()->insert('ta_cartreminder_rule_groupcondition', ['id_rule' => (int) $id_rule]);

        return \Db::getInstance()->Insert_ID();
    }

    private function addCondition($data)
    {
        \Db::getInstance()->insert('ta_cartreminder_rule_condition', $data);

        return \Db::getInstance()->Insert_ID();
    }

    private function addConditionValue($data)
    {
        \Db::getInstance()->insert('ta_cartreminder_rule_condition_value', $data);

        return \Db::getInstance()->Insert_ID();
    }

    private function addReminder($data)
    {
        \Db::getInstance()->insert('ta_cartreminder_rule_reminder', $data);

        return \Db::getInstance()->Insert_ID();
    }

    private function initCartRule()
    {
        $cr = new \CartRule();
        $cr->date_from = date('Y-m-d H:i:s');
        $cr->date_to = date('Y-m-d H:i:s');
        $cr->quantity = 1;
        $cr->quantity_per_user = 1;
        $cr->active = false;

        return $cr;
    }

    private function addEmailTemplate($info, $id_lang_en, $id_lang_fr)
    {
        $em_demo_template_path = '/var/www/html/demo_email/';
        $email = new \TACartReminderMailTemplate();
        $email->name = $info['name'];
        $email->subject = $info['subject'];
        $email->title = $info['subject'];
        echo "\n*" . $em_demo_template_path . "\n*";
        $email->content_html = [
            $id_lang_en => \Tools::file_get_contents($em_demo_template_path . $info['content_file'] . '_en.html'),
            $id_lang_fr => \Tools::file_get_contents($em_demo_template_path . $info['content_file'] . '_fr.html'),
        ];
        $email->content_txt = [
            $id_lang_en => \Tools::file_get_contents($em_demo_template_path . $info['content_file'] . '_en.txt'),
            $id_lang_fr => \Tools::file_get_contents($em_demo_template_path . $info['content_file'] . '_fr.txt'),
        ];
        $email->add();

        return $email;
    }
}
