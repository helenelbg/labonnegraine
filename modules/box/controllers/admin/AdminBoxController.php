<?php

class AdminBoxController extends ModuleAdminController {

    public function init() {
        parent::init();
    }

	public function traiter_box($id_custo,$date_saison_lien_txt) {
   
		$req_deja_traitee="SELECT * FROM flags_saisons_box WHERE id_order_detail='".$id_custo."' AND flag_saisons='".$date_saison_lien_txt."'";
		$T_deja_traitee= Db::getInstance()->executeS($req_deja_traitee);
		if(count($T_deja_traitee)) {
			return;
		}
         
		$req_transporteur='SELECT max(id_carrier) as id_carrier_max FROM '._DB_PREFIX_.'carrier WHERE id_reference=189';


        $T_Transporteur = Db::getInstance()->executeS($req_transporteur);
		$id_transporteur_livraison=$T_Transporteur[0]['id_carrier_max'];
        $id_produit_fictif=1858;
        $id_declinaison_fictive=0;
		// 2 : Paiement accepté
		// 3 : Preparation en cours
		// 21 : Box à livrer prochainement
		//$etat_commande = 2; 
		$etat_commande = 21; 
		$req_traiter='SELECT id_order_detail,od.id_order, date_add FROM '._DB_PREFIX_.'customization c LEFT JOIN `'.
                _DB_PREFIX_.'orders` o on (c.id_cart=o.id_cart) LEFT JOIN '._DB_PREFIX_.'order_detail od on (od.id_order=o.id_order) '
                . ' WHERE c.id_customization='.$id_custo.' AND c.id_product=od.product_id AND c.id_product_attribute=od.product_attribute_id';


        $T_traiter = Db::getInstance()->executeS($req_traiter);
  
  
  
		$req_traitement="INSERT INTO flags_saisons_box (id_order_detail, flag_saisons) VALUE (".$id_custo.",'".$date_saison_lien_txt."')";
        Db::getInstance()->execute($req_traitement);

        $old_od=new OrderDetail( $T_traiter[0]['id_order_detail']);
        $old_order=new Order($old_od->id_order);

        $T_pdts_old=$old_order->getProducts();
            foreach($T_pdts_old as $T_pdt) {
                if($T_traiter[0]['id_order_detail']==$T_pdt['id_order_detail'])
                {
                    if(sizeOf($T_pdt['customizedDatas'])>0) {
                        foreach ($T_pdt['customizedDatas'] as $T_delivs) {
                             foreach ($T_delivs as $T_custo) {
                                  if($T_custo["datas"][1][0]['id_customization']==$id_custo) {
                                 $p_old_datas=$T_custo['datas'];
                                 $p_old_name=$T_pdt['product_name'];
                                 $p_old_qte=$T_custo['quantity'];
                                  }
                             }
                        }
                    }
                }

            }

        $newadress=new Address();
        $newadress->id_customer=$old_order->id_customer;
        $newadress->alias="Livraison Box AUTO";

        $new_data=array();
        $tdata1=$p_old_datas[1];
        $deb_cust=0;
        foreach($tdata1 as $T_data)
        {
            if($deb_cust==0) $deb_cust=$T_data['id_customization_field'];
            $new_data[$T_data['id_customization_field']]=$T_data['value'];
        }

        $newadress->lastname=$new_data[$deb_cust];
        $newadress->firstname=$new_data[$deb_cust+1];
        $newadress->address1=$new_data[$deb_cust+2];
        if(isset($new_data[$deb_cust+3])) {
            $newadress->address2=$new_data[$deb_cust+3];
        }

        $regexCPOSTAL= "'^[0-9]{5}$'"; // CODE POSTAL
        $codepostal=$new_data[$deb_cust+4];

		if(!preg_match($regexCPOSTAL,$codepostal)) {
			$newadress->address2=$newadress->address2."  ".$codepostal;
			$codepostal="00000";
		}

        $newadress->postcode=$codepostal;
        $newadress->city=$new_data[$deb_cust+5];
        if(preg_match('/^[+0-9. ()-]*$/', $new_data[$deb_cust+7])) {
            $newadress->phone=$new_data[$deb_cust+7];
        }
        else {
            $newadress->phone="-";
            $newadress->address2=$newadress->address2." ".$new_data[$deb_cust+7];
        }
        $newadress->id_manufacturer=1;
        $newadress->id_supplier=1;
        $newadress->id_warehouse=1;
        $newadress->id_country=8;
        $newadress->id_state=1;
        $newadress->country="France";
        $newadress->id_state=0;

        $newadress->add();

        Context::getContext()->customer=new Customer($old_order->id_customer);

        $new_cart=new Cart();
        $new_cart->id_customer=$old_order->id_customer;
        $new_cart->id_address_delivery = $old_order->id_address_invoice;
        $new_cart->id_address_invoice = $old_order->id_address_invoice;
        $new_cart->id_lang = 2;
        $new_cart->id_cart=1;
        $new_cart->id_currency=1;
        $new_cart->recyclable = 0;
        $new_cart->gift = 0;
        $new_cart->id_carrier=$id_transporteur_livraison;
        $new_cart->payment=0;
        $new_cart->module=0;
        $new_cart->total_paid=0;
        $new_cart->total_paid_real=0;
        $new_cart->total_products=0;
        $new_cart->total_products_wt=0;
        $new_cart->conversion_rate=0;
        $new_cart->total_products_wt=0;

        $new_cart->add();

        $new_cart->updateQty(1, $id_produit_fictif, 0);

        $new_cart->update();

        do
        $reference = Order::generateReference();
        while(Order::getByReference($reference)->count());

        $new_order=new Order();
        $new_order->reference = $reference;
        $new_order->id_customer=$old_order->id_customer;
        $new_order->id_cart=$new_cart->id;
        $new_order->id_currency=1;
        $new_order->id_carrier=$id_transporteur_livraison;
        $new_order->payment="Le paiement";
        $new_order->module="1";
        $new_order->total_paid=0;
        $new_order->total_paid_real=0;
        $new_order->total_products=0;
        $new_order->total_products_wt=0;
        $new_order->conversion_rate=1;
        $new_order->id_shop = 1;
        $new_order->id_lang = 2;
        $new_order->valid = 1;
        $new_order->secure_key = md5(uniqid(rand(), true));
        $new_order->id_address_delivery=$newadress->id;
        $new_order->id_address_invoice=$old_order->id_address_invoice;
        $new_order->id_customer=$old_order->id_customer;
        $new_order->current_state=$etat_commande;
        $new_order->add();

        $new_order_detail=new OrderDetail();
        $new_order_detail->id_order=$new_order->id;
        $new_order_detail->product_id=$id_produit_fictif;
        $new_order_detail->id_order_invoice=0;
        $new_order_detail->id_shop=0;
        $new_order_detail->id_warehouse =0;
        $new_order_detail->product_price=0;
        $new_order_detail->id_shop=0;
        $new_order_detail->id_shop=0;
        $new_order_detail->product_attribute_id=$id_declinaison_fictive; //11480
        $new_order_detail->product_name=$p_old_name;
        $new_order_detail->product_quantity=$p_old_qte;

        $new_order_detail->add();

        $order_carrier = new OrderCarrier();
        $order_carrier->id_order = $new_order->id;
        $order_carrier->id_carrier = $id_transporteur_livraison;
        $order_carrier->weight = 0;
        $order_carrier->shipping_cost_tax_excl = 0;
        $order_carrier->shipping_cost_tax_incl = 0;
        $order_carrier->add();

        $id_cart=$new_cart->id;
        /*$req_colissimo="INSERT INTO `"._DB_PREFIX_."socolissimo_delivery_info` (id_cart, id_customer, delivery_mode, prname, prfirstname,pradress3, pradress1, przipcode,prtown, cecountry,cephonenumber,ceemail,cename,cefirstname)
            VALUE ('".$new_cart->id."','".$old_order->id_customer."','DOM','".$newadress->lastname."','".$newadress->firstname."','".$newadress->address1."','".$newadress->address2."','".$newadress->postcode."','".$newadress->city."','".$newadress->country."','".$newadress->phone."','".$new_data[$deb_cust+6]."','".$newadress->firstname."','".$newadress->address1."')";*/
		$req_colissimo="INSERT INTO `"._DB_PREFIX_."colissimo_order` (id_order, id_colissimo_service, id_colissimo_pickup_point, migration, ddp, ddp_cost, hidden)
			VALUE ('".$new_order->id."','1','0','0','0','0','0')";
        Db::getInstance()->execute($req_colissimo);
    }//******************** FIN fonction traiter box ************************/

