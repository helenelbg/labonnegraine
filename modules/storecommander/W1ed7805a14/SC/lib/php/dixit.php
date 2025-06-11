<?php
/*
 * CONFIG
 */

$enabled_sources = array(
    'name' => _l('Name'),
    'shortdesc' => _l('Short description'),
    'desc' => _l('Description'),
    'meta_title' => _l('Meta title'),
    'meta_description' => _l('Meta description'),
    'meta_keywords' => _l('Meta keywords'),
);

/*
 * FUNCTIONS
 */
function dixit_getTotalNbWords($textes)
{
    $nb_words = 0;
    if (!empty($textes))
    {
        if (!is_array($textes))
        {
            $textes = array($textes);
        }

        $headers = array();
        //$posts = array("textes"=>json_encode($textes));
        $posts = array('textes' => str_replace('&', '%26', json_encode($textes)));
        $ret = sc_file_get_contents('http://api.storecommander.com/Dixit/TotalNbWords/', 'POST', $posts, $headers, 500);
        if (!empty($ret))
        {
            $ret = json_decode($ret, true);
            if (!empty($ret['code']) && $ret['code'] == '200' && !empty($ret['nb_words']))
            {
                $nb_words = $ret['nb_words'];
            }
        }
    }

    return $nb_words;
}

function dixit_getAllPdtTextes($pdts, $sources, $id_lang)
{
    if (empty($pdts) || empty($sources) || empty($id_lang))
    {
        return null;
    }
    $return = array();
    foreach ($pdts as $id)
    {
        $textes = array();

        if (SCMS)
        {
            $id_shop_default = Db::getInstance()->getValue('SELECT id_shop_default FROM `'._DB_PREFIX_.'product` WHERE `id_product` = "'.(int) $id.'"');
            $p = new Product($id, false, null, $id_shop_default);
        }
        else
        {
            $p = new Product($id);
        }

        foreach ($sources as $source)
        {
            if ($source == 'name')
            {
                if (!empty($p->name[$id_lang]))
                {
                    $textes[$source] = $p->name[$id_lang];
                }
            }
            elseif ($source == 'shortdesc')
            {
                if (!empty($p->description_short[$id_lang]))
                {
                    $textes[$source] = html_entity_decode($p->description_short[$id_lang]);
                }
            }
            elseif ($source == 'desc')
            {
                if (!empty($p->description[$id_lang]))
                {
                    $textes[$source] = html_entity_decode($p->description[$id_lang]);
                }
            }
            elseif ($source == 'meta_title')
            {
                if (!empty($p->meta_title[$id_lang]))
                {
                    $textes[$source] = $p->meta_title[$id_lang];
                }
            }
            elseif ($source == 'meta_description')
            {
                if (!empty($p->meta_description[$id_lang]))
                {
                    $textes[$source] = $p->meta_description[$id_lang];
                }
            }
            elseif ($source == 'meta_keywords')
            {
                if (!empty($p->meta_keywords[$id_lang]))
                {
                    $textes[$source] = $p->meta_keywords[$id_lang];
                }
            }
        }
        if (!empty($textes))
        {
            $return[$id] = $textes;
        }
    }

    return $return;
}

function dixit_getAllTextes($pdts, $sources, $id_lang)
{
    if (empty($pdts) || empty($sources) || empty($id_lang))
    {
        return null;
    }
    $pdt_textes = dixit_getAllPdtTextes($pdts, $sources, $id_lang);
    $return = array();
    foreach ($pdt_textes as $id => $textes)
    {
        foreach ($textes as $source => $text)
        {
            $return[] = $text;
        }
    }

    return $return;
}

/*
 * ACTIONS
 */

