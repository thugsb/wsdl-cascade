<?php
date_default_timezone_set('America/New_York');
$title = 'Update the date and locations of approved events';

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
  
  foreach ($asset["metadata"]->dynamicFields->dynamicField as $dyn) {
  	if ($dyn->name == 'detailid') {
  		$detailid = $dyn->fieldValues->fieldValue->value;
	  }
  }

  if ($detailid == '') {
  	if (!$cron) {echo "<div>This must be a manually created event.</div>";}
  	return;
  }
	
	//$to      = 'thugsb@gmail.com';
	$to      = 'tguiliano@sarahlawrence.edu';
	$headers = 'From: com@vm-www.slc.edu' . "\r\n";
	$headers .= 'MIME-Version: 1.0' . "\r\n";
	$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
	$headers .= 'Cc: stu@t.apio.ca, wjoell@sarahlawrence.edu' . "\r\n";
		
	$message = '<p><a target="_blank" href="https://cms.slc.edu:8443/entity/open.act?id='.$asset['id'].'&type=page">'.$asset['name'].'</a>.</p>';


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
		  		
		  		$s_change = 'Start time has changed from '. $old_date->format('Y-m-d H:i:s') .' to '. $new_date->format('Y-m-d H:i:s') ."<br/>\n";
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
		  		
		  		$e_change = 'End time has changed from '. $old_date->format('Y-m-d H:i:s') .' to '. $new_date->format('Y-m-d H:i:s') ."<br/>\n";
					$message .= $e_change;
	  			$dyn->fieldValues->fieldValue->value = $end_date;
	  			$changed = true;
	  		} else {
	  			if (!$cron) { echo '<div class="k">End is the same</div><br/>'; }
	  		}
		  }
	  	if ($dyn->name == 'location') {
	  		if ($dyn->fieldValues->fieldValue->value != $event_location) {
		  		$l_change = 'Location has changed from '. $dyn->fieldValues->fieldValue->value .' to '. $event_location ."<br/>\n";
	  			$message .= $l_change;
		  		$dyn->fieldValues->fieldValue->value = $event_location;
	  			$changed = true;
	  		} else {
	  			if (!$cron) { echo '<div class="k">Location is the same</div><br/>'; }
	  		}
		  }
	  }
	  if ($date_string != $event_date) {
		  $d_change = 'The DAY has changed from '. $date_string .' to '. $event_date .' and so <a style="background:#eaa" href="https://cms.slc.edu:8443/entity/move.act?id='.$asset['id'].'&type=page">this asset needs to be moved</a>.'."<br/>\n";
			$message .= $d_change;
	  } else {
	  	if (!$cron) { echo '<div class="k">The day is the same.</div>'; }
	  }

	} else { // $eventFound ?
		$message .= '<div class="f">This event was not found. As such, editing it failed. Is the $yearstart and $yearend set correctly?</div>';
		$subject = 'WARNING: Event does not exist in the XML: '.$asset['name'];
		if ($cron) {
			mail($to, $subject, $message, $headers);
		} else {
			echo $message.'<hr/>';
		}
	}
  
  if ($changed == true) {
		$subject = 'Event changed in the XML: '.$asset['name'];
		if ($cron) {
			mail($to, $subject, $message, $headers);
		} else {
			echo $message.'<hr/>';
		}
  }
}

if (!$cron) {include('../header.php');}



?>
