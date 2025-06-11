<?php
/**
 * Cart Reminder
 *
 *    @author    EIRL Timactive De Véra
 *    @copyright Copyright (c) TIMACTIVE 2015 -EIRL Timactive De Véra
 *    @license   Commercial license
 *
 *    @category pricing_promotion
 *
 *    @version 1.1.0
 *************************************
 **         CART REMINDER            *
 **          V 1.0.0                 *
 *
 * |_   _(_)          / _ \     | | (_)
 *   | |  _ _ __ ___ / /_\ \ ___| |_ ___   _____
 *   | | | | '_ ` _ \|  _  |/ __| __| \ \ / / _ \
 *   | | | | | | | | | | | | (__| |_| |\ V /  __/
 *   \_/ |_|_| |_| |_\_| |_/\___|\__|_| \_/ \___|
 *
 * +
 * + Languages: EN, FR
 * + PS version: 1.5,1.6
 *
 * Front Controller for Unsubscribe cart reminder
 */
if (!defined('_PS_VERSION_')) {
    exit;
}

class TACartReminderUnscribeModuleFrontController extends ModuleFrontController
{
    /**
     * Error not use but need for next version
     *
     * @var array
     */
    public $errors = [];

    /**
     * @see ModuleFrontController::_construct
     */
    public function __construct()
    {
        parent::__construct();
        $this->context = Context::getContext();
    }

    /**
     * call assign function for template
     *
     * @see ModuleFrontController::_construct
     */
    public function init()
    {
        parent::init();
        $this->assign();
    }

    /**
     * Assign all var needs for the unsubscribe front template
     */
    public function assign()
    {
        $module_instance = new TACartReminder();
        // flag success to false
        $success_unscriber = false;
        $message_err = '';
        $id_customer = (int) Tools::getValue('id_customer');
        $id_shop = (int) Tools::getValue('id_shop');
        $unscribe_token = Tools::getValue('token_unscribe');
        // check all and security token for unsusribe
        $customer = new Customer($id_customer);
        $customer_email = $customer->email;
        if ($id_customer && $id_shop
            && $unscribe_token == md5(Configuration::get('TA_CARTR_KEY') . 'unscribe_' . $id_customer . '_' . $id_shop)) {
            if (!TACartReminderCustomerUnsubscribe::exist($customer_email, $id_shop)) {
                $customer = new Customer($id_customer);
                $unscriber = new TACartReminderCustomerUnsubscribe();
                $unscriber->id_customer = $id_customer;
                $unscriber->email = $customer_email;
                $unscriber->id_shop = $id_shop;
                if ($unscriber->add()) {
                    $success_unscriber = true;
                }
                /* Cancel all journal */

                $journals = TACartReminderJournal::getJournalsByCustomer($customer->email, $id_shop);
                foreach ($journals as $journal) {
                    if ($journal['state'] != 'CANCELED' || $journal['state'] != 'FINISHED') {
                        $journal_to_cancel = new TACartReminderJournal((int) $journal['id_journal']);
                        $journal_to_cancel->state = 'CANCELED';
                        $journal_to_cancel->update();
                        $mess = new TACartReminderMessage();
                        $mess->id_journal = (int) $journal['id_journal'];
                        $mess->message = $module_instance->l('The cart reminder was canceled because the customer is now unsubscribed');
                        $mess->add();
                    }
                }
            } else {
                $message_err = $module_instance->l('You have already unsubscribed');
            }
        } else {
            $message_err = $module_instance->l('Token invalid');
        }
        $this->context->smarty->assign([
            'message_err' => $message_err,
            'success_unscriber' => $success_unscriber,
        ]);
        if (version_compare(_PS_VERSION_, '1.7.0', '>=') === true) {
            $this->setTemplate('module:tacartreminder/views/templates/front/unscribe.tpl');
        } else {
            $this->setTemplate('unscribe.tpl');
        }
    }
}