function dixit_action_getQuote($project)
{
    $params = (!empty($project['params']) ? json_decode($project['params'], true) : '');
    $prices = array('amount' => 0, 'cost' => 0);
    if (!empty($project['list_items']) && $project['list_items'] != '-')
    {
        $pdts = explode('-', trim($project['list_items'], '-'));

        $sources = array();
        if (!empty($params['source']))
        {
            $sources = explode(',', $params['source']);
        }

        $iso = '';
        $id_lang = '';
        if (!empty($params['lang_source']))
        {
            list($id_lang_dixit, $iso) = explode('_', $params['lang_source']);
        }
        if (!empty($iso))
        {
            $id_lang = Language::getIdByIso($iso);
        }

        $advanced = false;
        if (!empty($params['level']) && $params['level'] == 'advanced')
        {
            $advanced = true;
        }

        $textes = dixit_getAllTextes($pdts, $sources, $id_lang);

        $prices['amount'] = Dixit::getPrice($textes, $advanced);
        $prices['cost'] = Dixit::getCost($textes, $advanced);
    }

    return $prices;
}

function dixit_action_started($project)
{
    $params = (!empty($project['params']) ? json_decode($project['params'], true) : '');
    $return = array('stop' => '1');
    if (!empty($project['list_items']) && $project['list_items'] != '-')
    {
        if ($project['status'] == '8' || $project['status'] == '113')
        {
            $headers = array();
            $posts = array();
            $posts['KEY'] = 'gt789zef132kiy789u13v498ve15nhry98';
            $posts['LICENSE'] = '#';
            $posts['URLCALLING'] = '#';
            $posts['status'] = '9';
            if (defined('IS_SUBS') && IS_SUBS == '1')
            {
                $posts['SUBSCRIPTION'] = '1';
            }
            $ret = makeCallToOurApi('Fizz/Project/Update/'.$project['id_project'], $headers, $posts);
        }

        // INIT
        $limit = 10;

        $sources = array();
        if (!empty($params['source']))
        {
            $sources = explode(',', $params['source']);
        }

        $iso = '';
        $id_lang = '';
        if (!empty($params['lang_source']))
        {
            list($id_lang_dixit, $iso) = explode('_', $params['lang_source']);
        }
        if (!empty($iso))
        {
            $id_lang = Language::getIdByIso($iso);
        }
        $link = new Link();

        $protocol = 'http://';
        $protocol_ssl = 'https://';
        $protocol_link = (Configuration::get('PS_SSL_ENABLED')) ? $protocol_ssl : $protocol;
        if (SCMS)
        {
            $selected_shops_id = (int) Configuration::get('PS_SHOP_DEFAULT');
            $shop = new Shop((int) $selected_shops_id);
            $_PS_BASE_URL_ = $protocol_link.$shop->domain.$shop->getBaseURI().'img/p/';
        }
        else
        {
            $_PS_BASE_URL_ = $protocol_link.Tools::getShopDomain(false)._THEME_PROD_DIR_;
        }

        $pdts_transactionId = array();
        if (!empty($params['transactionId']))
        {
            $pdts_transactionId = $params['transactionId'];
        }

        $pdts = explode('-', trim($project['list_items'], '-'));

        // CREATE 10 PRODUCTS
        $num = 1;
        foreach ($pdts as $pdt)
        {
            $do = false;

            if (!empty($pdts_transactionId[$pdt]))
            {
                if ($pdts_transactionId[$pdt] == 'error')
                {
                    $do = true;
                }
            }
            else
            {
                $do = true;
            }
            if ($do)
            {
                if (SCMS)
                {
                    $id_shop_default = Db::getInstance()->getValue('SELECT id_shop_default FROM `'._DB_PREFIX_.'product` WHERE `id_product` = "'.(int) $pdt.'"');
                    $p = new Product($pdt, false, null, $id_shop_default);
                }
                else
                {
                    $p = new Product($pdt);
                }

                if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
                {
                    $comment = 'Fiche produit : '.$link->getProductLink($p, null, null, null, $id_lang, $p->id_shop_default);
                }
                else
                {
                    $comment = 'Fiche produit : '.$link->getProductLink($p, null, null, null, $id_lang);
                }
                if (!empty($params['comment']))
                {
                    $comment .= "\n Instructions : ".$params['comment'];
                }

                $image = '';
                $sql = ' SELECT id_image FROM `'._DB_PREFIX_.'image` WHERE `id_product` = "'.(int) $p->id.'" ORDER BY cover DESC';
                $id_img = Db::getInstance()->getValue($sql);
                if (!empty($id_img))
                {
                    $image = $_PS_BASE_URL_.getImgPath((int) $p->id, (int) $id_img, _s('CAT_EXPORT_IMAGE_FORMAT'), 'jpg');
                }

                $textes = array();
                $name = '';
                foreach ($sources as $source)
                {
                    if ($source == 'name')
                    {
                        if (!empty($p->name[$id_lang]))
                        {
                            $textes[$source] = $p->name[$id_lang];
                            $name = $p->name[$id_lang];
                        }
                    }
                    elseif ($source == 'shortdesc')
                    {
                        if (!empty($p->description_short[$id_lang]))
                        {
                            $textes[$source] = html_entity_decode($p->description_short[$id_lang]);
                        }
                    }
                    elseif ($source == 'desc')
                    {
                        if (!empty($p->description[$id_lang]))
                        {
                            $textes[$source] = html_entity_decode($p->description[$id_lang]);
                        }
                    }
                    elseif ($source == 'meta_title')
                    {
                        if (!empty($p->meta_title[$id_lang]))
                        {
                            $textes[$source] = $p->meta_title[$id_lang];
                        }
                    }
                    elseif ($source == 'meta_description')
                    {
                        if (!empty($p->meta_description[$id_lang]))
                        {
                            $textes[$source] = $p->meta_description[$id_lang];
                        }
                    }
                    elseif ($source == 'meta_keywords')
                    {
                        if (!empty($p->meta_keywords[$id_lang]))
                        {
                            $textes[$source] = $p->meta_keywords[$id_lang];
                        }
                    }
                }

                $advanced = false;
                if (!empty($params['level']) && $params['level'] == 'advanced')
                {
                    $advanced = true;
                }
                $amount = Dixit::getPrice($textes, $advanced);
                $cost = Dixit::getCost($textes, $advanced);

                if (!eServicesTools::checkHasFizz($amount))
                {
                    $headers = array();
                    $posts = array();
                    $posts['KEY'] = 'gt789zef132kiy789u13v498ve15nhry98';
                    $posts['LICENSE'] = '#';
                    $posts['URLCALLING'] = '#';
                    $posts['status'] = '113';
                    if (defined('IS_SUBS') && IS_SUBS == '1')
                    {
                        $posts['SUBSCRIPTION'] = '1';
                    }
                    $ret = makeCallToOurApi('Fizz/Project/Update/'.$project['id_project'], $headers, $posts);

                    return array('status' => 'error', 'message' => _l('Not enough Fizz. Refill your wallet and re-start project'), 'stop' => '1');
                }

                $headers = array();
                $posts = array();
                $posts['KEY'] = 'gt789zef132kiy789u13v498ve15nhry98';
                $ret = makeCallToOurApi('Dixit/getBalance/', $headers, $posts);
                if (!empty($ret['code']) && $ret['code'] == '200' && isset($ret['balance']))
                {
                    if (empty($ret['balance']) || $ret['balance'] < $cost)
                    {
                        $headers = array();
                        $posts = array();
                        $posts['KEY'] = 'gt789zef132kiy789u13v498ve15nhry98';
                        $posts['LICENSE'] = '#';
                        $posts['URLCALLING'] = '#';
                        $posts['status'] = '300';
                        if (defined('IS_SUBS') && IS_SUBS == '1')
                        {
                            $posts['SUBSCRIPTION'] = '1';
                        }
                        $ret = makeCallToOurApi('Fizz/Project/Update/'.$project['id_project'], $headers, $posts);

                        return array('status' => 'error', 'message' => _l('Store Commander validation required'), 'stop' => '1');
                    }
                }

                $error = false;
                $headers = array();
                $posts = array();
                $posts['KEY'] = 'gt789zef132kiy789u13v498ve15nhry98';
                $posts['LICENSE'] = '#';
                $posts['URLCALLING'] = '#';
                $posts['id_project'] = $project['id_project'];
                $posts['id_product'] = $pdt;
                $posts['name'] = $name;
                $posts['comment'] = $comment;
                $posts['url_photo'] = $image;
                $posts['textes'] = str_replace('&', '%26', json_encode($textes));
                $posts['amount'] = $amount;
                if (defined('IS_SUBS') && IS_SUBS == '1')
                {
                    $posts['SUBSCRIPTION'] = '1';
                }
                $ret = makeCallToOurApi('Dixit/createTextMulti/', $headers, $posts, 500);
                if (!empty($ret['code']) && $ret['code'] == '200' && !empty($ret['id_transaction']))
                {
                    $pdts_transactionId[$pdt] = $ret['id_transaction'];
                }
                else
                {
                    $error = true;
                }

                if ($error)
                {
                    if (!empty($pdts_transactionId[$pdt]))
                    {
                        if ($pdts_transactionId[$pdt] == 'error')
                        {
                            $pdts_transactionId[$pdt] = 'reerror';
                        }
                        elseif ($pdts_transactionId[$pdt] != 'reerror')
                        {
                            $pdts_transactionId[$pdt] = 'error';
                        }
                    }
                    else
                    {
                        $pdts_transactionId[$pdt] = 'error';
                    }
                }

                ++$num;
            }
            if ($num > $limit)
            {
                break;
            }
        }

        // CHECK STATUS
        $total_pdts = count($pdts);
        $pdts_transacted = 0;
        $pdts_inerror = 0;

        $pdts_transactionId_new = array();
        $pdts_transactionId_diff = false;
        $headers = array();
        $posts = array();
        $posts['KEY'] = 'gt789zef132kiy789u13v498ve15nhry98';
        $posts['LICENSE'] = '#';
        $posts['URLCALLING'] = '#';
        if (defined('IS_SUBS') && IS_SUBS == '1')
        {
            $posts['SUBSCRIPTION'] = '1';
        }
        $ret = makeCallToOurApi('Fizz/Project/Get/'.$project['id_project'], $headers, $posts);
        if (!empty($ret['code']) && $ret['code'] == '200')
        {
            $project_new = $ret['project'];
            $params = (!empty($project_new['params']) ? json_decode($project_new['params'], true) : '');
            if (!empty($params['transactionId']))
            {
                $pdts_transactionId_new = $params['transactionId'];
            }
        }
        foreach ($pdts_transactionId as $pdt => $id_transaction)
        {
            if (!empty($pdts_transactionId_new[$pdt]))
            {
                if ($pdts_transactionId_new[$pdt] != $id_transaction)
                {
                    $pdts_transactionId_diff = true;
                }
            }
            else
            {
                if (!empty($id_transaction))
                {
                    $pdts_transactionId_diff = true;
                }
            }

            if (is_numeric($id_transaction) || $id_transaction == 'reerror')
            {
                ++$pdts_transacted;
            }
            if ($id_transaction == 'reerror')
            {
                ++$pdts_inerror;
            }
        }

        if ($pdts_transactionId_diff && !empty($project_new))
        {
            $params['transactionId'] = $pdts_transactionId;
            $headers = array();
            $posts = array();
            $posts['KEY'] = 'gt789zef132kiy789u13v498ve15nhry98';
            $posts['LICENSE'] = '#';
            $posts['URLCALLING'] = '#';
            $posts['params'] = json_encode($params);
            if (defined('IS_SUBS') && IS_SUBS == '1')
            {
                $posts['SUBSCRIPTION'] = '1';
            }
            $ret = makeCallToOurApi('Fizz/Project/Update/'.$project['id_project'], $headers, $posts);
        }

        // CHECK QUEUE
        if ($total_pdts == $pdts_transacted && $pdts_inerror > 0 && $total_pdts == $pdts_inerror)
        {
            $headers = array();
            $posts = array();
            $posts['KEY'] = 'gt789zef132kiy789u13v498ve15nhry98';
            $posts['LICENSE'] = '#';
            $posts['URLCALLING'] = '#';
            $posts['status'] = '109';
            if (defined('IS_SUBS') && IS_SUBS == '1')
            {
                $posts['SUBSCRIPTION'] = '1';
            }
            $ret = makeCallToOurApi('Fizz/Project/Update/'.$project['id_project'], $headers, $posts);

            $return = array('status' => 'success', 'message' => '', 'stop' => '1');
        }
        elseif ($total_pdts == $pdts_transacted && $pdts_inerror > 0 && $total_pdts > $pdts_inerror)
        {
            $headers = array();
            $posts = array();
            $posts['KEY'] = 'gt789zef132kiy789u13v498ve15nhry98';
            $posts['LICENSE'] = '#';
            $posts['URLCALLING'] = '#';
            $posts['status'] = '111';
            if (defined('IS_SUBS') && IS_SUBS == '1')
            {
                $posts['SUBSCRIPTION'] = '1';
            }
            $ret = makeCallToOurApi('Fizz/Project/Update/'.$project['id_project'], $headers, $posts);

            $return = array('status' => 'success', 'message' => '', 'stop' => '1');
        }
        elseif ($total_pdts == $pdts_transacted && $total_pdts > $pdts_inerror)
        {
            $headers = array();
            $posts = array();
            $posts['KEY'] = 'gt789zef132kiy789u13v498ve15nhry98';
            $posts['LICENSE'] = '#';
            $posts['URLCALLING'] = '#';
            $posts['status'] = '10';
            if (defined('IS_SUBS') && IS_SUBS == '1')
            {
                $posts['SUBSCRIPTION'] = '1';
            }
            $ret = makeCallToOurApi('Fizz/Project/Update/'.$project['id_project'], $headers, $posts);

            $return = array('status' => 'success', 'message' => '', 'stop' => '1');
        }
        else
        {
            $return = array('status' => 'success', 'message' => '');
        }
    }

    return $return;
}

