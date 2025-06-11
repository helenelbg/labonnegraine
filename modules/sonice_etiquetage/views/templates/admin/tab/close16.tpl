{**
 * NOTICE OF LICENSE
 *
 * This source file is subject to a commercial license from Common-Services Co., Ltd.
 * Use, copy, modification or distribution of this source file without written
 * license agreement from the SARL SMC is strictly forbidden.
 * In order to obtain a license, please contact us: contact@common-services.com
 * ...........................................................................
 * INFORMATION SUR LA LICENCE D'UTILISATION
 *
 * L'utilisation de ce fichier source est soumise a une licence commerciale
 * concedee par la societe Common-Services Co., Ltd.
 * Toute utilisation, reproduction, modification ou distribution du present
 * fichier source sans contrat de licence ecrit de la part de la Common-Services Co. Ltd. est
 * expressement interdite.
 * Pour obtenir une licence, veuillez contacter Common-Services Co., Ltd. a l'adresse: contact@common-services.com
 * ...........................................................................
 *
 * @package   SoNice_Etiquetage
 * @author    Alexandre D. <alexandre@common-services.com>
 * @copyright Copyright (c) 2011-2015 Common Services Co Ltd - 90/25 Sukhumvit 81 - 10260 Bangkok - Thailand
 * @license   Commercial license
 * Support by mail  :  support.sonice@common-services.com
 *}

<div id="tab-close" style="display: none;">
    
    <nav class="navbar navbar-default" role="navigation">
        <div class="container-fluid">
            <div class="navbar-header" style="margin-top: 15px;">
                <a class="navbar-brand" href="#">{l s='Orders' mod='sonice_etiquetage'} <small class="current_session_name"></small></a>
            </div>
            <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                <ul class="nav navbar-nav navbar-right" style="text-align: center;">
                    <li id="generate_slip"><a href="#"><img src="{$sne_img|escape:'htmlall':'UTF-8'}inbox_download.png" alt="listing"><br>{l s='Generate deposite slip' mod='sonice_etiquetage'}</a></li>
                </ul>
                <ul class="nav navbar-nav navbar-right" style="text-align: center;">
                    <li id="download_cn23"><a href="#"><img src="{$sne_img|escape:'htmlall':'UTF-8'}document_mark_as_final.png" alt="cn23"><br>{l s='Download CN23' mod='sonice_etiquetage'}</a></li>
                </ul>
                <ul class="nav navbar-nav navbar-right" style="text-align: center;">
                    <li id="download_csv" style="display: none;"><a href="{$sne_module_url|escape:'htmlall':'UTF-8'}download/expeditor.csv"><img src="{$sne_img|escape:'htmlall':'UTF-8'}file_extension_csv.png" alt="listing"><br>{l s='Download CSV' mod='sonice_etiquetage'}</a></li>
                </ul>
            </div>
        </div>
    </nav>
            
    <div class="cleaner">&nbsp;</div>
            
    <!-- Layus -->
    <div class="alert alert-info">
        {l s='This form has to be given to La Poste as a certificate of deposit.' mod='sonice_etiquetage'}
    </div>
	
	<div class="alert alert-danger" id="sne_error_close" style="display: none;">
		<code></code>
	</div>
    <div class="alert alert-warning" id="sne_warning_close" style="display: none;">
        <code></code>
    </div>
    <div class="alert alert-success" id="sne_success_close" style="display: none;">
        <code></code>
    </div>
    <div class="alert alert-info" id="sne_info_close" style="display: none;">
        <code></code>
    </div>
    <span></span>
</div>