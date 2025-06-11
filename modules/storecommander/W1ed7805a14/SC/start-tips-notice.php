<?php

    if (defined('_PS_CACHE_ENABLED_') && _PS_CACHE_ENABLED_ && _s('APP_DISABLE_CACHE_NOTICE'))
    {
        $message = _l('Note: Using the Prestashop cache system may interfere with the proper use of Store Commander as well as modules. To prevent this, go to the Preferences > Performance tab to disable the option at the end of the page.', 1);
        returnItemOfTheDay('info', $message, null, 5000);
    }

    ## read all tips_notice
    $now = new DateTime('now');
    $today = $now->format('Y-m-d');
    $last_reader = SCI::getConfigurationValue('SC_ADVICE_LAST_READER');
    if (empty($last_reader))
    {
        if (version_compare(_PS_VERSION_, '1.5.0.1', '>='))
        {
            Configuration::updateGlobalValue('SC_ADVICE_LAST_READER', $today);
        }
        else
        {
            Configuration::updateValue('SC_ADVICE_LAST_READER', $today);
        }
        $last_reader = $today;
    }

    $already_shown = false;
    $advice_to_show = false;
    $advice_notice = array(
        'amazon',
        'cdiscount',
        'feedbiz',
        '100customers',
        '250categories',
        'multilang',
    );

    if ($last_reader == $today)
    {
        $already_shown = true;
    }

    $tip_to_show = null;
    $tips_notice = array();
    $tips_path = SC_DIR.'tips/content/astuce.xml';
    $show_tips = true;
    if (!$already_shown && file_exists($tips_path))
    {
        $tip_last_reader = (array) json_decode(SCI::getConfigurationValue('SC_TIP_LAST_READED'), true);
        if (array_key_exists($sc_agent->id_employee, $tip_last_reader))
        {
            if (array_key_exists('disable', $tip_last_reader[$sc_agent->id_employee]))
            {
                $show_tips = false;
            }
        }
        if ($show_tips)
        {
            $id_tip = (int) $tip_last_reader[$sc_agent->id_employee]['id_tip'];
            $tips_xml = simplexml_load_file($tips_path);
            foreach ($tips_xml->astuces->astuce as $tip)
            {
                ## tant que current id est supérieur au dernier lu
                if ($tip->id > $id_tip)
                {
                    $tips_notice[(int) $tip->id] = array(
                        'id' => (int) $tip->id,
                        'name' => (string) $tip->{'name_'.SC_ISO_LANG_FOR_EXTERNAL},
                        'intro' => (string) $tip->{'intro_'.SC_ISO_LANG_FOR_EXTERNAL},
                        'link' => (string) $tip->{'link_'.SC_ISO_LANG_FOR_EXTERNAL},
                        'video' => (string) $tip->{'video_'.SC_ISO_LANG_FOR_EXTERNAL},
                        'category' => (string) $tip->{'category_'.SC_ISO_LANG_FOR_EXTERNAL},
                    );
                }
            }
        }
    }

    foreach ($advice_notice as $key => $extension)
    {
        if (!_s('APP_DISABLE_'.strtoupper($extension).'_ADVICE'))
        {
            unset($advice_notice[$key]);
        }
    }
    if (!empty($advice_notice))
    {
        $arr_value = array_values($advice_notice);
        $advice_to_show = $arr_value[0];
    }
    if (!empty($tips_notice))
    {
        $arr_value = array_values($tips_notice);
        $tip_to_show = $arr_value[0];
        $tips_readed_by_employee = (array) json_decode(SCI::getConfigurationValue('SC_TIP_LAST_READED'), true);
        $tips_readed_by_employee[(int) $sc_agent->id_employee]['id_tip'] = (int) $tip_to_show['id'];
        if (version_compare(_PS_VERSION_, '1.5.0.1', '>='))
        {
            Configuration::updateGlobalValue('SC_TIP_LAST_READED', json_encode($tips_readed_by_employee));
        }
        else
        {
            Configuration::updateValue('SC_TIP_LAST_READED', json_encode($tips_readed_by_employee));
        }
    }

    ## Tips
    if (empty($already_shown)
        && !empty($tip_to_show))
    {
        $link = array();
        if (!empty($tip_to_show['video']))
        {
            $link = array(
                'lk' => $tip_to_show['video'],
                'ttl' => _l('Watch video', 1),
            );
        }
        elseif (!empty($tip_to_show['link']))
        {
            $link = array(
                'lk' => $tip_to_show['link'],
                'ttl' => _l('More informations', 1),
            );
        }
        $message = $tip_to_show['intro'].(!empty($link) ? '<a href="'.$link['lk'].'" target="_blank">'.$link['ttl'].'</a>' : '');
        returnItemOfTheDay('tip', $message, 'APP_DISABLE_TIPS');
    }

    ## Amazon
    if (!defined('SC_Amazon_ACTIVE')
        && SCI::moduleIsInstalled('amazon')
        && SCI::moduleIsEnabled('amazon')
        && empty($already_shown)
        && $advice_to_show == 'amazon')
    {
        $message = _l('<p>You\'re using Amazon module. Save time publishing your products with SC Amazon</p><a href="https://www.storecommander.com/en/addon/1466-sc-amazon.html" target="_blank">More information</a>', 1);
        returnItemOfTheDay('advice', $message, 'APP_DISABLE_AMAZON_ADVICE');
    }

    ## Cdiscount
    if (!defined('SC_Cdiscount_ACTIVE')
        && SCI::moduleIsInstalled('cdiscount')
        && SCI::moduleIsEnabled('cdiscount')
        && empty($already_shown)
        && $advice_to_show == 'cdiscount')
    {
        $message = _l('<p>You\'re using Cdiscount module. Save time publishing your products with SC Cdiscount</p><a href="https://www.storecommander.com/en/addon/1468-sc-cdiscount.html" target="_blank">SC Cdiscount</a>', 1);
        returnItemOfTheDay('advice', $message, 'APP_DISABLE_CDISCOUNT_ADVICE');
    }

    ## Feed.biz
    if (!defined('SC_FeedBiz_ACTIVE')
        && SCI::moduleIsInstalled('feedbiz')
        && SCI::moduleIsEnabled('feedbiz')
        && empty($already_shown)
        && $advice_to_show == 'feedbiz')
    {
        $message = _l('<p>You\'re using Feed.biz module. Save time publishing your products with SC Feed.biz</p><a href="https://www.storecommander.com/en/addon/1467-sc-feedbizz.html" target="_blank">SC Feed.biz</a>', 1);
        returnItemOfTheDay('advice', $message, 'APP_DISABLE_FEEDBIZ_ADVICE');
    }

    ## > 100 customers
    if (empty($already_shown)
        && $advice_to_show == '100customers')
    {
        $nb_customer = Db::getInstance()->getValue('SELECT COUNT(id_customer) FROM '._DB_PREFIX_.'customer');
        if ($nb_customer > 100)
        {
            $message = _l('<p>100 customers are displayed in the interface by default. You can change this in the Settings.</p><a href="#" onclick="openSettingsWindow(\'Customers\',\'Interface\');return false;">Setting</a>', 1);
            returnItemOfTheDay('advice', $message, 'APP_DISABLE_100CUSTOMER_ADVICE');
        }
    }

    ## >= 250 categories
    if (empty($already_shown)
        && $advice_to_show == '250categories')
    {
        $nb_categories = Db::getInstance()->getValue('SELECT COUNT(id_category) FROM '._DB_PREFIX_.'category');
        if ($nb_categories >= 250)
        {
            $message = _l('<p>An option exists to load the category tree dynamically (optimize display performances)</p><a href="#" onclick="openSettingsWindow(\'Catalog\',\'Category\');return false;">Setting</a>', 1);
            returnItemOfTheDay('advice', $message, 'APP_DISABLE_250CATEGORIES_ADVICE');
        }
    }
    ## > 1 lang installed
    if (empty($already_shown)
        && $advice_to_show == 'multilang')
    {
        $nb_categories = Db::getInstance()->getValue('SELECT COUNT(id_lang) FROM '._DB_PREFIX_.'lang');
        if ($nb_categories > 1)
        {
            $message = _l('<p>You have multiple languages installed on your store, you can get your product pages translated by qualified translators directly from the interface</p><a href="https://www.storecommander.com/support/en/traduction/1580-product-pages-translation-how-does-it-work.html" target="_blank">More informations</a>', 1);
            returnItemOfTheDay('advice', $message, 'APP_DISABLE_MULTILANG');
        }
    }

    ## Update last message readed date
    if (version_compare(_PS_VERSION_, '1.5.0.1', '>='))
    {
        Configuration::updateGlobalValue('SC_ADVICE_LAST_READER', $today);
    }
    else
    {
        Configuration::updateValue('SC_ADVICE_LAST_READER', $today);
    }

function returnItemOfTheDay($type, $message, $setting_key = null, $expire = '-1')
{
    global $sc_agent;
    if (!empty($type))
    {
        $message = trim(str_replace("\n", '', $message));
        switch ($type) {
            case 'info':
                $html_content = $message;
                break;
            case 'advice':
                $html_content = '<span class="title">'._l('Advice of the day').'</span>';
                $html_content .= $message;
                if (!empty($setting_key))
                {
                    $html_content .= "<a href=\"javascript:disableThisNotice(\'".$setting_key."\',\'".$type."\');\">"._l('Disable this advice', 1).'</a>';
                }
                break;
            case 'tip':
                $html_content = '<span class="title">'._l('Tip of the day').'</span>';
                $html_content .= $message;
                if (!empty($setting_key))
                {
                    $html_content .= '<a href="javascript:disableTips('.$sc_agent->id_employee.');">'._l('Disable tips', 1).'</a>';
                }
                break;
        }
        if (!empty($html_content))
        {
            returnDhtmlxMessage($html_content, $type, $expire);
        }
    }
}

function returnDhtmlxMessage($text, $type, $expire)
{
    echo "dhtmlx.message({
        text: '".$text."',
        type: '".$type."',
        expire: ".$expire.'
    });';
}
