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
 
class Ets_affiliatemarketingAllModuleFrontController extends ModuleFrontController
{
    public function setMetas($meta = array())
    {
        $meta_title = isset($meta['title']) && $meta['title'] ? $meta['title'] : '';
        $meta_keywords = isset($meta['keywords']) && $meta['keywords'] ? $meta['keywords'] : '';
        $meta_description = isset($meta['description']) && $meta['description'] ? $meta['description'] : '';
        if ($this->module->is17) {
            $page = $this->getTemplateVarPage();
            $page['meta']['title'] = $meta_title ? $meta_title : '';
            $page['meta']['keywords'] = $meta_keywords ? $meta_keywords : '';
            $page['meta']['description'] = $meta_description ? $meta_description : '';
            $templateVars = array('page' => $page);
            $allow_voucher = (int)Configuration::get('ETS_AM_AFF_ALLOW_CONVERT_TO_VOUCHER');
            $allow_withdraw = (int)Configuration::get('ETS_AM_AFF_ALLOW_WITHDRAW');
            $templateVars['allow_convert_voucher'] = $allow_voucher;
            $templateVars['allow_withdraw'] = $allow_withdraw;
            $currency = array(
                'sign' => $this->context->currency->sign,
                'iso_code' => $this->context->currency->iso_code,
            );
            $templateVars['eam_currency'] = $currency;
            if ($this->module->is17) {
                $templateVars['controller'] = 'all';
            }
            $this->context->smarty->assign($templateVars);

        } else {
            $this->context->smarty->assign(array(
                'title' => $meta_title,
                'keywords' => $meta_keywords,
                'description' => $meta_description,
                'allow_convert_voucher' => (int)Configuration::get('ETS_AM_AFF_ALLOW_CONVERT_TO_VOUCHER'),
                'allow_withdraw' => (int)Configuration::get('ETS_AM_AFF_ALLOW_WITHDRAW')
            ));
        }
        $this->context->smarty->assign(array(
            'my_account_link' => $this->context->link->getPageLink('my-account', true),
            'home_link' => $this->context->link->getPageLink('index', true),
            'breadcrumb' => $this->module->getBreadcrumb(),
            'path' => $this->module->getBreadcrumb()
        ));
    }
}