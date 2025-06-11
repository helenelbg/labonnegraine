<?php

if(!defined('_PS_VERSION_'))
{
    exit;
}

class Aw_Mailsmailjet extends Module
{
    public function __construct()
    {
        $this->name = 'aw_mailsmailjet';
        $this->tab = 'front_office_features';
        $this->version = '1.0.0';
        $this->author = 'AnjouWeb';
        $this->need_instance = 0;
        $this->ps_versions_compliancy = [
            'min' => '1.7',
            'max' => _PS_VERSION_
        ];
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('Module mailjet');
        $this->description = $this->l('Gestions des envois d\'emails avec mailjet');

        $this->confirmUninstall = $this->l('Êtes-vous sûr de vouloir désinstaller ce module ?');
    }

    public function install()
    {
        if(Shop::isFeatureActive())
        {
            Shop::setContext(Shop::CONTEXT_ALL);
        }

        if(!parent::install() ||
            !$this->registerHook('header') ||
            !$this->registerHook('displayBackOfficeHeader') ||
            !Configuration::updateValue('AW_MAILSMAILJET_LOGIN', '0') ||
            !Configuration::updateValue('AW_MAILSMAILJET_KEY', '0')
        )
        {
            return false;
        }

        Db::getInstance()->execute('CREATE TABLE aw_mails_mailjet
        (
            aw_mails_mailjet_id INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
            aw_mails_mailjet_template_id INT(11),
            aw_mails_mailjet_template_name VARCHAR(250),
            aw_mails_mailjet_prestashop_template VARCHAR(150),
            aw_mails_mailjet_lang_id INT(11)
        )');

        return true;
    }

    public function uninstall()
    {
        if(!parent::uninstall() ||
            !Configuration::deleteByName('AW_MAILSMAILJET_LOGIN') &&
            !Configuration::deleteByName('AW_MAILSMAILJET_KEY')
        )
        {
            return false;
        }

        Db::getInstance()->execute('DROP TABLE aw_mails_mailjet');

        return true;
    }

    public function getContent()
    {
        $output = null;

        // Ajouter les clés mailjet
        if (Tools::isSubmit('submitLogin'))
        {
            $mailjet_login = strval(Tools::getValue('AW_MAILSMAILJET_LOGIN'));
            $mailjet_key = strval(Tools::getValue('AW_MAILSMAILJET_KEY'));

            if(
                !$mailjet_login ||
                empty($mailjet_login) &&
                !$mailjet_key ||
                empty($mailjet_key)
            )
            {
                $output .= $this->displayError($this->l('Invalid Configuration value'));
            }
            else
            {
                Configuration::updateValue('AW_MAILSMAILJET_LOGIN', $mailjet_login);
                Configuration::updateValue('AW_MAILSMAILJET_KEY', $mailjet_key);
                $output .= $this->displayConfirmation($this->l('Settings updated'));
            }
        }

        // Lier une template mailjet et prestashop
        if (Tools::isSubmit('submitLinkTemplate'))
        {
            $mailjet_template_id = strval(Tools::getValue('AW_MAILSMAILJET_TEMPLATE_MAILJET_ID'));
            $prestashop_template = strval(Tools::getValue('AW_MAILSMAILJET_TEMPLATE_PRESTASHOP_ID'));
            $lang_id = strval(Tools::getValue('AW_MAILSMAILJET_LANG_ID'));

            $mailjet_template_id =  explode(":", $mailjet_template_id);

            if(
                !$mailjet_template_id[0] ||
                empty($mailjet_template_id[0]) &&
                !$mailjet_template_id[1] ||
                empty($mailjet_template_id[1]) &&
                !$prestashop_template ||
                empty($prestashop_template) &&
                !$lang_id ||
                empty($lang_id)
            )
            {
                $output .= $this->displayError($this->l('Invalid Configuration value'));
            }
            else
            {
                $sql = 'INSERT INTO aw_mails_mailjet (aw_mails_mailjet_template_id,
                                                        aw_mails_mailjet_template_name,
                                                        aw_mails_mailjet_prestashop_template,
                                                        aw_mails_mailjet_lang_id)
                                                        VALUES ("'.$mailjet_template_id[0].'",
                                                                "'.$mailjet_template_id[1].'",
                                                                "'.$prestashop_template.'",
                                                                "'.$lang_id.'")';

                Db::getInstance($sql)->execute($sql);
            }
        }

        // Supprimer le lien entre un template mailjet et prestashop
        if (Tools::isSubmit('submitDeleteLinkTemplate'))
        {
            foreach($_POST['AW_MAILSMAILJET_DELETE_LINKED_MAILS_ID'] as $value)
            {
                $sql = 'DELETE FROM aw_mails_mailjet WHERE aw_mails_mailjet_id = "'.$value.'"';
                Db::getInstance($sql)->executeS($sql);
            }
        }

        return $output.$this->displayForm();
    }

