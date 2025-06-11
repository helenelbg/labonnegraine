{extends file='page.tpl'}

{if $order.details.payment == "Mandat administratif"}
	{include file='checkout/order-confirmation-mandat.tpl'}
{else}
	{block name='page_content_container' prepend}
		<section id="content-hook_order_confirmation" class="card">
		  <div class="card-block">
			<div class="row">
			  <div class="col-md-12">

				{block name='order_confirmation_header'}
				  <h3 class="h1 card-title">
					<i class="material-icons rtl-no-flip done">&#xE876;</i>{l s='Your order is confirmed' d='Shop.Theme.Checkout'}
				  </h3>
				{/block}

				<p>
				  {l s='An email has been sent to your mail address %email%.' d='Shop.Theme.Checkout' sprintf=['%email%' => $order_customer.email]}
				  {if $order.details.invoice_url}
					{* [1][/1] is for a HTML tag. *}
					{l
					  s='You can also [1]download your invoice[/1]'
					  d='Shop.Theme.Checkout'
					  sprintf=[
						'[1]' => "<a href='{$order.details.invoice_url}'>",
						'[/1]' => "</a>"
					  ]
					}
				  {/if}
				</p>

				{block name='hook_order_confirmation'}
				  {$HOOK_ORDER_CONFIRMATION nofilter}
				{/block}

			  </div>
			</div>
		  </div>
		</section>
		{literal}
		<!-- DÉBUT du code Google Avis clients -->
		<script src="https://apis.google.com/js/platform.js?onload=renderOptIn" async defer></script>
		<script type="text/javascript">
		window.renderOptIn = function() { 
			window.gapi.load('surveyoptin', function() {
				window.gapi.surveyoptin.render({
					"merchant_id": 8265898,
					"order_id": "{/literal}{$id_order_google}{literal}",
					"email": "{/literal}{$email_google}{literal}",
					"delivery_country": "{/literal}{$iso_country_google}{literal}",
					"estimated_delivery_date": "{/literal}{$delivery_date_google}{literal}"
				});
			});
		}
		
		</script>
		<!-- FIN du code du module de la fonction d'activation Google Avis Client -->
		{/literal}
	{/block}

	{block name='page_content_container'}
	  <section id="content" class="page-content page-order-confirmation card">
		<div class="card-block">
		  <div class="row">

			{block name='order_confirmation_table'}
			  {include
				file='checkout/_partials/order-confirmation-table.tpl'
				products=$order.products
				subtotals=$order.subtotals
				totals=$order.totals
				labels=$order.labels
				add_product_link=false
			  }
			{/block}

			{if $order_listEC != false}
				{foreach $order_listEC as $order_EC}
					{block name='order_confirmation_table'}
								{include
									file='checkout/_partials/order-confirmation-table.tpl'
									products=$order_EC.products
									subtotals=$order_EC.subtotals
									totals=$order_EC.totals
									labels=$order_EC.labels
									add_product_link=false
								}
								{/block}
				{/foreach}
			{/if}

			{block name='order_details'}
			  <div id="order-details" class="col-md-4">
				<h3 class="h3 card-title">{l s='Order details' d='Shop.Theme.Checkout'}:</h3>
				<ul>
				  <li id="order-reference-value">{l s='Order reference: %reference%' d='Shop.Theme.Checkout' sprintf=['%reference%' => $order.details.reference]}</li>
				  <li>{l s='Payment method: %method%' d='Shop.Theme.Checkout' sprintf=['%method%' => $order.details.payment]}</li>
				  {if !$order.details.is_virtual}
					<li>
					  {l s='Shipping method: %method%' d='Shop.Theme.Checkout' sprintf=['%method%' => $order.carrier.name]}<br>
					  <em>{$order.carrier.delay}</em>
					</li>
				  {/if}
				  {if $order.details.recyclable}
					<li>  
					  <em>{l s='You have given permission to receive your order in recycled packaging.' d="Shop.Theme.Customeraccount"}</em>
					</li>
				  {/if}
				</ul>
			  </div>
			{/block}

		  </div>
		</div>
	  </section>

	  {block name='hook_payment_return'}
		{if ! empty($HOOK_PAYMENT_RETURN)}
		<section id="content-hook_payment_return" class="card definition-list">
		  <div class="card-block">
			<div class="row">
			  <div class="col-md-12">
				{$HOOK_PAYMENT_RETURN nofilter}
			  </div>
			</div>
		  </div>
		</section>
		{/if}
	  {/block}

	  {if !$registered_customer_exists}
		{block name='account_transformation_form'}
		  <div class="card">
			<div class="card-block">
			  {include file='customer/_partials/account-transformation-form.tpl'}
			</div>
		  </div>
		{/block}
	  {/if}

	  {block name='hook_order_confirmation_1'}
		{hook h='displayOrderConfirmation1'}
	  {/block}

	  {block name='hook_order_confirmation_2'}
		<section id="content-hook-order-confirmation-footer">
		  {hook h='displayOrderConfirmation2'}
		</section>
	  {/block}
	{/block}
	
{/if}
