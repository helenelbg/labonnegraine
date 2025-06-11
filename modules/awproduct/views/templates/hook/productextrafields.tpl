<fieldset class="form-group">
	
	<div class="mb-4">
		<input id="coeur" type="checkbox" name="coeur" {$coeur} />
		<label for="coeur">Coup de coeur ♥</label>
	</div>

	<div class="mb-4">
	{if $type == 1}
		{$checked1 = " checked"}
		{$checked2 = ""}
	{else}
		{$checked1 = ""}
		{$checked2 = " checked"}
	{/if}
	<input id="type1" type="radio" name="type" value="1"{$checked1} />
	<label for="type1">Jardin d'essai</label>
	&nbsp;&nbsp;&nbsp;
	<input id="type2" type="radio" name="type" value="2"{$checked2} />
	<label for="type2">Influenceur</label>
	</div>

	<div class="mb-4">
		<label for="jardin_titre">Titre encart</label>
		<input id="jardin_titre" type="text" name="jardin_titre" class="form-control" value="{$jardin_titre}"/>
	</div>
	
	<div class="mb-4">
		<label for="jardin_contenu">Contenu encart</label>
		<textarea id="jardin_contenu" name="jardin_contenu" class="autoload_rte">{$jardin_contenu}</textarea>
	</div>
	
	<div class="mb-4">
		<label for="botanic_name">Nom botanique</label>
		<input id="botanic_name" type="text" name="botanic_name" class="form-control" value="{$botanic_name}"/>
	</div>
	

</fieldset>
<div class="clearfix"></div>


<div class="sachet-container">
	<h2>Informations sachet</h2>
    <div class="mb-4">
		<label for="sachet_titre1">Titre 1</label>
		<input id="sachet_titre1" type="text" name="sachet_titre1" class="form-control" value="{$sachet_titre1}"/>
	</div>
    <div class="mb-4">
		<label for="sachet_titre2">Titre 2</label>
		<input id="sachet_titre2" type="text" name="sachet_titre2" class="form-control" value="{$sachet_titre2}"/>
	</div>
	<div class="mb-4">
		<label for="sachet_desc_recto">Description recto</label>
		<textarea id="sachet_desc_recto" name="sachet_desc_recto" class="autoload_rte">{$sachet_desc_recto}</textarea>
	</div>
	<div class="mb-4">
		<label for="sachet_desc_verso">Description verso</label>
		<textarea id="sachet_desc_verso" name="sachet_desc_verso" class="autoload_rte">{$sachet_desc_verso}</textarea>
	</div>
    <div class="mb-4">
		<label for="sachet_normes">Normes</label>
		<input id="sachet_normes" type="text" name="sachet_normes" class="form-control" value="{$sachet_normes}"/>
	</div>
	<div class="mb-4">
		<input id="sachet_passphy" type="checkbox" name="sachet_passphy" {$sachet_passphy} />
		<label for="sachet_passphy">Passeport Phyto</label>
	</div>
</div>
