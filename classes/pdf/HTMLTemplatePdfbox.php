<?php
class HTMLTemplatePdfbox extends HTMLTemplate
{
	public $tableau; 
 
	public function __construct($datas, $smarty)
	{
		$this->tableau = $datas->tableau; 
		$this->smarty = $smarty; 
 
		// header informations
		$id_lang = Context::getContext()->language->id;
		$this->title = HTMLTemplatePdfbox::l($datas->titre);
		// footer informations
		//$this->shop = new Shop(Context::getContext()->shop->id);
	}
 
	/**
	 * Returns the template's HTML content
	 * @return string HTML content
	 */
	public function getContent()
	{
		$this->smarty->assign(array(
			'tableau' => $this->tableau,
		));
 
		//return $this->smarty->fetch('pdfbox.tpl');
                return " "; 
	}
 
/*	public function getLogo()
	{
		$this->smarty->assign(array(
			'custom_model' => $this->custom_model,
		));
 
		return $this->smarty->fetch(_PS_MODULE_DIR_ . 'my_module/custom_template_logo.tpl');
	}
 */
	/*public function getHeader()
	{
		$this->smarty->assign(array(
			'custom_model' => $this->custom_model,
		));
 
		return $this->smarty->fetch(_PS_MODULE_DIR_ . 'my_module/custom_template_header.tpl');
	}*/
 
	/**
	 * Returns the template filename
	 * @return string filename
	 */
	/*public function getFooter()
	{
		return $this->smarty->fetch(_PS_MODULE_DIR_ . 'my_module/custom_template_footer.tpl');
	}*/
 
	/**
	 * Returns the template filename
	 * @return string filename
	 */
	public function getFilename()
	{
		return 'custom_pdf.pdf';
	}
 
	/**
	 * Returns the template filename when using bulk rendering
	 * @return string filename
	 */
	public function getBulkFilename()
	{
		return 'custom_pdf.pdf';
	}
}
?>