<?php
/*

*
*  @author benoit@anjouweb.com
*/

if (!defined('_PS_VERSION_'))
	exit;

class AW_wordpressDBcontent extends Module
{
        /*private $WP_DB_name="c1jardinessai";
        //private $WP_DB_host="localhost";
        private $WP_DB_host="95.142.174.188";
        private $WP_DB_user="c1jardinessai";
        private $WP_DB_pass="59Woi#ee";*/
		private $WP_DB_name="jardinessai";
		private $WP_DB_host="92.243.24.83";
        private $WP_DB_user="jardinessai";
        private $WP_DB_pass="c6A!ahig";
        private $WP_DB_object;

	public function __construct()
	{
		$this->name = 'AW_wordpressDBcontent';
		//$this->tab = 'front_office_features';
		//$this->need_instance = 0;

		//$this->controllers = array('verification');

		//$this->bootstrap = true;
		parent::__construct();

		$this->displayName = $this->l('AW wordpress DB content');
		$this->description = $this->l('Recupere le contenu de la base wordpress');
		$this->confirmUninstall = $this->l('Are you sure that you want to delete all of your contacts?');
		//$this->ps_versions_compliancy = array('min' => '1.6', 'max' => '1.6.99.99');

		//$this->version = '2.3.2';
		$this->author = 'AW';
		$this->error = false;
		$this->valid = false;
		$this->_html = '';
		
		/*$this->_files = array(
			'name' => array('newsletter_conf', 'newsletter_voucher'),
			'ext' => array(
				0 => 'html',
				1 => 'txt'
			)
		);*/

		/*$this->_searched_email = null;

		$this->_html = '';
		if ($this->id)
		{
			$this->file = 'export_'.Configuration::get('PS_NEWSLETTER_RAND').'.csv';
			$this->post_valid = array();

			// Getting data...
			$countries = Country::getCountries($this->context->language->id);

			// ...formatting array
			$countries_list = array($this->l('All countries'));
			foreach ($countries as $country)
				$countries_list[$country['id_country']] = $country['name'];

			// And filling fields to show !
			$this->fields_export = array(
				'COUNTRY' => array(
					'title' => $this->l('Customers\' country'),
					'desc' => $this->l('Filter customers\' country.'),
					'type' => 'select',
					'value' => $countries_list,
					'value_default' => 0
				),
				'SUSCRIBERS' => array(
					'title' => $this->l('Newsletter subscribers'),
					'desc' => $this->l('Filter newsletter subscribers.'),
					'type' => 'select',
					'value' => array(
						0 => $this->l('All customers'),
						2 => $this->l('Subscribers'),
						1 => $this->l('Non-subscribers')
					),
					'value_default' => 2
				),
				'OPTIN' => array(
					'title' => $this->l('Opted-in subscribers'),
					'desc' => $this->l('Filter opted-in subscribers.'),
					'type' => 'select',
					'value' => array(
						0 => $this->l('All customers'),
						2 => $this->l('Subscribers'),
						1 => $this->l('Non-subscribers')
					),
					'value_default' => 0
				),
			);
		}*/
	}

	public function install()
	{
		if (!parent::install()  || !$this->registerHook(array('header', 'footer','leftColumn')))
			return false;

	}

	public function uninstall()
	{
		return parent::uninstall();
	}

	public function getContent()
	{
           /* $this->context->controller->addCSS(($this->_path).'AW_wordpressDBcontent.css', 'all');
			$this->_html = '';
            $this->_html.='<div class="WP_recents_posts modules-WP_recents_posts"><a href="https://www.jardin-essai.com/" target="_blank">';
            $this->_html.='<h2>Retrouvez les derniers articles du blog :</h2>';
            $this->_html.='<img id="logo_WP" src="/modules/AW_wordpressDBcontent/imgs/logo_noir.png" />';
            $this->WP_DB_object=mysqli_connect($this->WP_DB_host, $this->WP_DB_user,$this->WP_DB_pass,$this->WP_DB_name);
           // mysqli_select_db($this->WP_DB_name,$this->WP_DB_object);
            $result_wp_posts = mysqli_query($this->WP_DB_object,"SELECT * FROM AW_recents_posts");
            if($result_wp_posts) {
            $T_wp_posts = mysqli_fetch_assoc($result_wp_posts);
		$this->_html .=utf8_encode($T_wp_posts["content"]);
            }
            $this->_html.='</a></div>';
		return utf8_decode($this->_html);*/
		return '';
	}


	public function hookDisplayRightColumn($params)
	{
		return $this->hookDisplayLeftColumn($params);
	}



	public function hookDisplayLeftColumn($params)
	{
		/*if (!isset($this->prepared) || !$this->prepared)
			$this->_prepareHook($params);*/
		//$this->prepared = true;
		return $this->getContent();
	}

	public function hookFooter($params)
	{
		return $this->hookDisplayLeftColumn($params);
	}

}
