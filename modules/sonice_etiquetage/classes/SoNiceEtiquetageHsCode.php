<?php
/**
 * NOTICE OF LICENSE
 *
 * This source file is subject to a commercial license from Common-Services Co., Ltd.
 * Use, copy, modification or distribution of this source file without written
 * license agreement from the SARL SMC is strictly forbidden.
 * In order to obtain a license, please contact us: contact@common-services.com
 * ...........................................................................
 * INFORMATION SUR LA LICENCE D'UTILISATION
 *
 * L'utilisation de ce fichier source est soumise a une licence commerciale
 * concedee par la societe Common-Services Co., Ltd.
 * Toute utilisation, reproduction, modification ou distribution du present
 * fichier source sans contrat de licence ecrit de la part de la Common-Services Co. Ltd. est
 * expressement interdite.
 * Pour obtenir une licence, veuillez contacter Common-Services Co., Ltd. a l'adresse: contact@common-services.com
 * ...........................................................................
 *
 * @package   SoNice_Etiquetage
 * @author    Alexandre D. <alexandre@common-services.com>
 * @copyright Copyright (c) 2011-2015 Common Services Co Ltd - 90/25 Sukhumvit 81 - 10260 Bangkok - Thailand
 * @license   Commercial license
 * Support by mail  :  support.sonice@common-services.com
 */

class SoNiceEtiquetageHsCode
{

    /**
     * Save hs_codes from the module configuration
     *
     * @param $hs_codes array
     * @return bool
     */
    public static function saveHsCodes($hs_codes)
    {
        if (!is_array($hs_codes) || !count($hs_codes)) {
            return false;
        }

        $success = true;
        foreach ($hs_codes as $id_category => $hs_code) {
            $success &= (bool)Db::getInstance()->execute(
                'REPLACE INTO `'._DB_PREFIX_.'sonice_etq_hscode` (`id_category`, `hscode`)
				VALUES ('.(int)$id_category.', "'.pSQL($hs_code).'")
			');
        }

        return $success;
    }

    /**
     * Get all hs_codes saved in database, not indexed
     *
     * @return array
     * @throws PrestaShopDatabaseException
     */
    public static function getHsCodes()
    {
        return (array)Db::getInstance()->executeS(
            'SELECT *
			FROM `'._DB_PREFIX_.'sonice_etq_hscode`'
        );
    }

    /**
     * Get all hs_codes saved in databse, indexed by Category ID
     *
     * @return array
     */
    public static function getHsCodesPerCategory()
    {
        $hs_codes = self::getHsCodes();
        $hs_codes_per_category = array();

        foreach ($hs_codes as $hs_code) {
            $hs_codes_per_category[(int)$hs_code['id_category']] = $hs_code['hscode'];
        }

        return $hs_codes_per_category;
    }

    /**
     * Get the hs_code of a given category
     *
     * @param $id_category
     * @return mixed
     */
    public static function getHsCodeByIdCategory($id_category)
    {
        $hs_codes = self::getHsCodesPerCategory();

        if (array_key_exists($id_category, $hs_codes)) {
            return $hs_codes[$id_category];
        }
        return null;
    }

    /**
     * Get the hs_code for a product
     *
     * @param $id_product
     * @return mixed
     */
    public static function getProductHsCode($id_product)
    {
        $product = new Product($id_product);
        if (!Validate::isLoadedObject($product)) {
            return null;
        }

        $product_default_category = new Category($product->id_category_default);
        if (!Validate::isLoadedObject($product_default_category)) {
            return null;
        }

        $product_categories = $product_default_category->getParentsCategories(Context::getContext()->language->id);

        foreach ($product_categories as $product_category) {
            $hs_code = self::getHsCodeByIdCategory((int)$product_category['id_category']);
            if ($hs_code) {
                return $hs_code;
            }
        }

        $product_categories = array_reverse($product->getCategories());
        foreach ($product_categories as $product_category) {
            $hs_code = self::getHsCodeByIdCategory((int)$product_category);
            if ($hs_code) {
                return $hs_code;
            }
        }

        return null;
    }
}
