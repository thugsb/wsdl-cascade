<?php 
date_default_timezone_set('America/New_York');

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
  $from = '';
}
if (isset($_GET['to'])) {
  $to = $_GET['to'];
} else {
  $to = '';
}

$events = simplexml_load_file(CALENDAR_EVENTS_FEED_URL . '?cal=5&from='.$from.'&to='.$to, 'SimpleXMLElement',LIBXML_NOCDATA);
$private_events = simplexml_load_file(CALENDAR_EVENTS_FEED_URL . '?cal=2&from='.$from.'&to='.$to, 'SimpleXMLElement',LIBXML_NOCDATA);
// The full year
// $events = simplexml_load_file(CALENDAR_EVENTS_FEED_URL . '?cal=5&from=2012-09-01&to=2013-08-31', 'SimpleXMLElement',LIBXML_NOCDATA);
// $private_events = simplexml_load_file(CALENDAR_EVENTS_FEED_URL . '?cal=2&from=2012-09-01&to=2013-08-31', 'SimpleXMLElement',LIBXML_NOCDATA);

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
$title = 'Create event pages for events in the xml feeds';

$type_override = 'folder';
//$start_asset = '85a826eec0a8022b3d7ce269ce9477fa';
$start_asset = '';
$message = '<div class="f">WARNING: Legacy script. <a href="./importEventsToPending.php">Go to the new script</a>.</div>';
//$message = 'Set ?from=yyyy-mm-dd&to=yyyy-mm-dd';

if (array_key_exists('submit',$_POST)) {
  $headers = 'From: '. SERVER_EMAIL . "\r\n" . 'Content-type: text/html; charset=UTF-8';
  mail('stu@t.apio.ca','WARNING: Legacy script submitted',"readEventsXML was submitted by $user.", $headers);
}

$publish = array();

// Optionally override the container/child types
// $asset_type = 'assetFactoryContainer';
// $asset_children_type = 'assetFactory';

function pagetest($child) {
  // if (preg_match('/[a-z]/', $child->path->path))
    return true;
}
function edittest($asset) {
  // if (preg_match('/[a-z]/', $asset["contentTypePath"]))
    return true;
}

