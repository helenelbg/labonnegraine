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

if (version_compare(_PS_VERSION_, '1.5', '<') && defined('PS_ADMIN_DIR') && file_exists(PS_ADMIN_DIR.'/../classes/AdminTab.php')) {
    include_once(PS_ADMIN_DIR.'/../classes/AdminTab.php');
}

require_once(dirname(__FILE__).'/sonice_etiquetage.php');
require_once(dirname(__FILE__).'/controllers/admin/AdminOrdersSoniceEtiquetageController.inc.php');


class OrdersSoniceEtiquetage extends AdminTab
{

    private $module = 'sonice_etiquetage';

    public function __construct()
    {
        global $cookie;

        $this->id_lang = (int) $cookie->id_lang;
        $this->protocol = Configuration::get('PS_SSL_ENABLED') ? 'https://' : 'http://';
        $this->url = $this->protocol.htmlspecialchars($_SERVER['HTTP_HOST'], ENT_COMPAT, 'UTF-8').__PS_BASE_URI__.basename(_PS_MODULE_DIR_).'/'.$this->module.'/';
        $this->soniceController = new AdminSoniceOrdersControllerExt($this->id_lang);
        $this->js = $this->url.'views/js/';
        $this->css = $this->url.'views/css/';
        $this->images = $this->url.'views/img/';

        // Orders list
        $this->cookie = $cookie;

        parent::__construct();
    }



    public function display()
    {
        global $smarty;

        includeDatepicker('nothing');

        $module_configuration = unserialize(Configuration::get('SONICE_ETQ_CONF'));
        if (isset($module_configuration['legacy']) && !$module_configuration['legacy']) {
            $this->_addJS($this->js.'common-printserver.js');
        }

        $this->_addJS($this->js.'orders.js');
        $this->_addCSS($this->css.'orders.css');
        $this->_addCSS($this->css.'orders_compat.css');

        $smarty->assign(
                array(
                    'ps15x' => false,
                    'sne_token_order' => Tools::getAdminToken('AdminOrders'.(int) Tab::getIdFromClassName('AdminOrders').(int) $this->cookie->id_employee),
                    'sne_url' => $this->url,
                    'sne_css' => $this->css,
                    'sne_js' => $this->js,
                    'sne_img' => $this->images
                )
        );

        echo($this->soniceController->content($smarty));
    }



    private function _addCSS($css)
    {
        echo('<link type="text/css" rel="stylesheet" href="'.$css.'" />'."\n");
        return (true);
    }



    private function _addJS($js)
    {
        echo('<script type="text/javascript" src="'.$js.'"></script>'."\n");
        return (true);
    }
}
