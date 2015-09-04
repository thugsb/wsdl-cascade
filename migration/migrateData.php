<?php
date_default_timezone_set('America/New_York');
$title = 'Migrate data from old DD to new via interim DD';

// $type_override = 'page';
$start_asset = '898fea49c0a8022b3b519fac2af23643';

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
  if (preg_match('/^[a-z]/', $child->path->path) && !preg_match('/media/', $child->path->path))
    return true;
}
function edittest($asset) {
  if (preg_match('/tpl-default-interim/', $asset["contentTypePath"]))
    return true;
}

function changes(&$asset) {
  /* If you wish to use $changed, make sure it's global, and set it to false. 
   * When something is changed, it becomes true: */
  global $changed, $total;
  if ($asset['contentTypeId'] == '899184bec0a8022b3b519fac8a850c42') {
		$changed = true;


		$newnode = createNode('text', 'data', false, 'tpl-default');
		array_unshift($asset["structuredData"]->structuredDataNodes->structuredDataNode, $newnode);
	
	
		$settings = createNode('group', 'group-settings', true);
		$foundPageHeading = false;
		foreach ($asset["metadata"]->dynamicFields->dynamicField as $meta) {
			if ($meta->name = 'page-heading') {
				$foundPageHeading = true;
				if ($meta->fieldValues->fieldValue->value == '') {
					$settings->structuredDataNodes->structuredDataNode[0] = createNode('text', 'page-heading', false, 'Display Name');
				} else {
					$settings->structuredDataNodes->structuredDataNode[0] = createNode('text', 'page-heading', false, 'Custom');
				}
				$settings->structuredDataNodes->structuredDataNode[1] = createNode('text', 'custom-page-heading', false, $meta->fieldValues->fieldValue->value);
			}
		}
		if (!$foundPageHeading) {echo "<div class='f'>Didn't find a page heading.</div>";}
		$settings->structuredDataNodes->structuredDataNode[2] = createNode('asset', 'image', false, '', 'file');

	
	
		foreach ($asset["structuredData"]->structuredDataNodes->structuredDataNode as $sdnode) {
			if ($sdnode->identifier == "main_column") {
				foreach ($sdnode->structuredDataNodes->structuredDataNode as $subnode) {
				
					if ($subnode->identifier == 'syndicate-from') {
						if ($subnode->pageId != '') {echo "<div class='f'>Syndicate From is selected.</div>"; $total['f']++;}
					}
				
					if ($subnode->identifier == 'data-definition-block') {
						if ($subnode->blockId != '') {
							$newnode = createNode('group', 'group-primary', true);
							$newnode->structuredDataNodes->structuredDataNode[0] = createNode('text', 'status', false, 'On');
							$newnode->structuredDataNodes->structuredDataNode[1] = createNode('text', 'type', false, 'External Block');
							$newnode->structuredDataNodes->structuredDataNode[2] = createNode('group', 'group-block', true);
							$newnode->structuredDataNodes->structuredDataNode[2]->structuredDataNodes->structuredDataNode[0] = createNode('text', 'type', false);
							$newnode->structuredDataNodes->structuredDataNode[2]->structuredDataNodes->structuredDataNode[1] = createNode('asset', 'block', false, '', 'block', $subnode->blockId);
							array_push($asset["structuredData"]->structuredDataNodes->structuredDataNode, $newnode);
							echo "<div class='k'>External Block needs a type, and check its placement.</div>";
							$primaryOn = true;
						}
					}
				
					if ($subnode->identifier == 'content') {
						if ($subnode->text != '') {
							$newnode = createNode('group', 'group-primary', true);
							$newnode->structuredDataNodes->structuredDataNode[0] = createNode('text', 'status', false, 'On');
							$newnode->structuredDataNodes->structuredDataNode[1] = createNode('text', 'type', false, 'Text');
							$newnode->structuredDataNodes->structuredDataNode[2] = createNode('group', 'group-text', true);
							$newnode->structuredDataNodes->structuredDataNode[2]->structuredDataNodes->structuredDataNode[0] = createNode('text', 'wysiwyg', false, $subnode->text);
							array_push($asset["structuredData"]->structuredDataNodes->structuredDataNode, $newnode);
							echo "<div class='s'>Main Column WYSIWYG content will be copied into Primary.</div>";
							$primaryOn = true;
						}
					}
				
				}
			}
		
			if ($sdnode->identifier == "left_sidebar") {
				$left = false; $lefton = false;
				foreach ($sdnode->structuredDataNodes->structuredDataNode as $subnode) {
					if ($subnode->identifier == 'include') {
						if ($subnode->text == 'Yes') {
							$lefton = true;
						}
					}
					if ($subnode->identifier == 'content') {
						if ($subnode->text != '') {
							$left = true;
						}
					}
				}
				if ($left) {
					foreach ($sdnode->structuredDataNodes->structuredDataNode as $subnode) {
						if ($subnode->identifier == 'content') {
							$newnode = createNode('group', 'group-nav', true);
							if ($lefton) {
								$newnode->structuredDataNodes->structuredDataNode[0] = createNode('text', 'status', false, 'On');
							} else {
								$newnode->structuredDataNodes->structuredDataNode[0] = createNode('text', 'status', false, 'Off');
							}
							$newnode->structuredDataNodes->structuredDataNode[0] = createNode('text', 'type', false, 'Text');
							$newnode->structuredDataNodes->structuredDataNode[1] = createNode('group', 'group-text', true);
							$newnode->structuredDataNodes->structuredDataNode[1]->structuredDataNodes->structuredDataNode[0] = createNode('text', 'wysiwyg', false, $subnode->text);
							array_push($asset["structuredData"]->structuredDataNodes->structuredDataNode, $newnode);
							echo "<div class='s'>Left Sidebar content will be copied into Nav.</div>";
							$navOn = true;
						}
					}
				}
			}
		
			if ($sdnode->identifier == "right_sidebar") {
				$right = false; $righton = false;
				foreach ($sdnode->structuredDataNodes->structuredDataNode as $subnode) {
					if ($subnode->identifier == 'include') {
						if ($subnode->text == 'Yes') {
							$righton = true;
						}
					}
					if ($subnode->identifier == 'content') {
						if ($subnode->text != '') {
							$right = true;
						}
					}
				}
				if ($right) {
					foreach ($sdnode->structuredDataNodes->structuredDataNode as $subnode) {
						if ($subnode->identifier == 'content') {
							$newnode = createNode('group', 'group-secondary', true);
							$newnode->structuredDataNodes->structuredDataNode[0] = createNode('text', 'status', false, ($righton ? 'On': 'Off'));
							$newnode->structuredDataNodes->structuredDataNode[1] = createNode('text', 'type', false, 'Text');
							$newnode->structuredDataNodes->structuredDataNode[2] = createNode('group', 'group-text', true);
							$newnode->structuredDataNodes->structuredDataNode[2]->structuredDataNodes->structuredDataNode[0] = createNode('text', 'wysiwyg', false, $subnode->text);
							array_push($asset["structuredData"]->structuredDataNodes->structuredDataNode, $newnode);
							echo "<div class='s'>Right Sidebar content will be copied into Secondary.</div>";
							$secondaryOn = true;
						}
					}
				}
			}
		
			if ($sdnode->identifier == "forms") {
				$form = false;
				if (!is_array($sdnode->structuredDataNodes->structuredDataNode)) {
					$sdnode->structuredDataNodes->structuredDataNode = array($sdnode->structuredDataNodes->structuredDataNode);
				}
				foreach ($sdnode->structuredDataNodes->structuredDataNode as $form) {
					$formOn = false; $formID = ''; $formType = ''; $formRe = ''; $formPos = '';
					foreach ($form->structuredDataNodes->structuredDataNode as $subnode) {
						if ($subnode->identifier == 'placement') {
							if ($subnode->text != 'Off') {
								$formOn = true;
								$formPos = $subnode->text;
								if ($subnode->text == 'Manual' || $subnode->text == 'Right Sidebar') {echo "<div class='k'>Form used to be in $subnode->text, but will be placed in primary.</div>";}
							}
						}
						if ($subnode->identifier == 'type') {
							$formType = $subnode->text;
						}
						if ($subnode->identifier == 'id') {
							$formID = $subnode->text;
						}
						if ($subnode->identifier == 'redirect') {
							$formRe = $subnode->text;
						}
					}
					if ($formID != '') {
						$newnode = createNode('group', 'group-primary', true);
						$newnode->structuredDataNodes->structuredDataNode[0] = createNode('text', 'status', false, ($formOn ? 'On' : 'Off') );
						$newnode->structuredDataNodes->structuredDataNode[1] = createNode('text', 'type', false, 'Form');
						$newnode->structuredDataNodes->structuredDataNode[2] = createNode('group', 'group-form', true);
						$newnode->structuredDataNodes->structuredDataNode[2]->structuredDataNodes->structuredDataNode[0] = createNode('text', 'type', false, $formType);
						$newnode->structuredDataNodes->structuredDataNode[2]->structuredDataNodes->structuredDataNode[1] = createNode('text', 'id', false, $formID);
						$newnode->structuredDataNodes->structuredDataNode[2]->structuredDataNodes->structuredDataNode[2] = createNode('text', 'redirect', false, $formRe);
						array_push($asset["structuredData"]->structuredDataNodes->structuredDataNode, $newnode);
						echo "<div class='k'>Form will be copied into Primary. It was previously in $formPos, so check its placement.</div>";
						$primaryOn = true;
					}
				}
			}
		
		
			if ($sdnode->identifier == "related") {
				foreach ($sdnode->structuredDataNodes->structuredDataNode as $subnode) {
					if ($subnode->identifier == 'include') {
						if ($subnode->text == 'Yes') {echo "<div class='f'>WARNING: Related Content is included. Fix this manually.</div>";}
					}
				}
			}
			if ($sdnode->identifier == "ssp") {
				foreach ($sdnode->structuredDataNodes->structuredDataNode as $subnode) {
					if ($subnode->identifier == 'embed') {
						if ($subnode->text == 'Yes') {echo "<div class='f'>WARNING: SSP is included. Fix this manually.</div>";}
					}
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
      $o[3] .= '<h4><a href="https://cms.slc.edu:8443/entity/open.act?id='.$asset['id'].'&type='.$type.'#highlight">'.$asset['path']."</a></h4>";
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
