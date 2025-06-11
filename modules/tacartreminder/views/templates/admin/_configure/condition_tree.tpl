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
 * View for display categories tree in for condition category in setting rule
 *}
<div class="col-lg-12 bootstrap ta-condition-list ta-modal-tree">
	{$category_tree}{*HTML CONTENT*}
</div>
<script type="text/javascript">
	{if $fancy_tree}
	$('#categories-tree-{$condition_group_id|intval}-{$condition_id|intval}').fancytree(
	{
		 checkbox: true,
		 toggleEffect:false,
		 selectMode: 2,
		 minExpandLevel: 2,
		 select: function(event, data) {
		 		console.log('init checkbox');
				// Display list of selected nodes
				var selectednodes = data.tree.getSelectedNodes();
				$('#categories-tree-{$condition_group_id|intval}-{$condition_id|intval}-checkbox-list').html('');
				for (index = 0; index < selectednodes.length; ++index) {
 					var categoryId = selectednodes[index].data.category;
 					$('#categories-tree-{$condition_group_id|intval}-{$condition_id|intval}-checkbox-list')
   					.append(
       					$(document.createElement('input')).attr({
           					name:  'condition_select_{$condition_group_id|intval}_{$condition_id|intval}[]',
           					value: categoryId,
           					type:  'checkbox',
           					checked: true
       					})
    				);
				}
				var checkbox_click = $("input[name^=condition_select_{$condition_group_id|intval}_{$condition_id|intval}]:first");
				updateConditionShortDescriptionForTree(checkbox_click);
			}
	}
	);
	{/if}
	$(document).ready(function() {
			$("input[name^=condition_select_{$condition_group_id|intval}_{$condition_id|intval}]").click(function() {
	 			updateConditionShortDescriptionForTree($(this));
			});
			if($("input[name^=condition_select_{$condition_group_id|intval}_{$condition_id|intval}]").length > 0)
			{
				updateConditionShortDescriptionForTree($("input[name^=condition_select_{$condition_group_id|intval}_{$condition_id|intval}]:first"));
			}
		});
</script>