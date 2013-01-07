<?php
date_default_timezone_set('America/New_York');
$title = 'Move past events into _archived';

// $type_override = 'page';
$start_asset = '682153de7f00000269720b2a7e4cd04f';

// Optionally override the container/child types
// $asset_type = 'assetFactoryContainer';
// $asset_children_type = 'assetFactory';

function pagetest($child) {
  if (preg_match('/[a-z]/', $child->path->path) )
    return true;
}
function foldertest($child) {
  return false;
}
function edittest($asset) {
  if (preg_match('/Level 2 Event/', $asset["contentTypePath"]))
    return true;
}

function changes(&$asset) {
  global $changed, $total;
  $changed = false;
  
  $event_date = strtotime( substr($asset['name'],0,10) );
  // echo $event_date.'<br> ';
  // echo time().'<br> ';
  // echo time()-24*3600;
  if ($event_date < time()-48*3600) {
    $changed = true;
  } else {
    echo '<div class="k">This event is too recent, exiting...</div>';
    $total['k']++;
    echo '<div class="totals">Successes: '.$total['s'].' Failures: '.$total['f'].' Skipped: '.$total['k'].'</div>';
    exit;
  }
}


include('html_header.php');

?>


<?php

function readFolder($client, $auth, $id) {
  global $asset_type, $asset_children_type, $data, $o, $cron;
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
  global $asset_type, $asset_children_type, $data, $o, $cron;
  if (!is_array($asset["children"]->child)) {
    $asset["children"]->child=array($asset["children"]->child);
  }
  foreach($asset["children"]->child as $child) {
    if ($child->type == strtolower($asset_children_type)) {
      if (pagetest($child))
        readPage($client, $auth, array ('type' => $child->type, 'id' => $child->id), $child->type);
    } elseif ($child->type === strtolower($asset_type)) {
      if (foldertest($child))
        readFolder($client, $auth, array ('type' => $child->type, 'id' => $child->id));
    }
  }
}

function readPage($client, $auth, $id, $type) {
  global $asset_type, $asset_children_type, $data, $o, $cron;
  $reply = $client->read ( array ('authentication' => $auth, 'identifier' => $id ) );
  if ($reply->readReturn->success == 'true') {
    $asset = ( array ) $reply->readReturn->asset->$asset_children_type;
    if ($_POST['asset'] == 'on') {
      $name = '';
      if (!$asset['path']) {$name = $asset['name'];}
      echo '<h4><a href="https://cms.slc.edu:8443/entity/open.act?id='.$asset['id'].'&type='.$type.'#highlight">'.$asset['path'].$name."</a></h4>";
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
      echo '</div>';
    }
    
  } else {
    echo '<div class="f">Failed to read page: '.$id.'</div>';
  }
}


function editPage($client, $auth, $asset) {
  global $total, $asset_type, $asset_children_type, $data, $changed, $o, $cron;
  
  changes($asset);
  
  if ($_POST['after'] == 'on') {
    echo '<input type="checkbox" class="hidden" id="Aexpand'.$asset['id'].'"><label class="fullpage" for="Aexpand'.$asset['id'].'">';
      print_r($asset); // Shows the page as it will be
    echo '</label>';
  }

  if ($changed == true) {
    if ($_POST['action'] == 'edit') {
      $move = $client->move ( array ('authentication' => $auth, 'identifier' => array('type' => 'page', 'id' => $asset["id"]), 'moveParameters' => array('destinationContainerIdentifier'=> array('type'=>'folder', 'id'=>'1507b8a37f000002357a73247c2b2ab9'), 'doWorkflow'=>false) ) );
    }
    if ($move->moveReturn->success == 'true') {
      if ($cron) {
        $o[2] .= '<div style="color:#090;">Move success: <a href="https://cms.slc.edu:8443/entity/open.act?id='.$asset['id'].'&type='.$type.'#highlight">'.$asset['path']."</a></div>";
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
  } else {
    echo '<div class="k">No changes needed</div>';
    $total['k']++;
  }
}


?>
