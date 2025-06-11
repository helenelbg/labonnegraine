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

class SoColissimoCurlParser
{

    public $curl_response = null;
    public $label_binary = null;
    public $cn23_binary = null;
    public $has_cn23 = false;
    public $module_conf = array();



    public function __construct($curl_response)
    {
        $this->curl_response = $curl_response;
        if (preg_match('/<cn23>[\w\W].*<\/cn23>/', $curl_response)) {
            $this->has_cn23 = true;
        }

        $this->module_conf = unserialize(Configuration::get(
            'SONICE_ETQ_CONF',
            null,
            Context::getContext()->shop->id_shop_group,
            Context::getContext()->shop->id
        ));
    }



    public function parse()
    {
        if ($this->label_binary || $this->cn23_binary) {
            return ($this);
        }

        $response_to_parse = $this->curl_response;
        $response_to_parse = preg_replace('/--uu[\s\S].*/', '', $response_to_parse);
        // Sometimes the flag "s" causes error, solution is to remove it.
        $response_to_parse = preg_replace('/<soap[\s\S.*].*Envelope>/s', '', $response_to_parse);
        $response_to_parse = trim(preg_replace('/^Content[\s\S].*/m', '', $response_to_parse));

        if ($this->has_cn23) {
            $response_to_parse = explode('%PDF', $response_to_parse);
            $response_to_parse = array_values(array_filter($response_to_parse));

            $this->label_binary = $response_to_parse[0];
            $this->cn23_binary = '%PDF'.$response_to_parse[1];
        } else {
            $this->label_binary = $response_to_parse;
        }

        return ($this);
    }



    public function getLabelBinary()
    {
        if (preg_match('/^PDF/', $this->module_conf['output_print_type'])
            && Tools::substr($this->label_binary, 0, 4) != '%PDF') {
            $this->label_binary = '%PDF'.$this->label_binary;
        }

        return $this->label_binary;
    }


    public function getCN23Binary()
    {
        if (Tools::substr($this->cn23_binary, 0, 4) != '%PDF') {
            $this->cn23_binary = '%PDF'.$this->cn23_binary;
        }

        return $this->cn23_binary;
    }
}
