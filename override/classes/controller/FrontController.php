<?php

class FrontController extends FrontControllerCore
{
    public function initContent()
    {
		if (_PS_CYRIL_) {
			$this->context->smarty->assign('aff_cyril', true);
		}
		else {
			$this->context->smarty->assign('aff_cyril', false);
		}
		
        $this->assignGeneralPurposeVariables();
        $this->process();

        if (!isset($this->context->cart)) {
            $this->context->cart = new Cart();
        }

        $this->context->smarty->assign([
            'HOOK_HEADER' => Hook::exec('displayHeader'),
        ]);
    }
	
	public function setMedia()
    {
        $this->registerJavascript('slick-js', '/assets/slick/slick.min.js', ['position' => 'bottom', 'priority' => 1000]);
		$this->registerStylesheet('slick-min-css', '/assets/css/slick.min.css', ['media' => 'all', 'priority' => 1000]);
        $this->registerStylesheet('slick-theme-css', 'assets/slick/slick-theme.css', ['media' => 'all', 'priority' => 1000]);
        $this->registerStylesheet('slick-css', '/assets/slick/slick.css', ['media' => 'all', 'priority' => 1000]);
        $this->registerStylesheet('fa-css', '/assets/css/font-awesome.css', ['media' => 'all', 'priority' => 1000]);
        $this->registerStylesheet('desktop-css', '/assets/css/version-desktop-23.css', ['media' => 'all', 'priority' => 1000]);
        $this->registerStylesheet('mobile-css', '/assets/css/version-mobile-23.css', ['media' => 'all', 'priority' => 1000]);
		$this->registerStylesheet('brody-css', '/assets/fonts/brody/css_brody.css', ['media' => 'all', 'priority' => 1000]);
        $this->registerStylesheet('dorian-css', '/assets/css/dorian-23.css', ['media' => 'all', 'priority' => 1000]);
      
		return parent::setMedia();
    }

    public function assignGeneralPurposeVariables()
    {
        parent::assignGeneralPurposeVariables();
        $cart_presenter= $this->cart_presenter->present($this->context->cart);
        $custom_payment = Module::getInstanceByName('ets_payment_with_fee');
        $custom_payment->assignGeneralPurposeVariables($cart_presenter);
    }

}
