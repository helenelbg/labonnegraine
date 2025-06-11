<?php

class Mail extends MailCore
{
    public static function send(
        $idLang,
        $template,
        $subject,
        $templateVars,
        $to,
        $toName = null,
        $from = null,
        $fromName = null,
        $fileAttachment = null, 
        $mode_smtp = null,
        $templatePath = _PS_MAIL_DIR_,
        $die = false,
        $idShop = null,
        $bcc = null,
        $replyTo = null,
        $replyToName = null
    )
    {
		//$to = 'dorian@berry-web.com';
        //$to = 'guillaume@anjouweb.com';

        $mailC = new Mail();
        $value_template = $template;
        $linked_template = $mailC->getLinkedEmailsTemplates($value_template, $idLang);

        $get_all_template_vars[] = $templateVars;

		// Ajout des CGV et formulaire rétractation au mail confirmation de commande
		if ($template == 'order_conf'){
			$fileAttachment = array();
			$content = file_get_contents(_PS_ROOT_DIR_.'/pdf/CGV 2023-03.pdf');
			$fileAttachment['content'] = $content;
			$fileAttachment['name'] ='CGV';
			$fileAttachment['mime'] = 'application/pdf';
		}  
		
        //if(!empty($linked_template)) // commenté car cela bloque les mails mots de passe oublié
        if(false)
        {
            $aw_mailjet_login = Configuration::get('AW_MAILSMAILJET_LOGIN');
            $aw_mailjet_key =  Configuration::get('AW_MAILSMAILJET_KEY');
    
            $mailjet_value = array();
            $mailjet_value['aw_mailjet_login'] = $aw_mailjet_login;
            $mailjet_value['aw_mailjet_key'] = $aw_mailjet_key;
            $mailjet_value['name_function'] = 'send_new_mail';
            $mailjet_value['template_id'] = $linked_template[0]['aw_mails_mailjet_template_id'];
            $mailjet_value['template_vars'] = $get_all_template_vars;
            $mailjet_value['subject'] = $subject;
            $mailjet_value['fromName'] = $fromName;
            $mailjet_value['to'] = $to;
            $mailjet_value['replyTo'] = $replyTo;
            $mailjet_value['fileAttachment'] = $fileAttachment;
    
            $mailC->send_new_mail($mailjet_value);
            return true;
        }
        else
        {    
            return parent::send(
                $idLang,
                $template,
                $subject,
                $templateVars,
                $to,
                $toName,
                $from,
                $fromName,
                $fileAttachment,
                $mode_smtp,
                $templatePath,
                $die,
                $idShop,
                $bcc,
                $replyTo,
                $replyToName
            );
        }
    }

    public function send_new_mail($post_data = [])
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, "http://".Configuration::get('PS_SHOP_DOMAIN_SSL')."/modules/aw_mailsmailjet/curl/aw_mailjet.php");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post_data));
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);

        $result = curl_exec($ch);

        curl_close($ch);
    }

    public function getLinkedEmailsTemplates($value_template, $idLang)
    {
        $sql = 'SELECT * FROM aw_mails_mailjet WHERE aw_mails_mailjet_prestashop_template = "'.$value_template.'" AND aw_mails_mailjet_lang_id = "'.$idLang.'"';
        $linked_mails_template = Db::getInstance($sql)->executeS($sql);

        return $linked_mails_template;
    }
}
?>