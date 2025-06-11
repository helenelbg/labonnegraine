<?php

$grids = 'id_image,image,id_product,name,reference,legend,position,cover,width,height';
if (SCMS)
{
    $grids = 'id_image,image,id_product,name,reference,legend,position,cover,_SHOPS_,width,height';
}
