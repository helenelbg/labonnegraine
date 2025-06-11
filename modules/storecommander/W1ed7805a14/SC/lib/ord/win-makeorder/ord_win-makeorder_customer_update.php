<?php

$id_lang = (int) Tools::getValue('id_lang', null);
$id_shop = (int) Tools::getValue('id_shop', Configuration::get('PS_SHOP_DEFAULT'));
$customer_data = Tools::getValue('customer_data', null);
if (!empty($id_lang) && !empty($id_shop) && !empty($customer_data))
{
    checkData($customer_data);
    $newCustomer = new Customer();
    $newCustomer->firstname = $customer_data['firstname'];
    $newCustomer->lastname = $customer_data['lastname'];
    $newCustomer->email = $customer_data['email'];
    if (version_compare(_PS_VERSION_, '1.7.0.1', '>='))
    {
        $newCustomer->passwd = Tools::hash($customer_data['passwd']);
    }
    else
    {
        $newCustomer->passwd = Tools::encrypt($customer_data['passwd']);
    }
    $newCustomer->id_shop = $id_shop;
    if (_s('CUS_USE_COMPANY_FIELDS') && SCI::getConfigurationValue('PS_B2B_ENABLE'))
    {
        $newCustomer->company = $customer_data['company'];
        $newCustomer->siret = $customer_data['siret'];
        $newCustomer->ape = $customer_data['ape'];
    }
    try
    {
        $res = $newCustomer->add();
        if (!$res)
        {
            exit('ERR:'._l('An error has occured when inserting'));
        }
        exit($newCustomer->id);
    }
    catch (PrestaShopException $e)
    {
        exit('ERR:'.$e->getMessage());
    }
    catch (Exception $e)
    {
        exit('ERR:'.$e->getMessage());
    }
}
else
{
    exit('ERR:'._l('Empty data'));
}

function checkData($customer_data)
{
    $error = array();
    foreach ($customer_data as $field => $value)
    {
        if (!empty($value))
        {
            switch ($field) {
                case 'firstname':
                    $checkup = Validate::isName($value);
                    if (!$checkup)
                    {
                        $error[] = _l('Invalid firstname');
                    }
                    break;
                case 'lastname':
                    $checkup = Validate::isName($value);
                    if (!$checkup)
                    {
                        $error[] = _l('Invalid lastname');
                    }
                    break;
                case 'email':
                    $checkup = Validate::isEmail($value);
                    if (!$checkup)
                    {
                        $error[] = _l('Invalid email');
                    }
                    break;
                case 'passwd':
                    if (version_compare(_PS_VERSION_, '8.0.0', '>='))
                    {
                        $checkup = Validate::isAcceptablePasswordLength($value);
                    }
                    else
                    {
                        $checkup = Validate::isPasswd($value);
                    }
                    if (!$checkup)
                    {
                        $error[] = _l('Invalid password');
                    }
                    break;
                case 'company':
                    $checkup = Validate::isGenericName($value);
                    if (!$checkup)
                    {
                        $error[] = _l('Invalid company');
                    }
                    break;
                case 'siret':
                    $checkup = Validate::isSiret($value);
                    if (!$checkup)
                    {
                        $error[] = _l('Invalid SIRET');
                    }
                    break;
                case 'ape':
                    $checkup = Validate::isApe($value);
                    if (!$checkup)
                    {
                        $error[] = _l('Invalid APE');
                    }
                    break;
            }
        }
    }
    if (!empty($error))
    {
        echo 'ERR:'.implode('<br/>', $error);
        exit;
    }
}
