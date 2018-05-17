<?php
date_default_timezone_set('America/New_York');

include_once(__DIR__.'/../rollbar-init.php');
use \Rollbar\Rollbar;
use \Rollbar\Payload\Level;


$title = 'Cron job to create an Info DD block, assign it, and grant access to faculty pages that do not have one';

// $type_override = 'page';
$start_asset = '2891e3f87f00000101b7715d1ba2a7fb';

function pagetest($child) {
  if (isset($_GET['name'])) {
    if (preg_match("/^".$_GET['name']."[a-z]/",$child->path->path)) {
      return true;
    }
  } else {
    if ($child->path->path != 'index' && preg_match('/^[a-z]/',$child->path->path)) {
      return true;
    }
  }
}
function foldertest($child) {
  return false;
}
function edittest($asset) {
  if (preg_match('/slc-faculty\/Faculty/', $asset["contentTypePath"]))
    return true;
}

function arrayContainsInfoBlock(array $myArray, $word) {
  foreach ($myArray as $element) {
    if ($element->identifier == $word) {
      return true;
    }
  }
  return false;
}

function changes(&$asset, $blockID) {
  /* If you wish to use $changed, make sure it's global, and set it to false. 
   * When something is changed, it becomes true: */
  global $changed;
  $changed = false;
  if ( arrayContainsInfoBlock($asset["structuredData"]->structuredDataNodes->structuredDataNode, "info-block") ) {
    foreach ($asset["structuredData"]->structuredDataNodes->structuredDataNode as $sdnode) {
      if ($sdnode->identifier == "info-block") {
        $sdnode->blockPath = 'faculty-blocks/'.$asset['name'];
        $sdnode->blockId = $blockID;
        $changed = true;
      }
    }
  } else {
    
    $info = new stdClass();
    $info->type = 'asset';
    $info->identifier = 'info-block';
    $info->assetType = 'block';
    $info->blockPath = 'faculty-blocks/'.$asset['name'];
    $info->blockId = $blockID;
    
    array_push($asset["structuredData"]->structuredDataNodes->structuredDataNode, $info);
    $changed = true;
    
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
      $o[3] .= $asset['path']."\n".CMS_OPEN_PATH.$asset['id'].'&type='.$asset_children_type."\n";
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
      $o[1] .= 'Failed to read page: '.print_r($id, true)."\n";
    } else {
      echo '<div class="f">Failed to read page: '.print_r($id, true).'</div>';
    }
  }
}


function editPage($client, $auth, $asset) {
  global $total, $asset_type, $asset_children_type, $data, $changed, $o, $cron;
  
  if ( arrayContainsInfoBlock($asset["structuredData"]->structuredDataNodes->structuredDataNode, "info-block") ) {
    foreach ($asset["structuredData"]->structuredDataNodes->structuredDataNode as $sdnode) {
      if ($sdnode->identifier == "info-block") {
        if (!preg_match('/[0-9]/', $sdnode->blockId) ) {
          createAssignAccess($client, $auth, $asset);
        }
      }
    }
  } else {
    createAssignAccess($client, $auth, $asset);
  }
}




