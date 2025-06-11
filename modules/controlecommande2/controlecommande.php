<?php
/*
 * 2016 Anjouweb
 */

if (!defined('_PS_VERSION_'))
    exit;

include_once (dirname(__FILE__) . '/classes/OrderControl.php');
include_once(dirname(__FILE__) . '/classes/OrderProductsControl.php');
include_once(dirname(__FILE__) . '/classes/pdfMultipleRender.php');

class ControleCommande extends Module
{

    protected $error = false;

    const INSTALL_SQL_FILE = 'install/install.sql';

    public $first_fields_verification;
    public $second_fields_verification;
    public $order_state_verified;
    public $order_state_to_control;
    public $id_image_type;
    protected $allowed_field_type = array('isReference', 'isUpc', 'isEan13');

    public function __construct()
    {

        $this->name = 'controlecommande';
        $this->tab = 'administration';
        $this->version = '1.1';
        $this->author = 'Anjouweb';
        $this->need_instance = 0;
//        $this->table = 'order_control';
        $this->displayName = $this->l('Contrôle des commandes');
        $this->description = $this->l('Permet de controler la préparation des commandes');

        $this->context = Context::getContext();
        parent::__construct();

        $this->getConfigValues();
    }

    public function install()
    {
        if (parent::install() != false
        && $this->installTab()
        && $this->installSql()
        && $this->createHookPDFDeliverySlip()
        && $this->registerHook('displayBackOfficeHeader')
        && $this->registerHook('displayPDFDeliverySlip'))
        {
            $state = new OrderState();
            foreach (Language::getLanguages() as $language)
            {
                $state->name[$language['id_lang']] = "Prepared ";
            }
            $state->send_email = false;
            $state->invoice = false;
            $state->logable = true;
            $state->color = '#935a00';
            $state->shipped = false;
            $state->unremovable = false;
            $state->delivery = false;
            $state->hidden = true;
            $state->paid = false;
            $state->deleted = false;
            $state->add();
            Configuration::updateValue('CONTROL_ORDER_STATE_VERIFIED', $state->id);

            return true;
        }
        else
        {
            return false;
        }
    }

    public function createHookPDFDeliverySlip()
    {
        $exist = Hook::getIdByName('displayPDFDeliverySlip');
        if(!$exist)
        {

            $hook = new Hook();
            $hook->name = 'PDFDeliverySlip';
            $hook->title = 'PDF Delivery Slip';
            $hook->description = 'PDF Delivery Slip';
            $hook->position = 1;
            $hook->live_edit = false;
            if($hook->add())
            {
                return true;
            }
            else
            {
                return false;
            }
        }
        else
        {
            return true;
        }
    }
    public function hookdisplayPDFDeliverySlip($params)
    {
        $order = new Order($params['object']->id_order);
		/*$black = [0,0,0];
		$white = [255,255,255];
		$stuff = ['position'=>'S', 'border'=>false, 'padding'=>4, 'fgcolor'=>$black, 'bgcolor'=>$white, 'text'=>false, 'font'=>'helvetica', 'fontsize'=>8, 'stretchtext'=>4];
		$mytcpdf = new TCPDF();
		$mytcpdf->serializeTCPDFtagParameters(array($order->id, 'C39', '', '', 55, 20, 0.4, $stuff, 'N'));
        $this->context->smarty->assign(array(
					'order'=>$order,
					'params'=>$params,
//                                        'uniqueReference' => $order->getUniqReference(),
				));
        return $this->display(__FILE__, 'hook_pdf.tpl');*/
        include_once(dirname(__FILE__) . '/classes/barcode.php');

        $_GET['filepath'] = $order->reference.'.png';
        $_GET['text'] = $order->reference;

        // For demonstration purposes, get pararameters that are passed in through $_GET or set to the default value
        $filepath = dirname(__FILE__).'/barcode/'.$order->reference.'.png';
        $text = $order->reference;
        $size = "20";
        $orientation = "horizontal";
        $code_type = "code128";
        $print = false;
        $sizefactor = "1";

        // This function call can be copied into your project and can be made from anywhere in your code
        barcode( $filepath, $text, $size, $orientation, $code_type, $print, $sizefactor );


        $this->context->smarty->assign(array(
                    'order'=>$order
                  ));
        return $this->display(__file__, 'hook_pdf.tpl');
    }

