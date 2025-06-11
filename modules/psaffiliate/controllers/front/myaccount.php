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

class PsaffiliateMyaccountModuleFrontController extends ModuleFrontController
{
    public function __construct()
    {
        parent::__construct();
        $this->context = Context::getContext();
        $this->module->loadClasses('Affiliate');
    }

    public function initContent()
    {
        parent::initContent();

        return $this->displayTemplate();
    }

    public function displayTemplate()
    {
        if ($this->context->customer->isLogged()) {
            $this->module->loadClasses('AffConf');
            if ($this->module->isAffiliate() && !Tools::getValue('submitRegisterAffiliate')) {
                if (Tools::getValue('successRegistration')) {
                    $this->context->smarty->assign('successRegistration', true);
                }

                $limit = 6;
                $tab = Tools::getValue('t') ?: 'home';
                $page = (int)Tools::getValue('p') ?: 1;
                $search = trim(Tools::getValue('s'));
                $start = ($page - 1) * $limit;

                $affiliate = new Affiliate($this->module->getAffiliateId());
                $myaffiliatelink = $this->module->getAffiliateLink();

                $total = $this->getProductsWithCommisions(
                    $affiliate,
                    $tab == 'products' ? $start : 0,
                    $limit,
                    true,
                    $search ?: null
                );
                $product_commisions_last = ceil($total / $limit);

                $product_commisions = $this->getProductsWithCommisions(
                    $affiliate,
                    $tab == 'products' ? $start : 0,
                    $limit,
                    false,
                    $search ?: null
                );
                $product_commisions_pages = static::getPaginator($page, $product_commisions_last);

                $total = $this->getSellerSaleCommissions($affiliate, $tab == 'sales' ? $start : 0, $limit, true);
                $sale_commissions_last = ceil($total / $limit);

                $total_commissions = $this->getTotalCommissions($affiliate);
                
				$sale_commissions = $this->getSellerSaleCommissions($affiliate, $tab == 'sales' ? $start : 0, $limit);
                $sale_commissions_pages = static::getPaginator($page, $sale_commissions_last);

                $days_current_summary = AffConf::getConfig('days_current_summary');
                $minimum_payment_amount = (float)AffConf::getConfig('minimum_payment_amount');
                $this->context->smarty->assign('myaffiliatelink', $myaffiliatelink);
                $this->context->smarty->assign('affiliate', (array)$affiliate);
                $this->context->smarty->assign('currency', $this->context->currency);
                $this->context->smarty->assign('days_current_summary', $days_current_summary);
                $this->context->smarty->assign('minimum_payment_amount', $minimum_payment_amount);
                $this->context->smarty->assign('vouchers', $affiliate->getVouchers());
                $this->context->smarty->assign('current_tab', $tab);
                $this->context->smarty->assign('current_page', $page);
                $this->context->smarty->assign('product_commisions', $product_commisions);
                $this->context->smarty->assign('product_commisions_pages', $product_commisions_pages);
                $this->context->smarty->assign('product_commisions_last', $product_commisions_last);
                $this->context->smarty->assign('search_terms', $search);
                $this->context->smarty->assign('total_commissions', $total_commissions);
                $this->context->smarty->assign('sale_commissions', $sale_commissions);
                $this->context->smarty->assign('sale_commissions_pages', $sale_commissions_pages);
                $this->context->smarty->assign('sale_commissions_last', $sale_commissions_last);
                $this->context->smarty->assign(
                    'voucher_payments_enabled',
                    (bool)AffConf::getConfig('enable_voucher_payments')
                );
                $this->context->smarty->assign(
                    'lifetime_affiliates_enabled',
                    (bool)AffConf::getConfig('commissions_for_life')
                );
                if ((bool)AffConf::getConfig('commissions_for_life')) {
                    $this->context->smarty->assign(
                        'lifetime_affiliations',
                        $this->module->getLifetimeAffiliations($affiliate->id)
                    );
                }

                $this->context->smarty->assign('hasTexts', $this->module->hasTexts(true));
                $this->context->smarty->assign('hasBanners', $this->module->hasBanners(true));
                $campaigns = $affiliate->getCampaigns(true);

                foreach ($campaigns as $key => $campaign) {
                    $campaigns[$key]['earnings_clicks_sales_sum'] = Tools::displayPrice($campaign['total_earnings_clicks'] + $campaign['total_earnings_sales']);
                }

                $this->context->smarty->assign('campaigns', $campaigns);

                $campaigns_links = array();
                $link = new Link;

                foreach ($campaigns as $key => $campaign) {
                    $campaigns_links[$campaign['id_campaign']] = $link->getModuleLink(
                        'psaffiliate',
                        'campaign',
                        array('id_campaign' => $campaign['id_campaign'])
                    );
                }

                $this->context->smarty->assign('campaigns_links', $campaigns_links);

                if (Tools::version_compare(_PS_VERSION_, '1.7', '<')) {
                    $this->setTemplate("my-account-view.tpl");
                } else {
                    $this->setTemplate("module:psaffiliate/views/templates/front/ps17/my-account-view.tpl");
                }
            } else {
                $is_group_allowed = $this->module->isGroupAllowed($this->context->customer->id_default_group);
                if (!$is_group_allowed) {
                    $this->errors[] = Tools::displayError('Sorry, you do not have access to this page.');
                    $this->context->smarty->assign(array(
                        'error' => 'group_blocked',
                    ));
                    if (Tools::version_compare(_PS_VERSION_, '1.7', '<')) {
                        $this->setTemplate("my-account-register.tpl");
                    } else {
                        $this->setTemplate("module:psaffiliate/views/templates/front/ps17/my-account-register.tpl");
                    }

                    return;
                }
                $customer = $this->context->customer;

                if (Validate::isLoadedObject($customer)) {
                    $enableTerms = AffConf::getConfig('enable_terms_at_signup');
                    $termsCmsLink = null;

                    if ($enableTerms && $termsCmsId = AffConf::getConfig('terms_cms_id')) {
                        if (Validate::isLoadedObject($termsCms = new CMS((int)$termsCmsId))) {
                            $termsCmsLink = $this->context->link->getCmsLink($termsCms);
                        }
                    }

                    $this->module->loadClasses('Customfield');
                    $customFields = Customfield::all();

                    $this->context->smarty->assign(array(
                        'customer_name' => $customer->firstname." ".$customer->lastname,
                        'customer_email' => $customer->email,
                        'textarea_at_registration' => (bool)AffConf::getConfig('textarea_at_registration'),
                        'textarea_at_registration_required' => (bool)AffConf::getConfig('textarea_at_registration_required'),
                        'textarea_at_registration_label' => AffConf::getConfig('textarea_at_registration_label', true),
                        'ask_for_website' => AffConf::getConfig('ask_for_website'),
                        'affiliates_require_approval' => AffConf::getConfig('affiliates_require_approval'),
                        'display_terms_checkbox' => $enableTerms,
                        'terms_cms_link' => $termsCmsLink,
                        'custom_fields' => $customFields,
                    ));

                    if (Tools::getValue('submitRegisterAffiliate')) {
                        $this->context->smarty->assign('submitted', true);
                        if ($this->module->getAffiliateId($customer->id)) {
                            $this->errors[] = Tools::displayError('You are already registered as an affiliate.');
                        }
                        $require_textarea_registration = AffConf::getConfig('textarea_at_registration_required');
                        if ($require_textarea_registration && !Tools::getValue('textarea_registration')) {
                            $this->errors[] = sprintf(
                                Tools::displayError('Field "%s" is required.'),
                                AffConf::getConfig('textarea_at_registration_label')
                            );
                        }
                        if ($enableTerms && $termsCmsLink && !Tools::getValue('terms_and_conditions')) {
                            $this->errors[] = Tools::displayError('You must accept the terms and conditions.');
                        }

                        $metas = array();

                        // Validate custom meta fields.
                        foreach ($customFields as $customField) {
                            $key = 'custom_field_'.(int)$customField['id_field'];
                            $value = trim(Tools::getValue($key));

                            // Check if field is required.
                            if ((bool)$customField['required'] && !$value) {
                                $this->errors[] = sprintf(
                                    $this->l('Field "%s" is required.'),
                                    $customField['name']
                                );
                            }

                            // Check if field is valid.
                            if ($customField['type'] === 'link' && $value && !Validate::isUrl($value)) {
                                $this->errors[] = sprintf(
                                    $this->l('Field "%s" must be a valid link.'),
                                    $customField['name']
                                );
                            }

                            $metas[$key] = $value;
                        }

                        if (!sizeof($this->errors)) {
                            $affiliate = new Affiliate();
                            $affiliate->id_customer = $customer->id;
                            $affiliate->email = $customer->email;
                            $affiliate->firstname = $customer->firstname;
                            $affiliate->lastname = $customer->lastname;
                            $affiliate->active = AffConf::getConfig('affiliates_require_approval') ? false : true;
                            $affiliate->website = pSQL(Tools::getValue('website'));
                            $affiliate->textarea_registration = pSQL(Tools::getValue('textarea_registration'));
                            $affiliate->textarea_registration_label = pSQL(AffConf::getConfig(
                                'textarea_at_registration_label',
                                true
                            ));
                            $success = $affiliate->add();
                            $this->context->smarty->assign('success', $success);
                            if ($success) {
                                // Save custom fields
                                $affiliate->saveMeta($metas);

                                $link = new Link;
                                $redirect_url = $link->getModuleLink(
                                    'psaffiliate',
                                    'myaccount',
                                    array('successRegistration' => true),
                                    true
                                );

                                $admin_emails = $this->module->getAdminEmails();
                                if ($admin_emails) {
                                    $iso = Language::getIsoById((int) Configuration::get('PS_LANG_DEFAULT'));
                                    $dir_mail = false;

                                    if (file_exists($this->module->getPathDir().'/mails/'.$iso.'/admin_new_affiliate.txt') &&
                                        file_exists($this->module->getPathDir().'/mails/'.$iso.'/admin_new_affiliate.html')) {
                                        $dir_mail = $this->module->getPathDir().'/mails/';
                                    }

                                    if (file_exists(_PS_MAIL_DIR_.$iso.'/admin_new_affiliate.txt') &&
                                        file_exists(_PS_MAIL_DIR_.$iso.'/admin_new_affiliate.html')) {
                                        $dir_mail = _PS_MAIL_DIR_;
                                    }

                                    if (!$dir_mail) {
                                        $iso = 'en';
                                        if (file_exists($this->module->getPathDir().'/mails/'.$iso.'/admin_new_affiliate.txt') &&
                                            file_exists($this->module->getPathDir().'/mails/'.$iso.'/admin_new_affiliate.html')) {
                                            $dir_mail = $this->module->getPathDir().'/mails/';
                                        }

                                        if (file_exists(_PS_MAIL_DIR_.$iso.'/admin_new_affiliate.txt') &&
                                            file_exists(_PS_MAIL_DIR_.$iso.'/admin_new_affiliate.html')) {
                                            $dir_mail = _PS_MAIL_DIR_;
                                        }
                                    }

                                    $mail_id_lang = (int)Configuration::get('PS_LANG_DEFAULT');
                                    foreach ($admin_emails as $admin_email) {
                                        if ($dir_mail) {
                                            $template_vars = array(
                                                '{email}' => $affiliate->email,
                                                '{name}' => $affiliate->firstname." ".$affiliate->lastname,
                                            );
                                            Mail::Send(
                                                $mail_id_lang,
                                                'admin_new_affiliate',
                                                sprintf(Mail::l('New affiliate request: %s', $mail_id_lang), $affiliate->firstname." ".$affiliate->lastname),
                                                $template_vars,
                                                $admin_email,
                                                null,
                                                Configuration::get('PS_SHOP_EMAIL'),
                                                Configuration::get('PS_SHOP_NAME'),
                                                null,
                                                null,
                                                $dir_mail,
                                                null,
                                                (int)$this->context->shop->id
                                            );
                                        }
                                    }
                                }

                                Tools::redirect($redirect_url);
                            }
                        }
                        $this->context->smarty->assign('errors', $this->errors);
                    }

                    if (Tools::version_compare(_PS_VERSION_, '1.7', '<')) {
                        $this->setTemplate("my-account-register.tpl");
                    } else {
                        $this->setTemplate("module:psaffiliate/views/templates/front/ps17/my-account-register.tpl");
                    }
                }
            }
        } else {
            Tools::redirect('index.php?controller=authentication&back='.urlencode($this->context->link->getModuleLink(
                    'psaffiliate',
                    'myaccount'
                )));
        }
    }

