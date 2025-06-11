<?php 
// Array\n(\n    [aw_mailjet_login] => 34b10e378c3e0fa97459c5c143f5ec58\n    [aw_mailjet_key] => 548160ec9d9e64da604c578c68636f08\n    [name_function] => send_new_mail\n    [template_id] => 5050135\n    [template_vars] => Array\n        (\n            [0] => Array\n                (\n                    [{firstname}] => AMARY\n                    [{lastname}] => Guillaume\n                    [{order_name}] => -\n                    [{attached_file}] => -\n                    [{message}] => ghd\n                    [{email}] => guillaume+1@anjouweb.com\n                    [{product_name}] => \n                )\n\n        )\n\n    [subject] => Message depuis le formulaire de contact [no_sync]\n    [to] => guillaume@anjouweb.com\n)\n


    include dirname(__FILE__).'/curl/mailjet_config/vendor/autoload.php';

	use \Mailjet\Resources;

		
			$aw_mailjet_login = '34b10e378c3e0fa97459c5c143f5ec58';
			$aw_mailjet_key =  '548160ec9d9e64da604c578c68636f08';
			$template_id = 5050135;
			//$template_vars = $_POST['template_vars'];

			if(isset($_POST['subject']) && !empty($_POST['subject']))
			{
				$subject = $_POST['subject'];
			}
			else
			{
				$subject = 'test';
			}

			$fromName  = 'La Bonne Graine';
			/*$fileAttachment  = $_POST['fileAttachment'];

			foreach($template_vars[0] as $key => $get_template_var)
			{
				$key = str_replace('{', '', $key);
				$key = str_replace('}', '', $key);
				$new_template_vars[$key] = $get_template_var;
			}*/

			$_POST['to'] = 'guillaume@anjouweb.com';

			$mj = new \Mailjet\Client($aw_mailjet_login,$aw_mailjet_key);

			/*$pieces = array();
			foreach ($fileAttachment as $attachment) {
				if (isset($attachment['content'], $attachment['name'], $attachment['mime'])) {

					$pieces[] = array('ContentType' => $attachment['mime'],
					'Filename' => $attachment['name'],
					'Base64Content' => base64_encode($attachment['content']));
				}
			}*/

			$body = [
				
				'FromEmail' => "info@labonnegraine.com",
			
				'FromName' => 'La Bonne Graine',
			
				'Subject' => $subject,
	
				/*'MJ-TemplateLanguage' => true,
			
				'Mj-TemplateID' => $template_id,*/
				'Text-part' => "Dear passenger 1, welcome to Mailjet! May the delivery force be with you!",
            'HTML-part' => "<h3>Dear passenger 1, welcome to <a href=\"https://www.mailjet.com/\">Mailjet</a>!</h3><br />May the delivery force be with you!",

				//'Attachments' => $pieces,
			
				'Recipients' => [
					['Email' => $to,
					//'Vars' => $new_template_vars
					]
				],
			];
			
			$response = $mj->post(Resources::$Email, ['body' => $body]);
            echo '<pre>';
			echo $response->success();
            echo '</pre>';
			// $response->success() && var_dump($response->getData());
		
	
    ?>