<?php
$title = 'Hint: Disable "Show Asset Names" to write to file';

// $type_override = 'page';
$start_asset = 'e59cc45a7f00000100c46dcf503b2144';

// Optionally override the container/child types
// $asset_type = 'assetFactoryContainer';
// $asset_children_type = 'assetFactory';

function pagetest($child) {
  // if (preg_match('/[a-z]/', $child->path->path))
  return false;
}
function foldertest($child) {
  return true;
}
function edittest($asset) {
  if ($asset['metadataSetPath'] != 'www_config:Default Sets/Site Section')
    return true;
}

function changes(&$asset) {
  /* If you wish to use $changed, make sure it's global, and set it to false. 
   * When something is changed, it becomes true: */
  global $changed;
  $changed = true;
  $asset['metadataSetId'] = '36c124d67f0000020053f8eb9f04aedb';
  $asset['metadataSetPath'] = 'Site Section';
}


include('../html_header.php');

?>


<?php

function readFolder($client, $auth, $id) {
  $folder = $client->read ( array ('authentication' => $auth, 'identifier' => $id ) );
  if ($folder->readReturn->success == 'true') {
    $asset = ( array ) $folder->readReturn->asset->folder;
    if ($_POST['folder'] == 'on') {
      echo "<h1>Folder: ".$asset["path"]."</h1>";
    }
    
    // $folderLink = '<a class="left_label" href="https://cms.slc.edu:8443/entity/open.act?id='.$asset['id'].'&amp;type=folder">'.$asset['siteName'].'://'.$asset["path"].'</a> '.$asset['metadataSetPath']."<br>\n";
    // echo $folderLink; // This shows all the folders, regardless of Metadata Set
    
    if (edittest($asset)) {
      editFolder($client, $auth, $asset);
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
    if ($child->type == "folder") {
      if (foldertest($child))
        readFolder($client, $auth, array ('type' => 'folder', 'id' => $child->id));
    }
  }
}

function editFolder($client, $auth, $asset) {
  global $total;
  
  $folderLink = '<a class="left_label" href="https://cms.slc.edu:8443/entity/open.act?id='.$asset['id'].'&amp;type=folder">'.$asset['siteName'].'://'.$asset["path"].'</a> '.$asset['metadataSetPath']."<br>\n";
  echo $folderLink; // Shows the folders that would get edited
  
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
  
  if ($_POST['asset'] != 'on') {
    $myFile = "_ref/metaDataSets.html";
    $fh = fopen($myFile, 'a') or die("can't open file");
    fwrite($fh, $folderLink);
    fclose($fh);
  }
  
  changes($asset);
  
  if ($_POST['after'] == 'on') {
    echo '<input type="checkbox" class="hidden" id="Aexpand'.$asset['id'].'"><label class="fullpage" for="Aexpand'.$asset['id'].'">';
      print_r($asset); // Shows the page as it will be
    echo '</label>';
  }
  
  if ($_POST['action'] == 'edit') {
    $edit = $client->edit ( array ('authentication' => $auth, 'asset' => array('folder' => $asset) ) );
  }
  
  if ($edit->editReturn->success == 'true') {
    echo '<div class="s">Edit success</div>';
    $total['s']++;
  } else {
    echo '<div class="f">Edit failed: '.$asset['path'].'<div>'.extractMessage($result).'</div></div>';
    $total['f']++;
  }
  echo '</div>';
}



?>
