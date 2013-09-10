<?php
$title = 'Add a MobileIA config to all CSets with Mobile';

$type_override = 'pageconfigurationsetcontainer';
// $start_asset = '6833b8437f00000101b7715d707702d7';
$start_asset = '1697b0d97f00000101f92de526b6ff9b'; // All configs

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
  
  if(!is_array($asset['pageConfigurations']->pageConfiguration)) {
    $asset['pageConfigurations']->pageConfiguration = array($asset['pageConfigurations']->pageConfiguration);
  }
  // Copying configset-specific from one config to another
  // foreach ($asset['pageConfigurations']->pageConfiguration as $conf) {
  //   if ($conf->name == 'HTML' ) {
  //     if(!is_array($conf->pageRegions->pageRegion)) {
  //       $conf->pageRegions->pageRegion = array($conf->pageRegions->pageRegion);
  //     }
  //     $exists = false;
  //     if ($conf->pageRegions->pageRegion[0]->id) {
  //       $exists = 1;
  //       foreach ($conf->pageRegions->pageRegion as $region) {
  //         if ($region->name == 'RIGHT_SIDEBAR') {
  //           $newRegion = clone $region;
  //         }
  //       }
  //     }
  //   }
  // }
  // if ($exists == 1) {
  foreach ($asset['pageConfigurations']->pageConfiguration as $conf) {
    if ($conf->name == 'MobileIA' ) {
      if ($conf->publishable != '1' && $conf->outputExtension != '.html') {
        $conf->publishable = '1';
        $conf->outputExtension = '.html';
      
        // Adding pageRegions
        // $regions = array();
        // $content = new stdClass();
        // $content->name = 'CONTENT';
        // $content->blockId = '2aa617d67f0000021312656b001c0b8b';
        // $content->formatId = '6302a46f7f00000101b7715d4dda43ab';
        // array_push($regions, $content);
        // $conf->pageRegions = new stdClass();
        // $conf->pageRegions->pageRegion = $regions;
    
    
        // foreach ($conf->pageRegions->pageRegion as $region) {
        //   if ($region->name == 'CONTENT-BELOW') {
        //     $region->formatId = 'e3c8357f7f00000225ca623f101bacd3';
        //     $region->formatPath = '';
        //   }
        // }
    
        $changed = true;
      }
    }
  }
  // }
}


include('html_header.php');

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
