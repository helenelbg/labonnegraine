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
 * @package   sonice_etiquetage
 * @author    Alexandre D.
 * @copyright Copyright (c) 2011-2015 Common Services Co Ltd - 90/25 Sukhumvit 81 - 10260 Bangkok - Thailand
 * @license   Commercial license
 * Support by mail  :  support.sonice_etiquetage@common-services.com
 */

class CommonPrintServer
{
    const URL = 'http://localhost:4567/';

    const RESPONSE_INDEX = 'response';

    private static function call($method, $post = false, $postfields = false, $verbose = false, $array = true)
    {
        $ch = curl_init();
        //error_log('URL CURL : '.self::URL.$method.(!$post && $postfields ? '/'.urlencode($postfields));
        curl_setopt($ch, CURLOPT_POST, $post);
        curl_setopt($ch, CURLOPT_URL, self::URL.$method.(!$post && $postfields ? '/'.urlencode($postfields) : ''));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Accept-Encoding: gzip,deflate',
            'Content-Type: application/json; charset="utf-8"'
        ));
        if ($post && $postfields) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, $postfields);
        }
        curl_setopt($ch, CURLOPT_VERBOSE, $verbose);

        try {
            $result = curl_exec($ch);
        } catch (Exception $excp) {
            curl_close($ch);

            return array(
                'response' => null,
                'error' => $excp->getMessage()
            );
        }

        curl_close($ch);

        return Tools::jsonDecode($result, $array);
    }

    public static function getPrinters()
    {
        $printers = self::call(__FUNCTION__);

        if (is_array($printers) && array_key_exists(self::RESPONSE_INDEX, $printers)) {
            $printers = array_filter(explode('|', $printers[self::RESPONSE_INDEX]));
        }

        return $printers;
    }

    public static function getPrinter()
    {
        return self::call(__FUNCTION__);
    }

    public static function setPrinter($printer_name)
    {
        return self::call(__FUNCTION__, false, $printer_name);
    }

    public static function printFileByURL($url)
    {
        return self::call(__FUNCTION__, true, $url);
    }
}
