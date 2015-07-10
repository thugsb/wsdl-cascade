<?php
date_default_timezone_set('America/New_York');
$title = 'Delete References for Courses';

// $type_override = 'page';
$start_asset = '817373157f00000101f92de5bea1554a';

// Optionally override the container/child types
$asset_type = 'folder';
$asset_children_type = 'reference';

$year = '2015-2016';

function pagetest($child) {
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
  $refcount = count($asset["children"]->child);
  $delcount = 0;
  foreach($asset["children"]->child as $child) {
    if ($child->type == strtolower($asset_children_type)) {
      if (pagetest($child)) {
        if ($_POST['action'] == 'edit') {
          $delete = $client->delete(array ('authentication' => $auth, 'identifier' => array('id' => $child->id, 'type' => 'reference') ) );
        }
        if ($delete->deleteReturn->success == 'true') {
          $delcount++;
        } else {
          if ($_POST['action'] == 'edit' || $cron) {$result = $client->__getLastResponse();} else {$result = '';}
          if ($cron) {
            $o[1] .= "<div style='padding:3px;color:#fff;background:#c00;'>".$child->path->path. " failed to delete</div>";
          }
          echo '<div class="f">Deletion Failed: '.$child->path->path.'<div>'.htmlspecialchars(extractMessage($result)).'</div></div>';
          $total['f']++;
        }
      }
    } elseif ($child->type === strtolower($asset_type)) {
      if (foldertest($child))
        readFolder($client, $auth, array ('type' => $child->type, 'id' => $child->id));
    }
  }
  if (preg_match('/^[a-z][-a-z\/]+\/'.$year.'\/related$/',$asset['path'])  && $asset["children"]->child[0]->id) {
    if ($delcount == $refcount) {
      if ($cron) {
        $o[0] .= "<div style='color:#090'>$delcount of $refcount references were deleted in ".$asset['path'].".</div>";
      } else {
        echo "<div class='s'>$delcount of $refcount references were deleted in ".$asset['path'].".</div>";
      }
      $total['s']++;
    } else {
      if ($cron) {
        $o[0] .= "<div style='padding:3px;color:#fff;background:#c00;'>Only $delcount of $refcount references were deleted in ".$asset['path'].".</div>";
      } else {
        echo "<div class='f'>$delcount of $refcount references were deleted in ".$asset['path'].".</div>";
      }
    }
  }
}


?>
