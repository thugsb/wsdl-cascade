<?php
date_default_timezone_set('GMT');

include_once(__DIR__.'/../rollbar-init.php');
use \Rollbar\Rollbar;
use \Rollbar\Payload\Level;


$title = 'Import Pages from Hubspot JSON to Cascade';

// $type_override = 'page';
$start_asset = 'fa1e9949c0a8022b06d5e847645d3e4e';

$hs = json_decode( file_get_contents('hs_pages.json') );

function pagetest($child) {
  return false;
}
function foldertest($child) {
  return false;
}
function edittest($asset) {
  return true;
}

function changes(&$asset, $page) {
  /* If you wish to use $changed, make sure it's global, and set it to false. 
   * When something is changed, it becomes true: */
  global $changed;
  $changed = false;

  $asd = $asset["structuredData"];

  if ($asset["metadata"]->title != $page->title) {
    $changed = true;
    $asset["metadata"]->title = $page->title;
    echo "Title\n";
  }

  if ($asset["metadata"]->displayName != $page->label) {
    $changed = true;
    $asset["metadata"]->displayName = $page->label;
    echo "displayName\n";
  }

  if (!$page->thanks) {
    if ( editNode($page->widgets->module_14283409281039348->body->form_to_use, ['group-form-content','group-form','id'], 'text', $asd ) ) {
      editNode('Hubspot', ['group-form-content','group-form','type'], 'text', $asd );
    }
  }

  if ($page->widget_containers->module_14283407904137244->widgets[0]->type == 'linked_image') {
    $matches = [];
    preg_match('/\/([-a-zA-Z0-9_]+\.(png|jpg))/', $page->widget_containers->module_14283407904137244->widgets[0]->body->src, $matches);
    $imageName = $matches[1];
    editNode('media/images/'.$imageName, ['group-content','image'], 'filePath', $asd);
  } elseif ($page->widget_containers->module_14283407904137244->widgets[0]->type == 'gallery' || $page->widget_containers->module_14283407904137244->widgets[0]->type == 'image_slider') {
    editNode('::CONTENT-XML-CHECKBOX::Yes', ['group-content', 'gallery-toggle'], 'text', $asd);
    echo '<div class="k">IMPORTANT: Gallery needs linking up. The images are:</div>';
    foreach ($page->widget_containers->module_14283407904137244->widgets[0]->body->slides as $key => $slide) {
      $matches = [];
      preg_match('/\/([-a-zA-Z0-9_]+\.(png|jpg))/', $slide->img_src, $matches);
      $imageName = $matches[1];
      echo $imageName . ' ';
    }
  } else {
    echo '<div style="color:#f00">module_14283407904137244->widgets[0] is not a linked_image, gallery or image_slider for page '. $page->url .'</div>';
  }


  if ($page->widget_containers->module_14283407904137244->widgets[1]->type == 'rich_text') {
    editNode($page->widget_containers->module_14283407904137244->widgets[1]->body->html, ['group-content','wysiwyg'], 'text', $asd);
  } else {
    echo '<div class="k">module_14283407904137244->widgets[1] is not a rich_text, it is '. $page->widget_containers->module_14283407904137244->widgets[1]->type .'. This is OK if [2] is rich_text...</div>';
    if ($page->widget_containers->module_14283407904137244->widgets[2]->type == 'rich_text') {
      echo '<div class="s">It is :)</div>';
      editNode($page->widget_containers->module_14283407904137244->widgets[2]->body->html, ['group-content','wysiwyg'], 'text', $asd);
    } else {
      echo '<div class="f">module_14283407904137244->widgets[1] is not a rich_text</div>';
    }
  }


  if ($page->widget_containers->module_14283405926493359->widgets[0]->type == 'rich_text') {
    editNode($page->widget_containers->module_14283405926493359->widgets[0]->body->html, ['group-form-content','wysiwyg'], 'text', $asd);
  } else {
    echo '<div class="f">module_14283407904137244->widgets[0] is not a rich_text</div>';
  }


  foreach ($asset["structuredData"]->structuredDataNodes->structuredDataNode as $sdnode) {
    if ($sdnode->identifier == "group-settings") {
      foreach ($sdnode->structuredDataNodes->structuredDataNode as $settingsnode) {
        if ($settingsnode->identifier == "remarketing") {
          $blockName = $page->thanks ? 'thanks-code' : 'landing-code';
          $expectedBlockPath = $asset['parentFolderPath'] . '/' . $blockName;
          if ($settingsnode->blockPath != $expectedBlockPath) {
            $settingsnode->blockPath = $expectedBlockPath;
            $changed = true;
            echo 'blockPath';
          }
        }
      }
    }


    if ($sdnode->identifier == "group-content") {
      foreach ($sdnode->structuredDataNodes->structuredDataNode as $contentnode) {
        if ($contentnode->identifier == "") {

        }
      }
    }



  }
}

