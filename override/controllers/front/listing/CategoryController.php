<?php

class CategoryController extends CategoryControllerCore
{

    public function getListingLabel()
    {
        if (!Validate::isLoadedObject($this->category)) {
            $this->category = new Category(
                (int) Tools::getValue('id_category'),
                $this->context->language->id
            );
        }
 
        $labelEC = $this->category->name;
        $voyelles = array('a', 'e', 'i', 'o', 'u', 'y', 'A', 'E', 'I', 'O', 'U', 'Y');
        if ( in_array( substr($this->category->name, 0, 1), $voyelles ) )
        {
            $sepa = " d'";
        }
        else 
        {
            $sepa = " de ";
        }
        if ( $this->category->id != 466 && $this->category->id_parent == 344 )
        {
            $labelEC = 'Plants'.$sepa.$labelEC;
        }
        elseif ( $this->category->id != 20 && $this->category->id != 360 && ($this->category->id_parent == 18 || $this->category->id_parent == 233 || $this->category->id_parent == 442 || $this->category->id_parent == 443 || $this->category->id_parent == 444 || $this->category->id_parent == 261 || $this->category->id_parent == 465) )
        {
            $labelEC = 'Graines'.$sepa.$labelEC;
        }
        elseif ( $this->category->id == 360 && $this->category->id_parent == 18 )
        {
            $labelEC = 'Graines du '.str_replace('Le ', '', $labelEC);
        }
        elseif ( $this->category->id == 231 || $this->category->id == 248 )
        {
            $labelEC = 'Plants de '.$labelEC;
        }
        elseif ( $this->category->id == 135 || $this->category->id == 132 || $this->category->id == 131 || $this->category->id == 133 || $this->category->id == 134 || $this->category->id == 213 )
        {
            $labelEC = $labelEC.' en racines nues';
        }
        elseif ( $this->category->id == 299 || $this->category->id == 338 )
        {
            $labelEC = $labelEC.' en mottes compressées';
        }

        return $this->trans(
            '%category_name%',
            ['%category_name%' => $labelEC],
            'Shop.Theme.Catalog'
        );
    }
	
	public function initContent() {

        CategoryControllerCore::initContent();
	
         // template different si catégorie 227 "les box"
		if (isset($this->category) && $this->category->id==227) {
			$this->setTemplate('category-box.tpl');
		}
    }
	
}
