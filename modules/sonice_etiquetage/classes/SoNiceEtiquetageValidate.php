<?php
/**
 * NOTICE OF LICENSE
 *
 * This source file is subject to a commercial license from Common-Services Co., Ltd.
 * Use, copy, modification or distribution of this source file without written
 * license agreement from the SARL SMC is strictly forbidden.
 * In order to obtain a license, please contact us: contact@common-services.com
 * ...........................................................................
 * INFORMATION SUR LA LICENCE D'UTILISATION
 *
 * L'utilisation de ce fichier source est soumise a une licence commerciale
 * concedee par la societe Common-Services Co., Ltd.
 * Toute utilisation, reproduction, modification ou distribution du present
 * fichier source sans contrat de licence ecrit de la part de la Common-Services Co. Ltd. est
 * expressement interdite.
 * Pour obtenir une licence, veuillez contacter Common-Services Co., Ltd. a l'adresse: contact@common-services.com
 * ...........................................................................
 *
 * @package   SoNice_Etiquetage
 * @author    Alexandre D. <alexandre@common-services.com>
 * @copyright Copyright (c) 2011-2015 Common Services Co Ltd - 90/25 Sukhumvit 81 - 10260 Bangkok - Thailand
 * @license   Commercial license
 * Support by mail  :  support.sonice@common-services.com
 */

class SoNiceEtiquetageValidate
{

    public static $country_calling_codes = array(
        'AF' => '93',
        'AL' => '355',
        'DZ' => '213',
        'AD' => '376',
        'AO' => '244',
        'AQ' => '672',
        'AR' => '54',
        'AM' => '374',
        'AW' => '297',
        'AU' => '61',
        'AT' => '43',
        'AZ' => '994',
        'BH' => '973',
        'BD' => '880',
        'BY' => '375',
        'BE' => '32',
        'BZ' => '501',
        'BJ' => '229',
        'BT' => '975',
        'BO' => '591',
        'BQ' => '599',
        'BA' => '387',
        'BW' => '267',
        'BV' => '47',
        'BR' => '55',
        'IO' => '246',
        'BN' => '673',
        'BG' => '359',
        'BF' => '226',
        'BI' => '257',
        'KH' => '855',
        'CM' => '237',
        'CA' => '1',
        'CV' => '238',
        'CF' => '236',
        'TD' => '235',
        'CL' => '56',
        'CN' => '86',
        'CX' => '61',
        'CC' => '61',
        'CO' => '57',
        'KM' => '269',
        'CG' => '242',
        'CD' => '243',
        'CK' => '682',
        'CR' => '506',
        'HR' => '385',
        'CU' => '53',
        'CW' => '599',
        'CY' => '357',
        'CZ' => '420',
        'CI' => '225',
        'DK' => '45',
        'DJ' => '253',
        'EC' => '593',
        'EG' => '20',
        'SV' => '503',
        'GQ' => '240',
        'ER' => '291',
        'EE' => '372',
        'ET' => '251',
        'FK' => '500',
        'FO' => '298',
        'FJ' => '679',
        'FI' => '358',
        'FR' => '33',
        'GF' => '594',
        'PF' => '689',
        'TF' => '262',
        'GA' => '241',
        'GM' => '220',
        'GE' => '995',
        'DE' => '49',
        'GH' => '233',
        'GI' => '350',
        'GR' => '30',
        'GL' => '299',
        'GP' => '590',
        'GT' => '502',
        'GG' => '44',
        'GN' => '224',
        'GW' => '245',
        'GY' => '592',
        'HT' => '509',
        'HM' => '672',
        'HN' => '504',
        'HK' => '852',
        'HU' => '36',
        'IS' => '354',
        'IN' => '91',
        'ID' => '62',
        'IR' => '98',
        'IQ' => '964',
        'IE' => '353',
        'IM' => '44',
        'IL' => '972',
        'IT' => '39',
        'JP' => '81',
        'JE' => '44',
        'JO' => '962',
        'KZ' => '7',
        'KE' => '254',
        'KI' => '686',
        'KP' => '850',
        'KR' => '82',
        'KW' => '965',
        'KG' => '996',
        'LA' => '856',
        'LV' => '371',
        'LB' => '961',
        'LS' => '266',
        'LR' => '231',
        'LY' => '218',
        'LI' => '423',
        'LT' => '370',
        'LU' => '352',
        'MO' => '853',
        'MK' => '389',
        'MG' => '261',
        'MW' => '265',
        'MY' => '60',
        'MV' => '960',
        'ML' => '223',
        'MT' => '356',
        'MH' => '692',
        'MQ' => '596',
        'MR' => '222',
        'MU' => '230',
        'YT' => '262',
        'MX' => '52',
        'FM' => '691',
        'MD' => '373',
        'MC' => '33',
        'MN' => '976',
        'ME' => '382',
        'MA' => '212',
        'MZ' => '258',
        'MM' => '95',
        'NA' => '264',
        'NR' => '674',
        'NP' => '977',
        'NL' => '31',
        'NC' => '687',
        'NZ' => '64',
        'NI' => '505',
        'NE' => '227',
        'NG' => '234',
        'NU' => '683',
        'NF' => '672',
        'NO' => '47',
        'OM' => '968',
        'PK' => '92',
        'PW' => '680',
        'PS' => '970',
        'PA' => '507',
        'PG' => '675',
        'PY' => '595',
        'PE' => '51',
        'PH' => '63',
        'PN' => '870',
        'PL' => '48',
        'PT' => '351',
        'PR' => '1',
        'QA' => '974',
        'RO' => '40',
        'RU' => '7',
        'RW' => '250',
        'RE' => '262',
        'BL' => '590',
        'MF' => '590',
        'PM' => '508',
        'WS' => '685',
        'SM' => '378',
        'ST' => '239',
        'SA' => '966',
        'SN' => '221',
        'SC' => '248',
        'SL' => '232',
        'SG' => '65',
        'SK' => '421',
        'SI' => '386',
        'SB' => '677',
        'SO' => '252',
        'ZA' => '27',
        'GS' => '500',
        'SS' => '211',
        'ES' => '34',
        'LK' => '94',
        'SD' => '249',
        'SR' => '597',
        'SJ' => '47',
        'SZ' => '268',
        'SE' => '46',
        'CH' => '41',
        'SY' => '963',
        'TW' => '886',
        'TJ' => '992',
        'TZ' => '255',
        'TH' => '66',
        'TL' => '670',
        'TG' => '228',
        'TK' => '690',
        'TO' => '676',
        'TN' => '216',
        'TR' => '90',
        'TM' => '993',
        'TV' => '688',
        'UG' => '256',
        'UA' => '380',
        'AE' => '971',
        'GB' => '44',
        'US' => '1',
        'UY' => '598',
        'UZ' => '998',
        'VU' => '678',
        'VE' => '58',
        'VN' => '84',
        'WF' => '681',
        'EH' => '212',
        'YE' => '967',
        'ZM' => '260',
        'ZW' => '263',
        'AX' => '358'
    );

