<?php

class Address extends AddressCore
{
    public static function initialize($id_address = null, $with_geoloc = false)
    {
        $context = Context::getContext();

        if ($id_address) {
            $context_hash = (int) $id_address;
        } elseif ($with_geoloc && isset($context->customer->geoloc_id_country)) {
            $context_hash = md5((int) $context->customer->geoloc_id_country . '-' . (int) $context->customer->id_state . '-' .
                                $context->customer->postcode);
        } else {
            $context_hash = md5((string) $context->country->id);
        }

        $cache_id = 'Address::initialize_' . $context_hash;

        if (!Cache::isStored($cache_id)) {
            // if an id_address has been specified retrieve the address
            if ($id_address) {
                $address = new Address((int) $id_address);

                if (!Validate::isLoadedObject($address)) {
                    //throw new PrestaShopException('Invalid address #' . (int) $id_address);
                }
            } elseif ($with_geoloc && isset($context->customer->geoloc_id_country)) {
                $address = new Address();
                $address->id_country = (int) $context->customer->geoloc_id_country;
                $address->id_state = (int) $context->customer->id_state;
                $address->postcode = $context->customer->postcode;
            } elseif ((int) $context->country->id && ((int) $context->country->id != Configuration::get('PS_SHOP_COUNTRY_ID'))) {
                $address = new Address();
                $address->id_country = (int) $context->country->id;
                $address->id_state = 0;
                $address->postcode = '0';
            } elseif ((int) Configuration::get('PS_SHOP_COUNTRY_ID')) {
                // set the default address
                $address = new Address();
                $address->id_country = (int) Configuration::get('PS_SHOP_COUNTRY_ID');
                $address->id_state = (int) Configuration::get('PS_SHOP_STATE_ID');
                $address->postcode = Configuration::get('PS_SHOP_CODE');
            } else {
                // set the default address
                $address = new Address();
                $address->id_country = (int) Configuration::get('PS_COUNTRY_DEFAULT');
                $address->id_state = 0;
                $address->postcode = '0';
            }
            Cache::store($cache_id, $address);

            return $address;
        }

        return Cache::retrieve($cache_id);
    }

    public static function getZoneById($id_address)
    {
        if (empty($id_address)) {
            return false;
        }

        $id_zone = Hook::exec('actionGetIDZoneByAddressID', ['id_address' => $id_address]);

        if (is_numeric($id_zone)) {
            self::$_idZones[$id_address] = (int) $id_zone;

            return self::$_idZones[$id_address];
        }

        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow('
			SELECT s.`id_zone` AS id_zone_state, c.`id_zone`
			FROM `' . _DB_PREFIX_ . 'address` a
			LEFT JOIN `' . _DB_PREFIX_ . 'country` c ON c.`id_country` = a.`id_country`
			LEFT JOIN `' . _DB_PREFIX_ . 'state` s ON s.`id_state` = a.`id_state`
			WHERE a.`id_address` = ' . (int) $id_address);

        if (empty($result['id_zone_state']) && empty($result['id_zone'])) {
            return false;
        }

        self::$_idZones[$id_address] = (int) $result['id_zone'];

        return self::$_idZones[$id_address];
    }

}
