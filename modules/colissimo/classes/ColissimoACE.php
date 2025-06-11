<?php
/**
 * 2007-2020 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2020 PrestaShop SA
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 */

/**
 * Class ColissimoACE
 */
class ColissimoACE extends ObjectModel
{
    /** @var int $id_colissimo_ace */
    public $id_colissimo_ace;

    /** @var int $ref_order */
    public $ref_order;

    /** @var string $shipping_number */
    public $shipping_number;

    /** @var array $definition */
    public static $definition = array(
        'table' => 'colissimo_ace',
        'primary' => 'id_colissimo_ace',
        'fields' => array(
            'ref_order' => array('type' => self::TYPE_INT, 'required' => false),
            'shipping_number' => array('type' => self::TYPE_STRING, 'required' => false, 'size' => 64),
        ),
    );

    /**
     * @var array $webserviceParameters
     */
    protected $webserviceParameters = array(
        'objectMethods' => array('add' => 'addWs'),
        'objectNodeName' => 'colissimo_ace',
        'fields' => array(
            'id_order' => array(),
            'shipping_number' => array(),
        ),
    );

    /**
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     */
    public function addWs()
    {
        /** @var Colissimo $module */
        $module = Module::getInstanceByName('colissimo');
        $module->logger->setChannel('ACE');
        if (ColissimoOrder::exists($this->ref_order)) {
            $order = new Order((int) $this->ref_order);
            $colissimoLabel = new ColissimoLabel();
            $colissimoLabel->id_colissimo_order = (int) ColissimoOrder::getIdByOrderId($this->ref_order);
            $colissimoLabel->id_colissimo_deposit_slip = 0;
            $colissimoLabel->shipping_number = pSQL($this->shipping_number);
            $colissimoLabel->label_format = 'pdf';
            $colissimoLabel->return_label = 0;
            $colissimoLabel->cn23 = 0;
            $colissimoLabel->coliship = 1;
            $colissimoLabel->migration = 0;
            $colissimoLabel->insurance = null;
            $colissimoLabel->file_deleted = 0;
            $colissimoLabel->save(true);
            $orderCarrier = ColissimoOrderCarrier::getByIdOrder($this->ref_order);
            if (Validate::isLoadedObject($orderCarrier) && !$orderCarrier->tracking_number) {
                $orderCarrier->tracking_number = pSQL($colissimoLabel->shipping_number);
                $orderCarrier->save();
                $hash = md5($order->reference.$order->secure_key);
                $link = Context::getContext()->link->getModuleLink(
                    'colissimo',
                    'tracking',
                    array('order_reference' => $order->reference, 'hash' => $hash)
                );
                $isoLangOrder = Language::getIsoById($order->id_lang);
                if (isset($this->module->PNAMailObject[$isoLangOrder])) {
                    $object = $module->PNAMailObject[$isoLangOrder];
                } else {
                    $object = $module->PNAMailObject['en'];
                }
                ColissimoTools::sendHandlingShipmentMail(
                    $order,
                    sprintf($object, $order->reference),
                    $link
                );
                $module->logger->info('Send tracking mail for shipment '.$colissimoLabel->shipping_number);
            }
            if (Configuration::get('COLISSIMO_USE_SHIPPING_IN_PROGRESS')) {
                $idShippingInProgressOS = Configuration::get('COLISSIMO_OS_SHIPPING_IN_PROGRESS');
                $shippingInProgressOS = new OrderState((int) $idShippingInProgressOS);
                if (Validate::isLoadedObject($shippingInProgressOS)) {
                    if (!$order->getHistory(Context::getContext()->language->id, (int) $idShippingInProgressOS)) {
                        $history = new OrderHistory();
                        $history->id_order = (int) $order->id;
                        $history->changeIdOrderState($idShippingInProgressOS, (int) $order->id);
                        try {
                            $history->add();
                        } catch (Exception $e) {
                            $module->logger->error(sprintf('Cannot change status of order #%d', $order->id));
                        }
                    }
                } else {
                    $module->logger->error('Shipping in Progress order state is not valid');
                }
            }
            $module->logger->info('Label imported successfully.');

            return true;
        }

        return false;
    }

    /**
     * @param bool $die
     * @param bool $error_return
     * @return bool|string
     */
    public function validateFields($die = true, $error_return = false)
    {
        if (($id = ColissimoLabel::getLabelIdByShippingNumber($this->shipping_number)) !== 0) {
            return sprintf('Colissimo label already exists (label ID #%d)', (int) $id);
        }

        return true;
    }
}
