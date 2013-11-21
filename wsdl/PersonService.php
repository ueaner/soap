<?php
/**
 * PHP SoapServer: non-WSDL mode
 * @author ueaner <ueaner@gmail.com> www.aboutc.net
 */

include '../Service.php';

$className = rtrim(pathinfo(__FILE__, PATHINFO_FILENAME), 'Service');

$mode = pathinfo(__DIR__, PATHINFO_FILENAME);

$service = new Service($mode, $className);
$service->run();