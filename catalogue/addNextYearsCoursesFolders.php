<?php
date_default_timezone_set('America/New_York');
$title = 'Copy the base asset used for course folders';
/* This script will copy the base asset used for course folders into the /courses/ folder
   of each discipline. Just change $lastyear and $nextyear.
*/

// $type_override = 'page';
$start_asset = '817373157f00000101f92de5bea1554a'; // Undergrad
// $start_asset = '4e9e12a97f000001015d84e03ea3fb26'; // Grad

$lastyear = '/2016-2017$/';
$nextyear = "2017-2018";

function foldertest($child) {
  if (preg_match('/^[a-z]/', $child->path->path) && !preg_match('/\/*\//', $child->path->path))
    return true;
}

include('../html_header.php');

function readFolder($client, $auth, $id) {
  $folder = $client->read ( array ('authentication' => $auth, 'identifier' => $id ) );
  if ($folder->readReturn->success == 'true') {
    $asset = ( array ) $folder->readReturn->asset->folder;
    $children = (array) $asset["children"];
    if ($_POST['folder'] == 'on') {
      echo "<h1>Folder: ".$asset["path"]."</h1>";
    }
    if ($_POST['children'] == 'on' && !$cron) {
      echo '<button class="btn" href="#cModal'.$asset['id'].'" data-toggle="modal">View Children</button><div id="cModal'.$asset['id'].'" class="modal hide" tabindex="-1" role="dialog" aria-hidden="true"><div class="modal-body">';
        print_r($asset["children"]); // Shows all the children of the folder
      echo '</div></div>';
    }
    indexFolder($client, $auth, $asset);
  } else {
    echo '<div class="f">Failed to read folder: '.$asset["name"].'</div>';
  }
}

function indexFolder($client, $auth, $asset) {
  global $total, $lastyear, $nextyear;
  if (is_array($asset["children"]->child)) {
    foreach($asset["children"]->child as $child) {
      if ($child->type == "folder") {

        // The current/previous year:
        if (preg_match($lastyear, $child->path->path)) {

          // Copy asset
          if ($_POST['asset'] == 'on') {
            echo 'Creating: '.substr($child->path->path,0,strpos($child->path->path, '/')).'/'.$nextyear;
          }

          $destFolder = array ('type' => 'folder', 'id' => $asset["id"]);
          // The next year:
          $copyParams = array ("newName" => $nextyear, 'destinationContainerIdentifier' => $destFolder, "doWorkflow" => false);
          $baseAsset = array ('type' => 'folder', 'id' => '857434137f00000101f92de5518ef553' );
          $params = array ('authentication' => $auth, 'identifier' => $baseAsset, 'copyParameters' => $copyParams );

          if ($_POST['action'] == 'edit') {
            $copy = $client->copy ( $params );
          }

          if ($copy->copyReturn->success == 'true') {
            echo '<div class="s">Copy success: '.substr($child->path->path,0,strpos($child->path->path, '/')).'/'.$nextyear.'</div>';
            $total['s']++;
          } else {
            echo '<div class="f">Copy Failed: '.substr($child->path->path,0,strpos($child->path->path, '/')).'/'.$nextyear.'</div>';
            $total['f']++;
          }
          echo '<hr/>';
        }
        if (foldertest($child)) {
          readFolder($client, $auth, array ('type' => 'folder', 'id' => $child->id));
        }
      }
    }
  }
}


?>
