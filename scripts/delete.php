<?php
  if (!isset($_GET['secure']) && $_GET['secure'] != '1548dfs5656' )
  {
    die();
  }

  ini_set('memory_limit', '-1');
  include("../config/config.inc.php");

function deleteorderbyid($id)
    {
            $shopid = 1;
            $thisorder = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('SELECT id_cart FROM ' . _DB_PREFIX_ . 'orders WHERE id_order = ' . $id . ' AND id_shop = ' . $shopid);
            if (isset($thisorder[0])) {
                $q = 'DELETE a,b FROM ' . _DB_PREFIX_ . 'order_return AS a LEFT JOIN ' . _DB_PREFIX_ . 'order_return_detail AS b ON a.id_order_return = b.id_order_return WHERE id_order="' . $id . '"';
                if (!Db::getInstance()->Execute($q)) {
                    $this->errorlog = $this->l('ERROR');
                }

                // deleting order_slip
                $q = 'DELETE a,b FROM ' . _DB_PREFIX_ . 'order_slip AS a LEFT JOIN ' . _DB_PREFIX_ . 'order_slip_detail AS b ON a.id_order_slip = b.id_order_slip WHERE id_order="' . $id . '"';
                if (!Db::getInstance()->Execute($q)) {
                    $this->errorlog = $this->l('ERROR');
                }

                $q = 'DELETE FROM ' . _DB_PREFIX_ . 'cart_product WHERE id_cart="' . $thisorder[0]['id_cart'] . '" AND id_shop = "' . $shopid . '"';
                if (!Db::getInstance()->Execute($q)) {
                    $this->errorlog = $this->l('ERROR');
                }

                $q = 'DELETE FROM ' . _DB_PREFIX_ . 'order_history WHERE id_order="' . $id . '"';
                if (!Db::getInstance()->Execute($q)) {
                    $this->errorlog = $this->l('ERROR');
                }

                $q = 'DELETE FROM ' . _DB_PREFIX_ . 'order_detail_tax WHERE id_order_detail IN (SELECT id_order_detail FROM ' . _DB_PREFIX_ . 'order_detail WHERE id_order ="' . $id . '")';
                if (!Db::getInstance()->Execute($q)) {
                    $this->errorlog = $this->l('ERROR');
                }

                $q = 'DELETE FROM ' . _DB_PREFIX_ . 'order_detail WHERE id_order="' . $id . '" AND id_shop = "' . $shopid . '"';
                if (!Db::getInstance()->Execute($q)) {
                    $this->errorlog = $this->l('ERROR');
                }

                $q = 'DELETE FROM ' . _DB_PREFIX_ . 'order_payment WHERE order_reference IN (SELECT reference FROM ' . _DB_PREFIX_ . 'orders WHERE id_order="' . $id . '")';
                if (!Db::getInstance()->Execute($q)) {
                    $this->errorlog = $this->l('ERROR');
                }

                $q = 'DELETE FROM ' . _DB_PREFIX_ . 'orders WHERE id_order="' . $id . '" AND id_shop = "' . $shopid . '"';
                if (!Db::getInstance()->Execute($q)) {
                    $this->errorlog = $this->l('ERROR');
                }

                $q = 'DELETE FROM ' . _DB_PREFIX_ . 'order_carrier WHERE id_order="' . $id . '"';
                if (!Db::getInstance()->Execute($q)) {
                    $this->errorlog = $this->l('ERROR');
                }

                $q = 'DELETE FROM ' . _DB_PREFIX_ . 'order_invoice_tax WHERE id_order_invoice IN (SELECT id_order_invoice FROM ' . _DB_PREFIX_ . 'order_invoice WHERE id_order="' . $id . '")';
                if (!Db::getInstance()->Execute($q)) {
                    $this->errorlog = $this->l('ERROR');
                }

                $q = 'DELETE FROM ' . _DB_PREFIX_ . 'order_invoice WHERE id_order="' . $id . '"';
                if (!Db::getInstance()->Execute($q)) {
                    $this->errorlog = $this->l('ERROR');
                }

                $q = 'DELETE FROM ' . _DB_PREFIX_ . 'order_invoice_payment WHERE id_order="' . $id . '"';
                if (!Db::getInstance()->Execute($q)) {
                    $this->errorlog = $this->l('ERROR');
                }

                $q = 'DELETE FROM ' . _DB_PREFIX_ . 'order_cart_rule WHERE id_order="' . $id . '"';
                if (!Db::getInstance()->Execute($q)) {
                    $this->errorlog = $this->l('ERROR');
                }


            }
    }

    $reqc='SELECT id_order FROM `ps_orders` o WHERE o.id_order <= 226107 AND o.current_state = "10";';
    $T_lignes_reqc = Db::getInstance()->executeS($reqc);
    foreach($T_lignes_reqc as $T_ligne_c)
    {
       // echo 'CMD'.$T_ligne_c['id_order'].'<br />';
    deleteorderbyid($T_ligne_c['id_order']);
    }
    echo 'ok';