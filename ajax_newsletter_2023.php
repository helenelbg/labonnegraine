<?php

// Ce script post formulaire de https://dev.labonnegraine.com/content/96-newsletter
// Dorian, BERRY-WEB, janvier 2023

require 'config/config.inc.php';
require 'init.php';

if(!isset($_POST['email'])){
	echo 'error';
	exit;
}

$email = $_POST['email'];
$prenom = $_POST['prenom'];
$nom = $_POST['nom'];

$contact = $email;
if($prenom || $nom){
	$contact = trim($prenom . ' '. $nom);
}

//$verif_url = 'https://'.$_SERVER['HTTP_HOST'].'/confirm_newsletter_2023.php?token=';

registerGuest($email, false);
$token = getToken($email);

if(!$token){
	echo 'error';
	exit;
}

/*$verif_url = Context::getContext()->link->getModuleLink(
	'blocknewsletter', 'verification', array(
		'token' => $token,
	)
);*/

$verif_url = 'https://'.$_SERVER['HTTP_HOST'].'/confirm_newsletter_2023.php?token='.$token;

if(!$verif_url){
	echo 'error';
	exit;
}

$res = Mail::Send(
 
    // == REQUIRED FIELDS ARE BELOW ==
 
    /* -- Language id --
    Basic context (if available): $this->context->language->id
    Alternative context: Context::getContext()->language->id
    Default store language: Configuration::get('PS_LANG_DEFAULT') */
 
    Context::getContext()->language->id,
    // --------
 
    /* -- Template name --
    Put your mail template into each language folder of /mails/{lang_iso}/ in .html and .txt format. 
    Ex.: my_mail_template.html and my_mail_template.txt.
    You can make your own /mails/ directory with subdirectories with all your language names (just look into /mails/ directory) anywhere you want to - The path will be specified later. */
 
    'newsletter_verif',
    // --------
 
    /* -- Topic -- */
 
    'Confirmation de votre inscription à la newsletter Les Bons Plan(t)s de La Bonne Graine',
    // --------
 
    /* -- Variables --
    Put null if you don't want to send any. Example of array: */
 
    array(
        '{email}' => $contact,
        '{verif_url}' => $verif_url,
	),
    // --------
 
    /* -- Receiver email address --
    It can be customer email or your email - depending on your needs. 
    Basic context (if available): $this->context->customer->email
    Alternative context: Context::getContext()->language->email
    Your main (BackOffice) email: Configuration::get("PS_SHOP_EMAIL") */
  
    $email,
    // --------
 
    // == OPTIONAL FIELDS ARE BELOW ==
 
    /* -- Receiver name --
    This could be firstname and lastname of a customer.
    You can get customer context and just put ->firstname , ->lastname.
    Or just type any name you want to. */
 
    null,
    // --------
 
    /* -- Sender email --
    Could be your store email: Configuration::get("PS_SHOP_EMAIL")
    but better put the null on this */
 
    null,
    // --------
 
    /* -- Sender name --
    Could be Your firstname and lastname, shopname or both. 
    Get shop name: Configuration::get("PS_SHOP_NAME") */
 
    null,
    // --------
 
    /* -- Attachment -- */
 
    null, // replace with $attach variable if you want to send an attachment,
    // --------
 
    /* -- SMTP mode -- */
 
    null, // just put null here
    // --------
 
    /* -- Mails directory -- 
    Path to /mails/ directory with languages iso codes and with your templates. */
 
    _PS_ROOT_DIR_.'/mails/',
    //_PS_ROOT_DIR_.'/modules/blocknewsletter/newsletter_verif/mails/',
    // --------
 
    /* -- Die after error? --  */
 
    false,
    // --------
 
    /* -- ID Shop -- 
    Basic context (if available):$this->context->shop->id
    Alternative context: Context::getContext()->shop->id
    */
 
    null,
    // --------
 
    /* -- BCC -- 
    Bcc recipient(s) (email address). */
    null,
 
    // --------
 
    /* -- Reply to --
    Email address for setting the Reply-To header. */
 
    null
    // --------
);

if(!$res){
	echo 'error';
	exit;
}

echo 'ok';
	
	
function registerGuest($email, $active = true)
{
	$sql = 'INSERT INTO '._DB_PREFIX_.'newsletter (id_shop, id_shop_group, email, newsletter_date_add, ip_registration_newsletter, http_referer, active)
			VALUES
			('.Context::getContext()->shop->id.',
			1,
			\''.pSQL($email).'\',
			NOW(),
			\''.pSQL(Tools::getRemoteAddr()).'\',"",
			'.(int)$active.'
			)';
	return Db::getInstance()->execute($sql);
}

function getToken($email)
{

	$sql = 'SELECT MD5(CONCAT( `email` , `newsletter_date_add`, \''.pSQL(Configuration::get('NW_SALT')).'\')) as token
			FROM `'._DB_PREFIX_.'newsletter`
			WHERE `active` = 0
			AND `email` = \''.pSQL($email).'\'';

	return Db::getInstance()->getValue($sql);
}
	
?>