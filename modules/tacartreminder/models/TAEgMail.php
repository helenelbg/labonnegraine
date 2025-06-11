<?php
/**
 * Cart Reminder
 *
 *    @author    EIRL Timactive De Véra
 *    @copyright Copyright (c) TIMACTIVE 2015 -EIRL Timactive De Véra
 *    @license   Commercial license
 *
 *    @category pricing_promotion
 *
 *    @version 1.1.0
 *************************************
 **         CART REMINDER            *
 **          V 1.0.0                 *
 *************************************
 *  _____ _            ___       _   _
 * |_   _(_)          / _ \     | | (_)
 *   | |  _ _ __ ___ / /_\ \ ___| |_ ___   _____
 *   | | | | '_ ` _ \|  _  |/ __| __| \ \ / / _ \
 *   | | | | | | | | | | | | (__| |_| |\ V /  __/
 *   \_/ |_|_| |_| |_\_| |_/\___|\__|_| \_/ \___|
 *
 * +
 * + Languages: EN, FR
 * + PS version: 1.5,1.6
 *
 * TAEgMail Object representation of a one element in the
 * XML store in this file data/egmail/egmails.xml
 */
if (!defined('_PS_VERSION_')) {
    exit;
}

class TAEgMail
{
    public $id;
    /**
     * @var string title
     */
    public $title;
    /**
     * Array list of suggestions color, image
     *
     * @var array suggestions
     */
    public $suggestions;

    /**
     * get a HTML Content of personnalisation
     *
     * @param $lang_iso
     * @param $suggestioncustom
     *
     * @return mixed html content
     */
    public function getContent($lang_iso, $suggestioncustom)
    {
        $egmail_file = _PS_ROOT_DIR_ . '/modules/tacartreminder/data/egmail/egmail' . $this->id . '_' . $lang_iso . '.html';
        if (file_exists($egmail_file)) {
            $content = Tools::file_get_contents($egmail_file);
            foreach ($suggestioncustom->variables as $variable) {
                $content = str_replace('[[' . $variable->id . ']]', $variable->value, $content);
            }
            $content = str_replace('[[bgpattern]]', $suggestioncustom->bgpattern, $content);
            $shop = new Shop((int) Configuration::get('PS_SHOP_DEFAULT'));
            $ssl = (bool) Configuration::get('PS_SSL_ENABLED');
            $base = (($ssl) ? 'https://' . $shop->domain_ssl : 'http://' . $shop->domain);
            $mod_img_url = $base . $shop->getBaseURI() . 'modules/tacartreminder/views/img/';
            $content = str_replace('[[mod_img_url]]', $mod_img_url, $content);

            return $content;
        }
    }

    /**
     * Get Egmail Object or Array of list Egmail Object
     *
     * @param $egmail_id filter
     *
     * @return array|TAEgMail
     */
    public static function getEgMail($egmail_id)
    {
        $eg_mail = self::getEgMails($egmail_id);

        return $eg_mail;
    }

    /**
     * Get Egmail depending the filter id
     *
     * @param bool|false $filter_id
     *
     * @return array|TAEgMail
     */
    public static function getEgMails($filter_id = false)
    {
        $xml = simplexml_load_string(
            Tools::file_get_contents(_PS_ROOT_DIR_ . '/modules/tacartreminder/data/egmail/egmails.xml')
        );
        $egmails = [];
        foreach ($xml->egmail as $egmaildom) {
            $eg_mail = new TAEgMail();
            $eg_mail->id = (int) $egmaildom->id;
            $eg_mail->title = (string) $egmaildom->title;
            $eg_mail->suggestions = [];
            foreach ($egmaildom->suggestions->suggestion as $suggestiondom) {
                $suggestion = TAEgMailSuggestion::getFromDom($suggestiondom);
                $eg_mail->suggestions[] = $suggestion;
            }
            if ($filter_id && (int) $filter_id == $eg_mail->id) {
                return $eg_mail;
            }
            $egmails[] = $eg_mail;
        }

        return $egmails;
    }

    /**
     * Get Image pattern
     *
     * @return array
     */
    public static function getBgPatterns()
    {
        $bgpatterns = [];
        $dossier = _PS_ROOT_DIR_ . '/modules/tacartreminder/views/img/egmail/bgpattern';
        $d = dir($dossier);
        while ($entry = $d->read()) {
            if ($entry != '.' && $entry != '..' && $entry != '' && $entry && strpos($entry, 'png') !== false) {
                $bgpatterns[] = $entry;
            }
        }
        $d->close();

        return $bgpatterns;
    }
}
