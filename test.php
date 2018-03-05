<?php
/**
 * PHP SOAP Web-service 测试文件
 * @author ueaner <ueaner@gmail.com> www.aboutc.net
 */

include 'Client.php';

$mode = 'wsdl'; // or non-wsdl

$params = array(
    'serverIP' => '127.0.0.1',
    'serverPort' => '80',
    'mode' => $mode,
    'serviceName' => 'Person'
);

$clientClass = new Client($params);
$client = $clientClass->getClient();

try {
    // say 为 server 端 Person.class.php 中的函数
    $result = $client->__soapCall('say', array('aboutc'));
    // 或
    $result2 = $client->serverVar();
    var_dump($result, $result2);
}
catch (SoapFault $fault){
    echo 'Error Message: ' . $fault->getMessage();
}

// 注:
// 如果对方使用的 .NET，尝试使用以下两种形式：
// 例如你需要传类似这样的参数：
// $params['email'] = 'test@aboutc.net';
// $params['name'] = 'ueaner';
//  $client->__soapCall('someFunction', array('param' => $params));
//  $client->__soapCall('someFunction', array($params));
