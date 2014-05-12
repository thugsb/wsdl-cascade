<?php
date_default_timezone_set('America/New_York');
$title = 'Copy Grad Catalogue Program Index Pages';

// $type_override = 'page';
$start_asset = '4e9e12a97f000001015d84e03ea3fb26';

// Optionally override the container/child types
// $asset_type = 'assetFactoryContainer';
// $asset_children_type = 'assetFactory';

$lastyear = '2013-2014';
$nextyear = '2014-2015';
$descBlockID = '';

function pagetest($child) {
  if (preg_match('/index$/', $child->path->path) && substr_count($child->path->path,'/') == 1)
    return true;
}
function foldertest($child) {
  if (preg_match('/^[a-z]/', $child->path->path) && substr_count($child->path->path,'/') < 1)
    return true;
}
function edittest($asset) {
  // if (substr_count($child->path->path,'/') > 1)
    return true;
}

function changes(&$asset) {
  global $changed, $nextyear, $descBlockID;
  $changed = true;
  
  // echo $descBlockID;
  // 
  // foreach ($asset["structuredData"]->structuredDataNodes->structuredDataNode as $group) {
  //   if ($group->identifier == 'main_column') {
  //     foreach ($group->structuredDataNodes->structuredDataNode as $field) {
  //       if ($field->identifier == 'data-definition-block') {
  //         if ($field->blockId != $descBlockID) {
  //           $field->blockId = $descBlockID;
  //           $field->blockPath = '';
  //           $changed = true;
  //         }
  //       }
  //     }
  //   }
  // }
}


include('../html_header.php');

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
  global $asset_type, $asset_children_type, $data, $o, $cron, $nextyear, $descBlockID;
  if (!is_array($asset["children"]->child)) {
    $asset["children"]->child=array($asset["children"]->child);
  }
  // foreach($asset["children"]->child as $child) {
  //   if (preg_match('/description-'.$nextyear.'$/',$child->path->path)) {
  //     $descBlockID = $child->id;
  //   }
  // }
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
        echo '<button class="btn" href="#bModal'.$asset['id'].'" data-toggle="modal">View Before</button><div id="bModal'.$asset['id'].'" class="modal hide" tabindex="-1" role="dialog" aria-hidden="true"><div class="modal-body">';
          print_r($asset); // Shows the page in all its glory
        echo '</div></div>';
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
  global $total, $asset_type, $asset_children_type, $data, $changed, $o, $cron, $lastyear;
  
  changes($asset);
  
  if ($_POST['after'] == 'on') {
    echo '<button class="btn" href="#aModal'.$asset['id'].'" data-toggle="modal">View After</button><div id="aModal'.$asset['id'].'" class="modal hide" tabindex="-1" role="dialog" aria-hidden="true"><div class="modal-body">';
      print_r($asset); // Shows the page as it will be
    echo '</div></div>';
  }
  
  if ($changed == true) {
    // First copy the index to create the asset for last year...
    if ($_POST['action'] == 'edit') {
      $copy = $client->copy ( array ('authentication' => $auth, 'identifier' => array('type' => $asset_children_type, 'id' => $asset['id']), 'copyParameters' => array('newName'=> $lastyear, 'destinationContainerIdentifier' => array('id' =>$asset['parentFolderId'], type => 'folder'), 'doWorkflow'=>false) ) );
    }
    if ($copy->copyReturn->success == 'true') {
      echo '<div class="s">Copy success</div>';
      $total['s']++;
    } else {
      // $result = $client->__getLastResponse();
      echo '<div class="f">Copy failed: '.$asset['path'].'<div>'.htmlspecialchars(extractMessage($result)).'</div></div>';
      $total['f']++;
    }
    
    // ...Then replace the description block in the index
    // if ($_POST['action'] == 'edit') {
    //   $edit = $client->edit ( array ('authentication' => $auth, 'asset' => array($asset_children_type => $asset) ) );
    // }
    // if ($edit->editReturn->success == 'true') {
    //   echo '<div class="s">Edit success</div>';
    //   $total['s']++;
    // } else {
    //   echo '<div class="f">Edit failed: '.$asset['path'].'<div>'.extractMessage($result).'</div></div>';
    //   $total['f']++;
    // }
  } else {
    echo '<div class="k">No changes needed</div>';
    $total['k']++;
  }
}


?>
