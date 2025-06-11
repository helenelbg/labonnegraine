<?php
include_once _PS_MODULE_DIR_.'hh_customcheckout/classes/CustomCheckoutStep.php';
class Hh_CustomCheckout extends Module
{

    public function __construct()
    {
        $this->name = 'hh_customcheckout';
        $this->tab = 'others';
        $this->version = '0.2.0';
        $this->author = 'hhennes';
        $this->bootstrap = true;
        parent::__construct();

        $this->displayName = $this->l('Sample checkout step module');
        $this->description = $this->l('Sample module for adding a custom step in checkout');
    }

    public function install()
    {
        return parent::install()
            && $this->registerHook('actionCheckoutRender');
    }

    /**
     * Ajout de notre nouvelle étape dans le tunnel de commande
     *
     * @param array $params
     * @return void
     */
    public function hookActionCheckoutRender(array $params)
    {
        //L'objet checkoutProcess est passé par référence, on peut donc le modifier directement.
        //Cf. controllers/front/OrderController.php:143
        /** @var CheckoutProcess $checkoutProcess */
        $checkoutProcess = $params['checkoutProcess'];
        //Ajout de notre étape spécifique
        $checkoutProcess->addStep(
            new CustomCheckoutStep(
                $this->context,
                $this->getTranslator(),
                $this
            )
        );
        //Récupération des étapes du panier
        $currentSteps = $checkoutProcess->getSteps();
        //Récupération de notre étape (qui est la dernière du tableau des étapes)
        $customStep = array_pop($currentSteps);
        //Position à laquelle on veut positionner notre étape
        $stepPosition = 3;
        //Découpe du tableau pour récupérer les éléments Avant et après l'étape
        $beforeSteps = array_slice($currentSteps,0,$stepPosition);
        $afterSteps = array_slice($currentSteps,$stepPosition);
        //Nouvel ordre des étapes
        $sortedSteps = array_merge($beforeSteps,[$customStep],$afterSteps);
        //On le définit dans le checkout
        $checkoutProcess->setSteps($sortedSteps);
    }

}