<?php
date_default_timezone_set('America/New_York');
$title = 'When encountering a folder with a single "index" page in it, move the page to the parent, renamed as the folder';

// $type_override = 'page';
$start_asset = 'b732376b7f00000251436a90408943ae';

// Optionally override the container/child types
// $asset_type = 'assetFactoryContainer';
// $asset_children_type = 'assetFactory';

function pagetest($child) {
  return false;
}
function foldertest($child) {
  return true;
}
function edittest($asset) {
  if (preg_match('/[a-z]/', $asset["contentTypePath"]))
    return true;
}

function changes(&$asset) {
  /* If you wish to use $changed, make sure it's global, and set it to false.
   * When something is changed, it becomes true: */
  global $changed;
  $changed = false;
  // if ($asset["metadata"]->teaser != 'test') {
  //    $changed = true;
  //    $asset["metadata"]->teaser = 'test';
  // }
}

if (!$cron) {include('../html_header.php');}



function readFolder($client, $auth, $id) {
  global $asset_type, $asset_children_type, $data, $o, $cron;
  $folder = $client->read ( array ('authentication' => $auth, 'identifier' => $id ) );
  if ($folder->readReturn->success == 'true') {

    $asset = ( array ) $folder->readReturn->asset->$asset_type;
    if ($cron) {
      $o[4] .= "<h4>Folder: ".$asset["path"]."</h4>";
    } elseif ($_POST['folder'] == 'on') {
      echo "<h1>Folder: ".$asset["path"]."</h1>";
    }
    if ($_POST['children'] == 'on' && !$cron) {
      echo '<button class="btn" href="#cModal'.$asset['id'].'" data-toggle="modal">View Children</button><div id="cModal'.$asset['id'].'" class="modal hide" tabindex="-1" role="dialog" aria-hidden="true"><div class="modal-body">';
        print_r($asset["children"]); // Shows all the children of the folder
      echo '</div></div>';
    }
    indexFolder($client, $auth, $asset);
  } else {
    if ($cron) {
      $o[1] .= '<div style="padding:3px;color:#fff;background:#c00;">Failed to read folder: '.$asset["path"].'</div>';
    } else {
      echo '<div class="f">Failed to read folder: '.$asset["path"].'</div>';
    }
  }
}
function indexFolder($client, $auth, $asset) {
  global $asset_type, $asset_children_type, $data, $o, $cron, $total;
  if (!is_array($asset["children"]->child)) {
    $asset["children"]->child=array($asset["children"]->child);
  }
  if ( count($asset["children"]->child) == 1 && substr($asset["children"]->child[0]->path->path, -5) == 'index' ) {
    echo $asset['name'].' is a culprit!<br>';
    if ($_POST['action'] == 'edit') {

      $move = $client->move ( array ('authentication' => $auth, 'identifier' => array('type' => 'folder', 'id' => $asset['id']), 'moveParameters' => array('newName'=> $asset['name'].'1', 'doWorkflow'=>false) ) );
      if ($move->moveReturn->success == 'true') {
        echo '<div class="s">Move folder success</div>';
        $total['s']++;

        $parent_folder = array( 'type' => 'folder', 'id' => $asset['parentFolderId'] );
        $moveIndex = $client->move ( array ('authentication' => $auth, 'identifier' => array('type' => 'page', 'id' => $asset["children"]->child[0]->id), 'moveParameters' => array('newName'=> $asset['name'], 'doWorkflow'=>false, 'destinationContainerIdentifier' => $parent_folder ) ) );
        if ($moveIndex->moveReturn->success == 'true') {
          echo '<div class="s">Move index success</div>';
          $total['s']++;
        } else {
          if ($_POST['debug'] == 'on') {
            $result = $client->__getLastResponse();
          }
          echo '<div class="f">Move index failed: '.$asset["children"]->child[0]->path->path.'<div>'.extractMessage($result).'</div></div>';
          $total['f']++;
        }

      } else {
        if ($_POST['debug'] == 'on') {
          $result = $client->__getLastResponse();
        }
        echo '<div class="f">Move folder failed: '.$asset['path'].'<div>'.extractMessage($result).'</div></div>';
        $total['f']++;
      }
    }
  }
  foreach($asset["children"]->child as $child) {
    if ($child->type === strtolower($asset_type)) {
      if (foldertest($child))
        readFolder($client, $auth, array ('type' => $child->type, 'id' => $child->id));
    }
  }
}



?>
