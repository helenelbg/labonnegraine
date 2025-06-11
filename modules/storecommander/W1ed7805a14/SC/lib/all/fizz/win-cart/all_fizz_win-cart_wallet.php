<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
    <title>Store Commander</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <link rel="stylesheet" href="lib/css/style.css">
    <style>
        body {
            line-height: 27px;
            font-weight: normal;
            font-family: Tahoma;
            font-size: 12px;
            color: #000000;
        }
    </style>
</head>
<body>
<img src="lib/img/fizz_big.png" alt="Fizz" title="Fizz" width="60px" style="float: left; margin-right: 10px;"/>
<center><?php echo _l('Your wallet'); ?><br/>
<strong style="font-size: 20px;"><?php
    $amount = getWallet();
    if (empty($amount))
    {
        $amount = 0;
    }
    $amount = floor($amount);
    echo $amount;
?> Fizz</strong></center>
</body>
</html>