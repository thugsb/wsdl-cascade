<?php
date_default_timezone_set('America/New_York');
$title = 'Tag Catalogue CDP folders with appropriate Area tags.';

// $type_override = 'page';
$start_asset = '817373157f00000101f92de5bea1554a';

$year = "2015-2016";
$disciplines = array();
$arts = array('dance', 'music', 'theatre', 'visual-arts', 'writing');
$hist = array('anthropology', 'asian-studies', 'economics', 'environmental-studies', 'geography', 'history', 'politics', 'psychology', 'public-policy', 'science-technology-and-society', 'sociology');
$hums = array('art-history', 'chinese', 'classics', 'film-history', 'french', 'german', 'greek', 'italian', 'japanese', 'latin', 'literature', 'philosophy', 'religion', 'russian', 'spanish');
$scis = array('biology', 'chemistry', 'computer-science', 'mathematics', 'physics');
// Optionally override the container/child types
// $asset_type = 'assetFactoryContainer';
// $asset_children_type = 'assetFactory';

function pagetest($child) {
  if (preg_match('/^[a-z]/', $child->path->path))
    return true;
}
function foldertest($child) {
  return false;
}
function edittest($asset) {
  if ($asset['parentFolderPath'] == '/')
    return true;
}

function changes(&$asset) {
  global $client, $auth, $year, $disciplines, $arts, $hums, $hist, $scis;
  /* If you wish to use $changed, make sure it's global, and set it to false. 
   * When something is changed, it becomes true: */
  global $changed;
  $changed = false;
  $existing = array();
  foreach ($asset["metadata"]->dynamicFields->dynamicField as $dyn) {
    if ($dyn->name == "areas") {
      if (is_array($dyn->fieldValues->fieldValue) ) {
        foreach($dyn->fieldValues->fieldValue as $value) {
          array_push($existing, $value->value);
        }
      } else {
        array_push($existing, $dyn->fieldValues->fieldValue->value);
      }
    }
  }
  sort($existing);
  foreach ($asset["metadata"]->dynamicFields->dynamicField as $dyn) {
    $cdp = false;
    $disciplines = array();
    $areas = array();
    if ($dyn->name == "areas") {
      if (is_array($dyn->fieldValues->fieldValue) ) {echo 'array';
        foreach($dyn->fieldValues->fieldValue as $value) {
          if ($value->value == "Cross-Disciplinary Paths") {$cdp = true;}
        }
      } else {echo 'object';
        if($dyn->fieldValues->fieldValue->value == "Cross-Disciplinary Paths") {$cdp = true;}
      }
      if($cdp) {
        readFolder($client, $auth, array(type => 'folder', path => array(path => $asset['path'].'/'.$year.'/related', siteName => 'www.sarahlawrence.edu+catalogue') ) );
        $disciplines = array_unique($disciplines);
        print_r($disciplines);
        foreach($disciplines as $disc) {
          if (in_array($disc, $arts) ) {array_push($areas, 'Creative and Performing Arts');}
          if (in_array($disc, $hist) ) {array_push($areas, 'History and the Social Sciences');}
          if (in_array($disc, $hums) ) {array_push($areas, 'Humanities');}
          if (in_array($disc, $scis) ) {array_push($areas, 'Science and Mathematics');}
        }
        array_push($areas, 'Cross-Disciplinary Paths');
        $areas = array_unique($areas);
        sort($areas);
        if ($existing == $areas) {
          echo 'MATCH';
        } else {
          $dyn->fieldValues->fieldValue = array();
          foreach($areas as $area) {
            $val = new stdClass();
            $val->value = $area;
            array_push($dyn->fieldValues->fieldValue, $val);
          }
          $changed = true;
        }
      }
    }
  }
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
  foreach($asset["children"]->child as $child) {
    if ($child->type === 'folder') {
      if (pagetest($child))
        readPage($client, $auth, array ('type' => $child->type, 'id' => $child->id), $child->type);
    } elseif ($child->type === 'reference') {
      readPage($client, $auth, array ('type' => $child->type, 'id' => $child->id), $child->type, true);
    }
  }
}

