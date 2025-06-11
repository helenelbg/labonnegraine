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
/**
 * Class AdminEtsAmRewardHistoryController
 * @property Ets_affiliatemarketing $module
 */
class AdminEtsAmRewardHistoryController extends AdminEtsAmListController
{
    public function init()
    {
        parent::init();
        $this->bootstrap = true;
        if (($action_reward = Tools::getValue('doActionRewardItem', false)) && Validate::isCleanHtml($action_reward)) {
            $id_reward = (int)Tools::getValue('id_reward', false);
            $response = EtsAmAdmin::actionReward($id_reward, $action_reward);
            $reward_status = $this->l('Approved');
            if ($action_reward == 'cancel') {
                $reward_status = $this->l('Canceled');
            }
            if ($response['success']) {
                die(json_encode(array(
                    'success' => true,
                    'message' => $this->l('The status has been successfully updated'),
                    'actions' => $response['actions'],
                    'status' => $reward_status,
                    'user' => $response['user'],
                )));
            } else {
                die(json_encode(array(
                    'success' => false,
                    'message' => $this->l('Reward update failed'),
                )));
            }
        }
        if (($action_reward = Tools::getValue('doActionRewardUsageItem', false)) && Validate::isCleanHtml($action_reward)) {
            $id_reward = (int)Tools::getValue('id_reward', false);
            $response = EtsAmAdmin::actionRewardUsage($id_reward, $action_reward);
            if ($response['success']) {
                $status = $this->l('Deducted');
                if ($action_reward == 'cancel') {
                    $status = $this->l('Refunded');
                }
                die(json_encode(array(
                    'success' => true,
                    'message' => $this->l('The status has been successfully updated'),
                    'actions' => $response['actions'],
                    'status' => $status,
                    'user' => $response['user'],
                )));
            } else {
                die(json_encode(array(
                    'success' => false,
                    'message' => $this->l('Reward update failed'),
                )));
            }
        }
    }
    public function renderList()
    {
        $tabActive = 'reward_history';
        return $this->_renderList($tabActive);
    }
}
