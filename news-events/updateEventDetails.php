<?php
date_default_timezone_set('America/New_York');
$title = 'Update the date and locations of approved events';

include_once(__DIR__.'/../rollbar-init.php');
use \Rollbar\Rollbar;
use \Rollbar\Payload\Level;

include_once('eventFolderIDs.php');

$start_asset = $year_folder;

$all_events = simplexml_load_file('http://my.slc.edu/feeds/events/?cal=2&from='.$yearstart.'&to='.$yearend, 'SimpleXMLElement',LIBXML_NOCDATA);


function pagetest($child) {
  return true;
}
function foldertest($child) {
  if (preg_match('/_pending/', $child->path->path))
		return true;
}
function edittest($asset) {
  if (preg_match('/tpl-events-item/', $asset["contentTypePath"]))
    return true;
}

function changes(&$asset) {
  global $changed, $all_events, $cron;
  $changed = false;
  
  $date_string = substr($asset['name'], 0, 10);
  
  preg_match('/eid[0-9]{6,6}$/', $asset['name'], $detailIDMatches);
  $detailid = substr($detailIDMatches[0], 3,6);
  // echo $detailid;

  if ($detailid == '') {
  	if (!$cron) {echo "<div>This must be a manually created event.</div>";}
  	return;
  }
	
	$to      = 'tguiliano@sarahlawrence.edu';
  $headers = 'From: com@vm-www.slc.edu' . "\r\n" . 'Cc: wjoell@sarahlawrence.edu';
		
	$message = $asset['name']."\n".'https://cms.slc.edu:8443/entity/open.act?id='.$asset['id'].'&type=page'."\n";


	$eventFound = false;
	foreach ($all_events->event as $i=>$event) {
		if ($detailid == $event->detailid) {
			$eventFound = true;
			$e_date = new DateTime();
			$unix_date = (integer)$event->date->unixbegin;
			$e_date->setTimestamp($unix_date);
			$event_date = $e_date->format('Y-m-d');
			
			$start_date = (integer)$event->date->unixbegin * 1000;
			$end_date = (integer)$event->date->unixend * 1000;
			$event_location = $event->location;
			break;
		}
	}

	if ($eventFound) {

	  foreach ($asset["metadata"]->dynamicFields->dynamicField as $dyn) {
	  	if ($dyn->name == 'begin') {
	  		if ($dyn->fieldValues->fieldValue->value != $start_date) {
		  		$old_date = new DateTime();
		  		$old_date->setTimestamp($dyn->fieldValues->fieldValue->value / 1000);
		  		$new_date = new DateTime();
		  		$new_date->setTimestamp($start_date / 1000);
		  		
		  		$s_change = 'Start time has changed from '. $old_date->format('Y-m-d H:i:s') .' to '. $new_date->format('Y-m-d H:i:s') ."\n";
	  			$message .= $s_change;
	  			$dyn->fieldValues->fieldValue->value = $start_date;
	  			$changed = true;
	  		} else {
	  			if (!$cron) { echo '<div class="k">Start is the same</div><br/>'; }
	  		}
		  }
	  	if ($dyn->name == 'end') {
	  		if ($dyn->fieldValues->fieldValue->value != $end_date) {
		  		$old_date = new DateTime();
		  		$old_date->setTimestamp($dyn->fieldValues->fieldValue->value / 1000);
		  		$new_date = new DateTime();
		  		$new_date->setTimestamp($end_date / 1000);
		  		
		  		$e_change = 'End time has changed from '. $old_date->format('Y-m-d H:i:s') .' to '. $new_date->format('Y-m-d H:i:s') ."\n";
					$message .= $e_change;
	  			$dyn->fieldValues->fieldValue->value = $end_date;
	  			$changed = true;
	  		} else {
	  			if (!$cron) { echo '<div class="k">End is the same</div><br/>'; }
	  		}
		  }
	  	if ($dyn->name == 'location') {
	  		if ($dyn->fieldValues->fieldValue->value != $event_location) {
		  		$l_change = 'Location has changed from '. $dyn->fieldValues->fieldValue->value .' to '. $event_location ."\n";
	  			$message .= $l_change;
		  		$dyn->fieldValues->fieldValue->value = $event_location;
	  			$changed = true;
	  		} else {
	  			if (!$cron) { echo '<div class="k">Location is the same</div><br/>'; }
	  		}
		  }
	  }
	  if ($date_string != $event_date) {
		  $d_change = 'The DAY has changed from '. $date_string .' to '. $event_date .' and so this asset needs to be moved.'."\n".'https://cms.slc.edu:8443/entity/move.act?id='.$asset['id'].'&type=page'."\n";
			$message .= $d_change;
	  } else {
	  	if (!$cron) { echo '<div class="k">The day is the same.</div>'; }
	  }

	} else { // $eventFound ?
    $subject = 'WARNING: Event does not exist in the XML'."\n";
		$message .= 'The event "'.$asset['name'].'" was not found. As such, editing it failed. Is the $yearstart and $yearend set correctly?';
		if ($cron) {
      $response = Rollbar::log(Level::warning(), $subject . $message);
      if (!$response->wasSuccessful()) {
        mail($to, 'Logging with Rollbar FAILED ' . $_GET['s'], $subject . $message, $headers);
      }
		} else {
			echo $message.'<hr/>';
		}
	}
  
  if ($changed == true) {
		$subject = 'Event changed in the XML'."\n".$asset['name'];
		if ($cron) {
      $response = Rollbar::log(Level::info(), $subject . $message);
      if (!$response->wasSuccessful()) {
        mail($to, 'Logging with Rollbar FAILED ' . $_GET['s'], $subject . $message, $headers);
      }
		} else {
			echo $message.'<hr/>';
		}
  }
}

