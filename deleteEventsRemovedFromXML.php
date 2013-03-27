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
  $from = '2012-09-01';
}
if (isset($_GET['to'])) {
  $to = $_GET['to'];
} else {
  $to = '2013-08-31';
}

$events = simplexml_load_file('http://my.slc.edu/feeds/events/?cal=5&from='.$from.'&to='.$to, 'SimpleXMLElement',LIBXML_NOCDATA);
$private_events = simplexml_load_file('http://my.slc.edu/feeds/events/?cal=2&from='.$from.'&to='.$to, 'SimpleXMLElement',LIBXML_NOCDATA);
// The full year
// $events = simplexml_load_file('http://my.slc.edu/feeds/events/?cal=5&from=2012-09-01&to=2013-08-31', 'SimpleXMLElement',LIBXML_NOCDATA);
// $private_events = simplexml_load_file('http://my.slc.edu/feeds/events/?cal=2&from=2012-09-01&to=2013-08-31', 'SimpleXMLElement',LIBXML_NOCDATA);

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
$title = 'Delete event pages that have been removed from the events xml feeds';

$type_override = 'folder';
$start_asset = '682153de7f00000269720b2a7e4cd04f';

$message = 'Set ?from=yyyy-mm-dd&to=yyyy-mm-dd';

$children = array();

function pagetest($child) {
  // global $event_names;
  // if (in_array($child->name, $event_names))
    return false;
}
function foldertest($child) {
  // if (preg_match('/^_[a-z]/', $child->path->path))
    return false;
}
function edittest($asset) {
  // if (preg_match('/[a-z]/', $asset["contentTypePath"]))
    return true;
}

function changes(&$asset) {
  /* If you wish to use $changed, make sure it's global, and set it to false. 
   * When something is changed, it becomes true: */
  // global $changed;
  // $changed = false;
  // if ($asset["metadata"]->teaser != 'test') {$changed = true;}
  // $asset["metadata"]->teaser = 'test';
}


if (!$cron) {
  include('html_header.php');
}

foreach ($children as $child) {
  $name = str_replace('events/2012-2013/','',$child->path->path);
  if (in_array($name, $event_names) ) {
    echo '<div class="k">'.$name.' will be kept</div>';
  } else {
    if ($_POST['action'] == 'edit' || $cron) {
      $delete = $client->delete ( array ('authentication' => $auth, 'identifier' => array ('type' => $asset_children_type, 'id' => $child->id ) ) );
      if ($delete->deleteReturn->success == 'true') {
        echo '<div class="s">'.$name.' was deleted</div>';
      } else {
        echo '<div class="f">'.$name.' could not be deleted</div>';
        print_r($delete);
      }
    } else {
      echo '<div class="d">'.$name.' will be deleted</div>';
    }
  }
}

if (!$cron) {
  echo '<input type="checkbox" class="hidden" id="EAexpand'.$asset['id'].'"><label class="fullpage" for="EAexpand'.$asset['id'].'">';
    print_r($children);
    print_r($event_names);
    // print_r($events); // Shows all the events in the XML feeds
  echo '</label>';
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
    if ($_POST['children'] == 'on') {
      echo '<input type="checkbox" class="hidden" id="Aexpand'.$asset['id'].'"><label class="fullpage" for="Aexpand'.$asset['id'].'">';
        print_r($asset["children"]); // Shows all the children of the folder
      echo '</label>';
    }
    indexFolder($client, $auth, $asset);
  } else {
    echo '<div class="f">Failed to read folder: '.$asset["path"].'</div>';
  }
}
function indexFolder($client, $auth, $asset) {
  global $asset_type, $asset_children_type, $data, $o, $cron, $children;
  if (!is_array($asset["children"]->child)) {
    $asset["children"]->child=array($asset["children"]->child);
  }
  foreach($asset["children"]->child as $child) {
    if ($child->path->path != 'events/2012-2013/_archived' && $child->path->path != 'events/2012-2013/_inactive' && $child->path->path != 'events/2012-2013/index') {
      array_push($children, $child);
    }
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
    $asset = ( array ) $reply->readReturn->asset->$asset_children_type;
    if ($_POST['asset'] == 'on') {
      $name = '';
      if (!$asset['path']) {$name = $asset['name'];}
      echo '<h4><a href="https://cms.slc.edu:8443/entity/open.act?id='.$asset['id'].'&type='.$type.'#highlight">'.$asset['path'].$name."</a></h4>";
    }
    
    if (edittest($asset)) {
      echo '<div class="page">';
      if ($_POST['before'] == 'on') {
        echo '<input type="checkbox" class="hidden" id="Bexpand'.$asset['id'].'"><label class="fullpage" for="Bexpand'.$asset['id'].'">';
          print_r($asset); // Shows the page in all its glory
        echo '</label>';
      }

      echo "<script type='text/javascript'>var page_".$asset['id']." = ";
      print_r(json_encode($asset));
      echo '; console.log(page_'.$asset['id'].')';
      echo "</script>";
      
      editPage($client, $auth, $asset);
      echo '</div>';
    }
    
  } else {
    echo '<div class="f">Failed to read page: '.$id.'</div>';
  }
}


function editPage($client, $auth, $asset) {
  global $total, $asset_type, $asset_children_type, $data, $changed, $o, $cron;
  
  changes($asset);
  
  if ($_POST['after'] == 'on') {
    echo '<input type="checkbox" class="hidden" id="Aexpand'.$asset['id'].'"><label class="fullpage" for="Aexpand'.$asset['id'].'">';
      print_r($asset); // Shows the page as it will be
    echo '</label>';
  }
  
  if ($changed == true) {
    if ($_POST['action'] == 'edit') {
      $edit = $client->edit ( array ('authentication' => $auth, 'asset' => array($asset_children_type => $asset) ) );
    }
    if ($edit->editReturn->success == 'true') {
      echo '<div class="s">Edit success</div>';
      $total['s']++;
    } else {
      echo '<div class="f">Edit failed: '.$asset['path'].'<div>'.extractMessage($result).'</div></div>';
      $total['f']++;
    }
  } else {
    echo '<div class="k">No changes needed</div>';
    $total['k']++;
  }
}


?>
