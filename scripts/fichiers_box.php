<?php

include('../config/config.inc.php');
include('../init.php');
$req_cmd = 'SELECT * FROM `'._DB_PREFIX_.'orders` as o WHERE id_order = "227233" ';
//echo $req_cmd;   INNER JOIN ps_order_carrier as car ON car.id_order=o.id_order
$req_traiter = Db::getInstance()->executeS($req_cmd);
//echo $req_traiter;
//print_r($req_traiter);

foreach ($req_traiter as $une_cmd) {
  $une_cmd['{firstname}'] =  $une_cmd['firstname'];
  unset($une_cmd['firstname']);
  $une_cmd['{lastname}'] =  $une_cmd['lastname'];
  unset($une_cmd['lastname']);
  $une_cmd['{payment}'] =  $une_cmd['payment'];
  unset($une_cmd['payment']);
  $une_cmd['{total_products}'] =  $une_cmd['total_products'];
  unset($une_cmd['total_products']);
  $une_cmd['{total_discounts}'] =  $une_cmd['total_discounts'];
  unset($une_cmd['total_discounts']);
  $une_cmd['{total_wrapping}'] =  $une_cmd['total_wrapping'];
  unset($une_cmd['total_wrapping']);
  $une_cmd['{total_shipping}'] =  $une_cmd['total_shipping'];
  unset($une_cmd['total_shipping']);
  $une_cmd['{total_paid}'] =  $une_cmd['total_paid'];
  unset($une_cmd['total_paid']);
  $une_cmd['{date}'] =  $une_cmd['date_add'];
  unset($une_cmd['date_add']);
  $une_cmd['{delivery_info}'] =  $une_cmd['delivery_info'];
  unset($une_cmd['delivery_info']);
  $order = new Order($une_cmd['id_order']);
  $delivery = new Address((int)$order->id_address_delivery);
  $invoice = new Address((int)$order->id_address_invoice);
  $carrier = new Carrier((int)$order->id_carrier);
  $customer = new Customer((int)$order->id_customer);
  $cart = new Cart((int)$order->id_cart);
  $context=Context::getContext();
      $une_cmd['{order_name}'] = $order->getUniqReference();
      $une_cmd['{delivery_company}'] = $delivery->company;
      $une_cmd['{delivery_firstname}'] = $delivery->firstname;
      $une_cmd['{delivery_lastname}'] = $delivery->lastname;
      $une_cmd['{delivery_address1}'] = $delivery->address1;
      $une_cmd['{delivery_address2}'] = $delivery->address2;
      $une_cmd['{delivery_city}'] = $delivery->city;
      $une_cmd['{delivery_postal_code}'] = $delivery->postcode;
      $une_cmd['{delivery_country}'] = $delivery->country;
      $une_cmd['{delivery_state}'] = $delivery->id_state ? $delivery_state->name : '';
      $une_cmd['{delivery_phone}'] = $delivery->phone ? $delivery->phone : $delivery->phone_mobile;
      $une_cmd['{delivery_other}'] = $delivery->other;
      $une_cmd['{invoice_company}'] = $invoice->company;
      $une_cmd['{invoice_firstname}'] = $invoice->firstname;
      $une_cmd['{invoice_lastname}'] = $invoice->lastname;
      $une_cmd['{invoice_address2}'] = $invoice->address2;
      $une_cmd['{invoice_address1}'] = $invoice->address1;
      $une_cmd['{invoice_city}'] = $invoice->city;
      $une_cmd['{invoice_postal_code}'] = $invoice->postcode;
      $une_cmd['{invoice_country}'] = $invoice->country;
      $une_cmd['{invoice_state}'] = $invoice->id_state ? $invoice_state->name : '';
      $une_cmd['{invoice_phone}'] = $invoice->phone ? $invoice->phone : $invoice->phone_mobile;
      $une_cmd['{invoice_other}'] = $invoice->other;
      $une_cmd['{carrier}'] = (($carrier->name == '0') ? $configuration['PS_SHOP_NAME'] : $carrier->name);
      $products = $order->getProducts();
      $customized_datas = Product::getAllCustomizedDatas((int)$cart->id);
      //print_r($customized_datas);
      Product::addCustomizationPrice($products, $customized_datas);
      $produit = false;
      $items_table = '';
      foreach ($products as $key => $product)
      {
        if($product["product_id"]==1849 || $product["product_id"]==1850 || $product["product_id"]==1851){
          $produit = true;
          $myid_customization=$product["id_customization"];
        }
        $unit_price = Product::getTaxCalculationMethod($customer->id) == PS_TAX_EXC ? $product['product_price'] : $product['product_price_wt'];

        $customization_text = '';
        if (isset($customized_datas[$product['product_id']][$product['product_attribute_id']]))
        {
          foreach ($customized_datas[$product['product_id']][$product['product_attribute_id']][$order->id_address_delivery] as $customization)
          {
            if (isset($customization['datas'][Product::CUSTOMIZE_TEXTFIELD]))
              foreach ($customization['datas'][Product::CUSTOMIZE_TEXTFIELD] as $text)
                $customization_text .= $text['name'].': '.$text['value'].'<br />';

            if (isset($customization['datas'][Product::CUSTOMIZE_FILE]))
              $customization_text .= count($customization['datas'][Product::CUSTOMIZE_FILE]).'image(s)<br />';

            $customization_text .= '---<br />';
          }
          if (method_exists('Tools', 'rtrimString'))
            $customization_text = Tools::rtrimString($customization_text, '---<br />');
          else
            $customization_text = preg_replace('/---<br \/>$/', '', $customization_text);
        }

              $url = $context->link->getProductLink($product['product_id']);
        $items_table .=
          '<tr style="background-color:'.($key % 2 ? '#DDE2E6' : '#EBECEE').';">
            <td style="padding:0.6em 0.4em;">'.$product['product_reference'].'</td>
            <td style="padding:0.6em 0.4em;">
              <strong><a href="'.$url.'">'.$product['product_name'].'</a>'
                                  .(isset($product['attributes_small']) ? ' '.$product['attributes_small'] : '')
                                  .(!empty($customization_text) ? '<br />'.$customization_text : '')
                              .'</strong>
            </td>
            <td style="padding:0.6em 0.4em; text-align:right;">'.Tools::displayPrice($unit_price, $currency, false).'</td>
            <td style="padding:0.6em 0.4em; text-align:center;">'.(int)$product['product_quantity'].'</td>
            <td style="padding:0.6em 0.4em; text-align:right;">'
              .Tools::displayPrice(($unit_price * $product['product_quantity']), $currency, false)
            .'</td>
          </tr>';
      }
      foreach ($order->getCartRules() as $discount)
      {
        $items_table .=
          '<tr style="background-color:#EBECEE;">
              <td colspan="4" style="padding:0.6em 0.4em; text-align:right;">Voucher code:'.$discount['name'].'</td>
            <td style="padding:0.6em 0.4em; text-align:right;">-'.Tools::displayPrice($discount['value'], $currency, false).'</td>
        </tr>';
      }
 $une_cmd['{products}'] = $items_table;
 $une_cmd['{discounts}'] = "";

      if($produit == true){
        //$myid_customization = 523;
        $attachment ="";
      /*  $file=dirname(__FILE__)."/fichiers_box/box_".$myid_customization.".pdf";
        $attachment['content']= file_get_contents($file);
         $attachment['name']="box_".$myid_customization.".pdf";
          $attachment['mime']= 'application/pdf';*/


                        $mycont=Context::getContext();
                        $mycart=new Cart((int)$order->id_cart);
                        $T_myproducts=$mycart->getProducts(true);
                        $T_concordances[11763]=1;
                        $T_concordances[11764]=2;
                        $T_concordances[11765]=3;
                        $T_concordances[11766]=4;
                        $T_concordances[11767]=1;
                        $T_concordances[11768]=2;
                        $T_concordances[11769]=3;
                        $T_concordances[11770]=4;
                        $T_concordances[11771]=1;
                        $T_concordances[11772]=2;
                        $T_concordances[11773]=3;
                        $T_concordances[11774]=4;
                        $T_concordances[11775]=1;
                        $T_concordances[11776]=2;
                        $T_concordances[11777]=3;
                        $T_concordances[11778]=4;

                        $mycustomer= new Customer($mycart->id_customer);
//error_reporting(E_ALL);
//ini_set('display_errors', 1);
                       //fr/new_order
                        //  mail("valentin.j@anjouweb.com", "debug box template + product",print_r($template,true).print_r($T_myproducts,true).print_r($mycart,true));
                     // if ($template == 'fr/order_conf' || $template=='fr/order_conf_box') {
                            require_once "../classes/pdf/HTMLTemplatePdfbox.php";
                            require_once "../classes/pdf/PDFGenerator_paysage.php";
                            require_once "../classes/pdf/PDF_paysage.php";
                            require_once "../classes/tableau.php";
                            //mail("valentin.j@anjouweb.com","debugmyproduct",print_r($T_myproducts,true));
                            //var_dump($T_myproducts);
                        foreach ($T_myproducts as $T_myproduct){
                            if($T_myproduct["id_product"]==1849 || $T_myproduct["id_product"]==1850 || $T_myproduct["id_product"]==1851){
                                error_log(print_r($T_myproduct, true));
                                $mycustomername=$mycustomer->firstname." ".$mycustomer->lastname;
                                $myproductname=$T_myproduct["name"];
                                $myproductattributeid=$T_myproduct["id_product_attribute"];
                                $myid_customization=$T_myproduct["id_customization"];

                                var_dump($myid_customization);
                                $req="SELECT value
                                        FROM ps_customized_data
                                        WHERE id_customization ='".$myid_customization."'
                                        ORDER BY ps_customized_data.index ASC ";
                                $T_customize = Db::getInstance()->executeS($req);
                                //var_dump($T_customize);
                                $mydestinataire=$T_customize[1]["value"]." ".$T_customize[0]["value"];
                                $mynbsaisons=$T_concordances[$myproductattributeid];
                                $tableau= array();
                                $tableau[]=array($mydestinataire,$mycustomername,$myproductname,$mynbsaisons);
                                //var_dump($myproductattributeid);

                            $T_PDF= new Tableau();
                            $T_PDF->tableau=$tableau;
                            $T_PDF->titre="Expedition box";
                            //global $AW_outfile;
                            //$AW_outfile="liste_factures.pdf";
                            //var_dump($T_PDF);
                           // echo "cccc";
                            $filename=dirname(__FILE__)."/../fichiers_box/box_".$myid_customization.".pdf";
                            $pdf = new PDF_paysage($T_PDF, 'pdfBox', Context::getContext()->smarty);

                            $pdf->render($filename);


                                //$file = _PS_ROOT_DIR_ . '/'.$filename;
                                $file = $filename;
                                //error_log('DEBUT ATTACHEMENT PDF BOX : '.$file);
                                sleep(2);
                                // $attachment_box = new Swift_Message_Attachment(file_get_contents($file), "box_".$myid_customization.".pdf", 'application/pdf');
                                 $attachment['content']= file_get_contents($file);
         $attachment['name']="box_".$myid_customization.".pdf";
          $attachment['mime']= 'application/pdf';
                                //error_log('MILIEU ATTACHEMENT PDF BOX : '.$file);
                               // $message->attach($attachment_box);
                                //error_log('FIN ATTACHEMENT PDF BOX : '.$file);
                            } // fin si box
                            // var_dump($tableau);

                        }
                  //  }



      print_r($une_cmd);
               /* Mail::Send(
                  2,
                  'order_conf_box',
                  Mail::l('Order confirmation', 2),
                  $une_cmd,
                  $customer->email,
                 $customer->email,
                  null,
                  null,
                  $attachment,
                  null, _PS_MAIL_DIR_, false, 1,'guillaume@anjouweb.com'
                );*/
               //die;
              }
}
  ?>
