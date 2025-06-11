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

require_once _PS_MODULE_DIR_.'psaffiliate/classes/Affiliate.php';

class Customfield extends ObjectModel
{
    public $id_field;
    public $type;
    public $required;
    public $active;
    public $name;

    public static $definition = array(
        'table' => 'aff_custom_fields',
        'primary' => 'id_field',
        'multilang' => true,
        'fields' => array(
            'id_field' => array(
                'type' => self::TYPE_INT,
                'validate' => 'isUnsignedInt',
                'copy_post' => false,
            ),
            'type' => array(
                'type' => self::TYPE_STRING,
                'validate' => 'isGenericName',
                'values' => array('text', 'textarea', 'link'),
                'default' => 'text',
                'copy_post' => false,
            ),
            'required' => array(
                'type' => self::TYPE_BOOL,
                'validate' => 'isBool',
                'copy_post' => false,
            ),
            'active' => array(
                'type' => self::TYPE_BOOL,
                'validate' => 'isBool',
                'copy_post' => false,
            ),
            // Multi-language fields.
            'name' => array(
                'type' => self::TYPE_STRING,
                'lang' => true,
                'validate' => 'isGenericName',
                'required' => true,
                'size' => 128,
            ),
        ),
    );

    public function toggleRequired()
    {
        // Update only active field
        $this->setFieldsToUpdate(array('required' => true));

        // Update active status on object
        $this->required = !(int)$this->required;

        // Change status to active/inactive
        return $this->update(false);
    }

    public static function all($activeOnly = true)
    {
        $langTable = _DB_PREFIX_.static::$definition['table'].'_lang';

        $query = (new DbQuery())
            ->select('cf.*, cfl.`name`')
            ->from(static::$definition['table'], 'cf')
            ->join("left join `{$langTable}` cfl on cf.`id_field` = cfl.`id_field`")
            ->where('cfl.`id_lang` = '.(int)Context::getContext()->language->id);

        if ($activeOnly) {
            $query->where('cf.`active` = 1');
        }

        $results = Db::getInstance()->executeS($query);

        return $results ?: array();
    }

    public static function allByAffiliate($affiliate)
    {
        if (!is_object($affiliate)) {
            $affiliate = new Affiliate((int)$affiliate);
        }

        if (!Validate::isLoadedObject($affiliate)) {
            return array();
        }

        $langTable = _DB_PREFIX_.static::$definition['table'].'_lang';
        $metaTable = _DB_PREFIX_.Affiliate::getMetaTable();

        $query = (new DbQuery())
            ->select('cf.*, cfl.`name`, m.`value`')
            ->from(static::$definition['table'], 'cf')
            ->join("left join `{$langTable}` cfl on cf.`id_field` = cfl.`id_field`")
            ->join("left join `{$metaTable}` m on m.`key` = concat(\"custom_field_\", cf.`id_field`)")
            ->where('cfl.`id_lang` = '.(int)Context::getContext()->language->id)
            ->where('m.`id_affiliate` = '.(int)$affiliate->id);

        $results = Db::getInstance()->executeS($query);

        return $results ?: array();
    }

    public function delete()
    {
        // Delete associated metas.
        if ($status = parent::delete()) {
            Affiliate::deleteMetaByKey('custom_field_'.$this->id);
        }

        return $status;
    }
}
