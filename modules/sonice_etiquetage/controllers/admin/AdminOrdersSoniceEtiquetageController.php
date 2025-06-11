<?php
/**
 * NOTICE OF LICENSE
 *
 * This source file is subject to a commercial license from Common-Services Co., Ltd.
 * Use, copy, modification or distribution of this source file without written
 * license agreement from the SARL SMC is strictly forbidden.
 * In order to obtain a license, please contact us: contact@common-services.com
 * ...........................................................................
 * INFORMATION SUR LA LICENCE D'UTILISATION
 *
 * L'utilisation de ce fichier source est soumise a une licence commerciale
 * concedee par la societe Common-Services Co., Ltd.
 * Toute utilisation, reproduction, modification ou distribution du present
 * fichier source sans contrat de licence ecrit de la part de la Common-Services Co. Ltd. est
 * expressement interdite.
 * Pour obtenir une licence, veuillez contacter Common-Services Co., Ltd. a l'adresse: contact@common-services.com
 * ...........................................................................
 *
 * @package   SoNice_Etiquetage
 * @author    Alexandre D. <alexandre@common-services.com>
 * @copyright Copyright (c) 2011-2015 Common Services Co Ltd - 90/25 Sukhumvit 81 - 10260 Bangkok - Thailand
 * @license   Commercial license
 * Support by mail  :  support.sonice@common-services.com
 */

require_once(dirname(__FILE__).'/AdminOrdersSoniceEtiquetageController.inc.php');

/* ps 1.5 */
class AdminOrdersSoniceEtiquetageController extends ModuleAdminController
{

    public $module = 'sonice_etiquetage';
    public $name = 'sonice_etiquetage';



    public function __construct()
    {
        $this->path = _PS_MODULE_DIR_.$this->module.'/';

        $this->className = 'sonice_etiquetage';
        $this->display = 'edit';

        $this->id_lang = (int)Context::getContext()->language->id;

        $this->lang = true;
        $this->deleted = false;
        $this->colorOnBackground = false;

        if (Tools::getValue('sne_switcher') && Tools::getValue('sne_switcher') === 'inter') {
            $this->url = __PS_BASE_URI__.basename(_PS_MODULE_DIR_).'/'.$this->name.'_int/';
        } else {
            $this->url = __PS_BASE_URI__.basename(_PS_MODULE_DIR_).'/'.$this->name.'/';
        }

        $this->js = $this->url.'views/js/';
        $this->css = $this->url.'views/css/';
        $this->images = $this->url.'views/img/';
        $this->soniceOrdersController = new AdminSoniceOrdersControllerExt($this->id_lang);

        $this->multishop_context = Shop::CONTEXT_SHOP;
        $this->context = Context::getContext();
        $this->bootstrap = true;

        $cookie = new Cookie('sonice_current_employee', '/', time() + (86400 * 30));
        $cookie->variable_name = $this->context->employee->id;

        parent::__construct();
    }



    public function renderForm()
    {
        $this->context->smarty->assign(
                array(
                    'ps15x' => true,
                    'sne_token_order' => Tools::getAdminToken('AdminOrders'.(int)Tab::getIdFromClassName('AdminOrders').(int)$this->context->employee->id),
                )
        );

        $module_configuration = unserialize(Configuration::get('SONICE_ETQ_CONF'));
        if (isset($module_configuration['legacy']) && !$module_configuration['legacy']) {
            $this->addJS($this->js.'common-printserver.js');
        }

        $this->addJS($this->js.'orders.js');

        if (!version_compare(_PS_VERSION_, '1.6', '>=')) {
            $this->addCSS($this->css.'orders.css');
        } else {
            $this->addCSS($this->css.'orders16.css');
        }
        $this->addJqueryUI('ui.datepicker');

        // Live Chat Pro
        foreach ($this->context->controller->js_files as $key => $css) {
            if (strpos($css, 'livechatpro') !== false) {
                unset($this->context->controller->js_files[$key]);
            }
        }

        $html = $this->soniceOrdersController->content($this->context->smarty);

        return ($html.$this->content.parent::renderForm());
    }
}
