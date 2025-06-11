<?php $url = (Tools::getValue('url', ''));
if (empty($url))
{
    exit();
}
$dir = _PS_IMG_.'/banner/';
?><!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>SC - Affiliation</title>
</head>
<body>
<center><img src="<?php echo $dir.$url; ?>" /></center>
</body>
</html>