function changes(&$asset, $event_n) {
  global $changed, $events, $event_dupes, $o, $cron;
  $changed = false;
  $detailid = explode('-eid',$event_n);
  for ($i = 0; $i < count($events->event); $i++) {
    $object = $events->event[$i];
    if ($object->detailid == $detailid[1]) break;
    $object = NULL;
  }
  $fields = array(
    'title'=>title, 
    'descriptiontext'=>teaser
  );
  foreach ($fields as $ofield=>$afield) {
    $data = (string)$object->$ofield;
    $data = str_replace('&nbsp;','&#160;',$data);
    $data = str_replace('& ','and ',$data);
    $data = str_replace('&',' and ',$data);
    $data = strip_tags($data);
    // echo $data;
    if ($asset["metadata"]->$afield != $data) {
      $asset["metadata"]->$afield = $data;
      $changed = true;
    }
  }
  
  $summary = (string)$object->description;
  if ($summary != '') {
    $summary = strip_tags($summary,'<p><a>');
    $tidy = new tidy;
    $tidy->parseString($summary, array("clean" => true, "output-xhtml" => true, "show-body-only" => true), 'utf8');
    $tidy->cleanRepair;
    $summary = $tidy->value;
    $summary = str_replace('&nbsp;','&#160;',$summary);
    $summary = str_replace('<p>&#160;</p>','',$summary);
    if ($cron) {
      $o[1] .= '<div style="color:#900;">PHP Tidy: '.$tidy->errorBuffer."</div>";
    } else {
      echo '<div class="f">PHP Tidy: '.$tidy->errorBuffer.'</div>';
    }
  }  
  if ($asset["metadata"]->summary != $summary) {
    $asset["metadata"]->summary = $summary;
    $changed = true;
  }
  
  foreach ($asset["metadata"]->dynamicFields->dynamicField as $dyn) {
    if ($dyn->name == 'begin') {
      if ($dyn->fieldValues->fieldValue->value != $object->date->unixbegin * 1000) {
        $dyn->fieldValues->fieldValue->value = $object->date->unixbegin * 1000;
        $changed = true;
      }
    } elseif ($dyn->name == 'end') {
      if ($dyn->fieldValues->fieldValue->value != $object->date->unixend * 1000) {
        $dyn->fieldValues->fieldValue->value = $object->date->unixend * 1000;
        $changed = true;
      }
    } elseif ($dyn->name == 'month') {
      $month = (string)$object->date->month;
      if ($dyn->fieldValues->fieldValue->value != $month) {
        $dyn->fieldValues->fieldValue->value = $month;
        $changed = true;
      }
    } elseif ($dyn->name == 'sponsor') {
      $sponsor = (string)$object->sponsor;
      $sponsor = preg_replace("/[^A-Za-z0-9- \/\(\)']+/", "", $sponsor);
      if ($dyn->fieldValues->fieldValue->value != $sponsor) {
        $dyn->fieldValues->fieldValue->value = $sponsor;
        $changed = true;
      }
    } elseif ($dyn->name == 'location') {
      $location = (string)$object->location;
      if ($dyn->fieldValues->fieldValue->value != $location) {
        $dyn->fieldValues->fieldValue->value = $location;
        $changed = true;
      }
    } elseif ($dyn->name == 'calendar') {
      $calendar = (string)$object->calendar->name;
      if ($dyn->fieldValues->fieldValue->value != $calendar) {
        $dyn->fieldValues->fieldValue->value = $calendar;
        $changed = true;
      }
    } elseif ($dyn->name == 'type') {
      $type = (string)$object->type->name;
      $type = preg_replace("/[^A-Za-z0-9- \/\(\)']+/", "", $type);
      if ($dyn->fieldValues->fieldValue->value != $type) {
        $dyn->fieldValues->fieldValue->value = $type;
        $changed = true;
      }
    } elseif ($dyn->name == 'alldayevent') {
      $alldayevent = (string)$object->alldayevent;
      if ($dyn->fieldValues->fieldValue->value != $alldayevent) {
        $dyn->fieldValues->fieldValue->value = $alldayevent;
        $changed = true;
      }
    } elseif ($dyn->name == 'recurring') {
      $recurring = (string)$object->recurring;
      if ($dyn->fieldValues->fieldValue->value != $recurring) {
        $dyn->fieldValues->fieldValue->value = $recurring;
        $changed = true;
      }
    } elseif ($dyn->name == 'eventurl') {
      $eventurl = (string)$object->eventurl;
      if ($dyn->fieldValues->fieldValue->value != $eventurl) {
        $dyn->fieldValues->fieldValue->value = $eventurl;
        $changed = true;
      }
    } elseif ($dyn->name == 'eventsource') {
      $eventsource = (string)$object->detailslink;
      if ($dyn->fieldValues->fieldValue->value != $eventsource) {
        $dyn->fieldValues->fieldValue->value = $eventsource;
        $changed = true;
      }
    } elseif ($dyn->name == 'eventid') {
      $eventid = (string)$object->id;
      if ($dyn->fieldValues->fieldValue->value != $eventid) {
        $dyn->fieldValues->fieldValue->value = $eventid;
        $changed = true;
      }
    } elseif ($dyn->name == 'detailid') {
      $detailid = (string)$object->detailid;
      if ($dyn->fieldValues->fieldValue->value != $detailid) {
        $dyn->fieldValues->fieldValue->value = $detailid;
        $changed = true;
      }
    }
  }
  // foreach ($asset["structuredData"]->structuredDataNodes->structuredDataNode->structuredDataNodes->structuredDataNode as $field) {
  //   if ($field->identifier == 'begin') {
  //     if ($field->text != $object->date->unixbegin * 1000) {
  //       $field->text = $object->date->unixbegin * 1000;
  //       $changed = true;
  //     }
  //   } elseif ($field->identifier == 'end') {
  //     if ($field->text != $object->date->unixend * 1000) {
  //       $field->text = $object->date->unixend * 1000;
  //       $changed = true;
  //     }
  //   }
  // }

}

