<?php
$title = 'Publish catalogue discipline pages to the prince destination';

// $type_override = 'page';
$start_asset = '817373157f00000101f92de5bea1554a';

// Optionally override the container/child types
// $asset_type = 'assetFactoryContainer';
// $asset_children_type = 'assetFactory';


// $year = '[-0-9]+'; // Matches all years
$year = '2012-2013';

function pagetest($child) {
  global $year;
  if (preg_match('/^[a-z][-a-z]+\/[-a-z]+\/index/',$child->path->path) || preg_match('/^humanities\/modern-languages-and-literatures\/[-a-z]+\/index$/',$child->path->path))
    return true;
}
function foldertest($child) {
  global $year;
  if (preg_match('/^[a-z][-a-z]+$/',$child->path->path) || preg_match('/^[a-z][-a-z]+\/[-a-z]+$/',$child->path->path) || preg_match('/^humanities\/modern-languages-and-literatures\/[-a-z]+$/',$child->path->path))
    return true;
}
function edittest($asset) {
  if (preg_match('/^slc-catalogue-undergraduate\/Discipline Landing Page/', $asset["contentTypePath"]))
    return true;
}

function changes(&$asset) {

}


include('html_header.php');


function readFolder($client, $auth, $id) {
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
    indexFolder($client, $auth, $asset);
  } else {
    echo '<div class="f">Failed to read folder: '.$asset["path"].'</div>';
  }
}
function indexFolder($client, $auth, $asset) {
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
    }
  }
}

function readPage($client, $auth, $id) {
  $reply = $client->read ( array ('authentication' => $auth, 'identifier' => $id ) );
  if ($reply->readReturn->success == 'true') {
    $asset = ( array ) $reply->readReturn->asset->page;
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
  global $total;
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
  
  changes($asset);
  
  if ($_POST['after'] == 'on') {
    echo '<input type="checkbox" class="hidden" id="Aexpand'.$asset['id'].'"><label class="fullpage" for="Aexpand'.$asset['id'].'">';
      print_r($asset); // Shows the page as it will be
    echo '</label>';
  }
  
  if ($_POST['action'] == 'edit') {
    $publish = $client->publish ( array ('authentication' => $auth, 'identifier' => array('type' => 'page', 'id' => $asset["id"], 'destination' => 'prince') ) );
  }
  
  
  if ($publish->publishReturn->success == 'true') {
    echo '<div class="s">Publish success</div>';
    $total['s']++;
  } else {
    echo '<div class="f">Publish failed: '.$asset['path'].'<div>'.extractMessage($result).'</div></div>';
    $total['f']++;
  }
  echo '</div>';
}


?>