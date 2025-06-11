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

class SoNiceEtiquetageSupport
{

    /** @const Documentation base URL */
    const SONICE_ETQ_DOC_URL = 'http://documentation.common-services.com/sonice_etiquetage/';

    /** @var array  */
    public static $error_list = array(
        30518 => 'erreur-30518-le-numero-tarifaire-dun-article-na-pas-ete-transmis/',
        30301 => 'err-30301-poids-colis-incorrect/',
        30008 => 'erreur-30008-service-non-autorise-pour-cet-identifiant/',
        30000 => 'erreur-30000-identifiant-mdp-incorrect/',
        30400 => 'err-30400-code-point-de-retrait-pas-transmis/',
        40015 => 'erreur-40015-service-momentanement-indisponible/',
        'error_occured' => 'une-erreur-sest-produite/',
        'tcpdf' => 'tcpdf-error-image-unable-to-get-the-size-of-the-image/',
        'ajax' => 'lexecution-du-script-ajax-a-echoue/',
        30221 => 'erreur-30221-le-numero-de-portable-du-destinataire-est-incorrect/',
        30403 => 'erreur-30403-le-code-ou-ladresse-point-de-retrait-na-pas-ete-transmis/',
        30102 => 'erreur-30102-code-pays-expediteur-pas-transmis/'
    );

    /**
     * Get URL to the documentation from an error code
     *
     * @param null|string|int $error_code
     * @return null|string
     */
    public static function getDocumentationLink($error_code = null)
    {
        if (!$error_code || !array_key_exists($error_code, self::$error_list)) {
            return null;
        }
        return sprintf('%s%s', self::SONICE_ETQ_DOC_URL, self::$error_list[$error_code]);
    }
}
