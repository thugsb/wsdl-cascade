<?php
date_default_timezone_set('America/New_York');
$title = 'Find pages that aren\'t related';

// $type_override = 'page';
$start_asset = 'c621c0d17f00000101f92de5212d40b7';

$site_folder = 'about';

// du -a > filename.txt to generate the file.
$myFile = "indexes/relationships/$site_folder.txt";
$fh = fopen($myFile, 'r') or die("can't open file");
$file_list = fread($fh, filesize($myFile));
fclose($fh);

// Optionally override the container/child types
$asset_type = 'folder';
$asset_children_type = 'file';

function pagetest($child) {
  if (preg_match('/[a-z]/', $child->path->path))
    return true;
}
function foldertest($child) {
  if (preg_match('/[a-z]/', $child->path->path))
    return true;
}
function edittest($asset) {
  return false;
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

if (!$cron) {include('html_header.php');}



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
  global $asset_type, $asset_children_type, $data, $o, $cron, $site_folder, $file_list;
  $reply = $client->listSubscribers ( array ('authentication' => $auth, 'identifier' => $id ) );
  if ($reply->listSubscribersReturn->success == 'true') {

    if ($reply->listSubscribersReturn->subscribers->assetIdentifier) {
      if (!$cron) {
        echo '<div class="page clearfix">';
        if ($_POST['before'] == 'on') {
          echo '<button class="btn pull-right" href="#iModal'.$id['id'].'" data-toggle="modal">View Subscribers ('.count($reply->listSubscribersReturn->subscribers->assetIdentifier).')</button><div id="iModal'.$id['id'].'" class="modal hide" tabindex="-1" role="dialog" aria-hidden="true"><div class="modal-body">';
            print_r($reply); // Shows the page in all its glory
          echo '</div></div>';
        }
        echo '<h4>This has subscribers: <a href="https://cms.slc.edu:8443/entity/relationships.act?id='.$id['id'].'&type='.$type.'#highlight">'.$id['id']."</a></h4>";
        echo '</div>';
      }
    } else {
      $read = $client->read ( array ('authentication' => $auth, 'identifier' => $id ) );
      if ($read->readReturn->success == 'true') {
        // For some reason the names of asset differ from the returned object
        $returned_type = '';
        foreach ($read->readReturn->asset as $t => $a) {
          if (!empty($a)) {$returned_type = $t;}
        }
        
        $asset = ( array ) $read->readReturn->asset->$returned_type;
        if ($cron) {
          $o[3] .= '<h4><a href="https://cms.slc.edu:8443/entity/relationships.act?id='.$asset['id'].'&type='.$type.'#highlight">'.$asset['path']."</a></h4>";
        } elseif ($_POST['asset'] == 'on') {
          $name = '';
          if (!$asset['path']) {$name = $asset['name'];}
          echo '<h4><a href="https://cms.slc.edu:8443/entity/relationships.act?id='.$asset['id'].'&type='.$type.'#highlight">No Subscribers: '.$asset['path'].$name."</a></h4>";
        }

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
        
        if ($_POST['action'] == 'edit') {
          $myFile = "indexes/relationships/$site_folder.html";
          $fh = fopen($myFile, 'a+') or die("can't open file");
          $contents = fread($fh, 13421772 );
          if ( !preg_match('/charset/', $contents) ) {
            fwrite($fh, "<!DOCTYPE html>\n<html>\n<head>\n  <meta content='text/html; charset=UTF-8' http-equiv='Content-Type'/>\n</head>\n<body>\n");
          }
          $str = "<div><a href='https://www.sarahlawrence.edu/$site_folder/".$asset['path']."'>&raquo;</a>";
          if ( !strstr($file_list, $asset['path'])) {
            $str .= " x";
          }
          $str .= " <a href='https://cms.slc.edu:8443/entity/relationships.act?id=".$asset['id']."&type=file#highlight'>".$asset['siteName'].'://'.$asset['path']."</a></div>\n";
          fwrite($fh, $str);
          fclose($fh);
        }

        if (edittest($asset)) {
          editPage($client, $auth, $asset);
        }
        if (!$cron) {echo '</div>';}
        
      }
      
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
      $edit = $client->edit ( array ('authentication' => $auth, 'asset' => array($asset_children_type => $asset) ) );
    }
    if ($edit->editReturn->success == 'true') {
      if ($cron) {
        $o[2] .= '<div style="color:#090;">Edit success: <a href="https://cms.slc.edu:8443/entity/open.act?id='.$asset['id'].'&type='.$type.'#highlight">'.$asset['path']."</a></div>";
      } else {
        echo '<div class="s">Edit success</div>';
      }
      $total['s']++;
    } else {
      if ($_POST['debug'] == 'on') {
        $result = $client->__getLastResponse();
      }
      if ($cron) {
        $o[1] .= '<div style="padding:3px;color:#fff;background:#c00;">Edit failed: <a href="https://cms.slc.edu:8443/entity/open.act?id='.$asset['id'].'&type='.$type.'#highlight">'.$asset['path']."</a><div>".htmlspecialchars(extractMessage($result)).'</div></div>';
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
