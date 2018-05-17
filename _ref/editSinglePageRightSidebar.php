<!DOCTYPE html>
<html>
<head>
  <title>Edits a single pages right sidebar after finding the correct node to edit</title>
  <style type="text/css">
  body {white-space:pre;}
  .s {color:#090;}
  .f {color:#c00;}
  </style>
</head>
<body>
<?php
include("../web_services_util.php");
require_once('../_config.php');

$client = new SoapClient ( CMS_PATH, array ('trace' => 1 ) );	
$auth = array ('username' => '', 'password' => '' );
$id = array ('type' => 'page', 'id' => 'c1bc72317f00000201b98c3e815f00ef' );	
$params = array ('authentication' => $auth, 'identifier' => $id );

// Read asset
$reply = $client->read ( $params );
if ($reply->readReturn->success == 'true') {
  $asset = ( array ) $reply->readReturn->asset->page;

  // print_r($asset);

  $data = $asset["structuredData"];
  $nodes = $data->structuredDataNodes->structuredDataNode;

  foreach($nodes as $node){
    if($node->identifier == 'right_sidebar'){
      $node->structuredDataNodes->structuredDataNode[1]->text = "Hello";
      print_r($node);
    }
  }



  // Edit page
  $params = array ('authentication' => $auth, 'asset' => array ('page' => $asset ) );
  try {
    // Enable this to edit
    // $reply = $client->edit ( $params );
  }
  catch(Exception $e) {
    echo "<div class='f'>Problem: {$e->getMessage()}</div>";
  }		
  $result = $client->__getLastResponse();
  if (!isSuccess($result)) {
    echo '<div class="f">Error occurred: ';
    echo .extractMessage($result).'</div>';
  } else {
    echo '<div class="s">Asset updated successfully</div>';
  }
  echo '<div class="s">Done</div>';
}
else
{
  echo '<div class="f">Read failed</div>';
}



?>
</body>
</html>