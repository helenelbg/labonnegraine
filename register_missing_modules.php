<?php
require_once __DIR__.'/config/config.inc.php';
require_once __DIR__.'/init.php';

$db = Db::getInstance();
$moduleDir = _PS_MODULE_DIR_;
$modules = scandir($moduleDir);
$count = 0;

foreach ($modules as $moduleName) {
    if ($moduleName[0] === '.' || !is_dir($moduleDir . $moduleName)) {
        continue;
    }

    // Vérifie si le module est déjà enregistré
    $exists = $db->getValue('SELECT COUNT(*) FROM '._DB_PREFIX_.'module WHERE name = "'.pSQL($moduleName).'"');
    if (!$exists) {
        // Tu peux ajuster la version si besoin
        $db->execute('INSERT INTO '._DB_PREFIX_.'module (name, active, version) VALUES ("'.pSQL($moduleName).'", 0, "1.0.0")');
        echo "➕ Module ajouté : <strong>$moduleName</strong><br>";
        $count++;
    }
}

echo "<hr><strong>✅ $count module(s) ajouté(s).</strong>";
