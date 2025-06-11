<?php

// Ce script met à jour les préférences utilisateur en terme de newsletter.
// Dorian, BERRY-WEB, mars 2022

require 'config/config.inc.php';
require 'init.php';

if(!isset($_POST['newsletter_bonplan'])){
	exit;
}

if(!isset($_POST['newsletter_dossiercyril'])){
	exit;
}

$newsletter_bonplan = $_POST['newsletter_bonplan'];
$newsletter_dossiercyril = $_POST['newsletter_dossiercyril'];

$customer = Context::getContext()->customer;
$email = Context::getContext()->customer->email;

if($newsletter_bonplan == "1") {
	$customer->newsletter = 1;
	// subscription à la liste Mailjet
	$id_newsletter_bonsplans = Tools::get_id_newsletter_bonsplans();
	Tools::aw_subscribe($email, $id_newsletter_bonsplans, 'addnoforce');
}

if($newsletter_dossiercyril == "1") {
	$customer->optin = 1;
	// subscription à la liste Mailjet
	$id_newsletter_cyril = Tools::get_id_newsletter_cyril();
	Tools::aw_subscribe($email, $id_newsletter_cyril, 'addnoforce');
}

// update en BDD
$customer->save();
echo 'ok';
