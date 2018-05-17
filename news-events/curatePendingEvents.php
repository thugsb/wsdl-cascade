<?php
date_default_timezone_set('America/New_York');
$title = 'Curate Events';

include_once('eventFolderIDs.php');

// $type_override = 'page';
$start_asset = $pending_folder;

$message .= 'NOTE: This page requires JavaScript. Optionally, use the GET argument "?date=yyyy-mm-dd" to filter by date, where dd and mm are not required. The "Read only" vs "Edit" radio options have no effect on this page.';
$htmlHead = "<link href='../lib/selectize.default.css' rel='stylesheet'/>
<script src='curation.js'></script>
<script src='../lib/selectize.min.js'></script>";
$script = 'window.eventYear = "' . $events_year . '"';


if (array_key_exists('submit',$_POST)) { //If form was submitted 
  $client = new SoapClient ( $_POST['client'], array ('trace' => 1 ) ); 
  $auth = array ('username' => $_POST['login'], 'password' => $_POST['password'] );
  $message .= '<div class="hidden"><select name="deleted_event" id="deleted_events" class="deleted_events"> <option value="">Select a deleted event</option>';
  $deleted_folders = [$deleted_folder, $previous_deleted_folder];
  foreach ($deleted_folders as $key => $folder) {
    if ( isset($folder) ) {
      $del_folder = $client->read ( array ('authentication' => $auth, 'identifier' => array ('type' => 'folder', id => $folder ) ) );
      if ($del_folder->readReturn->success == 'true') {
        $del_asset = ( array ) $del_folder->readReturn->asset->folder;
        $deleted_events = $del_asset["children"]->child;
        if (is_array($deleted_events) ) {
          foreach ($deleted_events as $del_event) {
            $message .= "<option value='".$del_event->id."'>".$del_event->path->path."</option>";
          }
        }
      } else {
        if (!$cron) {$message .= "<option>Couldn't read deleted events folder.</option>";}
      }
    }
  }
  $message .= '</select></div>';
}

// Optionally override the container/child types
// $asset_type = 'assetFactoryContainer';
// $asset_children_type = 'assetFactory';

function pagetest($child) {
  global $acad_year;
  $pattern = '/^events\/'.$acad_year.'\/_pending\/'.$_GET['date'].'/';
  if (preg_match($pattern, $child->path->path))
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
      $o[3] .= '<h4><a href="'.CMS_OPEN_PATH.$asset['id'].'&type='.$type.'#highlight">'.$asset['path']."</a></h4>";
    } elseif ($_POST['asset'] == 'on') {
      $name = '';
      if (!$asset['path']) {$name = $asset['name'];}

      foreach ($asset["metadata"]->dynamicFields->dynamicField as $dyn) {
        if ($dyn->name == 'begin') {
          $begin = intval($dyn->fieldValues->fieldValue->value)/1000;
        }
        if ($dyn->name == 'end') {
          $end = intval($dyn->fieldValues->fieldValue->value)/1000;
        }
        if ($dyn->name == 'sponsor') {
          $sponsor = $dyn->fieldValues->fieldValue->value;
        }
        if ($dyn->name == 'location') {
          $location = $dyn->fieldValues->fieldValue->value;
        }
        if ($dyn->name == 'type') {
          $type = $dyn->fieldValues->fieldValue->value;
        }
        if ($dyn->name == 'recurring') {
          $recurring = $dyn->fieldValues->fieldValue->value;
        }
        if ($dyn->name == 'eventsource') {
          $eventsource = $dyn->fieldValues->fieldValue->value;
        }
      }
      foreach ($asset["structuredData"]->structuredDataNodes->structuredDataNode as $sdn) {
        if ($sdn->identifier == 'main_column') {
          foreach ($sdn->structuredDataNodes->structuredDataNode as $main_node) {
            if ($main_node->identifier == 'content') {
              $content = $main_node->text;
            }
          }
        }
      }
      if (strpos($_POST['client'], CMS_BASE_PATH) === false) {
        $cmsLink = CMS_DEV_BASE_PATH;
      } else {
        $cmsLink = CMS_BASE_PATH;
      }
      echo '<form class="event-form clearfix" method="POST" target="result-'.$asset['id'].'" data-id="'.$asset['id'].'">';
        echo '<div class="btn btn-info pull-right collapser">Expand/Collapse</div>';
        echo '<input type="hidden" name="id" value="'.$asset['id'].'"/>';
        echo '<h4><a target="_blank" href="'. $cmsLink .'/entity/open.act?id='.$asset['id'].'&type=page&">'.$asset['metadata']->title.'</a></h4>';
        echo '<div class="event-details">';
          echo '<div>'.$asset['path'].$name.'</div>';
          echo '<div class="k">'.date("D M dS, H:i", $begin).'</div> - <div class="k">'.date('D M dS, H:i', $end).'</div>'.($recurring == 'False' ? '' : '<div class="label label-info">Recurring</div>').' <a class="label label-success" target="_blank" href="'.$eventsource.'">Source</a>';
          echo '<div><strong>Location:</strong> '.$location.'</div>';
          echo '<div><strong>Sponsor:</strong> '.$sponsor.'</div>';
          echo '<div><strong>Type:</strong> '.$type.'</div>';
          echo '<div style="max-width:600px; background:#dde;">'.$asset['metadata']->summary.'</div>';
          echo '<div style="max-width:600px;">'.$content.'</div>';
          echo '<div class="event-tags"><div class="loading-tagging">Loading...</div></div>';
          echo '<input name="login" type="hidden" value="'. $_POST['login'] .'"/><input name="password" type="hidden" value="'. $_POST['password'] .'"/><input name="client" type="hidden" value="'. $_POST['client'] .'"/><input name="type" type="hidden" value="page"/><input name="action" type="hidden" value="edit"/>';
          echo '<div class="event-actions hidden text-center"><div class="btn-group"><input type="submit" name="submit" class="btn btn-success btn-enable btn-action" value="Enable and Tag '. $asset['metadata']->title .'"/><input type="submit" name="submit" class="btn btn-warning btn-reject btn-action" value="Reject"/></div> OR...</div>';
        echo '</div>';
        echo '<h5>Action results:</h5>';
        echo '<iframe name="result-'. $asset['id'] .'" id="result-'. $asset['id'] .'" class="actionOutputIframe"></iframe>';
      echo "</form>";
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

?>
