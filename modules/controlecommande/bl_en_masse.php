<?php
    include('../../config/config.inc.php');
    include('../../init.php');

    $_GET['poste'] == 'controle2';
    $links = array();
    $ids = array();

    function startsWith( $haystack, $needle ) {
		$length = strlen( $needle );
		return substr( $haystack, 0, $length ) === $needle;
	}
    if ( isset($_FILES['file']) && !empty($_FILES['file']) )
    {
        $uploaddir = dirname(__FILE__).'/';
        $uploadfile = $uploaddir . basename($_FILES['file']['name']);

        move_uploaded_file($_FILES['file']['tmp_name'], $uploadfile);
        $ligne = 0;
        //echo dirname(__FILE__).'/'.$_FILES['file']['name'];
        if (($file = fopen(dirname(__FILE__).'/'.$_FILES['file']['name'], "r")) !== false) {

            while (($data = fgetcsv($file, 1000, ";")) !== false) {

                if ($ligne != 0) {

                    $_POST['id_order'] = $data[0];
                    $ids[] = $data[0];
                  }
                  $ligne++;
                }






                }
            }


?>
<html>
    <head>
          <script src="https://code.jquery.com/jquery-1.12.4.js"></script>
    </head>
    <body>
        <form action="bl_en_masse.php" method="POST" enctype="multipart/form-data">
            <input name="file" type="file" />
            <input type="submit" value="Imprimer" />
        </form>
        <?php

            if ( isset($ids) && count($ids) > 0 )
            {
            $implode = implode('-', $ids);
                ?>
                    <script language="Javascript">
                        function imprim()
                        {

                        }

                        $( document ).ready(function() {
                            <?php
                                echo 'window.open("https://dev.labonnegraine.com/admin123/test_etiquettes2.php?deliveryslipsadmin='.$implode.'");';

                            ?>
                        });
                    </script>
                <?php
            }
        ?>
    </body>
</html>
