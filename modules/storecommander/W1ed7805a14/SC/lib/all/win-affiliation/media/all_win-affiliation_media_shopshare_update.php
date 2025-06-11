<?php

$idlist = Tools::getValue('idlist', '');
$action = Tools::getValue('action', '');
$id_lang = Tools::getValue('id_lang', '0');
$id_shop = Tools::getValue('id_shop', '0');
$value = Tools::getValue('value', '0');

if ($value == 'true' || $value == 1)
{
    $value = 1;
}
else
{
    $value = 0;
}

$multiple = false;
if (strpos($idlist, ',') !== false)
{
    $multiple = true;
}

$ids = explode(',', $idlist);

if ($action != '' && !empty($id_shop) && !empty($idlist))
{
    switch ($action) {
        case 'present':
            foreach ($ids as $id)
            {
                $banner = new SCAffBanner($id);
                $banner_shops = $banner->GetShops();

                if ($value == 1 && !in_array($id_shop, $banner_shops))
                {
                    $banner_shops[] = $id_shop;
                }
                elseif ($value == 0 && in_array($id_shop, $banner_shops))
                {
                    $banner_shops = array_diff($banner_shops, array($id_shop));
                }

                $banner->shops = '-';
                foreach ($banner_shops as $banner_shop)
                {
                    if (!empty($banner_shop))
                    {
                        $banner->shops .= $banner_shop.'-';
                    }
                }
                $banner->save();
            }
            break;
        case 'mass_present':
            $shops = explode(',', $id_shop);
            foreach ($shops as $id_shop)
            {
                foreach ($ids as $id)
                {
                    echo $id.'->'.$value.'<br/>';
                    $banner = new SCAffBanner($id);
                    $banner_shops = $banner->GetShops();

                    if ($value == 1 && !in_array($id_shop, $banner_shops))
                    {
                        $banner_shops[] = $id_shop;
                    }
                    elseif ($value == 0 && in_array($id_shop, $banner_shops))
                    {
                        $banner_shops = array_diff($banner_shops, array($id_shop));
                    }

                    $banner->shops = '-';
                    foreach ($banner_shops as $banner_shop)
                    {
                        if (!empty($banner_shop))
                        {
                            $banner->shops .= $banner_shop.'-';
                        }
                    }
                    $banner->save();
                }
            }
            break;
    }
}
