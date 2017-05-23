<?php
date_default_timezone_set('America/New_York');
$title = 'Import the WSDL scripts from the local machine into Cascade CMS';

/*
In this script, we read the folder in cascade, and then compare the children to
the folder that gets read locally. If the file doesn't exist, create it, and edit
it to fill in the content. If it does exist, check whether it needs to be changed,
and if so, edit it.
The script then needs to work recursively through the subfolders.
If a new folder is added, the script will need to be run twice in order for
the contained files to be imported.
*/

// $type_override = 'page';
$start_asset = '09581f787f000002315b5b685aeacf4a';

// Optionally override the container/child types
$asset_type = 'folder';
$asset_children_type = 'file';

$message = 'Warning: This script only copes with a single layer of subfolders';

if (file_exists('/Users/stu')) {
  $path = '/Users/stu/Sites/web-services/';
} else {
  $path = '/srv/www/htdocs/__web-services/';
}

function pagetest($child) {
  return true;
}
function foldertest($child) {
  return true;
}
function edittest($asset) {
  return true;
}

function changes(&$asset, $code) {
  /* If you wish to use $changed, make sure it's global, and set it to false. 
   * When something is changed, it becomes true: */
  global $changed;
  $changed = false;
  if ($asset["data"] != $code) {
     $changed = true;
     $asset["text"] = $code;
  }
}

if (!$cron) {include('html_header.php');}

function readScripts($link, $files, $folders, $subFolder) {
  global $asset_type, $asset_children_type, $client, $auth, $total, $cron, $o;
  if ($subFolder == '') {$folderPath = '/';} else {$folderPath = '/'.$subFolder.'/';}
  if ($subFolder == '') {$entryPath = '';} else {$entryPath = $subFolder.'/';}
  if ($handle = opendir($link)) {
    /* This is the correct way to loop over the directory. */
    while (false !== ($entry = readdir($handle))) {
      if (!preg_match('/^\./', $entry)) {
        if (is_dir($entry) ) {
          $o[0] .= "<h4>$entry is a folder";
          if (!in_array("__web-services".$folderPath."$entry", $folders) ) {
            
            if ($cron) {
              $o[4] .= "<h4>$entry folder doesn't exist.</h4>";
            } elseif ($_POST['folder'] == 'on') {
              echo "<div class='k'>$entry folder doesn't exist.</div>\n";
            }
            
            if ($_POST['action'] == 'edit' || $cron) {
              createFolder($entry, $subFolder);
            }
            
          }
        } else {
          $o[0] .= "<h4>$entry is NOT a folder";
          if (!in_array("__web-services".$folderPath."$entry", $files) ) {
            
            if ($cron) {
              $o[3] .= "<h4>$entry file doesn't exist.</h4>";
            } elseif ($_POST['folder'] == 'on') {
              echo "<div class='k'>$entry file doesn't exist.</div>\n";
            }
            
            if ($_POST['action'] == 'edit' || $cron) {
              createFile($entry, $subFolder);
            }
          } else {
            if (file_exists($entryPath.$entry) ) {
              $code = file_get_contents($entryPath.$entry);
              readPage($client, $auth, array ('type' => $asset_children_type, 'path' => array ('path' => "__web-services".$folderPath.$entry, 'siteName' => 'Cascade - Scripting and Maintenance') ), $asset_children_type, $entry, $code);
            } else {
              if ($cron) {
                $o[1] .= "<h4>File not found: $entry</h4>";
              } elseif ($_POST['folder'] == 'on') {
                echo "<div class='f'>File not found: $entry</div>";
              }
              $total['f']++;
            }
          }
        }
      }
    }
    closedir($handle);
  }
}

