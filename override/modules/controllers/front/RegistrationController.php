<?php

class RegistrationController extends RegistrationControllerCore
{
	public function initContent()
    {	
        $register_form = $this
            ->makeCustomerForm()
            ->setGuestAllowed(false)
            ->fillWith(Tools::getAllValues());

        // If registration form was submitted

        if (Tools::isSubmit('submitCreate')) {

            $hookResult = array_reduce(
                Hook::exec('actionSubmitAccountBefore', [], null, true),
                function ($carry, $item) {
                    return $carry && $item;
                },
                true
            );

            // If no problem occured in the hook, let's get the user redirected
            if ($hookResult && $register_form->submit() && !$this->ajax) {

				return $this->redirectWithNotifications('my-account?create=1');

            }
        }

        $this->context->smarty->assign([
            'register_form' => $register_form->getProxy(),
            'hook_create_account_top' => Hook::exec('displayCustomerAccountFormTop'),
        ]);
        $this->setTemplate('customer/registration');

        FrontController::initContent();
    }
}
