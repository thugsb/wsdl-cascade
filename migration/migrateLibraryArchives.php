<?php
date_default_timezone_set('America/New_York');
$title = 'Migrate data from old Library and Archives DD to new via interim DD';

// $type_override = 'page';
$start_asset = 'a7bcce9dc0a8022b240c49c521ceeb4c';

// Optionally override the container/child types
// $asset_type = 'assetFactoryContainer';
// $asset_children_type = 'assetFactory';

function createNode($type, $id, $nodes = false, $text = '', $at=false, $bi=false, $bp=false, $fi=false, $fp=false, $pi=false, $pp=false, $si=false, $sp=false, $re=false) {
	$newnode = new StdClass();
	$newnode->type = $type;
	$newnode->identifier = $id;
	if ($nodes == true) {
		$newnode->structuredDataNodes = new StdClass();
		$newnode->structuredDataNodes->structuredDataNode = array();
	} else {
		$newnode->structuredDataNodes = false;
	}
	$newnode->text = $text;
	if ($at) {$newnode->assetType = $at;}
	if ($bi) {$newnode->blockId = $bi;}
	if ($bp) {$newnode->blockPath = $bp;}
	if ($fi) {$newnode->fileId = $fi;}
	if ($fp) {$newnode->filePath = $fp;}
	if ($pi) {$newnode->pageId = $pi;}
	if ($pp) {$newnode->pagePath = $pp;}
	if ($si) {$newnode->symlinkId = $si;}
	if ($sp) {$newnode->symlinkPath = $sp;}
	if ($re) {$newnode->recycled = $re;}
	return $newnode;
}


function pagetest($child) {
  if (preg_match('/[a-z]/', $child->path->path))
    return true;
}
function foldertest($child) {
  if (!preg_match('/media/', $child->path->path))
    return true;
}
function edittest($asset) {
  if (preg_match('/dev-library-archives-interim/', $asset["contentTypePath"]))
    return true;
}