function editNode($value, $layers, $field, $array) {
  global $changed;
  foreach ($array->structuredDataNodes->structuredDataNode as $node) {
    if ($node->identifier == $layers[0] ) {
      array_shift($layers);
      $count = count($layers);
      if ( $count == 0 ) {
        if ($node->$field != $value) {
          $node->$field = $value;
          $changed = true;
          $edited  = 'true';
          echo '<div class="k">Editing <code>' . $node->identifier . '</code></div>';
          return true;
        }
      } else {
        $returnValue = editNode($value, $layers, $field, $node);
      }
    }
  }
  return $returnValue;
}


if (!$cron) {include(__DIR__.'/../html_header.php');}

foreach ($hs->pages as $i => $page) {
  if ( $page->template_path == 'generated_layouts/3708457939.html' || $page->template_path == 'generated_layouts/3610973427.html' ) {
    $slugs = [
      'sarah-lawrence-college-in-cuba-thankyou',
      'finding-sarah-lawrence-thank-you',
      'sarah-lawrence-college-in-cuba',
      'finding-sarah-lawrence',
      'sarah-lawrence-college-in-beijing-0',
      'sarah-lawrence-college-in-beijing-thankyou-0'
    ];
      echo $page->slug;
    if ( in_array($page->slug, $slugs)) {
      echo $page->slug;
    // Get the path info
    if ( preg_match('/-0$/', $page->slug ) ) {
      // Thank-you page
      $page->thanks = true;
      $page->folderPath = preg_replace('/-0$/', '', $page->slug);
      $page->pageName = 'thank-you';
      $page->pagePath = $page->folderPath . '/' . $page->pageName;
    } else {
      $page->thanks = false;
      $page->folderPath = $page->slug;
      $page->pageName = 'index';
      $page->pagePath = $page->folderPath . '/' . $page->pageName;
    }
    if ($page->slug == "mfa-writing-sarah-lawrence-college-nonfiction") {
      $page->thanks = false;
      $page->folderPath = 'mfa-writing-sarah-lawrence-college';
      $page->pageName = 'nonfiction';
      $page->pagePath = $page->folderPath . '/' . $page->pageName;
    } elseif ($page->slug == "mfa-writing-sarah-lawrence-college-fiction") {
      $page->thanks = false;
      $page->folderPath = 'mfa-writing-sarah-lawrence-college';
      $page->pageName = 'fiction';
      $page->pagePath = $page->folderPath . '/' . $page->pageName;
    } elseif ($page->slug == "mfa-writing-sarah-lawrence-college-poetry") {
      $page->thanks = false;
      $page->folderPath = 'mfa-writing-sarah-lawrence-college';
      $page->pageName = 'poetry';
      $page->pagePath = $page->folderPath . '/' . $page->pageName;

    } elseif ($page->slug == "sarah-lawrence-college-in-beijing-0") {
      $page->thanks = false;
      $page->folderPath = 'sarah-lawrence-college-in-beijing';
      $page->pageName = 'index';
      $page->pagePath = $page->folderPath . '/' . $page->pageName;
    } elseif ($page->slug == "sarah-lawrence-college-in-beijing-thankyou-0") {
      $page->thanks = true;
      $page->folderPath = 'sarah-lawrence-college-in-beijing';
      $page->pageName = 'thank-you';
      $page->pagePath = $page->folderPath . '/' . $page->pageName;
    } elseif ($page->slug == "finding-sarah-lawrence-thank-you") {
      $page->thanks = true;
      $page->folderPath = 'finding-sarah-lawrence';
      $page->pageName = 'thank-you';
      $page->pagePath = $page->folderPath . '/' . $page->pageName;
    } elseif ($page->slug == "sarah-lawrence-college-in-cuba-thankyou") {
      $page->thanks = true;
      $page->folderPath = 'sarah-lawrence-college-in-cuba';
      $page->pageName = 'thank-you';
      $page->pagePath = $page->folderPath . '/' . $page->pageName;
    }

    // Create code blocks
    if (array_key_exists('submit',$_POST)) {
      if ($page->widgets->module_1429555843225755->body->value != '') {
        $cascadeFolder = array(type => 'folder', path => array( path => $page->folderPath, siteName => 'info.sarahlawrence.edu') );
        createXMLcodeBlocks( $cascadeFolder, $page );
      }
    }

    // Edit the page
    if (array_key_exists('submit',$_POST)) {
      $cascadePage = array( type => 'page', path => array( path => $page->pagePath, siteName => 'info.sarahlawrence.edu') );
      readPage( $client, $auth, $cascadePage, 'page', $page );
    }
    }
  }
}

