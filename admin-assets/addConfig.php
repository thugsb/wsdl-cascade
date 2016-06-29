<?php
$title = 'Add a config to all CSets';

$type_override = 'pageconfigurationsetcontainer';
$start_asset = '6833b8437f00000101b7715d707702d7';

// Optionally override the container/child types
$asset_type = 'pageConfigurationSetContainer';
$asset_children_type = 'pageConfigurationSet';

function pagetest($child) {
  return true;
}
function foldertest($child) {
  return true;
}
function edittest($asset) {
  return true;
}

function changes(&$asset) {
  global $changed;
  $changed = false;
  $configs = array();
  if(!is_array($asset['pageConfigurations']->pageConfiguration)) {
    $asset['pageConfigurations']->pageConfiguration = array($asset['pageConfigurations']->pageConfiguration);
  }
  foreach ($asset['pageConfigurations']->pageConfiguration as $conf) {
    if (preg_match('/v6/', $conf->name) ) {
      array_push($configs, $conf->name);
    }
  }
  if (in_array('v6', $configs)) {
    echo 'Already got v6.';
  } else {
    echo 'v6 still needs adding!';
    $changed = true;
    
    $newConf = new stdClass();
    $newConf->name = 'v6'; 
    $newConf->templateId = '9c05584fc0a8022b5db2e114c3516d51';
    $newConf->outputExtension = '.html';
    $newConf->serializationType = 'HTML';
    $newConf->publishable = '';
    $newConf->defaultConfiguration = '1';
    array_push($asset['pageConfigurations']->pageConfiguration, $newConf);
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
      echo '<h2><a href="https://cms.slc.edu:8443/entity/open.act?id='.$asset['id'].'&type='.$type.'#highlight">'.$asset['path']."</a></h2>";
    }
    
    if (edittest($asset)) {
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
      
      editPage($client, $auth, $asset);
    }
    
  } else {
    echo '<div class="f">Failed to read page: '.$id.'</div>';
  }
}


function editPage($client, $auth, $asset) {
  global $total, $asset_type, $asset_children_type, $data, $changed;
  
  changes($asset);
  
  if ($_POST['after'] == 'on') {
    echo '<button class="btn" href="#aModal'.$asset['id'].'" data-toggle="modal">View After</button><div id="aModal'.$asset['id'].'" class="modal hide" tabindex="-1" role="dialog" aria-hidden="true"><div class="modal-body">';
      print_r($asset); // Shows the page as it will be
    echo '</div></div>';
  }
  
  if ($changed == true) {
    
    if ($_POST['action'] == 'edit') {
      // $create = $client->create ( array ('authentication' => $auth, 'asset' => $newAsset ) );
      $edit = $client->edit ( array ('authentication' => $auth, 'asset' => array($asset_children_type => $asset) ) );
    }
    if ($edit->editReturn->success == 'true') {
      echo '<div class="s">Edit success</div>';
      $total['s']++;
    } else {
      echo '<div class="f">Edit failed: '.$asset['path'].'<div>'.extractMessage($result).'</div></div>';
      $total['f']++;
    }
  } else {
    echo '<div class="k">No changes needed</div>';
    $total['k']++;
  }
  
  echo '</div>';
}


?>
