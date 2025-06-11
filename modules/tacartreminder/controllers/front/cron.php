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
 *************************************
 *  _____ _            ___       _   _
 * |_   _(_)          / _ \     | | (_)
 *   | |  _ _ __ ___ / /_\ \ ___| |_ ___   _____
 *   | | | | '_ ` _ \|  _  |/ __| __| \ \ / / _ \
 *   | | | | | | | | | | | | (__| |_| |\ V /  __/
 *   \_/ |_|_| |_| |_\_| |_/\___|\__|_| \_/ \___|
 *
 * +
 * + Languages: EN, FR
 * + PS version: 1.5,1.6
 * Cron use remind all abandonned cart
 */
if (!defined('_PS_VERSION_')) {
    exit;
}

class TACartReminderCronModuleFrontController extends ModuleFrontController
{
    public function initContent()
    {
        parent::initContent();
        $this->assign();
    }

    /**
     * method manage post data
     *
     * @throws Exception
     */
    public function postProcess()
    {
        ini_set('memory_limit', '600M');
        if (function_exists('set_time_limit')) {
            @set_time_limit(1200);
        }
        $message = '';
        // get the token
        if ($token = Tools::getValue('token')) {
            if (trim($token) == trim(Configuration::get('TA_CARTR_TOKEN'))) {
                // need xhprof extension @source "http://php.net/manual/fr/book.xhprof.php"
                $performance_audit = (bool) ((int) Tools::getValue('performance_audit', 0));
                $this->module->setPerformanceAudit($performance_audit);
                if (trim($token) == trim(Configuration::get('TA_CARTR_TOKEN'))) {
                    TACartReminder::launchBatchAllShops();
                } else {
                    $message = $this->module->loglongline('Not a valid token');
                }
            } else {
                $message = $this->module->l('token invalid', 'cron');
            }
        }
        $this->context->smarty->assign([
            'message' => $message,
        ]);
    }

    public function assign()
    {
        if (version_compare(_PS_VERSION_, '1.7.0', '>=') === true) {
            $this->setTemplate(
                'module:tacartreminder/views/templates/front/cron.tpl'
            );
        } else {
            $this->setTemplate('cron.tpl');
        }
    }
}
