<?php
/**
 * NOTICE OF LICENSE
 *
 * This file is licenced under the Software License Agreement.
 * With the purchase or the installation of the software in your application
 * you accept the licence agreement.
 *
 * You must not modify, adapt or create derivative works of this source code.
 *
 * @author    Active Design <office@activedesign.ro>
 * @copyright 2016-2018 Active Design
 * @license   LICENSE.txt
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

class AffConf
{
    public function getConfigForm()
    {
        if (!class_exists('Psaffiliate')) {
            $moduleObj = Module::getInstanceByName('psaffiliate');
        } else {
            $moduleObj = new Psaffiliate;
        }
        $id_lang = (int)Configuration::get('PS_LANG_DEFAULT');
        $id_currency = Configuration::get('PS_CURRENCY_DEFAULT');
        $currency = new Currency($id_currency);
        $orderStates = array_merge(
            array(array('id_order_state' => '0', 'name' => '-')),
            OrderState::getOrderStates($id_lang)
        );
        $customerGroups = array_merge(
            array(array('id_group' => '0', 'name' => $this->l('None'))),
            Group::getGroups($id_lang)
        );

        return array(
            'form' => array(
                'legend' => array(
                    'title' => $this->l('Settings'),
                    'icon' => 'icon-cogs',
                ),
                'input' => array(
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Commissions for life'),
                        'desc' => $this->l('If this is ON, any customer brought to your website by
                        an affiliate will generate commissions for life on all his sales. The lifetime affiliate is
                        associated only when the customer makes an order (it can also be associated when the customer
                        registers, check the next switch.'),
                        'name' => 'commissions_for_life',
                        'required' => false,
                        'class' => 't',
                        'is_bool' => true,
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Enabled'),
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->l('Disabled'),
                            ),
                        ),
                    ),
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Associate lifetime affiliations when a customer registers'),
                        'desc' => $this->l('This will make lifetime affiliations possible not only when a
                         customer makes an order, but when a customer which comes from an affiliate link registers
                         on your website too.'),
                        'name' => 'commissions_for_life_at_registration',
                        'required' => false,
                        'class' => 't',
                        'is_bool' => true,
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Enabled'),
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->l('Disabled'),
                            ),
                        ),
                    ),
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Allow overriding commissions for life'),
                        'desc' => $this->l('If a customer is associated with affiliate #1 but he
                         comes on your website from affiliate\'s #2 link, then: YES - #2 will get the commission
                         / NO - #1 will get the commission'),
                        'name' => 'override_commissions_for_life',
                        'required' => false,
                        'class' => 't',
                        'is_bool' => true,
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Enabled'),
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->l('Disabled'),
                            ),
                        ),
                    ),
                    array(
                        'col' => 5,
                        'type' => 'text',
                        'desc' => $this->l('You can increase or decrease the commissions generated by lifetime
                        affiliates based on this multiplier. For example, a multiplier of 0.5, will give the
                        affiliate only 50% of it\'s commission. (this applies only for lifetime affiliations)'),
                        'name' => 'commission_for_life_multiplier',
                        'label' => $this->l('Commissions for life multiplier'),
                        'validate' => 'isFloat',
                    ),
                    array(
                        'col' => 5,
                        'type' => 'text',
                        'prefix' => '<i class="icon icon-calendar"></i>',
                        'suffix' => $this->l('DAYS'),
                        'desc' => $this->l('For how many days do we remember the affiliate of the customer?'),
                        'name' => 'days_remember_affiliate',
                        'label' => $this->l('Days to remember affiliate'),
                        'validate' => 'isFloat',
                    ),
                    array(
                        'col' => 5,
                        'type' => 'text',
                        'prefix' => '<i class="icon icon-calendar"></i>',
                        'suffix' => $this->l('DAYS'),
                        'desc' => $this->l('For how many days do we display the current summary?'),
                        'name' => 'days_current_summary',
                        'label' => $this->l('Days for current summary'),
                        'validate' => 'isInt',
                    ),
                    array(
                        'col' => 5,
                        'type' => 'text',
                        'prefix' => '<i class="icon icon-money"></i>',
                        'suffix' => $currency->iso_code,
                        'desc' => $this->l('What is the minimum amount for  a user to request payment?'),
                        'name' => 'minimum_payment_amount',
                        'label' => $this->l('Minimum payment amount'),
                        'validate' => 'isInt',
                    ),
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Enable uploadable invoices'),
                        'desc' => $this->l('Allow affiliates to upload invoices when requesting payments.'),
                        'name' => 'enable_invoices',
                        'required' => false,
                        'class' => 't',
                        'is_bool' => true,
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Enabled'),
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->l('Disabled'),
                            ),
                        ),
                    ),
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Uploading an invoice is mandatory'),
                        'name' => 'mandatory_invoices',
                        'required' => false,
                        'class' => 't',
                        'is_bool' => true,
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Enabled'),
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->l('Disabled'),
                            ),
                        ),
                    ),
                    array(
                        'type' => 'textarea',
                        'label' => $this->l('Invoicing details'),
                        'desc' => $this->l('
                            Specify any invoicing details your affiliates should be aware of
                             when uploading invoices such as company name, address, VAT number etc.
                        '),
                        'name' => 'invoicing_details',
                        'required' => false,
                        'col' => 5,
                        'lang' => true,
                    ),
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Enable voucher payments'),
                        'desc' => $this->l('Allow affiliates to request voucher payments.'),
                        'name' => 'enable_voucher_payments',
                        'required' => false,
                        'class' => 't',
                        'is_bool' => true,
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Enabled'),
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->l('Disabled'),
                            ),
                        ),
                    ),
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Vouchers for affiliates only'),
                        'desc' => $this->l('Only the affiliate that requested the voucher can use it.'),
                        'name' => 'vouchers_for_affiliates_only',
                        'required' => false,
                        'class' => 't',
                        'is_bool' => true,
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Enabled'),
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->l('Disabled'),
                            ),
                        ),
                    ),
                    array(
                        'col' => 4,
                        'type' => 'switch',
                        'label' => $this->l('Vouchers partial use'),
                        'desc' => $this->l('
                            Only applicable if the voucher value is greater than the cart total.
                             If you do not allow partial use, the voucher value will be lowered to the total
                             order amount. If you allow partial use, however,
                             a new voucher will be created with the remainder.
                        '),
                        'name' => 'vouchers_partial_use',
                        'required' => false,
                        'class' => 't',
                        'is_bool' => true,
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Enabled'),
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->l('Disabled'),
                            ),
                        ),
                    ),
                    array(
                        'col' => 4,
                        'type' => 'switch',
                        'label' => $this->l('Always approve vouchers'),
                        'desc' => $this->l('Automatically approve vocuhers payments when requested.'),
                        'name' => 'vouchers_always_approved',
                        'required' => false,
                        'class' => 't',
                        'is_bool' => true,
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Enabled'),
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->l('Disabled'),
                            ),
                        ),
                    ),
                    array(
                        'col' => 4,
                        'type' => 'text',
                        'desc' => $this->l('
                            Upon voucher payment request, convert requested amount into
                             voucher amount using the specified exchange rate. For example, if the vouchers exchange
                             rate is 1.25 and an affiliate requests $100 as voucher payment,
                             the voucher value will be 100 × 1.25 ($125).
                        '),
                        'name' => 'vouchers_exchange_rate',
                        'label' => $this->l('Vouchers exchange rate'),
                        'validate' => 'isUnsignedFloat',
                    ),
                    array(
                        'col' => 4,
                        'type' => 'text',
                        'desc' => $this->l('
                            An affiliate can receive more commission if the customer brought to your website by
                            him makes his first order. Example: If you put here "1.2", the affiliate will receive 20%
                            more commission.
                        '),
                        'name' => 'first_order_multiplier',
                        'label' => $this->l('Multiplier for first order'),
                        'validate' => 'isUnsignedFloat',
                    ),
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Include shipping taxes when calculating commissions'),
                        'name' => 'include_shipping_tax',
                        'required' => false,
                        'class' => 't',
                        'is_bool' => true,
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Enabled'),
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->l('Disabled'),
                            ),
                        ),
                    ),
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Include tax rules when calculating commissions'),
                        'name' => 'include_tax_rules',
                        'required' => false,
                        'class' => 't',
                        'is_bool' => true,
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Enabled'),
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->l('Disabled'),
                            ),
                        ),
                    ),
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Substract cart rules value when calculating commissions'),
                        'name' => 'include_cart_rules',
                        'required' => false,
                        'class' => 't',
                        'is_bool' => true,
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Enabled'),
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->l('Disabled'),
                            ),
                        ),
                    ),
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Categories and products commissions are used as bonus'),
                        'name' => 'cat_prod_commission_bonus',
                        'hint' => $this->l('If this is ON, we add general commission rates and categories (or products) rates to total commission. If this is OFF, then we add only one of the following: product, category, general commission rates. (same order)'),
                        'required' => false,
                        'class' => 't',
                        'is_bool' => true,
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Enabled'),
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->l('Disabled'),
                            ),
                        ),
                    ),
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Commission per sale (value) is applied for each product'),
                        'name' => 'general_rate_value_per_product',
                        'required' => false,
                        'class' => 't',
                        'is_bool' => true,
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Enabled'),
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->l('Disabled'),
                            ),
                        ),
                    ),
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Product commission multiplier multiplies with Category commission multiplier'),
                        'name' => 'multiply_with_category',
                        'desc' => $this->l('Scenario: Product #1 multiplier 1.50, category of the product
                        multiplier 2.00 => YES - Total multiplier: 3.00 / NO - Total multiplier: 1.50'),
                        'required' => false,
                        'class' => 't',
                        'is_bool' => true,
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Enabled'),
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->l('Disabled'),
                            ),
                        ),
                    ),
                    array(
                        'col' => 5,
                        'type' => 'select',
                        'desc' => $this->l('On what order states does the system approve the commission for affiliates?'),
                        'name' => 'order_states_approve[]',
                        'label' => $this->l('Order states to approve commissions'),
                        'multiple' => true,
                        'options' => array(
                            'query' => $orderStates,
                            'id' => 'id_order_state',
                            'name' => 'name',
                        ),
                        'size' => sizeof($orderStates),
                    ),
                    array(
                        'col' => 5,
                        'type' => 'select',
                        'desc' => $this->l('On what order states does the system cancel the commission for affiliates?'),
                        'name' => 'order_states_cancel[]',
                        'label' => $this->l('Order states to cancel commissions'),
                        'multiple' => true,
                        'options' => array(
                            'query' => $orderStates,
                            'id' => 'id_order_state',
                            'name' => 'name',
                        ),
                        'size' => sizeof($orderStates),
                    ),
                    array(
                        'col' => '4',
                        'type' => 'switch',
                        'label' => $this->l('Override previous affiliate'),
                        'desc' => array(
                            $this->l('Let\'s say affiliate A brings you a customer. If that customer then enters via affiliate B\'s link and the customer makes an order, one of the following happens:'),
                            $this->l('1) if override enabled, affiliate B will get the commission'),
                            $this->l('2) if override disabled, affiliate A will get the commission'),
                        ),
                        'name' => 'override_previous_affiliate',
                        'required' => false,
                        'class' => 't',
                        'is_bool' => true,
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Enabled'),
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->l('Disabled'),
                            ),
                        ),
                    ),
                    array(
                        'type' => 'switch',
                        'label' => $this->l('New affiliates require manual approval'),
                        'name' => 'affiliates_require_approval',
                        'required' => false,
                        'class' => 't',
                        'is_bool' => true,
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Enabled'),
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->l('Disabled'),
                            ),
                        ),
                        'desc' => $this->l('Do you want new affiliates to require manual approval? If you want to automatically approve all new affiliates request, leave it disabled.'),
                    ),
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Make new customers affiliates directly'),
                        'name' => 'new_customers_affiliates_directly',
                        'required' => false,
                        'class' => 't',
                        'is_bool' => true,
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Enabled'),
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->l('Disabled'),
                            ),
                        ),
                        'desc' => $this->l('If this is enabled, new users are automatically subscribed as affiliates'),
                    ),
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Ask for affiliate\'s website'),
                        'name' => 'ask_for_website',
                        'required' => false,
                        'class' => 't',
                        'is_bool' => true,
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Enabled'),
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->l('Disabled'),
                            ),
                        ),
                        'desc' => $this->l('Do you want to ask for affiliate\'s website at registration?'),
                    ),
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Enable Textarea at registration'),
                        'name' => 'textarea_at_registration',
                        'required' => false,
                        'class' => 't',
                        'is_bool' => true,
                        'hint' => $this->l('This feature will be removed soon - you can use the Custom Fields instead'),
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Enabled'),
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->l('Disabled'),
                            ),
                        ),
                        'desc' => $this->l('Ask new affiliates to include some details in a text field?'),
                    ),
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Textarea at registration required'),
                        'name' => 'textarea_at_registration_required',
                        'required' => false,
                        'class' => 't',
                        'is_bool' => true,
                        'hint' => $this->l('This feature will be removed soon - you can use the Custom Fields instead'),
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Enabled'),
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->l('Disabled'),
                            ),
                        ),
                        'desc' => $this->l('Is the textarea mandatory?'),
                    ),
                    array(
                        'col' => 5,
                        'type' => 'text',
                        'desc' => $this->l('The label of the textarea (only if enabled).'),
                        'name' => 'textarea_at_registration_label',
                        'hint' => $this->l('This feature will be removed soon - you can use the Custom Fields instead'),
                        'label' => $this->l('Textarea at registration label'),
                        'lang' => true,
                    ),
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Enable terms and conditions'),
                        'name' => 'enable_terms_at_signup',
                        'required' => false,
                        'class' => 't',
                        'is_bool' => true,
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Enabled'),
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->l('Disabled'),
                            ),
                        ),
                        'desc' => $this->l('Enable terms and conditions checkbox for affiliate signup form.'),
                    ),
                    array(
                        'type' => 'select',
                        'label' => $this->l('Terms and conditions CMS page'),
                        'name' => 'terms_cms_id',
                        'options' => array(
                            'query' => CMS::getCMSPages((int)Context::getContext()->language->id),
                            'id' => 'id_cms',
                            'name' => 'meta_title',
                        ),
                        'validate' => 'isUnsignedInt',
                    ),
                    array(
                        'type' => 'radio',
                        'label' => $this->l('Affiliate link type'),
                        'name' => 'affiliate_link_type',
                        'required' => false,
                        'class' => 't',
                        'is_bool' => false,
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => 0,
                                'label' => $this->l('Use affiliate ID'),
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => 1,
                                'label' => $this->l('Use affiliate ID, prefixed by last 2 digits of registration year'),
                            ),
                        ),
                        'desc' => $this->l('This applies in affiliate links.'),
                    ),
                    array(
                        'col' => 5,
                        'type' => 'text',
                        'desc' => $this->l('Default is "aff". You can change it how you want.'),
                        'name' => 'affiliate_id_parameter',
                        'label' => $this->l('Affiliate ID parameter in link'),
                    ),
                    array(
                        'col' => 5,
                        'type' => 'text',
                        'desc' => $this->l('Default is "y". You can put any letter you want.'),
                        'name' => 'affiliate_year_prefix_parameter',
                        'label' => $this->l('Affiliate year prefix letter'),
                    ),
                    array(
                        'col' => 5,
                        'type' => 'select',
                        'desc' => $this->l('For which customer groups shall the "Become affiliate" button be shown? Leaving this empty means every group will see the button.'),
                        'name' => 'groups_allowed[]',
                        'label' => $this->l('Groups allowed for becoming affiliates'),
                        'multiple' => true,
                        'options' => array(
                            'query' => $customerGroups,
                            'id' => 'id_group',
                            'name' => 'name',
                        ),
                        'size' => sizeof($customerGroups),
                    ),
                    array(
                        'col' => 5,
                        'type' => 'textarea',
                        'desc' => $this->l('You will receive an email on these addresses when a customer will want to register as an affiliate and when an affiliate will request payment. (one per line)'),
                        'name' => 'emails',
                        'label' => $this->l('Your emails'),
                    ),
                ),
                'submit' => array(
                    'title' => $this->l('Save'),
                ),
            ),
        );
    }

    public function validateFields()
    {
        $errors = array();
        $AffConf = new AffConf;
        foreach ($AffConf->getConfigForm() as $configForm) {
            foreach ($configForm['input'] as $input) {
                $name = $input['name'];
                $label = $input['label'];
                if (isset($input['validate'])) {
                    $validate = $input['validate'];
                    if (!Validate::{$validate}(pSQL(Tools::getValue($name)))) {
                        $errors[$name] = $this->generateError($label, $validate);
                    }
                }
                if ($name == 'affiliate_year_prefix_parameter') {
                    $value = Tools::getValue($name);
                    if (Tools::strlen($value) != 1 || !ctype_alpha($value)) {
                        $errors[$name] = $this->generateError($label, 'isAffiliateYearPrefixParameter');
                    }
                }
            }
        }

        return implode("<br />", $errors);
    }

    public function generateError($label = false, $validate = false)
    {
        if (!class_exists('Psaffiliate')) {
            $moduleObj = Module::getInstanceByName('psaffiliate');
        } else {
            $moduleObj = new Psaffiliate;
        }
        if ($label && $validate) {
            switch ($validate) {
                case 'isAffiliateYearPrefixParameter':
                    return sprintf(
                        $this->l('%s must contain exactly 1 letter. (numbers or other characters are not accepted)'),
                        $label
                    );
                case 'isFloat':
                    return sprintf(
                        $this->l('The field "%s" has to be a float value, separated by dot (".")'),
                        $label
                    );
                default:
                    return sprintf(
                        $this->l('The field "%1$s" is not validating the rule "%2$s"'),
                        $label,
                        $validate
                    );
            }
        } else {
            return $this->l('Unknown error');
        }
    }

    public static function getConfiguration()
    {
        $db = Db::getInstance();
        /* Normal fields */
        $sql = "SELECT * FROM `"._DB_PREFIX_."aff_configuration`;";
        $sql = $db->executeS($sql);
        /* Multi-lang fields */
        $sql_lang = "SELECT * FROM `"._DB_PREFIX_."aff_configuration_lang`;";
        $sql_lang = $db->executeS($sql_lang);
        $sql_lang_array = array();
        foreach ($sql_lang as $sql_lang_row) {
            $sql_lang_array[$sql_lang_row['name']][(int)$sql_lang_row['id_lang']] = $sql_lang_row['value'];
            foreach ($sql as $sql_key => $sql_row) {
                if ($sql_row['name'] == $sql_lang_row['name']) {
                    unset($sql[$sql_key]);
                }
            }
        }
        $langs = Language::getLanguages(false);
        $id_default_lang = (int)Configuration::get('PS_LANG_DEFAULT');
        foreach ($sql_lang as $sql_lang_row) {
            foreach ($langs as $lang) {
                if (!isset($sql_lang_array[$sql_lang_row['name']][(int)$lang['id_lang']])) {
                    if (isset($sql_lang_array[$sql_lang_row['name']][$id_default_lang])) {
                        $sql_lang_array[$sql_lang_row['name']][(int)$lang['id_lang']] = $sql_lang_array[$sql_lang_row['name']][$id_default_lang];
                    } else {
						$av = array_values($sql_lang_array[$sql_lang_row['name']]);
						if(is_array($av)){
							$sql_lang_array[$sql_lang_row['name']][(int)$lang['id_lang']] = array_shift($av);
						}
                    }
                }
            }
        }
        $configuration = array();
        foreach ($sql as $sqlval) {
            if (is_array(Tools::getValue($sqlval['name']))) {
                $_POST[$sqlval['name']] = Tools::jsonEncode(Tools::getValue($sqlval['name']));
            }
            if (is_string(Tools::getValue(
                    $sqlval['name'],
                    $sqlval['value']
                )) && is_array(Tools::jsonDecode(
                    Tools::getValue($sqlval['name'], $sqlval['value']),
                    true
                ))
            ) {
                $configuration[$sqlval['name']."[]"] = Tools::jsonDecode(Tools::getValue(
                    $sqlval['name'],
                    $sqlval['value']
                ), true);
            } else {
                $configuration[$sqlval['name']] = Tools::getValue($sqlval['name'], $sqlval['value']);
            }
        }
        $AffConf = new AffConf;
        foreach ($AffConf->getConfigForm() as $configForm) {
            foreach ($configForm['input'] as $input) {
                $name = $input['name'];
                if (!isset($configuration[$name])) {
                    $configuration[$name] = "";
                }
            }
        }
        $configuration = array_merge($configuration, $sql_lang_array);

        return $configuration;
    }

    public static function getConfig($name = false, $id_lang = null)
    {
        if (!$name) {
            return AffConf::getConfiguration();
        } else {
            $configuration = AffConf::getConfiguration();
            if (isset($configuration[$name])) {
                if ($id_lang === true) {
                    $id_lang = Context::getContext()->language->id;
                }
                if ($id_lang) {
                    if (isset($configuration[$name][$id_lang])) {
                        return $configuration[$name][$id_lang];
                    } else {
                        $id_lang_default = Configuration::get('PS_LANG_DEFAULT');

                        return $configuration[$name][$id_lang_default];
                    }
                }

                return $configuration[$name];
            }
        }

        return false;
    }

    public static function getLangFields()
    {
        $AffConf = new AffConf;
        $configForm = $AffConf->getConfigForm();
        $inputs = $configForm['form']['input'];
        $lang_fields = array();
        foreach ($inputs as $input) {
            if (isset($input['lang']) && $input['lang']) {
                $lang_fields[] = $input['name'];
            }
        }

        return $lang_fields;
    }

    public function l($string)
    {
        return Translate::getModuleTranslation('psaffiliate', $string, get_class($this));
    }
}
