<?php
if (!defined('STORE_COMMANDER')) { exit; }

$content = trim(file_get_contents("php://input"));
$scc_data = json_decode($content, true);

$shop = $scc_data['data_shop'];
$dealer = $scc_data['data_dealers'][array_key_first($scc_data['data_dealers'])];

$result = [];

// CUSTOMER ############################################################################################################
if ($scc_data['data_id_customer'] == null)
{
    $new_cus = new Customer();
    $new_cus->id_gender = $dealer['cus_gender'];
    $new_cus->firstname = $dealer['cus_firstname'];
    $new_cus->lastname = $dealer['cus_lastname'];
    $new_cus->company = $dealer['addr_company'];
    $new_cus->email = $dealer['cus_email_invoice'];

    //$new_cus->note = $dealer['note'];;

    $new_cus->birthday = '2020-01-01';
    $new_cus->reset_password_validity = '2030-01-01';
    $new_cus->newsletter_date_add = '2030-01-01';

    $new_cus->newsletter = false;
    $new_cus->optin = false;

    if (version_compare(_PS_VERSION_, '1.7.0.1', '>=')) {
        $new_cus->passwd = Tools::hash($dealer['addr_company']);
    } else {
        $new_cus->passwd = Tools::encrypt($dealer['addr_company']);
    }
    $new_cus->siret = $dealer['cus_siret'];
    $new_cus->note = $dealer['note'];
    $new_cus->id_shop = $shop['data_shop_id'];

    try
    {
        $res = $new_cus->add();
        if (!$res) $result['error'] = 'ERROR:' . _l('An error has occured when inserting');
        else $result['inserted_cus'] = $new_cus->id;
    }
    catch (PrestaShopException $e)
    {
        $result['error'] = 'ERROR:'.$e->getMessage();
    }
    catch (Exception $e)
    {
        $result['error'] = 'ERROR:'.$e->getMessage();
    }
}

// ADDRESS #############################################################################################################
if ($scc_data['data_id_address'] == null)
{
    $new_addr = new Address();
    $new_addr->alias = $dealer['addr_alias'];
    $new_addr->firstname = $dealer['cus_firstname'];
    $new_addr->lastname = $dealer['cus_lastname'];
    $new_addr->address1 = $dealer['addr_address1'];
    $new_addr->address2 = $dealer['addr_address2'];
    $new_addr->postcode = $dealer['addr_zipcode'];
    $new_addr->city = $dealer['addr_city'];
    $new_addr->id_country = Country::getIdByName(null, $dealer['addr_country']);
    $new_addr->id_customer = (isset($result['inserted_cus'])) ? $result['inserted_cus'] : $scc_data['data_id_customer'];
    $new_addr->vat_number = $dealer['addr_vat_number'];
    try
    {
        $res = $new_addr->add();
        if (!$res) $result['error'] = 'ERROR:' . _l('An error has occured when inserting');
        else $result['inserted_addr'] = $new_addr->id;
    }
    catch (PrestaShopException $e)
    {
        $result['error'] = 'ERROR:'.$e->getMessage();
    }
    catch (Exception $e)
    {
        $result['error'] = 'ERROR:'.$e->getMessage();
    }
}

// CATEGORY ############################################################################################################
if ($scc_data['data_id_category'] == null)
{
    $new_cat = new Category();
    $name = 'eCartes BUM Store Commander';
    foreach ($languages as $lang)
    {
        $new_cat->link_rewrite[$lang['id_lang']] = link_rewrite($name, $lang['iso_code']);
        $new_cat->name[$lang['id_lang']] = $name;
    }
    $new_cat->active = false;
    $new_cat->position = 100000;
    $new_cat->id_shop_list[] = $shop['data_shop_id'];

    try
    {
        $res = $new_cat->add();
        if (!$res) $result['error'] = 'ERROR:' . _l('An error has occured when inserting');
        else $result['inserted_cat'] = $new_cat->id;
    }
    catch (PrestaShopException $e)
    {
        $result['error'] = 'ERROR:'.$e->getMessage();
    }
    catch (Exception $e)
    {
        $result['error'] = 'ERROR:'.$e->getMessage();
    }
}

// PRODUCTS ############################################################################################################
$productList = [20 , 30 , 50 ,100];
foreach ($productList as $amount)
{
    if ($scc_data['data_id_product'.$amount] == null) {
        $new_prd = new Product();
        $new_prd->name = 'eCartes Sc BUM '.$amount.'€ (hors champs TVA art. 256 ter CGI)';
        $new_prd->reference = 'SCCeCarte'.$amount;
        $new_prd->quantity = 10000;
        $new_prd->price = $amount;
        $new_prd->active = false;
        $new_prd->visibility = 'none';
        $new_prd->category = result['inserted_cat'];

        $new_prd->id_shop_list[] = $shop['data_shop_id'];

        try
        {
            $res = $new_prd->add();
            if (!$res) $result['error'] = 'ERROR:' . _l('An error has occured when inserting');
            else $result['inserted_prd'.$amount] = $new_prd->id;
        } catch (PrestaShopException $e) {
            $result['error'] = 'ERROR:' . $e->getMessage();
        } catch (Exception $e) {
            $result['error'] = 'ERROR:' . $e->getMessage();
        }
    }
}

// CATEGORY_PRODUCT ####################################################################################################
$sql = 'INSERT INTO `'._DB_PREFIX_.'category_product` (id_product,id_category,position) VALUES
('. (int) $result['inserted_prd20'].','. (int) $result['inserted_cat'].',100000),
('. (int) $result['inserted_prd30'].','. (int) $result['inserted_cat'].',100000),
('. (int) $result['inserted_prd50'].','. (int) $result['inserted_cat'].',100000),
('. (int) $result['inserted_prd100'].','. (int) $result['inserted_cat'].',100000)';
Db::getInstance()->execute($sql);

// APPEL API POUR UPDATE LA TABLE SHOP #################################################################################
$post = array(
    'id_scc_shop' => $shop['id_scc_shop'],
    'inserted_cus' => (isset($result['inserted_cus'])) ? $result['inserted_cus'] : null,
    'inserted_addr' => (isset($result['inserted_addr'])) ? $result['inserted_addr'] : null,
    'inserted_cat' => (isset($result['inserted_cat'])) ? $result['inserted_cat'] : null,
    'inserted_prd20' => (isset($result['inserted_prd20'])) ? $result['inserted_prd20'] : null,
    'inserted_prd30' => (isset($result['inserted_prd30'])) ? $result['inserted_prd30'] : null,
    'inserted_prd50' => (isset($result['inserted_prd50'])) ? $result['inserted_prd50'] : null,
    'inserted_prd100' => (isset($result['inserted_prd100'])) ? $result['inserted_prd100'] : null
);

//$result['error']='ERROR';

$response=makeDefaultCallToOurApi("scc/updateShopWithPSEntitiesIds.php", array(), $post);

echo json_encode($result);

?>
