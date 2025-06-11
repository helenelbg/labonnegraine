<?php 
    class TaxRulesTaxManager extends TaxRulesTaxManagerCore
    {
        public function getTaxCalculator()
        {
            if ($this->address->id_country != Configuration::get('PS_COUNTRY_DEFAULT')) {
                $vatNumber = $this->address->vat_number;
    
                if (empty($vatNumber)) {
                    $id_customer = $this->address->id_customer;
                    
                    $context = Context::getContext();
                    
                    if (isset($context->cart)) {
                        $billingAddress = new Address((int)$context->cart->id_address_invoice);
                        
                        if  ($billingAddress->id_country != Configuration::get('PS_COUNTRY_DEFAULT')) {
                            $vatNumber = $billingAddress->vat_number;
                        }
                    }
                }
    
                if (!empty($vatNumber) && preg_match('/^[A-Z]{2}[A-Z0-9]{2,14}$/', $vatNumber)) 
                    return new TaxCalculator(array());
            }
            
            return parent::getTaxCalculator();
        }	
    }
?>