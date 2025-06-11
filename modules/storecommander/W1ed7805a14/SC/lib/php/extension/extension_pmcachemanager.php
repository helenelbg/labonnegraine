<?php

class ExtensionPMCM
{
    public static $modInstance;

    public static function hasPMCacheManager()
    {
        $return = false;
        if (!empty(self::$modInstance) && is_object(self::$modInstance))
        {
            $return = self::$modInstance;
        }
        else
        {
            $version = SCI::getConfigurationValue('PM_CM_LAST_VERSION');
            if (!empty($version))
            {
                if ($moduleInstance = Module::getInstanceByName('pm_cachemanager'))
                {
                    $return = $moduleInstance;
                    self::$modInstance = $moduleInstance;
                }
            }
        }

        return $return;
    }

    public static function clearFromIdsProduct($ids)
    {
        $modInstance = self::hasPMCacheManager();
        if (!empty($modInstance) && is_object($modInstance))
        {
            if (!is_array($ids))
            {
                $ids = explode(',', $ids);
            }
            if (!empty($ids))
            {
                pm_cachemanager::deleteCacheFromIdProduct($ids);
            }
        }
    }

    public static function clearFromIdsCategory($ids_cat)
    {
        $modInstance = self::hasPMCacheManager();
        if (!empty($modInstance) && is_object($modInstance))
        {
            if (!is_array($ids_cat))
            {
                $ids_cat = explode(',', $ids_cat);
            }

            $ids = array();
            foreach ($ids_cat as $id_cat)
            {
                $ids_product = $modInstance->getIdsProductFromIdCategory($id_cat);
                if (!empty($ids_product))
                {
                    if (!empty($ids) && count($ids) > 0)
                    {
                        $ids = array_merge($ids, $ids_product);
                    }
                    else
                    {
                        $ids = $ids_product;
                    }
                }
            }

            if (!empty($ids) && $modInstance->_isFilledArray($ids))
            {
                pm_cachemanager::deleteCacheFromIdProduct($ids);
            }
        }
    }

    public static function clearFromIdsAttributeGroup($ids_ag)
    {
        $modInstance = self::hasPMCacheManager();
        if (!empty($modInstance) && is_object($modInstance))
        {
            if (!is_array($ids_ag))
            {
                $ids_ag = explode(',', $ids_ag);
            }

            $ids = array();
            foreach ($ids_ag as $id_ag)
            {
                $ids_product = $modInstance->getIdsProductFromIdAttributeGroup($id_ag);
                if (!empty($ids_product))
                {
                    if (!empty($ids) && count($ids) > 0)
                    {
                        $ids = array_merge($ids, $ids_product);
                    }
                    else
                    {
                        $ids = $ids_product;
                    }
                }
            }

            if (!empty($ids) && $modInstance->_isFilledArray($ids))
            {
                pm_cachemanager::deleteCacheFromIdProduct($ids);
            }
        }
    }

    public static function clearFromIdsAttribute($ids_a)
    {
        $modInstance = self::hasPMCacheManager();
        if (!empty($modInstance) && is_object($modInstance))
        {
            if (!is_array($ids_a))
            {
                $ids_a = explode(',', $ids_a);
            }

            $ids = array();
            foreach ($ids_a as $id_a)
            {
                $ids_product = $modInstance->getIdsProductFromIdAttribute($id_a);
                if (!empty($ids_product))
                {
                    if (!empty($ids) && count($ids) > 0)
                    {
                        $ids = array_merge($ids, $ids_product);
                    }
                    else
                    {
                        $ids = $ids_product;
                    }
                }
            }

            if (!empty($ids) && $modInstance->_isFilledArray($ids))
            {
                pm_cachemanager::deleteCacheFromIdProduct($ids);
            }
        }
    }

    public static function clearFromIdsFeature($ids_f)
    {
        $modInstance = self::hasPMCacheManager();
        if (!empty($modInstance) && is_object($modInstance))
        {
            if (!is_array($ids_f))
            {
                $ids_f = explode(',', $ids_f);
            }

            $ids = array();
            foreach ($ids_f as $id_f)
            {
                $ids_product = $modInstance->getIdsProductFromIdFeature($id_f);
                if (!empty($ids_product))
                {
                    if (!empty($ids) && count($ids) > 0)
                    {
                        $ids = array_merge($ids, $ids_product);
                    }
                    else
                    {
                        $ids = $ids_product;
                    }
                }
            }

            if (!empty($ids) && $modInstance->_isFilledArray($ids))
            {
                pm_cachemanager::deleteCacheFromIdProduct($ids);
            }
        }
    }

