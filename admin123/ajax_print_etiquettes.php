<?php
/*
* 2007-2012 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Open Software License (OSL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/osl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2012 PrestaShop SA
*  @version  Release: $Revision: 14002 $
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/
define('_PS_ADMIN_DIR_', getcwd());
define('PS_ADMIN_DIR', _PS_ADMIN_DIR_); // Retro-compatibility

include(PS_ADMIN_DIR.'/../config/config.inc.php');

/* Header can't be included, so cookie must be created here */
$cookie = new Cookie('psAdmin');
if (!$cookie->id_employee)
	Tools::redirectAdmin('login.php');
	
$orders = explode("-", $_GET['orders']);
$db = Db::getInstance();
$adresse_ids = array();
foreach($orders as $id_order)
{
$commande = new Order($id_order);
    $req_ref = "SELECT id_reference FROM "._DB_PREFIX_."carrier WHERE id_carrier='".$commande->id_carrier."';";
    $carrier_ref = $db->ExecuteS($req_ref);

	/*$query_carrier = "SELECT print_etiquette FROM "._DB_PREFIX_."carrier WHERE id_carrier='".$carrier_ref[0]['id_reference']."';";
	$carrier = $db->ExecuteS($query_carrier);

	if($carrier[0]['print_etiquette'] == "1")*/
	//error_log('carrier : '.$carrier[0]['id_reference']);
	if($carrier_ref[0]['id_reference'] == "342" || $carrier_ref[0]['id_reference'] == "143" || $carrier_ref[0]['id_reference'] == "150")
	{
		/////////$adresse_ids[] =$commande->id_address_delivery;
		$adresse_ids[] =$id_order;
	}
}
echo implode('-', $adresse_ids);