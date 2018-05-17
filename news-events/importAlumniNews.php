<?php 
date_default_timezone_set('America/New_York');
$title = 'Import Alumni News from php array';

$start_asset = 'dd645236c0a8022b55184fda02fd375a';
$type_override = 'page';

function pagetest($child) {
	return true;
}
function foldertest($child) {
    return false;
}
function edittest($asset) {
    return true;
}
function changes(&$asset, $value) {
	global $changed;
	$changed = false;

	$startDate = date('Y-m-d\T14:00:00.000\Z', $value['dateObject']);

	if ($asset["metadata"]->title != $value['name']) {
		$changed = true;
		$asset["metadata"]->title = $value['name'];
	}
	if ($asset["metadata"]->startDate != $startDate ) {
		$changed = true;
		$asset["metadata"]->startDate = $startDate;
	}
	foreach ($asset["structuredData"]->structuredDataNodes->structuredDataNode as $sdnode) {
    	if ($sdnode->identifier == "content") {
			$changed = true;
    		$sdnode->text = $value['text'];
    	} else if ($sdnode->identifier == 'announceLink') {
			if (isset($value['url'])) {
	    		foreach ($sdnode->structuredDataNodes->structuredDataNode as $ancNode) {
	    			if ($ancNode->identifier == 'ancLinkExternal') {
	    				$ancNode->text = $value['url'];
						$changed = true;
	    			} else if ($ancNode->identifier == 'externalLinkCheck') {
						$ancNode->text = 'Yes';
						$changed = true;
	    			}
	    		}
	    	}
	    }
	}
}

include('../html_header.php');

include('alumniNews.php');

foreach ($articles as $key => $value) {
	// title
	// start date
	// wysiwyg
	// direct link
	// new tag yes
	$newsDate = strtotime($value['date']);
	$value['dateObject'] = $newsDate;
	$slug = date('Y-m-d-', $newsDate) . preg_replace("/[\s]/", '-', $value['name']);
	$slug = strtolower(preg_replace("/[^-A-Za-z0-9]/", '', $slug));
	echo $slug;
	$value['slug'] = $slug;

	$destFolder = array ('type' => 'folder', 'id' => '480f2e4bc0a8022b6dde97b1d7665498');
	$copyParams = array ("newName" => $slug, 'destinationContainerIdentifier' => $destFolder, "doWorkflow" => false);
	$copying = array ('type' => 'page', 'id' => 'dd645236c0a8022b55184fda02fd375a' );

	if ($_POST['action'] == 'edit' || $cron) {
		$copy = $client->copy ( array ('authentication' => $auth, 'identifier' => $copying, 'copyParameters' => $copyParams ) );
	}
	if ($copy->copyReturn->success == 'true') {
		if ($cron) {
			$o[2] .= '<div style="color:#090;">Created successfully: '.$slug."</div>";
		} else {
			echo '<div class="s">Created successfully: '.$slug.'</div>';
		}
		$total['s']++;
		readToEditPage($client, $auth, array ('type' => $asset_children_type, 'path' => array ('path' => '/alumni/news/'.$slug, 'siteName' => MAIN_SITE_NAME) ), $asset_children_type, $slug, $value);
	} else {
		if ($cron) {
			$o[1] .= '<div style="padding:3px;color:#fff;background:#c00;">Creation failed: '.$slug.'<div>'.extractMessage($result)."</div></div>";
		} else {
			echo '<div class="f">Creation failed: '.$slug.'<div>'.extractMessage($result).'</div></div>';
		}
		$total['f']++;
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


function readToEditPage($client, $auth, $id, $type, $slug, $value) {
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
      
      editPage($client, $auth, $asset, $slug, $value);
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

function editPage($client, $auth, $asset, $slug, $value) {
  global $total, $asset_type, $asset_children_type, $data, $changed, $o, $cron;

  changes($asset, $value);

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
        $o[2] .= '<div style="color:#090;">Edit success: <a href="'.CMS_OPEN_PATH.$asset['id'].'&type='.$type.'#highlight">'.$asset['path']."</a></div>";
      } else {
        echo '<div class="s">Edit success</div>';
      }
      $total['s']++;
    } else {
      if ($_POST['debug'] == 'on') {
        $result = $client->__getLastResponse();
      }
      if ($cron) {
        $o[1] .= '<div style="padding:3px;color:#fff;background:#c00;">Edit failed: <a href="'.CMS_OPEN_PATH.$asset['id'].'&type='.$type.'#highlight">'.$asset['path']."</a><div>".htmlspecialchars(extractMessage($result)).'</div></div>';
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