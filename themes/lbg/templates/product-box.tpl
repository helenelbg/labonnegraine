{extends file='page.tpl'}



<div class="row">
	{block name="left_column"}
	  <div id="left-column" class="col-xs-12 col-sm-4 col-md-3">    
		{hook h="displayLeftColumn"}         
	  </div>
	{/block}

	{block name="content_wrapper"}
	  <div id="content-wrapper" class="js-content-wrapper left-column right-column col-sm-4 col-md-6">
		{hook h="displayContentWrapperTop"}
		{block name="content"}
		{/block}
		{hook h="displayContentWrapperBottom"}
	  </div>
	{/block}
</div>
		
{block name='page_content'}
	
	<div id="primary_block" class="simple_box_block">
		<input type="hidden" id="val_id_product" value="{$product->id}" >

			<h1 class="Page_Produit_Titre Page_Box_Titre">{$product->name}
				<button onclick="window.history.go(-1)" class="retour_page_produit"></button>
			</h1>
			{if isset($confirmation) && $confirmation}
				<p class="confirmation">
					{$confirmation}
				</p>
			{/if}

			<!-- right infos-->
			<div id="pb-right-column">
				<!-- product img-->
				<div id="box-image-block">
					<img src="{$product.cover.large.url}" alt="{$product->name}" />	
				</div>

				{if $product->description_short}
					<div id="short_description_block" class="noborder pdt_desc_box">
						<div id="idTab1" class="rte">{$product->description_short nofilter}</div>
						{if $product->description}
							<div id="idTab1" class="rte">{$product->description nofilter}</div>
						{/if}
					</div>
				{/if}
				{$jour=date("j")}
				{$mois=date("n")}
				{if $mois>10 || ($mois ==10 && $jour>=10) || ($mois ==1 && $jour<10)}
					{$mois_envoi1="Mi-Janvier"}
					{$mois_envoi2="Mi-Avril"}
					{$mois_envoi3="Mi-Juillet"}
					{$mois_envoi4="Mi-Octobre"}
				{/if}
				{if ($mois>1 && $mois<4) || ($mois ==1 && $jour>=10) || ($mois ==4 && $jour<10) }
					{$mois_envoi1="Mi-Avril"}
					{$mois_envoi2="Mi-Juillet"}
					{$mois_envoi3="Mi-Octobre"}
					{$mois_envoi4="Mi-Janvier"}
				{/if}
				{if ($mois>4 && $mois<7) || ($mois ==4 && $jour>=10) || ($mois ==7 && $jour<10) }
					{$mois_envoi1="Mi-Juillet"}
					{$mois_envoi2="Mi-Octobre"}
					{$mois_envoi3="Mi-Janvier"}
					{$mois_envoi4="Mi-Avril"}
				{/if}
				{if ($mois>7 && $mois<10) || ($mois ==7 && $jour>=10) || ($mois ==10 && $jour<10) }
					{$mois_envoi1="Mi-Octobre"}
					{$mois_envoi2="Mi-Janvier"}
					{$mois_envoi3="Mi-Avril"}
					{$mois_envoi4="Mi-Juillet"}
				{/if}

				<div id="cadeau_box"><img src="/themes/lbg/assets/img/picto_cadeau.png"> Date d'expédition des box<br />
				<div class="detail">

					<div class="saison_box">
						<b>1 saison :</b><br />
						{$mois_envoi1}<br />---<br />
					</div>
					<div class="saison_box">
						<b>2 saisons :</b><br />
						{$mois_envoi1}<br />
						{$mois_envoi2}<br />---<br />
					</div>
					<div class="saison_box">
						<b>3 saisons :</b><br />
						{$mois_envoi1}<br />
						{$mois_envoi2}<br />
						{$mois_envoi3}<br />---<br />
					</div>
					<div class="saison_box">
						<b>4 saisons :</b><br />
						</div>
						{$mois_envoi1}<br />
						{$mois_envoi2}<br />
						{$mois_envoi3}<br />
						{$mois_envoi4}
					</div>
					<div class="saison_box_fin">
						Date limite pour bénéficier de la box en cours : le 10 du mois d'expédition (10 janvier, 10 avril, 10 juillet, 10 octobre)
					</div>
				</div>
			</div>
			
			<!-- left infos-->
			<div id="pb-left-column-box">
