<?php
/*
* 2007-2014 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Open Software License (OSL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/osl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2014 PrestaShop SA
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

include_once(_PS_TOOL_DIR_.'tcpdf/config/lang/eng.php');
include_once(_PS_TOOL_DIR_.'tcpdf/tcpdf.php');

/**
 * @since 1.5
 */
class PDFGenerator_paysage extends TCPDF
{
	const DEFAULT_FONT = 'helvetica';
        public $tableau;
	public $header;
	public $footer;
	public $content;
	public $font;

	public $font_by_lang = array(
		'ja' => 'cid0jp', 
		'bg' => 'freeserif', 
		'ru' => 'freeserif', 
		'uk' => 'freeserif', 
		'mk' => 'freeserif', 
		'el' => 'freeserif', 
		'en' => 'dejavusans',		
		'vn' => 'dejavusans', 
		'pl' => 'dejavusans',
		'ar' => 'dejavusans',
		'fa' => 'dejavusans',
		'ur' => 'dejavusans',
		'az' => 'dejavusans',
		'ca' => 'dejavusans',
		'gl' => 'dejavusans',
		'hr' => 'dejavusans',
		'sr' => 'dejavusans',
		'si' => 'dejavusans',
		'cs' => 'dejavusans',
		'sk' => 'dejavusans',
		'ka' => 'dejavusans',
		'he' => 'dejavusans',
		'lo' => 'dejavusans',
		'lv' => 'dejavusans',
		'tr' => 'dejavusans',
		'ko' => 'cid0kr',
		'zh' => 'cid0cs',
		'tw' => 'cid0cs',
		'th' => 'freeserif'
		);


	public function __construct($object,$use_cache = false)
	{
            //var_dump($object);
            //echo "dddd";
            //die();
            $this->tableau=$object->tableau;
		parent::__construct('L', 'mm', 'A4', true, 'UTF-8', $use_cache, false);
		$this->setRTL(Context::getContext()->language->is_rtl);
	}

	/**
	 * set the PDF encoding
	 * @param string $encoding
	 */
	public function setEncoding($encoding)
	{
		$this->encoding = $encoding;
	}

	/**
	 *
	 * set the PDF header
	 * @param string $header HTML
	 */
	public function createHeader($header)
	{
		$this->header = $header;
	}

	/**
	 *
	 * set the PDF footer
	 * @param string $footer HTML
	 */
	public function createFooter($footer)
	{
		$this->footer = $footer;
	}

	/**
	 *
	 * create the PDF content
	 * @param string $content HTML
	 */
	public function createContent($content)
	{
		$this->content = $content;
	}

	/**
	 * Change the font
	 * @param string $iso_lang
	 */
	public function setFontForLang($iso_lang)
	{
		$this->font = PDFGenerator::DEFAULT_FONT;
		if (array_key_exists($iso_lang, $this->font_by_lang))
			$this->font = $this->font_by_lang[$iso_lang];

		$this->setHeaderFont(array($this->font, '', PDF_FONT_SIZE_MAIN));
		$this->setFooterFont(array($this->font, '', PDF_FONT_SIZE_MAIN));

		$this->setFont($this->font);
	}

	/**
	 * @see TCPDF::Header()
	 */
	public function Header()
	{
                    // get the current page break margin
                    $bMargin = $this->getBreakMargin();
                    // get current auto-page-break mode
                    $auto_page_break = $this->AutoPageBreak;
                    // disable auto-page-break
                    $this->SetAutoPageBreak(false, 0);
                    // set bacground image
                    $img_file = dirname(__FILE__).'/../../message_box_dim.jpg'; 
                    $this->Image($img_file, 10, 20, 277, 0, '', '', '', false, 300, '', false, false, 0);
                    // $this->setJPEGQuality(75);
                   // $img_file = 'message_box.jpg';
                   //  $this->Image($img_file, 10, 20, 277, 100, 'JPG', '', '', true, 300, '', false, false, 0, false, false,false);
                    // restore auto-page-break status
                    $this->SetAutoPageBreak($auto_page_break, $bMargin);
                    // set the starting point for the page content
                    $this->setPageMark();
		//$this->writeHTML($this->header);
	}

	/**
	 * @see TCPDF::Footer()
	 */
	public function Footer()
	{
		//$this->writeHTML($this->footer);
	}

