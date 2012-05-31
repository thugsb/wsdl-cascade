<?php
$title = 'Move Archived Faculty to the _inactive folder';

// $type_override = 'page';
$start_asset = '2891e3f87f00000101b7715d1ba2a7fb';

// Optionally override the container/child types
// $asset_type = 'assetFactoryContainer';
// $asset_children_type = 'assetFactory';

function pagetest($child) {
  if (preg_match('/^[a-z]/',$child->path->path) && $child->path->path != 'index')
    return true;
}
function foldertest($child) {
  return false;
}
function edittest($asset) {
  return true;
}

function changes(&$asset) {
  $asset["metadata"]->teaser = 'test';
}


include('html_header.php');



function readFolder($client, $auth, $id) {
  global $asset_type, $asset_children_type, $data;
  $folder = $client->read ( array ('authentication' => $auth, 'identifier' => $id ) );
  if ($folder->readReturn->success == 'true') {
    
    $asset = ( array ) $folder->readReturn->asset->$asset_type;
    if ($_POST['folder'] == 'on') {
      echo "<h1>Folder: ".$asset["path"]."</h1>";
    }
    if ($_POST['children'] == 'on') {
      echo '<input type="checkbox" class="hidden" id="Aexpand'.$asset['id'].'"><label class="fullpage" for="Aexpand'.$asset['id'].'">';
        print_r($asset["children"]); // Shows all the children of the folder
      echo '</label>';
    }
    indexFolder($client, $auth, $asset);
  } else {
    echo '<div class="f">Failed to read folder: '.$asset["path"].'</div>';
  }
}
function indexFolder($client, $auth, $asset) {
  global $data;
  if (!is_array($asset["children"]->child)) {
    $asset["children"]->child=array($asset["children"]->child);
  }
  foreach($asset["children"]->child as $child) {
    if ($child->type == "page") {
      if (pagetest($child))
        readPage($client, $auth, array ('type' => 'page', 'id' => $child->id));
    } elseif ($child->type == "folder") {
      if (foldertest($child))
        readFolder($client, $auth, array ('type' => 'folder', 'id' => $child->id));
    } elseif ($child->type == "assetfactory") {
      if (assetfactorytest($child))
        readPage($client, $auth, array ('type' => 'assetfactory', 'id' => $child->id));
    }
  }
}

function readPage($client, $auth, $id) {
  global $asset_type, $asset_children_type, $data;
  $reply = $client->read ( array ('authentication' => $auth, 'identifier' => $id ) );
  if ($reply->readReturn->success == 'true') {
    $asset = ( array ) $reply->readReturn->asset->$asset_children_type;
    if ($_POST['asset'] == 'on') {
      echo '<h4>'.$asset['path']."</h4>";
    }
    
    if (edittest($asset)) {
      editPage($client, $auth, $asset);
    }
    
  } else {
    echo '<div class="f">Failed to read page: '.$id.'</div>';
  }
}


function editPage($client, $auth, $asset) {
  global $total, $asset_type, $asset_children_type, $data;
  echo '<div class="page">';
  if ($_POST['before'] == 'on') {
    echo '<input type="checkbox" class="hidden" id="Bexpand'.$asset['id'].'"><label class="fullpage" for="Bexpand'.$asset['id'].'">';
      print_r($asset); // Shows the page in all its glory
    echo '</label>';
  }
  
  echo "<script type='text/javascript'>var page_".$asset['id']." = ";
  print_r(json_encode($asset));
  echo '; console.log(page_'.$asset['id'].')';
  echo "</script>";
  
  if ($_POST['after'] == 'on') {
    echo '<input type="checkbox" class="hidden" id="Aexpand'.$asset['id'].'"><label class="fullpage" for="Aexpand'.$asset['id'].'">';
      print_r($asset); // Shows the page as it will be
    echo '</label>';
  }

  if ($asset["structuredData"]->structuredDataNodes->structuredDataNode[0]->structuredDataNodes->structuredDataNode[6]->text == "Archived") {
    echo '<div class="f">This faculty is inactive</div>';
    if ($_POST['action'] == 'edit') {
      $move = $client->move ( array ('authentication' => $auth, 'identifier' => array('type' => 'page', 'id' => $asset["id"]), 'moveParameters' => array('destinationContainerIdentifier'=> array('type'=>'folder', 'id'=>'6824bab27f00000101b7715d4c99fd4c'), 'doWorkflow'=>false) ) );
      if ($move->moveReturn->success == 'true') {
        echo '<div class="s">Move success</div>';
        $total['s']++;
      } else {
        echo '<div class="f">Move failed: '.$asset['path'].'<div>'.extractMessage($result).'</div></div>';
        $total['f']++;
      }
    }
  } else {
    echo '<div class="s">This faculty is active</div>';
  }
  
  
  echo '</div>';
  

}


?>