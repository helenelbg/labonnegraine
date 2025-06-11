<?php
class CategoriesEnAvant extends Module
{

    public function __construct()
    {
        $this->name = 'categoriesenavant';
        $this->tab = 'front_office_features';
        $this->version = '1.0.0';
        $this->author = 'Anjou Web';
        $this->need_instance = 1;

        $this->bootstrap = true;
        parent::__construct();

        $this->displayName = $this->l('Mise en avant de catégories');
        $this->description = $this->l('Mise en avant de catégories');
    }

    public function install()
    {
        Configuration::updateValue('CATEGORIESENAVANT_TITRE', 'Nos nouvelles gammes');
        Configuration::updateValue('CATEGORIESENAVANT_COULEUR', '#D82637');
        Configuration::updateValue('CATEGORIESENAVANT_NBR', 3);
        Configuration::updateValue('CATEGORIESENAVANT_CATEGORIES', '');

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
        if (Tools::isSubmit('submitCategoriesEnAvant'))
        {

            $nbr = Tools::getValue('CATEGORIESENAVANT_NBR');
            if (!Validate::isInt($nbr) || $nbr <= 0)
                $errors[] = $this->l('The number of products is invalid. Please enter a positive number.');

            $categories = Tools::getValue('CATEGORIESENAVANT_CATEGORIES');

            if (!empty($categories) && !Validate::isArrayWithIds($categories))
				$errors[] = $this->l('The category ID is invalid. Please choose an existing category ID.');


            if (isset($errors) && count($errors))
            {
                $output = $this->displayError(implode('<br />', $errors));
            }
            else
            {
                Configuration::updateValue('CATEGORIESENAVANT_TITRE', Tools::getValue('CATEGORIESENAVANT_TITRE'));
                Configuration::updateValue('CATEGORIESENAVANT_COULEUR', Tools::getValue('CATEGORIESENAVANT_COULEUR'));
                Configuration::updateValue('CATEGORIESENAVANT_NBR', (int) $nbr);
                Configuration::updateValue('CATEGORIESENAVANT_CATEGORIES', json_encode($categories));
                Tools::clearCache(Context::getContext()->smarty, $this->getTemplatePath('categoriesenavant.tpl'));
                $output = $this->displayConfirmation($this->l('Your settings have been updated.'));
            }
        }
        
        return $output.$this->renderForm();
    }
    
    public function getCategories()
    {
        $categories_ids = json_decode(Configuration::get('CATEGORIESENAVANT_CATEGORIES'));
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
            $this->context->controller->addCSS(($this->_path).'css/categoriesenavant.css', 'all');
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
        $selected_categories = json_decode(Configuration::get('CATEGORIESENAVANT_CATEGORIES'));
        
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
						'name' => 'CATEGORIESENAVANT_NBR',
						'class' => 'fixed-width-xs',
						'desc' => $this->l('Set the number of categories that you would like to display on homepage (default: 3).'),
					),
					array(
						'type' => 'text',
						'label' => $this->l('Titre du bloc'),
						'name' => 'CATEGORIESENAVANT_TITRE',
					),
					array(
						'type' => 'text',
						'label' => $this->l('Couleur du bloc'),
						'name' => 'CATEGORIESENAVANT_COULEUR',
					),
                                        array(
                                            'type' => 'categories', //Type de champ � mettre � cat�gories
                                            'label' => $this->l('Category'),
                                            'name' => 'CATEGORIESENAVANT_CATEGORIES',
                                            'desc' => $this->l('Select category to display'),
                                            'required' => true,
                                            'empty_message' => $this->l('Please fill the category id'),
                                            //Informations sp�cifiques de l'arbre
                                            'tree' => array(
                                                'id' => 'category_tree',
                                                'use_checkbox' => true, 
                                                'selected_categories' => $selected_categories
                                                ) // Cat�gorie s�lectionn�es ( variable array )
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
        $helper->submit_action = 'submitCategoriesEnAvant';
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
                'CATEGORIESENAVANT_TITRE' => Tools::getValue('CATEGORIESENAVANT_TITRE', (int)Configuration::get('CATEGORIESENAVANT_TITRE')),
                'CATEGORIESENAVANT_COULEUR' => Tools::getValue('CATEGORIESENAVANT_COULEUR', (int)Configuration::get('CATEGORIESENAVANT_COULEUR')),
                'CATEGORIESENAVANT_NBR' => Tools::getValue('CATEGORIESENAVANT_NBR', (int)Configuration::get('CATEGORIESENAVANT_NBR')),
            );
    }
    

}
