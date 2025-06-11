<?php
include('../../config/config.inc.php');
include('../../init.php');

echo '<br><br>Product::$definition  <br> [ <br><pre>';
print_r(Product::$definition['fields']);
echo '</pre><br>]<br>';