    public static $format_codification = array(
        'A' => '/[^a-zA-Z ]/',
        'AN' => '/[^[:print:]]|[\"]/',
        'CP' => '/[^a-zA-Z0-9]/',
        'N' => '/[^-0-9.]/',
        // ^((\+|00)([0-9]{1,3})\s?|0)[067]{1,2}(\s?\d{2}){4}$
        'TP' => '/[^+0-9]/'
    );

    public static function sanitize($data, $format, $element = null, $iso_country = null)
    {
        $data_format = preg_replace('/[^a-zA-Z]/', '', $format);
        $data = str_replace(array('°', '|',  '¤','«','»','Æ','æ'), '', $data);

        switch ($data_format) {
            case 'A':
            case 'AN':
            case 'CP':
            case 'N':
            case 'TP':
                $data_min_length = 0;
                $data_max_length = 999;

                $data_length = explode('..', preg_replace('/[^0-9\.\-]/', '', $format));
                if (count($data_length) == 1 && $data_length[0]) {
                    $data_min_length = $data_max_length = (int)$data_length[0];
                } elseif (count($data_length) == 2) {
                    $data_min_length = (int)$data_length[0];
                    $data_max_length = (int)$data_length[1];
                }

                if (mb_detect_encoding((string)$data) != 'UTF-8') {
                    iconv('ISO-8859-15', 'UTF-8//TRANSLIT', $data);
                }

                $data = mb_convert_encoding($data, 'HTML-ENTITIES', 'UTF-8');
                $searches = array('&szlig;', '&(..)lig;', '&([aouAOU])uml;', '&(.)[^;]*;');
                $replacements = array('ss', '\\1', '\\1e', '\\1');
                foreach ($searches as $key => $search) {
                    $data = mb_ereg_replace($search, $replacements[$key], $data);
                }

                $data = preg_replace(self::$format_codification[$data_format], '', $data);
                if (Tools::strlen($data) < $data_min_length) {
                    die(sprintf('Error with the element ['.$element.'] : String ["%s"] is too short ! Minimum length is %d.', $data, $data_min_length));
                }
                $data = Tools::substr($data, 0, $data_max_length);
                break;
            case 'B':
                $data = (int)(bool)$data;
                break;

            default:
                die('Error with the element ['.$element.'] : Unknown format : '.$data_format);
        }

        // Phone | change 0033661123456 to +33661123456
        if ($data_format == 'TP') {
            if ($iso_country && Tools::strlen($iso_country) == 2 && Tools::strtoupper($iso_country) == 'FR') {
                $data = preg_replace('/^\+33/', '0', $data);
            } else {
                $data = preg_replace('/^00/', '+', $data);

                if ($iso_country && Tools::strlen($iso_country) == 2 && $data && $data[0] != '+' && isset(self::$country_calling_codes[$iso_country])) {
                    $substr_start = $data[0] == 0 ? 1 : 0;
                    $data = '+'.self::$country_calling_codes[$iso_country].Tools::substr($data, $substr_start);
                }

                // Check belgium phone number, must start by +324
                if ($iso_country && Tools::strlen($iso_country) == 2 && Tools::strtoupper($iso_country) == 'BE' && Tools::substr($data, 0, 4) != '+324') {
                    $data = null;
                }
            }
        }

        return $data;
    }
}
