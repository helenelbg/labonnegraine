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

class ColissimoDepositSlipResponse
{

    public $id = '';
    public $message = '';
    public $type = '';

    public $deposit_slip;

    protected $response;
    protected $path;
    protected $url;

    public function __construct(stdClass $response)
    {
        $this->response = $response;

        $this->id = $response->return->messages->id;
        $this->message = $response->return->messages->messageContent;
        $this->type = $response->return->messages->type;

        if (isset($response->return->bordereau)) {
            $this->deposit_slip = $response->return->bordereau->bordereauDataHandler;
            $this->path = realpath(dirname(__FILE__).'/../../download').'/bordereau_'.$this->getSecurityToken().'.pdf';
            $this->url = SoColissimoTools::getShopDomainSsl(true)._MODULE_DIR_.
                'sonice_etiquetage/download/bordereau_'.$this->getSecurityToken().'.pdf';
        }
    }

    public function getResponse()
    {
        return $this->response;
    }

    public function saveFile()
    {
        if ($this->type == 'ERROR' || !$this->deposit_slip) {
            throw new Exception($this->message, $this->id);
        }

        $path = $this->path.'';

        $file = fopen($path, 'w');

        if (fwrite($file, $this->deposit_slip) === false) {
            fclose($file);

            throw new Exception('Unable to write inside label file. Please check folder Permissions');
        }

        fclose($file);

        return true;
    }

    public function getPath()
    {
        return $this->path;
    }

    public function getDownloadLink()
    {
        return $this->url;
    }

    private function getSecurityToken()
    {
        static $token;

        if ($token) {
            return $token;
        }

//        $token = Configuration::get(
//            'SONICE_ETQ_TOKEN',
//            null,
//            Context::getContext()->shop->id_shop_group,
//            Context::getContext()->shop->id
//        );

        if (!$token) {
            $token = md5(_COOKIE_KEY_.time());
        }

        return $token;
    }
}
