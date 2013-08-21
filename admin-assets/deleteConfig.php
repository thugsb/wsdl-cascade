<?php
$title = 'Delete a config from all CSets';

$type_override = 'pageconfigurationsetcontainer';
$start_asset = '1697b0d97f00000101f92de526b6ff9b';

// Optionally override the container/child types
$asset_type = 'pageConfigurationSetContainer';
$asset_children_type = 'pageConfigurationSet';

$configID = '';

function pagetest($child) {
  return true;
}
function foldertest($child) {
  if ($child->path->path != 'mobile-pages' && $child->path->path != 'wwww_archived')
    return true;
}
function edittest($asset) {
  return true;
}

function changes(&$asset) {
  global $changed, $configID;
  $changed = false;
  
  if(!is_array($asset['pageConfigurations']->pageConfiguration)) {
    $asset['pageConfigurations']->pageConfiguration = array($asset['pageConfigurations']->pageConfiguration);
  }
  foreach ($asset['pageConfigurations']->pageConfiguration as $key => $conf) {
    if ($conf && $conf->name == 'MobileIA') {
      $configID = $conf->id;
      $changed = true;
      break;
    }
  }
}


include('../html_header.php');

?>


<?php

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
  global $data, $asset_children_type, $asset_type;
  if (!is_array($asset["children"]->child)) {
    $asset["children"]->child=array($asset["children"]->child);
  }
  foreach($asset["children"]->child as $child) {
    if ($child->type == strtolower($asset_children_type)) {
      readPage($client, $auth, array ('type' => $child->type, 'id' => $child->id), $child->type);
    } elseif ($child->type === strtolower($asset_type)) {
      if (foldertest($child))
        readFolder($client, $auth, array ('type' => $child->type, 'id' => $child->id));
    }
  }
}

function readPage($client, $auth, $id, $type) {
  global $asset_type, $asset_children_type, $data;
  $reply = $client->read ( array ('authentication' => $auth, 'identifier' => $id ) );
  if ($reply->readReturn->success == 'true') {
    $asset = ( array ) $reply->readReturn->asset->$asset_children_type;
    if ($_POST['asset'] == 'on') {
      echo '<h4><a href="https://cms.slc.edu:8443/entity/open.act?id='.$asset['id'].'&type='.$type.'#highlight">'.$asset['path']."</a></h4>";
    }
    
    if (edittest($asset)) {
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
      
      editPage($client, $auth, $asset);
    }
    
  } else {
    echo '<div class="f">Failed to read page: '.$id.'</div>';
  }
}


function editPage($client, $auth, $asset) {
  global $total, $asset_type, $asset_children_type, $data, $changed, $configID;
  
  changes($asset);
  
  if ($_POST['after'] == 'on') {
    echo '<input type="checkbox" class="hidden" id="Aexpand'.$asset['id'].'"><label class="fullpage" for="Aexpand'.$asset['id'].'">';
      print_r($asset); // Shows the page as it will be
    echo '</label>';
  }
  
  if ($changed == true) {
    echo $configID;
    
    if ($_POST['action'] == 'edit') {
      $delete = $client->delete ( array ('authentication' => $auth, 'identifier' => array('id' => $configID, 'type' => 'pageconfiguration') ) );
    }
    if ($delete->deleteReturn->success == 'true') {
      echo '<div class="s">Delete success</div>';
      $total['s']++;
    } else {
      echo '<div class="f">Delete failed: '.$asset['path'].'<div>'.extractMessage($result).'</div></div>';
      $total['f']++;
    }
  } else {
    echo '<div class="k">No changes needed</div>';
    $total['k']++;
  }
  
  echo '</div>';
}


?>
