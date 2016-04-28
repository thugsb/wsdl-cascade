<?php
date_default_timezone_set('America/New_York');
$title = 'Move Archived Faculty to the _inactive folder';

// $type_override = 'page';
$start_asset = '2891e3f87f00000101b7715d1ba2a7fb';

// Optionally override the container/child types
// $asset_type = 'assetFactoryContainer';
// $asset_children_type = 'assetFactory';

function pagetest($child) {
  if (isset($_GET['name'])) {
    if (preg_match("/^".$_GET['name']."[a-z]/",$child->path->path)) {
      return true;
    }
  } else {
    if ($child->path->path != 'index' && preg_match('/^[a-z]/',$child->path->path)) {
      return true;
    }
  }
}
function foldertest($child) {
  return false;
}
function edittest($asset) {
  return true;
}

function changes(&$asset) {
  global $changed, $total, $cron;
  $changed = false;
  foreach ($asset["metadata"]->dynamicFields->dynamicField as $dyn) {
    if ($dyn->name == 'status') {
      if ($dyn->fieldValues->fieldValue->value != 'Active') {
        $changed = true;
      }
    }
  }
}

if (!$cron)
  include('../html_header.php');



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
    $total['f']++;
  }
}
function indexFolder($client, $auth, $asset) {
  global $data, $o, $cron;
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
  global $asset_type, $asset_children_type, $data, $o, $cron;
  $reply = $client->read ( array ('authentication' => $auth, 'identifier' => $id ) );
  if ($reply->readReturn->success == 'true') {
    $asset = ( array ) $reply->readReturn->asset->$asset_children_type;
    if ($cron) {
      $o[3] .= '<h4><a href="https://cms.slc.edu:8443/entity/open.act?id='.$asset['id'].'&type='.$type.'#highlight">'.$asset['path']."</a></h4>";
    } elseif ($_POST['asset'] == 'on') {
      echo '<h4>'.$asset['path']."</h4>";
    }
    
    if (edittest($asset)) {
      editPage($client, $auth, $asset);
    }
    
  } else {
    if ($cron) {
      $o[1] .= '<div style="padding:3px;color:#fff;background:#c00;">Failed to read page: '.$id.'</div>';
    } else {
      echo '<div class="f">Failed to read page: '.$id.'</div>';
    }
    $total['f']++;
  }
}


function editPage($client, $auth, $asset) {
  global $total, $asset_type, $asset_children_type, $data, $changed, $o, $cron;
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
  
  changes($asset);
  
  if ($_POST['after'] == 'on' && !$cron) {
    echo '<button class="btn" href="#aModal'.$asset['id'].'" data-toggle="modal">View After</button><div id="aModal'.$asset['id'].'" class="modal hide" tabindex="-1" role="dialog" aria-hidden="true"><div class="modal-body">';
      print_r($asset); // Shows the page as it will be
    echo '</div></div>';
  }

  if ($changed == true) {
    if (!$cron) {echo '<div class="f">This faculty is inactive</div>';}
    if ($_POST['action'] == 'edit' || $cron) {
      $publish = $client->publish ( array ('authentication' => $auth, 'publishInformation' => array('identifier' => array('type' => 'page', 'id' => $asset["id"]), 'unpublish' => true ) ) );
      if ($publish->publishReturn->success == 'true') {
        if ($cron) {
          $o[2] .= $asset['name'].' was unpublished<br>';
        } else {
          echo '<div class="s">'.$asset['name'].' was unpublished</div>';
        }
        $total['s']++;
      } else {
        if ($cron) {
          $o[1] .= $asset['name'].' FAILED to unpublish<br>';
        } else {
          echo '<div class="f">'.$asset['name'].' could not be unpublished</div>';
          print_r($publish);
        }
        $total['f']++;
      }
      
      sleep(5);
      $move = $client->move ( array ('authentication' => $auth, 'identifier' => array('type' => 'page', 'id' => $asset["id"]), 'moveParameters' => array('destinationContainerIdentifier'=> array('type'=>'folder', 'id'=>'6824bab27f00000101b7715d4c99fd4c'), 'doWorkflow'=>false) ) );
      if ($move->moveReturn->success == 'true') {
        if ($cron) {
          $o[2] .= '<div style="color:#090;">Move success: <a href="https://cms.slc.edu:8443/entity/open.act?id='.$asset['id'].'&type='.$type.'#highlight">'.$asset['path']."</a></div>";
          $headers = 'From: com@vm-www.slc.edu' . "\r\n" . 'Content-type: text/html; charset=UTF-8';
          mail("45bd091b@opayq.com", "SLC Faculty Member Archived", "https://cms.slc.edu:8443/entity/open.act?id=".$asset['id']."&type=page", $headers);
        } else {
          echo '<div class="s">Move success</div>';
        }
        $total['s']++;
      } else {
        if ($cron) {
          $o[1] .= '<div style="padding:3px;color:#fff;background:#c00;">Move failed: <a href="https://cms.slc.edu:8443/entity/open.act?id='.$asset['id'].'&type='.$type.'#highlight">'.$asset['path']."</a><div>".extractMessage($result).'</div></div>';
        } else {
          echo '<div class="f">Move failed: '.$asset['path'].'<div>'.extractMessage($result).'</div></div>';
        }
        $total['f']++;
      }
    }
  } else {
    if (!$cron) {echo '<div class="s">This faculty is active</div>';}
  }
  
  
  if (!$cron) {echo '</div>';}
  

}


?>