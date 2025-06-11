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
 * TAEgMailImage is a class to represent a MailImage element store
 * in this file data/egmail/egmails.xml
 */
if (!defined('_PS_VERSION_')) {
    exit;
}

class TAEgMailImage
{
    /**
     * Background color hexadecimal or RGB
     *
     * @var bgcolor
     */
    public $bgcolor;
    /**
     * Image url
     *
     * @var url
     */
    public $url;

    /**
     * Build TAEgMailImage object by xml dom
     *
     * @param $imgdom
     *
     * @return TAEgMailImage
     */
    public static function getFromDom($imgdom)
    {
        $image = new TAEgMailImage();
        $image->bgcolor = (string) $imgdom->bgcolor;
        $image->url = (string) $imgdom->url;

        return $image;
    }
}
