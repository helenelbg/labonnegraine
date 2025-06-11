<?php
require 'application_top.php';

if (isset($_POST['id_test']) && !empty($_POST['id_test'])) {
    $id_test = (int)$_POST['id_test'];
    
    $sql = 'DELETE FROM AW_test_lots WHERE id = "'.$id_test.'"';
    
    if (Db::getInstance()->execute($sql)) {
        echo 'ok';
    } else {
        echo 'error';
    }
} else {
    echo 'missing_params';
}
?>