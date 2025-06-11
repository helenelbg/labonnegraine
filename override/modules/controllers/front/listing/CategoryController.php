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

        return $this->trans(
            '%category_name%',
            ['%category_name%' => $this->category->name],
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
