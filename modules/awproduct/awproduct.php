<?php

use PrestaShopBundle\Form\Admin\Type\DatePickerType;
use PrestaShop\PrestaShop\Core\Grid\Filter\Filter;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\DataColumn;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class AwProduct extends Module {
    
     public function __construct() {
		
		require_once(dirname(__FILE__).'/classes/AwCustomCategory.php');

        $this->name = 'awproduct';
        $this->tab = 'others';
        $this->author = 'anjouweb';
        $this->version = '1.0.0';
        $this->need_instance = 0;
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('awproduct');
        $this->description = $this->l('add new fields to product');
        $this->ps_versions_compliancy = array('min' => '1.7.1', 'max' => _PS_VERSION_);
    }
    
   public function install() {
		$awCustomCategory = "CREATE TABLE IF NOT EXISTS `"._DB_PREFIX_."aw_custom_category` (
            `id_aw_custom_category` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
            `id_category` INT(10) NULL DEFAULT 0,
            `date_precommande` VARCHAR(255) NULL DEFAULT NULL,
            PRIMARY KEY (`id_aw_custom_category`)
        )";
		Db::getInstance()->execute($awCustomCategory);
		
        if (!parent::install() || !$this->_installSql()
                //Pour les hooks suivants regarder le fichier src\PrestaShopBundle\Resources\views\Admin\Product\form.html.twig
                || ! $this->registerHook('displayAdminProductsExtra')
				|| ! $this->registerHook('displayAdminProductsMainStepLeftColumnMiddle')
				|| ! $this->registerHook('displayAdminCategoryExtra')
				|| ! $this->registerHook('displayBackOfficeCategory')
				|| ! $this->registerHook('displayBackOfficeHeader')
				|| ! $this->registerHook('actionProductUpdate')
				|| ! $this->registerHook('actionAdminCategoriesControllerSaveAfter')
				|| ! $this->registerHook('actionCategoryFormBuilderModifier')
				|| ! $this->registerHook('actionAfterCreateCategoryFormHandler')
				|| ! $this->registerHook('actionAfterUpdateCategoryFormHandler')
				|| ! $this->registerHook('actionAdminCategoriesFormModifier')
				|| ! $this->registerHook('actionPresentCart')
        ) {
            return false;
        }

        return true;
    }
    
     public function uninstall() {
        return parent::uninstall() && $this->_unInstallSql();
    }

    /**
     * Modifications sql du module
     * @return boolean
     */
    protected function _installSql() {
        /*$sqlInstall = "ALTER TABLE " . _DB_PREFIX_ . "product "
                . "ADD custom_field_nom VARCHAR(255) NULL"
                . "ADD custom_field_couleur VARCHAR(255) NULL";
        $sqlInstallLang = "ALTER TABLE " . _DB_PREFIX_ . "product_lang "
                . "ADD custom_field_description_listing TEXT NULL";

        $returnSql = Db::getInstance()->execute($sqlInstall);
        $returnSqlLang = Db::getInstance()->execute($sqlInstallLang);
        
        return $returnSql && $returnSqlLang;*/
        return true;
    }

    /**
     * Suppression des modifications sql du module
     * @return boolean
     */
    protected function _unInstallSql() {
		return true;
    }
    
    
    public function hookDisplayAdminProductsExtra($params)
    {
       return $this->_displayHookContentBlock(__FUNCTION__);
    }
    
    /**
     * Affichage des informations supplémentaires sur la fiche produit
     * @param type $params
     * @return type
     */
    public function hookDisplayAdminProductsMainStepLeftColumnMiddle($params) {
		$id_product = $params['id_product'];
		
		$sql = "SELECT * FROM awpf WHERE id_product = " . pSQL($id_product);
        $res = Db::getInstance()->executeS($sql);
		
		$sql2 = "SELECT botanic_name FROM ps_product WHERE id_product = " . pSQL($id_product);
        $res2 = Db::getInstance()->executeS($sql2);
		
		$coeur = '';
		$jardin_titre = '';
		$jardin_contenu = '';
		$botanic_name = '';
        $type = 1;
		
		if(count($res)){
			$coeur = $res[0]['coeur'];
			if($coeur){
				$coeur = "checked";
			}
			$jardin_titre = $res[0]['jardin_titre'];
			$jardin_contenu = $res[0]['jardin_contenu'];
			$type = $res[0]['type'];
            
			$sachet_titre1 = $res[0]['sachet_titre1'];
			$sachet_titre2 = $res[0]['sachet_titre2'];
			$sachet_desc_recto = $res[0]['sachet_desc_recto'];
			$sachet_desc_verso = $res[0]['sachet_desc_verso'];
			$sachet_normes = $res[0]['sachet_normes'];
			$sachet_passphy = $res[0]['sachet_passphy'];
			if($sachet_passphy){
				$sachet_passphy = "checked";
			}
		}
		
		if(count($res2)){
			$botanic_name = $res2[0]['botanic_name'];
		}
		
        $this->context->smarty->assign(
			array(
				'coeur' => $coeur,
				'jardin_titre' => $jardin_titre,
				'jardin_contenu' => $jardin_contenu,
				'botanic_name' => $botanic_name,
				'type' => $type,
				'sachet_titre1' => $sachet_titre1,
				'sachet_titre2' => $sachet_titre2,
				'sachet_desc_recto' => $sachet_desc_recto,
				'sachet_desc_verso' => $sachet_desc_verso,
				'sachet_normes' => $sachet_normes,
				'sachet_passphy' => $sachet_passphy,
            )
		);
        
        return $this->display(__FILE__, 'views/templates/hook/productextrafields.tpl');
    }

   
   

    /**
     * Fonction pour afficher les différents blocks disponibles
     * @param type $hookName
     * @return type
     */
    protected function  _displayHookContentBlock($hookName) {
        $this->context->smarty->assign('hookName',$hookName);
        return $this->display(__FILE__, 'views/templates/hook/hookBlock.tpl');
    }

	public function hookDisplayBackOfficeHeader() {
		$this->context->controller->addJS($this->_path.'views/js/custom.js');
    }
	
	/**
	 * A l'enregistrement d'un produit
     * @param array $params
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     */
	public function hookActionProductUpdate($params) {
		
		// Cette fonction est appelée lorsqu'un produit est enregistré en BO.
		
		if(isset($_POST["id_product"])){
			$id_product = $_POST["id_product"];
			$coeur = isset($_POST['coeur']) ? 1 : 0; // checkbox
			$jardin_titre = $_POST["jardin_titre"];
			$jardin_contenu = $_POST["jardin_contenu"];
			$type = $_POST["type"];
			$botanic_name = $_POST["botanic_name"];
            
			$sachet_titre1 = $_POST["sachet_titre1"];
			$sachet_titre2 = $_POST["sachet_titre2"];
			$sachet_desc_recto = $_POST["sachet_desc_recto"];
			$sachet_desc_verso = $_POST["sachet_desc_verso"];
			$sachet_normes = $_POST["sachet_normes"];
			$sachet_passphy = isset($_POST['sachet_passphy']) ? 1 : 0; // checkbox

			$sql = "SELECT * FROM awpf WHERE id_product = " . pSQL($id_product);
			$res = Db::getInstance()->executeS($sql);

			// Insert si la ligne n'existe pas
			if(!count($res)){ 
				$sql = 'INSERT INTO awpf (id_product) VALUES('.pSQL($id_product).')'; 
				Db::getInstance()->execute($sql);			
			}
			
			// Update du produit
			$sql = 'UPDATE awpf SET 
			coeur = "'.pSQL($coeur).'",
			jardin_titre = "'.pSQL($jardin_titre).'",
			type = "'.pSQL($type).'",
			jardin_contenu = "'.pSQL($jardin_contenu, true).'",
			sachet_titre1 = "'.pSQL($sachet_titre1).'",
			sachet_titre2 = "'.pSQL($sachet_titre2).'",
			sachet_desc_recto = "'.pSQL($sachet_desc_recto).'",
			sachet_desc_verso = "'.pSQL($sachet_desc_verso).'",
			sachet_normes = "'.pSQL($sachet_normes).'",
			sachet_passphy = "'.pSQL($sachet_passphy).'"
			WHERE id_product='.pSQL($id_product);
			Db::getInstance()->execute($sql);
			
			$sql = 'UPDATE ps_product SET 
			botanic_name = "'.pSQL($botanic_name).'"
			WHERE id_product='.pSQL($id_product);
			Db::getInstance()->execute($sql);
		}
		  
    }
	
	public static function getCoeur() {
		return 1;
	}
	
	
	
	/**
     * @param array $params
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     */
    public function hookActionAdminCategoriesFormModifier($params)
    {
        $params['fields']['precommande'] = array(
            'form' => array(
                'legend' => array(
                    'title' => $this->l('Date de précommande'),
                    'icon'  => 'icon-tags',
                ),
                'input'  => array(
                    array(
                        'type'  => 'text',
                        'label' => $this->l('Date de précommande'),
                        'name'  => 'date_precommande',
                    ),
                ),
                'submit' => array(
                    'title' => $this->l('Save'),
                ),
            ),
        );
        $idCategory = (int) Tools::getValue('id_category');
        $categoryCustomDetails = AwCustomCategory::getByIdCategory($idCategory);
        $params['fields_value']['date_precommande'] = $categoryCustomDetails->date_precommande;
    }

    /**
     * @param array $params
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     */
    public function hookActionAdminCategoriesControllerSaveAfter($params)
    {
        $idCategory = (int) Tools::getValue('id_category');
        $date_precommande = Tools::getValue('date_precommande');

        $customCategory = AwCustomCategory::getByIdCategory((int) $idCategory);
        $customCategory->id_category = (int) $idCategory;
        $customCategory->date_precommande = $date_precommande;
        $customCategory->save();
    }

    /**
     * @param array $params
     * @throws \PrestaShop\PrestaShop\Core\Module\Exception\ModuleErrorException
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     */
    public function hookActionAfterUpdateCategoryFormHandler($params)
    {
        $this->updateCategoryCustomData($params);
    }

    /**
     * @param array $params
     * @throws \PrestaShop\PrestaShop\Core\Module\Exception\ModuleErrorException
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     */
    public function hookActionAfterCreateCategoryFormHandler($params)
    {
        $this->updateCategoryCustomData($params);
    }

    /**
     * @param array $params
     * @throws \PrestaShop\PrestaShop\Core\Module\Exception\ModuleErrorException
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     */
    private function updateCategoryCustomData($params)
    {
        $customCategory = new AwCustomCategory((int) $params['form_data']['aw_custom_category_id']);
        $customCategory->id_category = $params['id'];
        $customCategory->date_precommande = $params['form_data']['date_precommande'];
        $customCategory->date_precommande_b = $params['form_data']['date_precommande_b'];
        $customCategory->date_precommande_date = $params['form_data']['date_precommande_date'];
        $customCategory->semaines = $params['form_data']['semaines'];
        try {
            $customCategory->save();
        } catch (Exception $e) {
            throw new \PrestaShop\PrestaShop\Core\Module\Exception\ModuleErrorException($e->getMessage());
        }
    }

    /**
     * @param array $params
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     */
    public function hookActionCategoryFormBuilderModifier($params)
    {
        /** @var \Symfony\Component\Form\FormBuilderInterface $formBuilder */
        $formBuilder = $params['form_builder'];
        $customCategory = AwCustomCategory::getByIdCategory($params['id']);
        $formBuilder->add(
            'date_precommande',
            \Symfony\Component\Form\Extension\Core\Type\TextType::class,
            array('label' => $this->l('Date de précommande avant'), 'required' => false)
        );
		$formBuilder->add(
            'date_precommande_b',
            \Symfony\Component\Form\Extension\Core\Type\TextType::class,
            array('label' => $this->l('Date de précommande après'), 'required' => false)
        );
		$formBuilder->add(
            'date_precommande_date',
            DatePickerType::class,
            array('label' => $this->l('Date de précommande'), 'required' => false)
        );
		$formBuilder->add(
            'semaines',
            \Symfony\Component\Form\Extension\Core\Type\TextareaType::class,
            array('label' => $this->l('Semaines d\'expédition avec séparateur ; (vide si immédiate)'), 'required' => false)
        );
        $formBuilder->add(
            'aw_custom_category_id',
            \Symfony\Component\Form\Extension\Core\Type\HiddenType::class,
            array('data' => $customCategory->id)
        );

        $params['data']['date_precommande'] = $customCategory->date_precommande;
        $params['data']['date_precommande_b'] = $customCategory->date_precommande_b;
        $params['data']['date_precommande_date'] = $customCategory->date_precommande_date;
        $params['data']['semaines'] = $customCategory->semaines;

        $formBuilder->setData($params['data']);
    }
	
	public function hookActionOrderGridDefinitionModifier(array $params)
	{
		/*if (_PS_VERSION_ >= '1.7.7.0') {
			$definition = $params['definition'];
			$definition
				->getColumns()
				->addAfter(
					'osname',
					(new DataColumn('utm_source'))
						->setName($this->l('UTM'))
						->setOptions(
							[
								'field' => 'utm_source'
							]
						)
				);
			$filters = $definition->getFilters();
			$filters->add(
				(new Filter('utm_source', TextType::class))
					->setTypeOptions([
						'required' => false,
						'attr' => [
							'placeholder' => '',
						],
					])
					->setAssociatedColumn('utm_source')
			);
		}*/
	}



}
