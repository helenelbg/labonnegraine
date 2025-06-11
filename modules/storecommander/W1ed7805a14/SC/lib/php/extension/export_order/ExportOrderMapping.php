<?php

class ExportOrderMapping extends ObjectModel
{
    public $id_extension_export_order_mapping;

    /** @var string Name */
    public $name;

    /** @var int Separator */
    public $separator;

    /** @var string list of fields : fields1|field2|... */
    public $fields;

    /** @var string one of : CSV PDF */
    public $export_format;

    /** @var string represents specific format props : key1::val1@@key2::val2... */
    public $format_properties;

    /** @var string Object creation date */
    public $date_add;

    /** @var string Object last modification date */
    public $date_upd;


     public static $definition = [
        'table' => SC_DB_PREFIX.'extension_export_order_mapping',
        'primary' => 'id_extension_export_order_mapping',
        'fields' => [
            'name' => ['type' => self::TYPE_STRING, 'validate' => 'isGenericName','required' => true, 'size' => 128],
            'separator' => ['type' => self::TYPE_INT, 'validate' => 'isInt'],
            'fields' => ['type' => self::TYPE_STRING],
            'export_format' => ['type' => self::TYPE_STRING, 'validate' => 'isCleanHtml'],
            'format_properties' => ['type' => self::TYPE_STRING, 'validate' => 'isCleanHtml'],
            'date_add' => ['type' => self::TYPE_DATE, 'validate' => 'isDate'],
            'date_upd' => ['type' => self::TYPE_DATE, 'validate' => 'isDate'],
        ]
    ];

    public static function getMappingList()
    {
        return Db::getInstance()->executeS('SELECT *, CONVERT((LENGTH(fields) - LENGTH(REPLACE(fields, \'__\', \'\')))/2 + 1, Signed) AS `number_field`
                                                FROM `'._DB_PREFIX_.self::$definition['table'].'`');
    }

    /**
     * Return the list of all fields aliases defined for this template.
     */
    public function getAliases()
    {
        $aliases = array();
        $parts = explode('__', $this->fields);
        foreach ($parts as $part)
        {
            $subParts = explode('|', $part);
            $aliases[] = $subParts[1];
        }

        return $aliases;
    }
}
