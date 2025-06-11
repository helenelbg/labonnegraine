<?php
	// ym_trainz 04/2015
	/* fonctions pour générer les codes barres EAN 13 + addon 2
	Produit une séquence de 0 et de 1 , 
	les 1 sont les barres noires, 
	les 0 sont les blanches ne seront pas dessinées puisqu'égales à la couleur de fond
	voir ces liens utiles ci-desous :
	
	http://barcode-coder.com/fr/
	http://barcode-coder.com/en/ean-13-specification-102.html
	http://grandzebu.net/informatique/codbar/ean13.htm
	http://fr.m.wikipedia.org/wiki/Code-barres_EAN	
	*/
	// ------------------------------
	function CD_ean13($code){ 
		/* calcul de la clé EAN13 (13ème chiffre)
		1) prend les 12 1ers chiffres et ignorant la clé (13ème chiffre)
		2) renvoie les 13 chiffres avec la clé calculée 
		*/
		$code = str_replace("-","",$code); // on supprime les tirets		
		if((!ctype_digit($code)) || (strlen($code)<12)){ // tester la longueur ou si se ne sont que des chiffres
			return "Erreur EAN13(".$code.")";
		}
		$c = str_split($code); // on passe en tableau
		$sum = (($c[1] + $c[3] + $c[5] + $c[7] + $c[9] + $c[11]) * 3) + ($c[0] + $c[2] + $c[4] + $c[6] + $c[8] + $c[10]);
		$sum = (10 - ($sum % 10)) % 10;
		return substr($code,0,12).$sum; // on renvoie les 12 premiers chiffres et la clé recalculée
	}
	function genBitCodebar_ean13($ean13,$addon=""){
		/*
		générer le code bar on une séquence de bits 0/1
		$addon 2 ou 5 ou rien		
		*/
		// -----------------------------------------
		// initialisation des DONNEES
		// -----------------------------------------
		// table    A            B       et  C (de 0 à 9)
		$encoding = array(
			array('0001101', '0100111', '1110010'),
			array('0011001', '0110011', '1100110'),
			array('0010011', '0011011', '1101100'),
			array('0111101', '0100001', '1000010'),
			array('0100011', '0011101', '1011100'),
			array('0110001', '0111001', '1001110'),
			array('0101111', '0000101', '1010000'),
			array('0111011', '0010001', '1000100'),
			array('0110111', '0001001', '1001000'),
			array('0001011', '0010111', '1110100')
		);
		// table (selon premier digit) servant à encoder la partie #1 (d2 à d7) du code : 0 donne table A et 1 donne table B 
		$first = array('000000','001011','001101','001110','010011','011001','011100','010101','010110','011010'); // de 0 à 9
		// séparateurs
		$start_stop = '101'; // debut et fin EAN13
		$center = '01010'; // entre partie #1 (d2 à d7) et partie #2 (d8 à d13)		
		$sequence = $first[$ean13[0]]; // on extrait la séquence en fonction du 1er digit (qui n'est pas codé mais déduit !)
									// si d1 = 3 (par exemple) on aura 001110 soit table AABBBA donc A pour encoder digit 2 , A pour d3, B pour d4...
		// -----------------------
		$result = "0000000000"; // 0000000000 pour laisser une marge à gauche
		$result.=$start_stop; // EAN13 aura une longueur de 102 bits (sans compter l'addon éventuel ni les marges)
		// -----------------------		
		// partie #1
		for($i=1; $i<7; $i++){
			$result .= $encoding[intval($ean13[$i])][intval($sequence[$i-1])]; // table A ou B selon la séquence
		}
		$result .= $center;
		// partie #2 , tout dans la table C donc [2]
		for($i=7; $i<13; $i++){
			$result .=$encoding[intval($ean13[$i])][2];
		}
		$result .= $start_stop; // fin du code EAN13
		// -----------------------------------------
		// ADDON
		// -----------------------------------------
		$la = strlen($addon); // longueur de l'addon 2 ou 5 ou rien
		if(($la!=2) && ($la !=5)) {return $result."0000000000";} // pas d'addon retour EAN13 simple avec marge à droite
		$start_addon = "0000000"."1011"; // avant la séquence de l'addon on laisse un blanc 0000000 puis le start_addon
		$endCharAddon = "01"; // après chaque caractère, sauf le dernier
		
		if($la == 2){
			// ADDON 2
			$table = array('00','01','10','11'); // la table qui renvoie au codage $encoding
			$chkSum = intval($addon) % 4; // somme de controle de l'addon (reste de la division par 4)
		}
		else {
			// ADDON 5
			$table = array('11000','10100','10010','10001','01100','00110','00011','01010','01001','00101');
			$c = str_split($addon); // on repasse en tableau
			$chkSum = (($c[1] + $c[3]) * 9) + (($c[0] + $c[2] + $c[4])*3); // 9 fois les chiffres de rang paires + 3 fois les impairs
			$chkSum = $chkSum % 10;
		}
		// Ecriture de l'addon
		$result .= $start_addon;
		$sequence = $table[intval($chkSum)]; // // on extrait la séquence en fonction du check sum
		for($i=0; $i<$la; $i++){
			$result .= $encoding[intval($addon[$i])][intval($sequence[$i])]; // table A ou B selon la séquence
			if($i<($la-1)){
				$result .= $endCharAddon; // sauf le dernier caractère
			}
		}
		// -----------------------------------------
		return $result."0000000000"; // retour avec marge à droite
	}	
?>