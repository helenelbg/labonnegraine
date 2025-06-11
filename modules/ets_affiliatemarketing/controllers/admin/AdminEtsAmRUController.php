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

if (!defined('_PS_VERSION_'))
    exit;
require_once dirname(__FILE__) . '/AdminEtsAmFormController.php';
/**
 * Class AdminEtsAmRUController
 * @property Ets_affiliatemarketing $module
 */
class AdminEtsAmRUController extends AdminEtsAmFormController
{
    public function init()
    {
        parent::init();
        $this->bootstrap = true;
        if ((bool)Tools::isSubmit('sortPaymentMethodField', false)) {
            if (($sort_data = Tools::getValue('sort_data')) && Ets_affiliatemarketing::validateArray($sort_data)) {
                if (EtsAmAdmin::updateSortPaymentMethodfield($sort_data)) {
                    die(json_encode(array(
                        'success' => true,
                        'message' => $this->l('Sorted successfully'),
                    )));
                }
            }
            die(json_encode(array(
                'success' => false,
                'message' => $this->l('Sort failed.'),
            )));
        }
        if ((bool)Tools::isSubmit('sortPaymentMethod', false)) {
            if (($sort_data = Tools::getValue('sort_data')) && Ets_affiliatemarketing::validateArray($sort_data)) {
                if (EtsAmAdmin::updateSortPaymentMethod($sort_data)) {
                    $this->module->_clearCache('*',$this->module->_getCacheId('list_payment',false));
                    die(json_encode(array(
                        'success' => true,
                        'message' => $this->l('Sorted successfully'),
                    )));
                }
            }
            die(json_encode(array(
                'success' => false,
                'message' => $this->l('Sort failed.'),
            )));
        }
        if ((bool)Tools::isSubmit('getLanguage')) {
            $langs = Language::getLanguages(false);
            $currency = Currency::getDefaultCurrency();
            die(json_encode(array(
                'success' => true,
                'languages' => $langs,
                'currency' => $currency
            )));
        }
    }
    public function renderList()
    {
        $tabActive = Tools::getValue('tabActive','reward_usage');
        if(!in_array($tabActive,array('reward_usage','payment_settings')))
            Tools::redirectAdmin($this->context->link->getAdminLink('AdminEtsAmRU'));
        if($tabActive=='reward_usage')
            return $this->_renderList($tabActive);
        $this->getPaymentMethods();
        $this->context->smarty->assign($this->module->getAssign($tabActive));
        return ($this->module->_errors ? $this->module->displayError($this->module->_errors) : '').$this->module->display(_PS_MODULE_DIR_.$this->module->name.DIRECTORY_SEPARATOR.$this->module->name.'.php', 'admin_form.tpl');
    }
    protected function getPaymentMethods()
    {
        $link_pm = $this->context->link->getAdminLink('AdminEtsAmRU', true) . '&tabActive=payment_settings';
        if (Tools::isSubmit('update_payment_method', false)) {
            if ($pm_name = Tools::getValue('payment_method_name', array())) {
                $title_fill = 0;
                foreach ($pm_name as $item) {
                    if ($item) {
                        if (!Validate::isString($item)) {
                            $this->module->_errors[] = $this->l('Title of payment method must be a string.');
                        } else {
                            $title_fill = 1;
                        }
                    }
                }
                if (!$title_fill) {
                    $this->module->_errors[] = $this->l('Title of payment method is required.');
                }
            }
            if ($pm_desc = Tools::getValue('payment_method_desc', array())) {
                foreach ($pm_desc as $item) {
                    if ($item) {
                        if (!Validate::isCleanHtml($item)) {
                            $this->module->_errors[] = $this->l('Description of payment method must be a string.');
                        }
                    }
                }
            }
            if ($pm_note = Tools::getValue('payment_method_note', array())) {
                foreach ($pm_note as $item) {
                    if ($item) {
                        if (!Validate::isCleanHtml($item)) {
                            $this->module->_errors[] = $this->l('Note of payment method must be a string.');
                        }
                    }
                }
            }
            if (($pm_fee_type = Tools::getValue('payment_method_fee_type')) && $pm_fee_type != 'NO_FEE') {
                if ($pm_fee_type == 'FIXED') {
                    if (!($pm_fee_fixed = Tools::getValue('payment_method_fee_fixed'))) {
                        $this->module->_errors[] = $this->l('Fee (fixed amount) is required');
                    } elseif (!Validate::isFloat($pm_fee_fixed)) {
                        $this->module->_errors[] = $this->l('Fee (fixed amount) must be a decimal number.');
                    }
                } elseif ($pm_fee_type == 'PERCENT') {
                    if (($pm_fee_percent = Tools::getValue('payment_method_fee_percent')) == '') {
                        $this->module->_errors[] = $this->l('Fee (percentage) is required');
                    } elseif (!Validate::isFloat($pm_fee_percent)) {
                        $this->module->_errors[] = $this->l('Fee (percentage) must be a decimal number.');
                    } elseif ($pm_fee_percent <= 0 || $pm_fee_percent > 100)
                        $this->module->_errors[] = $this->l('Fee (percentage) is not valid.');
                }
            }
            if ($pm_estimated = Tools::getValue('payment_method_estimated', false)) {
                if (!Validate::isUnsignedInt($pm_estimated)) {
                    $this->module->_errors[] = $this->l('Estimated processing time must be a integer');
                }
            }
            if ($pmf = Tools::getValue('payment_method_field', array())) {
                foreach ($pmf as $item) {
                    if (isset($item['title']) && is_array($item['title']) && $item['title']) {
                        $title_fill = 0;
                        foreach ($item['title'] as $title) {
                            if ($title) {
                                if (!Validate::isString('$title')) {
                                    $this->module->_errors[] = $this->l('Title of payment method field must be a string');
                                } else {
                                    $title_fill = 1;
                                }
                            }
                        }
                        if (!$title_fill) {
                            $this->module->_errors[] = $this->l('Title of payment method field is required');
                        }
                    }
                }
            }
            if (!$this->module->_errors) {
                if (($id_pm = (int)Tools::getValue('payment_method')) && ($paymentMethod = new Ets_PaymentMethod($id_pm)) && Validate::isLoadedObject($paymentMethod)) {
                    EtsAmAdmin::updatePaymentMethod($id_pm,
                        $pm_name,
                        $pm_fee_type,
                        isset($pm_fee_fixed) ? $pm_fee_fixed : null,
                        isset($pm_fee_percent) ? $pm_fee_percent : null,
                        (int)Tools::getvalue('payment_method_enabled'),
                        $pm_estimated,
                        $pm_desc,
                        $pmf,
                        $pm_note
                    );
                    $this->module->_clearCache('*',$this->module->_getCacheId('list_payment',false));
                    $this->module->_html .= $this->module->displayConfirmation($this->l('Payment method updated successfully'));
                } else
                    $this->module->_errors[] = $this->l('Method not exists');
            }
        } elseif (Tools::isSubmit('create_payment_method', false)) {
            if ($pm_name = Tools::getValue('payment_method_name', array())) {
                $title_fill = 0;
                foreach ($pm_name as $item) {
                    if ($item) {
                        if (!Validate::isString($item)) {
                            $this->module->_errors[] = $this->l('Title of payment method must be a string.');
                        } else {
                            $title_fill = 1;
                        }
                    }
                }
                if (!$title_fill) {
                    $this->module->_errors[] = $this->l('Title of payment method is required.');
                }
            }
            if ($pm_desc = Tools::getValue('payment_method_desc', array())) {
                foreach ($pm_desc as $item) {
                    if ($item) {
                        if (!Validate::isCleanHtml($item)) {
                            $this->module->_errors[] = $this->l('Description of payment method must be a string.');
                        }
                    }
                }
            }
            if ($pm_note = Tools::getValue('payment_method_note', array())) {
                foreach ($pm_note as $item) {
                    if ($item) {
                        if (!Validate::isCleanHtml($item)) {
                            $this->module->_errors[] = $this->l('Note of payment method must be a string.');
                        }
                    }
                }
            }
            if (($pm_fee_type = Tools::getValue('payment_method_fee_type')) && $pm_fee_type != 'NO_FEE') {
                if ($pm_fee_type == 'FIXED') {
                    if (!($pm_fee_fixed = Tools::getValue('payment_method_fee_fixed'))) {
                        $this->module->_errors[] = $this->l('Fee (fixed amount) is required');
                    } elseif (!Validate::isFloat($pm_fee_fixed)) {
                        $this->module->_errors[] = $this->l('Fee (fixed amount) must be a decimal number.');
                    }
                } elseif ($pm_fee_type == 'PERCENT') {
                    if (!($pm_fee_percent = Tools::getValue('payment_method_fee_percent'))) {
                        $this->module->_errors[] = $this->l('Fee (percentage) is required');
                    } elseif (!Validate::isFloat($pm_fee_percent)) {
                        $this->module->_errors[] = $this->l('Fee (percentage) must be a decimal number.');
                    }
                }
            }
            if ($pm_estimated = Tools::getValue('payment_method_estimated', false)) {
                if (!Validate::isUnsignedInt($pm_estimated)) {
                    $this->module->_errors[] = $this->l('Estimated processing time must be a integer');
                }
            }
            if (!$this->module->_errors) {
                $id_pm = EtsAmAdmin::createPaymentMethod(
                    $pm_name,
                    $pm_fee_type,
                    isset($pm_fee_fixed) ? $pm_fee_fixed : null,
                    isset($pm_fee_percent) ? $pm_fee_percent : null,
                    (int)Tools::getvalue('payment_method_enabled'),
                    $pm_estimated,
                    $pm_desc,
                    $pm_note
                );
                if ($id_pm) {
                    $this->module->_clearCache('*',$this->module->_getCacheId('list_payment',false));
                    $this->context->cookie->__set('flash_created_pm_success', $this->l('Payment method created successfully.'));
                    return Tools::redirectAdmin($link_pm . '&payment_method=' . $id_pm . '&edit_pm=1');
                }
            } else {
                $languages = Language::getLanguages('false');
                $currency = Currency::getDefaultCurrency();
                $this->context->smarty->assign(array(
                    'languages' => $languages,
                    'currency' => $currency,
                    'link_pm' => $link_pm,
                    'query' => Tools::getAllValues()
                ));
                return $this->module->_html .= $this->module->display(_PS_MODULE_DIR_.$this->module->name.DIRECTORY_SEPARATOR.$this->module->name.'.php', 'payment/create_payment_method.tpl');
            }
        } elseif (Tools::isSubmit('delete_payment_method') && ($id_pm = (int)Tools::getValue('payment_method'))) {
            EtsAmAdmin::deletePaymentMethod($id_pm);
            $this->module->_clearCache('*',$this->module->_getCacheId('list_payment',false));
        }
        $languages = Language::getLanguages('false');
        $currency = Currency::getDefaultCurrency();
        if (Tools::isSubmit('create_pm')) {
            $this->context->smarty->assign(array(
                'languages' => $languages,
                'currency' => $currency,
                'link_pm' => $link_pm,
            ));
            return $this->module->_html .= $this->module->display(_PS_MODULE_DIR_.$this->module->name.DIRECTORY_SEPARATOR.$this->module->name.'.php', 'payment/create_payment_method.tpl');
        } elseif (Tools::isSubmit('edit_pm') && ($id_pm = (int)Tools::getValue('payment_method', false))) {
            $payment_method = EtsAmAdmin::getPaymentMethod($id_pm);
            $pmf = EtsAmAdmin::getListPaymentMethodField($id_pm);
            $this->context->smarty->assign(array(
                'payment_method' => $payment_method,
                'payment_method_fields' => $pmf,
                'languages' => $languages,
                'default_lang' => (int)Configuration::get('PS_LANG_DEFAULT'),
                'currency' => $currency,
                'link_pm' => $link_pm
            ));
            if ($msg = $this->context->cookie->__get('flash_created_pm_success')) {
                $this->module->_html .= $this->module->displayConfirmation($msg);
                $this->context->cookie->__set('flash_created_pm_success', null);
            }
            return $this->module->_html .= $this->module->display(_PS_MODULE_DIR_.$this->module->name.DIRECTORY_SEPARATOR.$this->module->name.'.php', 'payment/edit_payment_method.tpl');
        }
        $cacheID = $this->module->_getCacheId(array('list_payment',$this->context->employee->id));
        if(!$this->module->isCached('payment/payment_methods.tpl',$cacheID))
        {
            $payment_methods = EtsAmAdmin::getListPaymentMethods();
            $default_currency = Currency::getDefaultCurrency()->iso_code;
            $this->context->smarty->assign(array(
                'payment_methods' => $payment_methods,
                'default_currency' => $default_currency,
                'link_pm' => $link_pm
            ));
        }
        return $this->module->_html .= $this->module->display($this->module->getLocalPath(), 'payment/payment_methods.tpl',$cacheID);
    }
}
