<?php
// script qui permet de générer le composer.json à partir du composer.lock

$lock = json_decode(file_get_contents('composer.lock'), true);

$composer = [
    'name' => 'custom/generated-project',
    'description' => 'Généré automatiquement depuis composer.lock',
    'require' => []
];

foreach ($lock['packages'] as $package) {
    $name = $package['name'];
    $version = $package['version'];
    // Simplifie les versions genre "v1.2.3" => "^1.2"
    $versionClean = preg_replace('/^v?(\d+\.\d+).*/', '^$1', $version);
    $composer['require'][$name] = $versionClean;
}

file_put_contents('composer.json', json_encode($composer, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
echo "✅ composer.json généré.\n";
