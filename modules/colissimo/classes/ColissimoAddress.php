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
 * Class ColissimoAddress
 */
class ColissimoAddress extends ObjectModel
{
     /** @var int $id_colissimo_address */
    public $id_colissimo_address;

    /** @var int $id_cart */
    public $id_cart;

    /** @var string $code_porte2 */
    public $code_porte1;
    
    /** @var string $code_porte2 */
    public $code_porte2;


    /** @var array $definition */
    public static $definition = array(
        'table'   => 'colissimo_address',
        'primary' => 'id_colissimo_address',
        'fields'  => array(
            'id_cart'       => array('type' => self::TYPE_INT, 'required' => true),
            'code_porte1'        => array('type' => self::TYPE_STRING, 'required' => false, 'size' => 8),
            'code_porte2' => array('type' => self::TYPE_STRING, 'required' => false, 'size' => 8),
        ),
    );


   /**
     * @param int $idCart
     * @return int
     */
    public static function getAddressByCartId($idCart)
    {
        $dbQuery = new DbQuery();
        $dbQuery->select('id_colissimo_address')
                ->from('colissimo_address')
                ->where('id_cart = '.(int) $idCart);
        
        return (int) Db::getInstance(_PS_USE_SQL_SLAVE_)
                       ->getValue($dbQuery);
    }
}
