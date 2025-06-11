<?php
$post_action = Tools::getValue('action');

$files = array(
    _PS_ROOT_DIR_.'/blm.php',
);

if (!empty($post_action) && $post_action == 'do_check')
{
    $results = 'OK';
    $found = false;
    $content = $messageHacked = '';

    foreach ($files as $filepath)
    {
        if (file_exists($filepath))
        {
            $found = true;
            break;
        }
    }

    if ($found)
    {
        $results = 'KO';
        $messageHacked = '<p><strong>'._l('Your shop seems to have been hacked! Please contact your web agency to patch your shop.').'</strong></p>';
        foreach ($files as $filepath)
        {
            if (file_exists($filepath))
            {
                $messageHacked .= '<p>- <span style="color: #777777;"></span>'.$filepath.'</p>';
            }
        }
    }

    if (Configuration::get('PS_SMARTY_CACHE')
        && Configuration::get('PS_SMARTY_CACHING_TYPE') == 'mysql'
    ) {
        if(file_exists(_PS_ROOT_DIR_.'/classes/Smarty/SmartyCacheResourceMysql.php'))
        {
            require_once _PS_ROOT_DIR_.'/classes/Smarty/SmartyCacheResourceMysql.php';
        }
        else
        {
            require_once _PS_ROOT_DIR_.'/classes/SmartyCacheResourceMysql.php';
        }

        $smartyCacheMysqlClass = new ReflectionClass('Smarty_CacheResource_Mysql');
        if (!$smartyCacheMysqlClass->hasProperty('phpEncryption'))
        {
            $results = 'KO';
        }
    }
    if ($results == 'KO')
    {
        ob_start(); ?>
        <div style="padding: 20px;height: 100%;overflow: auto;">
                <img src="./lib/img/security.png" alt="" style="float: left; margin-right: 20px; margin-bottom: 20px;"/>
                <?php
                echo '<p><strong>'._l('Verify your shop vulnerability to %s', null, array('CVE-2022-31181')).'</strong></p>'; ?>
        <div style="clear: both">
            <?php echo '<ul><li>'._l('The first solution is to upgrade your PrestaShop version to %s or above. Please contact your web agency.', null, array('1.7.8.7')).'</li>
                        <li>'._l('The second solution is to contact your web agency to patch your shop.').'</li>';
        echo '</ul>'; ?>
        </div>
        <?php if ($messageHacked != ''){ ?>
        <div style="margin-top: 30px; background: red; border: 1px solid red; padding: 10px;color:white;">
            <?php echo $messageHacked; ?>
        </div>
        <br/>
        <?php } ?>
        <div style="clear: both">
            <?php
            echo _l('Technical information about this security issue:');
        echo '<ul>
                        <li><a href="https://github.com/PrestaShop/PrestaShop/security/advisories/GHSA-hrgx-p36p-89q4" target="_blank">'._l('Link to the official description').'</a></li>
                        <li><a href="https://github.com/PrestaShop/PrestaShop/commit/972e12bb07e50cf3e6e5d8aa48c3414fe988cbec" target="_blank">'._l('Link to technical information to patch your shop').'</a></li>
                        <li><a href="https://cve.mitre.org/cgi-bin/cvename.cgi?name=CVE-2022-31181" target="_blank">'._l('Link to CVE details').'</a></li>
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
