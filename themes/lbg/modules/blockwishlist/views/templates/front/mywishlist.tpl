{*
* 2007-2017 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2017 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}

<div id="mywishlist">
	{capture name=path}
		<a href="{$link->getPageLink('my-account', true)|escape:'html':'UTF-8'}">
			{l s='My account' mod='blockwishlist'}
		</a>
		<span class="navigation-pipe">
			{$navigationPipe}
		</span>
		<span class="navigation_page">
			{l s='My wishlists' mod='blockwishlist'}
		</span>
	{/capture}

	<h1 class="page-heading"><img src="/themes/default-bootstrap/img/picto-envies-on.png" alt=""><span>{l s='Vos listes d\'envies' mod='blockwishlist'}</span></h1>

	{include file="$tpl_dir./errors.tpl"}
<br>
	{if $id_customer|intval neq 0}
		{if $wishlists}
			
			<select id="select_wishlist" onchange="javascript:event.preventDefault();WishlistManage('block-order-detail', $(this).val());">
				{foreach from=$wishlists item=w}
					<option value="{$w.id_wishlist}">{$w.name}</option>
				{/foreach}	
			</select>

			<div id="block-history" class="block-center">
				<table class="table table-bordered table-mywishlist">
					<thead>
						<tr>
							<th class="first_item">{l s='Vos listes d\'envies' mod='blockwishlist'}</th>
							<th class="item mywishlist_second">{l s='' mod='blockwishlist'}</th>
							<th class="item mywishlist_second">{l s='Nombre de produits' mod='blockwishlist'}</th>
							<!--th class="item mywishlist_second">{l s='Viewed' mod='blockwishlist'}</th-->
							<th class="item mywishlist_second">{l s='Créée le' mod='blockwishlist'}</th>
							<!--th class="item mywishlist_second">{l s='Direct Link' mod='blockwishlist'}</th-->
							<th class="item mywishlist_second">{l s='Liste par défaut' mod='blockwishlist'}</th>
							<th class="item mywishlist_second">{l s='Partager' mod='blockwishlist'}</th>
							<th class="last_item mywishlist_second">{l s='Delete' mod='blockwishlist'}</th>
						</tr>
					</thead>
					<tbody>
						{section name=i loop=$wishlists}
							<tr id="wishlist_{$wishlists[i].id_wishlist|intval}">
								<td class="first_item">
									<a href="#" class="wishlist_name" onclick="javascript:event.preventDefault();WishlistManage('block-order-detail', '{$wishlists[i].id_wishlist|intval}'); $('#select_wishlist').val({$wishlists[i].id_wishlist|intval}); $('#select_wishlist').show(); $('.table-mywishlist').hide(); ">
										{$wishlists[i].name|truncate:23:'...'|escape:'htmlall':'UTF-8'}
									</a>
								</td>
								
								<td class="align_center">
									<a class="js_wishlist_name" href="#" data-name="{$wishlists[i].name}" onclick="javascript:event.preventDefault(); $('#wishlist_name_change').show(); 
										$('#wnc_id').val({$wishlists[i].id_wishlist});
										$('#wnc_i').val({$i});
										$('#wnc_name').val($(this).data('name'));">
										<i class="icon icon-edit"></i>
									</a>
								</td>
								<td class="align_center">
									{assign var=n value=0}
									{foreach from=$nbProducts item=nb name=i}
										{if $nb.id_wishlist eq $wishlists[i].id_wishlist}
											{assign var=n value=$nb.nbProducts|intval}
										{/if}
									{/foreach}
									{if $n}
										{$n|intval}
									{else}
										0
									{/if}
								</td>
								<!--td>{$wishlists[i].counter|intval}</td-->
								<td>{$wishlists[i].date_add|date_format:"%d/%m/%Y"}</td>
								<!--td>
									<a href="#" onclick="javascript:event.preventDefault();WishlistManage('block-order-detail', '{$wishlists[i].id_wishlist|intval}');">
										{l s='View' mod='blockwishlist'}
									</a>
								</td-->
								<td class="wishlist_default">
									{if isset($wishlists[i].default) && $wishlists[i].default == 1}
										<span class="is_wish_list_default">
											<i class="icon icon-check-square"></i>
										</span>
									{else}
										<a href="#" onclick="javascript:event.preventDefault();(WishlistDefault('wishlist_{$wishlists[i].id_wishlist|intval}', '{$wishlists[i].id_wishlist|intval}'));">
											<i class="icon icon-square"></i>
										</a>
									{/if}
								</td>
								<td class="align_center">
									<a href="#" onclick="javascript:event.preventDefault();shareWishListModal('{$wishlist_url_token}{$wishlists[i].token}');">
										<img src="/themes/default-bootstrap/img/picto_partager.png" alt="">
										
										
									</a>
								</td>
								<td class="wishlist_delete last_item">
									<a class="icon" href="#" onclick="javascript:event.preventDefault();return (WishlistDelete('wishlist_{$wishlists[i].id_wishlist|intval}', '{$wishlists[i].id_wishlist|intval}', '{l s='Do you really want to delete this wishlist ?' mod='blockwishlist' js=1}'));">
										<i class="icon-trash"></i>
									</a>
								</td>
							</tr>
						{/section}
						<tr class="noborder">
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td class="wishlist_delete">
								<a class="icon" href="#" onclick="javascript:event.preventDefault(); $('#form_wishlist').show(); ">
									<i class="icon-plus-square"></i>
								</a>
							</td>
						</tr>
					</tbody>
				</table>
			</div>
			<div id="block-order-detail">&nbsp;</div>
		{/if}
		

		
		<form method="post" class="form_new_wishlist std box" id="form_wishlist">
			<fieldset>
				<h3 class="page-subheading">{l s='New wishlist' mod='blockwishlist'}</h3>
				<div class="form-group">
					<input type="hidden" name="token" value="{$token|escape:'html':'UTF-8'}" />
					<input type="text" id="name" name="name" class="inputTxt form-control" placeholder="{l s='Name' mod='blockwishlist'}" value="{if isset($smarty.post.name) and $errors|@count > 0}{$smarty.post.name|escape:'html':'UTF-8'}{/if}" />
				</div>
				<p class="submit">
                    <button id="submitWishlist" class="btn btn-default button button-medium" type="submit" name="submitWishlist">
                    	<span>{l s='Save' mod='blockwishlist'}<i class="icon-chevron-right right"></i></span>
                    </button>
				</p>
			</fieldset>
		</form>
		
		<div class="form_new_wishlist std box" id="wishlist_name_change">
			<fieldset>
				<h3 class="page-subheading">{l s='Modifier le nom de la liste' mod='blockwishlist'}</h3>
				<div class="form-group">
					<input type="hidden" id="wnc_id" value="" />
					<input type="hidden" id="wnc_i" value="" />
					<input type="text" id="wnc_name" class="inputTxt form-control" placeholder="{l s='Name' mod='blockwishlist'}" value="" />
				</div>
				<p class="submit">
                    <button class="btn btn-default button button-medium" type="button" onclick="wishlistNameChange()">
                    	<span>{l s='Save' mod='blockwishlist'}<i class="icon-chevron-right right"></i></span>
                    </button>
				</p>
			</fieldset>
		</div>
	{/if}

</div>
