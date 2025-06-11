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

{function name="categories" nodes=[] depth=0}
  {$categoryId = 0}
  {if isset($category)}
    {if isset($category->id)}
      {$categoryId = $category->id} {* Pages fiches produit *}
    {elseif isset($category.id)}
      {$categoryId = $category.id} {* Pages listing produit *}
    {/if}
  {/if}
  
  {strip}
    {if $nodes|count}
      <ul class="category-sub-menu">
	    
        {foreach from=$nodes item=node}
		  {if $node.id != 5 && $node.id != 227 } {* Semis et Box *}
			  {$expanded = false}
			  {if $node.id == $categoryId}
					{$expanded = true}
				{/if}
			  {foreach from=$node.children item=child}
				{if $child.id == $categoryId}
					{$expanded = true}
				{/if}
				{foreach from=$child.children item=child2}
					{if $child2.id == $categoryId}
						{$expanded = true}
					{/if}
				{/foreach}
			  {/foreach}
			  <li data-depth="{$depth}" {if $node.id == $categoryId}class="active"{/if}>
				  <a href="{$node.link}">{$node.name}</a>
				  {if $node.children}
					<div class="navbar-toggler collapse-icons" data-toggle="collapse" data-target="#exCollapsingNavbar{$node.id}" {if $expanded} aria-expanded="true"{/if}>
					  <i class="material-icons add">&#xE145;</i>
					  <i class="material-icons remove">&#xE15B;</i>
					</div>
					<div class="collapse{if $expanded} in{/if}" id="exCollapsingNavbar{$node.id}">
					  {categories nodes=$node.children depth=$depth+1}
					</div>
				  {/if}
			  </li>
			{/if}
        {/foreach}
      </ul>
    {/if}
  {/strip}

{/function}

<div class="block-categories">
  <ul class="category-top-menu">
    <li><div class="text-uppercase h6">
	Notre catalogue
	{*if isset($category)}
      {if isset($category->name)} 
		{$category->name}
	  {elseif isset($category.name)} 
	    {$category.name}
	  {/if}
	{else}
	  {$categories.name}
	{/if*}
	</div></li>
    {if !empty($categories.children)}
      <li>{categories nodes=$categories.children}</li>
    {/if}
  </ul>
</div>

{if Configuration::get('MP_VISUEL_1')}
	{assign var='visuel_1' value='/upload/'|cat:Configuration::get('MP_VISUEL_1')}
	<div class="visuel_colonne_gauche">
		<br />
		<a href="/content/89-reseaux-sociaux">
			<img src="{$visuel_1}" alt="" />
		</a><br />&nbsp;
	</div>
{/if}			
