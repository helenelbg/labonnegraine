<?php

if (!defined('_PS_VERSION_')) {
    exit;
}

use PrestaShop\PrestaShop\Core\Module\WidgetInterface;

class Ps_EmailsubscriptionOverride extends Ps_Emailsubscription implements WidgetInterface
{
	public function newsletterRegistration($hookName = null)
    {
        $isPrestaShopVersionOver177 = version_compare(_PS_VERSION_, '1.7.7', '>=');

        if ($isPrestaShopVersionOver177 && ($hookName !== null)) {
            if (empty($_POST['blockHookName']) || $_POST['blockHookName'] !== $hookName) {
                return false;
            }
        }

        // hook for newsletter registration/unregistration : fill-in hookError string is there is an error
        $hookError = null;
        Hook::exec(
            'actionNewsletterRegistrationBefore',
            [
                'hookName' => $hookName,
                'email' => $_POST['email'],
                'action' => $_POST['action'],
                'hookError' => &$hookError,
            ]
        );
        /** @var string|null $hookError */
        if ($hookError !== null) {
            return $this->error = $hookError;
        }

        if (empty($_POST['email']) || !Validate::isEmail($_POST['email'])) {
            return $this->error = $this->trans('Invalid email address.', [], 'Shop.Notifications.Error');
        } elseif ($_POST['action'] == '1') {
            $register_status = $this->isNewsletterRegistered($_POST['email']);

            if ($register_status < 1) {
                return $this->error = $this->trans('This email address is not registered.', [], 'Modules.Emailsubscription.Shop');
            }

            if (!$this->unregister($_POST['email'], $register_status)) {
                return $this->error = $this->trans('An error occurred while attempting to unsubscribe.', [], 'Modules.Emailsubscription.Shop');
            }

            return $this->valid = $this->trans('Unsubscription successful.', [], 'Modules.Emailsubscription.Shop');
        } elseif ($_POST['action'] == '0') {
            $register_status = $this->isNewsletterRegistered($_POST['email']);
            if ($register_status > 0) {
                return $this->error = $this->trans('This email address is already registered.', [], 'Modules.Emailsubscription.Shop');
            }

            $email = pSQL($_POST['email']);
            if (!$this->isRegistered($register_status)) {
                if (Configuration::get('NW_VERIFICATION_EMAIL')) {
                    // create an unactive entry in the newsletter database
                    if ($register_status == self::GUEST_NOT_REGISTERED) {
                        $this->registerGuest($email, false);
                    }

                    if (!$token = $this->getToken($email, $register_status)) {
                        return $this->error = $this->trans('An error occurred during the subscription process.', [], 'Modules.Emailsubscription.Shop');
                    }

                    $this->sendVerificationEmail($email, $token);

                    return $this->valid = $this->trans('A verification email has been sent. Please check your inbox.', [], 'Modules.Emailsubscription.Shop');
                } else {
                    if ($this->register($email, $register_status)) {
                        $this->valid = $this->trans('You have successfully subscribed to this newsletter.', [], 'Modules.Emailsubscription.Shop');
                    } else {
                        return $this->error = $this->trans('An error occurred during the subscription process.', [], 'Modules.Emailsubscription.Shop');
                    }

                    if ($code = Configuration::get('NW_VOUCHER_CODE')) {
                        $this->sendVoucher($email, $code);
                    }

                    if (Configuration::get('NW_CONFIRMATION_EMAIL')) {
                        $this->sendConfirmationEmail($email);
                    }
                }
            }
        }
        // hook
        Hook::exec(
            'actionNewsletterRegistrationAfter',
            [
                'hookName' => $hookName,
                'email' => $_POST['email'],
                'action' => $_POST['action'],
                'error' => &$this->error,
            ]
        );
		
		// Début - newsletter Mailjet, Dorian BERRY-WEB, mai 2023

		/*if(isset($_POST['email'])){
			
			$email = $_POST['email'];
			
			$body = [
			  'Action' => "addnoforce",
			  'Email' => $email 
			];

			// $list_ID = 10287721; // newsletter Cyril
			$list_ID = 2074282; // newsletter bonsplans

			$user = '34b10e378c3e0fa97459c5c143f5ec58';	
						
			$pass = '548160ec9d9e64da604c578c68636f08';

			$url = 'https://api.mailjet.com/v3/REST/contactslist/' .$list_ID. '/managecontact';

			$auth_key = $user.":".$pass;
			$encoded_auth_key = base64_encode($auth_key);
			$headers = array();
			$headers[] = 'Authorization: Basic '.$encoded_auth_key;

			$ch = curl_init();
			$opt = array(
				CURLOPT_POSTFIELDS => $body,
				CURLOPT_HTTPHEADER => $headers,
				CURLOPT_URL => $url,
				CURLOPT_FRESH_CONNECT => 1,
				CURLOPT_RETURNTRANSFER => 1,
				CURLOPT_FORBID_REUSE => 1,
				CURLOPT_TIMEOUT => 4,
				CURLOPT_SSL_VERIFYHOST => 0,
				CURLOPT_SSL_VERIFYPEER => 0
			);
			curl_setopt_array($ch, $opt);
				
			$result=curl_exec($ch);

			if ($result == false) {
				//echo  "error :".curl_error($ch);
			}
			curl_close($ch);
			//echo $result;
			 
		}*/
		
		if(isset($_POST['email'])){
			
			$email = $_POST['email'];
			
			$list_ID = Tools::get_id_newsletter_bonsplans();
			Tools::aw_subscribe($email, $list_ID);
			
			$list_ID = Tools::get_id_newsletter_cyril();
			Tools::aw_subscribe($email, $list_ID);
			
		}
		
		// Fin - newsletter Mailjet, Dorian BERRY-WEB, mai 2023
	
        return true;
    }
}
