<?php

/*
  Projet : La Bonne Graine
  Fichier : controle.php
  Date de création : 08/09/2020
  Par : Yohan ANDRE
  Date de dernière modification : 08/09/2020
 */

try {
	$connexion = new PDO("mysql:host=localhost;dbname=dev", "dev", "AYsUWk9s4PdWS4KqEeBMCu2D");
	$connexion->exec("SET NAMES 'UTF8'");
} catch (Exception $e) {
	echo 'Erreur : ' . $e->getMessage();
}


?>

<!-- HEADER -->
<!DOCTYPE html>
<html lang="fr">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta http-equiv="X-UA-Compatible" content="ie=edge">

	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" integrity="sha384-JcKb8q3iqJ61gNV9KGb8thSsNjpSL0n8PARn9HuZOnIxN0hoP+VmmDGMN5t9UJ0Z" crossorigin="anonymous">
	<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
	<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js" integrity="sha384-9/reFTGAW83EW2RDu2S0VKaIzap3H66lZH81PoYlFhbGU+6BZp6G7niu735Sk7lN" crossorigin="anonymous"></script>
	<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js" integrity="sha384-B4gt1jrGC7Jh4AgTPSdUtOBvfO8shuf57BaghqFfPlYxofvL8/KUEfYiJOMMV+rV" crossorigin="anonymous"></script>
	<script src="https://kit.fontawesome.com/bfb6502beb.js" crossorigin="anonymous"></script>


	<style type="text/css">
.content-form{
	flex-wrap: inherit;
}
li{
	list-style: none;
	padding: 0;
	margin: auto;
}
.resultProduct{
	flex-flow: column;
}
</style>


	<title>La Bonne Graine</title>
</head>

<body>

	<div class='container mx-auto'>
		<div class="row " width="100%">
			<div class="col d-flex justify-content-center">
				<img class="logo img-responsive" src="https://cdn2.labonnegraine.com/img/logo135.png" alt="La Bonne Graine" width="217" height="213">
			</div>
		</div>

		<form action="controle.php" method="POST" id="formFunnel">

			<div class="row d-flex justify-content-center " style=" margin-top: 100px;">
				<div class="content-form d-flex justify-content-center ">
					<label for="ean13">Code Ean13 ou référence :</label>
					<input type="text" id="ean13" name="ean13" placeholder="Votre code Ean13 ou référence" class="form-control" autofocus>
					<input type="submit" value="VALIDER" class="btn btn-warning button-form" id="createFunnel">
				</div>
			</div>

		</form>

		<?php
		$bloc = '';
		$codeEan = $_POST['ean13'];
		$id_lang = "1";
		if ($codeEan != "") {
			$requeteSelectProduct = "SELECT id_product, reference FROM `ps_product` WHERE `ean13` = '$codeEan' OR `reference` = '$codeEan'";
			$rSelectProduct = $connexion->query($requeteSelectProduct);
			$product = $rSelectProduct->fetch(PDO::FETCH_ASSOC);

			if ($product['id_product'] != false) {
				$id_product = $product['id_product'];

				$requeteSelectLang = "SELECT name FROM `ps_product_lang` WHERE `id_lang` = '$id_lang' AND `id_product` = '$id_product'";
				$rSelectLang = $connexion->query($requeteSelectLang);
				$lang = $rSelectLang->fetch(PDO::FETCH_ASSOC);

				$nameProduct = $lang['name'];
				$bloc .= '<div class="resultProduct  d-flex justify-content-center" >
					<h2>'.$nameProduct.'</h2><h3>'.$product['reference'].'</h3></div>';
			} else {
				$requeteSelectAttr = "SELECT id_product_attribute, id_product FROM `ps_product_attribute` WHERE `ean13` = '$codeEan' OR `reference` = '$codeEan'";
				$rSelectAttr = $connexion->query($requeteSelectAttr);
				$attr = $rSelectAttr->fetch(PDO::FETCH_ASSOC);

				$id_product_attribute = $attr['id_product_attribute'];
				$id_product = $attr['id_product'];
				$requeteSelectCombination = "SELECT id_attribute FROM `ps_product_attribute_combination` WHERE `id_product_attribute` = '$id_product_attribute'";
				$rSelectComb = $connexion->query($requeteSelectCombination);
				$combination = $rSelectComb->fetchAll(PDO::FETCH_ASSOC);

				$requeteSelectLang = "SELECT name FROM `ps_product_lang` WHERE `id_product` = '$id_product' AND `id_lang` = '$id_lang'";
				$rSelectLang = $connexion->query($requeteSelectLang);
				$lang = $rSelectLang->fetch(PDO::FETCH_ASSOC);
				// var_dump($combination);
				$nameProduct = $lang['name'];
				// A REVOIR

				$requeteSelectProductR = "SELECT id_product, reference FROM `ps_product` WHERE `id_product` = '$id_product'";
				$rSelectProductR = $connexion->query($requeteSelectProductR);
				$productR = $rSelectProductR->fetch(PDO::FETCH_ASSOC);

				$bloc .= '<div class="resultProduct  d-flex justify-content-center" >
					<h2>'.$nameProduct.'</h2><h3>'.$productR['reference'].'</h3><div class="decliProduct">';
				foreach ($combination as $key) {
					$id_attr = $key['id_attribute'];
					// var_dump($key);
					$requeteSelectAttrLang = "SELECT name FROM `ps_attribute_lang` WHERE `id_attribute` = '$id_attr' AND `id_lang` = '$id_lang'";
					$rSelectAttrLang = $connexion->query($requeteSelectAttrLang);
					$attrLang = $rSelectAttrLang->fetch(PDO::FETCH_ASSOC);

					$nameDecli = $attrLang['name'];

					$bloc .=
					'
					<ul class="listDecli">
					<li class="nameDecli">'.$nameDecli.'</li>
					</ul>
					';

				}
				$bloc .= '</div></div>';

			}
			echo $bloc;
		}




		?>

	</div>

</body>
