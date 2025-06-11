<?php

function check_if_not_exist($code)
{
    $sql = 'SELECT * FROM `'._DB_PREFIX_.
        (version_compare(_PS_VERSION_, '1.5.0.1', '>=') ? 'cart_rule' : 'discount')
        .'` WHERE `code` = "'.pSQL($code).'"';
    return (Db::getInstance()->getRow($sql));
}

function generate_cr_code($size)
{
    $alphabet = '123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $len=strlen($alphabet);
    $code = '';
    for ($i = 0; $i < $size; $i++) {
        $code .= $alphabet[rand(0, $len-1 )];
    }
    return $code;
}

$content = trim(file_get_contents("php://input"));
$data = json_decode($content, true);

$cr_qty_20 = $data['cr_qty_20'];
$cr_qty_30 = $data['cr_qty_30'];
$cr_qty_50 = $data['cr_qty_50'];
$cr_qty_100 = $data['cr_qty_100'];
$cr_id_scc = $data['cr_id_scc'];
$cr_id_scc_formated = str_pad($cr_id_scc,4,'0',STR_PAD_LEFT);
$cr_id_shop = $data['cr_id_shop'];
$cr_url_shop = $data['cr_url_shop'];

function GenerateCR($cr_qty, $cr_amount, $cr_id_scc_formated, $cr_id_shop)
{
    $return = array();

    for ($i=0; $i<$cr_qty; $i++)
    {
        $cr_random_code = '';
        do $cr_code = 'SCC' . $cr_id_scc_formated . generate_cr_code(8);
        while (!empty(check_if_not_exist($cr_code)));

        if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
        {
            $sql = 'SELECT id_cart_rule
                    FROM ' . _DB_PREFIX_ . "cart_rule
                    WHERE code='" . pSQL($cr_code) . "'";
            $res = Db::getInstance()->ExecuteS($sql);

            if (!$res)
            {
                $coupon = new CartRule();
                $coupon->date_from = date('Y-m-d H:i:s');
                $coupon->date_to = '2024-12-31 23:59:59';
                $coupon->code = $cr_code;
                $coupon->reduction_amount = $cr_amount;
                $coupon->name = array(SCI::getConfigurationValue('PS_LANG_DEFAULT') => 'E-carte '.$cr_amount.'€ '.$cr_code);
                $coupon->quantity = 1;
                $coupon->quantity_per_user = 1;
                $coupon->active = 1;
                $coupon->reduction_tax = 1;
                $coupon->shop_restriction = 1;

                if ($coupon->add())
                {
                    $return[$coupon->id] = implode(';',[$coupon->id, $cr_code, $cr_amount, $coupon->date_from, $coupon->date_to, $cr_id_shop, $cr_id_scc_formated]);
                }

                // since shop_restriction = 1 we need to add line in ps_cart_rule_shop
                $sql = 'INSERT INTO ' . _DB_PREFIX_ .'cart_rule_shop
                        VALUES ('.(int) $coupon->id.','.(int) $cr_id_shop.')';
                $res = Db::getInstance()->ExecuteS($sql);
            }
        }
        else
        {
            $sql = 'SELECT id_discount
                    FROM ' . _DB_PREFIX_ . "discount
                    WHERE name='" . pSQL($cr_code) . "'";
            $res = Db::getInstance()->ExecuteS($sql);
            if (!$res)
            {
                $coupon = new Discount();
                $coupon->date_from = date('Y-m-d H:i:s');
                $coupon->date_to = '2024-12-31 23:59:59';
                $coupon->name = 'E-carte '.$cr_amount.'€ '.$cr_code;
                $coupon->id_discount_type = 1;
                $coupon->code = $cr_code;
                $coupon->value = $cr_amount;
                $coupon->quantity = 1;
                $coupon->quantity_per_user = 1;
                $coupon->active = 1;
                $coupon->reduction_tax = 1;

                if ($coupon->save())
                {
                    $return[$coupon->id] = implode(';',[$coupon->id, $cr_code, $cr_amount, $coupon->date_from, $coupon->date_to, $cr_id_shop, $cr_id_scc_formated]);
                }
            }
        }
    }
    return $return;
}

// preparation du mail
$MailSubject="SCC - E-cartes générées pour ".$cr_url_shop."\n";
$MailContent= "prefix marchand : ".$cr_id_scc_formated." - id_shop : ".$cr_id_shop."\n";
$MailContent .= $cr_qty_20 . " de 20€\n";
$MailContent .= $cr_qty_30." de 30€\n";
$MailContent .= $cr_qty_50." de 50€\n";
$MailContent .= $cr_qty_100." de 100€\n\n";
$MailContent .= "ID "._l('Cart rule').";"._l('Code').";"._l('Amount').";"._l('Date from').";"._l('Date to').";ID "._l('shop').";ID SCC "._l('customer')."\n";

$recap = array();

$recap = array_merge($recap, GenerateCR($cr_qty_20, 20, $cr_id_scc_formated, $cr_id_shop));
$recap = array_merge($recap, GenerateCR($cr_qty_30, 30, $cr_id_scc_formated, $cr_id_shop));
$recap = array_merge($recap, GenerateCR($cr_qty_50, 50, $cr_id_scc_formated, $cr_id_shop));
$recap = array_merge($recap, GenerateCR($cr_qty_100, 100, $cr_id_scc_formated, $cr_id_shop));

foreach ($recap as $v)
{
    $MailContent.=$v."\n";
}

// Envoi du mail => scc@storecommander.com
if (!mail('scc@storecommander.com', $MailSubject, $MailContent)){
    $recap['error']='mail_not_sent';
}

echo json_encode($recap);
?>