if (array_key_exists('submit',$_POST) || $cron) {
  if ($cron) {
    $o[1] .= '';
  }
  if ($cron) {
    $client = new SoapClient ( $clientURL, array ('trace' => 1 ) );	
    $auth = array ('username' => $username, 'password' => $password );  
  } else {
    $client = new SoapClient ( $_POST['client'], array ('trace' => 1 ) );
    $auth = array ('username' => $_POST['login'], 'password' => $_POST['password'] );
  }
  
  $all_event_assets = array();
  // _archived
  $folder = $client->read ( array ('authentication' => $auth, 'identifier' => array ('type' => 'folder', 'id' => '85a8aa9fc0a8022b3d7ce269aa47d242') ) );
  if ($folder->readReturn->success == 'true') {
    $asset = ( array ) $folder->readReturn->asset->folder;
    if (!is_array($asset["children"]->child)) {
      $asset["children"]->child=array($asset["children"]->child);
    }
    foreach($asset["children"]->child as $child) {
      array_push($all_event_assets, $child->path->path);
    }
  }
  // _inactive
  $folder = $client->read ( array ('authentication' => $auth, 'identifier' => array ('type' => 'folder', 'id' => '85a91e39c0a8022b3d7ce269adaec728') ) );
  if ($folder->readReturn->success == 'true') {
    $asset = ( array ) $folder->readReturn->asset->folder;
    if (!is_array($asset["children"]->child)) {
      $asset["children"]->child=array($asset["children"]->child);
    }
    foreach($asset["children"]->child as $child) {
      array_push($all_event_assets, $child->path->path);
    }
  }
  // Show the Archived and Inactive events
  // echo '<input type="checkbox" class="hidden" id="archived_inactive"><label class="fullpage" for="archived_inactive">';
  //   print_r($all_event_assets);
  // echo '</label>';
}


if (!$cron) {
  include('../html_header.php');
}


foreach ($publish as $id) {
  $publish = $client->publish ( array ('authentication' => $auth, 'publishInformation' => array('identifier' => array('type' => 'page', 'id' => $id), 'unpublish' => false ) ) );
  if ($publish->publishReturn->success == 'true') {
    if ($cron) {
      $o[2] .= '<div style="color:#090;">Publish success: <a href="'.CMS_OPEN_PATH.$id.'&type=page#highlight">'.$id.'</a></div>';
    } else {
      echo '<div class="s">Publish success: <a href="'.CMS_OPEN_PATH.$id.'&type=page#highlight">'.$id.'</a></div>';
    }
  } else {
    if ($cron) {
      $o[1] .= '<div style="padding:3px;color:#fff;background:#c00;">Publish failed: <a href="'.CMS_OPEN_PATH.$id.'&type=page#highlight">'.$id.'</a></div>';
    } else {
      echo '<div class="f">Publish failed: <a href="'.CMS_OPEN_PATH.$id.'&type=page#highlight">'.$id.'</a><div>'.extractMessage($result).'</div></div>';
    }
    $total['f']++;
  }
}

if (!$cron) {
  echo '<button class="btn" href="#eModal'.$asset['id'].'" data-toggle="modal">View Events</button><div id="eModal'.$asset['id'].'" class="modal hide" tabindex="-1" role="dialog" aria-hidden="true"><div class="modal-body">';
    print_r($events); // Shows all the events in the XML feeds
  echo '</div></div>';
  // echo '<h1>Duplicate Events:<br/><pre>';print_r($event_dupes);echo '</pre></h1>';
}

