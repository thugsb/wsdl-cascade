<?php 
date_default_timezone_set('America/New_York');

include_once('eventFolderIDs.php');


$yearpath = "events/$acad_year/";

if (isset($_GET['from'])) {
  $from = $_GET['from'];
} else {
  $from = $yearstart;
}
if (isset($_GET['to'])) {
  $to = $_GET['to'];
} else {
  $to = $yearend;
}
$all_events = simplexml_load_file('http://my.slc.edu/feeds/events/?cal=5&from='.$from.'&to='.$to, 'SimpleXMLElement',LIBXML_NOCDATA);
$private_events = simplexml_load_file('http://my.slc.edu/feeds/events/?cal=2&from='.$from.'&to='.$to, 'SimpleXMLElement',LIBXML_NOCDATA);

simplexml_merge($all_events, $private_events);

$event_names = array();
foreach ($all_events->event as $i=>$event) {
  $e_date = new DateTime();
  $unix_date = (integer)$event->date->unixbegin;
  $e_date->setTimestamp($unix_date);
  $event_date = $e_date->format('Y-m-d');
  $event_title = preg_replace("/[^A-Za-z ]+/", "", $event->title);
  $event_title = preg_replace("/  /", " ", $event_title);
  $event_title = str_replace(" ", "_", $event_title);
  $event_title = strtolower(substr($event_title, 0, 20));
  $event_n = $event_date . '-' . $event_title . '-eid'.$event->detailid;
  // $event_n = str_replace(':','-',$event->date->begin[0]).'Z-'.$event->detailid;
  array_push($event_names, $event_n);
}

?>

<?php
$title = 'Detect event pages that have been removed from the events xml feeds';

$type_override = 'folder';
$start_asset = $year_folder;

$message = 'You can set ?from=yyyy-mm-dd&to=yyyy-mm-dd but you should make sure to use the whole academic year!';

$children = array();




if (!isset($cron)) {
  $cron=false;
  include('../html_header.php');
}

if (!$cron) {
  echo '<button class="btn" href="#pModal'.$asset['id'].'" data-toggle="modal">Pages in cascade</button><div id="pModal'.$asset['id'].'" class="modal hide" tabindex="-1" role="dialog" aria-hidden="true"><div class="modal-body">';
    print_r($children);
  echo '</div></div>';
  echo '<button class="btn" href="#eModal'.$asset['id'].'" data-toggle="modal">Events in XML</button><div id="eModal'.$asset['id'].'" class="modal hide" tabindex="-1" role="dialog" aria-hidden="true"><div class="modal-body">';
    print_r($event_names);
    // print_r($all_events); // Shows all the events in the XML feeds
  echo '</div></div>';
}

?>


<?php

function readFolder($client, $auth, $id) {
  global $asset_type, $asset_children_type, $data, $o, $cron;
  $folder = $client->read ( array ('authentication' => $auth, 'identifier' => $id ) );
  if ($folder->readReturn->success == 'true') {
    
    $asset = ( array ) $folder->readReturn->asset->$asset_type;
    if ($_POST['folder'] == 'on') {
      echo "<h1>Folder: ".$asset["path"]."</h1>";
    }
    if ($cron) {
      $o[4] .= '<div style="color:#009;">Folder: '.$asset["path"]."</div>";
    }
    
    if ($_POST['children'] == 'on' && !$cron) {
      echo '<button class="btn" href="#cModal'.$asset['id'].'" data-toggle="modal">View Children</button><div id="cModal'.$asset['id'].'" class="modal hide" tabindex="-1" role="dialog" aria-hidden="true"><div class="modal-body">';
        print_r($asset["children"]); // Shows all the children of the folder
      echo '</div></div>';
    }
    indexFolder($client, $auth, $asset);
  } else {
    if ($cron) {
      $o[1] .= '<div style="padding:3px;color:#fff;background:#c00;">Failed to read folder: '.$asset["path"]."</div>";
    } else {
      echo '<div class="f">Failed to read folder: '.$asset["path"].'</div>';
    }
  }
}

