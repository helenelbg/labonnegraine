<?php

class AwCustomCategory extends ObjectModel
{
    /** @var int $id_category */
    public $id_category;

    /** @var string $date_precommande */
    public $date_precommande;

    /** @var string $semaines */
    public $semaines;

    /** @var array $definition */
    public static $definition = array(
        'table'   => 'aw_custom_category',
        'primary' => 'id_aw_custom_category',
        'fields'  => array(
            'id_category'       => array('type' => self::TYPE_INT, 'required' => false),
            'date_precommande'        => array('type' => self::TYPE_STRING, 'required' => false, 'size' => 255),
            'date_precommande_b'        => array('type' => self::TYPE_STRING, 'required' => false, 'size' => 255),
            'date_precommande_date'        => array('type' => self::TYPE_STRING, 'required' => false, 'size' => 64),
            'semaines'       => array('type' => self::TYPE_HTML, 'required' => false),
        ),
    );

    /**
     * @param int $idCategory
     * @return AwCustomCategory
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     */
    public static function getByIdCategory($idCategory)
    {
        $dbQuery = new DbQuery();
        $dbQuery->select('id_aw_custom_category')
                ->from('aw_custom_category')
                ->where('id_category = '.(int) $idCategory);
        $id = Db::getInstance(_PS_USE_SQL_SLAVE_)
                ->getValue($dbQuery);

        return new self((int) $id);
    }
}
