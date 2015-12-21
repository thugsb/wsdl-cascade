<?php
date_default_timezone_set('America/New_York');
$title = 'Deactivate Event';

include("../web_services_util.php");

if (!array_key_exists('submit', $_POST)) {echo 'no'; exit;}

$client = new SoapClient ( $_POST['client'], array ('trace' => 1 ) );	
$auth = array ('username' => $_POST['login'], 'password' => $_POST['password'] );
$id = array ('type' => 'page', 'id' => $_POST['id'] );
$params = array ('authentication' => $auth, 'identifier' => $id );

$reply = $client->read ( $params );
if ($reply->readReturn->success == 'true') {
  $asset = ( array ) $reply->readReturn->asset->page;
  
  $move = $client->move ( array ('authentication' => $auth, 'identifier' => $id, 'moveParameters' => array('destinationContainerIdentifier'=> array('type'=>'folder', 'id'=>'e7fad9437f000002781205b8ac89680f'), 'doWorkflow'=>false) ) );

  if ($move->moveReturn->success == 'true') {
    echo '<div style="color:#faa732;">'.$asset['name'].' is now Deactive</div>';
  } else {
    echo '<div style="color:#900;">Move failed</div>';
  }

} else {echo '<div style="color:#900;">Read failed</div>';}

?>