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
  
  $active_folder = 'f591deccc0a8022b36e21ad709360573';
  
  $move = $client->move ( array ('authentication' => $auth, 'identifier' => $id, 'moveParameters' => array('destinationContainerIdentifier'=> array('type'=>'folder', 'id'=>$active_folder), 'doWorkflow'=>false) ) );

  if ($move->moveReturn->success == 'true') {
    echo '<div style="color:#090;">Move success: '.$asset['name'].' is now enabled</div>';
  } else {
    echo '<div style="color:#900;">Move failed: '.$asset['name'].'</div>';
  }

} else {echo '<div style="color:#900;">Read failed</div>';}

?>