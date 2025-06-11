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
require_once dirname(__FILE__) . '/AdminEtsAmListController.php';
class AdminEtsAmWithdrawalsController extends AdminEtsAmListController
{
    public function init()
    {
        parent::init();
        $this->bootstrap = true;
        if (($action = Tools::getValue('table_action')) && Validate::isCleanHtml($action) && ($id = Tools::getValue('id'))) {
            if (Validate::isInt($id)) {
                $id = (int)$id;
                if (in_array($action, array('APPROVE', 'DECLINE_RETURN', 'DECLINE_DEDUCT', 'DELETE'))) {
                    $response = Ets_Withdraw::updateWithdrawAndReward($id, $action);
                    $wStatus = $this->l('Approved');
                    if ($action == 'DECLINE_RETURN' || $action == 'DECLINE_DEDUCT') {
                        $wStatus = $this->l('Declined');
                    }
                    if ($response['success']) {
                        die(json_encode(array(
                            'success' => true,
                            'message' => $action == 'DELETE' ? $this->l('Successful deleted') : $this->l('Updated successfully'),
                            'actions' => $response['actions'],
                            'status' => $wStatus
                        )));
                    }
                }
            }
            die(json_encode(array(
                'success' => false,
                'message' => $this->l('Error')
            )));
        }
        $link_withdraw = $this->context->link->getAdminLink('AdminEtsAmWithdrawals', true);
        if (Tools::isSubmit('submitSaveNoteWithdrawal', false)) {
            if (($note = Tools::getValue('note', false)) && Validate::isCleanHtml($note) && ($id_usage = (int)Tools::getValue('id_usage'))) {
                $usage = new Ets_Reward_Usage($id_usage);
                $usage->note = $note;
                $usage->update();
                $this->module->_html .= $this->module->displayConfirmation($this->l('Saved successfully'));
            }
        }
        if (Tools::isSubmit('submitApproveWithdrawItem', false)) {
            if ($id_usage = (int)Tools::getValue('id_usage', false)) {
                Ets_Withdraw::updateWithdrawAndReward($id_usage, 'APPROVE');
                $this->module->_html .= $this->module->displayConfirmation($this->l('Saved successfully'));
            }
        }
        if (Tools::isSubmit('submitDeclineReturnWithdrawItem', false)) {
            if ($id_usage = (int)Tools::getValue('id_usage', false)) {
                Ets_Withdraw::updateWithdrawAndReward($id_usage, 'DECLINE_RETURN');
                $this->module->_html .= $this->module->displayConfirmation($this->l('Saved successfully'));
            }
        }
        if (Tools::isSubmit('submitDeclineDeductWithdrawItem', false)) {
            if ($id_usage = (int)Tools::getValue('id_usage', false)) {
                Ets_Withdraw::updateWithdrawAndReward($id_usage, 'DECLINE_DEDUCT');
                $this->module->_html .= $this->module->displayConfirmation($this->l('Saved successfully'));
            }
        }
        if (Tools::isSubmit('submitDeleteWithdrawItem', false)) {
            if ($id_usage = (int)Tools::getValue('id_usage', false)) {
                $usage = new Ets_Reward_Usage($id_usage);
                $usage->deleted = 1;
                $usage->update();
                $this->module->_html .= $this->module->displayConfirmation($this->l('Saved successfully'));
                Tools::redirectAdmin($link_withdraw);
            }
        }
        if(Tools::isSubmit('downloadInvoice') && ($id_withdraw = (int)Tools::getValue('id_withdraw')) && ($withdraw = new Ets_Withdraw($id_withdraw)) && Validate::isLoadedObject($withdraw))
        {
            $withdraw->downloadInvoice();
        }
    }
    public function renderList()
    {
        $tabActive = 'withdraw_list';
        return $this->_renderList($tabActive);
    }
}
