<?php
if (!defined('STORE_COMMANDER')) { exit; }

$action = Tools::getValue('action', null);
$languages = Language::getLanguages(false, false);
$default_id_lang = Configuration::get('PS_LANG_DEFAULT');

$UkooArrayHeaders = Array(
    'Authorization: Bearer ' . UKOOPARTS_PANEL_API_TOKEN, // Ajoute le jeton d'accès dans l'en-tête
    'Content-Type: application/json',
    'Output-Format' => 'JSON'
);

$UkooArrayContent = Array();

if (!empty($action))
{
    switch ($action) {
        case 'redirect_bo' :
            $idLastProduct = Tools::getValue('id_product_ps');
            $responseGet = json_decode(sc_file_get_contents(UKOOPARTS_API_BASE_URL . 'products/' . $idLastProduct, 'GET', $UkooArrayContent, $UkooArrayHeaders), true);
            $BOurl = 'https://' . UKOOPARTS_PANEL_DOMAIN . '/produits/' . $responseGet['response'][0]['id'];
            exit($BOurl);
        case 'insert':
            if (strpos(Tools::getValue('product_id'),',')) $product_ids = explode(',',Tools::getValue('product_id'));
            else $product_ids[] = Tools::getValue('product_id');

            foreach ($product_ids as $product_id) {
                $UkooArrayContent = array(
                    'modele_id' => Tools::getValue('model'),
                    'product_id' => $product_id,
                );
                if (!empty(Tools::getValue('year'))) $UkooArrayContent['year'] = Tools::getValue('year');

                $response = json_decode(sc_file_get_contents(UKOOPARTS_API_BASE_URL . 'compats/store', 'PUT', $UkooArrayContent, $UkooArrayHeaders, null, true), true);
            }
            break;
        case 'insert_with_period':
            if (strpos(Tools::getValue('product_id'),',')) $product_ids = explode(',',Tools::getValue('product_id'));
            else $product_ids[] = Tools::getValue('product_id');

            $period = explode(',',Tools::getValue('period'));
            foreach ($product_ids as $product_id) {
                foreach ($period as $year) {
                    $ArrayOneCompat = array(
                        'modele_id' => Tools::getValue('model'),
                        'product_id' => $product_id,
                        'year' => $year
                    );
                    $UkooArrayContent['compats'][] = $ArrayOneCompat;
                }
                $response = json_decode(sc_file_get_contents(UKOOPARTS_API_BASE_URL . 'compats/bulkstore', 'PUT', $UkooArrayContent, $UkooArrayHeaders, null, true), true);
            }
            break;
        case 'update':
            $nValue = Tools::getValue('value');
            $idRow = Tools::getValue('id_row');
            $idArray = explode('_', $idRow);
            // suppression
            $UkooArrayContent = array("modele_id" => $idArray[1], "product_id" => $idArray[0]);
            $UkooArrayContent["year"] = (!empty($idArray[2])) ? $idArray[2] : null;
            $responseDelete = json_decode(sc_file_get_contents(UKOOPARTS_API_BASE_URL . 'compats', 'DELETE', $UkooArrayContent, $UkooArrayHeaders, null, true), true);
            // insertion
            $UkooArrayContent = array(
                'modele_id' => $idArray[1],
                'product_id' => $idArray[0]
            );
            if ($nValue!="") $UkooArrayContent['year'] = $nValue;
            $response = json_decode(sc_file_get_contents(UKOOPARTS_API_BASE_URL . 'compats/store', 'PUT', $UkooArrayContent, $UkooArrayHeaders, null, true), true);
            break;
        case 'compat_delete':
            $idRows = Tools::getValue('compats');
            $compatsArray = explode(',', $idRows);
            $chunks_compats = array_chunk($compatsArray, 1000);
            foreach ($chunks_compats as $chunk) {
                $UkooArrayContent = array();
                foreach ($chunk as $idRow) {
                    $idArray = explode('_', $idRow);
                    $ArrayOneCompat = array("modele_id" => $idArray[1], "product_id" => $idArray[0]);
                    $ArrayOneCompat["year"] = (!empty($idArray[2])) ? $idArray[2] : null;
                    $UkooArrayContent['compats'][] = $ArrayOneCompat;
                }
                $response = json_decode(sc_file_get_contents(UKOOPARTS_API_BASE_URL . 'compats/bulkdestroy', 'DELETE', $UkooArrayContent, $UkooArrayHeaders, null, true), true);
            }
            break;
        case 'paste': ## Copy/Paste compats from prop
            $ids = explode(',', Tools::getValue('ids_target'));
            $compats_source = explode(',', Tools::getValue('compat_source'));
            $chunks_cs = array_chunk($compats_source, 1000);
            foreach ($ids as $id_target) {
                foreach ($chunks_cs as $chunk_cs) {
                    $UkooArrayContent = array();
                    foreach ($chunk_cs as $compat_source) {
                        $compat_source = explode('_', $compat_source);
                        if ($id_target != $compat_source[0]) {
                            $ArrayOneCompat = array('modele_id' => $compat_source['1'], 'product_id' => $id_target);
                            if (!empty($compat_source['2'])) $ArrayOneCompat['year'] = $compat_source['2'];
                            $UkooArrayContent['compats'][] = $ArrayOneCompat;
                        }
                    }
                    $response = json_decode(sc_file_get_contents(UKOOPARTS_API_BASE_URL . 'compats/bulkstore', 'PUT', $UkooArrayContent, $UkooArrayHeaders, null, true), true);
                }
            }
            break;
        case 'sync_database':
            $ModeleArray = array();
            $idLastProduct = Tools::getValue('id_product_ps');
            $responseGet = json_decode(sc_file_get_contents(UKOOPARTS_API_BASE_URL . 'compats/products/' . $id_productsource, 'GET', $UkooArrayContent, $UkooArrayHeaders), true);
            if ($responseGet['meta']['code'] == 200) {
                foreach ($responseGet['response'][0] as $compat)
                    $ModeleArray[$compat['modele_id']] = $compat['modele_id'];
                foreach ($ModeleArray as $mod_id)
                    $UkooArrayContent = array(
                        'scope' => "ModeleAssociation",
                        'onlyFor' => $mod_id
                    );
                $response = json_decode(sc_file_get_contents(UKOOPARTS_API_BASE_URL . 'export_to/prestashop', 'PUT', $UkooArrayContent, $UkooArrayHeaders, null, true), true);
            }
            break;
    }
    if ($response['meta']['code'] == 200) exit('OK');
    else exit(_l(trim($response['meta']['errorDetails'])));
}
