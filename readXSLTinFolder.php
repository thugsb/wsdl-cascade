<?php
$title = 'Read all the XSLT files in a folder - Select Show Before to get the Expand All option';

// $type_override = 'page';
$start_asset = 'c39051f57f00000201849c5c76213f88';

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
  $asset["metadata"]->teaser = 'test';
}


include('html_header.php');


function readFolder($client, $auth, $id) {
  $odd = 0;

  $folder = $client->read ( array ('authentication' => $auth, 'identifier' => $id ) );
  if ($folder->readReturn->success == 'true') {
    $asset = ( array ) $folder->readReturn->asset->folder;
    if ($_POST['folder'] == 'on') {
      echo "<h1>Folder: ".$asset["path"]."</h1>";
    }
    if ($_POST['children'] == 'on') {
      echo '<input type="checkbox" class="hidden" id="Aexpand'.$asset['id'].'"><label class="fullpage" for="Aexpand'.$asset['id'].'">';
        print_r($asset["children"]); // Shows all the children of the folder
      echo '</label>';
    }
    indexFolder($client, $auth, $asset, $odd);
  } else {
    echo '<div class="f">Failed to read folder: '.$asset["name"].'</div>';
  }
}

function indexFolder($client, $auth, $asset, $odd) {
  foreach($asset["children"]->child as $child) {
    if ($child->type == "format_XSLT") {
      if ($odd == 0)
        $odd = 1;
      else
        $odd = 0;
      readPage($client, $auth, array ('type' => 'format_XSLT', 'id' => $child->id), $odd);
    } elseif ($child->type == "folder") {
      readFolder($client, $auth, array ('type' => 'folder', 'id' => $child->id));
    }
  }
}



function readPage($client, $auth, $id, $odd) {
  $reply = $client->read ( array ('authentication' => $auth, 'identifier' => $id ) );
  if ($reply->readReturn->success == 'true') {
    $asset = ( array ) $reply->readReturn->asset->xsltFormat;
    if ($_POST['asset'] == 'on') {
      echo '<h4>'.$asset['path']."</h4>";
    }
    echo '<input type="checkbox" class="hidden" id="ID-'.$asset['id'].'">';
    if ($odd == 0)
      echo '<label class="fullpage" for="ID-'.$asset['id'].'">';
    else
      echo '<label class="fullpage odd" for="ID-'.$asset['id'].'">';
    
    echo '<h3>Name: '.$asset['name']."</h3>";
    echo '<h5>Path: '.$asset['path']."</h5>";
    echo htmlspecialchars($asset['xml'], ENT_QUOTES)."</label>";
    
  } else {
    echo '<div class="f">Failed to read page: '.$id.'</div>';
  }
}

?>