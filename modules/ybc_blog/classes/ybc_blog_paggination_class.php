<?php
/**
 * Copyright ETS Software Technology Co., Ltd
 *
 * NOTICE OF LICENSE
 *
 * This file is not open source! Each license that you purchased is only available for 1 website only.
 * If you want to use this file on more websites (or projects), you need to purchase additional licenses.
 * You are not allowed to redistribute, resell, lease, license, sub-license or offer our resources to any third party.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future.
 *
 * @author ETS Software Technology Co., Ltd
 * @copyright  ETS Software Technology Co., Ltd
 * @license    Valid for 1 website (or project) for each purchase of license
 */

if (!defined('_PS_VERSION_')) { exit; }
class Ybc_blog_paggination_class {
	public $total = 0;
	public $page = 1;
	public $limit = 20;
	public $num_links = 10;
	public $url = '';
	public $text = 'Showing {start} to {end} of {total} ({pages} Pages)';
	public $text_first;
	public $text_last;
	public $text_next;
	public $text_prev;
	public $style_links = 'links';
	public $style_results = 'results';
    public $alias;
    public $friendly;
    public $name;
    public function __construct()
    {
        $this->alias = Configuration::get('YBC_BLOG_ALIAS');
        $this->friendly = (int)Configuration::get('YBC_BLOG_FRIENDLY_URL') && (int)Configuration::get('PS_REWRITING_SETTINGS') ? true : false;
        /** @var Ybc_blog $module */
        $module = Module::getInstanceByName('ybc_blog');
        $this->text_first = $module->displayText('|&lt;','span');
        $this->text_last = $module->displayText('&gt;|','span');
        $this->text_next = $module->displayText('&gt;','span');
        $this->text_prev = $module->displayText('&lt;','span');
    }
	public function render() {
        if($this->limit!=20 && Tools::isSubmit('paginator_'.$this->name.'_select_limit'))
            $this->url .= '&paginator_'.$this->name.'_select_limit='.$this->limit;
		$total = $this->total;
		/** @var Ybc_blog $module */
		$module = Module::getInstanceByName('ybc_blog');
		if($total<=1)
            return false;
		if ($this->page < 1) {
			$page = 1;
		} else {
			$page = $this->page;
		}
		
		if (!(int)$this->limit) {
			$limit = 10;
		} else {
			$limit = $this->limit;
		}
		
		$num_links = $this->num_links;
		$num_pages = ceil($total / $limit);
		
		$output = '';
		
		if ($page > 1) {
			$output .= $module->displayText($this->text_first,'a','frist',null,$this->replacePage(1)).$module->displayText($this->text_prev,'a','prev',null,$this->replacePage($page-1));
    	}

		if ($num_pages > 1) {
			if ($num_pages <= $num_links) {
				$start = 1;
				$end = $num_pages;
			} else {
				$start = $page - floor($num_links / 2);
				$end = $page + floor($num_links / 2);
			
				if ($start < 1) {
					$end += abs($start) + 1;
					$start = 1;
				}
						
				if ($end > $num_pages) {
					$start -= ($end - $num_pages);
					$end = $num_pages;
				}
			}

			if ($start > 1) {
				$output .= ' .... ';
			}

			for ($i = $start; $i <= $end; $i++) {
				if ($page == $i) {
					$output .= $module->displayText($i,'b');
				} else {
					$output .= $module->displayText($i,'a',null,null,$this->replacePage($i));
				}	
			}
							
			if ($end < $num_pages) {
				$output .= ' .... ';
			}
		}
		
   		if ($page < $num_pages) {
			$output .= $module->displayText($this->text_next,'a','next',null,$this->replacePage($page+1)). $module->displayText($this->text_last,'a','last',null,$this->replacePage($num_pages));
		}
		
		$find = array(
			'{start}',
			'{end}',
			'{total}',
			'{pages}'
		);
		
		$replace = array(
			($total) ? (($page - 1) * $limit) + 1 : 0,
			((($page - 1) * $limit) > ($total - $limit)) ? $total : ((($page - 1) * $limit) + $limit),
			$total, 
			$num_pages
		);
		if($num_pages==1)
            $this->text = $this->l('Showing {start} to {end} of {total} ({pages} Page)');
		return ($output ? $module->displayText($output,'dev','links') : '') . $module->displayText(str_replace($find, $replace, $this->text),'div',$this->style_results).$module->displayPaggination($limit,$this->name);
	}
    public function replacePage($page)
    {
        return str_replace('_page_', $page, $this->url);
    }
    public function l($string)
    {
        return Translate::getModuleTranslation('ybc_blog', $string, pathinfo(__FILE__, PATHINFO_FILENAME));
    }
}
?>