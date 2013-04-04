<?php
include("../web_services_util.php");

$client = new SoapClient ( "http://dev.cascadeserver.com/ws/services/AssetOperationService?wsdl", array ('trace' => 1 ) );  
$auth = array ('username' => '', 'password' => '' );

$read1 = array('identifier'=> array('id' => 'c9cc9e707f0001010172b7918b29591a', type=>'page'));
$operation1 = array('read' =>$read1);

$read2 = array('identifier'=> array('id' => 'c9cc9ddd7f0001010172b7915f164296', type=>'page'));
$operation2 = array('read' =>$read2);

$operations = array($operation1, $operation2);
$result = $client->batch(array('authentication'=>$auth, 'operation' => $operations));


print_r($result);
?>