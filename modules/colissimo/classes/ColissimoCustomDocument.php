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
 * Class ColissimoCustomCategory
 */
class ColissimoCustomDocument extends ObjectModel
{
    /** @var int $id_colissimo_label */
    public $id_colissimo_label;

    /** @var int $id_colissimo_order */
    public $id_colissimo_order;

    /** @var string $id_document */
    public $id_document;
    
    /** @var string $type */
    public $type;
    
    /** @var string $date_add */
    public $date_add;

    /** @var array $definition */
    public static $definition = array(
        'table'   => 'colissimo_custom_document',
        'primary' => 'id_colissimo_custom_document',
        'fields'  => array(
            'id_colissimo_label'       => array('type' => self::TYPE_INT, 'required' => true),
            'id_colissimo_order'        => array('type' => self::TYPE_INT, 'required' => true),
            'id_document' => array('type' => self::TYPE_STRING, 'required' => true, 'size' => 50),
            'type'           => array('type' => self::TYPE_STRING, 'required' => true, 'size' => 20),
            'date_add'     => array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
        ),
    );
    
    /**
     * @param string $idLabel
     * @param string $type
     * @return int
     */
    public static function getCustomDocumentId($idLabel, $type)
    {
        $dbQuery = new DbQuery();
        $dbQuery->select('id_colissimo_custom_document')
                ->from('colissimo_custom_document')
                ->where('id_colissimo_label = "'.(int) $idLabel.'"')
                ->where('type = "'.pSQL($type).'"');

        return (int) Db::getInstance(_PS_USE_SQL_SLAVE_)
                       ->getValue($dbQuery);
    }
}