    public function getBreadcrumbLinks()
    {
        $breadcrumb = parent::getBreadcrumbLinks();

        $breadcrumb['links'][] = $this->addMyAccountToBreadcrumb();

        return $breadcrumb;
    }

    public function l($string, $specific = false, $class = null, $addslashes = false, $htmlentities = true)
    {
        return Translate::getModuleTranslation('psaffiliate', $string, 'myaccount');
    }

    public function getProductsWithCommisions($affiliate, $start, $limit, $count = false, $search = null)
    {
        $pr = _DB_PREFIX_;
        $select = !$count ? 'select p.`id_product`, p.`reference`, pl.`name`, pl.`link_rewrite`' : 'select count(p.`id_product`) as `total`';
        $limit = !$count ? "limit ".(int)$start.", ".(int)$limit : '';
        $like = $search ? 'and (pl.`name` like "%'.pSQL($search).'%" or p.`reference` like "%'.pSQL($search).'%")' : '';

        $product_commisions = Db::getInstance()->executeS("
            {$select}
            from `{$pr}product` p
            inner join `{$pr}product_lang` pl
            on p.`id_product` = pl.`id_product`
            left join `{$pr}product_shop` ps
            on ps.`id_product` = p.`id_product` and ps.`id_shop` = ".(int)$this->context->shop->id."
            left join `{$pr}aff_product_rates` pr
            on pr.`id_product` = p.`id_product`
            left join `{$pr}aff_category_rates` cr
            on ps.`id_category_default` = cr.`id_category`
            where ps.`active` = 1
            and pl.`id_lang` = ".(int)$this->context->language->id."
            and pl.`id_shop` = ".(int)$this->context->shop->id."
            and ((pr.rate_percent != 0 or pr.rate_value != 0) or ((pr.rate_percent = -1 and cr.rate_percent != 0) or (pr.rate_value = -1 and cr.rate_value != 0)))
            and pr.multiplier != 0 and cr.multiplier != 0
            {$like}
            order by p.`date_add` desc
            {$limit}
        ");

            
            
          /*  echo "
            {$select}
            from `{$pr}product` p
            inner join `{$pr}product_lang` pl
            on p.`id_product` = pl.`id_product`
            left join `{$pr}product_shop` ps
            on ps.`id_product` = p.`id_product` and ps.`id_shop` = ".(int)$this->context->shop->id."
            left join `{$pr}aff_product_rates` pr
            on pr.`id_product` = p.`id_product`
            left join `{$pr}aff_category_rates` cr
            on ps.`id_category_default` = cr.`id_category`
            where ps.`active` = 1
            and pl.`id_lang` = ".(int)$this->context->language->id."
            and pl.`id_shop` = ".(int)$this->context->shop->id."
            and ((pr.rate_percent != 0 or pr.rate_value != 0) or ((pr.rate_percent = -1 and cr.rate_percent != 0) or (pr.rate_value = -1 and cr.rate_value != 0)))
            and pr.multiplier != 0 and cr.multiplier != 0
            {$like}
            order by p.`date_add` desc
            {$limit}
        "; die;*/
            
        if (!$product_commisions) {
            return !$count ? array() : 0;
        }

        if ($count) {
            return (int)$product_commisions[0]['total'];
        }

        array_walk($product_commisions, function (&$product) use ($affiliate) {
            $product['commision'] = $this->module->formatProductRates(
                $this->module->getRatesForProduct((int)$product['id_product'], (int)$affiliate->id)
            );

            $cover = Product::getCover((int)$product['id_product']) ?: null;
            if (method_exists('ImageType', 'getFormattedName')) {
                $image_small_formatted_name = ImageType::getFormattedName('small');
            } else {
                $image_small_formatted_name = ImageType::getFormatedName('small');
            }
            $product['image'] = $cover ? $this->context->link->getImageLink(
                $product['link_rewrite'],
                (int)$cover['id_image'],
                $image_small_formatted_name
            ) : null;


            $product['link'] = $this->context->link->getProductLink(
                (int)$product['id_product'],
                $product['link_rewrite']
            );
            $product['aff_link'] = $this->module->getAffiliateLink((int)$affiliate->id, (int)$product['id_product']);
        });

        return $product_commisions;
    }

    public function getTotalCommissions($seller)
    {
        $pr = _DB_PREFIX_;
        $select = 'select s.*, SUM(s.commission) as `total`';

        $sales = Db::getInstance()->executeS("
            {$select}
            from `{$pr}aff_sales` s 
            left join `{$pr}orders` o
            on s.`id_order` = o.`id_order`
            where s.`id_affiliate` = ".(int)$seller->id."
        ");

        if (is_array($sales) && count($sales)) {
			$total = Tools::displayPrice(Tools::convertPrice($sales[0]['total']));
            return $total;
        }

        return false;
    }
	
	public function getSellerSaleCommissions($seller, $start, $limit, $count = false)
    {
        $pr = _DB_PREFIX_;
        $select = !$count
            ? 'select s.*, (o.`total_products_wt` - o.`total_discounts_tax_incl`) as `order_total`'
            : 'select count(s.`id_sale`) as total';
        $limit = !$count ? "limit ".(int)$start.", ".(int)$limit : '';

        $sales = Db::getInstance()->executeS("
            {$select}
            from `{$pr}aff_sales` s 
            left join `{$pr}orders` o
            on s.`id_order` = o.`id_order`
            where s.`id_affiliate` = ".(int)$seller->id."
            order by s.`id_sale` desc
            {$limit}
        ");

        if (!$sales) {
            return !$count ? array() : 0;
        }

        if ($count) {
            return (int)$sales[0]['total'];
        }

        array_walk($sales, function (&$sale) {
            $sale['commission'] = Tools::displayPrice(Tools::convertPrice($sale['commission']));
            $sale['order_total'] = Tools::displayPrice(Tools::convertPrice($sale['order_total']));
        });

        return $sales;
    }

    public static function getPaginator($current, $last)
    {
        $delta = 2;
        $left = $current - $delta;
        $right = $current + $delta + 1;
        $range = $rangeWithDots = array();
        $l = null;

        for ($i = 1; $i <= $last; $i++) {
            if ($i == 1 || $i == $last || $i >= $left && $i < $right) {
                $range[] = $i;
            }
        }

        foreach ($range as $i) {
            if ($l) {
                if ($i - $l === 2) {
                    $rangeWithDots[] = $l + 1;
                } elseif ($i - $l !== 1) {
                    $rangeWithDots[] = '...';
                }
            }
            $rangeWithDots[] = $i;
            $l = $i;
        }

        return $rangeWithDots;
    }
}
