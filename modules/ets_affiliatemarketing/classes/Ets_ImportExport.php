<?php
/**
 * Copyright ETS Software Technology Co., Ltd
 *
 * NOTICE OF LICENSE
 *
 * This file is not open source! Each license that you purchased is only available for 1 website only.
 * If you want to use this file on more websites (or projects), you need to purchase additional licenses.
 * You are not allowed to redistribute, resell, lease, license, sub-license or offer our resources to any third party.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future.
 *
 * @author ETS Software Technology Co., Ltd
 * @copyright  ETS Software Technology Co., Ltd
 * @license    Valid for 1 website (or project) for each purchase of license
 */
 
if (!defined('_PS_VERSION_')) {
    exit();
}
require_once(_PS_MODULE_DIR_ . 'ets_affiliatemarketing/classes/Ets_Loyalty_Config.php');
require_once(_PS_MODULE_DIR_ . 'ets_affiliatemarketing/classes/Ets_Affiliate_Config.php');
require_once(_PS_MODULE_DIR_ . 'ets_affiliatemarketing/classes/Ets_AM.php');
require_once(_PS_MODULE_DIR_ . 'ets_affiliatemarketing/classes/Ets_Invitation.php');
require_once(_PS_MODULE_DIR_ . 'ets_affiliatemarketing/classes/Ets_Participation.php');
require_once(_PS_MODULE_DIR_ . 'ets_affiliatemarketing/classes/Ets_PaymentMethod.php');
require_once(_PS_MODULE_DIR_ . 'ets_affiliatemarketing/classes/Ets_PaymentMethodField.php');
require_once(_PS_MODULE_DIR_ . 'ets_affiliatemarketing/classes/Ets_Product_View.php');
require_once(_PS_MODULE_DIR_ . 'ets_affiliatemarketing/classes/Ets_Reward_Product.php');
require_once(_PS_MODULE_DIR_ . 'ets_affiliatemarketing/classes/Ets_Reward_Usage.php');
require_once(_PS_MODULE_DIR_ . 'ets_affiliatemarketing/classes/Ets_Sponsor.php');
require_once(_PS_MODULE_DIR_ . 'ets_affiliatemarketing/classes/Ets_User.php');
require_once(_PS_MODULE_DIR_ . 'ets_affiliatemarketing/classes/Ets_Voucher.php');
require_once(_PS_MODULE_DIR_ . 'ets_affiliatemarketing/classes/Ets_Withdraw.php');
require_once(_PS_MODULE_DIR_ . 'ets_affiliatemarketing/classes/Ets_Withdraw_Field.php');
require_once(_PS_MODULE_DIR_ . 'ets_affiliatemarketing/defines.php');
class Ets_ImportExport extends Module
{
    public function __construct()
    {
        $this->name = 'ets_affiliatemarketing';
        parent::__construct();
        $this->module = new Ets_affiliatemarketing();
    }
    public function generateArchive()
    {
        $errors = array();
        $zip = new ZipArchive();
        $cacheDir = _PS_CACHE_DIR_ . 'ets_affiliatemarketing/';
        $bannerDir = EAM_PATH_IMAGE_BANER;
        if (!is_dir($cacheDir))
            @mkdir($cacheDir, 0755, true);
        $zip_file_name = 'ets_affiliatemarketing_' . date('dmYHis') . '.zip';
        if ($zip->open($cacheDir . $zip_file_name, ZipArchive::OVERWRITE | ZipArchive::CREATE) === true) {
            if (!$zip->addFromString('eam_data.xml', $this->exportData())) {
                $errors[] = $this->l('Cannot create eam_data.xml');
            }
            if (is_dir($bannerDir)) {
                if ($images = scandir($bannerDir)) {
                    foreach ($images as $image) {
                        if ($image != '.' && $image != '..' && Ets_affiliatemarketing::isImageName($image))
                            $zip->addFile($bannerDir . $image, $image);
                    }
                }
            }
            $zip->close();
            if (!is_file($cacheDir . $zip_file_name)) {
                $errors[] = $this->l(sprintf('Could not create %1s', $cacheDir . $zip_file_name));
            }
            if (!$errors) {
                if (ob_get_length() > 0) {
                    ob_end_clean();
                }
                ob_start();
                header('Pragma: public');
                header('Expires: 0');
                header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
                header('Cache-Control: public');
                header('Content-Description: File Transfer');
                header('Content-type: application/octet-stream');
                header('Content-Disposition: attachment; filename="' . $zip_file_name . '"');
                header('Content-Transfer-Encoding: binary');
                ob_end_flush();
                readfile($cacheDir . $zip_file_name);
                if (file_exists($cacheDir . $zip_file_name))
                    @unlink($cacheDir . $zip_file_name);
                exit;
            }
        }
        return $errors;
    }
    protected function exportData()
    {
        $id_lang_default = Configuration::get('PS_LANG_DEFAULT');
        $xml_output = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml_output .= '<entity_profile>' . "\n";
        //Export
        $aff_reward_configs = Db::getInstance()->executeS("SELECT * FROM " . _DB_PREFIX_ . "ets_am_aff_reward");
        foreach ($aff_reward_configs as $key => $data) {
            $xml_output .= '<ets_am_aff_reward id_ets_am_aff_reward="' . $data['id_ets_am_aff_reward'] . '" id_product="' . $data['id_product'] . '" id_shop="' . $data['id_shop'] . '" use_default="' . $data['use_default'] . '" how_to_calculate="' . $data['how_to_calculate'] . '" default_percentage="' . $data['default_percentage'] . '" default_fixed_amount="' . $data['default_fixed_amount'] . '" >';
            $xml_output .= '</ets_am_aff_reward>' . "\n";
        }
        $loy_reward_configs = Db::getInstance()->executeS("SELECT * FROM " . _DB_PREFIX_ . "ets_am_loy_reward");
        foreach ($loy_reward_configs as $key => $data) {
            $xml_output .= '<ets_am_loy_reward id_ets_am_loy_reward="' . $data['id_ets_am_loy_reward'] . '" id_product="' . $data['id_product'] . '" id_shop="' . $data['id_shop'] . '" use_default="' . $data['use_default'] . '" base_on="' . $data['base_on'] . '" amount="' . $data['amount'] . '" amount_per="' . $data['amount_per'] . '" gen_percent="' . $data['gen_percent'] . '" >';
            $xml_output .= '</ets_am_loy_reward>' . "\n";
        }
        $banner_configs = Db::getInstance()->executeS("SELECT * FROM " . _DB_PREFIX_ . "ets_am_banner");
        foreach ($banner_configs as $key => $data) {
            $xml_output .= '<ets_am_banner id_ets_am_banner="' . $data['id_ets_am_banner'] . '" id_sponsor="' . $data['id_sponsor'] . '" datetime_added="' . $data['datetime_added'] . '" img="' . $data['img'] . '">';
            $xml_output .= '</ets_am_banner>' . "\n";
        }
        $invitation_configs = Db::getInstance()->executeS("SELECT * FROM " . _DB_PREFIX_ . "ets_am_invitation");
        foreach ($invitation_configs as $key => $data) {
            $xml_output .= '<ets_am_invitation id_ets_am_invitation="' . $data['id_ets_am_invitation'] . '" email="' . $data['email'] . '" name="' . $data['name'] . '" datetime_sent="' . $data['datetime_sent'] . '" id_friend="' . $data['id_friend'] . '" id_sponsor="' . $data['id_sponsor'] . '">';
            $xml_output .= '</ets_am_invitation>' . "\n";
        }
        $participation_configs = Db::getInstance()->executeS("SELECT * FROM " . _DB_PREFIX_ . "ets_am_participation");
        foreach ($participation_configs as $key => $data) {
            $xml_output .= '<ets_am_participation id_ets_am_participation="' . $data['id_ets_am_participation'] . '" id_customer="' . $data['id_customer'] . '" datetime_added="' . $data['datetime_added'] . '" status="' . $data['status'] . '" program="' . $data['program'] . '" id_shop="' . $data['id_shop'] . '" intro="' . $data['intro'] . '">';
            $xml_output .= '</ets_am_participation>' . "\n";
        }
        $payment_method_configs = Db::getInstance()->executeS("SELECT * FROM " . _DB_PREFIX_ . "ets_am_payment_method");
        foreach ($payment_method_configs as $key => $data) {
            $xml_output .= '<ets_am_payment_method id_ets_am_payment_method="' . $data['id_ets_am_payment_method'] . '" id_shop="' . $data['id_shop'] . '" fee_type="' . $data['fee_type'] . '" fee_fixed="' . $data['fee_fixed'] . '" fee_percent="' . $data['fee_percent'] . '" estimated_processing_time="' . $data['estimated_processing_time'] . '" enable="' . $data['enable'] . '" deleted="' . $data['deleted'] . '" sort="' . $data['sort'] . '">' . "\n";
            $payment_method_lang_configs = $this->getPaymentMethodLang($data['id_ets_am_payment_method']);
            foreach ($payment_method_lang_configs as $d) {
                $xml_output .= '<ets_am_payment_method_lang id_ets_am_payment_method_lang="' . $d['id_ets_am_payment_method_lang'] . '" id_payment_method="' . $d['id_payment_method'] . '" id_lang="' . $d['id_lang'] . '" iso_code="' . $d['iso_code'] . '" default="' . ($id_lang_default == (int)$d['id_lang'] ? 1 : 0) . '">';
                $xml_output .= '<title><![CDATA[' . $d['title'] . ']]></title>';
                $xml_output .= '<description><![CDATA[' . $d['description'] . ']]></description>';
                $xml_output .= '<note><![CDATA[' . $d['note'] . ']]></note>';
                $xml_output .= '</ets_am_payment_method_lang>' . "\n";
            }
            $xml_output .= '</ets_am_payment_method>' . "\n";
        }
        $payment_method_field_configs = Db::getInstance()->executeS("SELECT * FROM " . _DB_PREFIX_ . "ets_am_payment_method_field");
        foreach ($payment_method_field_configs as $key => $data) {
            $xml_output .= '<ets_am_payment_method_field id_ets_am_payment_method_field="' . $data['id_ets_am_payment_method_field'] . '" id_payment_method="' . $data['id_payment_method'] . '" type="' . $data['type'] . '" enable="' . $data['enable'] . '" required="' . $data['required'] . '" deleted="' . $data['deleted'] . '" sort="' . $data['sort'] . '">' . "\n";
            $payment_method_field_lang_configs = $this->getPaymentMethodFieldLang($data['id_ets_am_payment_method_field']);
            foreach ($payment_method_field_lang_configs as $d) {
                $xml_output .= '<ets_am_payment_method_field_lang id_ets_am_payment_method_field_lang="' . $d['id_ets_am_payment_method_field_lang'] . '" id_payment_method_field="' . $d['id_payment_method_field'] . '" id_lang="' . $d['id_lang'] . '" iso_code="' . $d['iso_code'] . '" default="' . ($id_lang_default == (int)$d['id_lang'] ? 1 : 0) . '">';
                $xml_output .= '<title><![CDATA[' . $d['title'] . ']]></title>';
                $xml_output .= '<description><![CDATA[' . $d['description'] . ']]></description>';
                $xml_output .= '</ets_am_payment_method_field_lang>' . "\n";
            }
            $xml_output .= '</ets_am_payment_method_field>' . "\n";
        }
        $product_view_configs = Db::getInstance()->executeS("SELECT * FROM " . _DB_PREFIX_ . "ets_am_product_view");
        foreach ($product_view_configs as $key => $data) {
            $xml_output .= '<ets_am_product_view id_ets_am_product_view="' . $data['id_ets_am_product_view'] . '" count="' . $data['count'] . '" id_product="' . $data['id_product'] . '" id_seller="' . $data['id_seller'] . '" date_added="' . $data['date_added'] . '">';
            $xml_output .= '</ets_am_product_view>' . "\n";
        }
        $reward_configs = Db::getInstance()->executeS("SELECT * FROM " . _DB_PREFIX_ . "ets_am_reward");
        foreach ($reward_configs as $key => $data) {
            $xml_output .= '<ets_am_reward id_ets_am_reward="' . $data['id_ets_am_reward'] . '" amount="' . $data['amount'] . '" program="' . $data['program'] . '" sub_program="' . $data['sub_program'] . '" status="' . $data['status'] . '" datetime_added="' . $data['datetime_added'] . '" datetime_validated="' . $data['datetime_validated'] . '" expired_date="' . $data['expired_date'] . '" datetime_canceled="' . $data['datetime_canceled'] . '" note="' . $data['note'] . '" id_customer="' . $data['id_customer'] . '" id_friend="' . $data['id_friend'] . '" id_order="' . $data['id_order'] . '" id_shop="' . $data['id_shop'] . '" id_currency="' . $data['id_currency'] . '" await_validate="' . $data['await_validate'] . '" send_expired_email="' . $data['send_expired_email'] . '" send_going_expired_email="' . $data['send_going_expired_email'] . '" last_modified="' . $data['last_modified'] . '" deleted="' . $data['deleted'] . '" >';
            $xml_output .= '</ets_am_reward>' . "\n";
        }
        $reward_product_configs = Db::getInstance()->executeS("SELECT * FROM " . _DB_PREFIX_ . "ets_am_reward_product");
        foreach ($reward_product_configs as $key => $data) {
            $xml_output .= '<ets_am_reward_product id_ets_am_reward_product="' . $data['id_ets_am_reward_product'] . '" id_product="' . $data['id_product'] . '" id_ets_am_reward="' . $data['id_ets_am_reward'] . '" program="' . $data['program'] . '" quantity="' . $data['quantity'] . '" amount="' . $data['amount'] . '" datetime_added="' . $data['datetime_added'] . '" id_seller = "' . (int)$data['id_seller'] . '" id_order="' . (int)$data['id_order'] . '">';
            $xml_output .= '</ets_am_reward_product>' . "\n";
        }
        $reward_usage_configs = Db::getInstance()->executeS("SELECT * FROM " . _DB_PREFIX_ . "ets_am_reward_usage");
        foreach ($reward_usage_configs as $key => $data) {
            $xml_output .= '<ets_am_reward_usage id_ets_am_reward_usage="' . $data['id_ets_am_reward_usage'] . '" amount="' . $data['amount'] . '" id_customer="' . $data['id_customer'] . '" id_shop="' . $data['id_shop'] . '" id_order="' . $data['id_order'] . '" id_withdraw="' . $data['id_withdraw'] . '" id_voucher="' . $data['id_voucher'] . '" id_currency="' . $data['id_currency'] . '" status="' . $data['status'] . '" note="' . $data['note'] . '" datetime_added="' . $data['datetime_added'] . '" deleted="' . $data['deleted'] . '" type="' . $data['type'] . '">';
            $xml_output .= '</ets_am_reward_usage>' . "\n";
        }
        $sponsor_configs = Db::getInstance()->executeS("SELECT * FROM " . _DB_PREFIX_ . "ets_am_sponsor");
        foreach ($sponsor_configs as $key => $data) {
            $xml_output .= '<ets_am_sponsor id_ets_am_sponsor="' . $data['id_ets_am_sponsor'] . '" id_customer="' . $data['id_customer'] . '" id_parent="' . $data['id_parent'] . '" level="' . $data['level'] . '" id_shop="' . $data['id_shop'] . '" datetime_added="' . $data['datetime_added'] . '">';
            $xml_output .= '</ets_am_sponsor>' . "\n";
        }
        $user_configs = Db::getInstance()->executeS("SELECT * FROM " . _DB_PREFIX_ . "ets_am_user");
        foreach ($user_configs as $key => $data) {
            $xml_output .= '<ets_am_user id_ets_am_user="' . $data['id_ets_am_user'] . '" id_customer="' . $data['id_customer'] . '" loy="' . $data['loy'] . '" aff="' . $data['aff'] . '" ref="' . $data['ref'] . '" status="' . $data['status'] . '" id_shop="' . $data['id_shop'] . '">';
            $xml_output .= '</ets_am_user>' . "\n";
        }
        $voucher_configs = Db::getInstance()->executeS("SELECT * FROM " . _DB_PREFIX_ . "ets_am_voucher");
        foreach ($voucher_configs as $key => $data) {
            $xml_output .= '<ets_am_voucher id_ets_am_voucher="' . $data['id_ets_am_voucher'] . '" id_cart_rule="' . $data['id_cart_rule'] . '" id_customer="' . $data['id_customer'] . '" id_product="' . $data['id_product'] . '" id_cart="' . $data['id_cart'] . '">';
            $xml_output .= '</ets_am_voucher>' . "\n";
        }
        $withdrawal_configs = Db::getInstance()->executeS("SELECT * FROM " . _DB_PREFIX_ . "ets_am_withdrawal");
        foreach ($withdrawal_configs as $key => $data) {
            $xml_output .= '<ets_am_withdrawal id_ets_am_withdrawal="' . $data['id_ets_am_withdrawal'] . '" id_payment_method="' . $data['id_payment_method'] . '" invoice="' . $data['invoice'] . '" status="' . $data['status'] . '">';
            $xml_output .= '</ets_am_withdrawal>' . "\n";
        }
        $withdrawal_field_configs = Db::getInstance()->executeS("SELECT * FROM " . _DB_PREFIX_ . "ets_am_withdrawal_field");
        foreach ($withdrawal_field_configs as $key => $data) {
            $xml_output .= '<ets_am_withdrawal_field id_ets_am_withdrawal_field="' . $data['id_ets_am_withdrawal_field'] . '" id_withdrawal="' . $data['id_withdrawal'] . '" id_payment_method_field="' . $data['id_payment_method_field'] . '" value="' . $data['value'] . '">';
            $xml_output .= '</ets_am_withdrawal_field>' . "\n";
        }
        //Export configs
        $xml_output .= '<configuration>' . "\n";
        $defined = new EtsAffDefine();
        $def_config_tabs = $defined->def_config_tabs();
        foreach ($def_config_tabs as $config_key => $config_data) {
            if (isset($config_data['subtabs']) && $config_data['subtabs']) {
                foreach ($config_data['subtabs'] as $key => $config) {
                    if ($config) {
                        //
                    }
                    $func = 'def_' . $key;
                    if (!method_exists($defined, $func)) {
                        continue;
                    }
                    $conf = $defined->{$func}();
                    if (isset($conf['form']) && isset($conf['config'])) {
                        $xml_output .= $this->exportXmlConfiguration($conf['config']);
                    }
                }
            } else {
                $func = 'def_' . $config_key;
                if (!method_exists($defined, $func)) {
                    continue;
                }
                $conf = $defined->{$func}();
                if (isset($conf['form']) && isset($conf['config'])) {
                    $xml_output .= $this->exportXmlConfiguration($conf['config']);
                }
            }
        }
        $xml_output .= '</configuration>' . "\n";
        $xml_output .= '</entity_profile>' . "\n";
        $xml_output = str_replace('&', 'and', $xml_output);
        return $xml_output;
    }
    protected function getPaymentMethodLang($id_pm)
    {
        return Db::getInstance()->executeS("SELECT pml.*, lang.iso_code as iso_code 
    		FROM `" . _DB_PREFIX_ . "ets_am_payment_method_lang` pml
			LEFT JOIN `" . _DB_PREFIX_ . "lang` lang ON pml.id_lang = lang.id_lang 
    		WHERE pml.id_payment_method = " . (int)$id_pm);
    }
    protected function getPaymentMethodFieldLang($id_pmf)
    {
        return Db::getInstance()->executeS("SELECT pmfl.*, lang.iso_code as iso_code 
    		FROM `" . _DB_PREFIX_ . "ets_am_payment_method_field_lang` pmfl
    		LEFT JOIN `" . _DB_PREFIX_ . "lang` lang ON pmfl.id_lang = lang.id_lang 
    		WHERE pmfl.id_payment_method_field = " . (int)$id_pmf);
    }
    public function exportXmlConfiguration($configs)
    {
        $languages = Language::getLanguages(false);
        $id_lang_default = Configuration::get('PS_LANG_DEFAULT');
        $xml_output = '';
        if ($configs) {
            foreach ($configs as $key => $config) {
                if (isset($config['lang']) && $config['lang']) {
                    $xml_output .= '<' . $key . '>' . "\n";
                    foreach ($languages as $language) {
                        $xml_output .= '<language iso_code="' . $language['iso_code'] . '"' . ($language['id_lang'] == $id_lang_default ? ' default="1"' : ' default="0"') . '>' . "\n";
                        $xml_output .= '<content><![CDATA[' . Configuration::get($key, $language['id_lang'], null, null, false) . ']]></content>' . "\n";
                        $xml_output .= '</language>' . "\n";
                    }
                    $xml_output .= '</' . $key . '>' . "\n";
                } else {
                    $xml_output .= '<' . $key . '><![CDATA[' . Configuration::get($key) . ']]></' . $key . '>' . "\n";
                }
            }
        }
        return $xml_output;
    }
    public function copyBanner($src, $dst)
    {
        if (is_dir($src)) {
            if ($images = scandir($src)) {
                foreach ($images as $image) {
                    if ($image != '.' && $image != '..' && Ets_affiliatemarketing::isImageName($image))
                        @copy($src . '/' . $image, $dst . '/' . $image);
                }
            }
        }
    }
    public function processImport($zipfile = false,$enable_restore_reward = true,$enable_restore_config = true,$enable_delete_reward = true)
    {
        $errors = array();
        $savePath = _PS_CACHE_DIR_ . 'ets_affiliatemarketing/';
        $bannerDir = EAM_PATH_IMAGE_BANER;
        if (is_dir($savePath))
            Ets_affiliatemarketing::removeDir($savePath);
        @mkdir($savePath, 0755, true);
        if (!is_dir($bannerDir))
            @mkdir($bannerDir, 0755, true);
        if (@file_exists($savePath . 'ets_affiliatemarketing.data.zip')) {
            @unlink($savePath . 'ets_affiliatemarketing.data.zip');
        }
        if (isset($_FILES['import_source']) && $_FILES['import_source']) {
            if (!$zipfile) {
                $uploader = new Uploader('import_source');
                $uploader->setMaxSize(1048576000);
                $uploader->setAcceptTypes(array('zip'));
                $uploader->setSavePath($savePath);
                $file = $uploader->process('ets_affiliatemarketing.data.zip');
                if ($file[0]['error'] === 0) {
                    if (!Tools::ZipTest($savePath . 'ets_affiliatemarketing.data.zip'))
                        $errors[] = $this->l('Zip file seems to be broken');
                } else {
                    $errors[] = $file[0]['error'];
                }
                $extractUrl = $savePath . 'ets_affiliatemarketing.data.zip';
            } else
                $extractUrl = $zipfile;
            if (!@file_exists($extractUrl))
                $errors[] = $this->l('Zip file doesn\'t exist');
            if (!$errors) {
                $zip = new ZipArchive();
                if ($zip->open($extractUrl) === true) {
                    if ($zip->locateName('eam_data.xml') === false) {
                        $errors[] = $this->l('eam_data.xml doesn\'t exist');
                        if ($extractUrl && !$zipfile) {
                            if (file_exists($extractUrl))
                                @unlink($extractUrl);
                        }
                    }
                } else
                    $errors[] = $this->l('Cannot open zip file. It might be broken or damaged');
                $zip->close();
            }
            if (!$errors) {
                if (!Tools::ZipExtract($extractUrl, $savePath))
                    $errors[] = $this->l('Cannot extract zip data');
                if (!@file_exists($savePath . 'eam_data.xml')) {
                    $errors[] = $this->l('Neither eam_data.xml exist');
                    Ets_affiliatemarketing::removeDir($savePath);
                }
            }
            if (!$errors) {
                if ($this->importData($savePath . 'eam_data.xml',$enable_restore_reward,$enable_restore_config,$enable_delete_reward))
                    $this->copyBanner($savePath, $bannerDir);
                Ets_affiliatemarketing::removeDir($savePath);
            }
        } else {
            $errors[] = $this->l('Data import is null');
        }
        return $errors;
    }
    public function importData($file_xml,$enable_restore_reward = true,$enable_restore_config = true,$enable_delete_reward = true)
    {
        if (file_exists($file_xml)) {
            $xml = simplexml_load_file($file_xml);
            if ($xml === false)
                return false;
            $payment_methods = array();
            $payment_method_fields = array();
            $rewards = array();
            $withdrawals = array();
            $vouchers = array();
            if ($enable_restore_reward) {
                if ($enable_delete_reward) {
                    //Delete all reward after import data
                    $this->deleteAllRewardData();
                }
                if (isset($xml->ets_am_aff_reward) && $xml->ets_am_aff_reward) {
                    foreach ($xml->ets_am_aff_reward as $xml_aff_reward) {
                        $aff_reward_configs = $this->getAffiliateConfig($xml_aff_reward['id_product'], $xml_aff_reward['id_shop']);
                        $aff_reward_configs->id_product = (int)$xml_aff_reward['id_product'];
                        $aff_reward_configs->id_shop = (int)$xml_aff_reward['id_shop'];
                        $aff_reward_configs->use_default = (int)$xml_aff_reward['use_default'];
                        $aff_reward_configs->how_to_calculate = $this->ets_is_sanitizeXML((string)$xml_aff_reward['how_to_calculate']);
                        $aff_reward_configs->default_percentage = (float)$xml_aff_reward['default_percentage'];
                        $aff_reward_configs->default_fixed_amount = (float)$xml_aff_reward['default_fixed_amount'];
                        $aff_reward_configs->save();
                    }
                }
                if (isset($xml->ets_am_loy_reward) && $xml->ets_am_loy_reward) {
                    foreach ($xml->ets_am_loy_reward as $xml_loy_reward) {
                        $loy_reward_configs = $this->getLoyaltyConfig($xml_loy_reward['id_product'], $xml_loy_reward['id_shop']);
                        $loy_reward_configs->id_product = (int)$xml_loy_reward['id_product'];
                        $loy_reward_configs->id_shop = (int)$xml_loy_reward['id_shop'];
                        $loy_reward_configs->use_default = (int)$xml_loy_reward['use_default'];
                        $loy_reward_configs->base_on = $this->ets_is_sanitizeXML((string)$xml_loy_reward['base_on']);
                        $loy_reward_configs->amount = (float)$xml_loy_reward['amount'];
                        $loy_reward_configs->amount_per = (float)$xml_loy_reward['amount_per'];
                        $loy_reward_configs->gen_percent = (float)$xml_loy_reward['gen_percent'];
                        $loy_reward_configs->qty_min = (int)$xml_loy_reward['qty_min'];
                        $loy_reward_configs->save();
                    }
                }
                if (isset($xml->ets_am_banner) && $xml->ets_am_banner) {
                    foreach ($xml->ets_am_banner as $xml_banner) {
                        $banner_configs = new Ets_Banner($xml_banner['id_ets_am_banner']);
                        $banner_configs->id_sponsor = (int)$xml_banner['id_sponsor'];
                        $banner_configs->datetime_added = (string)$xml_banner['datetime_added'] && (string)$xml_banner['datetime_added'] !== '0000-00-00 00:00:00' ? (string)$xml_banner['datetime_added'] : null;
                        $banner_configs->img = (string)$xml_banner['img'];
                        $banner_configs->save();
                    }
                }
                if (isset($xml->ets_am_invitation) && $xml->ets_am_invitation) {
                    foreach ($xml->ets_am_invitation as $xml_invitation) {
                        $invitation_configs = new Ets_Invitation($xml_invitation['id_ets_am_invitation']);
                        $invitation_configs->email = (int)$xml_invitation['email'];
                        $invitation_configs->name = $this->ets_is_sanitizeXML((string)$xml_invitation['name']);
                        $invitation_configs->datetime_sent = (string)$xml_invitation['datetime_sent'] && (string)$xml_invitation['datetime_sent'] !== '0000-00-00 00:00:00' ? (string)$xml_invitation['datetime_sent'] : null;
                        $invitation_configs->id_friend = (int)$xml_invitation['id_friend'];
                        $invitation_configs->id_sponsor = (int)$xml_invitation['id_sponsor'];
                        $invitation_configs->save();
                    }
                }
                if (isset($xml->ets_am_participation) && $xml->ets_am_participation) {
                    foreach ($xml->ets_am_participation as $xml_participation) {
                        $participation_configs = new Ets_Participation($xml_participation['id_ets_am_participation']);
                        $participation_configs->id_customer = (int)$xml_participation['id_customer'];
                        $participation_configs->datetime_added = (string)$xml_participation['datetime_added'] && (string)$xml_participation['datetime_added'] !== '0000-00-00 00:00:00' ? (string)$xml_participation['datetime_added'] : null;
                        $participation_configs->status = (int)$xml_participation['status'];
                        $participation_configs->program = (string)$xml_participation['program'];
                        $participation_configs->id_shop = (int)$xml_participation['id_shop'];
                        $participation_configs->intro = $this->ets_is_sanitizeXML((string)$xml_participation['intro']);
                        $participation_configs->save();
                    }
                }
                if (isset($xml->ets_am_payment_method) && $xml->ets_am_payment_method) {
                    foreach ($xml->ets_am_payment_method as $xml_payment_method) {
                        $payment_method_configs = new Ets_PaymentMethod($xml_payment_method['id_ets_am_payment_method']);
                        $payment_method_configs->id_ets_am_payment_method = (int)$xml_payment_method['id_ets_am_payment_method'];
                        $payment_method_configs->id_shop = (int)$xml_payment_method['id_shop'];
                        $payment_method_configs->fee_type = $this->ets_is_sanitizeXML((string)$xml_payment_method['fee_type']);
                        $payment_method_configs->fee_fixed = (float)$xml_payment_method['fee_fixed'];
                        $payment_method_configs->fee_percent = (float)$xml_payment_method['fee_percent'];
                        $payment_method_configs->estimated_processing_time = (int)$xml_payment_method['estimated_processing_time'];
                        $payment_method_configs->enable = (int)$xml_payment_method['enable'];
                        $payment_method_configs->deleted = (int)$xml_payment_method['deleted'];
                        $payment_method_configs->sort = (int)$xml_payment_method['sort'];
                        $payment_method_configs->save();
                        $payment_methods[(int)$xml_payment_method['id_ets_am_payment_method']] = $payment_method_configs->id;
                        if (isset($xml_payment_method->ets_am_payment_method_lang) && $xml_payment_method->ets_am_payment_method_lang) {
                            $id_lang_default = (int)Configuration::get('PS_LANG_DEFAULT');
                            $set_default_lang = false;
                            foreach ($xml_payment_method->ets_am_payment_method_lang as $xml_pml) {
                                if ((string)$xml_pml['iso_code']) {
                                    $id_lang = Language::getIdByIso((string)$xml_pml['iso_code']);
                                    if ($id_lang) {
                                        if ($id_lang == $id_lang_default) {
                                            $set_default_lang = true;
                                        }
                                        $id_pml = $this->getIdPaymentMethodLang($payment_method_configs->id, $id_lang);
                                        if ($id_pml) {
                                            $sql_lang = "UPDATE `" . _DB_PREFIX_ . "ets_am_payment_method_lang` SET title = '" . pSQL($this->ets_is_sanitizeXML((string)$xml_pml->title)) . "', description = '" . pSQL($this->ets_is_sanitizeXML((string)$xml_pml->description)) . "', note = '" . pSQL($this->ets_is_sanitizeXML((string)$xml_pml->note)) . "', id_lang = $id_lang WHERE id_ets_am_payment_method_lang = " . (int)$id_pml;
                                            Db::getInstance()->execute($sql_lang);
                                        } else {
                                            $sql_lang = "INSERT INTO `" . _DB_PREFIX_ . "ets_am_payment_method_lang` (`id_payment_method`, `title`, `description`, `note`, `id_lang`) VALUES (" . (int)$payment_method_configs->id . ", '" . pSQL($this->ets_is_sanitizeXML((string)$xml_pml->title)) . "', '" . pSQL($this->ets_is_sanitizeXML((string)$xml_pml->description)) . "', '" . pSQL($this->ets_is_sanitizeXML((string)$xml_pml->note)) . "', $id_lang)";
                                            Db::getInstance()->execute($sql_lang);
                                        }
                                    }
                                }
                            }
                            if (!$set_default_lang) {
                                foreach ($xml_payment_method->ets_am_payment_method_lang as $xml_pml) {
                                    if ((int)$xml_pml['default']) {
                                        $id_pml = $this->getIdPaymentMethodLang($payment_method_configs->id, $id_lang_default);
                                        if ($id_pml) {
                                            $sql_lang = "UPDATE `" . _DB_PREFIX_ . "ets_am_payment_method_lang` SET title = '" . pSQL($xml_pml->title) . "', description = '" . pSQL($xml_pml->description) . "', note = '" . pSQL($xml_pml->note) . "', id_lang=" . (int)$id_lang . " WHERE id_ets_am_payment_method_lang = " . (int)$id_pml;
                                            Db::getInstance()->execute($sql_lang);
                                        } else {
                                            $sql_lang = "INSERT INTO `" . _DB_PREFIX_ . "ets_am_payment_method_lang` (`id_payment_method`, `title`, `description`, `note`, `id_lang`) VALUES (" . (int)$payment_method_configs->id . ", '" . (string)$xml_pml->title . "', '" . (string)$xml_pml->description . "', '" . (string)$xml_pml->note . "', $id_lang)";
                                            Db::getInstance()->execute($sql_lang);
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
                if (isset($xml->ets_am_payment_method_field) && $xml->ets_am_payment_method_field) {
                    foreach ($xml->ets_am_payment_method_field as $xml_payment_method_field) {
                        $payment_method_field_configs = $this->getPaymentMethodField((int)$payment_methods[(int)$xml_payment_method_field['id_payment_method']], (int)$xml_payment_method_field['id_ets_am_payment_method_field']);
                        if (!$payment_method_field_configs->id_ets_am_payment_method_field) {
                            $payment_method_field_configs->id_ets_am_payment_method_field = (int)$xml_payment_method_field['id_ets_am_payment_method_field'];
                        }
                        $payment_method_field_configs->id_payment_method = (int)$payment_methods[(int)$xml_payment_method_field['id_payment_method']];
                        $payment_method_field_configs->type = $this->ets_is_sanitizeXML((string)$xml_payment_method_field['type']);
                        $payment_method_field_configs->sort = (int)$xml_payment_method_field['sort'];
                        $payment_method_field_configs->required = (int)$xml_payment_method_field['required'];
                        $payment_method_field_configs->enable = (int)$xml_payment_method_field['enable'];
                        $payment_method_field_configs->deleted = (int)$xml_payment_method_field['deleted'];
                        $payment_method_field_configs->save();
                        $payment_method_fields[(int)$xml_payment_method_field['id_ets_am_payment_method_field']] = $payment_method_field_configs->id;
                        if (isset($xml_payment_method_field->ets_am_payment_method_field_lang) && $xml_payment_method_field->ets_am_payment_method_field_lang) {
                            $id_lang_default = (int)Configuration::get('PS_LANG_DEFAULT');
                            $set_default_lang = false;
                            foreach ($xml_payment_method_field->ets_am_payment_method_field_lang as $xml_pmfl) {
                                if ((string)$xml_pmfl['iso_code']) {
                                    $id_lang = Language::getIdByIso((string)$xml_pmfl['iso_code']);
                                    if ($id_lang) {
                                        if ($id_lang == $id_lang_default) {
                                            $set_default_lang = true;
                                        }
                                        $id_pmfl = $this->getIdPaymentMethodFieldLang($payment_method_field_configs->id, $id_lang);
                                        if ($id_pmfl) {
                                            $sql_lang = "UPDATE `" . _DB_PREFIX_ . "ets_am_payment_method_field_lang` SET title = '" . pSQL($this->ets_is_sanitizeXML((string)$xml_pmfl->title)) . "', description = '" . pSQL($this->ets_is_sanitizeXML((string)$xml_pmfl->description)) . "', id_lang = $id_lang WHERE id_ets_am_payment_method_field_lang = " . (int)$id_pmfl;
                                            Db::getInstance()->execute($sql_lang);
                                        } else {
                                            $sql_lang = "INSERT INTO `" . _DB_PREFIX_ . "ets_am_payment_method_field_lang` (`id_payment_method_field`, `id_lang`,`title`, `description`) VALUES (" . (int)$payment_method_field_configs->id . ", " . (int)$id_lang . ",'" . pSql($this->ets_is_sanitizeXML((string)$xml_pmfl->title)) . "', '" . pSQL($this->ets_is_sanitizeXML((string)$xml_pmfl->description)) . "')";
                                            Db::getInstance()->execute($sql_lang);
                                        }
                                    }
                                }
                            }
                            if (!$set_default_lang) {
                                foreach ($xml_payment_method->id_ets_am_payment_method_field_lang as $xml_pmfl) {
                                    if ((int)$xml_pmfl['default']) {
                                        $id_pmfl = $this->getIdPaymentMethodFieldLang($payment_method_field_configs->id, $id_lang_default);
                                        if ($id_pmfl) {
                                            $sql_lang = "UPDATE `" . _DB_PREFIX_ . "ets_am_payment_method_field_lang` SET title = '" . pSQL($this->ets_is_sanitizeXML((string)$xml_pmfl->title)) . "', description = '" . pSQL($this->ets_is_sanitizeXML((string)$xml_pmfl->description)) . "', id_lang = $id_lang_default WHERE id_ets_am_payment_method_field_lang = " . (int)$id_pmfl;
                                            Db::getInstance()->execute($sql_lang);
                                        } else {
                                            $sql_lang = "INSERT INTO `" . _DB_PREFIX_ . "ets_am_payment_method_field_lang` (`id_payment_method_field`, `id_lang`,`title`, `description`) VALUES (" . (int)$payment_method_field_configs->id . ", " . (int)$id_lang_default . ",'" . pSQL($this->ets_is_sanitizeXML((string)$xml_pmfl->title)) . "', '" . pSQL($this->ets_is_sanitizeXML((string)$xml_pmfl->description)) . "')";
                                            Db::getInstance()->execute($sql_lang);
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
                if (isset($xml->ets_am_product_view) && $xml->ets_am_product_view) {
                    foreach ($xml->ets_am_product_view as $xml_product_view) {
                        $product_view_configs = new Ets_Product_View((int)$xml_product_view['id_ets_am_product_view']);
                        $product_view_configs->count = (int)$xml_product_view['count'];
                        $product_view_configs->id_product = (int)$xml_product_view['id_product'];
                        $product_view_configs->id_seller = (int)$xml_product_view['id_seller'];
                        $product_view_configs->date_added = (string)$xml_product_view['date_added'] && (string)$xml_product_view['date_added'] !== '0000-00-00 00:00:00' ? (string)$xml_product_view['date_added'] : null;
                        $product_view_configs->save();
                    }
                }
                if (isset($xml->ets_am_reward) && $xml->ets_am_reward) {
                    foreach ($xml->ets_am_reward as $xml_reward) {
                        $reward_configs = new Ets_AM((int)$xml_reward['id_ets_am_reward']);
                        $reward_configs->amount = (float)$xml_reward['amount'];
                        $reward_configs->program = (string)$xml_reward['program'];
                        $reward_configs->sub_program = (string)$xml_reward['sub_program'];
                        $reward_configs->status = (int)$xml_reward['status'];
                        $reward_configs->datetime_added = (string)$xml_reward['datetime_added'] && (string)$xml_reward['datetime_added'] !== '0000-00-00 00:00:00' ? (string)$xml_reward['datetime_added'] : null;
                        $reward_configs->datetime_validated = (string)$xml_reward['datetime_validated'] && (string)$xml_reward['datetime_validated'] !== '0000-00-00 00:00:00' ? (string)$xml_reward['datetime_validated'] : null;
                        $reward_configs->expired_date = (string)$xml_reward['expired_date'] && (string)$xml_reward['expired_date'] !== '0000-00-00 00:00:00' ? (string)$xml_reward['expired_date'] : null;
                        $reward_configs->datetime_canceled = (string)$xml_reward['datetime_canceled'] && (string)$xml_reward['datetime_canceled'] !== '0000-00-00 00:00:00' ? (string)$xml_reward['datetime_canceled'] : null;
                        $reward_configs->note = (string)$xml_reward['note'];
                        $reward_configs->id_customer = (int)$xml_reward['id_customer'];
                        $reward_configs->id_friend = (int)$xml_reward['id_friend'];
                        $reward_configs->id_order = (int)$xml_reward['id_order'];
                        $reward_configs->id_shop = (int)$xml_reward['id_shop'];
                        $reward_configs->id_currency = (int)$xml_reward['id_currency'];
                        $reward_configs->await_validate = (int)$xml_reward['await_validate'];
                        $reward_configs->send_expired_email = (string)$xml_reward['send_expired_email'] && (string)$xml_reward['send_expired_email'] !== '0000-00-00 00:00:00' ? (string)$xml_reward['send_expired_email'] : null;
                        $reward_configs->send_going_expired_email = (string)$xml_reward['send_going_expired_email'] && (string)$xml_reward['send_going_expired_email'] !== '0000-00-00 00:00:00' ? (string)$xml_reward['send_going_expired_email'] : null;
                        $reward_configs->last_modified = (string)$xml_reward['last_modified'] && (string)$xml_reward['last_modified'] !== '0000-00-00 00:00:00' ? (string)$xml_reward['last_modified'] : null;
                        $reward_configs->deleted = (int)$xml_reward['deleted'];
                        $reward_configs->save();
                        $rewards[(int)$xml_reward['id_ets_am_reward']] = $reward_configs->id;
                    }
                }
                if (isset($xml->ets_am_reward_product) && $xml->ets_am_reward_product) {
                    foreach ($xml->ets_am_reward_product as $xml_reward_product) {
                        $reward_product_configs = new Ets_Reward_Product((int)$xml_reward_product['id_ets_am_reward_product']);
                        $reward_product_configs->id_product = (int)$xml_reward_product['id_product'];
                        $reward_product_configs->id_ets_am_reward = isset($rewards[(int)$xml_reward_product['id_ets_am_reward']]) ? $rewards[(int)$xml_reward_product['id_ets_am_reward']] : (int)$xml_reward_product['id_ets_am_reward'];
                        $reward_product_configs->program = (string)$xml_reward_product['program'];
                        $reward_product_configs->quantity = (int)$xml_reward_product['quantity'];
                        $reward_product_configs->amount = (float)$xml_reward_product['amount'];
                        $reward_product_configs->id_seller = (int)$xml_reward_product['id_seller'];
                        $reward_product_configs->id_order = (int)$xml_reward_product['id_order'];
                        $reward_product_configs->datetime_added = (string)$xml_reward_product['datetime_added'] && (string)$xml_reward_product['datetime_added'] !== '0000-00-00 00:00:00' ? (string)$xml_reward_product['datetime_added'] : null;
                        $reward_product_configs->save();
                    }
                }
                if (isset($xml->ets_am_sponsor) && $xml->ets_am_sponsor) {
                    foreach ($xml->ets_am_sponsor as $xml_sponsor) {
                        $sponsor_configs = $this->getSponsorObject((int)$xml_sponsor['id_customer'], (int)$xml_sponsor['id_parent']);
                        $sponsor_configs->id_customer = (int)$xml_sponsor['id_customer'];
                        $sponsor_configs->id_parent = (int)$xml_sponsor['id_parent'];
                        $sponsor_configs->level = (int)$xml_sponsor['level'];
                        $sponsor_configs->id_shop = (int)$xml_sponsor['id_shop'];
                        $sponsor_configs->datetime_added = (string)$xml_sponsor['datetime_added'] && (string)$xml_sponsor['datetime_added'] !== '0000-00-00 00:00:00' ? (string)$xml_sponsor['datetime_added'] : null;
                        $sponsor_configs->save();
                    }
                }
                if (isset($xml->ets_am_user) && $xml->ets_am_user) {
                    foreach ($xml->ets_am_user as $xml_user) {
                        $user_configs = $this->getUserObject((int)$xml_user['id_customer'], (int)$xml_user['id_shop']);
                        $user_configs->id_customer = (int)$xml_user['id_customer'];
                        $user_configs->loy = (int)$xml_user['loy'];
                        $user_configs->ref = (int)$xml_user['ref'];
                        $user_configs->aff = (int)$xml_user['aff'];
                        $user_configs->status = (int)$xml_user['status'];
                        $user_configs->id_shop = (int)$xml_user['id_shop'];
                        $user_configs->save();
                    }
                }
                if (isset($xml->ets_am_voucher) && $xml->ets_am_voucher) {
                    foreach ($xml->ets_am_voucher as $xml_voucher) {
                        $voucher_configs = new Ets_Voucher((int)$xml_voucher['id_ets_am_voucher']);
                        $voucher_configs->id_cart_rule = (int)$xml_voucher['id_cart_rule'];
                        $voucher_configs->id_customer = (int)$xml_voucher['id_customer'];
                        $voucher_configs->id_product = (int)$xml_voucher['id_product'];
                        $voucher_configs->id_cart = (int)$xml_voucher['id_cart'];
                        $voucher_configs->save();
                        $vouchers[(int)$xml_voucher['id_ets_am_voucher']] = $voucher_configs->id;
                    }
                }
                if (isset($xml->ets_am_withdrawal) && $xml->ets_am_withdrawal) {
                    foreach ($xml->ets_am_withdrawal as $xml_withdrawal) {
                        $withdrawal_configs = new Ets_Withdraw((int)$xml_withdrawal['id_ets_am_withdrawal']);
                        $withdrawal_configs->id_payment_method = $payment_methods[(int)$xml_withdrawal['id_payment_method']];
                        $withdrawal_configs->invoice = (string)$xml_withdrawal['invoice'];
                        $withdrawal_configs->status = (int)$xml_withdrawal['status'];
                        $withdrawal_configs->save();
                        $withdrawals[(int)$xml_withdrawal['id_ets_am_withdrawal']] = $withdrawal_configs->id;
                    }
                }
                if (isset($xml->ets_am_withdrawal_field) && $xml->ets_am_withdrawal_field) {
                    foreach ($xml->ets_am_withdrawal_field as $xml_withdrawal_field) {
                        $withdrawal_field_configs = new Ets_Withdraw_Field((int)$xml_withdrawal_field['id_ets_am_withdrawal_field']);
                        $withdrawal_field_configs->id_withdrawal = $withdrawals[(int)$xml_withdrawal_field['id_withdrawal']];
                        $withdrawal_field_configs->id_payment_method_field = $payment_method_fields[(int)$xml_withdrawal_field['id_payment_method_field']];
                        $withdrawal_field_configs->value = (string)$xml_withdrawal_field['value'];
                        $withdrawal_field_configs->save();
                    }
                }
                if (isset($xml->ets_am_reward_usage) && $xml->ets_am_reward_usage) {
                    foreach ($xml->ets_am_reward_usage as $xml_reward_usage) {
                        $reward_usage_configs = new Ets_Reward_Usage((int)$xml_reward_usage['id_ets_am_reward_usage']);
                        $reward_usage_configs->amount = (float)$xml_reward_usage['amount'];
                        $reward_usage_configs->id_customer = (int)$xml_reward_usage['id_customer'];
                        $reward_usage_configs->id_shop = (int)$xml_reward_usage['id_shop'];
                        $reward_usage_configs->id_order = (int)$xml_reward_usage['id_order'];
                        $reward_usage_configs->id_withdraw = isset($withdrawals[(int)$xml_reward_usage['id_withdraw']]) ? $withdrawals[(int)$xml_reward_usage['id_withdraw']] : 0;
                        $reward_usage_configs->id_voucher = isset($vouchers[(int)$xml_reward_usage['id_voucher']]) ? $vouchers[(int)$xml_reward_usage['id_voucher']] : (int)$xml_reward_usage['id_voucher'];
                        $reward_usage_configs->id_currency = (int)$xml_reward_usage['id_currency'];
                        $reward_usage_configs->status = (int)$xml_reward_usage['status'] == -1 ? 0 : 1;
                        $reward_usage_configs->note = (string)$xml_reward_usage['note'];
                        $reward_usage_configs->datetime_added = (string)$xml_reward_usage['datetime_added'] && (string)$xml_reward_usage['datetime_added'] !== '0000-00-00 00:00:00' ? (string)$xml_reward_usage['datetime_added'] : null;
                        $reward_usage_configs->deleted = (int)$xml_reward_usage['deleted'];
                        $reward_usage_configs->type = (string)$xml_reward_usage['type'];
                        $reward_usage_configs->save();
                    }
                }
            }
            //Import configs
            if ($enable_restore_config) {
                if (isset($xml->configuration) && $xml->configuration) {
                    $defined = new EtsAffDefine();
                    $def_config_tabs = $defined->def_config_tabs();
                    foreach ($def_config_tabs as $config_key => $config_data) {
                        if (isset($config_data['subtabs']) && $config_data['subtabs']) {
                            foreach ($config_data['subtabs'] as $key => $config) {
                                if ($config) {
                                }
                                $func = 'def_' . $key;
                                if (!method_exists($defined, $func)) {
                                    continue;
                                }
                                $conf = $defined->{$func}();
                                if (isset($conf['form']) && isset($conf['config'])) {
                                    $this->importXmlConfigurations($conf['config'], $xml->configuration);
                                }
                            }
                        } else {
                            $func = 'def_' . $config_key;
                            if (!method_exists($defined, $func)) {
                                continue;
                            }
                            $conf = $defined->{$func}();
                            if (isset($conf['form']) && isset($conf['config'])) {
                                $this->importXmlConfigurations($conf['config'], $xml->configuration);
                            }
                        }
                    }
                }
            }
            return true;
        }
        return false;
    }
    public function importXmlConfigurations($configs, $xml)
    {
        $languages = Language::getLanguages(false);
        foreach ($configs as $key => $config) {
            if (isset($config['lang']) && $config['lang']) {
                $values = array();
                $xmlKey = $xml->{$key};
                $default = '';
                $languageimporteds = array();
                if ($xmlKey->language) {
                    foreach ($xmlKey->language as $language_xml) {
                        if ((int)$language_xml['default'])
                            $default = (string)$language_xml->content;
                        if ($id_lang = Language::getIdByIso((string)$language_xml['iso_code'])) {
                            $languageimporteds[] = $id_lang;
                            $values[$id_lang] = (string)$language_xml->content;
                        }
                    }
                }
                foreach ($languages as $lang) {
                    if (!in_array($lang['id_lang'], $languageimporteds)) {
                        $values[$lang['id_lang']] = $default;
                    }
                }
                Configuration::updateValue($key, $values);
            } else {
                if (isset($xml->{$key})) {
                    Configuration::updateValue($key, (string)$xml->{$key});
                }
            }
        }
    }
    protected function getAffiliateConfig($id_product, $id_shop)
    {
        $id = Db::getInstance()->getValue("SELECT id_ets_am_aff_reward FROM `" . _DB_PREFIX_ . "ets_am_aff_reward` WHERE id_product = " . (int)$id_product . " AND id_shop = " . (int)$id_shop);
        if ($id) {
            return new Ets_Affiliate_Config((int)$id);
        }
        return new Ets_Affiliate_Config();
    }
    protected function getLoyaltyConfig($id_product, $id_shop)
    {
        $id = Db::getInstance()->getValue("SELECT id_ets_am_loy_reward FROM `" . _DB_PREFIX_ . "ets_am_loy_reward` WHERE id_product = " . (int)$id_product . " AND id_shop = " . (int)$id_shop);
        if ($id) {
            return new Ets_Loyalty_Config((int)$id);
        }
        return new Ets_Loyalty_Config();
    }
    protected function getIdPaymentMethodLang($id_pm, $id_lang)
    {
        return DB::getInstance()->getValue("SELECT id_ets_am_payment_method_lang FROM `" . _DB_PREFIX_ . "ets_am_payment_method_lang` WHERE id_payment_method = " . (int)$id_pm . " AND id_lang = " . (int)$id_lang);
    }
    protected function getPaymentMethodField($id_pm, $id_pmf)
    {
        $id = Db::getInstance()->getValue("SELECT id_ets_am_payment_method_field FROM `" . _DB_PREFIX_ . "ets_am_payment_method_field` WHERE id_payment_method = " . (int)$id_pm . " AND id_ets_am_payment_method_field = " . (int)$id_pmf);
        if ($id) {
            return new Ets_PaymentMethodField((int)$id);
        }
        return new Ets_PaymentMethodField();
    }
    protected function getIdPaymentMethodFieldLang($id_pmf, $id_lang)
    {
        return DB::getInstance()->getValue("SELECT id_ets_am_payment_method_field_lang FROM `" . _DB_PREFIX_ . "ets_am_payment_method_field_lang` WHERE id_payment_method_field = " . (int)$id_pmf . " AND id_lang = " . (int)$id_lang);
    }
    public function ets_is_sanitizeXML($string)
    {
        if (!empty($string)) {
            $regex = '/(
	                [\xC0-\xC1] # Invalid UTF-8 Bytes
	                | [\xF5-\xFF] # Invalid UTF-8 Bytes
	                | \xE0[\x80-\x9F] # Overlong encoding of prior code point
	                | \xF0[\x80-\x8F] # Overlong encoding of prior code point
	                | [\xC2-\xDF](?![\x80-\xBF]) # Invalid UTF-8 Sequence Start
	                | [\xE0-\xEF](?![\x80-\xBF]{2}) # Invalid UTF-8 Sequence Start
	                | [\xF0-\xF4](?![\x80-\xBF]{3}) # Invalid UTF-8 Sequence Start
	                | (?<=[\x0-\x7F\xF5-\xFF])[\x80-\xBF] # Invalid UTF-8 Sequence Middle
	                | (?<![\xC2-\xDF]|[\xE0-\xEF]|[\xE0-\xEF][\x80-\xBF]|[\xF0-\xF4]|[\xF0-\xF4][\x80-\xBF]|[\xF0-\xF4][\x80-\xBF]{2})[\x80-\xBF] # Overlong Sequence
	                | (?<=[\xE0-\xEF])[\x80-\xBF](?![\x80-\xBF]) # Short 3 byte sequence
	                | (?<=[\xF0-\xF4])[\x80-\xBF](?![\x80-\xBF]{2}) # Short 4 byte sequence
	                | (?<=[\xF0-\xF4][\x80-\xBF])[\x80-\xBF](?![\x80-\xBF]) # Short 4 byte sequence (2)
	            )/x';
            $string = preg_replace($regex, '', $string);
        }
        return $string;
    }
    public function getSponsorObject($id_customer, $id_parent)
    {
        $id = Db::getInstance()->getValue("SELECT id_ets_am_sponsor FROM `" . _DB_PREFIX_ . "ets_am_sponsor` WHERE id_customer = " . (int)$id_customer . " AND id_parent = " . (int)$id_parent);
        if ($id) {
            return new Ets_Sponsor((int)$id);
        }
        return new Ets_Sponsor();
    }
    public function getUserObject($id_customer, $id_shop)
    {
        $id = Db::getInstance()->getValue("SELECT id_ets_am_user FROM `" . _DB_PREFIX_ . "ets_am_user` WHERE id_customer = " . (int)$id_customer) . " AND id_shop = " . (int)$id_shop;
        if ($id) {
            return new Ets_User((int)$id);
        }
        return new Ets_User();
    }
    protected function deleteAllRewardData()
    {
        $sqls = array();
        $sqls[] = "DELETE FROM " . _DB_PREFIX_ . "ets_am_loy_reward;";
        $sqls[] = "DELETE FROM " . _DB_PREFIX_ . "ets_am_aff_reward;";
        $sqls[] = "DELETE FROM " . _DB_PREFIX_ . "ets_am_banner;";
        $sqls[] = "DELETE FROM " . _DB_PREFIX_ . "ets_am_invitation;";
        $sqls[] = "DELETE FROM " . _DB_PREFIX_ . "ets_am_payment_method;";
        $sqls[] = "DELETE FROM " . _DB_PREFIX_ . "ets_am_payment_method_lang;";
        $sqls[] = "DELETE FROM " . _DB_PREFIX_ . "ets_am_payment_method_field;";
        $sqls[] = "DELETE FROM " . _DB_PREFIX_ . "ets_am_payment_method_field_lang;";
        $sqls[] = "DELETE FROM " . _DB_PREFIX_ . "ets_am_participation;";
        $sqls[] = "DELETE FROM " . _DB_PREFIX_ . "ets_am_product_view;";
        $sqls[] = "DELETE FROM " . _DB_PREFIX_ . "ets_am_reward;";
        $sqls[] = "DELETE FROM " . _DB_PREFIX_ . "ets_am_reward_product;";
        $sqls[] = "DELETE FROM " . _DB_PREFIX_ . "ets_am_reward_usage;";
        $sqls[] = "DELETE FROM " . _DB_PREFIX_ . "ets_am_sponsor;";
        $sqls[] = "DELETE FROM " . _DB_PREFIX_ . "ets_am_user;";
        $sqls[] = "DELETE FROM " . _DB_PREFIX_ . "ets_am_voucher;";
        $sqls[] = "DELETE FROM " . _DB_PREFIX_ . "ets_am_withdrawal;";
        $sqls[] = "DELETE FROM " . _DB_PREFIX_ . "ets_am_withdrawal_field;";
        foreach($sqls as $sql)
        {
            Db::getInstance()->execute($sql);
        }
        return true;
    }
}