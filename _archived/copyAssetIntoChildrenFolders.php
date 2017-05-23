<?php
$title = 'Copy a single asset into multiple children folders';

// $type_override = 'page';
// $id of parent folder, that the asset will be copied into the children of
$start_asset = '047ea2a27f00000201d3ecae371474a9';

// Optionally override the container/child types
// $asset_type = 'assetFactoryContainer';
// $asset_children_type = 'assetFactory';

function pagetest($child) {
  if (preg_match('/[a-z]/', $child->path->path))
    return true;
}
function foldertest($child) {
  if (preg_match('/^_[a-z]/', $child->path->path))
    return true;
}
function edittest($asset) {
  if (preg_match('/[a-z]/', $asset["contentTypePath"]))
    return true;
}


function changes(&$asset) {
  $asset["metadata"]->teaser = 's';
}


include('../html_header.php');


function readFolder($client, $auth, $id) {
  global $asset_type, $asset_children_type;
  $folder = $client->read ( array ('authentication' => $auth, 'identifier' => $id ) );
  if ($folder->readReturn->success == 'true') {
    
    $asset = ( array ) $folder->readReturn->asset->$asset_type;
    if ($_POST['folder'] == 'on') {
      echo "<h1>Folder: ".$asset["path"]."</h1>";
    }
    if ($_POST['children'] == 'on') {
      echo '<button class="btn" href="#cModal'.$asset['id'].'" data-toggle="modal">View Children</button><div id="cModal'.$asset['id'].'" class="modal hide" tabindex="-1" role="dialog" aria-hidden="true"><div class="modal-body">';
        print_r($asset["children"]); // Shows all the children of the folder
      echo '</div></div>';
    }
    indexFolder($client, $auth, $asset);
  } else {
    echo '<div class="f">Failed to read folder: '.$asset["path"].'</div>';
  }
}

function indexFolder($client, $auth, $asset) {
  foreach($asset["children"]->child as $child) {
    if ($child->type == "folder") {
      $destFolder = array ('type' => 'folder', 'id' => $child->id);
      // Set the name here
      $copyParams = array ("newName" => "courses", 'destinationContainerIdentifier' => $destFolder, "doWorkflow" => false);
      // The asset you're $copying
      $copying = array ('type' => 'page', 'id' => '3344391b7f00000271e8de2f67c38a2a' );	
      
      $params = array ('authentication' => $auth, 'identifier' => $copying, 'copyParameters' => $copyParams );

      if ($_POST['action'] == 'edit') {
        $reply = $client->copy ( $params );
      }
      
      if ($reply->copyReturn->success == 'true') {
        echo '<div class="s">Copy success</div>';
        $total['s']++;
      } else {
        echo '<div class="f">Edit failed: '.$child->path->path.'<div>'.extractMessage($result).'</div></div>';
        $total['f']++;
      }
    }
  }
}


?>