function dixit_action_checkStatus($project)
{
    $params = (!empty($project['params']) ? json_decode($project['params'], true) : '');
    $return = array();
    if (in_array($project['status'], array('10', '111')))
    {
        $headers = array();
        $posts = array();
        $posts['KEY'] = 'gt789zef132kiy789u13v498ve15nhry98';
        $posts['LICENSE'] = '#';
        $posts['URLCALLING'] = '#';
        $posts['id_project'] = $project['id_project'];
        if (defined('IS_SUBS') && IS_SUBS == '1')
        {
            $posts['SUBSCRIPTION'] = '1';
        }
        $ret = makeCallToOurApi('Dixit/checkStatus/', $headers, $posts);
        if (!empty($ret['code']) && $ret['code'] == '200' && !empty($ret['is_all_good']) && $ret['is_all_good'] == '1')
        {
            $headers = array();
            $posts = array();
            $posts['KEY'] = 'gt789zef132kiy789u13v498ve15nhry98';
            $posts['LICENSE'] = '#';
            $posts['URLCALLING'] = '#';
            $posts['status'] = '11';
            if (defined('IS_SUBS') && IS_SUBS == '1')
            {
                $posts['SUBSCRIPTION'] = '1';
            }
            $ret = makeCallToOurApi('Fizz/Project/Update/'.$project['id_project'], $headers, $posts);

            $project['status'] = '11';
            $return = dixit_action_importDatas($project);
        }
        elseif (!empty($ret['code']) && $ret['code'] == '200' && isset($ret['is_all_good']) && $ret['is_all_good'] != '1')
        {
            $return = array('status' => 'info', 'message' => _l('The projet is not yet processed.'));
        }
    }
    elseif (in_array($project['status'], array('11', '12')))
    {
        $return = dixit_action_importDatas($project);
    }

    return $return;
}

