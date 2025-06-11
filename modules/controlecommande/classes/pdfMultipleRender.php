<?php


class pdfMultipleRender extends PDF
{
    
    public function render($display = true, $pre_path = '')
    {
        $render = false;
        $this->pdf_renderer->setFontForLang(Context::getContext()->language->iso_code);
        $nbpdf = count($this->objects);
        foreach ($this->objects as $object)
        {
            $order = new Order($object->id_order);
            $this->filename = $order->reference.'.pdf';
            $template = $this->getTemplateObject($object); 
            if (!$template)
                continue;

            $template->assignHookData($object);

            $this->pdf_renderer->createHeader($template->getHeader());
            $this->pdf_renderer->createFooter($template->getFooter());
            $this->pdf_renderer->createContent($template->getContent());
            $this->pdf_renderer->writePage();
            $render = true;

            unset($template);
        }
        if ($render)
        {
            // clean the output buffer
            if (ob_get_level() && ob_get_length() > 0)
                ob_clean();
            return $this->pdf_renderer->render($this->filename, $display);
        }
    }

}