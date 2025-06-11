<?php
$post_action = Tools::getValue('action');

$path = _PS_ROOT_DIR_.'/';

if (!empty($post_action) && $post_action == 'do_check')
{
    $content = '';
    $results = 'OK';

    $found = array();

    $langs = array();
    $sql = 'SELECT * FROM '._DB_PREFIX_.'lang';
    $tmps = Db::getInstance()->ExecuteS($sql);
    foreach ($tmps as $tmp)
    {
        $langs[$tmp['iso_code']] = $tmp['name'];
    }

    foreach ($langs as $iso => $name)
    {
        $path_file = $path.'mails/'.$iso.'/';
        if (file_exists($path_file.'account.html'))
        {
            $content = file_get_contents($path_file.'account.html');
            if (strpos($content, '{passwd}') !== false)
            {
                $found[] = _l('Core').' / '.$name;
            }
        }
    }
    if (version_compare(_PS_VERSION_, '1.7.0.0', '<'))
    {
        $sql = 'SELECT t.*
                FROM '._DB_PREFIX_.'shop s
                    INNER JOIN '._DB_PREFIX_.'theme t ON (s.id_theme=t.id_theme)
                GROUP BY t.id_theme
                ORDER BY t.name DESC';
        $themes = Db::getInstance()->ExecuteS($sql);
        foreach ($themes as $theme)
        {
            foreach ($langs as $iso => $name)
            {
                $path_file = $path.'themes/'.$theme['directory'].'/mails/'.$iso.'/';
                if (file_exists($path_file.'account.html'))
                {
                    $content = file_get_contents($path_file.'account.html');
                    if (strpos($content, '{passwd}') !== false)
                    {
                        $found[] = _l('Theme').' '.$theme['name'].' / '.$name;
                    }
                }
            }
        }
    }

    if (!empty($found))
    {
        $results = 'KO';
        ob_start(); ?>
        <div style="padding: 20px;height: 100%;overflow: auto;">
            <img src="./lib/img/security.png" alt="" style="float: left; margin-right: 20px; margin-bottom: 20px;" />
            <?php
            echo '<p><strong>'._l('These email templates should not include customer passwords for security reason.').'</strong></p>';
        foreach ($found as $file)
        {
            echo '<p>- '.$file.'</p>';
        } ?>
            <div style="clear: both"></div>
            <div style="margin-top: 30px; text-align: center;">
                <a href="<?php echo getScExternalLink('action_fixmyprestashop_customer_password'); ?>" target="_blank" class="btn_red"><?php echo _l('Apply instructions in the following article to fix this.'); ?></a>
            </div>
        </div>
        <?php
        $content = ob_get_clean();
    }

    echo json_encode(array(
        'results' => $results,
        'contentType' => 'content',
        'content' => $content,
        'title' => _l('{passwd} found'),
    ));
}
