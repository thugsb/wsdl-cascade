<?php
date_default_timezone_set('America/New_York');
$title = 'Curate Events';

// $type_override = 'page';
$start_asset = 'e7fad51b7f000002781205b8981ed26d';

$message = 'NOTE: This page requires JavaScript. Optionally, use the GET argument "?date=yyyy-mm-dd" to filter by date, where dd and mm are not required.';
$user = $_POST['login'];
$password = $_POST['password'];
$client = $_POST['client'];
$script = <<<EOS

$(function() {
  $('body').append('<iframe name="result" id="result" style="position:fixed; bottom:0; right:0; width:50%; height:50px; background:#fff;"></iframe>');
  $('.event-form').append('<input name="login" type="hidden" value="$user"/><input name="password" type="hidden" value="$password"/><input name="client" type="hidden" value="$client"/><input name="type" type="hidden" value="page"/><input name="action" type="hidden" value="edit"/>');
  $('.event-form.visible').prepend('<div class="btn-group pull-right"><input type="submit" name="submit" class="btn btn-warning" value="De-activate"/></div>');
  $('.event-form.undecided').prepend('<div class="btn-group pull-right"><input type="submit" name="submit" class="btn btn-success" value="Activate"/><input type="submit" name="submit" class="btn btn-warning" value="De-activate"/></div>');
  
  $('.event-form .btn-success').click(function(e) {
    if ( $(this).is('.disabled') ) { e.preventDefault(); return false; }
    $(this).closest('form').attr('action','activate.php');
    $(this).closest('form').prepend('<div class="label label-success pull-right">Activated</div>');
    $(this).closest('form').find('.btn').addClass('disabled');
  });
  $('.event-form .btn-warning').click(function(e) {
    if ( $(this).is('.disabled') ) { e.preventDefault(); return false; }
    $(this).closest('form').attr('action','deactivate.php');
    $(this).closest('form').prepend('<div class="label label-important pull-right">Deactivated</div>');
    $(this).closest('form').find('.btn').addClass('disabled');
  });
});

EOS;

// Optionally override the container/child types
// $asset_type = 'assetFactoryContainer';
// $asset_children_type = 'assetFactory';

function pagetest($child) {
  $pattern = '/^events\/2014-2015\/'.$_GET['date'].'/';
  if (isset($_GET['date']) && preg_match($pattern, $child->path->path))
    return true;
}
function foldertest($child) {
  if (preg_match('/^_[a-z]/', $child->path->path))
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
      
      foreach ($asset["metadata"]->dynamicFields->dynamicField as $dyn) {
        if ($dyn->name == 'visible') {
          if ($dyn->fieldValues->fieldValue->value == 'Yes') {$visible = true;}
        }
        if ($dyn->name == 'calendar') {
          if ($dyn->fieldValues->fieldValue->value == 'Events Open to Public') {$public = true;}
        }
      }
      if ($visible) {
        echo '<form class="event-form visible" method="POST" target="result">';
      } else {
        echo '<form class="event-form undecided" method="POST" target="result">';
      }
      if ($public) {
        echo '<div class="label label-success pull-left">Public</div>';
      } else {
        echo '<div class="label label-info pull-left">Private</div>';
      }
      echo '<input type="hidden" name="id"cat: /tmp/TextMate-ScratchSnippet.txt: No such file or directory
       value="'.$asset['id'].'"/>'.'<h4>'.$asset['metadata']->title.'</h4>'.$asset['path'].$name."</form>";
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
