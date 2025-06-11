<?php
/*
* 2007-2012 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
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
*  @version  Release: $Revision: 14011 $
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

if (!defined('_PS_VERSION_'))
	exit;

class Moduleperso extends Module
{
	private $_html = '';

	public function __construct()
	{
		$this->name = 'moduleperso';
		$this->tab = 'front_office_features';
		$this->version = '1.1';
		$this->author = 'PrestaShop';
		$this->need_instance = 0;

		parent::__construct();

		$this->displayName = $this->l('Module perso');
		$this->description = $this->l('Configure des éléments personnalisés');
	}

	public function install()
	{
		if (!parent::install())
			return false;

		Configuration::updateValue('MP_LOGO', 0);

		return true;
	}

	public function uninstall()
	{
		Configuration::deleteByName('MP_LOGO');

		return parent::uninstall();
	}

	public function getContent()
	{
		$this->_html .= '<h2>'.$this->l('Configuration du module perso').'</h2>';
		$this->_postProcess();
		$this->_setConfigurationForm();

		return $this->_html;
	}
	
	private function _postProcess()
	{

		if (Tools::isSubmit('submitMain')){
			foreach($_POST as $key => $p){	
				if(strpos($key, "mp_") === 0){ // pour toutes les clés commançant par "mp_" (module perso)
					$key = strtoupper($key);
					Configuration::updateValue($key, $p);
				}	
			}
			
			// pour les checkboxes
			if(isset($_POST['mp_couleur_fond_produit_a'])){
				Configuration::updateValue('MP_COULEUR_FOND_PRODUIT_A', 'on');
			}else{
				Configuration::updateValue('MP_COULEUR_FOND_PRODUIT_A', 'off');
			}
			if(isset($_POST['mp_couleur_jardin_a'])){
				Configuration::updateValue('MP_COULEUR_JARDIN_A', 'on');
			}else{
				Configuration::updateValue('MP_COULEUR_JARDIN_A', 'off');
			}
			if(isset($_POST['mp_couleur_info_produit_a'])){
				Configuration::updateValue('MP_COULEUR_INFO_PRODUIT_A', 'on');
			}else{
				Configuration::updateValue('MP_COULEUR_INFO_PRODUIT_A', 'off');
			}
			if(isset($_POST['mp_couleur_savoir_plus_a'])){
				Configuration::updateValue('MP_COULEUR_SAVOIR_PLUS_A', 'on');
			}else{
				Configuration::updateValue('MP_COULEUR_SAVOIR_PLUS_A', 'off');
			}
			if(isset($_POST['mp_pancarte'])){
				Configuration::updateValue('MP_PANCARTE', 'on');
			}else{
				Configuration::updateValue('MP_PANCARTE', 'off');
			}
			if(isset($_POST['mp_popin_bons_plants'])){
				Configuration::updateValue('MP_POPIN_BONS_PLANTS', 'on');
			}else{
				Configuration::updateValue('MP_POPIN_BONS_PLANTS', 'off');
			}
			
			// suppression des images
			if(isset($_POST['mp_mini_header_hidden']) && $_POST['mp_mini_header_hidden'] == 1 ){
				Configuration::updateValue('MP_MINI_HEADER', '');
			}
			
			if(isset($_POST['mp_header_hidden']) && $_POST['mp_header_hidden'] == 1 ){
				Configuration::updateValue('MP_HEADER', '');
			}
			
			if(isset($_POST['mp_logo_hidden']) && $_POST['mp_logo_hidden'] == 1 ){
				Configuration::updateValue('MP_LOGO', '');
			}
			
			if(isset($_POST['mp_fond_de_page_hidden']) && $_POST['mp_fond_de_page_hidden'] == 1 ){
				Configuration::updateValue('MP_FOND_DE_PAGE', '');
			}
			
			if(isset($_POST['mp_slogan_1_hidden']) && $_POST['mp_slogan_1_hidden'] == 1 ){
				Configuration::updateValue('MP_SLOGAN_1', '');
			}
			
			if(isset($_POST['mp_slogan_2_hidden']) && $_POST['mp_slogan_2_hidden'] == 1 ){
				Configuration::updateValue('MP_SLOGAN_2', '');
			}
			
			if(isset($_POST['mp_visuel_1_hidden']) && $_POST['mp_visuel_1_hidden'] == 1 ){
				Configuration::updateValue('MP_VISUEL_1', '');
			}
			
			if(isset($_POST['mp_badge_hidden']) && $_POST['mp_badge_hidden'] == 1 ){
				Configuration::updateValue('MP_BADGE', '');
			}
			
			if(isset($_POST['mp_logo_b_hidden']) && $_POST['mp_logo_b_hidden'] == 1 ){
				Configuration::updateValue('MP_LOGO_B', '');
			}
			
			if(isset($_POST['mp_popin_bp_visuel_hidden']) && $_POST['mp_popin_bp_visuel_hidden'] == 1 ){
				Configuration::updateValue('MP_POPIN_BP_VISUEL', '');
			}
			
			if(isset($_POST['mp_avis_google_hidden']) && $_POST['mp_avis_google_hidden'] == 1 ){
				Configuration::updateValue('MP_AVIS_GOOGLE', '');
			}
			
			
			// input text
			if(isset($_POST['mp_popin_bp_lien'])){
				$mp_popin_bp_lien = pSQL($_POST['mp_popin_bp_lien']);
				Configuration::updateValue('MP_POPIN_BP_LIEN', $mp_popin_bp_lien);
			}
		

			//file upload code
			foreach($_FILES as $key => $file){	
				if(!$file["name"]){
					continue;
				}
			
				$target_dir = _PS_UPLOAD_DIR_;
				$target_file = $target_dir . basename($file["name"]);	
				$uploadOk = 1;
				$imageFileType = pathinfo($target_file,PATHINFO_EXTENSION);
										
				// Allow certain file formats
				if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif" ){
					$this->_html .= $this->displayError('Seulement les fichiers JPG, JPEG, PNG & GIF sont autorisés.');
					$uploadOk = 0;
				}
				else {
					if(file_exists($target_file)) {
						chmod($target_file,0755); //Change the file permissions if allowed
						unlink($target_file); //remove the file
					}
					if (move_uploaded_file($file["tmp_name"], $target_file)) {
						//echo "The file ". basename($file["name"]). " has been uploaded.";
						$file_location = basename($file["name"]);
						$key = strtoupper($key);
						Configuration::updateValue($key, $file_location);
						
					} 
					else {
						$this->_html .= $this->displayError('Une erreur est survenue lors de l\'upload de fichier.');
					}
				}	
			}
			$this->_html .= $this->displayConfirmation($this->l('Les paramètres ont été mis à jour'));
		}
	}
	
	private function _setConfigurationForm()
	{
		$upload = '/upload/';
		
		$mp_position_logo_gauche = '';
		$mp_position_logo_centre = '';
		$mp_couleur_fond_produit_a = "";
		$mp_couleur_jardin_a = "";
		$mp_couleur_info_produit_a = "";
		$mp_couleur_savoir_plus_a = "";
		$mp_pancarte = "";
		$mp_popin_bons_plants = "";
		if(Configuration::get('MP_POSITION_LOGO')=='gauche'){
			$mp_position_logo_gauche = ' selected="selected"';
		}
		elseif(Configuration::get('MP_POSITION_LOGO')=='centre'){
			$mp_position_logo_centre = ' selected="selected"';
		}
		if(Configuration::get('MP_COULEUR_FOND_PRODUIT_A')=='on'){
			$mp_couleur_fond_produit_a = ' checked="checked"';
		}
		if(Configuration::get('MP_COULEUR_JARDIN_A')=='on'){
			$mp_couleur_jardin_a = ' checked="checked"';
		}
		if(Configuration::get('MP_COULEUR_INFO_PRODUIT_A')=='on'){
			$mp_couleur_info_produit_a = ' checked="checked"';
		}
		if(Configuration::get('MP_COULEUR_SAVOIR_PLUS_A')=='on'){
			$mp_couleur_savoir_plus_a = ' checked="checked"';
		}
		if(Configuration::get('MP_PANCARTE')=='on'){
			$mp_pancarte = ' checked="checked"';
		}
		if(Configuration::get('MP_POPIN_BONS_PLANTS')=='on'){
			$mp_popin_bons_plants = ' checked="checked"';
		}
		
		
		
		$this->_html .= '
		<br />
		<form method="POST" enctype="multipart/form-data" action="index.php?controller=AdminModules&configure='.$this->name.'&token='.Tools::getAdminTokenLite('AdminModules').'">
			<div class="bootstrap">
			<fieldset>				

				<img class="img_mini_header" src="'.$upload.Configuration::get('MP_MINI_HEADER').'" alt="" style="max-width:500px;">
				<div style="margin-top:10px">
					<label style="width:auto;">Mini header : '.Configuration::get('MP_MINI_HEADER').'</label>
					<input type="file" name="mp_mini_header">
					<label style="width:auto;">Dimensions recommandées : 2000 x 200px</label>
					<input class="mp_mini_header_hidden" type="hidden" name="mp_mini_header_hidden" value="0">
					<input type="button" Value="Supprimer" onclick="jQuery(\'.img_mini_header\').attr(\'src\',\'\'); jQuery(\'.mp_mini_header_hidden\').val(1);">
				</div>
				<div class="clear"></div>
				<hr>
				
				<img class="img_header" src="'.$upload.Configuration::get('MP_HEADER').'" alt="" style="max-width:500px;">
				<div style="margin-top:10px">
					<label style="width:auto;">Header : '.Configuration::get('MP_HEADER').'</label>
					<input type="file" name="mp_header">
					<input class="mp_header_hidden" type="hidden" name="mp_header_hidden" value="0">
					<input type="button" Value="Supprimer" onclick="jQuery(\'.img_header\').attr(\'src\',\'\'); jQuery(\'.mp_header_hidden\').val(1);">
				</div>
				<div class="clear"></div>
				<hr>
				
				<img class="img_logo_b" src="'.$upload.Configuration::get('MP_LOGO_B').'" alt="" style="max-width:500px;">
				<div style="margin-top:10px">
					<label style="width:auto;">Logo : '.Configuration::get('MP_LOGO_B').'</label>
					<input type="file" name="mp_logo_b">
					<label style="width:auto;">Dimensions recommandées : 323 x 100px</label>
					<input class="mp_logo_b_hidden" type="hidden" name="mp_logo_b_hidden" value="0">
					<input type="button" Value="Supprimer" onclick="jQuery(\'.img_logo_b\').attr(\'src\',\'\'); jQuery(\'.mp_logo_b_hidden\').val(1);">
				</div>
				<div class="clear"></div>
				<hr>
				
				<img class="img_logo" src="'.$upload.Configuration::get('MP_LOGO').'" alt="" style="max-width:500px;">
				<div style="margin-top:10px">
					<label style="width:auto;">Logo : '.Configuration::get('MP_LOGO').'</label>
					<input type="file" name="mp_logo">
					<label style="width:auto;">Dimensions recommandées : 215 x 215px</label>
					<input class="mp_logo_hidden" type="hidden" name="mp_logo_hidden" value="0">
					<input type="button" Value="Supprimer" onclick="jQuery(\'.img_logo\').attr(\'src\',\'\'); jQuery(\'.mp_logo_hidden\').val(1);">
				</div>
				<div class="clear"></div>				
				<hr>
				
				<div style="margin-top:10px">
					<label style="width:auto;">Position du logo dans les pages catalogues :</label>
					<select name="mp_position_logo" style="width:auto;">					
						<option value="gauche" '.$mp_position_logo_gauche.'>Gauche</option>
						<option value="centre" '.$mp_position_logo_centre.'>Centré</option>
				    </select>
				</div>
				<div class="clear"></div>				
				<hr>
				 
				<img class="img_fond_de_page" src="'.$upload.Configuration::get('MP_FOND_DE_PAGE').'" alt="" style="max-width:500px;">
				<div style="margin-top:10px">
					<label style="width:auto;">Fond de page : '.Configuration::get('MP_FOND_DE_PAGE').'</label>
					<input type="file" name="mp_fond_de_page">
					<input class="mp_fond_de_page_hidden" type="hidden" name="mp_fond_de_page_hidden" value="0">
					<input type="button" Value="Supprimer" onclick="jQuery(\'.img_fond_de_page\').attr(\'src\',\'\'); jQuery(\'.mp_fond_de_page_hidden\').val(1);">
				</div>
				<div class="clear"></div>
				<hr>
				
				<div style="margin-top:10px">
					<label style="width:auto;">Couleur du fond de page si pas d\'image : '.Configuration::get('MP_COULEUR_FOND_DE_PAGE').'</label>
					<input type="color" name="mp_couleur_fond_de_page" value="'.Configuration::get('MP_COULEUR_FOND_DE_PAGE').'">
				</div>
				<div class="clear"></div>
				
				<img class="img_slogan_1" src="'.$upload.Configuration::get('MP_SLOGAN_1').'" alt="" style="max-width:500px;">
				<div style="margin-top:10px">
					<label style="width:auto;">Slogan 1 : '.Configuration::get('MP_SLOGAN_1').'</label>
					<input type="file" name="mp_slogan_1">
					<input class="mp_slogan_1_hidden" type="hidden" name="mp_slogan_1_hidden" value="0">
					<input type="button" Value="Supprimer" onclick="jQuery(\'.img_slogan_1\').attr(\'src\',\'\'); jQuery(\'.mp_slogan_1_hidden\').val(1);">
				</div>
				<div class="clear"></div>
				<hr>
				
				<img class="img_slogan_2" src="'.$upload.Configuration::get('MP_SLOGAN_2').'" alt="" style="max-width:500px;">
				<div style="margin-top:10px">
					<label style="width:auto;">Slogan 2 : '.Configuration::get('MP_SLOGAN_2').'</label>
					<input type="file" name="mp_slogan_2">
					<input class="mp_slogan_2_hidden" type="hidden" name="mp_slogan_2_hidden" value="0">
					<input type="button" Value="Supprimer" onclick="jQuery(\'.img_slogan_2\').attr(\'src\',\'\'); jQuery(\'.mp_slogan_2_hidden\').val(1);">
				</div>
				<div class="clear"></div>
				<hr>
				
				<div style="margin-top:10px">
					<label style="width:auto;">Couleur du texte : '.Configuration::get('MP_COULEUR_TEXTE').'</label>
					<input type="color" name="mp_couleur_texte" value="'.Configuration::get('MP_COULEUR_TEXTE').'">
				</div>
				<div class="clear"></div>
				<hr>
				
				<img class="img_visuel_1" src="'.$upload.Configuration::get('MP_VISUEL_1').'" alt="" style="max-width:500px;">
				<div style="margin-top:10px">
					<label style="width:auto;">Visuel sous le menu de gauche : '.Configuration::get('MP_VISUEL_1').'</label>
					<input type="file" name="mp_visuel_1">
					<label style="width:auto;">Dimensions recommandées : 380 x 380px</label>
					<input class="mp_visuel_1_hidden" type="hidden" name="mp_visuel_1_hidden" value="0">
					<input type="button" Value="Supprimer" onclick="jQuery(\'.img_visuel_1\').attr(\'src\',\'\'); jQuery(\'.mp_visuel_1_hidden\').val(1);">
				</div>
				<div class="clear"></div>
				<hr>
				
				<div style="margin-top:10px">
					<label style="width:auto;">Couleur du fond du cadre "détail produit" : '.Configuration::get('MP_COULEUR_FOND_PRODUIT').'</label>
					<input type="color" name="mp_couleur_fond_produit" value="'.Configuration::get('MP_COULEUR_FOND_PRODUIT').'">
				</div>
				<div class="clear"></div>
				<label style="width:auto;">Transparent</label>
				<input type="checkbox" name="mp_couleur_fond_produit_a" '.$mp_couleur_fond_produit_a.'">
				<div class="clear"></div>
				<hr>
				
				<div style="margin-top:10px">
					<label style="width:auto;">Couleur du fond du cadre "jardin d\'essai" : '.Configuration::get('MP_COULEUR_JARDIN').'</label>
					<input type="color" name="mp_couleur_jardin" value="'.Configuration::get('MP_COULEUR_JARDIN').'">
				</div>
				<div class="clear"></div>
				<label style="width:auto;">Transparent</label>
				<input type="checkbox" name="mp_couleur_jardin_a" '.$mp_couleur_jardin_a.'">
				<div class="clear"></div>
				<hr>
				
				<div style="margin-top:10px">
					<label style="width:auto;">Couleur du fond du cadre "caractéristiques produit" : '.Configuration::get('MP_COULEUR_INFO_PRODUIT').'</label>
					<input type="color" name="mp_couleur_info_produit" value="'.Configuration::get('MP_COULEUR_INFO_PRODUIT').'">
				</div>
				<div class="clear"></div>
				<label style="width:auto;">Transparent</label>
				<input type="checkbox" name="mp_couleur_info_produit_a" '.$mp_couleur_info_produit_a.'">
				<div class="clear"></div>
				<hr>
				
				<div style="margin-top:10px">
					<label style="width:auto;">Couleur du fond du cadre "en savoir plus" : '.Configuration::get('MP_COULEUR_SAVOIR_PLUS').'</label>
					<input type="color" name="mp_couleur_savoir_plus" value="'.Configuration::get('MP_COULEUR_SAVOIR_PLUS').'">
				</div>
				<div class="clear"></div>
				<label style="width:auto;">Transparent</label>
				<input type="checkbox" name="mp_couleur_savoir_plus_a" '.$mp_couleur_savoir_plus_a.'">
				<div class="clear"></div>
				<hr>
				
				<img class="img_pictos" src="'.$upload.Configuration::get('MP_PICTOS').'" alt="" style="max-width:500px;">
				<div style="margin-top:10px">
					<label style="width:auto;">Pictos des caractéristiques produit : '.Configuration::get('MP_PICTOS').'</label>
					<input type="file" name="mp_pictos">
					<input class="mp_pictos_hidden" type="hidden" name="mp_pictos_hidden" value="0">
					<input type="button" Value="Supprimer" onclick="jQuery(\'.img_pictos_1\').attr(\'src\',\'\'); jQuery(\'.mp_pictos_hidden\').val(1);">
				</div>
				<div class="clear"></div>
				<hr>
		   
				<div style="margin-top:10px">
					<label style="width:auto;">Afficher la pancarte</label>
					<input type="checkbox" name="mp_pancarte" '.$mp_pancarte.'">		
				</div>
				<div class="clear"></div>
				<hr>
				
				<img class="img_badge" src="'.$upload.Configuration::get('MP_BADGE').'" alt="" style="max-width:500px;">
				<div style="margin-top:10px">
					<label style="width:auto;">Badge : '.Configuration::get('MP_BADGE').'</label>
					<input type="file" name="mp_badge">
					<label style="width:auto;">Dimensions recommandées : 100 x 100px</label>
					<input class="mp_badge_hidden" type="hidden" name="mp_badge_hidden" value="0">
					<input type="button" Value="Supprimer" onclick="jQuery(\'.img_badge\').attr(\'src\',\'\'); jQuery(\'.mp_badge_hidden\').val(1);">
				</div>
				<div class="clear"></div>
				<hr>

				<div style="margin-top:10px">
					<label style="width:auto;">Afficher la popin bons plants</label>
					<input type="checkbox" name="mp_popin_bons_plants" '.$mp_popin_bons_plants.'">		
				</div>
				<div class="clear"></div>
				<hr>
				
				<div style="margin-top:10px">
					<label style="width:auto;">Lien de la popin bons plants :</label>
					<input class="mp_popin_bp_lien" name="mp_popin_bp_lien" type="text" value="'.Configuration::get('MP_POPIN_BP_LIEN').'">
				</div>
				<div class="clear"></div>
				<hr>
				
				<img class="img_mp_popin_bp_visuel" src="'.$upload.Configuration::get('MP_POPIN_BP_VISUEL').'" alt="" style="max-width:500px;">
				<div style="margin-top:10px">
					<label style="width:auto;">Visuel de la popin bons plants : '.Configuration::get('MP_POPIN_BP_VISUEL').'</label>
					<input type="file" name="mp_popin_bp_visuel">
					<input class="mp_popin_bp_visuel_hidden" type="hidden" name="mp_popin_bp_visuel_hidden" value="0">
					<input type="button" Value="Supprimer" onclick="jQuery(\'.img_mp_popin_bp_visuel\').attr(\'src\',\'\'); jQuery(\'.mp_popin_bp_visuel_hidden\').val(1);">
				</div>
				<div class="clear"></div>
				<hr>
				
				<img class="img_avis_google" src="'.$upload.Configuration::get('MP_AVIS_GOOGLE').'" alt="" style="max-width:500px;">
				<div style="margin-top:10px">
					<label style="width:auto;">Avis Google : '.Configuration::get('MP_AVIS_GOOGLE').'</label>
					<input type="file" name="mp_avis_google">
					<label style="width:auto;">Dimensions recommandées : 141 x 28px</label>
					<input class="mp_avis_google_hidden" type="hidden" name="mp_avis_google_hidden" value="0">
					<input type="button" Value="Supprimer" onclick="jQuery(\'.img_avis_google\').attr(\'src\',\'\'); jQuery(\'.mp_avis_google_hidden\').val(1);">
				</div>
				<div class="clear"></div>
				<hr>
				
				
		   
				<input type="submit" class="btn btn-default" name="submitMain" value="Enregistrer" />
			</fieldset>
			</div>
			
		</form>
		<style>
		.bootstrap hr {
			border-top: 1px solid #ccc;
		}
		</style>
		';
	}
}