    public function displayForm()
    {
        $aw_mailjet_login = Configuration::get('AW_MAILSMAILJET_LOGIN');
        $aw_mailjet_key =  Configuration::get('AW_MAILSMAILJET_KEY');

        if (Language::getIdByIso('en')) {
            $default_language = 'en';
        } else {
            $default_language = Language::getIsoById((int) Configuration::get('PS_LANG_DEFAULT'));
        }

        if (!$default_language || !Validate::isLanguageIsoCode($default_language)) {
            return false;
        }

        $mailjet_template_list = '';
        $prestashop_tempalte_list = '';
        $lang_list = '';
        // Si les clés existent récupère les templates
        if(!empty($aw_mailjet_login) && !empty($aw_mailjet_key))
        {

            $post_data['name_function'] = 'get_mailjet_template';
            $post_data['aw_mailjet_login'] = $aw_mailjet_login;
            $post_data['aw_mailjet_key'] = $aw_mailjet_key;

            $mailjet_template_list = $this->getMailjetTemplate($post_data);

            $prestashop_tempalte_list = $this->getAllEmailsTemplates($default_language);

            $linked_mails_template_list = $this->getLinkedEmailsTemplates($default_language);

            $lang_list = $this->getLangList();
        }

        $this->context->smarty->assign([
            'AW_MAILSMAILJET_LOGIN' => Configuration::get("AW_MAILSMAILJET_LOGIN"),
            'AW_MAILSMAILJET_KEY' => Configuration::get("AW_MAILSMAILJET_KEY"),
            'AW_MAILSMAILJET_TEMPLATE_MAILJET_LIST' => $mailjet_template_list,
            'AW_MAILSMAILJET_TEMPLATE_PRESTASHOP_LIST' => $prestashop_tempalte_list,
            'AW_MAILSMAILJET_LINKED_MAILS_TEMPLATE_LIST' => $linked_mails_template_list,
            'AW_MAILSMAILJET_LANG_LIST' => $lang_list,
        ]);

        return $this->display(__FILE__, 'views/templates/admin/manage.tpl');
    }

    // Ajouter le js et jquery et css dans le back office
    public function hookDisplayBackOfficeHeader()
    {
       // $this->context->controller->addCss($this->_path.'views/css/aw_mailsmailjet.css');

       $this->context->controller->addJS($this->_path . 'views/js/aw_mailsmailjet_back_office.js');
      // $this->context->controller->addJS($this->_path . 'views/js/jquery361.min.js');

        //echo '<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.1/jquery.min.js"></script>';
    }

    /*
    public function sendNewMail($postData = [])
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, "http://".Configuration::get('PS_SHOP_DOMAIN_SSL')."/modules/aw_mailsmailjet/curl/aw_mailjet.php");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);

        $result = curl_exec($ch);

        curl_close($ch);

        return json_decode($result);
    }
    */

    // Récupère les tempaltes mailjet
    public function getMailjetTemplate($post_data = [])
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, "http://".Configuration::get('PS_SHOP_DOMAIN_SSL')."/modules/aw_mailsmailjet/curl/aw_mailjet.php");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);

        $result = curl_exec($ch);

        curl_close($ch);

        return json_decode($result, true);
    }

    // Récupère les templates prestashop
    public function getAllEmailsTemplates($default_language)
    {
        $mailjet_mails_list = array();
        if(Tools::file_exists_cache(_PS_MAIL_DIR_ . $default_language . '/'))
        {
            $mailjet_mails_list = scandir(_PS_MAIL_DIR_ . $default_language . '/', SCANDIR_SORT_NONE);
        }

        foreach($mailjet_mails_list as $mailjet_mail)
        {
            if(substr($mailjet_mail, -3) == 'txt')
            {
                $mailjet_list[] = $mailjet_mail;
            }
        }

        return $mailjet_list;
    }


    public function getLinkedEmailsTemplates($id_lang)
    {
        $sql = 'SELECT * FROM aw_mails_mailjet';
        $linked_mails_template_list = Db::getInstance($sql)->executeS($sql);

        return $linked_mails_template_list;
    }


    // Récupère la liste des langue prestashop
    public function getLangList()
    {
        $sql = 'SELECT * FROM '._DB_PREFIX_.'lang WHERE active = "1"';
        $lang_list = Db::getInstance($sql)->executeS($sql);

        return $lang_list;
    }
}
?>
