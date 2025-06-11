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
if (!defined('_PS_VERSION_')) { exit; }
class Link extends LinkCore
{
    /*
    * module: ybc_blog
    * date: 2024-10-01 16:51:29
    * version: 4.6.4
    */
    public function getLanguageLink($idLang, Context $context = null)
    {
        if (!$context) {
            $context = Context::getContext();
        }
        $params = $_GET;
        unset($params['isolang'], $params['controller']);
        if (!$this->allow) {
            $params['id_lang'] = $idLang;
        } else {
            unset($params['id_lang']);
        }
        $controller = Dispatcher::getInstance()->getController();
        if (!empty($context->controller->php_self)) {
            $controller = $context->controller->php_self;
        }
        if (isset($params['fc']) && $params['fc'] == 'module' && isset($params['module']) && $params['module']=='ybc_blog') {
            
            $ybc_blog = Module::getInstanceByName('ybc_blog');
            return $ybc_blog->getLink($controller, $params, $idLang);
        }
        return parent::getLanguageLink($idLang,$context);
    }
}