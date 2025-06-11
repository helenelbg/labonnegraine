<?php

$id_image_cutout = Tools::getValue('id_image_cutout');
$id_image = Tools::getValue('id_image');

$return = 'KO';
if (!empty($id_image_cutout) && !empty($id_image))
{
    CutOut::payment(1);
    $return = CutOut::download($id_image_cutout, $id_image);
    if (empty($return))
    {
        $return = 'KO';
    }
    else
    {
        $return = 'OK';
    }
}
echo $return;