     public function hookdisplayBackOfficeHeader()
     {
         if($this->context->controller->className == 'AdminControleCommandeController')
         {
            $this->context->controller->addJquery();
            $this->context->controller->addCSS(($this->_path).'views/css/controlecommande.css', 'all');
            //$this->context->controller->addCSS(($this->_path).'views/css/jquery.fancybox.css', 'all');
            //$this->context->controller->addCSS(($this->_path).'views/css/bootstrap.min.css', 'all');
            //$this->context->controller->addCSS(($this->_path).'views/css/adapt.css', 'all');
            $this->context->controller->addJS(($this->_path).'views/js/jquery.fancybox.js');
            $this->context->controller->addJS(($this->_path).'views/js/jquery.tablesorter.js');
            $this->context->controller->addJS(($this->_path).'views/js/jquery.tablesorter.widgets.js');
            $this->context->controller->addJS(($this->_path).'views/js/custom2.js');
         }
     }
    private function installTab()
    {
        $tab = new Tab();
        foreach (Language::getLanguages(false) as $language)
            $tab->name[$language['id_lang']] = 'Contrôle des commandes';


        $tab->class_name = 'AdminControleCommande';
        $tab->module = $this->name;
        $tab->id_parent = Tab::getIdFromClassName('AdminParentOrders'); // ParentOrders

        if (!$tab->save())
        {
            $this->_errors[] = $this->l('Erreur à la creation du menu');
            return false;
        }
        else
        {
            return true;
        }
    }

    private function installSql()
    {
        if (!file_exists(dirname(__FILE__) . '/' . self::INSTALL_SQL_FILE))
            return (false);
        else if (!$sql = file_get_contents(dirname(__FILE__) . '/' . self::INSTALL_SQL_FILE))
            return (false);
        $sql = str_replace(array('PREFIX_', 'ENGINE_TYPE'), array(_DB_PREFIX_, _MYSQL_ENGINE_), $sql);
        $sql = preg_split("/;\s*[\r\n]+/", $sql);
        foreach ($sql as $query)
            if ($query)
                if (!Db::getInstance()->execute(trim($query)))
                    return false;

        return true;
    }

    public function uninstall()
    {
        $id_tab = tab::getIdFromClassName('AdminControleCommande');
        $tab = new Tab($id_tab);
        $tab->delete();
        if (parent::uninstall() && $tab->delete() &&
                Db::getInstance()->Execute('DROP TABLE `' . _DB_PREFIX_ . 'order_products_control`') &&
                Db::getInstance()->Execute('DROP TABLE `' . _DB_PREFIX_ . 'order_control`'))
        {
            return true;
        }
    }

    public function getContent()
    {
        $output = null;

        if (Tools::isSubmit('submit' . $this->name))
        {
            Configuration::updateValue('CONTROL_STATE_TOCONTROL', Tools::getValue('CONTROL_STATE_TOCONTROL'));
            Configuration::updateValue('CONTROL_ORDER_STATE', Tools::getValue('CONTROL_ORDER_STATE'));
            Configuration::updateValue('CONTROL_FIRST_FIELD', Tools::getValue('CONTROL_FIRST_FIELD'));
            Configuration::updateValue('CONTROL_SECOND_FIELD', Tools::getValue('CONTROL_SECOND_FIELD'));
            Configuration::updateValue('CONTROL_IMAGETYPE', Tools::getValue('CONTROL_IMAGETYPE'));

            $output .= $this->displayConfirmation($this->l('Settings are updated'));
        }
        $output .= $this->displayForm().$this->displayInformation2();
        return $output;
    }

