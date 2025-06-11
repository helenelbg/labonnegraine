{*
* 2022 - Keyrnel
*
* NOTICE OF LICENSE
*
* The source code of this module is under a commercial license.
* Each license is unique and can be installed and used on only one shop.
* Any reproduction or representation total or partial of the module, one or more of its components,
* by any means whatsoever, without express permission from us is prohibited.
* If you have not received this module from us, thank you for contacting us.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade this module to newer
* versions in the future.
*
* @author    Keyrnel
* @copyright 2022 - Keyrnel
* @license   commercial
* International Registered Trademark & Property of Keyrnel
*}

<div class="form-group">
    <label class="control-label col-lg-3">
        <span class="label-tooltip" data-toggle="tooltip"
        title="{l s='Invalid characters:' mod='flashsales'} &lt;&gt;;=#{}">
            {l s='Countdown string' mod='flashsales'}
        </span>
    </label>
    <div class="col-lg-9">
        {foreach from=$languages item=language}
        {if $languages|count > 1}
        <div class="row">
            <div class="translatable-field lang-{$language.id_lang|intval}" {if $language.id_lang != $default_language}style="display:none"{/if}>
                <div class="col-lg-9">
        {/if}
                    <input type="text" id="flashsale_countdown_string_{$layout|escape:'html':'UTF-8'}_{$language.id_lang|intval}" name="flashsale_countdown_string_{$layout|escape:'html':'UTF-8'}_{$language.id_lang|intval}" value="{Configuration::get("FLASHSALE_COUNTDOWN_STRING_`$layout|strtoupper`", $language.id_lang)|escape:'html':'UTF-8'}">
        {if $languages|count > 1}
                </div>
                <div class="col-lg-2">
                    <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
                        {$language.iso_code|escape:'html':'UTF-8'}
                        <span class="caret"></span>
                    </button>
                    <ul class="dropdown-menu">
                        {foreach from=$languages item=language}
                        <li><a href="javascript:hideOtherLanguage({$language.id_lang|intval});" tabindex="-1">{$language.name|escape:'html':'UTF-8'}</a></li>
                        {/foreach}
                    </ul>
                </div>
            </div>
        </div>
        {/if}
        {/foreach}
    </div>
</div>

