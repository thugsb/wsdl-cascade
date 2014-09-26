<!DOCTYPE html>
<html>
<head>
  <title>Displays the Access Rights and full asset data</title>
  <style type="text/css">
  body {white-space:pre;}
  .s {color:#090;}
  .f {color:#c00;}
  </style>
</head>
<body>
<?php
include("../web_services_util.php");

$client = new SoapClient ( "https://cms.slc.edu:8443/ws/services/AssetOperationService?wsdl", array ('trace' => 1 ) );	
$auth = array ('username' => '', 'password' => '' );
$id = array ('type' => 'page', 'id' => '6633aa8a7f00000201f9140fc1e13b0f' );
// $id = array ('type' => 'page', 'path' => array( 'path' => '/index', 'siteName' => 'www.slc.edu+about' ));
$params = array ('authentication' => $auth, 'identifier' => $id );

// Read asset Access
$reply = $client->readAccessRights ( $params );
if ($reply->readAccessRightsReturn->success == 'true') {
  $asset = ( array ) $reply;
  echo "<script type='text/javascript'>var access = ";
  print_r(json_encode($asset));
  echo "</script>";
  print_r($asset);
} else {
  echo '<div class="f">Read failed</div>';
}

// Read asset
$reply = $client->read ( $params );
if ($reply->readReturn->success == 'true') {
  $asset = ( array ) $reply;
  echo "<script type='text/javascript'>var asset = ";
  print_r(json_encode($asset));
  echo "</script>";
  print_r($asset);
} else {
  echo '<div class="f">Read failed</div>';
}


?>
</body>
</html>