<?php

class AdminProductsController extends AdminProductsControllerCore
{
	public function ajaxProcessAddAttachment()
	{
		// Ajustement pour lier un picto à un attachment.
		// Par Dorian, BERRY-WEB, mars 2023.
		$uniqidPicto = '';
		if (isset($_FILES['attachment_picto']))
		{
			$array = explode('.', $_FILES['attachment_picto']['name']);
			$extension = end($array);
			do $uniqidPicto = sha1(microtime()).'.'.$extension;
			while (file_exists(_PS_UPLOAD_DIR_.$uniqidPicto));
			if (!copy($_FILES['attachment_picto']['tmp_name'], _PS_UPLOAD_DIR_.$uniqidPicto))
				$_FILES['attachment_picto']['error'][] = $this->l('File copy failed');
			@unlink($_FILES['attachment_picto']['tmp_name']);
		}
		
		if (isset($_FILES['attachment_file']))
		{
			if ((int)$_FILES['attachment_file']['error'] === 1)
			{
				$_FILES['attachment_file']['error'] = array();

				$max_upload = (int)ini_get('upload_max_filesize');
				$max_post = (int)ini_get('post_max_size');
				$upload_mb = min($max_upload, $max_post);
				$_FILES['attachment_file']['error'][] = sprintf(
					$this->l('The file %1$s exceeds the size allowed by the server. The limit is set to %2$d MB.'),
					'<b>'.$_FILES['attachment_file']['name'].'</b> ',
					'<b>'.$upload_mb.'</b>'
				);
			}

			$_FILES['attachment_file']['error'] = array();

			$is_attachment_name_valid = false;
			$attachment_names = Tools::getValue('attachment_name');
			$attachment_descriptions = Tools::getValue('attachment_description');

			if (!isset($attachment_names) || !$attachment_names)
				$attachment_names = array();

			if (!isset($attachment_descriptions) || !$attachment_descriptions)
				$attachment_descriptions = array();

			foreach ($attachment_names as $lang => $name)
			{
				$language = Language::getLanguage((int)$lang);

				if (Tools::strlen($name) > 0)
					$is_attachment_name_valid = true;

				if (!Validate::isGenericName($name))
					$_FILES['attachment_file']['error'][] = sprintf(Tools::displayError('Invalid name for %s language'), $language['name']);
				elseif (Tools::strlen($name) > 32)
					$_FILES['attachment_file']['error'][] = sprintf(Tools::displayError('The name for %1s language is too long (%2d chars max).'), $language['name'], 32);
			}

			foreach ($attachment_descriptions as $lang => $description)
			{
				$language = Language::getLanguage((int)$lang);

				if (!Validate::isCleanHtml($description))
					$_FILES['attachment_file']['error'][] = sprintf(Tools::displayError('Invalid description for %s language'), $language['name']);
			}

			if (!$is_attachment_name_valid)
				$_FILES['attachment_file']['error'][] = Tools::displayError('An attachment name is required.');

			if (empty($_FILES['attachment_file']['error']))
			{
				if (is_uploaded_file($_FILES['attachment_file']['tmp_name']))
				{
					if ($_FILES['attachment_file']['size'] > (Configuration::get('PS_ATTACHMENT_MAXIMUM_SIZE') * 1024 * 1024))
						$_FILES['attachment_file']['error'][] = sprintf(
							$this->l('The file is too large. Maximum size allowed is: %1$d kB. The file you\'re trying to upload is: %2$d kB.'),
							(Configuration::get('PS_ATTACHMENT_MAXIMUM_SIZE') * 1024),
							number_format(($_FILES['attachment_file']['size'] / 1024), 2, '.', '')
						);
					else
					{
						do $uniqid = sha1(microtime());
						while (file_exists(_PS_DOWNLOAD_DIR_.$uniqid));
						if (!copy($_FILES['attachment_file']['tmp_name'], _PS_DOWNLOAD_DIR_.$uniqid))
							$_FILES['attachment_file']['error'][] = $this->l('File copy failed');
						@unlink($_FILES['attachment_file']['tmp_name']);
					}
				}
				else
					$_FILES['attachment_file']['error'][] = Tools::displayError('The file is missing.');

				if (empty($_FILES['attachment_file']['error']) && isset($uniqid))
				{
					$attachment = new Attachment();

					foreach ($attachment_names as $lang => $name)
						$attachment->name[(int)$lang] = $name;

					foreach ($attachment_descriptions as $lang => $description)
						$attachment->description[(int)$lang] = $description;

					$attachment->file = $uniqid;
					if($uniqidPicto){
						$attachment->picto = $uniqidPicto;
					}
					$attachment->mime = $_FILES['attachment_file']['type'];
					$attachment->file_name = $_FILES['attachment_file']['name'];

					if (empty($attachment->mime) || Tools::strlen($attachment->mime) > 128)
						$_FILES['attachment_file']['error'][] = Tools::displayError('Invalid file extension');
					if (!Validate::isGenericName($attachment->file_name))
						$_FILES['attachment_file']['error'][] = Tools::displayError('Invalid file name');
					if (Tools::strlen($attachment->file_name) > 128)
						$_FILES['attachment_file']['error'][] = Tools::displayError('The file name is too long.');
					if (empty($this->errors))
					{
						$res = $attachment->add();
						if (!$res)
							$_FILES['attachment_file']['error'][] = Tools::displayError('This attachment was unable to be loaded into the database.');
						else
						{
							$_FILES['attachment_file']['id_attachment'] = $attachment->id;
							$_FILES['attachment_file']['filename'] = $attachment->name[$this->context->employee->id_lang];
							$id_product = (int)Tools::getValue($this->identifier);
							$res = $attachment->attachProduct($id_product);
							if (!$res)
								$_FILES['attachment_file']['error'][] = Tools::displayError('We were unable to associate this attachment to a product.');
						}
					}
					else
						$_FILES['attachment_file']['error'][] = Tools::displayError('Invalid file');
				}
			}

			die(Tools::jsonEncode($_FILES));
		}
	}
	
