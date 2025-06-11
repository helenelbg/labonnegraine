{extends file='checkout/_partials/steps/checkout-step.tpl'}

{block name='step_content'}
  {hook h='displayPersonalInformationTop' customer=$customer}

  {if $customer.is_logged && !$customer.is_guest}

    <p class="identity">
      {* [1][/1] is for a HTML tag. *}
      {l s='Connected as [1]%firstname% %lastname%[/1].'
        d='Shop.Theme.Customeraccount'
        sprintf=[
          '[1]' => "<a href='{$urls.pages.identity}'>",
          '[/1]' => "</a>",
          '%firstname%' => $customer.firstname,
          '%lastname%' => $customer.lastname
        ]
      }
    </p>
    <p>
      {* [1][/1] is for a HTML tag. *}
      {l
        s='Not you? [1]Log out[/1]'
        d='Shop.Theme.Customeraccount'
        sprintf=[
        '[1]' => "<a href='{$urls.actions.logout}'>",
        '[/1]' => "</a>"
        ]
      }
    </p>
    {if !isset($empty_cart_on_logout) || $empty_cart_on_logout}
      <p><small>{l s='If you sign out now, your cart will be emptied.' d='Shop.Theme.Checkout'}</small></p>
    {/if}

    <div class="clearfix">
      <form method="GET" action="{$urls.pages.order}">
        <button
          class="continue btn btn-primary float-xs-right"
          name="controller"
          type="submit"
          value="order"
        >
          {l s='Continue' d='Shop.Theme.Actions'}
        </button>
      </form>

    </div>

  {else}
    <section class="login-form js-aw-login-form">
		<h3>Vous avez déjà créé un compte client ?</h3>
		{render file='customer/_partials/login-form.tpl' ui=$login_form}
	</section>
	<section class="login-form js-aw-no-account">
		<div class="no-account">
			<h3>Nouveau client ?</h3>
			<input type="button" class="create-account-button btn-primary js-aw-create-account-button" value="Créer votre compte"> 
		</div>
	</section>


    <div class="tab-content">
      <div class="tab-pane" id="checkout-guest-form" role="tabpanel">
        {render file='checkout/_partials/customer-form.tpl' ui=$register_form guest_allowed=$guest_allowed}
      </div>
    </div>


  {/if}
{/block}
