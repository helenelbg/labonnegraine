<?php
/**
 * NOTICE OF LICENSE
 *
 * This file is licenced under the Software License Agreement.
 * With the purchase or the installation of the software in your application
 * you accept the licence agreement.
 *
 * You must not modify, adapt or create derivative works of this source code.
 *
 * @author    Active Design <office@activedesign.ro>
 * @copyright 2016-2018 Active Design
 * @license   LICENSE.txt
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

class PsaffiliateTextsModuleFrontController extends ModuleFrontController
{
    public function __construct()
    {
        parent::__construct();
        $this->context = Context::getContext();
        $this->module->loadClasses(array('Affiliate', 'Text'));
    }

    public function initContent()
    {
        parent::initContent();

        return $this->displayTemplate();
    }

    public function displayTemplate()
    {
        if ($this->context->customer->isLogged()) {
            $this->context->smarty->assign('hasTexts', Text::hasTexts(true));
            $this->context->smarty->assign('texts', Text::getTexts(true));

            if (Tools::version_compare(_PS_VERSION_, '1.7', '<')) {
                $this->setTemplate("texts.tpl");
            } else {
                $this->setTemplate("module:psaffiliate/views/templates/front/ps17/texts.tpl");
            }
        } else {
            Tools::redirect('index.php?controller=authentication&back='.urlencode($this->context->link->getModuleLink(
                    'psaffiliate',
                    'myaccount'
                )));
        }
    }

    public function getBreadcrumbLinks()
    {
        $breadcrumb = parent::getBreadcrumbLinks();

        $breadcrumb['links'][] = $this->addMyAccountToBreadcrumb();
        $breadcrumb['links'][] = array(
            'title' => $this->l('Affiliate Account'),
            'url' => $this->context->link->getModuleLink('psaffiliate', 'myaccount'),
        );

        return $breadcrumb;
    }

    public function l($string, $specific = false, $class = null, $addslashes = false, $htmlentities = true)
    {
        return Translate::getModuleTranslation('psaffiliate', $string, 'campaign');
    }
}
