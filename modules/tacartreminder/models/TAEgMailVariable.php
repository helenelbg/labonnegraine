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
 * TAEgMailVariable is a class used to represent a Variable element store
 * in this file data/egmail/egmails.xml
 */
if (!defined('_PS_VERSION_')) {
    exit;
}

class TAEgMailVariable
{
    /**
     * @example color,..
     *
     * @var string type
     */
    public $type;
    /**
     * id can be used in egmail content
     *
     * @var int id
     */
    public $id;
    /**
     * @var string value
     */
    public $value;

    /**
     * Build TAEgMailVariable Object by XML Dom
     *
     * @param $variabledom
     *
     * @return TAEgMailVariable
     */
    public static function getFromDom($variabledom)
    {
        $variable = new TAEgMailVariable();
        $variable->type = (string) $variabledom['type'];
        $variable->id = (string) $variabledom['id'];
        $variable->value = (string) $variabledom;

        return $variable;
    }
}
