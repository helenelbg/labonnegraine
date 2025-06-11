<?php

header('Content-type: application/pdf');

if(isset($_GET["name"])){
    @readfile($_GET["name"]);
}
