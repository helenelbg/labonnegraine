<?php
//error_reporting(E_ALL);
//ini_set('display_errors', 1);

class HomeCategories extends Module
{

    public function __construct()
    {
        $this->name = 'homecategories';
        $this->tab = 'front_office_features';
        $this->version = '1.6.4';
        $this->author = 'PrestaShop';
        $this->need_instance = 1;

        $this->bootstrap = true;
        parent::__construct();

        $this->displayName = $this->l('Featured categories on the homepage');
        $this->description = $this->l('Displays categories in the central column of your homepage.');
    }

    public function install()
    {
        Configuration::updateValue('HOMECATGORIES_NBR', 3);
        Configuration::updateValue('HOMECATEGORIES_CATEGORIES', '');

        if (!parent::install() || !$this->registerHook('header') || !$this->registerHook('actionCategoryUpdate') || !$this->registerHook('displayHome'))
            return false;

        return true;
    }

    public function uninstall()
    {
        $this->_clearCache('*');

        return parent::uninstall();
    }

    public function getContent()
    {
        $output = '';
        $errors = array();
        if (Tools::isSubmit('submitHomeCategories'))
        {

            $nbr = Tools::getValue('HOMECATGORIES_NBR');
            if (!Validate::isInt($nbr) || $nbr <= 0)
                $errors[] = $this->l('The number of products is invalid. Please enter a positive number.');

            $categories = Tools::getValue('HOMECATEGORIES_CATEGORIES');

            if (!empty($categories) && !Validate::isArrayWithIds($categories))
				$errors[] = $this->l('The category ID is invalid. Please choose an existing category ID.');


            if (isset($errors) && count($errors))
            {
                $output = $this->displayError(implode('<br />', $errors));
            }
            else
            {
                Configuration::updateValue('HOMECATGORIES_NBR', (int) $nbr);
                Configuration::updateValue('HOMECATEGORIES_CATEGORIES', json_encode($categories));
                Tools::clearCache(Context::getContext()->smarty, $this->getTemplatePath('homecategories.tpl'));
                $output = $this->displayConfirmation($this->l('Your settings have been updated.'));
            }
        }
        
        return $output.$this->renderForm();
    }
    
    public function getCategories()
    {
        $categories_ids = json_decode(Configuration::get('HOMECATEGORIES_CATEGORIES'));
        $liste_categories = Category::getCategoryInformations($categories_ids);

        foreach($categories_ids as $id_category)
        {
            $cat = new Category($id_category);
            $liste_categories[$id_category]['id_image'] = $cat->id_image;
        }

        return $liste_categories;
    }
    
    public function hookDisplayHeader($params)
    {
            $this->hookHeader($params);
    }

    public function hookHeader($params)
    {
            $this->context->controller->addCSS(($this->_path).'css/homecategories.css', 'all');
    }

    public function hookDisplayHome($params)
    {
		return false;
		
        /*$categories = $this->getCategories();
        
        $this->smarty->assign(
                array(
                    'categories' => $categories
                )
        );

        return $this->display(__FILE__, 'homecategories.tpl');*/
    }

    public function hookDisplayHomeTabContent($params)
    {
            return $this->hookDisplayHome($params);
    }
    
    public function renderForm()
    {
        $selected_categories = json_decode(Configuration::get('HOMECATEGORIES_CATEGORIES'));
        
        if(empty($selected_categories))
        {
           $selected_categories = array();
        }

        $fields_form = array(
			'form' => array(
				'legend' => array(
					'title' => $this->l('Settings'),
					'icon' => 'icon-cogs'
				),
				'description' => $this->l('To add products to your homepage, simply add them to the corresponding product category (default: "Home").'),                                
				'input' => array(
					array(
						'type' => 'text',
						'label' => $this->l('Number of categories to be displayed'),
						'name' => 'HOMECATGORIES_NBR',
						'class' => 'fixed-width-xs',
						'desc' => $this->l('Set the number of categories that you would like to display on homepage (default: 3).'),
					),
                                        array(
                                            'type' => 'categories', //Type de champ à mettre à catégories
                                            'label' => $this->l('Category'),
                                            'name' => 'HOMECATEGORIES_CATEGORIES',
                                            'desc' => $this->l('Select category to display'),
                                            'required' => true,
                                            'empty_message' => $this->l('Please fill the category id'),
                                            //Informations spécifiques de l'arbre
                                            'tree' => array(
                                                'id' => 'category_tree',
                                                'use_checkbox' => true, 
                                                'selected_categories' => $selected_categories
                                                ) // Catégorie sélectionnées ( variable array )
                                            ),
                                    ),
				'submit' => array(
					'title' => $this->l('Save'),
				)
			),
		);
        $helper = new HelperForm();
        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $lang = new Language((int)Configuration::get('PS_LANG_DEFAULT'));
        $helper->default_form_language = $lang->id;
        $this->fields_form = array();
        $helper->identifier = $this->identifier;
        $helper->submit_action = 'submitHomeCategories';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false).'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->tpl_vars = array(
                'fields_value' => $this->getConfigFieldsValues(),
                'languages' => $this->context->controller->getLanguages(),
                'id_language' => $this->context->language->id
        );
        
        return $helper->generateForm(array($fields_form));        
    }
    
    public function getConfigFieldsValues()
    {
            return array(
                    'HOMECATGORIES_NBR' => Tools::getValue('HOMECATGORIES_NBR', (int)Configuration::get('HOMECATGORIES_NBR')),
            );
    }
    

}