foreach ($event_dupes as $event_n) {
  $detailid = explode('-eid',$event_n);
  $detailid = $detailid[1];
  $items = array();
  foreach($events->event as $event) {
    if ($event->detailid == $detailid) {
      array_push($items, clone $event);
    }
  }
  if (count($items) != 2) {
    if ($cron) {
      $o[0] .= '<div style="color:#600;">'.$detailid.' is duplicated '.count($items)." times.</div>";
    } else {
      echo '<div class="f">'.$detailid.' is duplicated '.count($items).' times.</div>';
    }
  } else {
    foreach ($items as $event) {
      $event->calendar = '';
    }
    $obj0 = (string) print_r($items[0], true);
    $obj1 = (string) print_r($items[1], true);
    if ($obj0 != $obj1) {
      if ($cron) {
        $o[1] .= '<div style="padding:3px;color:#fff;background:#c00;">Events are different with detailid: '.$detailid."</div>";
      } else {
        echo '<div class="f">Events are different</div>';
        echo '<button class="btn" href="#dModal'.$detailid.'" data-toggle="modal">View Events</button><div id="dModal'.$detailid.'" class="modal hide" tabindex="-1" role="dialog" aria-hidden="true"><div class="modal-body">';
          print_r($items);
        echo '</div></div>';
      }
    } else {
      if ($cron) {
        $o[3] .= '<div style="color:#009">Duplicate events with detailid '.$detailid." are the same</div>";
      } else {
        echo '<div class="k">Duplicate events with detailid "'.$detailid.'" are the same</div>';
      }
    }
  }
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
        print_r($asset['children']); // Shows all the children of the folder
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
  global $asset_type, $asset_children_type, $data, $event_names, $total, $o, $cron, $all_event_assets;
  if (!is_array($asset["children"]->child)) {
    $asset["children"]->child=array($asset["children"]->child);
  }
  $children = array();
  foreach($asset["children"]->child as $child) {
    array_push($children, $child->path->path);
  }
  foreach($event_names as $event_n) {
    if (in_array($asset['path'].'/'.$event_n, $children)) {
      // echo "<div class='k'>".$event_n." exists</div>";
      if (pagetest($event_n)) {
        readPage($client, $auth, array ('type' => $asset_children_type, 'path' => array ('path' => $asset['path'].'/'.$event_n, 'siteName' => CASCADE_SITE_PREFIX.'news-events') ), $asset_children_type, $event_n);
      }


    } else if (in_array($asset['path'].'/_archived/'.$event_n, $all_event_assets) ) {
      if ($cron) {
        $o[3] .= '<h4>'.$event_n.' (Archived)</h4>';
      } else {
        echo '<h4>'.$event_n.'</h4><div class="k">Archived</div>';
      }
      $total['k']++;
    } else if (in_array($asset['path'].'/_inactive/'.$event_n, $all_event_assets) ) {
      if ($cron) {
        $o[3] .= '<h4>'.$event_n.' (Inactive)</h4>';
      } else {
        echo '<h4>'.$event_n.'</h4><div class="k">Inactive</div>';
      }
      $total['k']++;


    } else {
      // echo "<div class='k'>".$event_n." will be created.</div>";
      $destFolder = array ('type' => 'folder', 'id' => $asset['id']);
      $copyParams = array ("newName" => $event_n, 'destinationContainerIdentifier' => $destFolder, "doWorkflow" => false);
      // The asset you're $copying
      $copying = array ('type' => 'page', 'id' => '432f506e7f0000021b1b5de78cbd125c' );	

      if ($_POST['action'] == 'edit' || $cron) {
        $copy = $client->copy ( array ('authentication' => $auth, 'identifier' => $copying, 'copyParameters' => $copyParams ) );
      }
      if ($copy->copyReturn->success == 'true') {
        if ($cron) {
          $o[2] .= '<div style="color:#090;">Created successfully: '.$event_n."</div>";
        } else {
          echo '<div class="s">Created successfully: '.$event_n.'</div>';
        }
        $total['s']++;
        readPage($client, $auth, array ('type' => $asset_children_type, 'path' => array ('path' => $asset['path'].'/'.$event_n, 'siteName' => CASCADE_SITE_PREFIX.'news-events') ), $asset_children_type, $event_n);
      } else {
        if ($cron) {
          $o[1] .= '<div style="padding:3px;color:#fff;background:#c00;">Creation failed: '.$event_n.'<div>'.extractMessage($result)."</div></div>";
        } else {
          echo '<div class="f">Creation failed: '.$event_n.'<div>'.extractMessage($result).'</div></div>';
        }
        $total['f']++;
      }
      
    }
  }
}