function createFile($entry, $subFolder) {
  global $asset_type, $asset_children_type, $client, $auth, $total, $cron, $o;
  if ($subFolder == '') {$containerPath = '';} else {$containerPath = '/'.$subFolder;}
  if ($subFolder == '') {$folderPath = '/';} else {$folderPath = '/'.$subFolder.'/';}
  $destFolder = array ('type' => 'folder', 'path' => array ('path' => "__web-services".$containerPath, 'siteName' => 'Cascade - Scripting and Maintenance') );
  $copyParams = array ("newName" => $entry, 'destinationContainerIdentifier' => $destFolder, "doWorkflow" => false);
  $baseAsset = array ('type' => 'file', 'id' => '09b331277f000002315b5b68fdc840a1' );
  $params = array ('authentication' => $auth, 'identifier' => $baseAsset, 'copyParameters' => $copyParams );

  if ($_POST['action'] == 'edit' || $cron) {
    $reply = $client->copy ( $params );
  }
  
  if ($reply->copyReturn->success == 'true') {
    if ($cron) {
      $o[2] .= "<h4>Copy success: $entry</h4>";
    } elseif ($_POST['folder'] == 'on') {
      echo '<div class="s">Copy success: '.$entry.'</div>';
    }
    $asset = ( array ) $reply->readReturn->asset->folder;
    $total['s']++;
    
    if (file_exists($entry) ) {
      $code = file_get_contents($entry);
      readPage($client, $auth, array ('type' => $asset_children_type, 'path' => array ('path' => "__web-services".$folderPath.$entry, 'siteName' => 'Cascade - Scripting and Maintenance') ), $asset_children_type, $entry, $code);
    } else {
      if ($cron) {
        $o[1] .= "<h4>File not found: $entry</h4>";
      } elseif ($_POST['folder'] == 'on') {
        echo "<div class='f'>File not found: $entry</div>";
      }
      $total['f']++;
    }
  } else {
    if ($_POST['debug'] == 'on') {
      $result = $client->__getLastResponse();
    }
    if ($cron) {
      $o[1] .= '<div style="padding:3px;color:#fff;background:#c00;">Edit failed: '.$entry."<div>".htmlspecialchars(extractMessage($result)).'</div></div>';
    } else {
      echo '<div class="f">Copy Failed: '.$entry.'<div>'.htmlspecialchars(extractMessage($result)).'</div></div>';
    }
    $total['f']++;
  }
}
function createFolder($entry, $subFolder) {
  global $asset_type, $asset_children_type, $client, $auth, $total, $cron, $o;
  if ($subFolder == '') {$containerPath = '';} else {$containerPath = '/'.$subFolder;}
  if ($subFolder == '') {$folderPath = '/';} else {$folderPath = '/'.$subFolder.'/';}
  $destFolder = array ('type' => 'folder', 'path' => array ('path' => "__web-services".$containerPath, 'siteName' => 'Cascade - Scripting and Maintenance') );
  $copyParams = array ("newName" => $entry, 'destinationContainerIdentifier' => $destFolder, "doWorkflow" => false);
  $baseAsset = array ('type' => 'folder', 'id' => '09b3c7fe7f000002315b5b68c2dce107' );
  $params = array ('authentication' => $auth, 'identifier' => $baseAsset, 'copyParameters' => $copyParams );

  if ($_POST['action'] == 'edit' || $cron) {
    $reply = $client->copy ( $params );
  }
  
  if ($reply->copyReturn->success == 'true') {
    if ($cron) {
      $o[2] .= "<h4>Copy success: $entry</h4>";
    } elseif ($_POST['folder'] == 'on') {
      echo '<div class="s">Copy success: '.$entry.'</div>';
    }
    $asset = ( array ) $reply->readReturn->asset->folder;
    $total['s']++;
  } else {
    if ($_POST['debug'] == 'on') {
      $result = $client->__getLastResponse();
    }
    if ($cron) {
      $o[1] .= '<div style="padding:3px;color:#fff;background:#c00;">Edit failed: '.$entry."<div>".htmlspecialchars(extractMessage($result)).'</div></div>';
    } else {
      echo '<div class="f">Copy Failed: '.$entry.'<div>'.htmlspecialchars(extractMessage($result)).'</div></div>';
    }
    $total['f']++;
  }
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
  global $asset_type, $asset_children_type, $data, $o, $cron, $path;
  if (!is_array($asset["children"]->child)) {
    $asset["children"]->child=array($asset["children"]->child);
  }
  $files = array();
  $folders = array();
  foreach($asset["children"]->child as $child) {
    if ($child->type == strtolower($asset_children_type)) {
      array_push($files, $child->path->path);
    } elseif ($child->type === strtolower($asset_type)) {
      array_push($folders, $child->path->path);
      if (foldertest($child))
        readFolder($client, $auth, array ('type' => $child->type, 'id' => $child->id));
    }
  }
  $files = array_filter($files); // Remove empty entries
  $folders = array_filter($folders); // Remove empty entries
  $subFolder = '';
  if ($asset['name'] != '__web-services') {$subFolder = $asset['name'];}
  readScripts($path.$subFolder, $files, $folders, $subFolder);
}

function readPage($client, $auth, $id, $type, $entry, $code) {
  global $asset_type, $asset_children_type, $data, $o, $cron;
  $reply = $client->read ( array ('authentication' => $auth, 'identifier' => $id ) );
  if ($reply->readReturn->success == 'true') {
    // For some reason the names of asset differ from the returned object
    $returned_type = '';
    foreach ($reply->readReturn->asset as $t => $a) {
      if (!empty($a)) {$returned_type = $t;}
    }
    
    $asset = ( array ) $reply->readReturn->asset->$returned_type;
    if ($cron) {
      $o[3] .= '<h4><a href="https://cms.slc.edu:8443/entity/open.act?id='.$asset['id'].'&type='.$type.'#highlight">'.$asset['path']."</a></h4>";
    } elseif ($_POST['asset'] == 'on') {
      $name = '';
      if (!$asset['path']) {$name = $asset['name'];}
      echo '<h4><a href="https://cms.slc.edu:8443/entity/open.act?id='.$asset['id'].'&type='.$type.'#highlight">'.$asset['path'].$name."</a></h4>";
    }
    
    if (edittest($asset)) {
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
      
      editPage($client, $auth, $asset, $entry, $code);
      if (!$cron) {echo '</div>';}
    }
    
  } else {
    if ($cron) {
      $o[1] .= '<div style="padding:3px;color:#fff;background:#c00;">Failed to read page: '.$id.'</div>';
    } else {
      echo '<div class="f">Failed to read page: '.$id.'</div>';
    }
  }
}


function editPage($client, $auth, $asset, $entry, $code) {
  global $total, $asset_type, $asset_children_type, $data, $changed, $o, $cron;
  
  changes($asset, $code);
  
  if ($_POST['after'] == 'on' && !$cron) {
    echo '<button class="btn" href="#aModal'.$asset['id'].'" data-toggle="modal">View After</button><div id="aModal'.$asset['id'].'" class="modal hide" tabindex="-1" role="dialog" aria-hidden="true"><div class="modal-body">';
      print_r($asset); // Shows the page as it will be
    echo '</div></div>';
  }
  
  if ($changed == true) {
    if ($_POST['action'] == 'edit' || $cron) {
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
      if ($_POST['debug'] == 'on' || $cron) {
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
