<?php
$post_action = Tools::getValue('action');

if (!empty($post_action) && $post_action == 'do_check')
{
    $content = '';
    $results = 'OK';

    $found = false;
    if (file_exists(_PS_CLASS_DIR_.'Validate.php')
        && !file_exists(_PS_OVERRIDE_DIR_.'classes/Validate.php'))
    {
        $content = file_get_contents(_PS_CLASS_DIR_.'Validate.php');
        $needle = <<<'EOT'
return preg_match('/^[a-zA-Z0-9.!_-]+$/', $order);
EOT;
        // code BIEN trouve => error
        if (strpos($content, $needle) !== false)
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
            echo '<p><strong>'._l('Verify your shop vulnerability to %s', null, array('CVE-2021-43789')).'</strong></p>'; ?>
            <div style="clear: both">
                <?php echo '<ul><li>'._l('The first solution is to upgrade your PrestaShop version to %s or above. Please contact your web agency.', null, array('1.7.8.2')).'</li>
                    <li>'._l('The second solution is to contact your web agency to patch your shop.').'</li>';
        echo '</ul>'; ?>
            </div>
            <div style="clear: both">
                <?php
                echo _l('Technical information about this security issue:');
        echo '<ul>
                    <li><a href="https://github.com/PrestaShop/PrestaShop/security/advisories/GHSA-6xxj-gcjq-wgf4" target="_blank">'._l('Link to the official description').'</a></li>
                    <li><a href="https://github.com/PrestaShop/PrestaShop/commit/6482b9ddc9dcebf7588dbfd616d2d635218408d6" target="_blank">'._l('Link to technical information to patch your shop').'</a></li>
                    <li><a href="https://cve.mitre.org/cgi-bin/cvename.cgi?name=CVE-2021-43789" target="_blank">'._l('Link to CVE details').'</a></li>
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