    public function displayForm()
    {
        foreach (Product::$definition['fields'] as $fieldname => $fieldsinfos)
        {
            if (isset($fieldsinfos['validate']) && in_array($fieldsinfos['validate'], $this->allowed_field_type))
            {
                $products_fields[] = array('key' => $fieldname, 'name' => $fieldname);
            }
        }
        // Get default Language
        $default_lang = (int) Configuration::get('PS_LANG_DEFAULT');
        $fields_form[0]['form'] = array(
            'legend' => array(
                'title' => $this->l('Settings'),
            ),
            'input' => array(
                array(
                    'type' => 'select',
                    'label' => $this->l('The Order state to control'),
                    'name' => 'CONTROL_STATE_TOCONTROL',
                    'options' => array
                        (
                        'query' => OrderState::getOrderStates(Context::getContext()->language->id),
                        'id' => 'id_order_state',
                        'name' => 'name',
                    ),
                    'required' => true
                ),
                array(
                    'type' => 'select',
                    'label' => $this->l('The Order state after verification'),
                    'name' => 'CONTROL_ORDER_STATE',
                    'options' => array
                        (
                        'query' => OrderState::getOrderStates(Context::getContext()->language->id),
                        'id' => 'id_order_state',
                        'name' => 'name',
                    ),
                    'required' => true
                ),
                array(
                    'type' => 'select',
                    'label' => $this->l('First Verification field'),
                    'name' => 'CONTROL_FIRST_FIELD',
                    'options' => array
                        (
                        'query' => $products_fields,
                        'id' => 'key',
                        'name' => 'name',
                    ),
                    'required' => true
                ),
                array(
                    'type' => 'select',
                    'label' => $this->l('Second Verification fields(if first not find)'),
                    'name' => 'CONTROL_SECOND_FIELD',
                    'options' => array
                        (
                        'query' => $products_fields,
                        'id' => 'key',
                        'name' => 'name',
                    ),
                    'required' => true
                ),
                array(
                    'type' => 'select',
                    'label' => $this->l('Select the type of image'),
                    'name' => 'CONTROL_IMAGETYPE',
                    'options' => array
                        (
                        'query' => ImageType::getImagesTypes('products'),
                        'id' => 'id_image_type',
                        'name' => 'name',
                    ),
                    'required' => true
                )
            ),
            'submit' => array(
                'title' => $this->l('Save'),
                'class' => 'button'
        ));

        $helper = new HelperForm();
        // Module, token and currentIndex
        $helper->module = $this;
        $helper->name_controller = $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->currentIndex = AdminController::$currentIndex . '&configure=' . $this->name;

        // Language
        $helper->default_form_language = $default_lang;
        $helper->allow_employee_form_lang = $default_lang;

        // Title and toolbar
        $helper->title = $this->displayName;
        $helper->show_toolbar = true;        // false -> remove toolbar
        $helper->toolbar_scroll = true;      // yes - > Toolbar is always visible on the top of the screen.
        $helper->submit_action = 'submit' . $this->name;
        $helper->toolbar_btn = array(
            'save' =>
            array(
                'desc' => $this->l('Save'),
                'href' => AdminController::$currentIndex . '&configure=' . $this->name . '&save' . $this->name .
                '&token=' . Tools::getAdminTokenLite('AdminModules'),
            ),
            'back' => array(
                'href' => AdminController::$currentIndex . '&token=' . Tools::getAdminTokenLite('AdminModules'),
                'desc' => $this->l('Back to list')
            )
        );

        // Load current value
        $this->getConfigValues();
        $helper->fields_value['CONTROL_STATE_TOCONTROL'] = $this->order_state_to_control;
        $helper->fields_value['CONTROL_ORDER_STATE'] = $this->order_state_verified;
        $helper->fields_value['CONTROL_FIRST_FIELD'] = $this->first_fields_verification;
        $helper->fields_value['CONTROL_SECOND_FIELD'] = $this->second_fields_verification;
        $helper->fields_value['CONTROL_IMAGETYPE'] = $this->id_image_type;

        return $helper->generateForm($fields_form);
    }

