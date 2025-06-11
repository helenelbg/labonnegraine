<?php
$post_action = Tools::getValue('action');

if (!empty($post_action) && $post_action == 'do_check')
{
    $content = '';
    $results = 'OK';

    $found = false;
    if (file_exists(_PS_CORE_DIR_.'/src/PrestaShopBundle/Twig/LayoutExtension.php'))
    {
        $content = file_get_contents(_PS_CORE_DIR_.'/src/PrestaShopBundle/Twig/LayoutExtension.php');
        $needle = <<<'EOT'
private function escapeSmarty(
EOT;
        ## si fonction PAS trouve => error
        if (strpos($content, $needle) === false)
        {
            $found = true;
        }
    }

    if ($found)
    {
        $results = 'KO';
        ob_start(); ?>
        <div style="padding: 20px;height: 100%;overflow: auto;">
            <img src="./lib/img/security.png" alt="" style="float: left; margin-right: 20px; margin-bottom: 20px;"/>
            <?php
            echo '<p><strong>'._l('Verify your shop vulnerability to %s', null, array('CVE-2022-21686')).'</strong></p>'; ?>
            <div style="clear: both">
                <?php echo '<ul><li>'._l('The first solution is to upgrade your PrestaShop version to %s or above. Please contact your web agency.', null, array('1.7.8.3')).'</li>
                    <li>'._l('The second solution is to contact your web agency to patch your shop.').'</li>';
        echo '</ul>'; ?>
            </div>
            <div style="clear: both">
                <?php
                echo _l('Technical information about this security issue:');
        echo '<ul>
                    <li><a href="https://github.com/PrestaShop/PrestaShop/security/advisories/GHSA-mrq4-7ch7-2465/" target="_blank">'._l('Link to the official description').'</a></li>
                    <li><a href="https://github.com/PrestaShop/PrestaShop/commit/d02b469ec365822e6a9f017e57f588966248bf21" target="_blank">'._l('Link to technical information to patch your shop').'</a></li>
                    <li><a href="https://cve.mitre.org/cgi-bin/cvename.cgi?name=CVE-2022-21686" target="_blank">'._l('Link to CVE details').'</a></li>
                </ul>'; ?>
            </div>
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
