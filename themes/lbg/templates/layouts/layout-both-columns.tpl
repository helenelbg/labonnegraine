{**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License 3.0 (AFL-3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/AFL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/AFL-3.0 Academic Free License 3.0 (AFL-3.0)
 *}

{include file='_partials/helpers.tpl'}

<!doctype html>
<html lang="{$language.locale}">

  <head>
    {block name='head'}
      {include file='_partials/head.tpl'}
    {/block}
	
	{literal}
	<!-- Google Tag Manager -->
	<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
	new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
	j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
	'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
	})(window,document,'script','dataLayer','GTM-NV3HW77');</script>
	<!-- End Google Tag Manager -->
	{/literal}

  </head>

  <body id="{$page.page_name}" class="{$page.body_classes|classnames}">

	<!-- Google Tag Manager (noscript) -->
	<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-NV3HW77"
	height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
	<!-- End Google Tag Manager (noscript) -->

    {block name='hook_after_body_opening_tag'}
      {hook h='displayAfterBodyOpeningTag'}
    {/block}

    <main>
      {block name='product_activation'}
        {include file='catalog/_partials/product-activation.tpl'}
      {/block}

      <header id="header">
        {block name='header'}
          {include file='_partials/header.tpl'}
        {/block}
      </header>

      <section id="wrapper">
        {block name='notifications'}
          {include file='_partials/notifications.tpl'}
        {/block}

        {hook h="displayWrapperTop"}
        <div class="container">
          {block name='breadcrumb'}
            {include file='_partials/breadcrumb.tpl'}
          {/block}

          <div class="row">
            {block name="left_column"}
              <div id="left-column" class="col-xs-12 col-sm-4 col-md-3">
                <img src="/img/jardinez-authentique.png" alt="La Bonne Graine, Jardinez authentique !" class="jardinez" />
                {if $page.page_name == 'product'}
                  {hook h='displayLeftColumnProduct' product=$product category=$category}
                {else}
                  {hook h="displayLeftColumn"}
                {/if}
              </div>
            {/block}

            {block name="content_wrapper"}
              <div id="content-wrapper" class="js-content-wrapper left-column right-column col-sm-4 col-md-6">
                {hook h="displayContentWrapperTop"}
                {block name="content"}
                  <p>Hello world! This is HTML5 Boilerplate.</p>
                {/block}
                {hook h="displayContentWrapperBottom"}
              </div>
            {/block}

            {block name="right_column"}
              <div id="right-column" class="col-xs-12 col-sm-4 col-md-3">
                {if $page.page_name == 'product'}
                  {hook h='displayRightColumnProduct'}
                {else}
                  {hook h="displayRightColumn"}
                {/if}
              </div>
            {/block}
          </div>
        </div>
        {hook h="displayWrapperBottom"}
      </section>

      <footer id="footer" class="js-footer">
        {block name="footer"}
          {include file="_partials/footer.tpl"}
        {/block}
      </footer>

    </main>

    {block name='javascript_bottom'}
      {include file="_partials/password-policy-template.tpl"}
      {include file="_partials/javascript.tpl" javascript=$javascript.bottom}
    {/block}

    {block name='hook_before_body_closing_tag'}
      {hook h='displayBeforeBodyClosingTag'}
    {/block}

{*    {include file='module:aw_googlesuite/aw_googlesuite.tpl'}*}
    
    <!-- Conversion & Adblock detect - DEBUT -->
{*    {if $page['page_name'] == "order-confirmation" || $page['page_name'] == "module-paypal-submit"}*}
{*    {literal}*}
{*        <script>*}
{*            console.log("ICI");*}
{*            async function detectAdBlock(id_order) {*}
{*                let adBlockEnabled = false;*}
{*                const googleAdUrl = 'https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js';*}

{*                try {*}
{*                    await fetch(new Request(googleAdUrl)).catch(_ => adBlockEnabled = true);*}
{*                } catch (e) {*}
{*                    adBlockEnabled = true;*}
{*                } finally {*}
{*                    $.ajax(*}
{*                        {*}
{*                            url: "/ajaxAddLog.php",*}
{*                            type: "POST",*}
{*                            data: {*}
{*                                id_order: id_order,*}
{*                                adBlockEnabled: ((adBlockEnabled) ? 1 : 0),*}
{*                                temporalite: "adblock"*}
{*                            },*}
{*                            dataType: "json",*}
{*                            success: function(res){},*}
{*                            error: function(){}*}
{*                        });*}
{*                }*}
{*            }*}
{*        </script>*}

{*        <script>*}
{*            $(function() {*}
{*                var id_order = "{/literal}{$smarty.get.id_order}{literal}";*}
{*                var token = "{/literal}{$smarty.get.id_order|md5|sha1}{literal}";*}

{*                $.ajax({*}
{*                    url: "/ajaxGetDataLayer.php",*}
{*                    type: "POST",*}
{*                    data: {*}
{*                        id_order: id_order,*}
{*                        token: token*}
{*                    },*}
{*                    dataType: "json",*}
{*                    success: function(data){*}

{*                        detectAdBlock(id_order);*}

{*                        $.ajax({*}
{*                            url: "/ajaxAddLog.php",*}
{*                            type: "POST",*}
{*                            data: {*}
{*                                id_order: id_order,*}
{*                                code: data["code"],*}
{*                                temporalite: "avant"*}
{*                            },*}
{*                            dataType: "json",*}
{*                            success: function(res){},*}
{*                            error: function(){}*}
{*                        });*}

{*                        if(data["code"] == 200){*}
{*                            console.log("CHRISTOPHE !");*}
{*                            dataLayer.push(data["datalayer"]);*}
{*                        }*}

{*                        $.ajax({*}
{*                            url: "/ajaxAddLog.php",*}
{*                            type: "POST",*}
{*                            data: {*}
{*                                id_order: id_order,*}
{*                                code: data["code"],*}
{*                                temporalite: "apres"*}
{*                            },*}
{*                            dataType: "json",*}
{*                            success: function(res){},*}
{*                            error: function(){}*}
{*                        });*}
{*                    },*}
{*                    error: function(){*}
{*                        $.ajax({*}
{*                            url: "/ajaxAddLog.php",*}
{*                            type: "POST",*}
{*                            data: {*}
{*                                id_order: id_order,*}
{*                                temporalite: "erreur"*}
{*                            },*}
{*                            dataType: "json",*}
{*                            success: function(res){},*}
{*                            error: function(){}*}
{*                        });*}
{*                        console.error("Erreur lors de la récupération du datalayer");*}
{*                    }*}
{*                });*}
{*            });*}
{*        </script>*}
{*    {/literal}*}
{*    {/if}*}

    <!-- Conversion & Adblock detect - FIN -->

  </body>

</html>
