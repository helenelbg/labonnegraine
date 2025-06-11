<?php

// ----------------------------------------------------------------------------
//
//  Function:   runMaintenance
//  Purpose:        Do maintenance of tables and files
//  Arguments:    none
//
// ----------------------------------------------------------------------------
    function runMaintenance()
    {
        if (!file_exists(SC_CSV_IMPORT_DIR.'category/'))
        {
            mkdir(SC_CSV_IMPORT_DIR.'category/', 0775);
        }
        if (!file_exists(SC_CSV_IMPORT_DIR.'category/'.'images/'))
        {
            mkdir(SC_CSV_IMPORT_DIR.'category/'.'images/', 0775);
        }

        // purge history if more than APP_CHANGE_HISTORY_MAX items
        $sql = 'SELECT id_history FROM '._DB_PREFIX_.'storecom_history ORDER BY id_history DESC LIMIT '.(int) _s('APP_CHANGE_HISTORY_MAX').',1';
        $res = Db::getInstance()->ExecuteS($sql);
        if (count($res) != 0)
        {
            $sql = 'DELETE FROM '._DB_PREFIX_.'storecom_history WHERE id_history <= '.(int) $res[0]['id_history'];
            Db::getInstance()->Execute($sql);
        }

        // créer le champs dans declinaison : id_sc_available_later
        // créer table sc_available_later (id, id_lang, available_later)
        // maintenance pour supprimer message dans sc_available_later non utilisé
        if (SCI::getConfigurationValue('SC_DELIVERYDATE_INSTALLED') == '1')
        {
            if (SCI::getConfigurationValue('SC_DELIVERYDATE_SC_INSTALLED') == '0')
            {
                if (!isField('id_sc_available_later', 'product_attribute'))
                {
                    $sql = 'ALTER TABLE `'._DB_PREFIX_."product_attribute` ADD `id_sc_available_later` INT NOT NULL DEFAULT '0' AFTER `available_date` ";
                    Db::getInstance()->Execute($sql);
                }

                $sql = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'sc_available_later` (
                  `id_sc_available_later` int(10) unsigned NOT NULL auto_increment,
                    `id_lang` int(10) unsigned NOT NULL,
                  `available_later` char(255) default NULL,
                  PRIMARY KEY (`id_sc_available_later`)
                ) ENGINE=MyISAM DEFAULT CHARSET=utf8;';
                Db::getInstance()->Execute($sql);

                if (SC_TOOLS && file_exists(SC_TOOLS_DIR.'grids_combinations_conf.xml'))
                {
                    $grids_combinations_conf = simplexml_load_file(SC_TOOLS_DIR.'grids_combinations_conf.xml');
                    if (!empty($grids_combinations_conf->grids->grid->value))
                    {
                        $sourceGridFormat = (string) $grids_combinations_conf->grids->grid->value;

                        if (!empty($sourceGridFormat))
                        {
                            SC_Ext::addNewField('combinations', 'available_later');
                        }
                    }
                }
                if (SC_TOOLS && file_exists(SC_TOOLS_DIR.'grids_combinationmultiproduct_conf.xml'))
                {
                    $grids_combinationmultiproduct_conf = simplexml_load_file(SC_TOOLS_DIR.'grids_combinationmultiproduct_conf.xml');
                }

                SCI::updateConfigurationValue('SC_DELIVERYDATE_SC_INSTALLED', 1);
            }

            $sql = 'DELETE FROM `'._DB_PREFIX_.'sc_available_later` WHERE id_sc_available_later NOT IN (SELECT DISTINCT(id_sc_available_later) FROM `'._DB_PREFIX_.'product_attribute` WHERE id_sc_available_later!=0) ';
            Db::getInstance()->Execute($sql);
        }

        if (defined('SC_Segmentation_ACTIVE') && SC_Segmentation_ACTIVE == '1')
        {
            if (!isTable('sc_segment'))
            {
                $query = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'sc_segment` (
                    `id_segment` int(11) NOT NULL AUTO_INCREMENT,
                    `name` varchar(255) NOT NULL,
                    `type` enum("manual","auto") NOT NULL DEFAULT "manual",
                    `auto_file` varchar(255) DEFAULT NULL,
                    `auto_params` text,
                    `access` varchar(255) DEFAULT NULL,
                    `description` text,
                    `id_parent` int(11) NOT NULL DEFAULT "0",
                    `position` int(11) NOT NULL DEFAULT "0",
                    PRIMARY KEY (`id_segment`)
                    ) ENGINE=InnoDB  DEFAULT CHARSET=utf8;';
                Db::getInstance()->Execute($query);
            }
            else
            {
                if (!isField('position', 'sc_segment'))
                {
                    $sql = 'ALTER TABLE `'._DB_PREFIX_."sc_segment` ADD `position` INT NOT NULL DEFAULT '0' AFTER `id_parent` ";
                    Db::getInstance()->Execute($sql);
                }
            }
            if (!isTable('sc_segment_element'))
            {
                $query = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'sc_segment_element` (
                      `id_segment_element` int(11) NOT NULL AUTO_INCREMENT,
                      `id_segment` int(11) NOT NULL,
                      `id_element` int(11) NOT NULL,
                      `type_element` varchar(255) NOT NULL,
                      PRIMARY KEY (`id_segment_element`)
                    ) ENGINE=InnoDB  DEFAULT CHARSET=utf8;';
                Db::getInstance()->Execute($query);
            }
        }

        if (file_exists(SC_DIR.'lib/js/ajaxfilemanager/'))
        {
            dirRemove(SC_DIR.'lib/js/ajaxfilemanager');
        }

        if(Configuration::get('PS_IMAGE_QUALITY') === 'webp_all'){
            $sql = sprintf("SELECT id_configuration, value FROM `%sconfiguration` WHERE name='SC_IMAGECOMPRESSION_ACTIVE'", _DB_PREFIX_);
            $compressionConfig = Db::getInstance()->getRow($sql);
            if(!empty($compressionConfig) && $compressionConfig['value'] !== '0') {
                $sql = sprintf("UPDATE `%sconfiguration` SET value='0' WHERE id_configuration=%s", _DB_PREFIX_,$compressionConfig['id_configuration']);
                Db::getInstance()->Execute($sql);
                $access_details = access_details();
                $data = array(
                    'LICENSE' => '#',
                    'DOMAIN' => getShopProtocol().$access_details['domain'].__PS_BASE_URI__,
                    'SC_UNIQUE_ID' => SCI::getConfigurationValue('SC_UNIQUE_ID'),
                    'compression_active' => 0, ## si preprod(2) on envoi bien 2 au serveur pour qu'on puisse intervenir
                );
                makeCallToOurApi('Compression/Active', array(), $data);
            }


        }

        removeOldScItems();
    }
