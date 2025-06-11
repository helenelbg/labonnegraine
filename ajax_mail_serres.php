<?php 

include('config/config.inc.php');

if ( isset($_POST["sujet"]) && !empty($_POST["sujet"]) )
{

MailCore::Send(
    (int)(Configuration::get('PS_LANG_DEFAULT')),
    "demandeSerres",
    $_POST["sujet"],
    array(
        "{nomSerres}" => $_POST["nomSerres"],
        "{prenomSerres}" => $_POST["prenomSerres"],
        "{telSerres}" => $_POST["telSerres"],
        "{mailSerres}" => $_POST["mailSerres"],
        "{prefSerres}" => $_POST["prefSerres"] 
    ),
    array(
        "stephane.dipalma@gmail.com",
        "anaelle.mandret@doviris.com"
    )
);

echo json_encode("OK");
}