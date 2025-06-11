<?php 

require_once(dirname(__FILE__)."/../../../config/config.inc.php");
require_once(dirname(__FILE__)."/../../../init.php");

if(Configuration::get("UTM_CRON_VALUE") != $_GET["token"])
    die("Meric de saisir le bon token");

$users = Db::getInstance()->executeS('SELECT * FROM `' . _DB_PREFIX_ . 'customer` WHERE `utm_expire` IS NOT NULL');

foreach($users as $user){
    if($user["utm_expire"] < date("Y-m-d H:i:s")){
        Db::getInstance()->execute('UPDATE `' . _DB_PREFIX_ . 'customer` SET `utm_source` = "", `utm_medium` = "", `utm_campaign` = "", `utm_expire` = NULL WHERE `id_customer` = ' . $user["id_customer"]);
    }
}