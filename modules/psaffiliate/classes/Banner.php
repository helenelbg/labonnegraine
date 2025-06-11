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

class Banner extends ObjectModel
{
    public $id;
    public $id_banner;
    public $title;
    public $image;
    public $active;

    public static $definition = array(
        'table' => 'aff_banners',
        'primary' => 'id_banner',
        'fields' => array(
            'id_banner' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt', 'copy_post' => false),
            'title' => array('type' => self::TYPE_STRING, 'validate' => 'isString', 'copy_post' => false),
            'image' => array('type' => self::TYPE_NOTHING, 'validate' => 'isCleanHtml', 'copy_post' => false),
            'active' => array('type' => self::TYPE_BOOL, 'validate' => 'isBool', 'copy_post' => false),
        ),
    );

    public function __construct($id = null)
    {
        parent::__construct($id);
    }

    public function toggleActive()
    {
        $id_banner = (int)Tools::getValue('id_banner');
        if ($id_banner) {
            $sql = Db::getInstance()->execute(
                "UPDATE `"._DB_PREFIX_."aff_banners` SET `active` = (CASE WHEN `active`='1' THEN '0' WHEN `active`='0' THEN '1' WHEN `active` IS NULL THEN '1' END) WHERE `id_banner`='".(int)$id_banner."' LIMIT 1;"
            );

            return $sql;
        }

        return false;
    }

    public static function getBanners($active = false, $with_image = false)
    {
        $sql = 'SELECT *, IF(`image` != "", CONCAT("'._PS_BASE_URL_SSL_._MODULE_DIR_."psaffiliate/views/img/banners/".'", `image`), NULL) as `image_link` FROM `'._DB_PREFIX_.'aff_banners`';
        if ($active) {
            $sql .= ' WHERE `active`="1"';
            if ($with_image) {
                $sql .= ' AND `image` != ""';
            }
        } elseif ($with_image) {
            $sql .= ' WHERE `image` != ""';
        }
        $banners = Db::getInstance()->executeS($sql);

        return $banners;
    }

    public static function hasBanners($active = false)
    {
        $banners = self::getBanners($active);

        return (bool)sizeof($banners);
    }

    public function update($null_values = false)
    {
        if (isset($_FILES['image'])
            && isset($_FILES['image']['tmp_name'])
            && !empty($_FILES['image']['tmp_name'])
        ) {
            if ($error = ImageManager::validateUpload($_FILES['image'], 4000000)) {
                return $error;
            } else {
                $ext = Tools::substr($_FILES['image']['name'], strrpos($_FILES['image']['name'], '.') + 1);
                $file_name = "banner-".$this->id.'.'.$ext;
                if (!move_uploaded_file(
                    $_FILES['image']['tmp_name'],
                    _PS_MODULE_DIR_.'/psaffiliate/views/img/banners/'.$file_name
                )
                ) {
                    return $this->displayError($this->l('An error occurred while attempting to upload the file.'));
                } else {
                    $this->image = $file_name;
                }
            }
        } else {
            $this->image = self::getImage($this->id);
        }

        return parent::update($null_values);
    }

    public function add($auto_date = true, $null_values = false)
    {
        $add = parent::add($auto_date, $null_values);
        if ($add && isset($this->id) && $this->id) {
            $this->id_banner = $this->id;
            $this->update($null_values);
        }

        return $add;
    }

    public function deleteImage($force_delete = false)
    {
        $file_name = $this->image;
        $this->image = null;
        $this->update();
        if ($file_name) {
            $file_path = _PS_MODULE_DIR_.'psaffiliate/views/img/banners/'.$file_name;
            if (file_exists($file_path)) {
                return unlink($file_path);
            }

            return true;
        }

        return false;
    }

    public function delete()
    {
        $this->deleteImage();

        return parent::delete();
    }

    public static function getImage($id_banner)
    {
        return Db::getInstance()->getValue(
            'SELECT `image` FROM `'._DB_PREFIX_.'aff_banners` WHERE `id_banner` = "'.(int)$id_banner.'"'
        );
    }

    public function l($string)
    {
        return Translate::getModuleTranslation('psaffiliate', $string, get_class($this));
    }
}
