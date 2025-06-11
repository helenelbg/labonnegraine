<?php

if (isset($_GET['DEBUG']))
{
    error_reporting(E_ALL ^ E_NOTICE);
    @ini_set('display_errors', 'on');
    @ini_set('log_errors', 'on');
}
else
{
    error_reporting(E_ALL ^ E_NOTICE ^ E_STRICT ^ E_DEPRECATED);
    @ini_set('display_errors', 'off');
}
$cspRules = "
            default-src 'none';
            script-src 'self' 'unsafe-eval' 'unsafe-inline' https://clippingmagic.com/ https://js.intercomcdn.com/ https://widget.intercom.io/;
            style-src 'self' 'unsafe-inline';
            object-src 'none';
            base-uri 'self';
            connect-src 'self' https://api-iam.intercom.io wss://nexus-websocket-a.intercom.io;
            font-src 'self' data:;
            frame-src 'self' *.storecommander.com storecommander.com;
            img-src 'self' data:;
            manifest-src 'self';
            media-src 'self';
            worker-src 'none';
        ";
//header("Content-Security-Policy: ".preg_replace("/[\r\n]*/","",$cspRules));
// Ready! Go!
require 'index2.php';
