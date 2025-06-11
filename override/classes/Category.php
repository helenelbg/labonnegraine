<?php
class Category extends CategoryCore {
   public static function getCategoryInformations($ids_category, $id_lang = null)
	{
		if ($id_lang === null)
			$id_lang = Context::getContext()->language->id;
		if (!is_array($ids_category) || !count($ids_category))
			return;
		$categories = array();
		$results = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
		SELECT c.`id_category`, cl.`name`, cl.`link_rewrite`, cl.`id_lang`
		FROM `'._DB_PREFIX_.'category` c
		LEFT JOIN `'._DB_PREFIX_.'category_lang` cl ON (c.`id_category` = cl.`id_category`'.Shop::addSqlRestrictionOnLang('cl').')
		'.Shop::addSqlAssociation('category', 'c').'
		WHERE cl.`id_lang` = '.(int)$id_lang.'
		AND c.`id_category` IN ('.implode(',', array_map('intval', $ids_category)).')');
		foreach ($results as $category)
			$categories[$category['id_category']] = $category;
		return $categories;
	}
	public static function recurseCategory($categories, $current, $id_category = 1, $id_selected = 1)
	{
		echo '<option value="'.$id_category.'"'.(($id_selected == $id_category) ? ' selected="selected"' : '').'>'.
			str_repeat('&nbsp;', $current['infos']['level_depth'] * 5).stripslashes($current['infos']['name']).'</option>';
		if (isset($categories[$id_category]))
			foreach (array_keys($categories[$id_category]) as $key)
				Category::recurseCategory($categories, $categories[$id_category][$key], $key, $id_selected);
	}
   
    /*
    * module: creativeelements
    * date: 2024-12-13 15:54:11
    * version: 2.11.0
    */
    const CE_OVERRIDE = true;
    /*
    * module: creativeelements
    * date: 2024-12-13 15:54:11
    * version: 2.11.0
    */
    public function __construct($idCategory = null, $idLang = null, $idShop = null)
    {
        parent::__construct($idCategory, $idLang, $idShop);
        $ctrl = Context::getContext()->controller;
        if ($ctrl instanceof CategoryController && !CategoryController::$initialized && !$this->active && Tools::getIsset('id_employee') && Tools::getIsset('adtoken')) {
            $tab = 'AdminCategories';
            if (Tools::getAdminToken($tab . (int) Tab::getIdFromClassName($tab) . (int) Tools::getValue('id_employee')) == Tools::getValue('adtoken')) {
                $this->active = 1;
            }
        }
    }
}
?>