// echo '<pre>';
// print_r($hs);
// echo '</pre>';


function createXMLcodeBlocks($cascadeFolder, $page) {
  global $client, $auth, $asset_type, $asset_children_type, $data, $o, $cron;
  $folder = $client->read ( array ('authentication' => $auth, 'identifier' => $cascadeFolder ) );
  if ($folder->readReturn->success == 'true') {
    
    $asset = ( array ) $folder->readReturn->asset->$asset_type;
    if ($_POST['folder'] == 'on') {
      echo "<h1>Folder: ".$asset["path"]."</h1>";
    }
    if ($_POST['children'] == 'on' && !$cron) {
      echo '<button class="btn" href="#cModal'.$asset['id'].'" data-toggle="modal">View Children</button><div id="cModal'.$asset['id'].'" class="modal hide" tabindex="-1" role="dialog" aria-hidden="true"><div class="modal-body">';
        print_r($asset["children"]); // Shows all the children of the folder
      echo '</div></div>';
    }

    $children = array();
    if (!is_array($asset["children"]->child)) {
      $asset["children"]->child=array($asset["children"]->child);
    }
    foreach($asset["children"]->child as $child) {
      if ($child->type == 'block_XML') {
        array_push($children, $child->path->path);
      }
    }

    $blockName = $page->thanks ? 'thanks-code' : 'landing-code';
    if ($page->pageName == 'nonfiction' || $page->pageName == 'fiction' || $page->pageName == 'poetry') {
      $blockName = $page->pageName . '-code';
    }

    if (!in_array($asset['name'] . '/' . $blockName, $children) ) {
      $destFolder = array ('type' => 'folder', 'id' => $asset['id']);
      $copyParams = array ("newName" => $blockName, 'destinationContainerIdentifier' => $destFolder, "doWorkflow" => false);
      // The asset you're $copying
      $copying = array ('type' => 'block_XML', 'id' => '05035b75c0a8022b5fe70b0253da8581' ); 
      if ($_POST['action'] == 'edit') {
        $copy = $client->copy ( array ('authentication' => $auth, 'identifier' => $copying, 'copyParameters' => $copyParams ) );
      }
      if ($copy->copyReturn->success == 'true') {
        echo '<div class="s">Created successfully: '.$blockName.'</div>';
        $total['s']++;
      } else {
        if ($_POST['debug'] == 'on' || $cron) {
          $result = $client->__getLastResponse();
        }
        echo '<div class="f">Creation failed: '.$blockName.'<div>'.extractMessage($result).'</div></div>';
        $total['f']++;
      }
    }

    $cascadeBlock = array ('type' => 'block_XML', 'path' => array ('path' => $asset['name'] . '/' . $blockName, 'siteName' => 'info.sarahlawrence.edu') );
    editBlock( $cascadeBlock, $page );

  } else {
    if ($cron) {
      $o[1] .= 'FAILED to read folder with given ID '.$id["id"]."\n";
    } else {
      echo '<div class="f">Failed to read folder: '.$id["id"].'</div>';
    }
  }
}

