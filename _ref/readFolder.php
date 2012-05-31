<!DOCTYPE html>
<html>
<head>
  <title>Read a folder</title>
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
$id = array ('type' => 'folder', 'id' => '2f7dcabc7f00000101f92de527bf1fa7' );

$folder = $client->read ( array ('authentication' => $auth, 'identifier' => $id ) );
if ($folder->readReturn->success == 'true') {
  $asset = ( array ) $folder->readReturn->asset->folder->children;
  print_r($asset);
  foreach($asset["child"] as $child) {
    if ($child->type == "page") {
      // Call a function something
    }
  }
} else {
  echo '<div class="f">Read failed</div>';
}


?>
</body>
</html>