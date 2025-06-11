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

class Ets_affiliatemarketingExecModuleFrontController extends ModuleFrontController
{
	public function initContent(){
        if(!$this->isTokenValid()){
            die(
                json_encode(
                    array(
                        'success' => false,
                        'message' => $this->module->l('Token is invalid', 'exec')
                    )
                )
            );
        }
        $this->context->cookie->__set('eam_ref_delay_popup', date('Y-m-d'));
        die(
            json_encode(
                array(
                    'success' => true
                )
            )
        );
	}
}