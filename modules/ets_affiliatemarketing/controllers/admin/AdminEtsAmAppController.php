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

/**
 * Class AdminEtsAmAppController
 * @property Ets_affiliatemarketing $module;
 */
require_once dirname(__FILE__) . '/AdminEtsAmAppUsersController.php';
class AdminEtsAmAppController extends AdminEtsAmAppUsersController
{
    public function init()
    {
        parent::init();
        $this->bootstrap = true;
    }
    public function renderList()
    {
        $tabActive = 'applications';
        $this->getApplications();
        $this->context->smarty->assign($this->module->getAssign($tabActive));
        return $this->module->display($this->module->getLocalPath(), 'admin_form.tpl');
    }
    public function getApplications()
    {
        if (($id_app = (int)Tools::getValue('id_application', false)) && Tools::isSubmit('viewapp') && ($app = Ets_Participation::getApplicationById($id_app))) {
            $this->context->smarty->assign(array(
                'app' => $app,
                'id_data' => $id_app,
                'user_link' => $this->module->getLinkCustomerAdmin($app['id_customer']),
                'link_app' => $this->context->link->getAdminLink('AdminEtsAmApp', true)
            ));
            $this->module->_html .= $this->module->display($this->module->getLocalPath(), 'view_app.tpl');
        } else {
            $filter = array(
                'type_date_filter' => Tools::getValue('type_date_filter'),
                'date_from_reward' => Tools::getValue('date_from_reward'),
                'date_to_reward' => Tools::getValue('date_to_reward'),
                'status' => Tools::getValue('status'),
                'search' => Tools::getValue('search'),
                'limit' => (int)Tools::getValue('limit'),
                'page' => (int)Tools::getValue('page'),
            );
            $cacheID = $this->module->_getCacheId(array('type'=>'list_app','id_employee' => $this->context->employee->id,'page' => (int)$filter['page'],'limit' => (int)$filter['limit'],'status' => $filter['status']!='all' ? $filter['status'] :'all'));
            if($filter['date_to_reward'] || !$this->module->isCached('list_applications.tpl',$cacheID))
            {
                $pagination = EtsAmAdmin::getDataApplications($filter);
                if ($pagination['results']) {
                    foreach ($pagination['results'] as &$result) {
                        $result['link'] = $this->module->getLinkCustomerAdmin($result['id_customer']);
                    }
                }
                $this->context->smarty->assign(array(
                    'results' => $pagination['results'],
                    'current_page' => $pagination['current_page'],
                    'total_page' => $pagination['total_page'],
                    'total_data' => $pagination['total_data'],
                    'per_page' => $pagination['per_page'],
                    'search' => ($search = Tools::getValue('search', '')) && Validate::isCleanHtml($search) ? $search : '',
                    'limit' => (int)Tools::getValue('limit', 10),
                    'search_placeholder' => $this->l('Search for id, status, email...'),
                    'params' => Tools::getAllValues(),
                    'link_customer' => $this->context->link->getAdminLink('AdminCustomers', true),
                    'enable_email_approve_app' => (int)Configuration::get('ETS_AM_ENABLED_EMAIL_RES_REG'),
                    'enable_email_decline_app' => (int)Configuration::get('ETS_AM_ENABLED_EMAIL_DECLINE_APP')
                ));
            }
            if($filter['date_to_reward'])
                $this->module->_html .= $this->module->display($this->module->getLocalPath(), 'list_applications.tpl');
            else
                $this->module->_html .= $this->module->display($this->module->getLocalPath(), 'list_applications.tpl',$cacheID);
        }
    }
}
