<?php
date_default_timezone_set('America/New_York');

include_once(__DIR__.'/rollbar-init.php');
use \Rollbar\Rollbar;
use \Rollbar\Payload\Level;


$title = 'Copy an asset factory folder to all the named locations in an array';

// $type_override = 'page';
// $start_asset = 'e59cc45a7f00000100c46dcf503b2144';

// Optionally override the container/child types
// $asset_type = 'assetFactoryContainer';
// $asset_children_type = 'assetFactory';

function pagetest($child) {
    return false;
}
function foldertest($child) {
    return false;
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
  // foreach ($asset["metadata"]->dynamicFields->dynamicField as $dyn) {
  //   if ($dyn->name == "xxx") {
  //     // Do stuff
  //   }
  // }
  // foreach ($asset["structuredData"]->structuredDataNodes->structuredDataNode as $sdnode) {
  //   if ($sdnode->identifier == "xxx") {
  //     // Do stuff
  //   }
  // }
}

if (!$cron) {include(__DIR__.'/../html_header.php');}

$folders = [
"sarah-lawrence-college-in-beijing",
"finding-sarah-lawrence",
"sarah-lawrence-college-in-cuba",
"mfa-writing-sarah-lawrence-college",
"mfa-dance-sarah-lawrence-college",
"mfa-theatre-sarah-lawrence-college",
"health-advocacy-sarah-lawrence-college",
"writing-institute-sarah-lawrence-college",
"womens-history-sarah-lawrence-college",
"summer-at-sarah-lawrence-college",
"art-of-teaching-sarah-lawrence-college",
"mayapple",
"summer-film",
"publish-and-promote-your-book",
"end-of-life-care",
"publishing-certificate-sarah-lawrence-college",
"summer-film-fb",
"summer-at-sarah-lawrence-college-fb",
"health-advocacy-children",
"masters-sarah-lawrence-college",
"health-advocacy-sarah-lawrence-college-aarp",
"art-of-teaching-ybs",
"end-of-life-care-trailer",
"health-advocacy-fb",
"health-advocacy-children-fb",
"health-advocacy-wp",
"health-advocacy-children-wp",
"dance-movement-therapy",
"summer-seminar-writers"];

if (array_key_exists('submit',$_POST)) {
  foreach ($folders as $key => $f) {
    $destFolder = array ('type' => 'folder', 'id' => 'fa1e9949c0a8022b06d5e847645d3e4e');
    $copyParams = array ("newName" => $f, 'destinationContainerIdentifier' => $destFolder, "doWorkflow" => false);
    // The asset you're $copying
    $copying = array ('type' => 'folder', 'id' => '04bdc414c0a8022b5fe70b02b0bb0ce7' ); 
    if ($_POST['action'] == 'edit') {
      $copy = $client->copy ( array ('authentication' => $auth, 'identifier' => $copying, 'copyParameters' => $copyParams ) );
    }
    if ($copy->copyReturn->success == 'true') {
      echo '<div class="s">Created successfully: '.$blockName.'</div>';
      $total['s']++;
    } else {
      if ($_POST['debug'] == 'on' || $cron) {
        $result = $client->__getLastResponse();
      }
      echo '<div class="f">Creation failed: '.$blockName.'<div>'.extractMessage($result).'</div></div>';
      $total['f']++;
    }
  }
}

function readFolder($client, $auth, $id) {
  global $asset_type, $asset_children_type, $data, $o, $cron;
  $folder = $client->read ( array ('authentication' => $auth, 'identifier' => $id ) );
  if ($folder->readReturn->success == 'true') {
    
    $asset = ( array ) $folder->readReturn->asset->$asset_type;
    if ($cron) {
      $o[4] .= "Folder: ".$asset["path"]."\n";
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
      $o[1] .= 'FAILED to read folder with given ID '.$id["id"]."\n";
    } else {
      echo '<div class="f">Failed to read folder: '.$id["id"].'</div>';
    }
  }
}
function indexFolder($client, $auth, $asset) {
  global $asset_type, $asset_children_type, $data, $o, $cron, $total;
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
    // For some reason the names of asset differ from the returned object
    $returned_type = '';
    foreach ($reply->readReturn->asset as $t => $a) {
      if (!empty($a)) {$returned_type = $t;}
    }
    
    $asset = ( array ) $reply->readReturn->asset->$returned_type;
    if ($cron) {
      $o[3] .= $asset['path']."\n".CMS_OPEN_PATH.$asset['id'].'&type='.$asset_children_type."\n";
    } elseif ($_POST['asset'] == 'on') {
      $name = '';
      if (!$asset['path']) {$name = $asset['name'];}
      echo '<h4><a href="'.CMS_OPEN_PATH.$asset['id'].'&type='.$type.'#highlight">'.$asset['path'].$name."</a></h4>";
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
      
      editPage($client, $auth, $asset);
      if (!$cron) {echo '</div>';}
    }
    
  } else {
    if ($cron) {
      $o[1] .= 'Failed to read page: '.print_r($id, true)."\n";
    } else {
      echo '<div class="f">Failed to read page: '.print_r($id, true).'</div>';
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
    if ($_POST['action'] == 'edit' || $cron) {
      $edit = $client->edit ( array ('authentication' => $auth, 'asset' => array($asset_children_type => $asset) ) );
    }
    if ($edit->editReturn->success == 'true') {
      if ($cron) {
        $o[2] .= 'Edit success: '.$asset['path']."\n".CMS_OPEN_PATH.$asset['id'].'&type='.$asset_children_type."\n";
      } else {
        echo '<div class="s">Edit success</div>';
      }
      $total['s']++;
    } else {
      if ($_POST['debug'] == 'on' || $cron) {
        $result = $client->__getLastResponse();
      }
      if ($cron) {
        $o[1] .= 'Edit failed: '.$asset['path']."\n".CMS_OPEN_PATH.$asset['id'].'&type='.$asset_children_type."\n".htmlentities(extractMessage($result))."\n\n";
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
