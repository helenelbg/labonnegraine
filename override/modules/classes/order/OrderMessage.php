<?php

class OrderMessage extends OrderMessageCore
{
   
    /**
     * @see ObjectModel::$definition
     */
    public static $definition = [
        'table' => 'order_message',
        'primary' => 'id_order_message',
        'multilang' => true,
        'fields' => [
            'date_add' => ['type' => self::TYPE_DATE, 'validate' => 'isDate'],

            /* Lang fields */
            'name' => ['type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isGenericName', 'required' => true, 'size' => 128],
            'message' => ['type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isMessage', 'required' => true, 'size' => 5000],
        ],
    ];

}