function changes(&$asset) {
  /* If you wish to use $changed, make sure it's global, and set it to false. 
   * When something is changed, it becomes true: */
  global $changed, $total;
  if ($asset['contentTypeId'] == 'c070c8cdc0a8022b11fbce64c8fa2cd6') {
		$changed = true;


		$newnode = createNode('text', 'data', false, 'tpl-default');
		array_unshift($asset["structuredData"]->structuredDataNodes->structuredDataNode, $newnode);
	
	
		$settings = createNode('group', 'group-settings', true);
		$settings->structuredDataNodes->structuredDataNode[0] = createNode('text', 'page-heading', false, 'Use Metadata');
		$settings->structuredDataNodes->structuredDataNode[1] = createNode('text', 'custom-page-heading', false, '');
		$settings->structuredDataNodes->structuredDataNode[2] = createNode('asset', 'image', false, '', 'file');


	
		$extBlock = false;
		foreach ($asset["structuredData"]->structuredDataNodes->structuredDataNode as $sdnode) {
			if ($sdnode->identifier == "text1") {
				if ($sdnode->text != '') {
					$newnode = createNode('group', 'group-primary', true);
					$newnode->structuredDataNodes->structuredDataNode[0] = createNode('text', 'status', false, 'On');
					$newnode->structuredDataNodes->structuredDataNode[1] = createNode('text', 'type', false, 'Text');
					$newnode->structuredDataNodes->structuredDataNode[2] = createNode('group', 'group-text', true);
					$newnode->structuredDataNodes->structuredDataNode[2]->structuredDataNodes->structuredDataNode[0] = createNode('text', 'wysiwyg', false, $sdnode->text);
					array_push($asset["structuredData"]->structuredDataNodes->structuredDataNode, $newnode);
					echo "<div class='s'>Text1 WYSIWYG content will be copied into Primary.</div>";
					$primaryOn = true;
				}
			}
			if ($sdnode->identifier == "content") {
				if ($sdnode->text != '') {
					$newnode = createNode('group', 'group-primary', true);
					$newnode->structuredDataNodes->structuredDataNode[0] = createNode('text', 'status', false, 'On');
					$newnode->structuredDataNodes->structuredDataNode[1] = createNode('text', 'type', false, 'Text');
					$newnode->structuredDataNodes->structuredDataNode[2] = createNode('group', 'group-text', true);
					$newnode->structuredDataNodes->structuredDataNode[2]->structuredDataNodes->structuredDataNode[0] = createNode('text', 'wysiwyg', false, $sdnode->text);
					array_push($asset["structuredData"]->structuredDataNodes->structuredDataNode, $newnode);
					echo "<div class='s'>Content WYSIWYG content will be copied into Primary.</div>";
					$primaryOn = true;
				}
			}
			if ($sdnode->identifier == "external-block") {
				if ($sdnode->blockId != '') {
					$extBlock = true;
					$newnode = createNode('group', 'group-primary', true);
					$newnode->structuredDataNodes->structuredDataNode[0] = createNode('text', 'status', false, 'On');
					$newnode->structuredDataNodes->structuredDataNode[1] = createNode('text', 'type', false, 'External Block');
					$newnode->structuredDataNodes->structuredDataNode[2] = createNode('group', 'group-block', true);
					$newnode->structuredDataNodes->structuredDataNode[2]->structuredDataNodes->structuredDataNode[0] = createNode('text', 'type', false);
					$newnode->structuredDataNodes->structuredDataNode[2]->structuredDataNodes->structuredDataNode[1] = createNode('asset', 'block', false, '', 'block', $sdnode->blockId);
					array_push($asset["structuredData"]->structuredDataNodes->structuredDataNode, $newnode);
					echo "<div class='k'>External Block needs a type, and check its placement.</div>";
					$primaryOn = true;
				}
			}
			if ($sdnode->identifier == "block-placement" && $extBlock) {
				echo "<div class='k'>External Block placement was $sdnode->text.</div>";
			}
			if ($sdnode->identifier == "form") {
				$includeForm = 'No';
				$formID = false;
				foreach ($sdnode->structuredDataNodes->structuredDataNode as $subnode) {
					if ($subnode->identifier == 'include') {
						if ($subnode->text == 'Yes') {
							$includeForm = 'Yes';
						}
					}
					if ($subnode->identifier == 'id') {
						if ($subnode->text != '') {
							$formID = $subnode->text;
						}
					}
				}
				if ($formID) {
					echo "<div class='k'>The form ID is $formID. Is it active? $includeForm</div>";
				}
			}
			if ($sdnode->identifier == "xml") {
				if ($sdnode->blockId != '') {
					$newnode = createNode('group', 'group-primary', true);
					$newnode->structuredDataNodes->structuredDataNode[0] = createNode('text', 'status', false, 'On');
					$newnode->structuredDataNodes->structuredDataNode[1] = createNode('text', 'type', false, 'External Block');
					$newnode->structuredDataNodes->structuredDataNode[2] = createNode('group', 'group-block', true);
					$newnode->structuredDataNodes->structuredDataNode[2]->structuredDataNodes->structuredDataNode[0] = createNode('text', 'type', false, 'Finding Aid XML');
					$newnode->structuredDataNodes->structuredDataNode[2]->structuredDataNodes->structuredDataNode[1] = createNode('asset', 'block', false, '', 'block', $sdnode->blockId);
					array_push($asset["structuredData"]->structuredDataNodes->structuredDataNode, $newnode);
					echo "<div class='s'>A Finding Aid XML has been assigned.</div>";
					$primaryOn = true;
				}
			}
			if ($sdnode->identifier == "pdf") {
				if ($sdnode->fileId != '') {
					$newnode = createNode('group', 'group-primary', true);
					$newnode->structuredDataNodes->structuredDataNode[0] = createNode('text', 'status', false, 'On');
					$newnode->structuredDataNodes->structuredDataNode[1] = createNode('text', 'type', false, 'Linked File');
					$newnode->structuredDataNodes->structuredDataNode[2] = createNode('group', 'group-file', true);
					$newnode->structuredDataNodes->structuredDataNode[2]->structuredDataNodes->structuredDataNode[0] = createNode('text', 'type', false, 'Finding Aid PDF');
					$newnode->structuredDataNodes->structuredDataNode[2]->structuredDataNodes->structuredDataNode[1] = createNode('asset', 'file', false, '', 'file', false, false, $sdnode->fileId);
					array_push($asset["structuredData"]->structuredDataNodes->structuredDataNode, $newnode);
					echo "<div class='s'>A Finding Aid PDF has been assigned.</div>";
					$primaryOn = true;
				}
			}
			if ($sdnode->identifier == "cycle") {
				if ($sdnode->text == 'No') {
					echo "<div class='k'>This exhibit is set to NOT cycle.</div>";
				}
			}
			if ($sdnode->identifier == "exhibit") {
				if ($sdnode->blockId != '') {
					$newnode = createNode('group', 'group-primary', true);
					$newnode->structuredDataNodes->structuredDataNode[0] = createNode('text', 'status', false, 'On');
					$newnode->structuredDataNodes->structuredDataNode[1] = createNode('text', 'type', false, 'External Block');
					$newnode->structuredDataNodes->structuredDataNode[2] = createNode('group', 'group-block', true);
					$newnode->structuredDataNodes->structuredDataNode[2]->structuredDataNodes->structuredDataNode[0] = createNode('text', 'type', false, 'Exhibit Block');
					$newnode->structuredDataNodes->structuredDataNode[2]->structuredDataNodes->structuredDataNode[1] = createNode('asset', 'block', false, '', 'block', $sdnode->blockId);
					array_push($asset["structuredData"]->structuredDataNodes->structuredDataNode, $newnode);
					echo "<div class='s'>An Exhibit block has been assigned.</div>";
					$primaryOn = true;
				}
			}


			if ($sdnode->identifier == "audio") {
				$audioPlace = false;
				foreach ($sdnode->structuredDataNodes->structuredDataNode as $subnode) {
					if ($subnode->identifier == 'include') {
						$audioPlace = $subnode->text;
					}
					if ($subnode->identifier == 'file') {
						if ($subnode->fileId != '') {
							$newnode = createNode('group', 'group-primary', true);
							$newnode->structuredDataNodes->structuredDataNode[0] = createNode('text', 'status', false, 'On');
							$newnode->structuredDataNodes->structuredDataNode[1] = createNode('text', 'type', false, 'Linked File');
							$newnode->structuredDataNodes->structuredDataNode[2] = createNode('group', 'group-file', true);
							$newnode->structuredDataNodes->structuredDataNode[2]->structuredDataNodes->structuredDataNode[0] = createNode('text', 'type', false, 'Audio');
							$newnode->structuredDataNodes->structuredDataNode[2]->structuredDataNodes->structuredDataNode[1] = createNode('asset', 'file', false, '', 'file', false, false, $subnode->fileId);
							array_push($asset["structuredData"]->structuredDataNodes->structuredDataNode, $newnode);
							echo "<div class='k'>This page has audio. It was placed $audioPlace.</div>";
							$primaryOn = true;
						}
					}
				}
			}



			
			
			if ($sdnode->identifier == "ssp") {
				foreach ($sdnode->structuredDataNodes->structuredDataNode as $subnode) {
					if ($subnode->identifier == 'embed') {
					  if ($subnode->text == 'Yes') {$galOn = true;} else {$galOn = false;}
					}
					if ($subnode->identifier == 'director') {
					  $galType = $subnode->text;
					}
					if ($subnode->identifier == 'type') {
					  $galSet = $subnode->text;
					}
					if ($subnode->identifier == 'id') {
					  $galID = $subnode->text;
						if ($galID != '') {
						  $gal = true;
						}
					}
					if ($subnode->identifier == 'attributes') {
    				foreach ($subnode->structuredDataNodes->structuredDataNode as $galnode) {
    					if ($galnode->identifier == 'ratio') {
    					  $galRatio = $galnode->text;
    					}
    				}
					}
					if ($subnode->identifier == 'flashvars') {
    				foreach ($subnode->structuredDataNodes->structuredDataNode as $galnode) {
    					if ($galnode->identifier == 'navAppearance') {
    					  $galApp = $galnode->text;
    					}
    					if ($galnode->identifier == 'nav-location') {
    					  $galLoc = $galnode->text;
    					}
    				}
					}
				}
				
				if ($gal) {
					$galnode = createNode('group', 'group-primary', true);
					$galnode->structuredDataNodes->structuredDataNode[0] = createNode('text', 'status', false, ($galOn ? 'On' : 'Off') );
					$galnode->structuredDataNodes->structuredDataNode[1] = createNode('text', 'type', false, 'Image Gallery');
					$galnode->structuredDataNodes->structuredDataNode[2] = createNode('group', 'group-gallery', true);
					$galnode->structuredDataNodes->structuredDataNode[2]->structuredDataNodes->structuredDataNode[0] = createNode('text', 'type', false, $galType);
					$galnode->structuredDataNodes->structuredDataNode[2]->structuredDataNodes->structuredDataNode[1] = createNode('text', 'id', false, $galID);
					$galnode->structuredDataNodes->structuredDataNode[2]->structuredDataNodes->structuredDataNode[2] = createNode('text', 'set', false, $galSet);
					$galnode->structuredDataNodes->structuredDataNode[2]->structuredDataNodes->structuredDataNode[3] = createNode('text', 'style', false, 'Carousel');
					$galnode->structuredDataNodes->structuredDataNode[2]->structuredDataNodes->structuredDataNode[4] = createNode('group', 'group-config', true);
					$galnode->structuredDataNodes->structuredDataNode[2]->structuredDataNodes->structuredDataNode[4]->structuredDataNodes->structuredDataNode[0] = createNode('text', 'ratio', false, $galRatio);
					$galnode->structuredDataNodes->structuredDataNode[2]->structuredDataNodes->structuredDataNode[4]->structuredDataNodes->structuredDataNode[1] = createNode('text', 'nav-appearance', false, $galApp);
					$galnode->structuredDataNodes->structuredDataNode[2]->structuredDataNodes->structuredDataNode[4]->structuredDataNodes->structuredDataNode[2] = createNode('text', 'nav-location', false, $galLoc);
					array_push($asset["structuredData"]->structuredDataNodes->structuredDataNode, $galnode);
					echo "<div class='s'>SSP will be copied into Primary.</div>";
					$primaryOn = true;
				}
			}
			
			
			if ($sdnode->identifier == "gallery") {
				foreach ($sdnode->structuredDataNodes->structuredDataNode as $subnode) {
					if ($subnode->identifier == 'size') {
						if ($subnode->text == 'Big' || $subnode->text == 'Small') {echo "<div class='f'>WARNING: Gallery is included. Its size if $subnode->text. Fix this manually.</div>";}
					}
				}
			}
		}

		$settings->structuredDataNodes->structuredDataNode[3] = createNode('text', 'intro', false, ($introOn ? '::CONTENT-XML-CHECKBOX::On' : '::CONTENT-XML-CHECKBOX::'));
		$settings->structuredDataNodes->structuredDataNode[4] = createNode('text', 'nav', false, ($navOn ? '::CONTENT-XML-CHECKBOX::On' : '::CONTENT-XML-CHECKBOX::'));
		$settings->structuredDataNodes->structuredDataNode[5] = createNode('text', 'primary', false, ($primaryOn ? '::CONTENT-XML-CHECKBOX::On' : '::CONTENT-XML-CHECKBOX::'));
		$settings->structuredDataNodes->structuredDataNode[6] = createNode('text', 'secondary', false, ($secondaryOn ? '::CONTENT-XML-CHECKBOX::On' : '::CONTENT-XML-CHECKBOX::'));

		array_push($asset["structuredData"]->structuredDataNodes->structuredDataNode, $settings);
	}
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
