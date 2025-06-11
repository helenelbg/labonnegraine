<?php	
	/* ym_trainz 04/2015
	dessine la séquence du code bar (suite des 0 et de 1 calculée dans : 
									genBitCodebar_ean13($ean13,$addon) (dans fonction_ean13_a2.php)
	http://www.commentcamarche.net/contents/794-php-generation-d-images
	usage : appel html
	<img src="fonction_ean13_draw.php?ean13=...&addon=...(etc)" />
	
	test :
	http://127.0.0.1:8887/ym_bcdi_utiles/EAN13/ym/fonction_ean13_draw.php?ean13=9782081661943
	*/
	include "fonction_ean13_a2.php";
	// ----------------------------
	// récupération des paramètres
	// ----------------------------
	$ean13 = ""; // le code en clair
	$addon = ""; // 2 , 5 ou rien
	$label = "true"; // si on affiche les chiffres en clair dans (sous) le code barre
	$hImg = "160"; // la hauteur en pixel de l'image
	$wElem = "2"; // largueur en pixel de l'élément de base (0 ou 1)
	$url = "img2.gif"; // où on doit sauver l'image crée sur le disque
	$nFont = 4; // choix de la police (1 2 3 4 ou 5) 4 ok
	if(isset($_GET['ean13'])){$ean13=$_GET['ean13'];}
	if(isset($_GET['addon'])){$addon=$_GET['addon'];}
	if(isset($_GET['label'])){$label=$_GET['label'];}
	if(isset($_GET['hImg'])){$hImg=$_GET['hImg'];}
	if(isset($_GET['wElem'])){$wElem=$_GET['wElem'];}	
	if(isset($_GET['url'])){$url=$_GET['url'];}
	if(isset($_GET['nFont'])){$nFont=$_GET['nFont'];}
	// ----------------------------
	if($ean13==""){ die("Pas d'EAN13 transmis dans fonction_ean13_draw.php");}
	// ----------------------------
	$ean13 = CD_ean13($ean13); // recalcul de la clé EAN13
	$codeBits = genBitCodebar_ean13($ean13,$addon);	
	$Larg = strlen($codeBits); // calcul de la largeur en fonction de l'addon
	$image = imagecreate($Larg*$wElem,$hImg); // creation d'une image vide de largeur x hauteur
	// table des couleurs
	$noir = imagecolorallocate($image,0,0,0); 
	$blanc = imagecolorallocate($image,255,255,255);	
	imagefill($image,0,0,$blanc);
	
	// ----------------------------
	$H = $hImg-30; // hauteur - 20 pour laisser la place aux chiffres en bas
	$x=0; // marge blanche à gauche depuis le code
	// ----------------------------
	// les bits 1 seront remplis en noir
	// ----------------------------
	// imagefilledrectangle(entier image,entier xgauchehaut,entier ygauchehaut,entier xdroitebas,entier ydroitebas,entier couleur)
	$ret = 0; // retrait en haut des barres de l'addon
	for($i=0;$i<strlen($codeBits);$i++){
		// parcours du code bit, on dessine les rectangle noir si bit = 1
		// longs trait digit $i =0 à 2 ; 43 à 47 ; 91 à la fin + décalage 10 bits de marge à gauche donc $i ci-dessous
		
		if(($i<13) || (($i>55)&&($i<59)) || ($i>101)){$sup=10;}
		else{$sup=0;} // allonger le trait début milieu fin et addon
		if($i>105) { $ret = 10;} // retrait en haut pour addon		
		//
		if($codeBits[$i]=="1"){ // on dessine
			imagefilledrectangle($image,$x,20 + $ret,
										$x+$wElem-1,$H+$sup,
										$noir);
		}
		$x += $wElem; // pour barrre suivante
	}
	// ----------------------------
	// LABELING (écriture des chiffres)
	// ----------------------------
	if($label != "false"){
		// Table de la position X des chiffres
		$Xlab = array(3,  15,22,29,36,43,50,   61,68,75,82,89,96,   114,124,134,144,154);
		$Ylab = $hImg - 28;
		$ean13 .= $addon; // on fusionne avec l'addon
		for($i=0;$i<strlen($ean13);$i++){
			if($i>12){$Ylab = 12;} // l'addon s'affiche en haut du code barre avec la police choisie $nFont (4 défaut)
			imagechar($image,$nFont,$Xlab[$i]*$wElem,$Ylab,$ean13[$i],$noir); //booléen imagestring(entier image,entier police,entier x,entier y,chaine texte,entier couleur);
		}
	}
	// ----------------------------
	// echo '<img src="'.imagegif($image).'" />';	
	header("Content-Type: image/gif");
	if(trim($url) == ""){
		imagegif($image);
	}
	else{
		$image2 = $image; // on a besoin de deux flux images
		imagegif($image2, $url); // on sauve l'image en même temps
		// imagedestroy($image2); ne pas faire
		imagegif($image); // celle qui sera renvoyé au header		
	}
	imagedestroy($image); // libérer la mémoire
	// ----------------------------
	
?>