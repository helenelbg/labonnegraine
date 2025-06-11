<?php // CHECK AUTHENTICATION ON SERVER SIDE #######################################################################

$usr = Tools::getValue('scc_auth_login', '');
$pwd = Tools::getValue('scc_auth_password', '');

$tok = hash('sha256', $usr . $pwd, false);

$response=makeDefaultCallToOurApi("scc/",array(),array('token' => $tok));

echo json_encode($response);
?>
