<?php
date_default_timezone_set('America/New_York');
$title = 'Create references for the new course folders, and remove the oldest one.';

// $type_override = 'page';
$start_asset = '817373157f00000101f92de5bea1554a'; // Undergrad
// $start_asset = '4e9e12a97f000001015d84e03ea3fb26'; // Grad

$siteName = 'www.sarahlawrence.edu+catalogue';
// $siteName = 'www.sarahlawrence.edu+grad-catalogue';

$oldyear = '2011-2012';
$nextyear = '2015-2016';

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
  global $total, $oldyear, $nextyear, $siteName;
  if (is_array($asset["children"]->child)) {
    foreach($asset["children"]->child as $child) {
      if ($child->type == "folder") {

        // The current/previous year:
        if (preg_match('/'.$nextyear.'$/', $child->path->path)) {

          $discFolder = substr($child->path->path,0,strpos($child->path->path, '/')).'/';

          // Copy asset
          if ($_POST['asset'] == 'on') {
            echo 'Creating reference: '.$discFolder.'_indexes/'.$nextyear;
          }
          
          $reference = array(
            'reference' => array(
              'name' => $nextyear,
              'parentFolderPath' => $discFolder.'_indexes',
              'referencedAssetId' => $child->id,
              'siteName' => $siteName,
              'referencedAssetType' => 'folder'
            )
          );
          
          
          // Create the new reference
          if ($_POST['action'] == 'edit') {
            $create = $client->create(array ('authentication' => $auth, 'asset' => $reference) );
          }
          if ($create->createReturn->success == 'true') {
            echo '<div class="s">Creation success: '.$discFolder.'_indexes/'.$nextyear.'</div>';
            $total['s']++;
          } else {
            if ($_POST['action'] == 'edit') {$result = $client->__getLastResponse();} else {$result = '';}
            echo '<div class="f">Creation Failed: '.$discFolder.'_indexes/'.$nextyear.'<div>'.htmlspecialchars(extractMessage($result)).'</div></div>';
            $total['f']++;
          }
          
          
          // Delete the old reference
          if ($_POST['action'] == 'edit') {
            $delete = $client->delete(array ('authentication' => $auth, 'identifier' => array('path' => array( 'path' => $discFolder.'_indexes/'.$oldyear, 'siteName' => $siteName ), 'type' => 'reference') ) );
          }
          if ($delete->deleteReturn->success == 'true') {
            echo '<div class="s">Deletion success: '.$discFolder.'_indexes/'.$oldyear.'</div>';
            $total['s']++;
          } else {
            if ($_POST['action'] == 'edit') {$result = $client->__getLastResponse();} else {$result = '';}
            echo '<div class="f">Deletion Failed: '.$discFolder.'_indexes/'.$oldyear.'<div>'.htmlspecialchars(extractMessage($result)).'</div></div>';
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
