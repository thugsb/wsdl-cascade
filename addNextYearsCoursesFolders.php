<?php
$title = 'Copy the base asset used for course folders';
/* This script will copy the base asset used for course folders into the /courses/ folder
   of each discipline. Just change $prev_year and $next_year.
*/

// $type_override = 'page';
$start_asset = '817373157f00000101f92de5bea1554a';

$prev_year = '/2011-2012$/';
$next_year = "2012-2013";


include('html_header.php');

function readFolder($client, $auth, $id) {
  $folder = $client->read ( array ('authentication' => $auth, 'identifier' => $id ) );
  if ($folder->readReturn->success == 'true') {
    $asset = ( array ) $folder->readReturn->asset->folder;
    $children = (array) $asset["children"];
    if ($_POST['folder'] == 'on') {
      echo "<h1>Folder: ".$asset["path"]."</h1>";
    }
    if ($_POST['children'] == 'on') {
      print_r($asset["children"]); // Shows all the children of the folder
    }
    indexFolder($client, $auth, $asset);
  } else {
    echo '<div class="f">Failed to read folder: '.$asset["name"].'</div>';
  }
}

function indexFolder($client, $auth, $asset) {
  global $total, $prev_year, $next_year;
  if (is_array($asset["children"]->child)) {
    foreach($asset["children"]->child as $child) {
      if ($child->type == "folder") {
        
        // The current/previous year:
        if (preg_match($prev_year, $child->path->path)) {
          
          // Copy asset
          if ($_POST['asset'] == 'on') {
            echo 'Copying: '.$child->path->path;
          }
          
          $destFolder = array ('type' => 'folder', 'id' => $asset["id"]);
          // The next year:
          $copyParams = array ("newName" => $next_year, 'destinationContainerIdentifier' => $destFolder, "doWorkflow" => false);
          $baseAsset = array ('type' => 'folder', 'id' => '857434137f00000101f92de5518ef553' );
          $params = array ('authentication' => $auth, 'identifier' => $baseAsset, 'copyParameters' => $copyParams );
          
          if ($_POST['action'] == 'edit') {
            $reply = $client->copy ( $params );
          }
          
          if ($reply->copyReturn->success == 'true') {
            echo '<div class="s">Copy success: '.$child->path->path.'</div>';
            $asset = ( array ) $reply->readReturn->asset->folder;
            $total['s']++;
          } else {
            echo '<div class="f">Copy Failed: '.$child->path->path.'</div>';
            $total['f']++;
          }
        }
        readFolder($client, $auth, array ('type' => 'folder', 'id' => $child->id));
      }
    }
  }
}


?>