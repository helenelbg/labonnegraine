<?php
/*
StoreCommander
*/
 class ExternHallAutoUpdate { private $params; public function __construct($payloadParams = null) { $this->params = $payloadParams; } public function doProcess() { doScUpdate(); } }
