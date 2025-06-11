<div class="utmstat_nav">
	<div class="buttonNav {if $active eq "Commandes"}active{/if}" data-utm="Commandes"><span>{l s='Commandes' d='Modules.utm'}</span></div>
	<div class="buttonNav {if $active eq "Clients"}active{/if}" data-utm="Clients"><span>{l s='Clients' d='Modules.utm'}</span></div>
	<div class="buttonNav {if $active eq "Statistiques"}active{/if}" data-utm="Statistiques"><span>{l s='Statistiques' d='Modules.utm'}</span></div>
	<div class="buttonNav" data-utm="config"><span>{l s='Configuration' d='Modules.utm'}</span></div>

	<form method="post" action="{$controllerUrl}" id="dispForm">
		<input type="hidden" name="page_fragment" value=""></button>
	</form>		
</div>

{include file=$fragments}

{literal}
<style>
	.utmstat_nav{
		height: 100px;
		background-color: white;
		border-radius: 5px;
		border: 1px #d3d8db solid;
		margin-bottom: 20px;
	}

	.utmstat_nav .buttonNav{
		height: calc(100% - 10px);
		width: calc((100% - 10px) / 4);
		position: relative;
		top: 5px;
		border: none;
		outline: none;
		float: left;
		transform: translateX(5px);
		cursor: pointer;
	}

	.utmstat_nav .buttonNav span{
		position: absolute;
		top: 50%;
		left: 50%;
		transform: translate(-50%, -50%);
		font-size: 18px;
		color: #888;
		font-weight: bold;
	}

	.utmstat_nav .buttonNav:nth-child(1):before, .utmstat_nav .buttonNav:nth-child(2):before, .utmstat_nav .buttonNav:nth-child(3):before{
		content: "";
		width: 1px;
		background-color: #BBB;
		height: 60%;
		position: absolute;
		transform: translateY(-50%);
		top: 50%;
		right: 0;
	}

	.utmstat_nav .buttonNav:nth-child(1){
		border-top-left-radius: 5px;
		border-bottom-left-radius: 5px;
	}

	.utmstat_nav .buttonNav:nth-child(3){
		border-top-right-radius: 5px;
		border-bottom-right-radius: 5px;
	}

	.utmstat_nav .buttonNav:hover, .utmstat_nav .buttonNav.active{
		background-color: #f1f4f2;
	}
</style>

<script>
	$(".buttonNav").on("click", function(){

		if($(this).attr("data-utm") === "config"){
			{/literal}
			window.location.href = "index.php?controller=AdminModules&configure=utm&token={$token}";
			{literal}
		}else{
			$("#dispForm input").val($(this).attr("data-utm"));
			$("#dispForm").submit();
		}
	});
</script>
{/literal}