<?php

require_once(dirname(__FILE__)."/../../../config/config.inc.php");
require_once(dirname(__FILE__)."/../../../init.php");

$result = "KO";

if(isset($_POST["idCustomer"])){
    Db::getInstance()->execute('UPDATE `' . _DB_PREFIX_ . 'customer` SET `utm_source` = "", `utm_medium` = "", `utm_campaign` = "", `utm_expire` = NULL WHERE `id_customer` = ' . $_POST["idCustomer"]);
    $result = 'OK';
}

echo json_encode($result);