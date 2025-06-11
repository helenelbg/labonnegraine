<?php

$action = Tools::getValue('action');
$id_lang = Tools::getValue('id_lang');

switch ($action) {
    case 'start':
        $id_project = Tools::getValue('id_project');
        if (!empty($id_project))
        {
            $headers = array();
            $posts = array();
            $posts['KEY'] = 'gt789zef132kiy789u13v498ve15nhry98';
            $posts['LICENSE'] = '#';
            $posts['URLCALLING'] = '#';
            if (defined('IS_SUBS') && IS_SUBS == '1')
            {
                $posts['SUBSCRIPTION'] = '1';
            }
            $ret = makeCallToOurApi('Fizz/Project/Get/'.$id_project, $headers, $posts);
            if (!empty($ret['code']) && $ret['code'] == '200')
            {
                $project = $ret['project'];

                $type = $project['type'];

                if ($project['type'] == 'dixit')
                {
                    if (in_array($project['status'] != '7'))
                    {
                        exit(json_encode(array('status' => 'error', 'message' => _l('This project has the wrong status to start'))));
                    }
                }

                $prices = array('amount' => 0, 'cost' => 0);
                $func = $type.'_action_getQuote';
                if (function_exists($func))
                {
                    $prices = $func($project);
                }

                $price = (string) $prices['amount'];
                $cost = (string) $prices['cost'];
                $project['amount'] = (string) $project['amount'];

                $good = false;
                $waiting = false;
                if (eServicesTools::checkHasFizz($project['amount']) && $price == $project['amount'])
                {
                    $good = true;

                    if ($project['type'] == 'dixit')
                    {
                        if ($project['amount'] >= 1500)
                        {
                            $waiting = true;
                        }
                        else
                        {
                            $headers = array();
                            $posts = array();
                            $posts['KEY'] = 'gt789zef132kiy789u13v498ve15nhry98';
                            $ret = makeCallToOurApi('Dixit/getBalance/', $headers, $posts);
                            if (!empty($ret['code']) && $ret['code'] == '200' && isset($ret['balance']))
                            {
                                if (empty($ret['balance']) || $ret['balance'] < $cost)
                                {
                                    $waiting = true;
                                }
                            }
                        }
                    }
                }

                if (!empty($project['amount']) && $good && !$waiting)
                {
                    $headers = array();
                    $posts = array();
                    $posts['KEY'] = 'gt789zef132kiy789u13v498ve15nhry98';
                    $posts['LICENSE'] = '#';
                    $posts['URLCALLING'] = '#';
                    $posts['status'] = '8';
                    if (defined('IS_SUBS') && IS_SUBS == '1')
                    {
                        $posts['SUBSCRIPTION'] = '1';
                    }
                    $ret = makeCallToOurApi('Fizz/Project/Update/'.$id_project, $headers, $posts);
                    if (!empty($ret['code']) && $ret['code'] == '200')
                    {
                        exit(json_encode(array('status' => 'success', 'action' => 'started', 'message' => '')));
                    }
                    else
                    {
                        exit(json_encode(array('status' => 'error', 'message' => _l('Error during project start'))));
                    }
                }
                elseif ($price != $project['amount'])
                {
                    $headers = array();
                    $posts = array();
                    $posts['KEY'] = 'gt789zef132kiy789u13v498ve15nhry98';
                    $posts['LICENSE'] = '#';
                    $posts['URLCALLING'] = '#';
                    if ($type == 'dixit')
                    {
                        $posts['status'] = '7';
                    }
                    $posts['amount'] = $price;
                    if (defined('IS_SUBS') && IS_SUBS == '1')
                    {
                        $posts['SUBSCRIPTION'] = '1';
                    }
                    $ret = makeCallToOurApi('Fizz/Project/Update/'.$id_project, $headers, $posts);
                    exit(json_encode(array('status' => 'error', 'message' => _l('The texts to translate were modified. We recalculate the price.'))));
                }
                elseif ($waiting)
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
                    $ret = makeCallToOurApi('Fizz/Project/Update/'.$id_project, $headers, $posts);
                    exit(json_encode(array('status' => 'error', 'message' => _l('Store Commander validation required'))));
                }
                else
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

                    exit(json_encode(array('status' => 'error', 'message' => _l('Not enough Fizz. Refill your wallet and re-start project'))));
                }
            }
        }
        break;
    case 'get_quote':
        $id_project = Tools::getValue('id_project');
        if (!empty($id_project))
        {
            $headers = array();
            $posts = array();
            $posts['KEY'] = 'gt789zef132kiy789u13v498ve15nhry98';
            $posts['LICENSE'] = '#';
            $posts['URLCALLING'] = '#';
            if (defined('IS_SUBS') && IS_SUBS == '1')
            {
                $posts['SUBSCRIPTION'] = '1';
            }
            $ret = makeCallToOurApi('Fizz/Project/Get/'.$id_project, $headers, $posts);
            if (!empty($ret['code']) && $ret['code'] == '200')
            {
                $project = $ret['project'];

                $type = $project['type'];

                $prices = array('amount' => 0, 'cost' => 0);
                $func = $type.'_action_getQuote';
                if (function_exists($func))
                {
                    $prices = $func($project);
                }
                $price = (string) $prices['amount'];
                $cost = (string) $prices['cost'];

                $headers = array();
                $posts = array();
                $posts['KEY'] = 'gt789zef132kiy789u13v498ve15nhry98';
                $posts['LICENSE'] = '#';
                $posts['URLCALLING'] = '#';
                if ($type == 'dixit')
                {
                    $posts['status'] = '7';
                }
                $posts['amount'] = $price;
                if (defined('IS_SUBS') && IS_SUBS == '1')
                {
                    $posts['SUBSCRIPTION'] = '1';
                }
                $ret = makeCallToOurApi('Fizz/Project/Update/'.$id_project, $headers, $posts);
                if (!empty($ret['code']) && $ret['code'] == '200')
                {
                    exit(json_encode(array('status' => 'success', 'message' => '')));
                }
                else
                {
                    exit(json_encode(array('status' => 'error', 'message' => _l('Error during setting price'))));
                }
            }
        }
        break;
    case 'configuring':
        $id_project = Tools::getValue('id_project');
        if (!empty($id_project))
        {
            $headers = array();
            $posts = array();
            $posts['KEY'] = 'gt789zef132kiy789u13v498ve15nhry98';
            $posts['LICENSE'] = '#';
            $posts['URLCALLING'] = '#';
            $posts['status'] = '1';
            if (defined('IS_SUBS') && IS_SUBS == '1')
            {
                $posts['SUBSCRIPTION'] = '1';
            }
            $ret = makeCallToOurApi('Fizz/Project/Update/'.$id_project, $headers, $posts);
        }
        break;
    case 'update':
        $id_project = Tools::getValue('id_project');
        if (!empty($id_project))
        {
            $headers = array();
            $posts = array();
            $posts['KEY'] = 'gt789zef132kiy789u13v498ve15nhry98';
            $posts['LICENSE'] = '#';
            $posts['URLCALLING'] = '#';
            if (defined('IS_SUBS') && IS_SUBS == '1')
            {
                $posts['SUBSCRIPTION'] = '1';
            }
            $ret = makeCallToOurApi('Fizz/Project/Get/'.$id_project, $headers, $posts);
            if (!empty($ret['code']) && $ret['code'] == '200')
            {
                $project = $ret['project'];
                $params = (!empty($project['params']) ? json_decode($project['params'], true) : '');

                if ($project['type'] == 'dixit')
                {
                    $params['source'] = Tools::getValue('source', '');
                    $params['lang_source'] = Tools::getValue('lang_source', '');
                    $params['lang_translation'] = Tools::getValue('lang_translation', '');
                    $params['level'] = Tools::getValue('level', '');
                    $params['comment'] = Tools::getValue('comment', '');
                }

                $headers = array();
                $posts = array();
                $posts['KEY'] = 'gt789zef132kiy789u13v498ve15nhry98';
                $posts['LICENSE'] = '#';
                $posts['URLCALLING'] = '#';
                $posts['params'] = json_encode($params);
                $posts['status'] = '2';
                $posts['amount'] = '0';
                if (defined('IS_SUBS') && IS_SUBS == '1')
                {
                    $posts['SUBSCRIPTION'] = '1';
                }
                $ret = makeCallToOurApi('Fizz/Project/Update/'.$id_project, $headers, $posts);
            }
        }
        break;
    case 'archive':
        $id_project = Tools::getValue('id_project');
        if (!empty($id_project))
        {
            $headers = array();
            $posts = array();
            $posts['KEY'] = 'gt789zef132kiy789u13v498ve15nhry98';
            $posts['LICENSE'] = '#';
            $posts['URLCALLING'] = '#';
            $posts['status'] = '999';
            if (defined('IS_SUBS') && IS_SUBS == '1')
            {
                $posts['SUBSCRIPTION'] = '1';
            }
            $ret = makeCallToOurApi('Fizz/Project/Update/'.$id_project, $headers, $posts);
            if (!empty($ret['code']) && $ret['code'] == '200')
            {
                // CREATE ARCHIVED CATEGORY
                $eservices_cat_archived = SCI::getConfigurationValue('SC_ESERVICES_CATEGORYARCHIVED');
                if (!empty($eservices_cat_archived))
                {
                    if (!Category::existsInDatabase((int) $eservices_cat_archived, 'category'))
                    {
                        $eservices_cat_archived = null;
                    }
                }
                if (empty($eservices_cat_archived))
                {
                    $eservices_cat = SCI::getConfigurationValue('SC_ESERVICES_CATEGORY');
                    $id_parent = $eservices_cat;
                    $name = 'ARCHIVED';

                    $newcategory = new Category();
                    $newcategory->id_parent = $id_parent;
                    $newcategory->level_depth = $newcategory->calcLevelDepth();
                    $newcategory->active = 0;

                    if (SCMS)
                    {
                        $shops = Shop::getShops(false, null, true);
                        $newcategory->id_shop_list = $shops;
                    }

                    $languages = Language::getLanguages(true);
                    foreach ($languages as $lang)
                    {
                        $newcategory->link_rewrite[$lang['id_lang']] = link_rewrite($name, (string) $lang['iso_code']);
                        $newcategory->name[$lang['id_lang']] = $name;
                    }
                    $newcategory->add();

                    if (!in_array(1, $newcategory->getGroups()))
                    {
                        $newcategory->addGroups(array(1));
                    }
                    if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
                    {
                        $shops = Category::getShopsByCategory((int) $id_parent);
                        foreach ($shops as $shop)
                        {
                            $position = Category::getLastPosition((int) $id_parent, $shop['id_shop']);
                            if (!$position)
                            {
                                $position = 1;
                            }
                            $newcategory->addPosition($position, $shop['id_shop']);
                        }
                    }
                    $eservices_cat_archived = $newcategory->id;
                    SCI::updateConfigurationValue('SC_ESERVICES_CATEGORYARCHIVED', $newcategory->id);
                }

                // MOVE PROJECT CATEGORY
                $headers = array();
                $posts = array();
                $posts['KEY'] = 'gt789zef132kiy789u13v498ve15nhry98';
                $posts['LICENSE'] = '#';
                $posts['URLCALLING'] = '#';
                if (defined('IS_SUBS') && IS_SUBS == '1')
                {
                    $posts['SUBSCRIPTION'] = '1';
                }
                $ret = makeCallToOurApi('Fizz/Project/Get/'.$id_project, $headers, $posts);
                if (!empty($ret['code']) && $ret['code'] == '200' && !empty($ret['project']))
                {
                    $params = $ret['project']['params'];
                    if (!empty($params))
                    {
                        $params = json_decode($params, true);

                        if (!empty($params['id_category']))
                        {
                            $project = $ret['project'];
                            $cat = new Category((int) $params['id_category']);
                            $cat->id_parent = $eservices_cat_archived;
                            $cat->save();
                        }
                    }
                }
            }
        }
        break;
    case 'delete':
        $id_project = Tools::getValue('id_project');
        if (!empty($id_project))
        {
            // DELETE PROJECT CATEGORY
            $id_category = 0;
            $headers = array();
            $posts = array();
            $posts['KEY'] = 'gt789zef132kiy789u13v498ve15nhry98';
            $posts['LICENSE'] = '#';
            $posts['URLCALLING'] = '#';
            if (defined('IS_SUBS') && IS_SUBS == '1')
            {
                $posts['SUBSCRIPTION'] = '1';
            }
            $ret = makeCallToOurApi('Fizz/Project/Get/'.$id_project, $headers, $posts);
            if (!empty($ret['code']) && $ret['code'] == '200' && !empty($ret['project']))
            {
                $params = $ret['project']['params'];
                if (!empty($params))
                {
                    $params = json_decode($params, true);
                    if (!empty($params['id_category']))
                    {
                        $id_category = $params['id_category'];
                    }
                }
            }

            // DELETE PROJECT
            $headers = array();
            $posts = array();
            $posts['KEY'] = 'gt789zef132kiy789u13v498ve15nhry98';
            $posts['LICENSE'] = '#';
            $posts['URLCALLING'] = '#';
            if (defined('IS_SUBS') && IS_SUBS == '1')
            {
                $posts['SUBSCRIPTION'] = '1';
            }
            $ret = makeCallToOurApi('Fizz/Project/Delete/'.$id_project, $headers, $posts);
            if (!empty($ret['code']) && $ret['code'] == '200')
            {
                if (!empty($id_category))
                {
                    $cat = new Category((int) $id_category);
                    $cat->delete();
                }
            }
        }
        break;
    case 'add':
        $type = Tools::getValue('type');
        if (!empty($type))
        {
            $name = Tools::getValue('name', '');
            $headers = array();
            $posts = array();
            $posts['KEY'] = 'gt789zef132kiy789u13v498ve15nhry98';
            $posts['LICENSE'] = '#';
            $posts['URLCALLING'] = '#';
            $posts['type'] = $type;
            $posts['name'] = $name;
            if (defined('IS_SUBS') && IS_SUBS == '1')
            {
                $posts['SUBSCRIPTION'] = '1';
            }
            $items = Tools::getValue('items');
            if (!empty($items))
            {
                $list_items = '-'.str_replace(',', '-', $items).'-';
                $posts['list_items'] = $list_items;
            }
            $iso = Language::getIsoById($id_lang);
            $posts['iso'] = ($iso == 'fr' ? 'fr' : 'en');
            $ret = makeCallToOurApi('Fizz/Project/Create', $headers, $posts);
            if (!empty($ret['code']) && $ret['code'] == '200' && !empty($ret['id_project']))
            {
                // CREATE ESERVICES CATEGORY
                $eservices_cat = SCI::getConfigurationValue('SC_ESERVICES_CATEGORY');
                if (!empty($eservices_cat))
                {
                    if (!Category::existsInDatabase((int) $eservices_cat, 'category'))
                    {
                        $eservices_cat = null;
                    }
                }
                if (empty($eservices_cat))
                {
                    if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
                    {
                        $id_parent = SCI::getConfigurationValue('PS_HOME_CATEGORY');
                    }
                    else
                    {
                        $id_parent = 1;
                    }
                    $name_cat = 'e-Services';

                    $newcategory = new Category();
                    $newcategory->id_parent = $id_parent;
                    $newcategory->level_depth = $newcategory->calcLevelDepth();
                    $newcategory->active = 0;

                    if (SCMS)
                    {
                        $shops = Shop::getShops(false, null, true);
                        $newcategory->id_shop_list = $shops;
                    }

                    $languages = Language::getLanguages(true);
                    foreach ($languages as $lang)
                    {
                        $newcategory->link_rewrite[$lang['id_lang']] = link_rewrite($name_cat, (string) $lang['iso_code']);
                        $newcategory->name[$lang['id_lang']] = $name_cat;
                    }
                    $newcategory->add();

                    if (!in_array(1, $newcategory->getGroups()))
                    {
                        $newcategory->addGroups(array(1));
                    }
                    if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
                    {
                        $shops = Category::getShopsByCategory((int) $id_parent);
                        foreach ($shops as $shop)
                        {
                            $position = Category::getLastPosition((int) $id_parent, $shop['id_shop']);
                            if (!$position)
                            {
                                $position = 1;
                            }
                            $newcategory->addPosition($position, $shop['id_shop']);
                        }
                    }
                    $eservices_cat = $newcategory->id;
                    SCI::updateConfigurationValue('SC_ESERVICES_CATEGORY', $newcategory->id);
                }

                // CREATE ARCHIVED CATEGORY
                if (!empty($eservices_cat))
                {
                    $eservices_cat_archived = SCI::getConfigurationValue('SC_ESERVICES_CATEGORYARCHIVED');
                    if (!empty($eservices_cat_archived))
                    {
                        if (!Category::existsInDatabase((int) $eservices_cat_archived, 'category'))
                        {
                            $eservices_cat_archived = null;
                        }
                    }
                    if (empty($eservices_cat_archived))
                    {
                        $id_parent = $eservices_cat;
                        $name_cat = 'ARCHIVED';

                        $newcategory = new Category();
                        $newcategory->id_parent = $id_parent;
                        $newcategory->level_depth = $newcategory->calcLevelDepth();
                        $newcategory->active = 0;

                        if (SCMS)
                        {
                            $shops = Shop::getShops(false, null, true);
                            $newcategory->id_shop_list = $shops;
                        }

                        $languages = Language::getLanguages(true);
                        foreach ($languages as $lang)
                        {
                            $newcategory->link_rewrite[$lang['id_lang']] = link_rewrite($name_cat, (string) $lang['iso_code']);
                            $newcategory->name[$lang['id_lang']] = $name_cat;
                        }
                        $newcategory->add();

                        if (!in_array(1, $newcategory->getGroups()))
                        {
                            $newcategory->addGroups(array(1));
                        }
                        if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
                        {
                            $shops = Category::getShopsByCategory((int) $id_parent);
                            foreach ($shops as $shop)
                            {
                                $position = Category::getLastPosition((int) $id_parent, $shop['id_shop']);
                                if (!$position)
                                {
                                    $position = 1;
                                }
                                $newcategory->addPosition($position, $shop['id_shop']);
                            }
                        }
                        $eservices_cat_archived = $newcategory->id;
                        SCI::updateConfigurationValue('SC_ESERVICES_CATEGORYARCHIVED', $newcategory->id);
                    }
                }

                // CREATE PROJECT CATEGORY
                $id_parent = $eservices_cat;

                $newcategory = new Category();
                $newcategory->id_parent = $id_parent;
                $newcategory->level_depth = $newcategory->calcLevelDepth();
                $newcategory->active = 0;

                if (SCMS)
                {
                    $shops = Shop::getShops(false, null, true);
                    $newcategory->id_shop_list = $shops;
                }

                $languages = Language::getLanguages(true);
                foreach ($languages as $lang)
                {
                    $newcategory->link_rewrite[$lang['id_lang']] = link_rewrite($name, (string) $lang['iso_code']);
                    $newcategory->name[$lang['id_lang']] = $name;
                }
                $newcategory->add();
                if (!empty($newcategory->id))
                {
                    if (!in_array(1, $newcategory->getGroups()))
                    {
                        $newcategory->addGroups(array(1));
                    }
                    if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
                    {
                        $shops = Category::getShopsByCategory((int) $id_parent);
                        foreach ($shops as $shop)
                        {
                            $position = Category::getLastPosition((int) $id_parent, $shop['id_shop']);
                            if (!$position)
                            {
                                $position = 1;
                            }
                            $newcategory->addPosition($position, $shop['id_shop']);
                        }
                    }

                    if (!empty($items))
                    {
                        ini_set('display_errors', 'ON');
                        $products_cat = array();
                        $i = 0;
                        $items = explode(',', $items);
                        foreach ($items as $id_p)
                        {
                            $products_cat[] = array(
                                'id_category' => (int) $newcategory->id,
                                'id_product' => (int) $id_p,
                                'position' => (int) $i,
                            );
                            ++$i;
                        }
                        Db::getInstance()->insert('category_product', $products_cat);
                    }

                    $headers = array();
                    $posts = array();
                    $posts['KEY'] = 'gt789zef132kiy789u13v498ve15nhry98';
                    $posts['LICENSE'] = '#';
                    $posts['URLCALLING'] = '#';
                    $posts['params'] = json_encode(array('id_category' => $newcategory->id));
                    if (defined('IS_SUBS') && IS_SUBS == '1')
                    {
                        $posts['SUBSCRIPTION'] = '1';
                    }
                    $ret = makeCallToOurApi('Fizz/Project/Update/'.$ret['id_project'], $headers, $posts);
                }
            }
        }
        break;
}