	/**
	 * Render the pdf file
	 *
	 * @param string $filename
         * @param  $display :  true:display to user, false:save, 'I','D','S' as fpdf display
	 * @throws PrestaShopException
	 */
	public function render($filename, $display = false)
	{
            //var_dump($filename);
		if (empty($filename))
			throw new PrestaShopException('Missing filename.');

		$this->lastPage();

		if ($display === true)
			$output = 'D';
		elseif ($display === false)
			$output = 'S';
		elseif ($display == 'D')
			$output = 'D';
		elseif ($display == 'S')
			$output = 'S';
		else 	
			$output = 'I';
			
		return $this->output($filename, false);
	}

	/**
	 * Write a PDF page
	 */
	public function writePage()
	{
		$this->SetHeaderMargin(5);
		$this->SetFooterMargin(18);
		$this->setMargins(10, 10, 10);

		$this->AddPage();                    
                // var_dump( $this->tableau);
                //echo "aaaa";
                $this->SetFillColor(255,255,255); // Grey
                $this->SetY(67);
                $this->SetX(120);
                $this->Cell(120,10,"",'L',0,'L',1,'',0,false,'T','C');
                
                $this->SetY(79);
                $this->SetX(120);
                $this->Cell(120,10,"",'L',0,'L',1,'',0,false,'T','C');
                
                $this->SetY(91);
                $this->SetX(120);
                $this->Cell(120,10,"",'L',0,'L',1,'',0,false,'T','C');
                
                
                $this->SetY(114);
                 $this->SetX(115);
                $this->Cell(15,11,"",'L',0,'L',1,'',0,false,'T','C');
                
                 $this->SetY(111);
                 $this->SetX(155);
                $this->Cell(117,18,"",'L',0,'L',1,'',0,false,'T','C');
                 
                 
                $this->SetFont('helvetica', '', 12);
                $this->TextField('test', 120, 10, array(), array('v' => " ".$this->tableau[0][0]), 120, 67);                                                

                $this->TextField('test1', 120, 10, array(), array('v' => " ".$this->tableau[0][1]), 120, 79);                

                $this->TextField('test2', 120, 10, array(), array('v' => " ".$this->tableau[0][2]), 120, 91);
                
                
                //$this->Cell('test3', 15, 11, array(), array('v' => "     ".$this->tableau[0][3]), 115, 114);
                  $this->SetY(120);
                 $this->SetX(115);
                $this->Cell( 15, 11, $this->tableau[0][3], 0, $ln=0, 'C', 0, '', 0, false, 'C', 'C');
                
                $this->TextField('test4', 117, 18,array('multiline'=>true), array('v' => ''), 155, 111);
                //

                
		//$this->writeHTML($this->content, true, false, true, false, '');
	}
	
	/**
	 * Override of TCPDF::getRandomSeed() - getmypid() is blocked on several hosting
	*/
	protected function getRandomSeed($seed='') 
	{
		$seed .= microtime();
		if (function_exists('openssl_random_pseudo_bytes') AND (strtoupper(substr(PHP_OS, 0, 3)) !== 'WIN')) {
			// this is not used on windows systems because it is very slow for a know bug
			$seed .= openssl_random_pseudo_bytes(512);
		} else {
			for ($i = 0; $i < 23; ++$i) {
				$seed .= uniqid('', true);
			}
		}
		$seed .= uniqid('', true);
		$seed .= rand();
		$seed .= __FILE__;
		$seed .= $this->bufferlen;
		if (isset($_SERVER['REMOTE_ADDR'])) {
			$seed .= $_SERVER['REMOTE_ADDR'];
		}
		if (isset($_SERVER['HTTP_USER_AGENT'])) {
			$seed .= $_SERVER['HTTP_USER_AGENT'];
		}
		if (isset($_SERVER['HTTP_ACCEPT'])) {
			$seed .= $_SERVER['HTTP_ACCEPT'];
		}
		if (isset($_SERVER['HTTP_ACCEPT_ENCODING'])) {
			$seed .= $_SERVER['HTTP_ACCEPT_ENCODING'];
		}
		if (isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
			$seed .= $_SERVER['HTTP_ACCEPT_LANGUAGE'];
		}
		if (isset($_SERVER['HTTP_ACCEPT_CHARSET'])) {
			$seed .= $_SERVER['HTTP_ACCEPT_CHARSET'];
		}
		$seed .= rand();
		$seed .= uniqid('', true);
		$seed .= microtime();
		return $seed;
	}
}
