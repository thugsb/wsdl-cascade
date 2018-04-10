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
  
  if(isset($_POST['academics'])){if (strpos($_POST['academics'],';') === false) {$academics = array($_POST['academics']);} else {$academics = explode(';',$_POST['academics']);}} else {$academics = array();}
  if(isset($_POST['audiences'])){if (strpos($_POST['audiences'],';') === false) {$audiences = array($_POST['audiences']);} else {$audiences = explode(';',$_POST['audiences']);}} else {$audiences = array();}
  if(isset($_POST['themes'])   ){if (strpos($_POST['themes'],   ';') === false) {$themes    = array($_POST['themes']);}    else {$themes    = explode(';',$_POST['themes']);}   } else {$themes = array();}
  if(isset($_POST['channels']) ){if (strpos($_POST['channels'], ';') === false) {$channels  = array($_POST['channels']);}  else {$channels  = explode(';',$_POST['channels']);} } else {$channels = array();}
  if(isset($_POST['sponsors']) ){if (strpos($_POST['sponsors'], ';') === false) {$sponsors  = array($_POST['sponsors']);}  else {$sponsors  = explode(';',$_POST['sponsors']);} } else {$sponsors = array();}

  if(in_array('Careers',$themes)){$careers = true;}
  
  
  
  
  foreach ($asset["metadata"]->dynamicFields->dynamicField as $dyn) {
    if ($dyn->name == 'faculty-tag') {
      if (isset($_POST['faculty'])) {
        $dyn->fieldValues->fieldValue = new StdClass();
        $dyn->fieldValues->fieldValue->value = $_POST['faculty'];
      } else {
        $dyn->fieldValues->fieldValue = new StdClass();
        $dyn->fieldValues->fieldValue->value = '';
      }
    } elseif ($dyn->name == 'academics') {
      $dyn->fieldValues->fieldValue = array();
      foreach($academics as $val) {
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

  foreach ($asset["structuredData"]->structuredDataNodes->structuredDataNode as $sdnode) {
    if ($sdnode->identifier == "group-settings") {
      $text = '';
      foreach ($channels as $key => $value) {
        $text .= '::CONTENT-XML-CHECKBOX::' . $value;
      }
      $newnode = new StdClass();
      $newnode->type = 'text';
      $newnode->identifier = 'channels';
      $newnode->text = $text;
      array_unshift($sdnode->structuredDataNodes->structuredDataNode, $newnode);
    }
  }
}

?>