if (!$cron) {include('../html_header.php');}



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
      $o[3] .= $asset['path']."\n".'https://cms.slc.edu:8443/entity/open.act?id='.$asset['id'].'&type='.$type."\n";
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
      
      editPage($client, $auth, $asset);
      if (!$cron) {echo '</div>';}
    }
    
  } else {
    if ($cron) {
      $o[1] .= 'FAILED to read page: '.$id."\n";
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
    if ($_POST['action'] == 'edit' || $cron) {
      $edit = $client->edit ( array ('authentication' => $auth, 'asset' => array($asset_children_type => $asset) ) );
    }
    if ($edit->editReturn->success == 'true') {
      if ($cron) {
        $o[2] .= 'Edit success: '.$asset['path']."\n".'https://cms.slc.edu:8443/entity/open.act?id='.$asset['id'].'&type='.$type."\n";
      } else {
        echo '<div class="s">Edit success</div>';
      }
      $total['s']++;

      if ( !strpos($asset['path'], '_pending') ) {
        $publish = $client->publish ( array ('authentication' => $auth, 'publishInformation' => array('identifier' => array('type' => $asset_children_type, 'id' => $asset['id']), 'unpublish' => false ) ) );
        if ($publish->publishReturn->success == 'true') {
          if ($cron) {
            $o[2] .= $asset['path'].' was published';
          } else {
            echo '<div class="s">'.$asset['path'].' was published</div>';
          }
          $total['s']++;
        } else {
          if ($cron) {
            $o[1] .= $asset['path'].' FAILED to publish';
          } else {
            echo '<div class="f">'.$asset['path'].' could not be published</div>';
            print_r($publish);
          }
          $total['f']++;
        }
      }

    } else {
      if ($_POST['debug'] == 'on' || $cron) {
        $result = $client->__getLastResponse();
      }
      if ($cron) {
        $o[1] .= 'Edit FAILED: '.$asset['path']."\n".htmlspecialchars(extractMessage($result))."\n\n";
      } else {
        echo '<div class="f">Edit failed: '.$asset['path']."\n".'https://cms.slc.edu:8443/entity/open.act?id='.$asset['id'].'&type='.$type."\n".htmlspecialchars(extractMessage($result)).'</div></div>';
      }
      $total['f']++;
    }
  } else {
    if (!$cron) {echo '<div class="k">No changes needed</div>';}
    $total['k']++;
  }
}


?>
