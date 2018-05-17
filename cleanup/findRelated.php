<?php
date_default_timezone_set('America/New_York');
$title = 'Find pages that aren\'t related';

// $type_override = 'page';
$start_asset = 'cbd123b87f00000201c142c2cc9bf017';

$output_filename = 'offices-services.html';


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
  global $asset_type, $asset_children_type, $data, $o, $cron;
  if (!is_array($asset["children"]->child)) {
    $asset["children"]->child=array($asset["children"]->child);
  }
  foreach($asset["children"]->child as $child) {
    if ($child->type == strtolower($asset_children_type)) {
      if (pagetest($child))
        readPage($client, $auth, array ('type' => $child->type, 'id' => $child->id), $child->type, $child->path->path);
    } elseif ($child->type === strtolower($asset_type)) {
      if (foldertest($child))
        readFolder($client, $auth, array ('type' => $child->type, 'id' => $child->id));
    }
  }
}

function readPage($client, $auth, $id, $type, $path) {
  global $asset_type, $asset_children_type, $data, $o, $cron, $output_filename, $file_list;
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
        echo '<h4>This has subscribers: <a href="'.CMS_ENTITY_PATH.'relationships.act?id='.$id['id'].'&type='.$type.'#highlight">'.$path."</a></h4>";
        echo '</div>';
        
        if ($_POST['action'] == 'edit') {
          $myFile = "indexes/relationships/$output_filename.html";
          $fh = fopen($myFile, 'a+') or die("can't open file");
          $contents = fread($fh, 13421772 );
          if ( !preg_match('/charset/', $contents) ) {
            fwrite($fh, "<!DOCTYPE html>\n<html>\n<head>\n  <meta content='text/html; charset=UTF-8' http-equiv='Content-Type'/>\n</head>\n<body>\n");
          }
          $str = "<hr/><div><a href='".CMS_OPEN_PATH.$id['id']."&type=page#highlight'>".$path."</a></div>\n";
          
          if (!is_array($reply->listSubscribersReturn->subscribers->assetIdentifier)) {
            $reply->listSubscribersReturn->subscribers->assetIdentifier=array($reply->listSubscribersReturn->subscribers->assetIdentifier);
          }
          foreach ($reply->listSubscribersReturn->subscribers->assetIdentifier as $relatedPage) {
            $related = "<div>Related to <a href='".CMS_OPEN_PATH.$relatedPage->id."&type=".$relatedPage->type."#highlight'>".$relatedPage->path->siteName.'/'.$relatedPage->path->path."</a></div>\n";
            echo $related;
            $str .= $related;
          }
          if (fwrite($fh, $str)) {echo "<div class='s'>Added</div>";}
          fclose($fh);
        }
      }
    } else {
      echo '<h4>No subscribers: <a href="'.CMS_ENTITY_PATH.'relationships.act?id='.$id['id'].'&type='.$type.'#highlight">'.$path."</a></h4>";
      
      
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
  return;
}

?>