function readPage($client, $auth, $id, $type, $event_n) {
  global $asset_type, $asset_children_type, $data, $o, $cron;
  $reply = $client->read ( array ('authentication' => $auth, 'identifier' => $id ) );
  if ($reply->readReturn->success == 'true') {
    $asset = ( array ) $reply->readReturn->asset->$asset_children_type;
    $name = '';
    if (!$asset['path']) {$name = $asset['name'];}
    if ($_POST['asset'] == 'on' && !$cron) {
      echo '<h4><a href="'.CMS_OPEN_PATH.$asset['id'].'&type='.$type.'#highlight">'.$asset['path'].$name."</a></h4>";
    }
    if ($cron) {
      $o[3] .= '<div style="color:#090;"><a href="'.CMS_OPEN_PATH.$asset['id'].'&type='.$type.'#highlight">'.$asset['path'].$name."</a></div>";
    }
    
    if (edittest($asset)) {
      if (!$cron) {echo '<div class="page">';}
      if ($_POST['before'] == 'on' && !$cron) {
        echo '<button class="btn" href="#bModal'.$asset['id'].'" data-toggle="modal">View Before</button><div id="bModal'.$asset['id'].'" class="modal hide" tabindex="-1" role="dialog" aria-hidden="true"><div class="modal-body">';
          print_r($asset); // Shows the page in all its glory
        echo '</div></div>';
        echo "<script type='text/javascript'>var page_".$asset['id']." = ";
        print_r(json_encode($asset));
        echo '; console.log(page_'.$asset['id'].')';
        echo "</script>";
      }

      
      editPage($client, $auth, $asset, $event_n);
      if (!$cron) {echo '</div>';}
    }
    
  } else {  
    if ($cron) {
      $o[1] .= '<div style="padding:3px;color:#fff;background:#c00;">Failed to read page: '.$id."</div>";
    } else {
      echo '<div class="f">Failed to read page: '.$id.'</div>';
    }
  }
}


function editPage($client, $auth, $asset, $event_n) {
  global $total, $asset_type, $asset_children_type, $data, $changed, $o, $cron, $publish;
  
  changes($asset, $event_n);
  
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
        $o[2] .= '<div style="color:#090;">Edit success: <a href="'.CMS_OPEN_PATH.$asset['id'].'&type='.$type.'#highlight">'.$asset['path']."</a></div>";
      } else {
        echo '<div class="s">Edit success</div>';
      }
      $total['s']++;
      
      array_push($publish, $asset["id"]);
      
    } else {
      if ($cron) {
        $o[1] .= '<div style="padding:3px;color:#fff;background:#c00;">Edit failed: <a href="'.CMS_OPEN_PATH.$asset['id'].'&type='.$type.'#highlight">'.$asset['path']."</a></div>";
      } else {
        echo '<div class="f">Edit failed: '.$asset['path'].'<div>'.extractMessage($result).'</div></div>';
      }
      $total['f']++;
    }
  } else {  
    if (!$cron) {
      echo '<div class="k">No changes needed</div>';
    }
    $total['k']++;
  }
}


?>