function indexFolder($client, $auth, $asset) {
  global $asset_type, $asset_children_type, $data, $o, $cron, $children, $yearpath, $event_names, $total, $deleted_folder;
  if (!is_array($asset["children"]->child)) {
    $asset["children"]->child=array($asset["children"]->child);
  }
  foreach($asset["children"]->child as $child) {
    if ($child->path->path != $yearpath.'_archived' && $child->path->path != $yearpath.'_inactive' && $child->path->path != $yearpath.'index') {
      array_push($children, $child);
    }
  }
  
  foreach ($children as $child) {
    if ($child->type == 'page') {
      $name = str_replace($yearpath,'',$child->path->path);
      preg_match('/[0-9]+-[0-9][0-9]-[0-9][0-9]/', $name, $event_date);
      preg_match('/[0-9]+-[0-9][0-9]-[0-9][0-9]-([a-z_]+)-eid/', $name, $event_slug);
      $date_matches = array();
      $name_matches = array();
      foreach($event_names as $ev) {
        if (preg_match("/$event_slug[1]/", $ev)) {
          array_push($name_matches, $ev);
        }
        if (preg_match("/$event_date[0]/", $ev)) {
          array_push($date_matches, $ev);
        }
      }
      if (in_array($name, $event_names) ) {
        if ($cron) {
          $o[3] .= $name.'<br>';
        } else {
          echo '<div class="k"><small>'.$name.' is in the XML feed.</small></div>';
        }
        $total['k']++;
      } else {
        if (!$cron) {echo '<div><strong><a target="_blank" href="https://cms.slc.edu:8443/entity/open.act?id='.$child->id.'&type=page">'.$name.'</a></strong> has been deleted from the XML feed.</div>';}
        $to      = 'tguiliano@sarahlawrence.edu';
        $subject = 'Event deleted from XML: '.$name;
        $message = '<p><a target="_blank" href="https://cms.slc.edu:8443/entity/open.act?id='.$child->id.'&type=page">'.$name.'</a> has been deleted from the XML event feed.</p>';
        $headers = 'From: stu@t.apio.ca' . "\r\n";
        $headers .= 'MIME-Version: 1.0' . "\r\n";
        $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
        $headers .= 'Cc: stu@t.apio.ca, wjoell@sarahlawrence.edu' . "\r\n";
        
        $reply = $client->read ( array ('authentication' => $auth, 'identifier' => array('id' => $child->id, 'type' => 'page') ) );
        if ($reply->readReturn->success == 'true') {
          $page_asset = ( array ) $reply->readReturn->asset->page;
          $message .= '<h4>'. $page_asset['metadata']->title .'</h4>';
        }
        
        if ($_POST['action'] == 'edit' || $cron) {
          $publish = $client->publish ( array ('authentication' => $auth, 'publishInformation' => array('identifier' => array('type' => $asset_children_type, 'id' => $child->id), 'unpublish' => true ) ) );
          $move = $client->move ( array ('authentication' => $auth, 'identifier' => array ('type' => $asset_children_type, 'id' => $child->id ), 'moveParameters' => array('destinationContainerIdentifier'=> array('type'=>'folder', 'id'=>$deleted_folder), 'doWorkflow'=>false) ) );
        }
        if ($publish->publishReturn->success == 'true') {
          if ($cron) {
            $o[2] .= $name.' was unpublished<br>';
          } else {
            echo '<div class="s">'.$name.' was unpublished</div>';
          }
          $message .= '<p>The event has been <span style="color:#090">successfully</span> unpublished.</p>';
          $total['s']++;
        } else {
          if ($cron) {
            $o[1] .= $name.' FAILED to unpublish<br>';
          } else {
            echo '<div class="f">'.$name.' could not be unpublished</div>';
            print_r($publish);
          }
          $message .= '<p>The event <span style="color:#900">failed to unpublish</span>.</p>';
          $total['f']++;
        }
        if ($move->moveReturn->success == 'true') {
          if ($cron) {
            $o[2] .= $name.' was moved to _deleted<br>';
          } else {
            echo '<div class="s">'.$name.' was moved to _deleted</div>';
          }
          $message .= '<p>The event has been <span style="color:#090">successfully</span> moved into the _deleted folder.</p>';
          $total['s']++;
        } else {
          if ($cron) {
            $o[1] .= $name.' FAILED to move<br>';
          } else {
            echo '<div class="f">'.$name.' could not be moved. '.($_POST['action'] == 'edit' ? '(Edit enabled)':'(Edit disabled)').'</div>';
            print_r($move);
          }
          $message .= '<p>The event <span style="color:#900">failed to move</span> into the _deleted folder.</p>';
          $total['f']++;
        }
        $message .= '<p>Please review this event.</p>';
        $message .= "<p>Here are other events that match the same name:</p><ul>";
        foreach ($name_matches as $ev) {
          $message .= '<li>'.$ev.'</li>';
        }
        $message .= '</ul><p>Here are other events that match the same date:</p><ul>';
        foreach ($date_matches as $ev) {
          $message .= '<li>'.$ev.'</li>';
        }
        $message .= '</ul>';
        if ($cron) {
          mail($to, $subject, $message, $headers);
        } else {
          echo $message.'<hr/>';
        }
      }
    }
  }

  
}

?>
