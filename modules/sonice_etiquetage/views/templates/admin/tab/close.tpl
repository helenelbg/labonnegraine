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
    <div class="cleaner">&nbsp;</div>
    
    <!-- Static toolbar / PS1.5 -->
    <div class="toolbar-placeholder">
        <div class="toolbarBox">
            <ul class="cc_button">
                <li>
                    <a href="{$sne_module_url|escape:'htmlall':'UTF-8'}download/expeditor.csv" class="toolbar_btn" id="download_csv" style="display: none;">
                        <span><img src="{$sne_img|escape:'htmlall':'UTF-8'}file_extension_csv.png" id="" alt="slip"></span>
                        <div>{l s='Download CSV' mod='sonice_etiquetage'}</div>
                    </a>
                </li>
                <li>
                    <a class="toolbar_btn" id="download_cn23">
                        <span><img src="{$sne_img|escape:'htmlall':'UTF-8'}document_mark_as_final.png" id="" alt="slip"></span>
                        <div>{l s='Download CN23' mod='sonice_etiquetage'}</div>
                    </a>
                </li>
                <li>
                    <a class="toolbar_btn" id="generate_slip">
                        <span><img src="{$sne_img|escape:'htmlall':'UTF-8'}inbox_download.png" id="" alt="slip"></span>
                        <div>{l s='Generate deposite slip' mod='sonice_etiquetage'}</div>
                    </a>
                </li>
            </ul>
            <div class="pageTitle">
                <h3><span class="current_obj">{l s='Orders' mod='sonice_etiquetage'} <small class="current_session_name"></small></span></h3>
            </div>
        </div>
    </div>
            
    <div class="cleaner">&nbsp;</div>
            
    <!-- Layus -->
    <div class="hint">
        {l s='This form has to be given to La Poste as a certificate of deposit.' mod='sonice_etiquetage'}
    </div>
	
	<div class="conf error" id="sne_error_close" style="display: none;">
		<code></code>
	</div>
    <div class="conf confirm" id="sne_success_close" style="display: none;">
        <code></code>
    </div>
    <div class="conf warn" id="sne_warning_close" style="display: none;">
        <code></code>
    </div>
    <div class="hint" id="sne_info_close" style="display: none;">
        <code></code>
    </div>
    <span></span>
</div>