<?php
$title = 'Move the previous years course folders to _archived';

// $type_override = 'page';
$start_asset = '817373157f00000101f92de5bea1554a';

// Optionally override the container/child types
// $asset_type = 'assetFactoryContainer';
// $asset_children_type = 'assetFactory';

$move_to = '';

function pagetest($child) {
  return false;
}



function changes(&$asset) {}


include('../html_header.php');



function readFolder($client, $auth, $id) {
  $folder = $client->read ( array ('authentication' => $auth, 'identifier' => $id ) );
  if ($folder->readReturn->success == 'true') {
    $asset = ( array ) $folder->readReturn->asset->folder;
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
  global $move_to;
  if (!is_array($asset["children"]->child)) {
    $asset["children"]->child=array($asset["children"]->child);
  }
  foreach($asset["children"]->child as $child) {
    if ($child->type == "folder") {
      if (preg_match('/^[a-z][-a-z\/]+2011-2012/', $child->path->path) ) {
        foreach($asset["children"]->child as $ch) {
          if (preg_match('/^[a-z][-a-z\/]+_archived$/',$ch->path->path) ) {
            $move_to = $ch;
          }
        }
        editPage($client, $auth, array ('type' => 'folder', 'id' => $child->id));
      }
      if (preg_match('/^[a-z][-a-z\/]+$/',$child->path->path) )
        readFolder($client, $auth, array ('type' => 'folder', 'id' => $child->id));
    }
  }
}



function editPage($client, $auth, $asset) {
  global $total, $move_to;
  
  /* This is unnecessary, but useful for checking the $asset is correct * /
  $folder = $client->read ( array ('authentication' => $auth, 'identifier' => array('id'=>$asset['id'], 'type'=>'folder') ) );
  if ($folder->readReturn->success == 'true') {
    $asset = ( array ) $folder->readReturn->asset->folder;
  } else {
    echo '<div class="f">Failed to read folder: '.$asset["path"].'</div>';
  }
  echo '<div>Folder: '.$asset['path'].'</div>';
  echo '<div>Destin: '.$move_to->path->path.' ID: '.$move_to->id.'</div>'; // */
  
  if ($_POST['asset'] == 'on') {
    echo $asset['id'];
  }
  
  echo '<div class="page">';
  if ($_POST['before'] == 'on') {
    echo '<button class="btn" href="#bModal'.$asset['id'].'" data-toggle="modal">View Before</button><div id="bModal'.$asset['id'].'" class="modal hide" tabindex="-1" role="dialog" aria-hidden="true"><div class="modal-body">';
      print_r($asset); // Shows the page in all its glory
    echo '</div></div>';
  }
  
  echo "<script type='text/javascript'>var page_".$asset['id']." = ";
  print_r(json_encode($asset));
  echo '; console.log(page_'.$asset['id'].')';
  echo "</script>";
  
  changes($asset);
  
  if ($_POST['after'] == 'on') {
    echo '<button class="btn" href="#aModal'.$asset['id'].'" data-toggle="modal">View After</button><div id="aModal'.$asset['id'].'" class="modal hide" tabindex="-1" role="dialog" aria-hidden="true"><div class="modal-body">';
      print_r($asset); // Shows the page in all its glory
    echo '</div></div>';
  }
  
  if ($_POST['action'] == 'edit') {
    $move = $client->move ( array ('authentication' => $auth, 'identifier' => array('type' => 'folder', 'id' => $asset['id']), 'moveParameters' => array('destinationContainerIdentifier'=> array('type'=>'folder', 'id'=>$move_to->id), 'doWorkflow'=>false) ) );
  }
  
  
  if ($move->moveReturn->success == 'true') {
    echo '<div class="s">Edit success</div>';
    $total['s']++;
  } else {
    echo '<div class="f">Edit failed: '.$asset['path'].'<div>'.extractMessage($result).'</div></div>';
    $total['f']++;
  }
  echo '</div>';
}

?>