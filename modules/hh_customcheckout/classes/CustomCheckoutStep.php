<?php
class CustomCheckoutStep extends AbstractCheckoutStep
{

    /** @var Hh_CustomCheckout */
    protected $module;

    public function __construct(
        Context $context,
        $translator, //Omitting type to avoid interface issue and to be compatible with PS 1.7.8 and 8.1
        Hh_CustomCheckout $module
    )
    {
        parent::__construct($context, $translator);
        $this->module = $module;
        $this->setTitle('Choix de la date de livraison');
    }

    /**
     * Traitement de la requête ( ie = Variables Posts du checkout
     * @param array $requestParameters
     * @return $this
     */
    public function handleRequest(array $requestParameters = array())
    {
        //Si les informations sont postées assignation des valeurs
        if (isset($requestParameters['submitCustomStep'])) {
            //Passage à l'étape suivante
            $this->setComplete(true);

            //Code 1.7.6
            if (version_compare(_PS_VERSION_, '1.7.6') > 0) {
                $this->setNextStepAsCurrent();
            } else {
                $this->setCurrent(false);
            }
        }

        return $this;
    }

    /**
     * Affichage de la step
     *
     * @param array $extraParams
     * @return string
     */
    public function render(array $extraParams = [])
    {

        //Assignation des informations d'affichage
        $defaultParams = array(
            //Informations nécessaires
            'identifier' => 'dilevery_choice',
            'position' => 4, //La position n'est qu'indicative ...
            'title' => $this->getTitle(),
            'step_is_complete' => (int)$this->isComplete(),
            'step_is_reachable' => (int)$this->isReachable(),
            'step_is_current' => (int)$this->isCurrent(),
            //Variables custom
        );

        $this->context->smarty->assign($defaultParams);
        return $this->module->display(
            _PS_MODULE_DIR_ . $this->module->name,
            'views/templates/front/customCheckoutStep.tpl'
        );
    }

}