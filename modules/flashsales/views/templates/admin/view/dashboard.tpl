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

<div id="flashsale_dashboard">
  <div class="row">
    <div class="col-lg-12">
      <div class="panel">
        <div class="panel-heading">
          <i class="icon-bars"></i> {l s='Dashboard' mod='flashsales'}
      	</div>
        <div class="form-group">
        	<ul class="nav nav-pills" role="tablist">
        	  <li role="presentation" class="active"><a href="#active" role="tab" data-toggle="tab">{l s='Active' mod='flashsales'}</a></li>
        	  <li role="presentation"><a href="#pending" role="tab" data-toggle="tab">{l s='Pending' mod='flashsales'}</a></li>
        	  <li role="presentation"><a href="#expired" role="tab" data-toggle="tab">{l s='Expired' mod='flashsales'}</a></li>
        	</ul>
        </div>
        <div class="tab-content">
        {if isset($flash_sales) && count($flash_sales)}
            {foreach from=$flash_sales key=id item=flash_sale}
                {include file="$tpl_list" id=$id flash_sale=$flash_sale}
            {/foreach}
        {/if}
        </div>
      </div>
    </div>
  </div>
</div>
