<?php

$url = $_POST['url'];
if(strlen($url) > 5){
	$url_douane = str_replace('.prn', '_CN23.pdf', $url);
	$pdf = explode('download/', $url_douane);
	if(file_exists('modules/colissimo/documents/cn23/'.$pdf[1])){
		echo $url_douane;
	}else{
		echo 'notok';
	}
}
