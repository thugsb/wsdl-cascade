<?php
$title = 'Add access to all grad faculty';

// $type_override = 'page';
$start_asset = '2891e3f87f00000101b7715d1ba2a7fb';

function pagetest($child) {
  if ($child->type == "page" && $child->path->path != 'index' && preg_match('/^[a-z]/',$child->path->path))
    return true;
}
function foldertest($child) {
  return false;
}
function edittest($asset) {
  $grad = false;
  for ($i = 11;$i <= 17;$i = $i+2) {
    if (preg_match('/slc-catalogue-graduate/', $asset["metadata"]->dynamicFields->dynamicField[$i]->fieldValues->fieldValue->value)) {
      $grad = true;
    }
  }
  if ($grad)
    return true;
}

include('html_header.php');



function readFolder($client, $auth, $id) {
  $folder = $client->read ( array ('authentication' => $auth, 'identifier' => $id ) );
  if ($folder->readReturn->success == 'true') {
    $asset = ( array ) $folder->readReturn->asset->folder;
    if ($_POST['folder'] == 'on') {
      echo "<h1>Folder: ".$asset["path"]."</h1>";
    }
    if ($_POST['children'] == 'on') {
      print_r($asset["children"]); // Shows all the children of the folder
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
    if ($child->type == "page") {
      if (pagetest($child))
        readPage($client, $auth, array ('type' => 'page', 'id' => $child->id));
    } elseif ($child->type == "folder") {
      if (foldertest($child))
        readFolder($client, $auth, array ('type' => 'folder', 'id' => $child->id));
    }
  }
}

function readPage($client, $auth, $id) {
  $reply = $client->read ( array ('authentication' => $auth, 'identifier' => $id ) );
  if ($reply->readReturn->success == 'true') {
    $asset = ( array ) $reply->readReturn->asset->page;
    if ($_POST['asset'] == 'on') {
      echo '<h4>'.$asset['path']."</h4>";
    }
    
    if (edittest($asset)) {
      editPage($client, $auth, $asset);
    }
    
  } else {
    echo '<div class="f">Failed to read page: '.$id.'</div>';
  }
}


function editPage($client, $auth, $asset) {
  global $total;
  echo '<div class="page">';
  
  echo "<script type='text/javascript'>var page_".$asset['id']." = ";
  print_r(json_encode($asset));
  echo '; console.log(page_'.$asset['id'].')';
  echo "</script>";
  
  $param = array ("type" => "page", "id" => $asset['id']);
  
  $reply = $client->readAccessRights ( array ('authentication' => $auth, 'identifier' => $param ) );
  if ($reply->readAccessRightsReturn->success == 'true') {
    $accessRightsInformation = $reply->readAccessRightsReturn->accessRightsInformation;
    
    if ($_POST['before'] == 'on') {
      echo '<input type="checkbox" class="hidden" id="Bexpand'.$asset['id'].'"><label class="fullpage" for="Bexpand'.$asset['id'].'">';
        print_r($accessRightsInformation); // Shows the page in all its glory
      echo '</label>';
    }
    
    $accessToAdd = array('level' => 'write', 'type' => 'group', 'name' => 'GradCatalogApprovers');
            
    if (!is_array($accessRightsInformation->aclEntries->aclEntry))
      $accessRightsInformation->aclEntries->aclEntry=array($accessRightsInformation->aclEntries->aclEntry);
    array_push($accessRightsInformation->aclEntries->aclEntry, $accessToAdd);

    if ($_POST['after'] == 'on') {
      echo '<input type="checkbox" class="hidden" id="Aexpand'.$asset['id'].'"><label class="fullpage" for="Aexpand'.$asset['id'].'">';
        print_r($accessRightsInformation); // Shows the page as it will be
      echo '</label>';
    }
    
    if ($_POST['action'] == 'edit') {
      $edit = $client->editAccessRights ( array ('authentication' => $auth, 'accessRightsInformation' => $accessRightsInformation, 'applyToChildren' => false ) );
    }
    
    
    if ($edit->editAccessRightsReturn->success == 'true') {
      echo '<div class="s">Edit rights success</div>';
      $total['s']++;
    } else {
      echo '<div class="f">Edit rights failed: '.$asset['path'].'<div>'.extractMessage($result).'</div></div>';
      $total['f']++;
    }
    
  } else {
    echo '<div class="f">Failed to read access rights of page: '.$asset.'</div>';
  }
  
  echo '</div>';
}


?>