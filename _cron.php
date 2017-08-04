<?php

include_once(__DIR__.'/rollbar-init.php');
use \Rollbar\Rollbar;
use \Rollbar\Payload\Level;

parse_str(implode('&', array_slice($argv, 1)), $_GET);

$o = array('','','','','');

include("web_services_util.php");

if (file_exists($_GET['c'])) {
  include($_GET['c']);
} else {
  echo 'You must supply a _credentials.php file, setting $username, $password and $email (to send the output to).'."\n";
  exit;
}

if ($_GET['s']) {
  $cron = true;
  include($_GET['s']);
} else {
  exit;
}





$asset_types = array("folder", "page", "assetfactory", "assetfactorycontainer", "block", "block_FEED", "block_INDEX", "block_TEXT", "block_XHTML_DATADEFINITION", "block_XML", "connectorcontainer", "twitterconnector", "facebookconnector", "wordpressconnector", "googleanalyticsconnector", "contenttype", "contenttypecontainer", "destination", "file", "group", "message", "metadataset", "metadatasetcontainer", "pageconfigurationset", "pageconfiguration", "pageregion", "pageconfigurationsetcontainer", "publishset", "publishsetcontainer", "reference", "role", "datadefinition", "datadefinitioncontainer", "format", "format_XSLT", "format_SCRIPT", "site", "sitedestinationcontainer", "symlink", "target", "template", "transport", "transport_fs", "transport_ftp", "transport_db", "transportcontainer", "user", "workflow", "workflowdefinition", "workflowdefinitioncontainer");
$total = array('s' => 0, 'f' => 0, 'k' => 0);

// If it's not a folder, you need to set the correct $asset_type (camelCase)
if (!isset($asset_type)) {
  $asset_type = 'folder';
  $asset_children_type = 'page';
}
if (!isset($data)) {$data = '';}

$client = new SoapClient ( 'https://cms.slc.edu:8443/ws/services/AssetOperationService?wsdl', array ('trace' => 1 ) );	
$auth = array ('username' => $username, 'password' => $password );
$ids = explode(',',$start_asset);



