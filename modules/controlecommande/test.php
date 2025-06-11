<?php

// fichier presta
include('../../config/config.inc.php'); 
include('../../init.php');

$state = new OrderState();

foreach(Language::getLanguages() as $language)
{
   $state->name[$language['id_lang']] = "Prepared 2 ";
}
$state->send_email = false;
$state->invoice = false;
$state->logable = true;
$state->color = '#935a00';
$state->shipped = false;
$state->unremovable = false;
$state->delivery = false;
$state->hidden = true;
$state->paid = false;
$state->deleted = false;

$res = $state->add();

echo '<br>$state->id : '.$state->id;