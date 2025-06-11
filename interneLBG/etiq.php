<?php 
    //error_log('GG POSTE : '.$_GET['poste']);
    if ( $_GET['poste'] == 'dpl' )
    {
      //error_log('GG AW1');
    $base64 = base64_encode(file_get_contents('test2.dpl'));
      echo 'http://localhost:8000/imprimerEtiquetteThermique?port=USB&protocole=DATAMAX&adresseIp=&etiquette='.$base64;
    }
    elseif ( $_GET['poste'] == 'zpl' )
    {
        // error_log('GG AW2');
       $base64 = base64_encode(file_get_contents('test2.zpl'));
         echo 'http://localhost:8000/imprimerEtiquetteThermique?port=USB&protocole=ZEBRA&adresseIp=&etiquette='.$base64;
         // error_log('GG AW2');
        $base64 = base64_encode(file_get_contents('test3.zpl'));
          echo '<br />http://localhost:8000/imprimerEtiquetteThermique?port=USB&protocole=ZEBRA&adresseIp=&etiquette='.$base64;
          // error_log('GG AW2');
         $base64 = base64_encode(file_get_contents('test4.zpl'));
           echo '<br />http://localhost:8000/imprimerEtiquetteThermique?port=USB&protocole=ZEBRA&adresseIp=&etiquette='.$base64;
    }
?>