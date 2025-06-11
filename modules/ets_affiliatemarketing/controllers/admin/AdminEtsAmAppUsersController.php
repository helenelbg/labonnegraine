<?php
/**
 * 2007-2022 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2022 PrestaShop SA
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 */

/**
 * Class AdminEtsAmFormController
 * @property Ets_affiliatemarketing $module;
 */
class AdminEtsAmAppUsersController extends ModuleAdminController
{
    public function init()
    {
        parent::init();
        if (Tools::isSubmit('actionApplication')) {
            $this->actionApplication();
        }
        if (Tools::isSubmit('submitAddUserReward')) {
            $this->submitAddUserReward();
        }
        if (Tools::isSubmit('actionUserReward')) {
            $this->actionUserReward();
        }
        if (Tools::isSubmit('actionProgramUser')) {
            $this->actionProgramUser();
        }
        if (Tools::isSubmit('loadMoreSponsorFriend')) {
            $this->loadMoreSponsorFriend();
        }
        if (Tools::isSubmit('loadMoreHistoryReward')) {
            $this->loadMoreHistoryReward();
        }
        if (Tools::isSubmit('aff_search_customer'))
            $this->ajaxSearchFriends();
        if (Tools::isSubmit('aff_add_search_customer'))
            $this->ajaxAddFriend();
    }
    protected function actionApplication()
    {
        $id_approve = (int)Tools::getValue('id_approve', 0);
        $action_user = ($action_user = Tools::getValue('action_user', 0)) && Validate::isCleanHtml($action_user) ? $action_user : '';
        $reason = null;
        if ($action_user == 'decline' || $action_user == 'approve') {
            $reason = ($reason = Tools::getValue('reason', null)) && Validate::isCleanHtml($reason) ? $reason : '';
        }
        $response = EtsAmAdmin::actionCustomer($id_approve, $action_user, $reason);
        $app_status = $this->l('Approved');
        if ($action_user == 'decline') {
            $app_status = $this->l('Declined');
        }
        if ($response['success']) {
            die(json_encode(array(
                'success' => true,
                'message' => $action_user == 'delete' ? $this->l('Deleted successfully.') : $this->l('Updated successfully.'),
                'redirect' => $action_user == 'delete' ? $this->context->link->getAdminLink('AdminEtsAmApp', true) : '',
                'actions' => $response['actions'],
                'status' => $app_status
            )));
        }
        die(json_encode(array(
            'success' => false,
            'message' => $this->l('Failed')
        )));
    }
    protected function submitAddUserReward()
    {
        $id_customer = (int)Tools::getValue('id_customer');
        $errors = array();
        if (!$id_customer || !Validate::isLoadedObject(new Customer($id_customer))) {
            $errors[] = $this->l('Customer is required');
        }
        $customer_loyalty = (int)Tools::getValue('aff_customer_loyalty');
        $customer_referral = (int)Tools::getValue('aff_customer_referral');
        $customer_affiliate = (int)Tools::getValue('aff_customer_affiliate');
        if (!$customer_loyalty && !$customer_referral && !$customer_affiliate)
            $errors[] = $this->l('Join program is required');
        elseif (!Ets_User::addUserReward($id_customer, $customer_loyalty, $customer_referral, $customer_affiliate)) {
            $errors[] = $this->l('This user is already joined marketing program');
        }
        if (!$errors) {
            $this->context->smarty->assign(
                array(
                    'id_customer' => $id_customer,
                    'link' => $this->context->link,
                    'aff_customer' => new Customer($id_customer),
                    'price_program' => Ets_AM::displayRewardAdmin(0),
                    'price_widthraw' => Ets_affiliatemarketing::displayPrice(0, (int)Configuration::get('PS_CURRENCY_DEFAULT')),
                )
            );
            die(
                json_encode(
                    array(
                        'tr_html' => $this->module->display($this->module->getLocalPath(), 'row_user.tpl'),
                        'success' => $this->module->displaySuccessMessage($this->l('Added successfully'))
                    )
                )
            );
        } else {
            die(
                json_encode(
                    array(
                        'errors' => $this->module->displayError($errors)
                    )
                )
            );
        }
    }
    protected function actionUserReward()
    {
        if (($id_customer = (int)Tools::getValue('id_user_reward', false)) && ($action = Tools::getValue('action_user_reward', false)) && Validate::isCleanHtml($action)) {
            $response = Ets_User::processActionStatus($id_customer, $action);
            $uStatus = $this->l('Active');
            if ($action == 'decline') {
                $uStatus = $this->l('Suspended');
            }
            if ($response) {
                die(json_encode(array(
                    'success' => true,
                    'message' => $this->l('Saved successfully'),
                    'actions' => $response['actions'],
                    'status' => $uStatus
                )));
            }
        }
        die(json_encode(array(
            'success' => false,
            'message' => $this->l('Error')
        )));
    }
    protected function actionProgramUser()
    {
        $id_user = (int)Tools::getValue('id_user', false);
        $program = Tools::getValue('program', false);
        $action = ($action = Tools::getValue('action_user', false)) && Validate::isCleanHtml($action) ? $action : '';
        $reason = ($reason = Tools::getValue('reason', false)) && Validate::isCleanHtml($reason) ? $reason : '';
        if ($id_user && $program && Validate::isCleanHtml($program) && $action) {
            if (Ets_Participation::actionProgramUser($id_user, $program, $action, $reason)) {
                die(json_encode(array(
                    'success' => true,
                    'message' => $this->l('Updated successfully')
                )));
            }
        }
        die(json_encode(array(
            'success' => false,
            'message' => $this->l('Failed.')
        )));
    }
    public function loadMoreHistoryReward()
    {
        $id_customer = (int)Tools::getValue('id_customer', false);
        $page = (int)Tools::getValue('page', false);
        $filter = array(
            'type_date_filter' => Tools::getValue('type_date_filter'),
            'date_from_reward' => Tools::getValue('date_from_reward'),
            'date_to_reward' => Tools::getValue('date_to_reward'),
            'program' => Tools::getValue('program'),
            'status' => Tools::getValue('status'),
            'limit' => (int)Tools::getValue('limit'),
            'page' => (int)$page,
        );
        if ($id_customer && $page) {
            $histories = EtsAmAdmin::getRewardHistory($id_customer, null, false, false, $filter);
            $this->context->smarty->assign(array(
                'reward_history' => $histories
            ));
            die(json_encode(array(
                'success' => true,
                'html' =>$this->module->display(_PS_MODULE_DIR_.$this->module->name.DIRECTORY_SEPARATOR.$this->module->name.'.php', 'user/paginate_history_reward.tpl')
            )));
        }
    }
    public function loadMoreSponsorFriend()
    {
        $id_customer = (int)Tools::getValue('id_customer', false);
        $page = (int)Tools::getValue('page', false);
        if ($id_customer && $page) {
            $sponsors = Ets_Sponsor::getDetailSponsors($id_customer, array(
                'page' => $page
            ));
            $this->context->smarty->assign(array(
                'sponsors' => $sponsors
            ));
            die(json_encode(array(
                'success' => true,
                'html' => $this->module->display(_PS_MODULE_DIR_.$this->module->name.DIRECTORY_SEPARATOR.$this->module->name.'.php', 'user/paginate_sponsors.tpl')
            )));
        }
    }
    public function ajaxSearchFriends()
    {
        if (($customer = Tools::getValue('customer')) && Validate::isCleanHtml($customer)) {
            $id_customer = (int)Tools::getValue('id_reward_users');
            $customers = Ets_Sponsor::searchFriends($customer, $id_customer);
            $this->context->smarty->assign(
                array(
                    'customers' => $customers,
                )
            );
            die(
            json_encode(
                array(
                    'list_customers' => $this->module->display(_PS_MODULE_DIR_.$this->module->name.DIRECTORY_SEPARATOR.$this->module->name.'.php', 'user/list_customer.tpl'),
                )
            )
            );
        }
    }
    public function ajaxAddFriend()
    {
        $id_sponsor = (int)Tools::getValue('id_customer');
        $customerParent = new Customer($id_sponsor);
        $id_customer = (int)Tools::getValue('id_friend');
        $customerFriend = new Customer($id_customer);
        if ($id_sponsor != $id_customer && Validate::isLoadedObject($customerFriend) && Validate::isLoadedObject($customerParent) && ($sponsor = Ets_Sponsor::addFriend($id_sponsor, $id_customer))) {
            die(
            json_encode(
                array(
                    'success' => $this->l('Added successful'),
                    'sponsor' => (array)$sponsor,
                )
            )
            );
        } else {
            die(
                json_encode(
                    array(
                        'errors' => $this->l('This customer is already in friends list of another sponsor'),
                    )
                )
            );
        }
    }
}