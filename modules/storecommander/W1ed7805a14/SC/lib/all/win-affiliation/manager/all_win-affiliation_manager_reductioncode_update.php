<?php

$id_lang = (int) Tools::getValue('id_lang', 0);
$id_partner = (int) Tools::getValue('id_partner', 0);
$code = (Tools::getValue('code', ''));

$return = 0;

if (!empty($code) && !empty($id_partner))
{
    if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
    {
        $sql = 'SELECT id_cart_rule
                FROM '._DB_PREFIX_."cart_rule
                WHERE code='".pSQL($code)."'";
        $res = Db::getInstance()->ExecuteS($sql);
        if (empty($res[0]['id_cart_rule']))
        {
            $coupon = new CartRule();
            $coupon->date_from = date('Y-m-d H:i:s');
            $coupon->date_to = date('Y-m-d H:i:s', strtotime(date('Y-m-d H:i:s', mktime()).' + '.(10 * 365).' day'));
            $coupon->code = $code;
            $coupon->name = array($id_lang => _l('Partner reduction:').' '.$code, SCI::getConfigurationValue('PS_LANG_DEFAULT') => _l('Partner reduction:').' '.$code);
            $coupon->active = 1;

            if ($coupon->save())
            {
                $partner = new SCAffPartner($id_partner);
                $partner->coupon_code = $code;
                $partner->save();

                $return = $coupon->id;
            }
        }
    }
    else
    {
        $sql = 'SELECT id_discount
                FROM '._DB_PREFIX_."discount
                WHERE name='".pSQL($code)."'";
        $res = Db::getInstance()->ExecuteS($sql);
        if (empty($res[0]['id_discount']))
        {
            $coupon = new Discount();
            $coupon->date_from = date('Y-m-d H:i:s');
            $coupon->date_to = date('Y-m-d H:i:s', strtotime(date('Y-m-d H:i:s', mktime()).' + '.(10 * 365).' day'));
            $coupon->name = $code;
            $coupon->id_discount_type = 1;
            $coupon->value = 0;
            $coupon->quantity = 0;
            $coupon->quantity_per_user = 0;
            $coupon->active = 1;

            if ($coupon->save())
            {
                $partner = new SCAffPartner($id_partner);
                $partner->coupon_code = $code;
                $partner->save();

                $return = $coupon->id;
            }
        }
    }
}

echo $return;
