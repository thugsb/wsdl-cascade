<?php
$title = 'Publish courses.html or faculty.html for grad programs (or any other page).';

$type_override = 'page';

// grad courses.html pages:
$start_asset = '105d3f587f00000200360e4672462506,5789de757f000002007fee41c1295355,57a1b6e87f000002007fee4180bedb02,b393a4297f0000026beb496643b784ac,57ac34137f000002007fee4170c05dd1,57ccda5b7f000002007fee4165ab4642,57ee228c7f000002007fee412a4478e4,57fbd3837f000002007fee41a6baf751,581edfd07f000002007fee4112a8f5a0';
// grad faculty.html pages:
// $start_asset = '1064a1bd7f00000200360e46cf00c479,5789e0457f000002007fee41fec2874a,57a1b85c7f000002007fee41d613af39,b395014b7f0000026beb496633af898c,57ac35b07f000002007fee41dde95491,57cce4407f000002007fee41f3ab3870,57ee24307f000002007fee41da9a788a,57fbd5477f000002007fee415e3cdec3,581ee16b7f000002007fee416b3533a5';

$dest = 'pending.slc.edu';

// Find HELP at http://help.hannonhill.com/discussions/web-services/1531-publishing-destinations-via-script

function pagetest($child) {
  return true;
}
function foldertest($child) {
  if (preg_match('/^[a-z][-a-z]+$/',$child->path->path))
    return true;
}
function edittest($asset) {
  return true;
}

function changes(&$asset) {

}


include('../html_header.php');


function readFolder($client, $auth, $id) {
  $folder = $client->read ( array ('authentication' => $auth, 'identifier' => $id ) );
  if ($folder->readReturn->success == 'true') {
    $asset = ( array ) $folder->readReturn->asset->folder;
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
  global $total, $dest;
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
  
  changes($asset);
  
  if ($_POST['after'] == 'on') {
    echo '<button class="btn" href="#aModal'.$asset['id'].'" data-toggle="modal">View After</button><div id="aModal'.$asset['id'].'" class="modal hide" tabindex="-1" role="dialog" aria-hidden="true"><div class="modal-body">';
      print_r($asset); // Shows the page as it will be
    echo '</div></div>';
  }
  
  if ($_POST['action'] == 'edit') {
    $publish = $client->publish ( array ('authentication' => $auth, 'publishInformation' => array('identifier' => array( 'type' => 'page', 'id' => $asset["id"] ), 'destinations' => array( array('type' => 'destination', 'path' => array( 'path' => $dest, 'siteId' => $asset["siteId"] ) ) ), 'unpublish' => false ) ) );
  }
  
  
  if ($publish->publishReturn->success == 'true') {
    echo '<div class="s">Publish success</div>';
    $total['s']++;
  } else {
    echo '<div class="f">Publish failed: '.$asset['path'].'<div>'.extractMessage($result).'</div></div>';
    $total['f']++;
  }
  echo '</div>';
}


?>