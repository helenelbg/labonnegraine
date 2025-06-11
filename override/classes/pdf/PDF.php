<?php
class PDF extends PDFCore
{
	/**
     * Render PDF.
     *
     * @param bool $display
     *
     * @return string|void
     *
     * @throws PrestaShopException
     */
    public function render($display = true)
    {
        $render = false;
        $this->pdf_renderer->setFontForLang(Context::getContext()->language->iso_code);
        foreach ($this->objects as $object) {
            $this->pdf_renderer->startPageGroup();
            $template = $this->getTemplateObject($object);
            if (!$template) {
                continue;
            }

            $template->assignHookData($object);

            $this->pdf_renderer->createHeader($template->getHeader());
            $this->pdf_renderer->createPagination($template->getPagination());
            $this->pdf_renderer->createContent($template->getContent());
			if($this->template == "DeliverySlip"){
				$this->pdf_renderer->writePageDeliverySlip();
			}else{
				$this->pdf_renderer->writePage();
			}
			
            // The footer must be added after adding the page, or TCPDF will
            // add the footer for the next page from on the last page of this
            // page group, which could mean the wrong store info is rendered.
            $this->pdf_renderer->createFooter($template->getFooter());
            $render = true;

            unset($template);
        }

        if ($render) {
            // clean the output buffer
            if (ob_get_level() && ob_get_length() > 0) {
                ob_clean();
            }

            return $this->pdf_renderer->render($this->getFilename(), $display);
        }
    }
}
