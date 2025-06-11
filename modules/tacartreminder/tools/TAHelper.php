<?php
/**
 * Cart Reminder
 *
 *    @author    EIRL Timactive De Véra
 *    @copyright Copyright (c) TIMACTIVE 2015 -EIRL Timactive De Véra
 *    @license   Commercial license
 *
 *    @category pricing_promotion
 *
 *    @version 1.1.0
 *************************************
 **         CART REMINDER            *
 **          V 1.0.0                 *
 *************************************
 *  _____ _            ___       _   _
 * |_   _(_)          / _ \     | | (_)
 *   | |  _ _ __ ___ / /_\ \ ___| |_ ___   _____
 *   | | | | '_ ` _ \|  _  |/ __| __| \ \ / / _ \
 *   | | | | | | | | | | | | (__| |_| |\ V /  __/
 *   \_/ |_|_| |_| |_\_| |_/\___|\__|_| \_/ \___|
 *
 * +
 * + Languages: EN, FR
 * + PS version: 1.5,1.6
 * TAHelper class use to generate html code
 * Only use for view
 */
if (!defined('_PS_VERSION_')) {
    exit;
}

class TAHelper
{
    /**
     * Generate Category tree for one element in list
     *
     * @param $treeview_id
     * @param $categories
     * @param $selected_cat
     * @param $current
     * @param int $id_category
     *
     * @return string
     */
    public function treeCategory($treeview_id, $categories, $selected_cat, $current, $id_category = 1)
    {
        $html = '';
        $html .= '<li id="' . $treeview_id . '_value_' . $id_category . '"';
        $html .= ' data-category="' . $id_category . '"';
        $html .= ' title="' . Tools::stripslashes($current['infos']['name']) . '"';
        $html .= (in_array($id_category, $selected_cat) ? ' data-selected="true" ' : '');
        $html .= (isset($categories[$id_category]) ? ' class="folder" ' : '') . '>';
        $html .= Tools::stripslashes($current['infos']['name']);
        if (isset($categories[$id_category])) {
            $html .= '<ul>';
            foreach ($categories[$id_category] as $key => $row) {
                $html .= $this->treeCategory(
                    $treeview_id,
                    $categories,
                    $selected_cat,
                    $categories[$id_category][$key],
                    $key
                );
            }
            $html .= '</ul>';
        }

        return $html;
    }

    /**
     * Generate Categories tree for Rule condition
     *
     * @param $lang_id
     * @param array $selected_cat
     * @param string $treeview_id
     * @param string $input_name
     * @param null $root
     *
     * @return string
     */
    public function renderCatTree(
        $lang_id,
        $selected_cat = [],
        $treeview_id = 'ta-tree',
        $input_name = 'ta-tree-input',
        $root = null
    ) {
        $id_shop = 0;
        if (Context::getContext()->shop->id) {
            $id_shop = Context::getContext()->shop->id;
        } else {
            if (!Shop::isFeatureActive()) {
                $id_shop = Configuration::get('PS_SHOP_DEFAULT');
            }
        }
        $shop = new Shop($id_shop);
        $root_category = Category::getRootCategory(null, $shop);
        if (!$root) {
            $root = [
                'infos' => ['name' => $root_category->name, 'id_category' => $root_category->id],
            ];
        }
        $categories = Category::getCategories((int) $lang_id, false);
        $html = '<div id="' . $treeview_id . '" class="ta-tree">';
        $html .= '<ul>';
        $html .= $this->treeCategory($treeview_id, $categories, $selected_cat, $root, $root['infos']['id_category']);
        $html .= '</ul>';
        $html .= '</div>';
        $html .= '<div id="' . $treeview_id . '-checkbox-list" style="display:none" >';
        foreach ($selected_cat as $id_category) {
            $html .= '<input type="checkbox" name="' . $input_name . '[]" value="' . $id_category . '" checked="checked"/>';
        }
        $html .= '</div>';

        return $html;
    }
}
