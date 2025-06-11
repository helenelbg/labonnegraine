<?php
if (!defined('STORE_COMMANDER')) { exit; }
// CHECK AUTHENTICATION ON SERVER SIDE #######################################################################

$tok = Tools::getValue('token', '');

$response=makeDefaultCallToOurApi("scc/getShopPSEntitiesIds.php", array('scc-token' => $tok), array( 'url' => _PS_BASE_URL_SSL_));

echo json_encode($response);
?>
