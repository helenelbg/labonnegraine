<?php
/**
 * This file is part of module : DMU Liste des commandes améliorée.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this Module to
 * newer versions in the future. If you wish to customize the Module for
 * your needs please use "themes/" or/and "override/modules" directories
 * or refer to http://www.prestashop.com for more information.
 *
 *   .--.
 *   |   |.--..-. .--, .--.--.   .--.--. .-.   .  . .,-.
 *   |   ;|  (.-'(   | |  |  |   |  |  |(.-'   |  | |   )
 *   '--' '   `--'`-'`-'  '  `-  '  '  `-`--'  `--`-|`-'
 *        w w w . d r e a m - m e - u p . f r       '
 *
 *  @author    Dream me up <prestashop@dream-me-up.fr>
 *  @copyright 2007 - 2023 Dream me up
 *  @license   All Rights Reserved
 */
if (!defined('_PS_VERSION_')) {
    exit;
}

class VuesCommandes extends ObjectModel
{
    public $id;
    public $name;
    public $statuts;
    public $default;
    public $position;

    protected $fieldsValidate = ['name' => 'isString'];
    protected $fieldsRequired = ['name'];

    protected $tables = ['dmu_vues_commandes'];
    protected $table = 'dmu_vues_commandes';
    protected $identifier = 'id_vue';

    public function getFields()
    {
        $fields = [];
        if (isset($this->id)) {
            $fields['id_vue'] = (int) $this->id;
        }
        $fields['name'] = pSQL($this->name);
        $fields['statuts'] = pSQL(implode(',', $this->statuts));
        $fields['default'] = (int) $this->default;
        $fields['position'] = (int) $this->position;

        // Mise à zéro des autres vues
        // $this->setDefault();
        return $fields;
    }

    public static function getViews($id = false)
    {
        $where_order = ((int) $id) ? ' WHERE id_vue = ' . (int) $id : '';
        $sql = 'SELECT *
                FROM ' . _DB_PREFIX_ . 'dmu_vues_commandes' .
                $where_order . '
                ORDER BY position';

        return ((int) $id) ? Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($sql)
            : Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS($sql);
    }

    public static function getDefaultId()
    {
        $sql = 'SELECT id_vue FROM `' . _DB_PREFIX_ . 'dmu_vues_commandes`
                ORDER BY `default` DESC, `id_vue` ASC';

        return Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($sql);
    }

    public static function setDefaultView($id_vue = null)
    {
        if ($id_vue) {
            $sql = 'UPDATE `' . _DB_PREFIX_ . 'dmu_vues_commandes`
                    SET `default` = 0';
            if (Db::getInstance()->execute($sql)) {
                $sql = 'UPDATE `' . _DB_PREFIX_ . 'dmu_vues_commandes`
                        SET `default` = 1
                        WHERE id_vue = ' . $id_vue;
                if (Db::getInstance()->execute($sql)) {
                    return true;
                }
            }
        }

        return false;
    }

    public function getNextPosition()
    {
        $sql = 'SELECT * FROM `' . _DB_PREFIX_ . 'dmu_vues_commandes`
                ORDER BY position, name, id_vue';
        if ($positions = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql)) {
            $pos = 0; // on en profite pour faire un peu de rangement au cas où !
            foreach ($positions as $position) {
                $sql = 'UPDATE `' . _DB_PREFIX_ . 'dmu_vues_commandes`
                        SET position = ' . ($pos++) . '
                        WHERE id_vue = ' . $position['id_vue'];
                Db::getInstance()->execute($sql);
            }

            return count($positions);
        }