function editBlock($cascadeBlock, $page) {
  global $client, $auth, $o, $cron;
  $reply = $client->read ( array ('authentication' => $auth, 'identifier' => $cascadeBlock ) );
  if ($reply->readReturn->success == 'true') {
    // For some reason the names of asset differ from the returned object
    $returned_type = '';
    foreach ($reply->readReturn->asset as $t => $a) {
      if (!empty($a)) {$returned_type = $t;}
    }
    
    $asset = ( array ) $reply->readReturn->asset->$returned_type;
    if ($_POST['asset'] == 'on') {
      $name = '';
      if (!$asset['path']) {$name = $asset['name'];}
      echo '<h5><a href="https://cms.slc.edu:8443/entity/open.act?id='.$asset['id'].'&type=block">'.$asset['path']."</a></h5>";
    }
    echo '<div class="page">';
      if ($_POST['before'] == 'on' && !$cron) {
        echo '<button class="btn" href="#bModal'.$asset['id'].'" data-toggle="modal">View Before</button><div id="bModal'.$asset['id'].'" class="modal hide" tabindex="-1" role="dialog" aria-hidden="true"><div class="modal-body">';
          print_r($asset); // Shows the page in all its glory
        echo '</div></div>';
      }

      $xmlCode = '<system-xml>' . str_replace('\n','',preg_replace('/<!--[-]+/', '<!--', preg_replace('/[-]+-->/', '-->', $page->widgets->module_1429555843225755->body->value ) ) ) . '</system-xml>';
      $doc = @simplexml_load_string($xmlCode);
      if ($doc) {
        if ( $asset['xml'] == '<x/>' ) {
          $asset['xml'] = $xmlCode;
          echo 'Editing Block...';
          if ($_POST['action'] == 'edit' || $cron) {
            $edit = $client->edit ( array ('authentication' => $auth, 'asset' => array('xmlBlock' => $asset) ) );
          }
          if ($edit->editReturn->success == 'true') {
            echo '<div class="s">Edit success</div>';
            $total['s']++;
          } else {
            if ($_POST['debug'] == 'on' || $cron) {
              $result = $client->__getLastResponse();
            }
            echo '<div class="f">Edit failed: '.$asset['path'].'<div>'.htmlspecialchars(extractMessage($result)).'</div></div>';
            $total['f']++;
          }
        } else {
          echo '<div class="k">No changes needed</div>';
        }
      } else {
          echo '<div class="f">This is not valid XML. Copy it yourself.</div>';
      }

    echo '</div>';
    
  }
}

function readFolder($client, $auth, $id) {}

function indexFolder($client, $auth, $asset) {
  global $asset_type, $asset_children_type, $data, $o, $cron, $total, $existingPages;
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

function readPage($client, $auth, $id, $type, $page) {
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
      $o[3] .= $asset['path']."\n".'https://cms.slc.edu:8443/entity/open.act?id='.$asset['id'].'&type='.$asset_children_type."\n";
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
      
      editPage($client, $auth, $asset, $page);
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


function editPage($client, $auth, $asset, $page) {
  global $total, $asset_type, $asset_children_type, $data, $changed, $o, $cron;

  changes($asset, $page);
  
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
        $o[2] .= 'Edit success: '.$asset['path']."\n".'https://cms.slc.edu:8443/entity/open.act?id='.$asset['id'].'&type='.$asset_children_type."\n";
      } else {
        echo '<div class="s">Edit success</div>';
      }
      $total['s']++;
    } else {
      if ($_POST['debug'] == 'on' || $cron) {
        $result = $client->__getLastResponse();
      }
      if ($cron) {
        $o[1] .= 'Edit failed: '.$asset['path']."\n".'https://cms.slc.edu:8443/entity/open.act?id='.$asset['id'].'&type='.$asset_children_type."\n".htmlentities(extractMessage($result))."\n\n";
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
