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
 * TAEgMailSuggestion is a class used to represent a suggestion element store
 * in this file data/egmail/egmails.xml
 */
if (!defined('_PS_VERSION_')) {
    exit;
}

class TAEgMailSuggestion
{
    /**
     * Color 1 for palette in hexadecimal
     *
     * @var string palette_color1 attribute
     */
    public $palette_color1;
    /**
     * Color 2 for palette in hexadecimal
     *
     * @var string palette_color 2 attribute
     */
    public $palette_color2;
    /**
     * Color 3 for palette in hexadecimal
     *
     * @var string palette_color 3 attribute
     */
    public $palette_color3;
    /**
     * Color 4 for palette in hexadecimal
     *
     * @var string palette_color 4 attribute
     */
    public $palette_color4;
    /**
     * Color 5 for palette in hexadecimal
     *
     * @var string palette_color 5 attribute
     */
    public $palette_color5;
    /**
     * Color 6 for palette in hexadecimal
     *
     * @var string palette_color 6 attribute
     */
    public $palette_color6;
    /**
     * Bg pattern selected for this selection
     *
     * @var string bgpattern
     */
    public $bgpattern;
    /**
     * @var array variables
     */
    public $variables = [];
    /**
     * @var array images
     */
    public $images = [];

    /**
     * Build EgMailSuggestion Object by XML Dom
     *
     * @param $domsug
     *
     * @return TAEgMailSuggestion
     */
    public static function getFromDom($domsug)
    {
        $suggestion = new TAEgMailSuggestion();
        $palettedom = $domsug->palette;
        $suggestion->palette_color1 = (string) $palettedom->color1;
        $suggestion->palette_color2 = (string) $palettedom->color2;
        $suggestion->palette_color3 = (string) $palettedom->color3;
        $suggestion->palette_color4 = (string) $palettedom->color4;
        $suggestion->palette_color5 = (string) $palettedom->color5;
        $suggestion->palette_color6 = (string) $palettedom->color6;
        $suggestion->variables = [];
        $suggestion->images = [];
        foreach ($domsug->variables->variable as $variabledom) {
            $variable = TAEgMailVariable::getFromDom($variabledom);
            $suggestion->variables[] = $variable;
        }
        $suggestion->bgpattern = (string) $domsug->bgpattern;

        return $suggestion;
    }
}
