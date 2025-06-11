<?php

    function readCusImportConfigXML($files)
    {
        global $importConfig;
        $importConfig = array();
        // read config
        if ($feed = @simplexml_load_file(SC_CSV_IMPORT_DIR.'customers/'.SC_CSV_IMPORT_CONF))
        {
            foreach ($feed->csvfile as $file)
            {
                if (strpos((string) $file->name, '&') === false)
                {
                    $importConfig[(string) $file->name] = array(
                                                        'name' => (string) $file->name,
                                                        'supplier' => (string) $file->supplier,
                                                        'mapping' => (string) $file->mapping,
                                                        'fieldsep' => (string) $file->fieldsep,
                                                        'valuesep' => (string) $file->valuesep,
                                                        'utf8' => (string) $file->utf8,
                                                        'idby' => (string) $file->idby,
                                                        'iffoundindb' => (string) $file->iffoundindb,
                                                        'firstlinecontent' => (string) $file->firstlinecontent,
                                                        'importlimit' => (string) $file->importlimit,
                                                        'id_shop' => (int) $file->id_shop,
                                                    );
                }
            }
        }
        // config by default
        foreach ($files as $file)
        {
            if ($file != '' && !sc_in_array($file, array_keys($importConfig), 'cusWinImportProcess_importConfig') && strpos($file, '&') === false)
            {
                $importConfig[$file] = array(
                                                    'name' => $file,
                                                    'supplier' => '',
                                                    'mapping' => '',
                                                    'fieldsep' => 'dcomma',
                                                    'valuesep' => ',',
                                                    'utf8' => '1',
                                                    'idby' => 'email',
                                                    'iffoundindb' => 'skip',
                                                    'firstlinecontent' => '',
                                                    'importlimit' => '500',
                                                    'id_shop' => '0',
                                                    );
                if (SCMS)
                {
                    $importConfig[$file]['id_shop'] = (int) Configuration::get('PS_SHOP_DEFAULT');
                }
            }
        }
    }

    function writeCusImportConfigXML()
    {
        global $importConfig;
        $content = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
        $content .= '<csvfiles>'."\n";
        foreach ($importConfig as $conf)
        {
            if (file_exists(SC_CSV_IMPORT_DIR.'customers/'.$conf['name']))
            {
                $content .= '<csvfile>'."\n";
                $content .= '<name><![CDATA['.$conf['name'].']]></name>';
                $content .= '<supplier><![CDATA['.$conf['supplier'].']]></supplier>';
                $content .= '<mapping><![CDATA['.$conf['mapping'].']]></mapping>';
                $content .= '<id_shop><![CDATA['.$conf['id_shop'].']]></id_shop>';
                $content .= '<fieldsep><![CDATA['.$conf['fieldsep'].']]></fieldsep>';
                $content .= '<valuesep><![CDATA['.$conf['valuesep'].']]></valuesep>';
                $content .= '<utf8><![CDATA['.$conf['utf8'].']]></utf8>';
                $content .= '<idby><![CDATA['.$conf['idby'].']]></idby>';
                $content .= '<iffoundindb><![CDATA['.$conf['iffoundindb'].']]></iffoundindb>';
                $content .= '<firstlinecontent><![CDATA['.$conf['firstlinecontent'].']]></firstlinecontent>';
                $content .= '<importlimit><![CDATA['.$conf['importlimit'].']]></importlimit>';
                $content .= '</csvfile>'."\n";
            }
        }
        $content .= '</csvfiles>';

        return file_put_contents(SC_CSV_IMPORT_DIR.'customers/'.SC_CSV_IMPORT_CONF, $content);
    }

    function loadMappingCus($filename)
    {
        global $sc_agent;
        if ($filename == '')
        {
            return '';
        }
        if (strpos($filename, '.map.xml') === false)
        {
            $filename = $filename.'.map.xml';
        }
        $content = '';
        if (file_exists(SC_CSV_IMPORT_DIR.'customers/'.$filename) && $feed = simplexml_load_file(SC_CSV_IMPORT_DIR.'customers/'.$filename))
        {
            $id_lang = (int) $feed->id_lang;
            if (!$id_lang)
            {
                $id_lang = (int) $sc_agent->id_lang;
            }

            foreach ($feed->map as $map)
            {
                $content .= trim((string) $map->csvname).','.trim((string) $map->dbname).','.trim((string) $map->options).';';
            }
        }

        return $content;
    }

    function hasAddress()
    {
        global $mappingData,$addressFields;
        foreach ($addressFields as $addressField)
        {
            if (sc_in_array($addressField, $mappingData['DBArray'], 'cusWinImportProcess_DBArray'))
            {
                return true;
            }
        }

        return false;
    }