    public static function clearFromIdsFeatureValue($ids_fv)
    {
        $modInstance = self::hasPMCacheManager();
        if (!empty($modInstance) && is_object($modInstance))
        {
            if (!is_array($ids_fv))
            {
                $ids_fv = explode(',', $ids_fv);
            }

            $ids = array();
            foreach ($ids_fv as $id_fv)
            {
                $ids_product = $modInstance->getIdsProductFromIdFeatureValue($id_fv);
                if (!empty($ids_product))
                {
                    if (!empty($ids) && count($ids) > 0)
                    {
                        $ids = array_merge($ids, $ids_product);
                    }
                    else
                    {
                        $ids = $ids_product;
                    }
                }
            }

            if (!empty($ids) && $modInstance->_isFilledArray($ids))
            {
                pm_cachemanager::deleteCacheFromIdProduct($ids);
            }
        }
    }
}

$ajax = Tools::getValue('ajax', 0);
if ($ajax && $moduleInstance = Module::getInstanceByName('pm_cachemanager'))
{
    //Clear product cache on product edit
    if (((strpos(Tools::getValue('act'), 'cat_') !== false) && ((strpos(Tools::getValue('act'), '_update') !== false) || (strpos(Tools::getValue('act'), '_delete') !== false)))
            &&
            (($id_product = Tools::getValue('id_product')) || ($id_product = Tools::getValue('product_list')) || ($id_product = Tools::getValue('idlist'))
                || ((strpos(Tools::getValue('act'), 'cat_product') !== false) && ($id_product = Tools::getValue('gr_id'))))
        ) {
        pm_cachemanager::deleteCacheFromIdProduct(explode(',', $id_product));
    }
    //Clear product cache on category update
    elseif ((($id_category = Tools::getValue('id_category')) || ($id_category = Tools::getValue('id_parent'))) && (Tools::getValue('act') == 'cat_category_update'))
    {
        $ids_product = $moduleInstance->getIdsProductFromIdCategory($id_category);
        if ($moduleInstance->_isFilledArray($ids_product))
        {
            pm_cachemanager::deleteCacheFromIdProduct($ids_product);
        }
    }
    //Clear product cache on category move
    elseif ((($id_category1 = Tools::getValue('idNewParent')) && ($id_category2 = Tools::getValue('idCateg'))) && (Tools::getValue('act') == 'cat_category_update'))
    {
        $ids_product = $moduleInstance->getIdsProductFromIdCategory($id_category);
        $ids_product = array_merge((array) $ids_product, (array) $moduleInstance->getIdsProductFromIdCategory($id_category2));
        if ($moduleInstance->_isFilledArray($ids_product))
        {
            pm_cachemanager::deleteCacheFromIdProduct($ids_product);
        }
    }
    //Clear product cache on attribute group update
    elseif (($id_attribute_group = Tools::getValue('gr_id')) && Tools::getValue('act') == 'cat_group_update')
    {
        $ids_product = $moduleInstance->getIdsProductFromIdAttributeGroup($id_attribute_group);
        if ($moduleInstance->_isFilledArray($ids_product))
        {
            pm_cachemanager::deleteCacheFromIdProduct($ids_product);
        }
    }
    //Clear product cache on attribute update
    elseif (($id_attribute = Tools::getValue('gr_id')) && Tools::getValue('act') == 'cat_attribute_update')
    {
        $ids_product = $moduleInstance->getIdsProductFromIdAttribute($id_attribute);
        if ($moduleInstance->_isFilledArray($ids_product))
        {
            pm_cachemanager::deleteCacheFromIdProduct($ids_product);
        }
    }
    //Clear product cache on feature update
    elseif (($id_feature = Tools::getValue('gr_id')) && Tools::getValue('act') == 'cat_feature_update')
    {
        $ids_product = $moduleInstance->getIdsProductFromIdFeature($id_feature);
        if ($moduleInstance->_isFilledArray($ids_product))
        {
            pm_cachemanager::deleteCacheFromIdProduct($ids_product);
        }
    }
    //Clear product cache on feature value update
    elseif (($id_feature_value = Tools::getValue('gr_id')) && Tools::getValue('act') == 'cat_featurevalue_update')
    {
        $ids_product = $moduleInstance->getIdsProductFromIdFeatureValue($id_feature_value);
        if ($moduleInstance->_isFilledArray($ids_product))
        {
            pm_cachemanager::deleteCacheFromIdProduct($ids_product);
        }
    }
}
