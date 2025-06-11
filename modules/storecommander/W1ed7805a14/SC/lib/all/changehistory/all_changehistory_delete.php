<?php

    $sql = 'TRUNCATE '._DB_PREFIX_.'storecom_history';
    Db::getInstance()->Execute($sql);
