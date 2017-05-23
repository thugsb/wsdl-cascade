<!DOCTYPE html>
<html>
<head>
  <title>Lists all assets</title>
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
$auth = array ('username' => 'stu-wsdl', 'password' => 'Baggi50^' );
$id = array ('type' => 'page', 'id' => '6633aa8a7f00000201f9140fc1e13b0f' );
// $id = array ('type' => 'page', 'path' => array( 'path' => '/index', 'siteName' => 'www.sarahlawrence.edu+about' ));
$params = array ('authentication' => $auth );

// Read asset
$reply = $client->listSites ( $params );
if ($reply->listSitesReturn->success == 'true') {
  $asset = ( array ) $reply;
  echo "<script type='text/javascript'>var asset = ";
  print_r(json_encode($asset));
  echo "</script>";
  print_r($asset);
} else {
  echo '<div class="f">Read failed</div>';
  $result = $client->__getLastResponse();
  echo '<div class="f">Edit failed: '.$asset['path'].'<div>'.htmlspecialchars(extractMessage($result)).'</div></div>';
  
}


?>
</body>
</html>