<div class="panel">
	<h3><i class="icon-cogs"></i> {l s='Informations' d='Modules.utm'}</h3>
	<div class="row">
		<div class="alert alert-info">
		{l s='La syntaxe à respecter pour les UTM est la suivante :' d='Modules.utm'}
		<ul>
			<li>{l s='utm_source => ' d='Modules.utm'}<b>{l s='?utm_source=test1' d='Modules.utm'}</b></li>
			<li>{l s='utm_campaign => ' d='Modules.utm'}<b>{l s='?utm_campaign=test2' d='Modules.utm'}</b></li>
			<li>{l s='utm_medium => ' d='Modules.utm'}<b>{l s='?utm_medium=test3' d='Modules.utm'}</b></li>
		</ul>
		<br>
		{l s='Les paramètres sont cumulables : ' d='Modules.utm'}<b>{l s='?utm_source=test1&utm_campaign=test2&utm_medium=test3' d='Modules.utm'}</b>
		<br>
		<br>
		{l s='Vous pouvez définir une tâche cron qui va supprimer les UTM en fonction de leurs dates d\'expiration en utilisant l\'URL suivante :' d='Modules.utm'}
		<br>
		<strong>{$CRON_URL}</strong>
		<br>
		<br>
		{l s='Il est recommandé de planifier la tâche cron tous les jours (de préférence à une heure où le trafic du site est peu élevé, la nuit par exemple).' d='Modules.utm'}
		</div>
	</div>
</div>

<div class="panel">
	<h3><i class="icon-cogs"></i> {l s='Paramètres' d='Modules.utm'}</h3>
	<div class="row">
		<div class="alert alert-info">{l s='Les changements pour les cookies auront lieu uniquement lors des nouvelles générations de cookies et n\'auront aucun effet sur les cookies déjà en place' d='Modules.utm'}</div>
	</div>
    <form action="" method="post" class="form-horizontal">
        <div class="form-group">
			<label class="col-lg-3 control-label">{l s='Durée des cookies (en jours)' d='Modules.utm'}</label>
			<div class="col-lg-9">
				<input type="text" name="UTM_COOKIE_DURATION" value="{$UTM_COOKIE_DURATION}" />
			</div>
		</div>
		<div class="form-group">
			<label class="col-lg-3 control-label">{l s='Conserver les UTM après la première commande et jusqu\'à l\'expiration' d='Modules.utm'}</label>
			<div class="col-lg-9">
				<span class="switch prestashop-switch fixed-width-lg">
					<input type="radio" name="UTM_COOKIE_EXPIRE" id="UTM_COOKIE_EXPIRE_on" value="yes" {if $UTM_COOKIE_EXPIRE eq "yes"} checked="checked"{/if}>
					<label for="UTM_COOKIE_EXPIRE_on" class="radioCheck">
						<i class="color_success"></i> {l s='Yes' d='Admin.Global'}
					</label>
					<input type="radio" name="UTM_COOKIE_EXPIRE" id="UTM_COOKIE_EXPIRE_off" value="no" {if $UTM_COOKIE_EXPIRE eq "no"} checked="checked"{/if}>
					<label for="UTM_COOKIE_EXPIRE_off" class="radioCheck">
						<i class="color_danger"></i> {l s='No' d='Admin.Global'}
					</label>
					<a class="slide-button btn"></a>
				</span>
			</div>
		</div>
        <div class="panel-footer">
			<button type="submit" class="btn btn-default pull-right" name="submitCookie"><i class="process-icon-save"></i> {l s='Save' d='Admin.Actions'}</button>
		</div>
    </form>
</div>

<div class="panel">
	<h3><i class="icon-cogs"></i> {l s='Afficher dans les commandes' d='Modules.utm'}</h3>
	<form action="" method="post" class="form-horizontal">
		<div class="form-group">
			<label class="col-lg-3 control-label">{l s='utm_source' d='Modules.utm'}</label>
			<div class="col-lg-9">
				<span class="switch prestashop-switch fixed-width-lg">
					<input type="radio" name="UTM_SOURCE_VALUE" id="UTM_SOURCE_VALUE_on" value="yes" {if $UTM_SOURCE_VALUE eq "yes"} checked="checked"{/if}>
					<label for="UTM_SOURCE_VALUE_on" class="radioCheck">
						<i class="color_success"></i> {l s='Yes' d='Admin.Global'}
					</label>
					<input type="radio" name="UTM_SOURCE_VALUE" id="UTM_SOURCE_VALUE_off" value="no" {if $UTM_SOURCE_VALUE eq "no"} checked="checked"{/if}>
					<label for="UTM_SOURCE_VALUE_off" class="radioCheck">
						<i class="color_danger"></i> {l s='No' d='Admin.Global'}
					</label>
					<a class="slide-button btn"></a>
				</span>
			</div>
		</div>
        <div class="form-group">
			<label class="col-lg-3 control-label">{l s='utm_medium' d='Modules.utm'}</label>
			<div class="col-lg-9">
				<span class="switch prestashop-switch fixed-width-lg">
					<input type="radio" name="UTM_MEDIUM_VALUE" id="UTM_MEDIUM_VALUE_on" value="yes" {if $UTM_MEDIUM_VALUE eq "yes"} checked="checked"{/if}>
					<label for="UTM_MEDIUM_VALUE_on" class="radioCheck">
						<i class="color_success"></i> {l s='Yes' d='Admin.Global'}
					</label>
					<input type="radio" name="UTM_MEDIUM_VALUE" id="UTM_MEDIUM_VALUE_off" value="no" {if $UTM_MEDIUM_VALUE eq "no"} checked="checked"{/if}>
					<label for="UTM_MEDIUM_VALUE_off" class="radioCheck">
						<i class="color_danger"></i> {l s='No' d='Admin.Global'}
					</label>
					<a class="slide-button btn"></a>
				</span>
			</div>
		</div>
        <div class="form-group">
			<label class="col-lg-3 control-label">{l s='utm_campaign' d='Modules.utm'}</label>
			<div class="col-lg-9">
				<span class="switch prestashop-switch fixed-width-lg">
					<input type="radio" name="UTM_CAMPAIGN_VALUE" id="UTM_CAMPAIGN_VALUE_on" value="yes" {if $UTM_CAMPAIGN_VALUE eq "yes"} checked="checked"{/if}>
					<label for="UTM_CAMPAIGN_VALUE_on" class="radioCheck">
						<i class="color_success"></i> {l s='Yes' d='Admin.Global'}
					</label>
					<input type="radio" name="UTM_CAMPAIGN_VALUE" id="UTM_CAMPAIGN_VALUE_off" value="no" {if $UTM_CAMPAIGN_VALUE eq "no"} checked="checked"{/if}>
					<label for="UTM_CAMPAIGN_VALUE_off" class="radioCheck">
						<i class="color_danger"></i> {l s='No' d='Admin.Global'}
					</label>
					<a class="slide-button btn"></a>
				</span>
			</div>
		</div>
		<div class="panel-footer">
			<button type="submit" class="btn btn-default pull-right" name="submitDisplay"><i class="process-icon-save"></i> {l s='Save' d='Admin.Actions'}</button>
		</div>
	</form>
</div>
