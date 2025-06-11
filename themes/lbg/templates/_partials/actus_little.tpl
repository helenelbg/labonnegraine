<div class="position_actus">
	<div id="actus_header">
		<div id="news-container1">
			<ul>
			{foreach from=Tools::getActualites() key=k item=actu}
				<li><a href="{$link->getCMSLink($actu.id_cms, $actu.link_rewrite)}" title="{$actu.meta_title}"><p><u>{$actu.meta_title}</u>
				{if $actu.meta_description}
				: {$actu.meta_description}
				{/if}</p></a>
				</li>
			{/foreach}
			</ul>
		</div>
	</div>
</div>

