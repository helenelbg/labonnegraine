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

class PsaffiliateCampaignModuleFrontController extends ModuleFrontController
{
    public function __construct()
    {
        parent::__construct();
        $this->context = Context::getContext();
        $this->module->loadClasses(array('Affiliate', 'Campaign'));
    }

    public function initContent()
    {
        parent::initContent();

        return $this->displayTemplate();
    }

    public function displayTemplate()
    {
        if ($this->context->customer->isLogged() && Psaffiliate::getAffiliateId()) {
            $id_campaign = Tools::getValue('id_campaign');
            $this->context->smarty->assign('id_campaign', $id_campaign);
            $campaign = new Campaign($id_campaign);
            if (Validate::isLoadedObject($campaign) || $id_campaign === false) {
                $id_affiliate = $this->module->getAffiliateId();
                if ($campaign->id_affiliate == $id_affiliate || $id_campaign === false) {
                    if (Tools::getValue('success') !== false) {
                        $this->context->smarty->assign('savedSuccess', Tools::getValue('success'));
                    }
                    if (Tools::getValue('submitSaveCampaign')) {
                        $campaign->name = pSQL(Tools::getValue('name'));
                        $campaign->description = pSQL(Tools::getValue('description'));
                        if ($id_campaign) {
                            $success = $campaign->update();
                        } else {
                            $campaign->id_affiliate = $id_affiliate;
                            $success = $campaign->add();
                        }
                        $link = new Link;
                        Tools::redirect($link->getModuleLink(
                            'psaffiliate',
                            'campaign',
                            array('id_campaign' => $campaign->id, 'success' => $success)
                        ));
                        $this->context->smarty->assign('savedSuccess', $success);
                    }
                    $this->context->smarty->assign('campaign', (array)$campaign);
                } else {
                    $this->context->smarty->assign('hasErrorNotYourCampaign', true);
                }
            } else {
                $this->context->smarty->assign('hasErrorNoCampaignFound', true);
            }

            if (Tools::version_compare(_PS_VERSION_, '1.7', '<')) {
                $this->setTemplate("campaign.tpl");
            } else {
                $this->setTemplate("module:psaffiliate/views/templates/front/ps17/campaign.tpl");
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
            'title' => $this->l('Affiliate Account33'),
            'url' => $this->context->link->getModuleLink('psaffiliate', 'myaccount'),
        );

        return $breadcrumb;
    }

    public function l($string, $specific = false, $class = null, $addslashes = false, $htmlentities = true)
    {
        return Translate::getModuleTranslation('psaffiliate', $string, 'campaign');
    }
}
