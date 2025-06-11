<?php
if (!defined('STORE_COMMANDER')) { exit; }

error_reporting(E_ERROR | E_PARSE);
ini_set('display_errors', 1);

## Copy/Paste compats from cat_product_grid (view references)
if (isset($_POST['parts_compatibilities']) && substr(Tools::getValue('parts_compatibilities'), 0, 22) == 'parts_compatibilities_')
{
    $prefixlen = strlen('parts_compatibilities_');
    $id_productsource = (int) substr(Tools::getValue('parts_compatibilities'), $prefixlen, strlen(Tools::getValue('parts_compatibilities')));

    if ($id_productsource != $id_product)
    {
        $UkooArrayHeaders = Array(
            'Authorization: Bearer ' . UKOOPARTS_PANEL_API_TOKEN,
            'Content-Type: application/json',
            'Output-Format' => 'JSON'
        );

        $response = json_decode(sc_file_get_contents(UKOOPARTS_API_BASE_URL.'compats/products/'.$id_productsource, 'GET', $UkooArrayContent, $UkooArrayHeaders), true);
        if ($response['meta']['code']==200){
            $compatibilities = $response['response'][0];
            $chunks = array_chunk($compatibilities, 100);

            foreach ($chunks as $chunk) {
                $bulkInsertArray = array();

                foreach ($chunk as $compat){
                    $ArrayOneCompat = Array('modele_id'=>$compat['modele_id'], 'product_id'=>$id_product);
                    if (!empty($compat['year'])) $ArrayOneCompat['year'] = $compat['year'];

                    $bulkInsertArray['compats'][] = $ArrayOneCompat;
                }
                $responseInsert = json_decode(sc_file_get_contents(UKOOPARTS_API_BASE_URL.'compats/bulkstore', 'PUT', $bulkInsertArray, $UkooArrayHeaders, null, true), true);
            }
        }
    }
}