function createAssignAccess($client, $auth, $asset) {
  global $total, $asset_type, $asset_children_type, $data, $changed, $o, $cron;
  if ($_POST['action'] == 'edit' || $cron) {
    
    // Test to see if an Info block already exists with the same name
    $folder = $client->read ( array ('authentication' => $auth, 'identifier' => array ('type' => 'folder', 'id' => 'e0550c0a7f00000235706368275431ca' ) ) );
    if ($folder->readReturn->success == 'true') {
      $children = ( array ) $folder->readReturn->asset->folder;
      $blockID = '';
      foreach($children["children"]->child as $child) {
        if ($child->path->path == 'faculty-blocks/'.$asset['name']) {
          $blockID = $child->id;
        }
      }
      
      if ($blockID == '') { // If a faculty block doesn't exist for that faculty member already, create one...
        $copy = $client->copy ( array ('authentication' => $auth, 'identifier' => array('type' => 'block_XHTML_DATADEFINITION', 'id' => 'e05452df7f000002357063689eb57431'), 'copyParameters' => array('newName'=> $asset['name'], 'destinationContainerIdentifier' => array('id' =>'e0550c0a7f00000235706368275431ca', type => 'folder'), 'doWorkflow'=>false) ) );
        if ($copy->copyReturn->success == 'true') {

          if ($cron) {
            $o[3] .= 'Info Block Copy success for '.$asset['name']."\n";
          } else {
            echo '<div class="s">Info Block Copy success</div>';
          }
          $total['s']++;
          
          // Get the ID of the newly-created Info block
          $folder = $client->read ( array ('authentication' => $auth, 'identifier' => array ('type' => 'folder', 'id' => 'e0550c0a7f00000235706368275431ca' ) ) );
          if ($folder->readReturn->success == 'true') {
            $children = ( array ) $folder->readReturn->asset->folder;
            foreach($children["children"]->child as $child) {
              if ($child->path->path == 'faculty-blocks/'.$asset['name']) {
                $blockID = $child->id;
              }
            }
          } else {
            if ($cron) {
              $o[1] .= 'Failed to read info blocks folder to find the ID of the block'."\n";
            } else {
              echo '<div class="f">Failed to read info blocks folder to find the ID of the block</div>';
            }
          }
        } else {
          if ($cron) {
            $o[1] .= 'Info Block Copy failed: '.$asset['path']."\n";
          } else {
            $result = $client->__getLastResponse();
            echo '<div class="f">Info Block Copy failed: '.$asset['path'].'<div>'.htmlspecialchars(extractMessage($result)).'</div></div>';
          }
          $total['f']++;
        }
      }
    } else {
      if ($cron) {
        $o[1] .= 'Failed to read info blocks folder to see if a faculty block existed'."\n";
      } else {
        echo '<div class="f">Failed to read info blocks folder to see if a faculty block existed</div>';
      }
    }
  }

  // Assign the Info block to the faculty page
  changes($asset, $blockID);

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
        $o[2] .= 'Edit success: '.$asset['path']."\n".CMS_OPEN_PATH.$asset['id'].'&type='.$asset_children_type."\n";
      } else {
        echo '<div class="s">Edit success</div>';
      }
      $total['s']++;

      // Find out if there's an email username (and so by extension, a Cascade user)
      $email = false;
      foreach ($asset["structuredData"]->structuredDataNodes->structuredDataNode as $sdnode) {
        if ($sdnode->identifier == "bio") {
          foreach ($sdnode->structuredDataNodes->structuredDataNode as $datanode) {
            if ($datanode->identifier == "email" && $datanode->text != '') {
              $email = $datanode->text;
            }
          }
        }
      }

      // If there is an email, we assign there's a Cascade user by the same name and assign that user Write access to the Info block
      if ($email) {
        $blockAsset = array ('type' => 'block_XHTML_DATADEFINITION', 'id' => $blockID );
        $reply = $client->readAccessRights ( array ('authentication' => $auth, 'identifier' => $blockAsset ) );
        if ($reply->readAccessRightsReturn->success == 'true') {
          $accessRightsInformation = $reply->readAccessRightsReturn->accessRightsInformation;
  
          $accessToAdd = array('level' => 'write', 'type' => 'user', 'name' => $email);

          if (!is_array($accessRightsInformation->aclEntries->aclEntry))
            $accessRightsInformation->aclEntries->aclEntry=array($accessRightsInformation->aclEntries->aclEntry);
          array_push($accessRightsInformation->aclEntries->aclEntry, $accessToAdd);

          $editAccess = $client->editAccessRights ( array ('authentication' => $auth, 'accessRightsInformation' => $accessRightsInformation, 'applyToChildren' => false ) );
          if ($editAccess->editAccessRightsReturn->success == 'true') {
            if ($cron) {
              $o[2] .= 'Edit rights success: '.$asset['path']."\n".CMS_OPEN_PATH.$blockID.'&type=block_XHTML_DATADEFINITION'."\n";
            } else {
              echo '<div class="s">Edit rights success</div>';
            }
            
            $total['s']++;
          } else {
            
            if ($cron) {
              $o[1] .= 'Edit rights failed: '.$asset['path']."\n";
            } else {
              $result = $client->__getLastResponse();
              echo '<div class="f">Edit rights failed: '.$asset['path'].'<div>'.extractMessage($result).'</div></div>';
            }
            
            $total['f']++;
          }

        } else {
          if ($cron) {
            $o[1] .= 'Access Read failed'.print_r($blockAsset, true)."\n";
          } else {
            echo '<div class="f">Access Read failed</div>';
          }
          
        }
      } else {  
        if ($cron) {
          $o[1] .= 'No email, no access!'."\n";
        } else {
          echo 'No email, no access!';
        }
      }

    } else {
      if ($_POST['debug'] == 'on' || $cron) {
        $result = $client->__getLastResponse();
      }
      if ($cron) {
        $o[1] .= 'Edit failed: '.$asset['path']."\n".CMS_OPEN_PATH.$asset['id'].'&type='.$asset_children_type."\n".htmlentities(extractMessage($result))."\n\n";
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
