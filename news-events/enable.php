<?php
date_default_timezone_set('America/New_York');
$title = 'Activate Event';

include_once('eventFolderIDs.php');
include("../web_services_util.php");

if (!array_key_exists('submit', $_POST)) {echo 'no'; exit;}

$client = new SoapClient ( $_POST['client'], array ('trace' => 1 ) );	
$auth = array ('username' => $_POST['login'], 'password' => $_POST['password'] );
$id = array ('type' => 'page', 'id' => $_POST['id'] );
$params = array ('authentication' => $auth, 'identifier' => $id );

$read = $client->read ( $params );
if ($read->readReturn->success == 'true') {
  $asset = ( array ) $read->readReturn->asset->page;
  
  $move = $client->move ( array ('authentication' => $auth, 'identifier' => $id, 'moveParameters' => array('destinationContainerIdentifier'=> array('type'=>'folder', 'id'=>$year_folder), 'doWorkflow'=>false) ) );

  if ($move->moveReturn->success == 'true') {
    echo '<div style="color:#090;">Move success: '.$asset['name'].' is now enabled</div>';
    
    addTags($asset);
    
    $edit = $client->edit ( array ('authentication' => $auth, 'asset' => array('page' => $asset) ) );
    if ($edit->editReturn->success == 'true') {
      echo '<div style="color:#090;">Edit success: '.$asset['name'].'</div>';
      
      $publish = $client->publish ( array ('authentication' => $auth, 'publishInformation' => array('identifier' => $id, 'unpublish' => false ) ) );
      if ($publish->publishReturn->success == 'true') {
        echo '<div style="color:#090;">Publish success: '.$asset['name'].'</div>';
      } else {
        echo '<div style="color:#900;">Publish failed: '.$asset['name'].'</div>';
        $total['f']++;
      }
    } else {
      echo '<div style="color:#900;">Edit failed: '.$asset['name'].' (publish was NOT attempted)</div>';
    }
    
  } else {
    echo '<div style="color:#900;">Move failed: '.$asset['name'].' (tagging and publish was NOT attempted)</div>';
  }

} else {echo '<div style="color:#900;">Read failed</div>';}



function addTags(&$asset) {
  
  if(isset($_POST['areas'])){if (is_array($_POST['areas'])) {$areas = $_POST['areas'];} else {$areas = array($_POST['areas']);}} else {$areas = array();}
  if(isset($_POST['disciplines'])){if (is_array($_POST['disciplines'])) {$disciplines = $_POST['disciplines'];} else {$disciplines = array($_POST['disciplines']);}} else {$disciplines = array();}
  if(isset($_POST['programs'])){if (is_array($_POST['programs'])) {$programs = $_POST['programs'];} else {$programs = array($_POST['programs']);}} else {$programs = array();}
  if(isset($_POST['studies'])){if (is_array($_POST['studies'])) {$studies = $_POST['studies'];} else {$studies = array($_POST['studies']);}} else {$studies = array();}
  if(isset($_POST['audiences'])){if (is_array($_POST['audiences'])) {$audiences = $_POST['audiences'];} else {$audiences = array($_POST['audiences']);}} else {$audiences = array();}
  if(isset($_POST['themes'])){if (is_array($_POST['themes'])) {$themes = $_POST['themes'];} else {$themes = array($_POST['themes']);}} else {$themes = array();}
  if(in_array('Careers',$themes)){$careers = true;}
  
  if(isset($_POST['sponsors'])){if (is_array($_POST['sponsors'])) {$sponsors = $_POST['sponsors'];} else {$sponsors = array($_POST['sponsors']);}} else {$sponsors = array();}
  
  
  foreach ($asset["metadata"]->dynamicFields->dynamicField as $dyn) {
    if ($dyn->name == 'faculty-tag') {
      if (isset($_POST['faculty-tag'])) {
        $dyn->fieldValues->fieldValue->value = $_POST['faculty-tag'];
      } else {
        $dyn->fieldValues->fieldValue->value = '';
      }
    } elseif ($dyn->name == 'academics') {
      $dyn->fieldValues->fieldValue = array();
      foreach($areas as $val) {
        $node = new StdClass();
        $node->value = $val;
        array_push($dyn->fieldValues->fieldValue, $node);
      }
      foreach($disciplines as $val) {
        $node = new StdClass();
        $node->value = $val;
        array_push($dyn->fieldValues->fieldValue, $node);
      }
      foreach($programs as $val) {
        $node = new StdClass();
        $node->value = $val;
        array_push($dyn->fieldValues->fieldValue, $node);
      }
      foreach($studies as $val) {
        $node = new StdClass();
        $node->value = $val;
        array_push($dyn->fieldValues->fieldValue, $node);
      }
    } elseif ($dyn->name == 'audiences') {
      $dyn->fieldValues->fieldValue = array();
      foreach($audiences as $val) {
        $node = new StdClass();
        $node->value = $val;
        array_push($dyn->fieldValues->fieldValue, $node);
      }
    } elseif ($dyn->name == 'themes') {
      $dyn->fieldValues->fieldValue = array();
      foreach($themes as $val) {
        $node = new StdClass();
        $node->value = $val;
        array_push($dyn->fieldValues->fieldValue, $node);
      }
    } elseif ($dyn->name == 'sponsors') {
      $dyn->fieldValues->fieldValue = array();
      foreach($sponsors as $val) {
        $node = new StdClass();
        $node->value = $val;
        array_push($dyn->fieldValues->fieldValue, $node);
      }
    }
    
  }
}

?>