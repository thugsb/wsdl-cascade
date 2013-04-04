<?php
include("../web_services_util.php");

$client = new SoapClient ( "http://dev.cascadeserver.com/ws/services/AssetOperationService?wsdl", array ('trace' => 1 ) );  
$auth = array ('username' => '', 'password' => '' );
$id = array ('type' => 'page', 'path' => array('path' => 'company/services/index', 'siteName' => 'example.com'));   
$response = $client->read(array('authentication'=>$auth, 'identifier'=>$id));
if ($response->readReturn->success!='true')
{
    echo "Asset cannot be read";
    exit();
}
$parentFolderId = $response->readReturn->asset->page->parentFolderId;

$reference = array
(
    'referencedAssetId' => '8936ff680a00016c5e4c03d4d096b940',
    'referencedAssetType' => 'page',
    'parentFolderId' => '7b8140187f00010101be7ff53dce6245',
    'siteName' => 'example.com',
    'name' => $response->readReturn->asset->page->name
    );


$asset = array('reference'=>$reference);



try
{
    $client->create(array('authentication' => $auth, 'asset'=>$asset));
}
catch(Exception $e)
{
    echo "\r\nProblem: {$e->getMessage()}\n";
}
$result = $client->__getLastResponse();
if (!isSuccess($result))
{
    echo "\r\nError occured:";
    echo "\r\n".extractMessage($result)."\r\n";
}
else    
    echo "\r\nAsset created successfully\r\n";
echo "Done";


?>