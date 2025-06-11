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

require_once dirname(__FILE__).'/ColissimoWebService.php';
require_once dirname(__FILE__).'/ColissimoDepositSlipResponse.php';

class ColissimoDepositSlipRequest extends ColissimoWebService
{

    protected $orders = array();
    protected $parameters = array();

    public function __construct(array $orders)
    {
        parent::__construct();

        $this->orders = $orders;
    }

    public function create()
    {
        if (!count($this->orders)) {
            throw new Exception('No order list provided.');
        }

        $this->parameters = $this->prepareParameters();

        return new ColissimoDepositSlipResponse(
            $this->call(self::WS, 'generateBordereauByParcelsNumbers', $this->parameters)
        );
    }

    public function getParameters()
    {
        return $this->parameters;
    }

    protected function prepareParameters()
    {
        $parameters = array(
            'contractNumber' => $this->contract_number,
            'password' => $this->password,
            'generateBordereauParcelNumberList' => array(
                'parcelsNumbers' => $this->orders
            )
        );

        return $parameters;
    }
}