    public function initContent(){
		

        if(!isset($_GET['saison']) ){      
			header("Location: index.php?controller=AdminBox&d&saison=1&token=".$_GET['token']);
		}
		$Context=Context::getContext();



		function update_custom_sql($id_custom,$index_custom,$txt_custom){
			  $req_custom ="SELECT * FROM "._DB_PREFIX_."customized_data WHERE `id_customization`='".$id_custom."' AND `index`='".$index_custom."'";
			$T_commande_box = Db::getInstance()->executeS($req_custom);
			if(sizeof($T_commande_box)==1) {
				$req_update="UPDATE "._DB_PREFIX_."customized_data SET value='".str_replace("'","\'",$txt_custom)."' WHERE `id_customization`='".$id_custom."' AND `index`='".$index_custom."'";
				Db::getInstance()->execute($req_update);
				error_log($req_update);
			}
			else
			{
				$req_update="INSERT INTO "._DB_PREFIX_."customized_data SET value='".str_replace("'","\'",$txt_custom)."', `id_customization`='".$id_custom."', `index`='".$index_custom."',`type`='1';";
				Db::getInstance()->execute($req_update);
				error_log($req_update);
			}
		}

		if(isset($_POST['id_cf'])) {
		   update_custom_sql($_POST['id_cf'],$_POST['index_custo_mail'],$_POST['txt_email']);
		}
		if(isset($_POST['id_modif_nom'])) {
		   update_custom_sql($_POST['id_modif_nom'],$_POST['index_custo_nom'],$_POST['txt_nom']);
		}
		if(isset($_POST['id_modif_prenom'])) {
		   update_custom_sql($_POST['id_modif_prenom'],$_POST['index_custo_prenom'],$_POST['txt_prenom']);
		}
		if(isset($_POST['id_modif_adresse_1'])) {
		   update_custom_sql($_POST['id_modif_adresse_1'],$_POST['index_custo_adresse_1'],$_POST['txt_adresse_1']);
		}
		if(isset($_POST['id_modif_code_postal'])) {
		   update_custom_sql($_POST['id_modif_code_postal'],$_POST['index_custo_code_postal'],$_POST['txt_code_postal']);
		}
		if(isset($_POST['id_modif_adresse_2'])) {
		   update_custom_sql($_POST['id_modif_adresse_2'],$_POST['index_custo_adresse_2'],$_POST['txt_adresse_2']);
		}
		if(isset($_POST['id_modif_phone'])) {
		   update_custom_sql($_POST['id_modif_phone'],$_POST['index_custo_phone'],$_POST['txt_phone']);
		}
		if(isset($_POST['id_modif_adresse_2_vraie'])) {
		   update_custom_sql($_POST['id_modif_adresse_2_vraie'],$_POST['index_custo_adresse_2_vraie'],$_POST['txt_adresse_2_vraie']);
		}

		$jour_envoi=10;
		$etat_commande=2; // paiement accepté

		$tdate_saison[]=1;
		$tdate_saison[]=4;
		$tdate_saison[]=7;
		$tdate_saison[]=10;


		$mois_courant=date("n");
		$jour_courant=date("j");
		$saisons[]=0;
		$saisons[]=0;
		$saisons[]=0;
		$saisons[]=0;

		$numsaison=0;
        $posannee=0;
        $saisons_annee[]=array();
        do {
            foreach($tdate_saison as $value) {
                if($numsaison>0) {
                    $saisons[$numsaison]=$value;
                    $saisons_annee[$numsaison]=$posannee;
                    $numsaison++;
                }
                if(($value>$mois_courant || ($value==$mois_courant && $jour_envoi>=$jour_courant) || $posannee>0) && $numsaison==0 ){
                    $saisons[$numsaison]=$value;
                    $saisons_annee[$numsaison]=$posannee;
                    $numsaison++;
                }
            }
            $posannee++;
            if($posannee>10) break;
        } while($numsaison<4);

		$posannee=0;
		$pos_saison=$saisons[0]-3;

		/***** s -1 *****/
		if($pos_saison<0)
		{
		   $pos_saison=$tdate_saison[3];
		   $posannee=$posannee -1;
		}
		$saisons["-1"]=$pos_saison;
		$saisons_annee["-1"]=$posannee;



		/***** s -2 *****/
		$pos_saison=$saisons["-1"] -3;
		if($pos_saison<0)
		{
		   $pos_saison=$tdate_saison[3];
			$posannee=$posannee -1;
		}
		$saisons["-2"]=$pos_saison;
		$saisons_annee["-2"]=$posannee;



		/***** s -3 *****/
		$pos_saison=$saisons["-2"]-3;
		if($pos_saison<0)
		{
		   $pos_saison=$tdate_saison[3];
			$posannee=$posannee -1;
		}
		$saisons["-3"]=$pos_saison;
		$saisons_annee["-3"]=$posannee;



		/***** s -4 *****/
		$pos_saison=$saisons["-3"]-3;
		if($pos_saison<0)
		{
		   $pos_saison=$tdate_saison[3];
		   $posannee=$posannee -1;
		}
		$saisons["-4"]=$pos_saison;
		$saisons_annee["-4"]=$posannee;

		$pos_s=0;

		$num_saison_lien=0;
		if(isset( $_GET['saison'])){
			$num_saison_lien =  $_GET['saison'];
		}

		if($num_saison_lien>0)
		{
			$num_saison_lien=$num_saison_lien-1;
		}
		$date_saison_lien=mktime(0, 0, 0,$saisons[0]+ ($num_saison_lien*3) , $jour_envoi+1, date("Y")+ $saisons_annee[0]);
		$date_saison_lien_4=mktime(0, 0, 0, date("m",$date_saison_lien)-12 , $jour_envoi, date("Y",$date_saison_lien));
		$date_saison_lien_3=mktime(0, 0, 0, date("m",$date_saison_lien)-9 , $jour_envoi, date("Y",$date_saison_lien));
		$date_saison_lien_2=mktime(0, 0, 0, date("m",$date_saison_lien)-6 , $jour_envoi, date("Y",$date_saison_lien));
		$date_saison_lien_1=mktime(0, 0, 0, date("m",$date_saison_lien)-3 , $jour_envoi, date("Y",$date_saison_lien));


		$date_saison_lien_txt=date("Y-m-d",$date_saison_lien);
		if(isset($_GET['id_custo2'])) {
			self::traiter_box($_GET['id_custo2'],$date_saison_lien_txt);
			header("Location: index.php?controller=AdminBox&d&saison=1&message=traiter_une&token=".$_GET['token']);
		}

		$select4saisons=" (pac.id_attribute=2313 AND o.date_add >= '".date("Y-m-d",$date_saison_lien_4)."' AND o.date_add < '".date("Y-m-d",$date_saison_lien)."' ) ";
		$select3saisons=" (pac.id_attribute=2312 AND o.date_add >= '".date("Y-m-d",$date_saison_lien_3)."' AND o.date_add < '".date("Y-m-d",$date_saison_lien)."' ) ";
		$select2saisons=" (pac.id_attribute=2311 AND o.date_add >= '".date("Y-m-d",$date_saison_lien_2)."' AND o.date_add < '".date("Y-m-d",$date_saison_lien)."' ) ";
		$select1saisons=" (pac.id_attribute=2310 AND o.date_add >= '".date("Y-m-d",$date_saison_lien_1)."' AND o.date_add < '".date("Y-m-d",$date_saison_lien)."' ) ";
		$select_date= " AND (".$select4saisons." OR ".$select3saisons." OR ".$select2saisons." OR ".$select1saisons.")";

        $lien=$this->context->link->getModuleLink('box','AdminBox');
        switch (date("m",$date_saison_lien))
        {
            case "01": $mois_lien="janvier"; break;
            case "04": $mois_lien="avril"; break;
            case "07": $mois_lien="juillet"; break;
            case "10": $mois_lien="octobre"; break;
        }
		$Context->smarty->assign("mois_lien",$mois_lien);

		// id_order_state 2 = paiement accepté

		$req_box='SELECT c.id_customization, f.flag_saisons, o.reference, o.date_add, od.id_order, od.id_order_detail, od.product_id, od.product_attribute_id, o.id_address_delivery, o.id_cart, od.product_quantity, od.product_quantity_refunded, od.product_quantity_return FROM `'.
		_DB_PREFIX_.'order_detail` od LEFT JOIN '._DB_PREFIX_.'product p on (p.id_product=od.product_id) '
			  . ' LEFT JOIN '._DB_PREFIX_.'orders o on (od.id_order=o.id_order) '
			  . ' LEFT JOIN '._DB_PREFIX_.'product_attribute_combination pac on (pac.id_product_attribute=od.product_attribute_id ) '
			  . ' LEFT JOIN '._DB_PREFIX_.'customization c on (o.id_cart=c.id_cart ) '
			  . ' LEFT JOIN '._DB_PREFIX_.'order_history oh on (o.id_order=oh.id_order ) '
			  . ' LEFT JOIN flags_saisons_box f on (c.id_customization=f.id_order_detail  AND flag_saisons=\''.$date_saison_lien_txt.'\' ) '
		. ' WHERE o.id_order <> 117342 AND o.id_order <> 195663 AND od.id_order_detail <> 1310135 AND o.current_state <> 6 AND o.current_state <> 8 AND oh.id_order_state = 2 AND `id_category_default` = 227 '.$select_date;

        //echo $req_box;
        $T_commande_box = Db::getInstance()->executeS($req_box);
				
        $T_lignes_box = array();
        $nbt=0;
        $T_index_mail=array();
        $liste_box = array(1849,1850,1851,2638);
		$T_stat = [];
		$STAT_declinaisons = [];
        foreach($T_commande_box as $T_ligne_commande) {
            $I_order=new Order($T_ligne_commande['id_order']);

            // test si custo absentes
            $test_pc=$I_order->getProducts();
            foreach($test_pc as $test_c) {
                if($T_ligne_commande['id_order_detail']==$test_c['id_order_detail'])
                {
					// bug fix , Dorian BERRY-WEB , 9 juin 2023 pour Prestashop 8
					$b_customizedDatas = false;
					if(is_array($test_c['customizedDatas']) && count($test_c['customizedDatas'])){
						$b_customizedDatas = true;
					}
                    if(in_array($T_ligne_commande['product_id'], $liste_box) && !$b_customizedDatas) {
                        $req_ajoutc='INSERT INTO '._DB_PREFIX_.'customization SET id_product_attribute="'.$T_ligne_commande['product_attribute_id'].'", id_address_delivery="'.$T_ligne_commande['id_address_delivery'].'", id_cart="'.$T_ligne_commande['id_cart'].'", id_product="'.$T_ligne_commande['product_id'].'", quantity="'.$T_ligne_commande['product_quantity'].'", quantity_refunded="'.$T_ligne_commande['product_quantity_refunded'].'", quantity_returned="'.$T_ligne_commande['product_quantity_return'].'", in_cart="1";';
                        Db::getInstance()->execute($req_ajoutc);

                        $reqCustoC = 'SELECT id_customization FROM '._DB_PREFIX_.'customization WHERE id_cart = "'.$T_ligne_commande['id_cart'].'" ORDER BY id_customization DESC LIMIT 0,1';
                        $custoC = Db::getInstance()->executeS($reqCustoC);

                        $req_fields = 'SELECT * FROM '._DB_PREFIX_.'customization_field WHERE id_product = "'.$T_ligne_commande['product_id'].'";';
                        $FieldsEc = Db::getInstance()->executeS($req_fields);
                        foreach ($FieldsEc as $fieldEc)
                        {
                            $req_ins1='INSERT INTO '._DB_PREFIX_.'customized_data SET id_customization = "'.$custoC[0]['id_customization'].'", type="1", `index` = "'.$fieldEc['id_customization_field'].'";';
                            Db::getInstance()->execute($req_ins1);
                        }
                    }
                }
            }

            $T_pdts=$I_order->getProducts();
            foreach($T_pdts as $T_pdt) {
                if($T_ligne_commande['id_order_detail']==$T_pdt['id_order_detail']) {
                    if(is_array($T_pdt['customizedDatas']) && count($T_pdt['customizedDatas'])) {
                        foreach ($T_pdt['customizedDatas'] as  $T_delivs) {
                            foreach ($T_delivs as $key => $T_custo) {
                                if($T_custo["datas"][1][0]['id_customization']==$T_ligne_commande['id_customization']) {
									$order_box=new Order($T_pdt['id_order']);
									$etat_commande=$order_box->getCurrentOrderState();
									$T_lignes_box[$key]['datas']=$T_custo['datas'];
								    $T_lignes_box[$key]['product_name']=$T_pdt['product_name'];
								    $T_lignes_box[$key]['reference']=$T_ligne_commande['reference'];
								    $T_lignes_box[$key]['id_order']=$T_ligne_commande['id_order'];
								    $T_lignes_box[$key]['date_add']=$T_ligne_commande['date_add'];
									$T_lignes_box[$key]['etat_commande_lib'] = '';
									if(isset($etat_commande->name[2])){
										$T_lignes_box[$key]['etat_commande_lib']=$etat_commande->name[2];
									}
								    $T_lignes_box[$key]['etat_commande_num']=$etat_commande->id;
								    $T_lignes_box[$key]['date_saison']= $T_ligne_commande['flag_saisons'];
								    $T_lignes_box[$key]['qte']= $T_custo['quantity'];
								    $T_lignes_box[$key]['link']= $this->context->link->getAdminLink('AdminOrders').'&id_order='.(int)$T_ligne_commande['id_order'].'&vieworder';
	
	
									$T_lignes_box[$key]['index_custo_mail'] = "";
									$T_lignes_box[$key]['index_custo_nom'] = "";
									$T_lignes_box[$key]['index_custo_prenom'] = "";
									$T_lignes_box[$key]['index_custo_adresse_1'] = "";
									$T_lignes_box[$key]['index_custo_code_postal'] = "";
									$T_lignes_box[$key]['index_custo_adresse_2'] = "";
									$T_lignes_box[$key]['index_custo_phone'] = "";
									$T_lignes_box[$key]['index_custo_adresse_2_vraie'] = "26";
								
									
                                    $deb_cust=0;
                                    $new_data=array();
                                    foreach($T_custo['datas'][1] as $T_data) {
										if($deb_cust==0) $deb_cust=$T_data['id_customization_field'];
										$new_data[$T_data['id_customization_field']]=$T_data['value'];
										$new_id_custo=$T_data['id_customization'];
										if($T_data['name']=="E-mail") {
											$T_lignes_box[$key]['index_custo_mail']=$T_data['index'];
										}else if($T_data['name']=="Nom") {
											$T_lignes_box[$key]['index_custo_nom']=$T_data['index'];
										}
										else if($T_data['name']=="Prénom") {
											$T_lignes_box[$key]['index_custo_prenom']=$T_data['index'];
										}
										else if($T_data['name']=="Adresse 1") {
											$T_lignes_box[$key]['index_custo_adresse_1']=$T_data['index'];
										}
										else if($T_data['name']=="Code Postal") {
											$T_lignes_box[$key]['index_custo_code_postal']=$T_data['index'];
										}
										else if($T_data['name']=="Ville") {
											$T_lignes_box[$key]['index_custo_adresse_2']=$T_data['index'];
										}
										else if($T_data['name']=="Phone") {
											$T_lignes_box[$key]['index_custo_phone']=$T_data['index'];
										}
										else if($T_data['name']=="Adresse 2") {
											$T_lignes_box[$key]['index_custo_adresse_2_vraie']=$T_data['index'];
										}
									}
									
									$T_lignes_box[$key]['new_datas']=$new_data;
									$T_lignes_box[$key]['deb_cust']=$deb_cust;
									$T_lignes_box[$key]['new_id_customization']=$new_id_custo;
									if(isset($T_stat[$T_pdt['product_id']])) {
										$T_stat[$T_pdt['product_id']]=$T_stat[$T_pdt['product_id']]+$T_custo['quantity'];
									} else {
										$T_stat[$T_pdt['product_id']]=$T_custo['quantity'];
									}

                                    if(isset($STAT_declinaisons[$T_data['id_product_attribute']])) {
										$STAT_declinaisons[$T_data['id_product_attribute']]=$STAT_declinaisons[$T_data['id_product_attribute']]+$T_custo['quantity'];
									} else {
										$STAT_declinaisons[$T_data['id_product_attribute']]=$T_custo['quantity'];
									}

									switch ($etat_commande->id) {
										case 2: case 3: case 4: case 5: case 9: case 12: case 14: case 20: case 18: case 21: case 22: case 23:
										    if(isset($_POST["action_traiter"]) && $_GET['saison']==-1 && $T_lignes_box[$key]['date_saison']=="") {
											   self::traiter_box($T_ligne_commande['id_customization'],$date_saison_lien_txt);
											   $nbt++;
												$T_lignes_box[$key]['date_saison']= "xxx";
										    }
											$T_lignes_box[$key]['paiement_ok']=true;
											break;
										default:
											$T_lignes_box[$key]['paiement_ok']=false;
											break;
									}
										
									

									
                                }
                            }
                        }
                    }
                }
            }
        }
		if(isset($_POST["action_traiter"]) ) {
			header("Location: index.php?controller=AdminBox&d&saison=-1&message=traiter_toutes&token=".$_GET['token']."&nbt=".$nbt);
		}

		$stat_array = [11767, 11768, 11769, 11770, 11771, 11772, 11773, 11774];
		foreach($stat_array as $s){
			if(!isset($STAT_declinaisons[$s])){
				$STAT_declinaisons[$s] = 0;
			}
		}
		
		foreach($T_lignes_box as &$s){
			if(!isset($s['new_datas'][10])){
				$s['new_datas'][10] = '';
			}
			if(!isset($s['new_datas'][18])){
				$s['new_datas'][18] = '';
			}
			if(!isset($s['new_datas'][26])){
				$s['new_datas'][26] = '';
			}
			if(!isset($s['new_datas'][90])){
				$s['new_datas'][90] = '';
			}
			if(!isset($s['new_datas'][94])){
				$s['new_datas'][94] = '';
			}
		}
		
		
	

        $Context->smarty->assign("T_lignes_box",$T_lignes_box);
        $Context->smarty->assign("T_stat",$T_stat);
        $Context->smarty->assign("T_stat_declinaisons",$STAT_declinaisons);
        $this->setTemplate('/box.tpl');
  
    }
}
