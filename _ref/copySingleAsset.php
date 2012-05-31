<!DOCTYPE html>
<html>
<head>
  <title>Copy a single asset to a new parent folder</title>
  <style type="text/css">
  body {white-space:pre;}
  .s {color:#090;}
  .f {color:#c00;}
  </style>
</head>
<body>
<?php
include("web_services_util.php");

$client = new SoapClient ( "https://cms.slc.edu:8443/ws/services/AssetOperationService?wsdl", array ('trace' => 1 ) );	
$auth = array ('username' => '', 'password' => '' );
$id = array ('type' => 'page', 'id' => 'c1bc72317f00000201b98c3e815f00ef' );	
$destFolder = array ('type' => 'folder', 'id' => '019ab6c77f00000101f92de57a5e5aaf');
$copyParams = array ('destinationContainerIdentifier' => $destFolder, "doWorkflow" => false, "newName" => "404b");
$params = array ('authentication' => $auth, 'identifier' => $id, 'copyParameters' => $copyParams );


// Copy asset
$reply = $client->copy ( $params );

if ($reply->copyReturn->success == 'true') {
  echo '<div class="s">Copy success</div>';
	$asset = ( array ) $reply->readReturn->asset->page;
	print_r($asset);
} else {
	echo '<div class="f">Copy Failed</div>';
}


?>
</body>
</html>