        return 0;
    }

    public static function str2hexColor($string)
    {
        $colors = '{"aliceblue":"#f0f8ff","antiquewhite":"#faebd7","aqua":"#00ffff","aquamarine":"#7fffd4",'
            . '"azure":"#f0ffff","beige":"#f5f5dc","bisque":"#ffe4c4","black":"#000000","blanchedalmond":"#ffebcd",'
            . '"blue":"#0000ff","blueviolet":"#8a2be2","brown":"#a52a2a","burlywood":"#deb887","cadetblue":"#5f9ea0",'
            . '"chartreuse":"#7fff00","chocolate":"#d2691e","coral":"#ff7f50","cornflowerblue":"#6495ed",'
            . '"cornsilk":"#fff8dc","crimson":"#dc143c","cyan":"#00ffff","darkblue":"#00008b","darkcyan":"#008b8b",'
            . '"darkgoldenrod":"#b8860b","darkgray":"#a9a9a9","darkgreen":"#006400","darkkhaki":"#bdb76b",'
            . '"darkmagenta":"#8b008b","darkolivegreen":"#556b2f","darkorange":"#ff8c00","darkorchid":"#9932cc",'
            . '"darkred":"#8b0000","darksalmon":"#e9967a","darkseagreen":"#8fbc8f","darkslateblue":"#483d8b",'
            . '"darkslategray":"#2f4f4f","darkturquoise":"#00ced1","darkviolet":"#9400d3","deeppink":"#ff1493",'
            . '"deepskyblue":"#00bfff","dimgray":"#696969","dodgerblue":"#1e90ff","firebrick":"#b22222",'
            . '"floralwhite":"#fffaf0","forestgreen":"#228b22","fuchsia":"#ff00ff","gainsboro":"#dcdcdc",'
            . '"ghostwhite":"#f8f8ff","gold":"#ffd700","goldenrod":"#daa520","gray":"#808080","green":"#008000",'
            . '"greenyellow":"#adff2f","honeydew":"#f0fff0","hotpink":"#ff69b4","indianred ":"#cd5c5c",'
            . '"indigo":"#4b0082","ivory":"#fffff0","khaki":"#f0e68c","lavender":"#e6e6fa","lavenderblush":"#fff0f5",'
            . '"lawngreen":"#7cfc00","lemonchiffon":"#fffacd","lightblue":"#add8e6","lightcoral":"#f08080",'
            . '"lightcyan":"#e0ffff","lightgoldenrodyellow":"#fafad2","lightgrey":"#d3d3d3","lightgreen":"#90ee90",'
            . '"lightpink":"#ffb6c1","lightsalmon":"#ffa07a","lightseagreen":"#20b2aa","lightskyblue":"#87cefa",'
            . '"lightslategray":"#778899","lightsteelblue":"#b0c4de","lightyellow":"#ffffe0","lime":"#00ff00",'
            . '"limegreen":"#32cd32","linen":"#faf0e6","magenta":"#ff00ff","maroon":"#800000",'
            . '"mediumaquamarine":"#66cdaa","mediumblue":"#0000cd","mediumorchid":"#ba55d3","mediumpurple":"#9370d8",'
            . '"mediumseagreen":"#3cb371","mediumslateblue":"#7b68ee","mediumspringgreen":"#00fa9a",'
            . '"mediumturquoise":"#48d1cc","mediumvioletred":"#c71585","midnightblue":"#191970","mintcream":"#f5fffa",'
            . '"mistyrose":"#ffe4e1","moccasin":"#ffe4b5","navajowhite":"#ffdead","navy":"#000080",'
            . '"oldlace":"#fdf5e6","olive":"#808000","olivedrab":"#6b8e23","orange":"#ffa500","orangered":"#ff4500",'
            . '"orchid":"#da70d6","palegoldenrod":"#eee8aa","palegreen":"#98fb98","paleturquoise":"#afeeee",'
            . '"palevioletred":"#d87093","papayawhip":"#ffefd5","peachpuff":"#ffdab9","peru":"#cd853f",'
            . '"pink":"#ffc0cb","plum":"#dda0dd","powderblue":"#b0e0e6","purple":"#800080","red":"#ff0000",'
            . '"rosybrown":"#bc8f8f","royalblue":"#4169e1","saddlebrown":"#8b4513","salmon":"#fa8072",'
            . '"sandybrown":"#f4a460","seagreen":"#2e8b57","seashell":"#fff5ee","sienna":"#a0522d","silver":"#c0c0c0",'
            . '"skyblue":"#87ceeb","slateblue":"#6a5acd","slategray":"#708090","snow":"#fffafa",'
            . '"springgreen":"#00ff7f","steelblue":"#4682b4","tan":"#d2b48c","teal":"#008080","thistle":"#d8bfd8",'
            . '"tomato":"#ff6347","turquoise":"#40e0d0","violet":"#ee82ee","wheat":"#f5deb3","white":"#ffffff",'
            . '"whitesmoke":"#f5f5f5","yellow":"#ffff00","yellowgreen":"#9acd32"}';
        $color = json_decode($colors);
        if (isset($color->{Tools::strtolower($string)})) {
            return $color->{Tools::strtolower($string)};
        }

        return $string;
    }

    public static function textColor($hexaColor)
    {
        if (preg_match('`^#[0-9]{6}$`iUs', $hexaColor)) {
            $r = hexdec(Tools::substr($hexaColor, 1, 2));
            $g = hexdec(Tools::substr($hexaColor, 3, 2));
            $b = hexdec(Tools::substr($hexaColor, 5, 2));
            $weight = (.3 * $r) + (.59 * $g) + (.11 * $b);
            if ($weight <= 128) {
                return '#fff';
            }
        }

        return '#000';
    }
}
