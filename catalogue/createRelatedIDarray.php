<?php
$title = 'Update relatedIDs.php';

$start_asset = '817373157f00000101f92de5bea1554a';

$year = '2015-2016';

function pagetest($child) {
  global $year;
  // if (preg_match('/^[a-z][-a-z\/]+\/courses\/_archived\/'.$year.'\/primary\/[a-zA-Z]/',$child->path->path))
  if (preg_match('/^[a-z][-a-z\/]+\/'.$year.'\/[a-zA-Z0-9]/',$child->path->path))
    return true;
}
function foldertest($child) {
  global $year;
  // if (preg_match('/^[a-z][-a-z\/]+$/',$child->path->path) || preg_match('/^[a-z][-a-z\/]+\/courses$/',$child->path->path) || preg_match('/^[a-z][-a-z\/]+\/courses\/_archived$/',$child->path->path) || preg_match('/^[a-z][-a-z\/]+\/courses\/_archived\/'.$year.'$/',$child->path->path) || preg_match('/^[a-z][-a-z\/]+\/courses\/_archived\/'.$year.'\/primary$/',$child->path->path))
  if (preg_match('/^[a-z][-a-z\/]+$/',$child->path->path) || preg_match('/^[a-z][-a-z\/]+\/'.$year.'$/',$child->path->path) )
    return true;
}


$disciplines = array();

if (!$cron) {include('../html_header.php');}

print_r($disciplines);

if ($_POST['action'] == 'edit') {
  $str = '<?php $relatedIDs = array(';
  foreach($disciplines as $key => $value) {
    $str .= "'".$key."' => ".$value.",\n";
  }
  $str .= "); ?>";
  file_put_contents("relatedIDs.php", $str);
}

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
  global $asset_type, $asset_children_type, $data, $o, $cron, $total, $disciplines;
  if (!is_array($asset["children"]->child)) {
    $asset["children"]->child=array($asset["children"]->child);
  }
  foreach($asset["children"]->child as $child) {
    if ($child->type === strtolower($asset_type)) {
      if (foldertest($child)) {
        readFolder($client, $auth, array ('type' => $child->type, 'id' => $child->id));
      } elseif (preg_match('/\/related$/', $child->path->path) ) {
        $disciplines[$asset['parentFolderPath']] = "'".$child->id."'";
      }
    }
  }
}



?>
