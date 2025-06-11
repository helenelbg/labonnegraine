<?php
class CmsController extends CmsControllerCore
{
	public function postProcess()
	{
		if (Tools::isSubmit('submitNewsletters'))
		{
			// submitNewsletters est le "name" du bouton submit dans /themes/lbg/template/cms/page-85.tpl
			$customer = $this->context->customer;
			$email = $customer->email;
			$newsletter = $customer->newsletter;
			$optin = $customer->optin;
			$customer->newsletter = 0;
			$customer->optin = 0;
			if(Tools::getValue('newsletter_bonplan') == "on"){
				$customer->newsletter = 1;
			}
			if(Tools::getValue('newsletter_dossiercyril') == "on"){
				$customer->optin = 1;
			} 			
			$customer->save();
			
			// Mise à jour Mailjet si changement
			
			if($optin != $customer->optin){
				$action = 'addnoforce';
				if(!$customer->optin){
					$action = 'unsub';
				}
				$id_newsletter_cyril = 10287721;
				Tools::aw_subscribe($email, $id_newsletter_cyril, $action);
			}
			
			if($newsletter != $customer->newsletter){
				$action = 'addnoforce';
				if(!$customer->newsletter){
					$action = 'unsub';
				}
				$id_newsletter_bonsplans = 2074282;
				Tools::aw_subscribe($email, $id_newsletter_bonsplans,  $action);
			}
		}
	}
}

