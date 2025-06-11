<?php
die;
	include dirname(__FILE__).'/mailjet_config/vendor/autoload.php';

	use \Mailjet\Resources;

		// Envoi un mail avec mailjet
       
        $vars = [
            '{code}' => 'LBGBAHCO20_1111',
            '{firstname}' => 'Guillaume',
            '{montant}' => '20,00€',
            '{date_fin}' => '31/12/2024',
        ];
        $get_all_template_vars[] = $vars;

		// Ajout des CGV et formulaire rétractation au mail confirmation de commande
		$fileAttachment = array();
		
            $aw_mailjet_login = '34b10e378c3e0fa97459c5c143f5ec58';
            $aw_mailjet_key =  '548160ec9d9e64da604c578c68636f08';
    
            $mailjet_value = array();
            $mailjet_value['to'] = 'guillaume.amary.lbg@gmail.com';
            $mailjet_value['replyTo'] = 'guillaume.amary.lbg@gmail.com';
            $mailjet_value['fileAttachment'] = $fileAttachment;

			$template_id = 6330670;
			$template_vars = $get_all_template_vars;

			$subject = 'Votre bon d\'achat est disponible, ne manquez pas cette opportunité !';
			

			$fromName  = 'TEST';
			$fileAttachment  = $fileAttachment;

			foreach($template_vars[0] as $key => $get_template_var)
			{
				$key = str_replace('{', '', $key);
				$key = str_replace('}', '', $key);
				$new_template_vars[$key] = $get_template_var;
			}

			
			$to = 'guillaume.amary.lbg@gmail.com';
			

			$mj = new \Mailjet\Client($aw_mailjet_login,$aw_mailjet_key);

			$pieces = array();
			
			if ( isset($fileAttachment['mime']) )
			{
				if (isset($fileAttachment['content'], $fileAttachment['name'], $fileAttachment['mime'])) {
					$pieces[] = array('Content-type' => $fileAttachment['mime'],
					'Filename' => $fileAttachment['name'],
					'content' => base64_encode($fileAttachment['content']));
				}
			}
			else 
			{
				foreach ($fileAttachment as $attachment) {
					if (isset($attachment['content'], $attachment['name'], $attachment['mime'])) {
						$pieces[] = array('Content-type' => $attachment['mime'],
						'Filename' => $attachment['name'],
						'content' => ($attachment['content']));
					}
				}
			}

			$body = [
				
				'FromEmail' => "info@labonnegraine.com",
			
				'FromName' => 'La Bonne Graine',
			
				'Subject' => $subject,
	
				'MJ-TemplateLanguage' => true,
			
				'Mj-TemplateID' => $template_id,

				'Attachments' => $pieces,
			
				'Recipients' => [
					['Email' => $to,
					'Vars' => $new_template_vars
					]
				],
				'Headers' => [
					'Reply-To' => "info@labonnegraine.com"
				]
			];
			
			$response = $mj->post(Resources::$Email, ['body' => $body]);
            
			if ( $response->success() == 1 )
			{
				echo 'OK';
			}
		echo 'Fin';
	
?>