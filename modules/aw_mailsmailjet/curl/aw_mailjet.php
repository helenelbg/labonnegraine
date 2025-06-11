<?php
	include dirname(__FILE__).'/mailjet_config/vendor/autoload.php';

	use \Mailjet\Resources;

	if(!empty($_POST['name_function']))
	{
		// Récupère les templates mailjet
		if($_POST['name_function'] == 'get_mailjet_template')
		{
			$aw_mailjet_login = $_POST['aw_mailjet_login'];
			$aw_mailjet_key =  $_POST['aw_mailjet_key'];

			$mj = new \Mailjet\Client($aw_mailjet_login,$aw_mailjet_key);
			$filters = [
				'Limit'=>1000,
			];
			
			
            $response = $mj->get(Resources::$Template, ['filters'=>$filters]);
            $response->success();
			
            $template_list = $response->getData();
            $data = array();
            foreach($template_list as $template)
            {
				//error_log(print_r($template, true));
				//error_log('-------------------------------');
                $data[] = ['template_id' => $template['ID'], 'template_name' => $template['Name']];
            }

			echo json_encode($data);
		}

		// Envoi un mail avec mailjet
		if($_POST['name_function'] == 'send_new_mail')
		{
			$aw_mailjet_login = $_POST['aw_mailjet_login'];
			$aw_mailjet_key =  $_POST['aw_mailjet_key'];
			$template_id = $_POST['template_id'];
			$template_vars = $_POST['template_vars'];

			if(isset($_POST['subject']) && !empty($_POST['subject']))
			{
				$subject = $_POST['subject'];
			}
			else
			{
				$subject = 'test';
			}

			$fromName  = $_POST['fromName'];
			$fileAttachment  = $_POST['fileAttachment'];

			foreach($template_vars[0] as $key => $get_template_var)
			{
				$key = str_replace('{', '', $key);
				$key = str_replace('}', '', $key);
				$new_template_vars[$key] = $get_template_var;
			}

			if(isset($_POST['to']) && !empty($_POST['to']))
			{
				$to = $_POST['to'];
			}
			else
			{
				$to = $new_template_vars['email'];
			}

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
					'Reply-To' => $_POST['replyTo']
				]
			];
			
			$response = $mj->post(Resources::$Email, ['body' => $body]);
			//error_log('$response : '.print_r($response, true));
			error_log('$response->success() : '.$response->success());
			if ( $response->success() == 1 )
			{
				return true;
			}
			return false;
		}
	}
	/*
	if(!empty($_POST['api_key']) && !empty($_POST['api_secret']))
	{
		$mj = new \Mailjet\Client($_POST['api_key'],$_POST['api_secret']);
		$response = $mj->get(Resources::$Template);
		$response->success();
		$template_list = $response->getData();
	
		$data = array();
		foreach($template_list as $template)
		{
			$data[] = ['template_id' => $template['ID'], 'template_name' => $template['Name']];
		}

		echo json_encode($data);
	}
	*/
?>