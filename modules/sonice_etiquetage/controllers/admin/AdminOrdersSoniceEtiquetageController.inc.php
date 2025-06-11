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

require_once(dirname(__FILE__).'/../../sonice_etiquetage.php');
require_once(dirname(__FILE__).'/../../classes/SoColissimoPDF.php');
require_once(dirname(__FILE__).'/../../classes/SoColissimoSession.php');
require_once(_PS_ROOT_DIR_.'/config/config.inc.php');


class AdminSoniceOrdersControllerExt extends SoNice_Etiquetage
{

    public $id_lang;
    public $url;
    public $path;
    private $module = 'sonice_etiquetage';
    protected $images;
    protected $debug;

    /** About 22 working days in a month */
    const NB_WORKING_DAY = 22;

    public function __construct($id_lang)
    {
        $this->id_lang = $id_lang;
        $this->protocol = Configuration::get('PS_SSL_ENABLED') ? 'https://' : 'http://';
        $this->url = $this->protocol.htmlspecialchars($_SERVER['HTTP_HOST'], ENT_COMPAT, 'UTF-8').
            __PS_BASE_URI__.basename(_PS_MODULE_DIR_).'/'.$this->module.'/';
        $this->path = str_replace('\\', '/', _PS_MODULE_DIR_).$this->module.'/';

        $this->ps15x = version_compare(_PS_VERSION_, '1.5', '>=');
    }

    /**
     * @param Smarty $smarty
     * @return mixed
     */
    public function content($smarty)
    {
        $id_shop = $this->ps15x ? Context::getContext()->shop->id : 1;

        $smarty->assign(
                array(
                    //'sne_orders' => SoColissimoPDF::getOrders(true, 0, 20),
                    'sne_labels' => SoColissimoPDF::getLabels(),
                    'sne_carrier_modify' => SoColissimoPDF::getCarriersAllowedModification(),
                    'sne_labels_available' => SoColissimoSession::getAvaibleOrders(),
                    'sne_session_list' => SoColissimoSession::getSessions(self::NB_WORKING_DAY, $id_shop),
                    'sne_carrier_list' => $this->getCarriers(),
                    'sne_webservice_url' => $this->url.'functions/get_labels.php',
                    'sne_pagination_url' => $this->url.'functions/pagination.php',
                    'sne_createsession_url' => $this->url.'functions/create_session.php',
                    'sne_usesession_url' => $this->url.'functions/use_session.php',
                    'sne_deletesession_url' => $this->url.'functions/delete_session.php',
                    'sne_deletelabel_url' => $this->url.'functions/delete_label.php',
                    'sne_updatesession_url' => $this->url.'functions/update_session.php',
                    'sne_getorderlist_url' => $this->url.'functions/get_order_list.php',
                    'sne_changesessionname_url' => $this->url.'functions/change_session_name.php',
                    'sne_generatelisting_url' => $this->url.'functions/generate_listing.php',
                    'sne_generatedeliveryslips_url' => $this->url.'functions/generate_delivery_slips.php',
                    'sne_generatetoday_url' => $this->url.'functions/generate_today.php',
                    'sne_downloadcn23_url' => $this->url.'functions/downloadcn23.php',
                    'sne_modifyaddress_url' => $this->url.'functions/modify_address.php',
                    'sne_modifyweight_url' => $this->url.'functions/modify_weight.php',
                    'sne_getlabelexpedition_url' => $this->url.'functions/get_label_expedition.php',
                    'sne_setorderassent_url' => $this->url.'functions/set_order_as_sent.php',
                    'sne_fusionsession_url' => $this->url.'functions/fusion_session.php',
                    'sne_changeordercarrier_url' => $this->url.'functions/change_order_carrier.php',
                    'sne_common_printserver' => $this->url.'functions/common-printserver.php',
                    'sne_print_zpl' => $this->url.'functions/print_zpl.php',
                    'sne_module_path' => $this->path,
                    'sne_module_url' => __PS_BASE_URI__.basename(_PS_MODULE_DIR_).'/'.$this->module.'/',
                    'sne_print_documents' => $this->url.'functions/print_documents.php',
                    'sne_default_session_name' => $this->getTodayDate(),
                    'sne_last_session_used' => Configuration::get('SONICE_ETQ_LAST_SESSION_USED'),
                    'sne_config' => unserialize(Configuration::get('SONICE_ETQ_CONF')),
                    'sne_is_installed' => Module::isInstalled('sonice_etiquetage'),
                    'sne_ps16x' => version_compare(_PS_VERSION_, '1.6', '>=') ? true : false,
                    'sne_context_key' => SoColissimoContext::getKey(Context::getContext()->shop)
                )
        );

        if (version_compare(_PS_VERSION_, '1.6', '>=')) {
            return ($smarty->fetch($this->path.'views/templates/admin/tab/AdminOrdersSoniceEtiquetage16.tpl'));
        }
        return ($smarty->fetch($this->path.'views/templates/admin/tab/AdminOrdersSoniceEtiquetage.tpl'));
    }



    private static function moduleIsEnabled($module_name)
    {
        if (version_compare(_PS_VERSION_, '1.5', '>=')) {
            return ((bool)Module::isEnabled($module_name));
        }
        return ((bool)Db::getInstance()->getValue(
            'SELECT `active`
            FROM `'._DB_PREFIX_.'module`
            WHERE `name` = "'.pSQL($module_name).'"'
        ));
    }



    private function getTodayDate()
    {
        if (Language::getIsoById($this->id_lang) === 'fr') {
            $jour = array('Dimanche', 'Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi');
            $mois = array('', 'Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre');

            $date = $jour[date('w')].' '.date('d').' '.$mois[date('n')].' '.date('Y');
        } else {
            $date = date('l d F Y');
        }

        return ($date);
    }



    private function getCarriers()
    {
        $result = array();
        $carrier_conf = unserialize(Configuration::get('SONICE_ETQ_CARRIER'));

        if (!$carrier_conf || !is_array($carrier_conf)) {
            return (false);
        }

        foreach ($carrier_conf as $carrier) {
            $shipping = new Carrier((int)$carrier);
            $result[] = $shipping->name;
        }

        return ($result);
    }
}