if (!function_exists(readFolder)) {

  function readFolder($client, $auth, $id) {
    global $asset_type, $asset_children_type, $data, $o, $total;
    $folder = $client->read ( array ('authentication' => $auth, 'identifier' => $id ) );
    if ($folder->readReturn->success == 'true') {
    
      $asset = ( array ) $folder->readReturn->asset->$asset_type;
      $o[4] .= "<h4>Folder: ".$asset["path"]."</h4>";

      // $o[5] .= '<div style="white-space:pre;">' . print_r($asset["children"], true) . '</div>'; // Shows all the children of the folder

      indexFolder($client, $auth, $asset);
    } else {
      $o[1] .= '<div style="padding:3px;color:#fff;background:#c00;">Failed to read folder: '.$asset["path"].'</div>';
      $total['f']++;
    }
  }
  function indexFolder($client, $auth, $asset) {
    global $data, $o;
    if (!is_array($asset["children"]->child)) {
      $asset["children"]->child=array($asset["children"]->child);
    }
    foreach($asset["children"]->child as $child) {
      if ($child->type == "page") {
        if (pagetest($child))
          readPage($client, $auth, array ('type' => 'page', 'id' => $child->id), $child->type);
      } elseif ($child->type == "folder") {
        if (foldertest($child))
          readFolder($client, $auth, array ('type' => 'folder', 'id' => $child->id));
      } elseif ($child->type == "assetfactory") {
        if (assetfactorytest($child))
          readPage($client, $auth, array ('type' => 'assetfactory', 'id' => $child->id, $child->type));
      }
    }
  }

  function readPage($client, $auth, $id, $type) {
    global $asset_type, $asset_children_type, $data, $o, $total;
    $reply = $client->read ( array ('authentication' => $auth, 'identifier' => $id ) );
    if ($reply->readReturn->success == 'true') {
      $asset = ( array ) $reply->readReturn->asset->$asset_children_type;
      $o[3] .= '<h4><a href="https://cms.slc.edu:8443/entity/open.act?id='.$asset['id'].'&type='.$type.'#highlight">'.$asset['path']."</a></h4>";
    
      if (edittest($asset)) {
        editPage($client, $auth, $asset, $type);
      }
    
    } else {
      $o[1] .= '<div style="padding:3px;color:#fff;background:#c00;">Failed to read page: '.$id.'</div>';
      $total['f']++;
    }
  }


  function editPage($client, $auth, $asset, $type) {
    global $total, $asset_type, $asset_children_type, $data, $changed, $o;
    // $o[6] .= '<div class="page"><h3>Before</h3>';
    // $o[6] .= '<div style="white-space:pre;">"' . print_r($asset, true) . '</div>'; // Shows the page in all its glory
  
    changes($asset);
  
    // $o[6] .= '<h3>After</h3>';
    // $o[6] .= '<div style="white-space:pre;">"' . print_r($asset, true) . '</div>'; // Shows the page as it will be
  
    if ($changed == true) {
      $edit = $client->edit ( array ('authentication' => $auth, 'asset' => array($asset_children_type => $asset) ) );
      if ($edit->editReturn->success == 'true') {
        // $o[6] .= '<div class="s">Edit success</div>';
        $o[2] .= '<div style="color:#090;">Edit success: <a href="https://cms.slc.edu:8443/entity/open.act?id='.$asset['id'].'&type='.$type.'#highlight">'.$asset['path']."</a></div>";
        $total['s']++;
      } else {
        $o[1] .= '<div style="padding:3px;color:#fff;background:#c00;">Edit failed: <a href="https://cms.slc.edu:8443/entity/open.act?id='.$asset['id'].'&type='.$type.'#highlight">'.$asset['path']."</a><div>".extractMessage($result).'</div></div>';
        $total['f']++;
      }
    } else {
      // $o[6] .= '<div class="k">No changes needed</div>';
      $total['k']++;
    }
  
    // $o[6] .= '</div>';
  }
}




if ($asset_type == 'folder' || preg_match('/container/', $asset_type) ) {
  foreach($ids as $id) {
    $asset = array ('type' => $asset_type, 'id' => $id );
    readFolder($client, $auth, $asset);
  }
  $o[0] .= '<div class="totals">Successes: '.$total['s'].' Failures: '.$total['f'].' Skipped: '.$total['k'].'</div>';
} else {
  foreach($ids as $id) {
    $asset = array ('type' => $asset_type, 'id' => $id );
    readPage($client, $auth, $asset, $asset_type);
  }
}

$changed = true;

$output = "Script: ".$_GET['s'] . "\n\nSummary:\n==========\n".$o[0];
if (strlen($o[1]) > 0 ) {
  $output .= "\n\n\nErrors:\n==========\n".$o[1];
}
if (strlen($o[2]) > 0 ) {
  $output .= "\n\n\nEdited Assets:\n==========\n".$o[2];
}
if (strlen($o[3]) > 0 ) {
  $output .= "\n\n\nAll Assets Processed:\n==========\n".$o[3];
}
if (strlen($o[4]) > 0 ) {
  $output .= "\n\n\nFolders Processed:\n==========\n".$o[4];
}

$headers = 'From: com@vm-www.slc.edu' . "\r\n" . 'Cc: wjoell@sarahlawrence.edu';

if ($total['f'] > 0) {
  $response = Rollbar::log(Level::error(), $output);
  if (!$response->wasSuccessful()) {
      mail($email, 'Logging with Rollbar FAILED ' . $_GET['s'], $output, $headers);
  }
} else {
  $response = Rollbar::log(Level::info(), $output);
  if (!$response->wasSuccessful()) {
      mail($email, 'Logging with Rollbar FAILED ' . $_GET['s'], $output, $headers);
  }
}

?>