<div class="css_form">
    {assign var=css_fields value=$css_fields[$layout]}
    <div class="form-group">
        <label class="control-label col-lg-3">
            {l s='Countdown box' mod='flashsales'}
        </label>
        <div class="col-lg-9">
            <div class="row">
                <div class="col-sm-6">
                    <div class="input-group">
                        <span class="input-group-addon">{l s='Width' mod='flashsales'}</span>
                        <input type="text" data-class=".flashsale-countdown-box.{$layout|escape:'html':'UTF-8'}" data-property="width" name="fields[{$layout|escape:'html':'UTF-8'}][box_width]" value="{if isset($css_fields.box_width)}{$css_fields.box_width|intval}{/if}"/>
                        <span class="input-group-addon">{l s='%' mod='flashsales'}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="form-group">
        <label class="control-label col-lg-3">
            {l s='Countdown icon' mod='flashsales'}
        </label>
        <div class="col-lg-9">
            <div class="row">
                <div class="col-sm-5">
            		<div class="input-group">
                        <span class="input-group-addon">{l s='Color' mod='flashsales'}</span>
            			<input type="color" data-class=".flashsale-countdown-box.{$layout|escape:'html':'UTF-8'} .content i.icon-clock-o" data-property="color" data-hex="true" class="color mColorPickerInput" name="fields[{$layout|escape:'html':'UTF-8'}][icon_color]" value="{if isset($css_fields.icon_color)}{$css_fields.icon_color|escape:'quotes':'UTF-8'}{/if}" />
            		</div>
                </div>
                <div class="col-sm-5">
                    <div class="input-group">
                        <span class="input-group-addon">{l s='Size' mod='flashsales'}</span>
                        <input type="text" data-class=".flashsale-countdown-box.{$layout|escape:'html':'UTF-8'} .content i.icon-clock-o" data-property="font-size" name="fields[{$layout|escape:'html':'UTF-8'}][icon_font_size]" value="{if isset($css_fields.icon_font_size)}{$css_fields.icon_font_size|intval}{/if}"/>
                        <span class="input-group-addon">{l s='px' mod='flashsales'}</span>
                    </div>
                </div>
                <div class="col-sm-2">
                    <span class="switch prestashop-switch">
                    <input id="icon_display_{$layout|escape:'html':'UTF-8'}_on" data-class=".flashsale-countdown-box.{$layout|escape:'html':'UTF-8'} .content i.icon-clock-o" data-property="display" type="radio" value="1" name="fields[{$layout|escape:'html':'UTF-8'}][icon_display]" {if isset($css_fields.icon_display) && $css_fields.icon_display}checked="checked"{/if}/>
                    <label class="radioCheck" for="icon_display_{$layout|escape:'html':'UTF-8'}_on">{l s='Show' mod='flashsales'}</label>
                    <input id="icon_display_{$layout|escape:'html':'UTF-8'}_off" data-class=".flashsale-countdown-box.{$layout|escape:'html':'UTF-8'} .content i.icon-clock-o" data-property="display" type="radio" value="0" name="fields[{$layout|escape:'html':'UTF-8'}][icon_display]" {if isset($css_fields.icon_display) && !$css_fields.icon_display}checked="checked"{/if}>
                    <label class="radioCheck" for="icon_display_{$layout|escape:'html':'UTF-8'}_off">{l s='Hide' mod='flashsales'}</label>
                    <a class="slide-button btn"></a>
                </div>
            </div>
    	</div>
    </div>

    <div class="form-group">
        <label class="control-label col-lg-3">
            {l s='Countdown string' mod='flashsales'}
        </label>
        <div class="col-lg-9">
            <div class="row">
                <div class="col-sm-6">
            		<div class="input-group">
                        <span class="input-group-addon">{l s='Color' mod='flashsales'}</span>
            			<input type="color" data-class=".flashsale-countdown-box.{$layout|escape:'html':'UTF-8'} .content span.title" data-property="color" data-hex="true" class="color mColorPickerInput" name="fields[{$layout|escape:'html':'UTF-8'}][title_color]" value="{if isset($css_fields.title_color)}{$css_fields.title_color|escape:'quotes':'UTF-8'}{/if}" />
            		</div>
                </div>
                <div class="col-sm-6">
                    <div class="input-group">
                        <span class="input-group-addon">{l s='Size' mod='flashsales'}</span>
                        <input type="text" data-class=".flashsale-countdown-box.{$layout|escape:'html':'UTF-8'} .content span.title" data-property="font-size" name="fields[{$layout|escape:'html':'UTF-8'}][title_font_size]" value="{if isset($css_fields.title_font_size)}{$css_fields.title_font_size|intval}{/if}"/>
                        <span class="input-group-addon">{l s='px' mod='flashsales'}</span>
                    </div>
                </div>
            </div>
    	</div>
    </div>

    <div class="form-group">
        <label class="control-label col-lg-3">
            {l s='Countdown time left' mod='flashsales'}
        </label>
        <div class="col-lg-9">
            <div class="row">
                <div class="col-sm-6">
            		<div class="input-group">
                        <span class="input-group-addon">{l s='Color' mod='flashsales'}</span>
            			<input type="color" data-class=".flashsale-countdown-box.{$layout|escape:'html':'UTF-8'} .content span.countdown" data-property="color" data-hex="true" class="color mColorPickerInput" name="fields[{$layout|escape:'html':'UTF-8'}][countdown_color]" value="{if isset($css_fields.countdown_color)}{$css_fields.countdown_color|escape:'quotes':'UTF-8'}{/if}" />
            		</div>
                </div>
                <div class="col-sm-6">
                    <div class="input-group">
                        <span class="input-group-addon">{l s='Size' mod='flashsales'}</span>
                        <input type="text" data-class=".flashsale-countdown-box.{$layout|escape:'html':'UTF-8'} .content span.countdown" data-property="font-size" name="fields[{$layout|escape:'html':'UTF-8'}][countdown_font_size]" value="{if isset($css_fields.countdown_font_size)}{$css_fields.countdown_font_size|intval}{/if}"/>
                        <span class="input-group-addon">{l s='px' mod='flashsales'}</span>
                    </div>
                </div>
            </div>
    	</div>
    </div>

    <div class="form-group">
        <label class="control-label col-lg-3">
            {l s='Countdown background' mod='flashsales'}
        </label>
        <div class="col-lg-9">
            <div class="row">
                <div class="col-sm-6">
            		<div class="input-group">
                        <span class="input-group-addon">{l s='Color' mod='flashsales'}</span>
            			<input type="color" data-class=".flashsale-countdown-box.{$layout|escape:'html':'UTF-8'}" data-property="background-color" data-hex="true" class="color mColorPickerInput" name="fields[{$layout|escape:'html':'UTF-8'}][box_background_color]" value="{if isset($css_fields.box_background_color)}{$css_fields.box_background_color|escape:'quotes':'UTF-8'}{/if}" />
            		</div>
            	</div>
            </div>
        </div>
    </div>

    <div class="form-group">
        <label class="control-label col-lg-3">
            {l s='Countdown border' mod='flashsales'}
        </label>
        <div class="col-lg-9">
            <div class="row">
                <div class="col-sm-5">
            		<div class="input-group">
                        <span class="input-group-addon">{l s='Color' mod='flashsales'}</span>
            			<input type="color" data-class=".flashsale-countdown-box.{$layout|escape:'html':'UTF-8'}" data-property="border-color" data-hex="true" class="color mColorPickerInput" name="fields[{$layout|escape:'html':'UTF-8'}][box_border_color]" value="{if isset($css_fields.box_border_color)}{$css_fields.box_border_color|escape:'quotes':'UTF-8'}{/if}" />
            		</div>
            	</div>
                <div class="col-sm-5">
                    <div class="input-group">
                        <span class="input-group-addon">{l s='Size' mod='flashsales'}</span>
                        <input type="text" data-class=".flashsale-countdown-box.{$layout|escape:'html':'UTF-8'}" data-property="border-width" name="fields[{$layout|escape:'html':'UTF-8'}][box_border_width]" value="{if isset($css_fields.box_border_width)}{$css_fields.box_border_width|intval}{/if}"/>
                        <span class="input-group-addon">{l s='px' mod='flashsales'}</span>
                    </div>
                </div>
                <div class="col-sm-2">
                    <span class="switch prestashop-switch">
                    <input id="box_border_style_{$layout|escape:'html':'UTF-8'}_on" data-class=".flashsale-countdown-box.{$layout|escape:'html':'UTF-8'}" data-property="border-style" type="radio" value="1" name="fields[{$layout|escape:'html':'UTF-8'}][box_border_style]" {if isset($css_fields.box_border_style) && $css_fields.box_border_style}checked="checked"{/if}/>
                    <label class="radioCheck" for="box_border_style_{$layout|escape:'html':'UTF-8'}_on">{l s='Show' mod='flashsales'}</label>
                    <input id="box_border_style_{$layout|escape:'html':'UTF-8'}_off" data-class=".flashsale-countdown-box.{$layout|escape:'html':'UTF-8'}" data-property="border-style" type="radio" value="0" name="fields[{$layout|escape:'html':'UTF-8'}][box_border_style]" {if isset($css_fields.box_border_style) && !$css_fields.box_border_style}checked="checked"{/if}/>
                    <label class="radioCheck" for="box_border_style_{$layout|escape:'html':'UTF-8'}_off">{l s='Hide' mod='flashsales'}</label>
                    <a class="slide-button btn"></a>
                </div>
            </div>
        </div>
    </div>

    <div class="form-group">
        <label class="control-label col-lg-3">
            {l s='Countdown padding' mod='flashsales'}
        </label>
        <div class="col-lg-9">
            <div class="row">
                <div class="col-sm-3">
                    <div class="input-group">
                        <span class="input-group-addon">{l s='Top' mod='flashsales'}</span>
                        <input type="text" data-class=".flashsale-countdown-box.{$layout|escape:'html':'UTF-8'}" data-property="padding-top" name="fields[{$layout|escape:'html':'UTF-8'}][box_padding_top]" value="{if isset($css_fields.box_padding_top)}{$css_fields.box_padding_top|intval}{/if}"/>
                        <span class="input-group-addon">{l s='px' mod='flashsales'}</span>
                    </div>
                </div>
                <div class="col-sm-3">
                    <div class="input-group">
                        <span class="input-group-addon">{l s='Bottom' mod='flashsales'}</span>
                        <input type="text" data-class=".flashsale-countdown-box.{$layout|escape:'html':'UTF-8'}" data-property="padding-bottom" name="fields[{$layout|escape:'html':'UTF-8'}][box_padding_bottom]" value="{if isset($css_fields.box_padding_bottom)}{$css_fields.box_padding_bottom|intval}{/if}"/>
                        <span class="input-group-addon">{l s='px' mod='flashsales'}</span>
                    </div>
                </div>
                <div class="col-sm-3">
                    <div class="input-group">
                        <span class="input-group-addon">{l s='Left' mod='flashsales'}</span>
                        <input type="text" data-class=".flashsale-countdown-box.{$layout|escape:'html':'UTF-8'}" data-property="padding-left" name="fields[{$layout|escape:'html':'UTF-8'}][box_padding_left]" value="{if isset($css_fields.box_padding_left)}{$css_fields.box_padding_left|intval}{/if}"/>
                        <span class="input-group-addon">{l s='px' mod='flashsales'}</span>
                    </div>
                </div>
                <div class="col-sm-3">
                    <div class="input-group">
                        <span class="input-group-addon">{l s='Right' mod='flashsales'}</span>
                        <input type="text" data-class=".flashsale-countdown-box.{$layout|escape:'html':'UTF-8'}" data-property="padding-right" name="fields[{$layout|escape:'html':'UTF-8'}][box_padding_right]" value="{if isset($css_fields.box_padding_right)}{$css_fields.box_padding_right|intval}{/if}"/>
                        <span class="input-group-addon">{l s='px' mod='flashsales'}</span>
                    </div>
                </div>
            </div>
    	</div>
    </div>

    <div class="form-group">
        <label class="control-label col-lg-3">
            {l s='Countdown margin' mod='flashsales'}
        </label>
        <div class="col-lg-9">
            <div class="row">
                <div class="col-sm-3">
                    <div class="input-group">
                        <span class="input-group-addon">{l s='Top' mod='flashsales'}</span>
                        <input type="text" data-class=".flashsale-countdown-box.{$layout|escape:'html':'UTF-8'}" data-property="margin-top" name="fields[{$layout|escape:'html':'UTF-8'}][box_margin_top]" value="{if isset($css_fields.box_margin_top)}{$css_fields.box_margin_top|intval}{/if}"/>
                        <span class="input-group-addon">{l s='px' mod='flashsales'}</span>
                    </div>
                </div>
                <div class="col-sm-3">
                    <div class="input-group">
                        <span class="input-group-addon">{l s='Bottom' mod='flashsales'}</span>
                        <input type="text" data-class=".flashsale-countdown-box.{$layout|escape:'html':'UTF-8'}" data-property="margin-bottom" name="fields[{$layout|escape:'html':'UTF-8'}][box_margin_bottom]" value="{if isset($css_fields.box_margin_bottom)}{$css_fields.box_margin_bottom|intval}{/if}"/>
                        <span class="input-group-addon">{l s='px' mod='flashsales'}</span>
                    </div>
                </div>
            </div>
    	</div>
    </div>
</div>