	public function initFormStats($obj)
	{
		$data = $this->createTemplate($this->tpl_form);
		if ((bool)$obj->id)
		{
			/*echo $obj->id;
			print_r($obj->reference);*/
			$product['reference'] = $obj->reference;
			$product['id_product'] = $obj->id;
			$product['name'] = $obj->name[2];
			$this->_html .=	'<form name="formInventaire" action="index.php?controller=AdminStats&token='.Tools::getAdminTokenLite('AdminStats').'&module=statsstocksinventaire" method="post">';
			$this->_html .= '<input type="hidden" value="'.Tools::getAdminTokenLite('AdminStats').'" id="token">';
			$this->_html .= '<input type="hidden" value="'.$product['id_product'].'" name="return" id="return">';

			$this->_html .= '<table class="table" width="100%" cellspacing="0" border="0">
         		<thead>
						<tr>
						 <th style="text-align:left">Référence</th>
						 <th style="text-align:left">Nom</th>
						 <th style="text-align:left">Date Dernier Inv.</th>
						 <th></th>
						 <th style="display:none;">Stock Théor.</th>
						 <th style="text-align:left">Stock Site</th>
						 <th style="text-align:left">Alerte (en g.)</th>
					 </tr>';
         	/*echo '<pre>';
  			print_r($obj);
  			echo '</pre>';*/
			$id_stock_presta_p = StockAvailable::getStockAvailableIdByProductId($product['id_product']);
			$stockAvailableProduct = new StockAvailable($id_stock_presta_p);
			$ale = Db::getInstance()->ExecuteS('SELECT valeur FROM psme_alerte WHERE id_product = "' . $product['id_product'] . '" ORDER BY id DESC LIMIT 0,1;');
			$this->_html .= '<tr><td>' . $product['reference'] . '</td><td colspan="4"><font style="color: #000000">' . $product['name'] . '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <a style="color:blue" href="javascript:afficher_lot(' . $product['id_product'] . ')">Informations lots</a></font></td><td>'.$stockAvailableProduct->quantity.'&nbsp;<input type="text" name="' . $product['id_product'] . '#alerte" value="' . @$ale[0]['valeur'] . '" style="width:50px;display: inline;"/></td></tr>';
			$this->_html .= '<tr id="info_lots_product_' . $product['id_product'] . '" class="detail_lot" style="display:none;"><td colspan="7">';
			$refs_fournisseur_inventaires = Db::getInstance()->ExecuteS('SELECT * FROM psme_inventaire_lots WHERE id_product = "' . $product['id_product'] . '" ORDER BY date_approvisionnement DESC;');
			$vrai = false;
			foreach ($refs_fournisseur_inventaires as $ref_fourn_inv)
			{
				if(!$vrai)
				{
					$this->_html .= '<table id="lot_num_' . $ref_fourn_inv['id_inventaire_lots'] . '" class="lot_inv table_detail" >
					<thead>
					<th>Fournisseur</th>
					<th>N&deg; lot origine</th>
					<th>Date appro</th>
					<th width="100px">Graines / Grammes</th>
					<th>QuantitÃ© appro</th>
					<th>N&deg; lot LBG</th>
					<th>Origine test Frns / LBG</th>
					<th>Date fin du test de germinaton</th>
					<th>Pourcentage germination</th>
					<th colspan="3">Commentaires</th>
					<th></th>
					</thead>
					<tbody>';
					$vrai = true;
				}
				/*$result_origine_test_lot = Db::getInstance()->ExecuteS('SELECT commentaire, origine_test, date_etape_1,resultat_etape_1,date_etape_2,resultat_etape_2,date_etape_3,resultat_etape_3 FROM AW_test_lots WHERE id_lot = "' . $ref_fourn_inv['id_inventaire_lots'] . '" ORDER BY id DESC LIMIT 1');
				if($result_origine_test_lot[0]['date_etape_3']!=0)
					$date_fin_test=$result_origine_test_lot[0]['date_etape_3'];
				elseif($result_origine_test_lot[0]['date_etape_2']!=0)
					$date_fin_test=$result_origine_test_lot[0]['date_etape_2'];
				elseif($result_origine_test_lot[0]['date_etape_1']!=0)
					$date_fin_test=$result_origine_test_lot[0]['date_etape_1'];
				else
					$date_fin_test="";
				if($result_origine_test_lot[0]['resultat_etape_3']!=0)
					$resultat_test=$result_origine_test_lot[0]['resultat_etape_3'];
				elseif($result_origine_test_lot[0]['resultat_etape_2']!=0)
					$resultat_test=$result_origine_test_lot[0]['resultat_etape_2'];
				elseif($result_origine_test_lot[0]['resultat_etape_1']!=0)
					$resultat_test=$result_origine_test_lot[0]['resultat_etape_1'];
				else
					$resultat_test="0";


				if($result_origine_test_lot[0]['origine_test']!="")$origine_test_lot=$result_origine_test_lot[0]['origine_test'];
				else $origine_test_lot="";
				$commentaire_test_lot= $result_origine_test_lot[0]['commentaire'];

				if(count($result_origine_test_lot)>0)
					$defaut_origine_test="LBG";
				else  $defaut_origine_test="Frns";
				$date_appro = $ref_fourn_inv['date_approvisionnement'];

				$array_date_appro = explode('-', $date_appro);

				$date_appro = @$array_date_appro[2] . "/" . @$array_date_appro[1] . "/" . @$array_date_appro[0];



				$date_germ = $ref_fourn_inv['date_test_germination'];

				$array_date_germ = explode('-', $date_germ);

				$date_germ = @$array_date_germ[2] . "/" . @$array_date_germ[1] . "/" . @$array_date_germ[0];

				$qty_lot = $ref_fourn_inv['quantite'];

				$graine_gramme = $ref_fourn_inv['graine_gramme'];*/

				$result_origine_test_lot = Db::getInstance()->ExecuteS('SELECT commentaire, origine_test, date_etape_1,resultat_etape_1,date_etape_2,resultat_etape_2,date_etape_3,resultat_etape_3 FROM AW_test_lots WHERE id_lot = "' . $ref_fourn_inv['id_inventaire_lots'] . '" ORDER BY id DESC LIMIT 1');
						if($result_origine_test_lot[0]['date_etape_3']!=0)
										$date_fin_test=substr($result_origine_test_lot[0]['date_etape_3'], 8, 2).'/'.substr($result_origine_test_lot[0]['date_etape_3'], 5, 2).'/'.substr($result_origine_test_lot[0]['date_etape_3'], 0, 4);
						elseif($result_origine_test_lot[0]['date_etape_2']!=0)
										$date_fin_test=substr($result_origine_test_lot[0]['date_etape_2'], 8, 2).'/'.substr($result_origine_test_lot[0]['date_etape_2'], 5, 2).'/'.substr($result_origine_test_lot[0]['date_etape_2'], 0, 4);
						elseif($result_origine_test_lot[0]['date_etape_1']!=0)
										 $date_fin_test=substr($result_origine_test_lot[0]['date_etape_1'], 8, 2).'/'.substr($result_origine_test_lot[0]['date_etape_1'], 5, 2).'/'.substr($result_origine_test_lot[0]['date_etape_1'], 0, 4);
						else
										$date_fin_test="";
						if($result_origine_test_lot[0]['resultat_etape_3']!=0)
										$resultat_test=$result_origine_test_lot[0]['resultat_etape_3'];
						elseif($result_origine_test_lot[0]['resultat_etape_2']!=0)
										$resultat_test=$result_origine_test_lot[0]['resultat_etape_2'];
						elseif($result_origine_test_lot[0]['resultat_etape_1']!=0)
										 $resultat_test=$result_origine_test_lot[0]['resultat_etape_1'];
						else
										$resultat_test="0";

				if($result_origine_test_lot[0]['origine_test']!="")$origine_test_lot=$result_origine_test_lot[0]['origine_test'];
				else $origine_test_lot="";
				$commentaire_test_lot= $result_origine_test_lot[0]['commentaire'];

				$date_germ = $ref_fourn_inv['date_test_germination'];
				$array_date_germ = explode('-', $date_germ);
				$date_germ = @$array_date_germ[2] . "/" . @$array_date_germ[1] . "/" . @$array_date_germ[0];

				if(count($result_origine_test_lot)==0)
				{
						$resultat_test=$ref_fourn_inv['percent_germination'];
						$date_fin_test= $date_germ;
						$commentaire_test_lot=$ref_fourn_inv['commentaire'];
				}
				if(count($result_origine_test_lot)>0)
						$origine_test_lot=$result_origine_test_lot[0]["origine_test"];
				else  $origine_test_lot="Frns";

			$date_appro = $ref_fourn_inv['date_approvisionnement'];
			$array_date_appro = explode('-', $date_appro);
			$date_appro = @$array_date_appro[2] . "/" . @$array_date_appro[1] . "/" . @$array_date_appro[0];


			$qty_lot = $ref_fourn_inv['quantite'];
			$graine_gramme = $ref_fourn_inv['graine_gramme'];



				$this->_html .='
				<tr>
				<td id="ref_fourn_lot_' . $ref_fourn_inv['id_inventaire_lots'] . '">' . $ref_fourn_inv['fournisseur'] . '</td>
				<td align="left" id="num_lot_org_' . $ref_fourn_inv['id_inventaire_lots'] . '">' . $ref_fourn_inv['numero_lot_origine'] . '</td>
				<td align="left" id="date_appro_lot_' . $ref_fourn_inv['id_inventaire_lots'] . '">' . $date_appro . '</td>
				<td align="left" id="graine_gramme_' . $ref_fourn_inv['id_inventaire_lots'] . '">' . $graine_gramme . '</td>
				<td align="left" id="quantite_lot_' . $ref_fourn_inv['id_inventaire_lots'] . '">' . $qty_lot . '</td>
				<td id="num_lot_LBG_' . $ref_fourn_inv['id_inventaire_lots'] . '">' . $ref_fourn_inv['numero_lot_LBG'] . '</td>
				<td id="remplir">'.$origine_test_lot.'</td>
				<td align="left" id="date_germ_lot_' . $ref_fourn_inv['id_inventaire_lots'] . '">' . $date_fin_test . '</td>
				<td align="left" id="germ_lot_' . $ref_fourn_inv['id_inventaire_lots'] . '">' . $resultat_test . '%</td>
				<td colspan="3" align="left" id="commentaire_lot_' . $ref_fourn_inv['id_inventaire_lots'] . '">' . $commentaire_test_lot . '</td>';
				$test_lots = Db::getInstance()->ExecuteS('SELECT * FROM AW_test_lots WHERE id_lot = "' . $ref_fourn_inv['id_inventaire_lots'] . '"');

				$this->_html .='
				<td>
				<div class="conteneur_lb" id="conteneur_lb_'. $ref_fourn_inv['id_inventaire_lots'] .'">
				<div class="lightbox" style="text-align:center">
				<h2 class="margin0" style="text-align: center;padding: 10px;border-bottom: 1px solid black;">Liste des tests - '.$product['name'].' - Lot n&deg;'.$ref_fourn_inv['numero_lot_LBG'].'</h2>
				<table width="900px" class="center">
				<thead>
				<tr>
				<th colspan="1" style="width:20px;"></th>
				<th colspan="1" style="width:90px;">Date fin<br />Germination</th>
				<th colspan="1" style="width:90px;">%<br />Germination</th>
				<th colspan="5" style="width:700px;">Commentaire</th>
				</tr>
				</thead>
				<tbody>';


				foreach ($test_lots as $test_lot)
				{
					if (!empty($test_lot['resultat_etape_3']))
					{
						$pourc_germ = $test_lot['resultat_etape_3'];
					}
					elseif (!empty($test_lot['resultat_etape_2']))
					{
						$pourc_germ = $test_lot['resultat_etape_2'];
					}
					else
					{
						$pourc_germ = $test_lot['resultat_etape_1'];
					}
					if($test_lot['date_etape_3']!=0)
						$date_fin_test=$test_lot['date_etape_3'];
					elseif($test_lot['date_etape_2']!=0)
						$date_fin_test=$test_lot['date_etape_2'];
					elseif($test_lot['date_etape_1']!=0)
						$date_fin_test=$test_lot['date_etape_1'];
					else
						$date_fin_test="";


					$this->_html .='
					<tr class="border_top_fonce click_display">
					<input type="hidden" name="id_lot" id="id_lot_'.$test_lot['id'].'" value="'. $ref_fourn_inv['id_inventaire_lots'] .'">
					<th class="icon_plus_moins plus" id="icon_plus_moins_'.$test_lot['id'].'" id-attr="'.$test_lot['id'].'"><span class="icon-plus"></span></th>
					<th>'.$date_fin_test.'</th>
					<th class="center">'.$pourc_germ.'</th>
					<th colspan="5"><textarea id="commentaire_'.$test_lot['id'].'" type="text">'.$test_lot['commentaire'].'</textarea></th>
					</tr>
					<tr class="display_'.$test_lot['id'].' display_none  border_claire">
					<th></th>
					<th></th>
					<th colspan="2">Etape 1</th>
					<th colspan="2">Etape 2</th>
					<th colspan="2">Etape 3</th>
					</tr>
					<tr class="display_'.$test_lot['id'].' display_none border_claire">
					<th></th>
					<th>Date d&eacute;but test</th>
					<th>Date</th>
					<th>Resultat</th>
					<th>Date</th>
					<th>Resultat</th>
					<th>Date</th>
					<th>Resultat</th>
					<th>Origine test</th>
					</tr>
					<tr class="display_'.$test_lot['id'].' display_none border_claire">
					<td></td>
					<th><input id="date_debut_semis_'.$test_lot['id'].'" class="datepickerLot" type="text" value="'.$test_lot['date_debut_semis'].'"></th>
					<td><input id="date_etape_1_'.$test_lot['id'].'" class="datepickerLot" type="text" value="'.$test_lot['date_etape_1'].'"></td>
					<td><input id="resultat_etape_1_'.$test_lot['id'].'" type="text" value="'.$test_lot['resultat_etape_1'].'"></td>
					<td><input id="date_etape_2_'.$test_lot['id'].'" class="datepickerLot" type="text" value="'.$test_lot['date_etape_2'].'"></td>
					<td><input id="resultat_etape_2_'.$test_lot['id'].'" type="text" value="'.$test_lot['resultat_etape_2'].'"></td>
					<td><input id="date_etape_3_'.$test_lot['id'].'" class="datepickerLot" type="text" value="'.$test_lot['date_etape_3'].'"></td>
					<td>
					<input id="resultat_etape_3_'.$test_lot['id'].'" type="text" value="'.$test_lot['resultat_etape_3'].'">
					</td>
					<td>
					<input id="origine_test_'.$test_lot['id'].'" type="text" value="'.$test_lot['origine_test'].'">
					</td>
					</tr>
					<tr class="display_'.$test_lot['id'].' display_none border_claire">
					<td colspan="8"><button class="envoi_modif" id="'.$test_lot['id'].'"><i class="icon-pencil"></i> Modifier</button></td>
					</tr>
					<tr class="display_'.$test_lot['id'].' display_none border_claire">
					<td colspan="8" style="background-color:grey;line-height: 2px;">&nbsp;</td>
					</tr>';
				}
				$this->_html .='
				</tbody>
				</table>
				<h2 class="margin0 titre_click_display" id="titre_click_display_'.$ref_fourn_inv['id_inventaire_lots'].'" style="padding:10px;padding-top:20px; cursor: pointer;">Cr&eacute;er un nouveau test de germination</h2>
				<table width="100%" class="center table_display" style="display: none;"  id="ligne_ajout_test_'. $ref_fourn_inv['id_inventaire_lots'] .'">
				<tbody>
				<tr class="border_claire">
				<th></th>
				<th></th>
				<th colspan="2">Etape 1</th>
				<th colspan="2">Etape 2</th>
				<th colspan="2">Etape 3</th>
				<th></th>
				</tr>
				<tr class="border_claire">
				<th></th>
				<th>Date d&eacute;but test</th>
				<th colspan="0.5">Date</th>
				<th>Resultat</th>
				<th>Date</th>
				<th>Resultat</th>
				<th>Date</th>
				<th>Resultat</th>
				<th>Origine test</th>
				</tr>
				<tr class="border_claire">
				<th></th>
				<th><input placeholder="Date d&eacute;but de semis" type="text" class="datepickerLot date_debut_test" name="date_debut_test" /></th>
				<td><input type="hidden" name="id_lot" id="id_lot" value="'. $ref_fourn_inv['id_inventaire_lots'] .'"><input type="text" class="datepickerLot date_etape_1" name="date_etape_1" /></td>
				<td><input type="text" name="resultat_etape_1" id="resultat_etape_1"/></td>
				<td><input type="text" class="datepickerLot date_etape_2" name="date_etape_2" ></td>
				<td><input type="text" name="resultat_etape_2" id="resultat_etape_2"/></td>
				<td><input type="text" class="datepickerLot date_etape_3" name="date_etape_3" /></td>
				<td><input type="text" name="resultat_etape_3" id="resultat_etape_3"/></td>

				// <td><input type="text" name="origine_test" id="origine_test" value="'.$defaut_origine_test.'"/></td>
				</tr>
				<tr>
				<th colspan="9"><textarea placeholder="Mettre un commentaire" name="commentaire_test" id="commentaire_test"></textarea></th>
				</tr>
				<tr>
				<td colspan="9"><button class="ajout_test" id="ajout_test_'. $ref_fourn_inv['id_inventaire_lots'] .'"><i class="process-icon-new" style="font-size:14px; width: auto; height: auto; display:inline-block; color: initial;"></i> Ajouter le test</button></td>
				</tr>
				</tbody>
				</table>
				</div>
				</div>
				</td>
				<td style="width:110px"><button type="button" onclick="javascript:voir_test(' . $ref_fourn_inv['id_inventaire_lots'] . ')"><i class="icon-search-plus"></i></button> <button type="button" onclick="javascript:supprimer_lot(' . $ref_fourn_inv['id_inventaire_lots'] . ')"><i class="icon-trash"></i></button> <button type="button" onclick="javascript:modifier_lot(' . $ref_fourn_inv['id_inventaire_lots'] . ', ' . $product['id_product'] . ')"><i class="icon-pencil"></i></button></td>

				</tr>';
   			}
			$date_courante = date('Y-m-d');
			$this->_html .='
			<tr>
			<td align="left"><input placeholder="Fournisseur" type="text" id="fournis' . $product['id_product'] . '" value="" /></td>
			<td align="left"><input placeholder="N&deg; lot origine" type="text" id="lot_org' . $product['id_product'] . '" value="" /></td>
			<td align="left"><input placeholder="Date appro" type="text" class="datepickerLot" id="date_appro' . $product['id_product'] . '" value="' . $date_courante . '" /></td>
			<td>
			<input type="radio" name="graine_gramme' . $product['id_product'] . '" id="graine' . $product['id_product'] . '" value="graine" />
			<label for="graine' . $product['id_product'] . '">Graines</label><br />
			<input type="radio" name="graine_gramme' . $product['id_product'] . '" id="gramme' . $product['id_product'] . '" value="gramme" checked="checked" />
			<label for="gramme' . $product['id_product'] . '">Grammes</label>
			</td>
			<td align="left"><input placeholder="Quantité appro" type="text" id="quantite' . $product['id_product'] . '" value="" /></td>
			<td align="left"><input placeholder="N&deg; lot LBG" type="text" id="lot_LBG' . $product['id_product'] . '" value="" /></td>
			<td></td>
			<td align="left"><input placeholder="" type="text" class="datepickerLot" id="date_germ' . $product['id_product'] . '" value="' . $date_courante . '" /></td>
			<td align="left"><input placeholder="Pourcentage germination" type="text" id="pourcent_germ' . $product['id_product'] . '" value="" /></td>
			<td colspan="3" align="left"><textarea placeholder="Mettre un commentaire" rows="3" cols="80" id="comm' . $product['id_product'] . '"></textarea></td>
			<td><input type="button" id="button_' . $product['id_product'] . '" value="Ajouter ce lot" onclick="submit_form_lot(' . $product['id_product'] . ')"/>&nbsp;&nbsp;&nbsp;&nbsp;<input type="button" id="button_reset_' . $product['id_product'] . '" value="Remettre &agrave; z&eacute;ro" onclick="reset_lot_add(' . $product['id_product'] . ')"/></td>
			</tr>
			</tbody>
			</table>';
			$poids_theorique = 0;
			$rangee_attrib = Db::getInstance()->ExecuteS('SELECT * FROM psme_product_attribute WHERE id_product = "' . $product['id_product'] . '";');
			$nb_graine = 0;
			foreach ($rangee_attrib AS $attrib)
			{
				$aux_dec = array();
				$qt_commandee = 0;
				$stock_theorique = 0;
				$rangee_comb = Db::getInstance()->ExecuteS('SELECT * FROM psme_product_attribute_combination WHERE id_product_attribute = "' . $attrib['id_product_attribute'] . '";');
				foreach ($rangee_comb AS $comb)
				{
			//echo 'SELECT * FROM psme_attribute_lang WHERE id_attribute = "'.$comb['id_attribute'].'" AND id_lang = 2;';
					$dec = Db::getInstance()->ExecuteS('SELECT name FROM psme_attribute_lang WHERE id_attribute = "' . $comb['id_attribute'] . '" AND id_lang = 2 LIMIT 0,1;');
					$aux_dec[] = $dec[0]['name'];
				}
				sort($aux_dec);
				$libelle_dec = implode(' - ', $aux_dec);

				$inv = Db::getInstance()->ExecuteS('SELECT * FROM psme_inventaire WHERE id_product_attribute = "' . $attrib['id_product_attribute'] . '" AND id_product = "' . $product['id_product'] . '" ORDER BY date DESC LIMIT 0,1;');
				if (!empty($inv[0]['date']))
				{
					$jour_inv = substr($inv[0]['date'], 6, 2);
					$mois_inv = substr($inv[0]['date'], 4, 2);
					$annee_inv = substr($inv[0]['date'], 0, 4);
					$heure_inv = substr($inv[0]['date'], 8, 2);
					$minutes_inv = substr($inv[0]['date'], 10, 2);
			//$der_inv = ' <font color="#808080">('.$jour_inv.'/'.$mois_inv.'/'.$annee_inv.' '.$heure_inv.':'.$minutes_inv.' = '.$inv[0]['valeur'].')</font>';
					//$der_inv = '<td>' . $jour_inv . '/' . $mois_inv . '/' . $annee_inv . '</td><td>' . $inv[0]['valeur'] . '</td>';
					$der_inv = '<td>' . $jour_inv . '/' . $mois_inv . '/' . $annee_inv . '</td><td></td>';
					$str_inv =  $jour_inv . '/' . $mois_inv . '/' . $annee_inv . ';'.$inv[0]['valeur'];
				}
				else
				{
					$der_inv = '<td>&nbsp;</td><td>&nbsp;</td>';
				}
				if ($der_inv != '<td>&nbsp;</td><td>&nbsp;</td>')
				{
			// Somme des quantit�s command�es depuis de dernier inventaire
			//$commandes = Db::getInstance()->ExecuteS('SELECT * FROM psme_order_detail pod, psme_orders po WHERE po.id_order = pod.id_order AND pod.product_id = "'.$product['id_product'].'" AND pod.product_attribute_id = "'.$attrib['id_product_attribute'].'" AND po.date_add > "'.$annee_inv.'-'.$mois_inv.'-'.$jour_inv.' '.$heure_inv.':'.$minutes_inv.'";');
	/*        $commandes = Db::getInstance()->ExecuteS('SELECT * FROM psme_order_detail pod, psme_orders po WHERE po.id_order = pod.id_order AND pod.product_id = "' . $product['id_product'] . '" AND pod.product_attribute_id = "' . $attrib['id_product_attribute'] . '" AND po.date_add > "' . $annee_inv . '-' . $mois_inv . '-' . $jour_inv . ' ' . $heure_inv . ':' . $minutes_inv . '" AND (SELECT logable FROM psme_order_state WHERE id_order_state LIKE (SELECT id_order_state FROM psme_order_history WHERE id_order = po.id_order ORDER BY date_add DESC LIMIT 0,1)) LIKE 1;');*/


			//mail('aurelien@anjouweb.com', 'test statsstocksinventaire.php','SELECT * FROM psme_order_detail pod, psme_orders po WHERE po.id_order = pod.id_order AND pod.product_id = "'.$product['id_product'].'" AND pod.product_attribute_id = "'.$attrib['id_product_attribute'].'" AND po.date_add > "'.$annee_inv.'-'.$mois_inv.'-'.$jour_inv.' '.$heure_inv.':'.$minutes_inv.'";');
/*              foreach ($commandes AS $commande)
					{
						$qt_commandee += $commande['product_quantity'];
					}
					$stock_theorique = $inv[0]['valeur'] - $qt_commandee;*/
					//if ( $product['id_product'] == 100 ) error_log('valeur : '.$inv[0]['valeur']);
					//if ( $product['id_product'] == 100 ) error_log('qt_commandee : '.$qt_commandee);
					//if ( $product['id_product'] == 100 ) error_log('stock_theorique : '.$stock_theorique);


					$qtec = Db::getInstance()->ExecuteS('SELECT * FROM psme_stock_available WHERE id_product = "' . $product['id_product'] . '" AND id_product_attribute = "' . $attrib['id_product_attribute'] . '";');

					$poids_theorique += $qtec[0]['quantity'] * $attrib['weight'];
					$stock_theorique = $qtec[0]['quantity'];
				}
				$id_stock_presta = StockAvailable::getStockAvailableIdByProductId($product['id_product'], $attrib['id_product_attribute']);
				$stockAvailable = new StockAvailable($id_stock_presta);

			 // print_r($reference);
				$this->_html .= '<tr><td></td><td>&nbsp;&nbsp;&nbsp;&nbsp;' . $libelle_dec . '</td>' . $der_inv . '<td style="display:none;">' . $stock_theorique . '</td><td>'.$stockAvailable->quantity.'</td></tr>';
				//$this->_html .= '<tr><td></td><td>&nbsp;&nbsp;&nbsp;&nbsp;' . $libelle_dec . '</td>' . $der_inv . '<td>' . $stock_theorique . '</td><td><input type="text" name="' . $product['id_product'] . '#' . $attrib['id_product_attribute'] . '" style="width:50px;" /></td><td>'.$stockAvailable->quantity.'</td><td>&nbsp;</td></tr>';


				$str_export .= $product['name'].' - '.$libelle_dec.';'.$str_inv.';'.$stock_theorique.';'.$stockAvailable->quantity."\n\r";
				$nb_graine_sach = substr($libelle_dec, 0, strpos($libelle_dec, ' '));
				$nb_graine_prod = $nb_graine_sach * $stock_theorique;
				$nb_graine = $nb_graine_prod + $nb_graine;
			}
			$qt_reassort = 0;
			$stock_theorique_tamp = 0;
			$inv_tamp = Db::getInstance()->ExecuteS('SELECT * FROM psme_inventaire WHERE id_product_attribute = "0" AND id_product = "' . $product['id_product'] . '" ORDER BY date DESC LIMIT 0,1;');
//SELECT max(date), SUM(valeur) as valeur FROM psme_inventaire WHERE id_product_attribute = "0" AND id_product = "198" GROUP BY SUBSTR(date,0,8) ORDER BY date DESC LIMIT 0,1
//$inv_tamp = Db::getInstance()->ExecuteS('SELECT max(date) as date, SUM(valeur) as valeur FROM psme_inventaire WHERE id_product_attribute = "0" AND id_product = "'.$product['id_product'].'" GROUP BY SUBSTR(date,0,8) ORDER BY date DESC LIMIT 0,1;');

			$total_site_tamp = $inv_tamp[0]['valeur'];
			if (!empty($inv_tamp[0]['date']))
			{
				$jour_inv_tamp = substr($inv_tamp[0]['date'], 6, 2);
				$mois_inv_tamp = substr($inv_tamp[0]['date'], 4, 2);
				$annee_inv_tamp = substr($inv_tamp[0]['date'], 0, 4);
				$heure_inv_tamp = substr($inv_tamp[0]['date'], 8, 2);
				$minutes_inv_tamp = substr($inv_tamp[0]['date'], 10, 2);
	//$der_inv = ' <font color="#808080">('.$jour_inv.'/'.$mois_inv.'/'.$annee_inv.' '.$heure_inv.':'.$minutes_inv.' = '.$inv[0]['valeur'].')</font>';
				//$der_inv_tamp = '<td>' . $jour_inv_tamp . '/' . $mois_inv_tamp . '/' . $annee_inv_tamp . '</td><td>' . $inv_tamp[0]['valeur'] . '</td>';
				$der_inv_tamp = '<td>' . $jour_inv_tamp . '/' . $mois_inv_tamp . '/' . $annee_inv_tamp . '</td><td></td>';
				$str_der_inv_tamp = $jour_inv_tamp . '/' . $mois_inv_tamp . '/' . $annee_inv_tamp . ';' . $inv_tamp[0]['valeur'];
			}
			else
			{
				$der_inv_tamp = '<td>&nbsp;</td><td>&nbsp;</td>';
			}
			if ($der_inv_tamp != '<td>&nbsp;</td><td>&nbsp;</td>')
			{
	// Somme des quantit�s command�es depuis de dernier inventaire
				$reassorts = Db::getInstance()->ExecuteS('SELECT * FROM psme_reassort WHERE id_product = "' . $product['id_product'] . '" AND id_product_attribute = "0" AND date > "' . $annee_inv_tamp . $mois_inv_tamp . $jour_inv_tamp . $heure_inv_tamp . $minutes_inv_tamp . '";');
				foreach ($reassorts AS $reassort)
				{
					$qt_reassort += $reassort['valeur'];
				}
				$stock_theorique_tamp = $inv_tamp[0]['valeur'] + $qt_reassort;
			}

			$this->_html .= '<tr><td></td><td>&nbsp;&nbsp;&nbsp;&nbsp;Stock tampon</td>' . $der_inv_tamp .'<td style="display:none"></td><td>'.$total_site_tamp.'</td></tr>';
			$str_export .= $product['name'].' - Stock TAMPON ;'.$str_der_inv_tamp.';'.$stock_theorique_tamp.';'."\n\r";
			if ($graine_gramme == "graine") {
				$stock_total = $stock_theorique_tamp + $nb_graine;
			}
			else{
				$stock_total =1000* $poids_theorique + $stock_theorique_tamp;
			}
			$this->_html .= '<tr><td></td><td colspan=3 style="text-align:right">&nbsp;&nbsp;&nbsp;&nbsp;Stock Total : </td><td>' . $stock_total .'</td>';
//<td><input type="text" name="'.$product['id_product'].'#reassort" value="" style="width:50px;"/></td>
			$this->_html .= '</tr>';
			$product_complet_use = new Product($product['id_product']);
			$prix_achat = floatval($product_complet_use->wholesale_price) / 1000;
			if ($graine_gramme == "graine") {
				$prix_stock_theorique = Tools::ps_round(floatval($prix_achat)*($stock_theorique + $stock_theorique_tamp + $nb_graine));
			}
			else{
				$prix_stock_theorique = Tools::ps_round(floatval($prix_achat) * floatval(($poids_theorique * 1000) + $stock_theorique_tamp), 2);
			}
			if ($prix_stock_theorique < 0)
			{
				$prix_stock_theorique = 0;
			}
			$total_theorique_valeur += $prix_stock_theorique;
			$this->_html .= '<tr class="border_bottom_inventaire"><td>&nbsp;</td><td colspan=3 style="text-align:right">&nbsp;&nbsp;&nbsp;&nbsp;Valeur : </td><td>' . $prix_stock_theorique . ' &euro;</td></tr>';

			$this->_html .= '</tbody></table><input type="hidden" name="maj" value="ok" /><input type="hidden" name="id_category" value="' . $id_category . '" /></form></div>';
			$this->_html .=  '<script type="text/javascript" src="/admin123/themes/default/js/statsProduct.js"></script>';
			$this->_html .=  '<script type="text/javascript">
        	$(function() {
        		$(".datepickerLot").datepicker({
        			dateFormat:"yy-mm-dd",
        	  		prevText:"",
        	  		nextText:""});
        		});
      		</script>';

			$data->assign('product',  $this->_html);
		}
		else
		{
			$this->displayWarning($this->l('Aïe'));
		}
		$this->tpl_form_vars['custom_form'] = $data->fetch();
	}
}
