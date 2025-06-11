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
 * TACRHtml2TextException class
 * Use to throw Exception when transform html to text
 * Catch possible
 */
if (!defined('_PS_VERSION_')) {
    exit;
}

class TACRHtml2TextException extends Exception
{
    /**
     * @var string more_info
     */
    public $more_info;

    /**
     * @see Exception::__construct
     *
     * @param string $message
     * @param string $more_info
     */
    public function __construct($message = '', $more_info = '')
    {
        parent::__construct($message);
        $this->more_info = $more_info;
    }
}
