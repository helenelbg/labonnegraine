{*
 * Cart Reminder
 * 
 *    @category advertising_marketing
 *    @author    Timactive - Romain DE VERA
 *    @copyright Copyright (c) TIMACTIVE 2014 - Romain De Véra
 *    @version 1.0.0
 *    @license   Commercial license
 *
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
 * Template to display product line in text for old client messagery
 * this template is used to render shopping cart in email
 * A developper can be override this file with your template
 *}
{$product.name|escape:'html':'UTF-8'}{if isset($product.attributes) && $product.attributes} - {$product.attributes|escape:'html':'UTF-8'}{/if}  {if !empty($product.gift)}{l s='Gift!' mod='tacartreminder'}{else}{if !$priceDisplay}{$product.price_wt_dp|escape:'htmlall':'UTF-8'}{else}{$product.price_dp|escape:'htmlall':'UTF-8'}{/if}{/if}						
						