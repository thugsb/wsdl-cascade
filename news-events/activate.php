<?php
date_default_timezone_set('America/New_York');
$title = 'Activate Event';

include("../web_services_util.php");

if (!array_key_exists('submit', $_POST)) {echo 'no'; exit;}

$client = new SoapClient ( $_POST['client'], array ('trace' => 1 ) );	
$auth = array ('username' => $_POST['login'], 'password' => $_POST['password'] );
$id = array ('type' => 'page', 'id' => $_POST['id'] );
$params = array ('authentication' => $auth, 'identifier' => $id );

$reply = $client->read ( $params );
if ($reply->readReturn->success == 'true') {
  $asset = ( array ) $reply->readReturn->asset->page;
  
  foreach ($asset["metadata"]->dynamicFields->dynamicField as $dyn) {
    if ($dyn->name == 'visible') {
      $dyn->fieldValues = new stdClass;
      $dyn->fieldValues->fieldValue = new stdClass;
      $dyn->fieldValues->fieldValue->value = 'Yes';
    }
  }
  
  $edit = $client->edit ( array ('authentication' => $auth, 'asset' => array ('page' => $asset ) ) );

  if ($edit->editReturn->success == 'true') {
    echo '<div style="color:#090;">'.$asset['name'].' is now Visible</div>';
  } else {
    echo '<div style="color:#900;">Edit failed</div>';
  }

} else {echo '<div style="color:#900;">Read failed</div>';}

?>