function readPage($client, $auth, $id, $type, $reference = false) {
  global $asset_type, $asset_children_type, $data, $o, $cron, $disciplines;
  $reply = $client->read ( array ('authentication' => $auth, 'identifier' => $id ) );
  if ($reply->readReturn->success == 'true') {
    // For some reason the names of asset differ from the returned object
    $returned_type = '';
    foreach ($reply->readReturn->asset as $t => $a) {
      if (!empty($a)) {$returned_type = $t;}
    }
    
    $asset = ( array ) $reply->readReturn->asset->$returned_type;
    
    if ($reference) {
      array_push($disciplines, current(explode("/", $asset['referencedAssetPath'])));
    }
    
    if ($cron) {
      $o[3] .= '<h4><a href="'.CMS_OPEN_PATH.$asset['id'].'&type='.$type.'#highlight">'.$asset['path']."</a></h4>";
    } elseif ($_POST['asset'] == 'on') {
      $name = '';
      if (!$asset['path']) {$name = $asset['name'];}
      echo '<h4><a href="'.CMS_OPEN_PATH.$asset['id'].'&type='.$type.'#highlight">'.$asset['path'].$name."</a></h4>";
    }
    
    if (edittest($asset)) {
      if (!$cron) {echo '<div class="page">';}
      if ($_POST['before'] == 'on' && !$cron) {
        echo '<button class="btn" href="#bModal'.$asset['id'].'" data-toggle="modal">View Before</button><div id="bModal'.$asset['id'].'" class="modal hide" tabindex="-1" role="dialog" aria-hidden="true"><div class="modal-body">';
          print_r($asset); // Shows the page in all its glory
        echo '</div></div>';
      }

      if (!$cron) {
        echo "<script type='text/javascript'>var page_".$asset['id']." = ";
        print_r(json_encode($asset));
        echo '; console.log(page_'.$asset['id'].')';
        echo "</script>";
      }
      
      editPage($client, $auth, $asset);
      if (!$cron) {echo '</div>';}
    }
    
  } else {
    if ($cron) {
      $o[1] .= '<div style="padding:3px;color:#fff;background:#c00;">Failed to read page: '.$id.'</div>';
    } else {
      echo '<div class="f">Failed to read page: '.$id.'</div>';
    }
  }
}


function editPage($client, $auth, $asset) {
  global $total, $asset_type, $asset_children_type, $data, $changed, $o, $cron;
  
  changes($asset);
  
  if ($_POST['after'] == 'on' && !$cron) {
    echo '<button class="btn" href="#aModal'.$asset['id'].'" data-toggle="modal">View After</button><div id="aModal'.$asset['id'].'" class="modal hide" tabindex="-1" role="dialog" aria-hidden="true"><div class="modal-body">';
      print_r($asset); // Shows the page as it will be
    echo '</div></div>';
  }
  
  if ($changed == true) {
    if ($_POST['action'] == 'edit') {
      $edit = $client->edit ( array ('authentication' => $auth, 'asset' => array('folder' => $asset) ) );
    }
    if ($edit->editReturn->success == 'true') {
      if ($cron) {
        $o[2] .= '<div style="color:#090;">Edit success: <a href="'.CMS_OPEN_PATH.$asset['id'].'&type='.$type.'#highlight">'.$asset['path']."</a></div>";
      } else {
        echo '<div class="s">Edit success</div>';
      }
      $total['s']++;
    } else {
      if ($_POST['debug'] == 'on') {
        $result = $client->__getLastResponse();
      }
      if ($cron) {
        $o[1] .= '<div style="padding:3px;color:#fff;background:#c00;">Edit failed: <a href="'.CMS_OPEN_PATH.$asset['id'].'&type='.$type.'#highlight">'.$asset['path']."</a><div>".htmlspecialchars(extractMessage($result)).'</div></div>';
      } else {
        echo '<div class="f">Edit failed: '.$asset['path'].'<div>'.htmlspecialchars(extractMessage($result)).'</div></div>';
      }
      $total['f']++;
    }
  } else {
    if (!$cron) {echo '<div class="k">No changes needed</div>';}
    $total['k']++;
  }
}

?>
