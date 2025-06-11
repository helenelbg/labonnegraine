<?php
/**
 * Copyright ETS Software Technology Co., Ltd
 *
 * NOTICE OF LICENSE
 *
 * This file is not open source! Each license that you purchased is only available for 1 website only.
 * If you want to use this file on more websites (or projects), you need to purchase additional licenses.
 * You are not allowed to redistribute, resell, lease, license, sub-license or offer our resources to any third party.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future.
 *
 * @author ETS Software Technology Co., Ltd
 * @copyright  ETS Software Technology Co., Ltd
 * @license    Valid for 1 website (or project) for each purchase of license
 */
 
if (!defined('_PS_VERSION_')) {
    exit();
}
class Ets_PaymentMethodField extends ObjectModel
{
    /**
     * @var int
     */
    public $id_ets_am_payment_method_field;
    /**
     * @var int
     */
    public $id_payment_method;
    /**
     * @var int
     */
    public $sort;
    /**
     * @var string
     */
    public $type;
    public $required;
    public $enable;
    public $deleted;
    public static $definition = array(
        'table' => 'ets_am_payment_method_field',
        'primary' => 'id_ets_am_payment_method_field',
        'fields' => array(
            'id_ets_am_payment_method_field' => array(
                'type' => self::TYPE_INT,
                'validate' => 'isUnsignedInt'
            ),
            'id_payment_method' => array(
                'type' => self::TYPE_INT,
                'validate' => 'isUnsignedInt'
            ),
            'type' => array(
                'type' => self::TYPE_STRING,
                'validate' => 'isString'
            ),
            'required' => array(
                'type' => self::TYPE_INT,
                'validate' => 'isUnsignedInt'
            ),
            'enable' => array(
                'type' => self::TYPE_INT,
                'validate' => 'isUnsignedInt'
            ),
            'sort' => array(
                'type' => self::TYPE_INT,
                'validate' => 'isUnsignedInt'
            ),
            'deleted' => array(
                'type' => self::TYPE_INT,
                'validate' => 'isUnsignedInt'
            ),
        )
    );
    public function createPaymentMethodField($params)
    {
        $this->id_payment_method = $params['id_payment_method'];
        $this->type = $params['type'];
        $this->sort = isset($params['sort']) ? (int)$params['sort'] : 1;
        $this->required = (int)$params['required'];
        $this->enable = (int)$params['enable'];
        if ($this->add()) {
            $languages = Language::getLanguages(false);
            $default_title = '';
            foreach ($params['title'] as $ft) {
                if ($ft) {
                    $default_title = $ft;
                    break;
                }
            }
            $sql_fields = array();
            foreach ($languages as $lang) {
                $id_lang = $lang['id_lang'];
                $sql_fields[] = " INSERT INTO `" . _DB_PREFIX_ . "ets_am_payment_method_field_lang` (id_payment_method_field, id_lang, title, description) VALUES(" . (int)$this->id . ", " . (int)$id_lang . ", '" . pSQL(isset($params['title'][$id_lang]) && $params['title'][$id_lang] ? $params['title'][$id_lang] : $default_title) . "', '" . pSQL(isset($params['desc'][$id_lang]) ? $params['desc'][$id_lang] : '') . "');";
            }
            if ($sql_fields) {
                foreach($sql_fields as $sql_field)
                    Db::getInstance()->execute($sql_field);
                return $this->id;
            }
        }
        return false;
    }
}
