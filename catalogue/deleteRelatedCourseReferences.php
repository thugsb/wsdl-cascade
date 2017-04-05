<?php
date_default_timezone_set('America/New_York');
$title = 'Delete References for Courses that are no longer referenced';

// $type_override = 'page';
$start_asset = '817373157f00000101f92de5bea1554a';

// Optionally override the container/child types
$asset_type = 'folder';
$asset_children_type = 'reference';

include_once('./relatedIDs.php');


$year = '2017-2018';

function referencetest($child) {
  global $year;
  if (preg_match('/^[a-z][-a-z\/]+\/'.$year.'\/related\//',$child->path->path))
    return true;
}
function foldertest($child) {
  global $year;
  if (preg_match('/^[a-z][-a-z\/]+$/',$child->path->path) || preg_match('/^[a-z][-a-z\/]+\/'.$year.'$/',$child->path->path) || preg_match('/^[a-z][-a-z\/]+\/'.$year.'\/related$/',$child->path->path) )
    return true;
}
function edittest($asset) {
  if (preg_match('/[a-z]/', $asset["contentTypePath"]))
    return true;
}

function changes(&$asset) {
  /* If you wish to use $changed, make sure it's global, and set it to false. 
   * When something is changed, it becomes true: */
  global $changed;
  $changed = false;
  // if ($asset["metadata"]->teaser != 'test') {
  //    $changed = true;
  //    $asset["metadata"]->teaser = 'test';
  // }
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
  global $asset_type, $asset_children_type, $data, $o, $cron, $total, $year;
  if (!is_array($asset["children"]->child)) {
    $asset["children"]->child=array($asset["children"]->child);
  }

  foreach($asset["children"]->child as $child) {
    if ($child->type == strtolower($asset_children_type)) {
      if (referencetest($child)) {
        readReference($client, $auth, array ('type' => $child->type, 'id' => $child->id), $asset_children_type);
      }
    } elseif ($child->type === strtolower($asset_type)) {
      if (foldertest($child))
        readFolder($client, $auth, array ('type' => $child->type, 'id' => $child->id));
    }
  }
}

function readReference($client, $auth, $id, $type) {
  global $asset_type, $asset_children_type, $data, $o, $cron, $discNames;
  $reply = $client->read ( array ('authentication' => $auth, 'identifier' => $id ) );
  if ($reply->readReturn->success == 'true') {
    // For some reason the names of asset differ from the returned object
    $returned_type = '';
    foreach ($reply->readReturn->asset as $t => $a) {
      if (!empty($a)) {$returned_type = $t;}
    }
    $asset = [];
    $asset = ( array ) $reply->readReturn->asset->$returned_type;

    if ($cron) {
      $o[3] .= '<h3>Reference: '.$asset['path'].'</h3>';
    } else {
      echo '<h3>Reference: '.$asset['path'].'</h3>';
    }

    preg_match('/[-a-z]+\//', $asset['path'], $disciplinePaths);
    $disciplinePath = str_replace('/','', $disciplinePaths[0] );
    $disciplineName = search_array_values($discNames, $disciplinePath);
    // echo $disciplineName;
    
    readPage($client, $auth, array ('type' => $asset['referencedAssetType'], 'id' => $asset['referencedAssetId']), $disciplineName, $asset );
  }
}

function search_array_values($array, $search) {
  $return = false;
  foreach ($array as $key => $value) {
    if ($search === $value) {
      $return = $key;
      break;
    }
  }
  return $return;
}


function readPage($client, $auth, $id, $disciplineName, $reference) {
  global $asset_type, $asset_children_type, $data, $o, $cron, $total;
  $reply = $client->read ( array ('authentication' => $auth, 'identifier' => $id ) );
  if ($reply->readReturn->success == 'true') {
    // For some reason the names of asset differ from the returned object
    $returned_type = '';
    foreach ($reply->readReturn->asset as $t => $a) {
      if (!empty($a)) {$returned_type = $t;}
    }
    
    $asset = ( array ) $reply->readReturn->asset->$returned_type;
    if ($cron) {
      $o[3] .= '<h4><a href="https://cms.slc.edu:8443/entity/open.act?id='.$asset['id'].'&type='.$id['type'].'#highlight">'.$asset['path']."</a></h4>";
    } elseif ($_POST['asset'] == 'on') {
      $name = '';
      if (!$asset['path']) {$name = $asset['name'];}
      echo '<h4><a href="https://cms.slc.edu:8443/entity/open.act?id='.$asset['id'].'&type='.$id['type'].'#highlight">Related Page: '.$asset['path'].$name."</a></h4>";
    }
    
    $relatedDisciplines = [];
    foreach ($asset["structuredData"]->structuredDataNodes->structuredDataNode as $sdnode) {
      if ($sdnode->identifier == "related") {
        array_push($relatedDisciplines, $sdnode->text);
      }
    }
    // print_r($relatedDisciplines);

    if (in_array($disciplineName, $relatedDisciplines)) {
      if (!$cron) {echo '<div class="s">'.$disciplineName.' is present</div>';}
      $total['k']++;
    } else {
      if (!$cron) {echo '<div class="k">'.$disciplineName.' is NOT present and should be deleted.</div>';}

      deleteReference($reference);
    }
    
  } else {
    if ($cron) {
      $o[1] .= '<div style="padding:3px;color:#fff;background:#c00;">Failed to read page: '.$id->id.'</div>';
    } else {
      echo '<div class="f">Failed to read page: '.$id->id.'. This is probably because the course page was deleted, and so the reference to it will be deleted also.</div>';
    }
    deleteReference($reference);
  }
}

function deleteReference($reference) {
  global $cron, $o, $total, $auth, $client;
  if ($_POST['action'] == 'edit' || $cron) {
    $delete = $client->delete(array ('authentication' => $auth, 'identifier' => array('id' => $reference['id'], 'type' => 'reference') ) );
  }
  if ($delete->deleteReturn->success == 'true') {
    if ($cron) {
      $o[0] .= "<div style='color:#090;'>The reference ".$reference['path']. " was deleted</div>";
    } else {
      echo '<div class="s">Deletion Success</div>';
    }
    $total['s']++;
  } else {
    if ($cron) {
      $o[1] .= "<div style='padding:3px;color:#fff;background:#c00;'>".$reference['path']. " failed to delete</div>";
    } else {
      if ($_POST['action'] == 'edit') {
        $result = $client->__getLastResponse();
        echo '<div class="f">Deletion Failed: '.$reference['path'].'</div>';
      } else {
        echo '<div class="f">Deletion Failed (switch to Edit mode): '.$reference['path'].'<div>'.htmlspecialchars(extractMessage($result)).'</div></div>';
      }
    }
    $total['f']++;
  }
}

?>
