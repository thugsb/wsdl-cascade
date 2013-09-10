<?php
$title = 'Add a MobileIA config to all CSets with Mobile';

$type_override = 'pageconfigurationsetcontainer';
$start_asset = '1697b0d97f00000101f92de526b6ff9b';

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
    if (preg_match('/Mobile/', $conf->name) ) {
      array_push($configs, $conf->name);
    }
  }
  if (in_array('Mobile', $configs)) {
    if (in_array('MobileIA', $configs)) {
      echo 'Already got MobileIA.';
    } else {
      echo 'Mobile, but no IA - it still needs adding!';
      $changed = true;
      
      $newConf = new stdClass();
      $newConf->name = 'MobileIA'; 
      $newConf->templateId = 'a4a773567f00000229bbbce6260f114b';
      $newConf->outputExtension = '-ia.html';
      $newConf->serializationType = 'HTML';
      $newConf->publishable = '1';
      $newConf->defaultConfiguration = '';
      
      
      foreach ($asset['pageConfigurations']->pageConfiguration as $conf) {
        if ($conf->name == 'Mobile') {
          // if(!is_array($conf->pageRegions)) {
          //   $conf->pageRegions = array($conf->pageRegions);
          // }
          // echo count($conf->pageRegions);
          if ( count((array)$conf->pageRegions) ) {
            $regions = array();
            echo '<h3 class="k">Page Regions are customized</h3>';
            if(!is_array($conf->pageRegions->pageRegion)) {
              $conf->pageRegions->pageRegion = array($conf->pageRegions->pageRegion);
            }
            foreach ($conf->pageRegions->pageRegion as $region) {
              if ($region->name == 'DEFAULT') {
                echo '<div class="s">DEFAULT copied as CONTENT</div>';
                $newRegion = clone $region;
                $newRegion->name = 'CONTENT';
                array_push($regions, $newRegion);
              } elseif ($region->name == 'MARKETING-METRICS') {
                echo '<div class="s">MARKETING-METRICS copied as JS-MARKETING</div>';
                $newRegion = clone $region;
                $newRegion->name = 'JS-MARKETING';
                array_push($regions, $newRegion);
              } elseif ($region->name == 'RIGHT_SIDEBAR') {
                echo '<div class="s">RIGHT_SIDEBAR copied</div>';
                $newRegion = clone $region;
                array_push($regions, $newRegion);
              } elseif ($region->name == 'META-FACET') {
                echo '<div class="s">META-FACET copied</div>';
                $newRegion = clone $region;
                array_push($regions, $newRegion);
              } elseif ($region->name == 'LEFT_NAV') {
                echo '<div class="k">LEFT_NAV probably does not matter</div>';
              } else {
                echo '<div class="f">'.$region->name.' region needs attention</div>';
              }
            }
            
            foreach ($regions as $r) {
              unset($r->id);
            }
            
            $newConf->pageRegions = new stdClass();
            $newConf->pageRegions->pageRegion = $regions;
          } else {
            echo '<div class="s">Page Regions are empty</div>';
          }
        }
      }
      
      
      
      array_push($asset['pageConfigurations']->pageConfiguration, $newConf);
      
      
    }
  } else {
    if (preg_grep('/Mobile/', $configs) ) {
      echo '<div class="f">Some other Mobile: ';
        print_r($configs);
      echo '</div>';
    } else {
      echo 'No Mobile config';
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
      print_r($asset); // Shows the page in all its glory
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
