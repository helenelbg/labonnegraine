<?php

$lang = array(
    'dragAndDrop' => _l('Drag & drop'),
    'or' => _l('or'),
    'browse' => _l('Browse files'),
    'filesOrFoldersHere' => _l('files here'),
    'cancel' => _l('Cancel'),
    'clearAll' => _l('Clear all'),
    'clear' => _l('Clear'),
    'add' => _l('Add'),
    'upload' => _l('Upload'),
    'download' => _l('Download'),
    'error' => _l('error'),
    'byte' => _l('B'),
    'kilobyte' => _l('KB'),
    'megabyte' => _l('MB'),
    'gigabyte' => _l('GB'),
);
echo 'let set_vault_lang = '.json_encode($lang).';'."\n";
echo 'dhx.i18n.setLocale("vault", set_vault_lang);'."\n";
