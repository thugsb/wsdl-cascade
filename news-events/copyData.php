<?php
date_default_timezone_set('America/New_York');
$title = 'Copy Data from Deleted Event';

echo 'Copying...';
ob_flush();
flush();

include_once('eventFolderIDs.php');
include("../web_services_util.php");

if (!array_key_exists('submit', $_POST)) {echo 'no'; exit;}

$client = new SoapClient ( $_POST['client'], array ('trace' => 1 ) ); 
$auth = array ('username' => $_POST['login'], 'password' => $_POST['password'] );
$id = array ('type' => 'page', 'id' => $_POST['id'] );
$params = array ('authentication' => $auth, 'identifier' => $id );
$deleted_event_id = array ('type' => 'page', 'id' => $_POST['deleted_event'] );
$del_params = array ('authentication' => $auth, 'identifier' => $deleted_event_id );

$read_del = $client->read ( $del_params );
if ($read_del->readReturn->success == 'true') {
  $del_page = ( array ) $read_del->readReturn->asset->page;
  $old_dynamic_fields = $del_page["metadata"];
  $old_structured_data = $del_page["structuredData"]->structuredDataNodes;

  $read = $client->read ( $params );
  if ($read->readReturn->success == 'true') {
    $asset = ( array ) $read->readReturn->asset->page;
    foreach ($asset["metadata"]->dynamicFields->dynamicField as $dyn) {
      if ($dyn->name == 'begin') {
        $begin = $dyn->fieldValues->fieldValue->value;
      }
      if ($dyn->name == 'end') {
        $end = $dyn->fieldValues->fieldValue->value;
      }
      if ($dyn->name == 'location') {
        $location = $dyn->fieldValues->fieldValue->value;
      }
    }
    $asset["metadata"] = $old_dynamic_fields;
    $asset["structuredData"]->structuredDataNodes = $old_structured_data;
    foreach ($asset["metadata"]->dynamicFields->dynamicField as $dyn) {
      if ($dyn->name == 'begin') {
        $dyn->fieldValues->fieldValue->value = $begin;
      }
      if ($dyn->name == 'end') {
        $dyn->fieldValues->fieldValue->value = $end;
      }
      if ($dyn->name == 'location') {
        $dyn->fieldValues->fieldValue->value = $location;
      }
    }

    $edit = $client->edit ( array ('authentication' => $auth, 'asset' => array('page' => $asset) ) );
    if ($edit->editReturn->success == 'true') {
      echo '<div style="color:#090;">Edit success</div>';

       $move = $client->move ( array ('authentication' => $auth, 'identifier' => $id, 'moveParameters' => array('destinationContainerIdentifier'=> array('type'=>'folder', 'id'=>$year_folder), 'doWorkflow'=>false) ) );

      if ($move->moveReturn->success == 'true') {
        echo '<div style="color:#090;">Move success: '.$asset['name'].' is now enabled</div>';
          
        $publish = $client->publish ( array ('authentication' => $auth, 'publishInformation' => array('identifier' => $id, 'unpublish' => false ) ) );
        if ($publish->publishReturn->success == 'true') {
          echo '<div style="color:#090;">Publish success: '.$asset['name'].'</div>';
        } else {
          echo '<div style="color:#900;">Publish failed: '.$asset['name'].'</div>';
          $total['f']++;
        }
      } else {
        echo '<div style="color:#900;">Move failed: '.$asset['name'].' (publish was NOT attempted)</div>';
      }
    } else {echo '<div style="color:#900;">Copying data (edit) failed (no changes were made)</div>';}
  } else {echo '<div style="color:#900;">Read failed (no changes were made)</div>';}
} else {
  echo '<div style="color:#900;">Read failed for deleted event (no changes were made)</div>';
  exit;
}
