<?php

class Attachment extends AttachmentCore
{
	public $picto;

	public static $definition = [
        'table' => 'attachment',
        'primary' => 'id_attachment',
        'multilang' => true,
        'fields' => [
            'file' => ['type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'required' => true, 'size' => 40],
			'picto' => ['type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'size' => 45],
            'mime' => ['type' => self::TYPE_STRING, 'validate' => 'isCleanHtml', 'required' => true, 'size' => 128],
            'file_name' => ['type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'size' => 128],
            'file_size' => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedId'],

            /* Lang fields */
            'name' => ['type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isGenericName', 'required' => true, 'size' => 32],
            'description' => ['type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isCleanHtml'],
        ],
        'associations' => [
            'products' => ['type' => self::HAS_MANY, 'field' => 'id_product', 'object' => 'Product', 'association' => 'product_attachment'],
        ],
    ];
	
}