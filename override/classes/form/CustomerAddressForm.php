<?php 
use Symfony\Component\Translation\TranslatorInterface;
class CustomerAddressForm extends CustomerAddressFormCore
{
    public function validate()
    {
        $is_valid = true;

        $postcode = $this->getField('postcode');
        if ($postcode && $postcode->isRequired()) {
            $country = $this->formatter->getCountry();
            
            if (!$country->checkZipCode($postcode->getValue())) {
                $postcode->addError($this->translator->trans(
                    'Invalid postcode - should look like "%zipcode%"',
                    ['%zipcode%' => $country->zip_code_format],
                    'Shop.Forms.Errors'
               ));
                $is_valid = false;
            }
            if ($country->id == 8)
            {
                if (($postcode->getValue() >= 97000 || $postcode->getValue() < 1000) && $postcode->getValue() != 98000)
                {
                    $postcode->addError($this->translator->trans(
                        'Pas de livraison dans les DOM / TOM.',
                        ['%zipcode%' => $country->zip_code_format],
                        'Shop.Forms.Errors'
                ));
                    $is_valid = false;
                }
            }
        }

        if (($hookReturn = Hook::exec('actionValidateCustomerAddressForm', ['form' => $this])) !== '') {
            $is_valid &= (bool) $hookReturn;
        }

        return $is_valid && parent::validate();
    }
}
?>