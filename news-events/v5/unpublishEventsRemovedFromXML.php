<?php 
date_default_timezone_set('America/New_York');

// To update for each year, change this and change the $from and $to (below)
$yearpath = 'events/2015-2016/';

// Function from http://php.net/manual/en/function.simplexml-load-string.php#48814
function simplexml_merge (SimpleXMLElement &$xml1, SimpleXMLElement $xml2) {
  // convert SimpleXML objects into DOM ones
  $dom1 = new DomDocument();
  $dom2 = new DomDocument();
  $dom1->loadXML($xml1->asXML());
  $dom2->loadXML($xml2->asXML());
  // pull all child elements of second XML
  $xpath = new domXPath($dom2);
  $xpathQuery = $xpath->query('/*/*');
  for ($i = 0; $i < $xpathQuery->length; $i++)
  {
    // and pump them into first one
    $dom1->documentElement->appendChild(
    $dom1->importNode($xpathQuery->item($i), true));
  }
  $xml1 = simplexml_import_dom($dom1);
}
if (isset($_GET['from'])) {
  $from = $_GET['from'];
} else {
  $from = '2015-09-01';
}
if (isset($_GET['to'])) {
  $to = $_GET['to'];
} else {
  $to = '2016-08-31';
}

$events = simplexml_load_file('http://my.slc.edu/feeds/events/?cal=5&from='.$from.'&to='.$to, 'SimpleXMLElement',LIBXML_NOCDATA);
$private_events = simplexml_load_file('http://my.slc.edu/feeds/events/?cal=2&from='.$from.'&to='.$to, 'SimpleXMLElement',LIBXML_NOCDATA);

// Merging the private into the public means the public events will be the ones created
simplexml_merge($events, $private_events);

$event_names = array();
$event_dupes = array();
foreach ($events->event as $i=>$event) {
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
  if (in_array($event_n, $event_names)) {
    array_push($event_dupes, $event_n);
  } else {
    array_push($event_names, $event_n);
  }
}
?>

<?php
$title = 'Unpublish event pages that have been removed from the events xml feeds';

$type_override = 'folder';
//$start_asset = '85a826eec0a8022b3d7ce269ce9477fa';
$start_asset = '';
$message = '<div class="f">WARNING: Legacy script. <a href="./detectEventsRemovedFromXML.php">Go to the new script</a>.</div>';
//$message = 'You can set ?from=yyyy-mm-dd&to=yyyy-mm-dd but you should make sure to use the whole academic year!';

if (array_key_exists('submit',$_POST)) {
  $headers = 'From: com@vm-www.slc.edu' . "\r\n" . 'Content-type: text/html; charset=UTF-8';
  mail('stu@t.apio.ca','WARNING: Legacy script submitted',"unpublishEventsRemovedFromXML was submitted by $user.", $headers);
}

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
    // print_r($events); // Shows all the events in the XML feeds
  echo '</div></div>';
  // echo '<h1>Duplicate Events:<br/><pre>';print_r($event_dupes);echo '</pre></h1>';
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
  global $asset_type, $asset_children_type, $data, $o, $cron, $children, $yearpath, $event_names, $total;
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
      if (in_array($name, $event_names) ) {
        if ($cron) {
          $o[3] .= $name.'<br>';
        } else {
          echo '<div class="k"><small>'.$name.' is in the XML feed.</small></div>';
        }
        $total['k']++;
      } else {
        if ($_POST['action'] == 'edit' || $cron) {
          $publish = $client->publish ( array ('authentication' => $auth, 'publishInformation' => array('identifier' => array('type' => $asset_children_type, 'id' => $child->id), 'unpublish' => true ) ) );
          if ($publish->publishReturn->success == 'true') {
            if ($cron) {
              $o[2] .= $name.' was unpublished<br>';
            } else {
              echo '<div class="s">'.$name.' was unpublished</div>';
            }
            $total['s']++;
          } else {
            if ($cron) {
              $o[1] .= $name.' FAILED to unpublish<br>';
            } else {
              echo '<div class="f">'.$name.' could not be unpublished</div>';
              print_r($publish);
            }
            $total['f']++;
          }
        } else {
          echo '<div class="d">'.$name.' will be unpublished</div>';
          $total['k']++;
        }
      }
    }
  }

  
}

?>
