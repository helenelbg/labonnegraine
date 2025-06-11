<?php

use PrestaShop\PrestaShop\Core\Addon\Module\ModuleManagerBuilder;

$post_action = Tools::getValue('action');

if (!empty($post_action) && $post_action == 'do_check')
{
    $content = '';
    $results = 'OK';

    if (version_compare(_PS_VERSION_, '1.7.0.0', '>='))
    {
        $moduleManagerBuilder = ModuleManagerBuilder::getInstance();
        $moduleManager = $moduleManagerBuilder->build();
        $blockwishlistIsInstalled = $moduleManager->isInstalled('blockwishlist');
    }
    else
    {
        $blockwishlistIsInstalled = Module::isInstalled('blockwishlist');
    }

    $blockwishlistIsEnabled = Module::isEnabled('blockwishlist');
    $blockwishlistModuleInstance = Module::getInstanceByName('blockwishlist');

    if ($blockwishlistIsInstalled && $blockwishlistIsEnabled && version_compare($blockwishlistModuleInstance->version, '2.1.0', '<='))
    {
        $results = 'KO';
        ob_start(); ?>
        <div style="padding: 20px;height: 100%;overflow: auto;">
            <img src="./lib/img/security.png" alt="" style="float: left; margin-right: 20px; margin-bottom: 20px;"/>
            <?php
            echo '<p><strong>'._l('Verify your shop vulnerability to %s', null, array('CVE-2022-31101')).'</strong></p>'; ?>
            <div style="clear: both">
                <?php echo '<ul><li>'._l('The first solution is to upgrade your blockwishlist module version to %s or above. Please contact your web agency.', null, array('2.1.1')).'</li>
                        <li>'._l('The second solution is to contact your web agency to patch your shop.').'</li>';
        echo '</ul>'; ?>
            </div>
            <div style="clear: both">
                <?php
                echo _l('Technical information about this security issue:');
        echo '<ul>
                        <li><a href="https://github.com/PrestaShop/blockwishlist/security/advisories/GHSA-2jx3-5j9v-prpp" target="_blank">'._l('Link to the official description').'</a></li>
                        <li><a href="https://github.com/PrestaShop/blockwishlist/commit/b3ec4b85af5fd73f74d55390b226d221298ca084" target="_blank">'._l('Link to technical information to patch your shop').'</a></li>
                        <li><a href="https://cve.mitre.org/cgi-bin/cvename.cgi?name=CVE-2022-31101" target="_blank">'._l('Link to CVE details').'</a></li>
                    </ul>'; ?>
            </div>
            <br/>
        </div>
        <?php
        $content = ob_get_clean();
    }

    echo json_encode(array(
        'results' => $results,
        'contentType' => 'content',
        'content' => $content,
        'title' => _l('Security Files'),
    ));
}
