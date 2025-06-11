<?php

$url = 'https://www.storecommander.com/eservices_encart.php?lang='.($user_lang_iso == 'fr' ? 'fr' : 'en').'&lic='.sha1(SCI::getConfigurationValue('SC_LICENSE_KEY'));

$content = file_get_contents($url);

echo $content;
