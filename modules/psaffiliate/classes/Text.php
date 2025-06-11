<?php
/**
 * NOTICE OF LICENSE
 *
 * This file is licenced under the Software License Agreement.
 * With the purchase or the installation of the software in your application
 * you accept the licence agreement.
 *
 * You must not modify, adapt or create derivative works of this source code.
 *
 * @author    Active Design <office@activedesign.ro>
 * @copyright 2016-2018 Active Design
 * @license   LICENSE.txt
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

class Text extends ObjectModel
{
    public $id;
    public $id_text;
    public $title;
    public $text;
    public $active;

    public static $definition = array(
        'table' => 'aff_texts',
        'primary' => 'id_text',
        'fields' => array(
            'id_text' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt', 'copy_post' => false),
            'title' => array('type' => self::TYPE_STRING, 'validate' => 'isString', 'copy_post' => false),
            'text' => array('type' => self::TYPE_NOTHING, 'validate' => 'isCleanHtml', 'copy_post' => false),
            'active' => array('type' => self::TYPE_BOOL, 'validate' => 'isBool', 'copy_post' => false),
        ),
    );

    public function __construct($id = null)
    {
        parent::__construct($id);
    }

    public function toggleActive()
    {
        $id_text = (int)Tools::getValue('id_text');
        if ($id_text) {
            $sql = Db::getInstance()->execute("UPDATE `"._DB_PREFIX_."aff_texts` SET `active` = (CASE WHEN `active`='1' THEN '0' WHEN `active`='0' THEN '1' WHEN `active` IS NULL THEN '1' END) WHERE `id_text`='".(int)$id_text."' LIMIT 1;");

            return $sql;
        }

        return false;
    }

    public static function getTexts($active = false, $id_affiliate = false)
    {
        $sql = 'SELECT * FROM `'._DB_PREFIX_.'aff_texts`';
        if ($active) {
            $sql .= ' WHERE `active`="1"';
        }
        $texts = Db::getInstance()->executeS($sql);
        foreach ($texts as &$text) {
            $text['text_parsed'] = str_replace('%site_name%', Configuration::get('PS_SHOP_NAME'), $text['text']);
            $text['text_parsed'] = str_replace(
                '%link%',
                Psaffiliate::getAffiliateLink($id_affiliate),
                $text['text_parsed']
            );
        }

        return $texts;
    }

    public static function hasTexts($active = false)
    {
        $texts = self::getTexts($active);

        return (bool)sizeof($texts);
    }
}
