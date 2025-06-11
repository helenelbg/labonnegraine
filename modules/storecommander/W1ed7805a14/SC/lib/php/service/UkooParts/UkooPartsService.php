<?php

if ((defined('SC_UkooParts_ACTIVE') && SC_UkooParts_ACTIVE == 1) && SCI::moduleIsInstalled('ukooparts')) {
    if (!defined('UKOOPARTS_PANEL_DOMAIN')) {
        define('UKOOPARTS_PANEL_DOMAIN', Configuration::get('UKOOPARTS_PANEL_DOMAIN'));
    }
    if (!defined('UKOOPARTS_PANEL_API_TOKEN')) {
        define('UKOOPARTS_PANEL_API_TOKEN', Configuration::get('UKOOPARTS_PANEL_API_TOKEN'));
    }
    if (!defined('UKOOPARTS_API_BASE_URL')) {
        define('UKOOPARTS_API_BASE_URL', 'https://' . UKOOPARTS_PANEL_DOMAIN . '/api/');
    }
}