function dixit_action_importDatas($project)
{
    $params = (!empty($project['params']) ? json_decode($project['params'], true) : '');
    $return = array();
    if (in_array($project['status'], array('11', '12')))
    {
        if ($project['status'] != '12')
        {
            $headers = array();
            $posts = array();
            $posts['KEY'] = 'gt789zef132kiy789u13v498ve15nhry98';
            $posts['LICENSE'] = '#';
            $posts['URLCALLING'] = '#';
            $posts['status'] = '12';
            if (defined('IS_SUBS') && IS_SUBS == '1')
            {
                $posts['SUBSCRIPTION'] = '1';
            }
            $ret = makeCallToOurApi('Fizz/Project/Update/'.$project['id_project'], $headers, $posts);
        }

        $pdts = explode('-', trim($project['list_items'], '-'));

        $importedPdt_state = array();
        if (!empty($params['importedPdt']))
        {
            $importedPdt_state = $params['importedPdt'];
        }
        $pdts_imported = 0;

        $id_lang = '';
        if (!empty($params['lang_translation']))
        {
            list($id_lang_dixit, $iso) = explode('_', $params['lang_translation']);
        }
        if (!empty($iso))
        {
            $id_lang = Language::getIdByIso($iso);
        }

        foreach ($pdts as $pdt)
        {
            if (!empty($importedPdt_state[$pdt]))
            {
                ++$pdts_imported;
                continue;
            }

            $headers = array();
            $posts = array();
            $posts['KEY'] = 'gt789zef132kiy789u13v498ve15nhry98';
            $posts['LICENSE'] = '#';
            $posts['URLCALLING'] = '#';
            $posts['id_project'] = $project['id_project'];
            $posts['id_product'] = $pdt;
            if (defined('IS_SUBS') && IS_SUBS == '1')
            {
                $posts['SUBSCRIPTION'] = '1';
            }
            $ret = makeCallToOurApi('Dixit/getTranslatedTextes/', $headers, $posts, 500);
            if (!empty($ret['code']) && $ret['code'] == '200' && !empty($ret['translated_textes']))
            {
                $translated_textes = $ret['translated_textes'];

                if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
                {
                    $id_shop_default = Db::getInstance()->getValue('SELECT id_shop_default FROM `'._DB_PREFIX_.'product` WHERE `id_product` = "'.(int) $pdt.'"');
                    $p = new Product($pdt, false, null, $id_shop_default);
                }
                else
                {
                    $p = new Product($pdt);
                }

                if (empty($p->price))
                {
                    $p->price = '0.00';
                }

                foreach ($translated_textes as $txt)
                {
                    $type = str_replace($pdt.'_', '', $txt['identifier']);
                    $content = $txt['content'];
                    if (!empty($content))
                    {
                        //$content = "English version - ".date("Y-m-d H:i:s")." - ".$content;
                        $oldvalue = '';
                        $value = '';
                        if ($type == 'name')
                        {
                            $oldvalue = $p->name[$id_lang];
                            $value = dixit_cleanString(strip_tags(html_entity_decode($content)));
                            $p->name[$id_lang] = $value;

                            if (_s('CAT_SEO_NAME_TO_URL'))
                            {
                                $p->link_rewrite[$id_lang] = link_rewrite($value, Language::getIsoById($id_lang));
                            }
                        }
                        elseif ($type == 'shortdesc')
                        {
                            $oldvalue = $p->description_short[$id_lang];
                            $value = dixit_cleanString($content);
                            $p->description_short[$id_lang] = ($content);
                        }
                        elseif ($type == 'desc')
                        {
                            $oldvalue = $p->description[$id_lang];
                            $value = dixit_cleanString($content);
                            $p->description[$id_lang] = ($content);
                        }
                        elseif ($type == 'meta_title')
                        {
                            $oldvalue = $p->meta_title[$id_lang];
                            $value = dixit_cleanString(strip_tags(html_entity_decode($content)));
                            $p->meta_title[$id_lang] = $value;
                        }
                        elseif ($type == 'meta_description')
                        {
                            $oldvalue = $p->meta_description[$id_lang];
                            $value = dixit_cleanString(strip_tags(html_entity_decode($content)));
                            $p->meta_description[$id_lang] = $value;
                        }
                        elseif ($type == 'meta_keywords')
                        {
                            $oldvalue = $p->meta_keywords[$id_lang];
                            $value = dixit_cleanString(strip_tags(html_entity_decode($content)));
                            $p->meta_keywords[$id_lang] = $value;
                        }
                        addToHistory('e-services', 'modification', $type, $pdt, (int) $id_lang, _DB_PREFIX_.'product_lang', $value, $oldvalue);
                    }
                }

                $error = false;

                try
                {
                    if (SCMS)
                    {
                        $p->id_shop_list = array($id_shop_default);
                    }
                    $p->save();
                }
                catch (Exception $e)
                {
                    $error = true;
                }

                if (!$error)
                {
                    $importedPdt_state[$pdt] = '1';
                    ++$pdts_imported;

                    $headers = array();
                    $posts = array();
                    $posts['KEY'] = 'gt789zef132kiy789u13v498ve15nhry98';
                    $posts['LICENSE'] = '#';
                    $posts['URLCALLING'] = '#';
                    $params['importedPdt'] = $importedPdt_state;
                    $posts['params'] = json_encode($params);
                    if (defined('IS_SUBS') && IS_SUBS == '1')
                    {
                        $posts['SUBSCRIPTION'] = '1';
                    }
                    $ret = makeCallToOurApi('Fizz/Project/Update/'.$project['id_project'], $headers, $posts);
                }
            }
            else
            {
                $importedPdt_state[$pdt] = '0';
            }
        }

        $transactionId = array();
        if (!empty($params['transactionId']))
        {
            $transactionId = $params['transactionId'];
        }
        $hasError = false;
        $finish = true;
        foreach ($transactionId as $id_p => $t)
        {
            if (!empty($t) && is_numeric($t))
            {
                if (isset($importedPdt_state[$id_p]) && $importedPdt_state[$id_p] == '0')
                {
                    $hasError = true;
                }
                elseif (!isset($importedPdt_state[$id_p]))
                {
                    $finish = false;
                }
            }
        }

        if (!$hasError && $finish)
        {
            $headers = array();
            $posts = array();
            $posts['KEY'] = 'gt789zef132kiy789u13v498ve15nhry98';
            $posts['LICENSE'] = '#';
            $posts['URLCALLING'] = '#';
            $posts['status'] = '13';
            if (defined('IS_SUBS') && IS_SUBS == '1')
            {
                $posts['SUBSCRIPTION'] = '1';
            }
            $ret = makeCallToOurApi('Fizz/Project/Update/'.$project['id_project'], $headers, $posts);
        }
        elseif ($hasError)
        {
            $headers = array();
            $posts = array();
            $posts['KEY'] = 'gt789zef132kiy789u13v498ve15nhry98';
            $posts['LICENSE'] = '#';
            $posts['URLCALLING'] = '#';
            $posts['status'] = '112';
            if (defined('IS_SUBS') && IS_SUBS == '1')
            {
                $posts['SUBSCRIPTION'] = '1';
            }
            $ret = makeCallToOurApi('Fizz/Project/Update/'.$project['id_project'], $headers, $posts);
            $return = array('status' => 'error', 'message' => _l('Error during import, please contact our support team.'));
        }
        else
        {
            $return = array('status' => 'error', 'message' => _l('The import is not complete. Please run it again. If the problem persists, please contact our support team.'));
        }
    }

    return $return;
}

function dixit_cleanString($string)
{
    $string = str_replace('’', "'", $string);

    return $string;
}