<div class="product-actions js-product-actions">
				<!-- add to cart form-->
				<form action="{$urls.pages.cart}" method="post" id="add-to-cart-or-refresh">
                  <input type="hidden" name="token" value="{$static_token}">
                  <input type="hidden" name="id_product" value="{$product.id}" id="product_page_product_id">
                  <input type="hidden" name="id_customization" value="{$product.id_customization}" id="product_customization_id" class="js-product-customization-id">


					<!-- hidden datas -->
					<p class="hidden">
						<input type="hidden" name="token" value="{$static_token}" />
						<input type="hidden" name="id_product" value="{$product->id|intval}" id="product_page_product_id" />
						<input type="hidden" name="add" value="1" />
						<input type="hidden" name="id_product_attribute" id="idCombination" value="" />
					</p>

					<!-- attributes -->
					{*<div id="attributes" class="attributes_box">
						<h3>Configurez votre box</h3>
						<table>
							{foreach from=$groups key=id_attribute_group item=group}
								<tr><td class="declinaison_name">{$group.name} :</td>
									{assign var='groupName' value='group_'|cat:$id_attribute_group}
							
									<td>
										<table>
											{foreach from=$group.attributes key=id_attribute item=group_attribute}
												<tr>
													<td><input type="radio" data-product-attribute="{$id_attribute_group}" name="group[{$id_attribute_group}]" value="{$id_attribute}" title="{$group_attribute.name}"{if $group_attribute.selected} checked="checked"{/if} /></td>
													<td>{$group_attribute.name}</td>
												</tr>
											{/foreach}
										</table>
									</td>
								</tr>
							{/foreach}
						</table>
					</div>*}
					{block name='product_variants'}
                      {include file='catalog/_partials/product-variants.tpl'}
                    {/block}

		
		
					<!-- Customizable products -->
					{if $product->customizable}
					<div class="livraison_box">
						<div class="customization_block custom_box">

								{if $product->uploadable_files|intval}
									<h2>{l s='Pictures'}</h2>


									<ul id="uploadable_files">
										{counter start=0 assign='customizationField'}
										{foreach from=$customizationFields item='field' name='customizationFields'}
											{if $field.type == 0}
												<li class="customizationUploadLine{if $field.required} required{/if}">{assign var='key' value='pictures_'|cat:$product->id|cat:'_'|cat:$field.id_customization_field}
													{if isset($pictures.$key)}<div class="customizationUploadBrowse"><img src="{$pic_dir}{$pictures.$key}_small" alt="" /><a href="{$link->getUrlWith('deletePicture', $field.id_customization_field)}"><img src="{$img_dir}icon/delete.gif" alt="{l s='delete'}" class="customization_delete_icon" /></a></div>{/if}
													<div class="customizationUploadBrowse"><input type="file" name="file{$field.id_customization_field}" id="img{$customizationField}" class="customization_block_input {if isset($pictures.$key)}filled{/if}" />{if $field.required}<sup>*</sup>{/if}
														<div class="customizationUploadBrowseDescription">{if !empty($field.name)}{$field.name}{else}{l s='Please select an image file from your hard drive'}{/if}</div></div>
												</li>
												{counter}
											{/if}
										{/foreach}
									</ul>
								{/if}
								<div class="clear"></div>
								{if $product->text_fields|intval}
									<h2>Adresse de livraison</h2>

									<p>
										<img src="/themes/lbg/assets/img/infos.gif" alt="Informations" />
										{l s="Indiquez l'adresse de livraison pour cette box"}
										{if $product->uploadable_files}<br />{l s='Allowed file formats are: GIF, JPG, PNG'}{/if}
									</p>
									<ul id="text_fields_box">
										{counter start=0 assign='customizationField'}
										{$pos_custom=0}
										{foreach from=$customizationFields item='field' name='customizationFields'}
											{if $field.type == 1}
												<li id="custom_box_{$pos_custom}" class="{if $pos_custom!=3}li_required {/if}customizationUploadLine{if $field.required} required{/if}">{assign var='key' value='textFields_'|cat:$product->id|cat:'_'|cat:$field.id_customization_field}
													<input placeholder="{if !empty($field.name)}{$field.name}{/if}{if $pos_custom!=3} *{/if}" type="text" name="textField{$field.id_customization_field}" id="textField{$customizationField}" value="{if isset($textFields.$key)}{$textFields.$key|stripslashes}{/if}" class="customization_block_input" />{if $field.required}<sup>*</sup>{/if}

											</li>
											{counter}
											{$pos_custom=$pos_custom+1}
										{/if}
									{/foreach}
								</ul>
							{/if}
							<p style="clear: left;" id="customizedDatas">
								<input type="hidden" name="quantityBackup" id="quantityBackup" value="" />
								<input type="hidden" name="submitCustomizedDatas" value="1" />
							</p>

					</div>
					{/if}
						
					<!-- quantity wanted -->
					<input type="hidden" name="qty" value="1" />
						
					<p class="clear W_85">Box exp&eacute;di&eacute;e en France m&eacute;tropolitaine uniquement</p>
					<p class="W_85"><b>C'est pour offrir ?</b><br/>
						N'ayez crainte ! Aucun email ne sera envoy&eacute; avant le jour de l'exp&eacute;dition.
					</p>
					
					<div>
						{block name='product_prices'}
							{include file='catalog/_partials/product-prices.tpl'}
						{/block}
					</div>
					
				</div>
				
				{block name='product_add_to_cart'}
                    <div class="product-add-to-cart js-product-add-to-cart">
						{if !$configuration.is_catalog}

							{block name='product_availability'}
								<span id="product-availability" class="js-product-availability">
							{if $product.show_availability && $product.availability_message}
								{if $product.availability == 'available'}
									<label id="availability_label">Disponibilité : </label>
								{elseif $product.availability == 'last_remaining_items'}
								<i class="material-icons product-last-items">&#xE002;</i>
							  {else}
								<i class="material-icons product-unavailable">&#xE14B;</i>
								{/if}


								{if $product.availability == 'last_remaining_items'}
									{$product.availability_message}
								{else}
									{if $product.quantity > 0 }
										{if $product.available_now != ""}
											<span id="availability_value" class="label-success">En stock</span>
										{else}
											{l s='Produit en stock'}
										{/if}
									{else}
										{if $product.not_available_message != ""}
											{$product.not_available_message}
										{elseif $product.available_later != ""}
											{$product.available_later}
										{else}
											{l s='Rupture stock'}
										{/if}
									{/if}
								{/if}
							{/if}
						  </span>
							{/block}

							{block name='product_minimal_quantity'}
								<p class="product-minimal-quantity js-product-minimal-quantity">
									{if $product.minimal_quantity > 1}
										{l
										s='The minimum purchase order quantity for the product is %quantity%.'
										d='Shop.Theme.Checkout'
										sprintf=['%quantity%' => $product.minimal_quantity]
										}
									{/if}
								</p>
							{/block}
							
							
							<div class="product-add-to-cart clearfix">

								{if $product.availability == 'last_remaining_items'}
									{$product.availability_message}
								{else}
									{if $product.quantity > 0 }
										{if $product.available_now != ""}
											<button
													class="btn btn-primary add-to-cart"
													data-button-action="add-to-cart"
													type="submit"
													style="display:none"
											>
												<i class="material-icons shopping-cart">&#xE547;</i>
												{l s='Add to cart' d='Shop.Theme.Actions'}
											</button>
											<button
													class="add_to_the_cart_fake"
													type="button"
													{if !$product.add_to_cart_url}
														disabled
													{/if}
											>
												<i class="material-icons shopping-cart">&#xE547;</i>
												{l s='Add to cart' d='Shop.Theme.Actions'}
											</button>
										{/if}
									{else}
										<div class="product-additional-info js-product-additional-info">
								  {hook h='displayProductAdditionalInfo' product=$product}
								</div>
									{/if}
								{/if}
								

								



								{hook h='displayProductActions' product=$product}
							</div>
						{/if}
					</div>
				{/block}
				
			</form>
		</div>
		</div>
	</div>
	
	<div class="section_box">
	  {block name='product_footer'}
      {hook h='displayFooterProduct' product=$product category=$category}
    {/block}
  </div>

{/block}
