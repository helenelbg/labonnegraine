<?php 
die;
    $api_key = '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';
    $api_url = 'https://api.openai.com/v1/chat/completions';

    $prompt = 'Résume moi cela en 250 caractères maximum : "Nom botanique : Solanum melongena

Repiquage : Sensible au froid, l\'aubergine ne sera mise en terre que lorsque le sol sera bien réchauffé (fin mai pour le Nord de la Loire, fin avril-début mai au Sud), quand les plants auront 5 ou 6 feuilles. Préparez un trou profond d\'une hauteur de godet et installez le plant d\'aubergine au centre et refermez avec de la terre fine en tassant légèrement. Arrosez abondamment pour favoriser l\'enracinement. Ensuite, n\'arrosez que s\'il est nécessaire. Pour le savoir, grattez au pied de votre plant, si la terre n\'est pas humide à 10 cm de profondeur, vous pouvez arroser.

Entretien du sol : Binages et sarclages réguliers. Un paillage en été pourra être nécessaire car la plante aime l\'eau autant que la chaleur.

Taille éventuelle : Pas de taille.

Arrosage : Arrosages réguliers, fréquents mais peu abondants.

Récolte : De juillet jusqu\'aux gelées, on récolte les fruits en fonction de ses besoins et on les consomme rapidement. L\'aubergine se cueille un peu avant sa maturité complète.

Conservation : Se conserve quelques jours au bas du réfrigérateur."';

    $data = array(
        'model' => 'gpt-3.5-turbo',
        'messages' => array(
            array('role' => 'system', 'content' => 'Vous êtes un assistant'),
            array('role' => 'user', 'content' => $prompt)
        ));

        $ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $api_url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    'Content-Type: application/json',
    'Authorization: Bearer ' . $api_key
));

$response = curl_exec($ch);
curl_close($ch);
var_dump($response);

if ($response) {
    $response_data = json_decode($response, true);
    $response_prod = $response_data['choices'][0]['message']['content'];
    var_dump($response_prod) ;
} else {
    echo "Une erreur s'est produite lors de l'appel à l'API.";
}
?>