    public function getConfigValues()
    {
        $this->first_fields_verification = Configuration::get('CONTROL_FIRST_FIELD');
        $this->second_fields_verification = Configuration::get('CONTROL_SECOND_FIELD');
        $this->order_state_verified = Configuration::get('CONTROL_ORDER_STATE');
        $this->order_state_to_control = Configuration::get('CONTROL_STATE_TOCONTROL');
        $this->id_image_type = Configuration::get('CONTROL_IMAGETYPE');
    }

//   public function viewAccess($disable = false)
//    {
//            if ($disable)
//                    return true;
//
//            if ($this->tabAccess['view'] === '1')
//                    return true;
//            return false;
//    }

    public function initContent()
    {
        if (!$this->viewAccess())
        {
            $this->errors[] = Tools::displayError('You do not have permission to view this.');
            return;
        }

        $this->initToolbar();
        $this->initPageHeaderToolbar();
    }

   function displayInformation2()
   {
       $html = "<br/><fieldset>"
               . "<legend>".$this->l("Generation etiquette transport")."</legend>"
               . "<div >"
               . $this->l("Vous pouvez gerer l'impression d'etiquette transport a la validation du controle.").''
                . $this->l('Pour cela il faut surcharger la classe order et y ajouter la fonction static printPDFIcons($id_order), vous trouverez un exemple ci-dessous').'<br/><b>'
                . $this->l("Veillez aussi a bien adapter votre statut de commande une fois le controle effectue pour permettre la generation d'etiquette selon vos transporteur.").'</b>'
               . '<pre style="background-color: white;">'
               .'

// TO PUT IN ORDER CLASSE OVERRIDE
public static function printPDFIcons($id_order)
	{
                $order = new Order($id_order);
		$orderState = OrderHistory::getLastOrderState($id_order);

                if (!Validate::isLoadedObject($orderState) OR !Validate::isLoadedObject($order))
			die(Tools::displayError(\'Invalid objects\'));

		echo \'< span style="width:20px; margin-right:5px;">\';

		if (($orderState->invoice AND $order->invoice_number))
			echo \'< a href="pdf.php?id_order=\'.(int)($order->id).\'&pdf">< img src="../img/admin/tab-invoice.gif" alt="invoice" />< /a>\';
		else
			echo \'&nbsp;\';
		echo \'< /span>\';
		echo \'< span style="width:20px;">\';

		if ($orderState->delivery AND $order->delivery_number)
			echo \'< a href="pdf.php?id_delivery=\'.(int)($order->delivery_number).\'">< img src="../img/admin/delivery.gif" alt="delivery" />< /a>\';
		else echo \'&nbsp;\';
		echo \'< /span>\';


		// Chronopost
                if ($orderState->delivery AND $order->delivery_number AND (
                    $order->id_carrier==Configuration::get(\'CHRONOPOST_CARRIER_ID\')
                    or $order->id_carrier==Configuration::get(\'CHRONORELAIS_CARRIER_ID\')
                    or $order->id_carrier==Configuration::get(\'CHRONOEXPRESS_CARRIER_ID\')
		))
		{
			echo \'< a href="../modules/chronopost/postSkybill.php?orderid=\'.(int)($order->id).\'&shared_secret=\'
				.Configuration::get(\'CHRONOPOST_SECRET\').\'" title="Imprimer la lettre de transport" id="label_\'.$order->id.\'_\'.$order->id_carrier.\'" target="_blank">
				< img src="../modules/chronopost/logo.gif" alt="Chronopost" title="Chronopost">
				< /a>\';
		}
		else echo \'&nbsp;\';
		echo \'< /span>\';

    }'
               . '</pre>'
               . '</div>'
            . '</fieldset>';

       return $html;
   }
}
