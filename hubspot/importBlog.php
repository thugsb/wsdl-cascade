<?php
date_default_timezone_set('GMT');

include_once(__DIR__.'/../rollbar-init.php');
use \Rollbar\Rollbar;
use \Rollbar\Payload\Level;


$title = 'Import Blog from Hubspot JSON to Cascade';

// $type_override = 'page';
$start_asset = '6e4829e8c0a8022b11e4367d7f93ee47';

$hs = json_decode( file_get_contents('hs_blogs.json') );

$existingPages = [];

function pagetest($child) {
  return false;
}
function foldertest($child) {
  return true;
}
function edittest($asset) {
  return true;
}

function changes(&$asset, $blog) {
  /* If you wish to use $changed, make sure it's global, and set it to false. 
   * When something is changed, it becomes true: */
  global $changed;
  $changed = false;

  if ($asset["metadata"]->title != $blog->title) {
    $changed = true;
    $asset["metadata"]->title = $blog->title;
    echo "Title\n";
  }

  $publishDate = date('Y-m-d\TH:i:s.v', $blog->publish_date / 1000);
  $publishDate .= 'Z';
  if ($asset["metadata"]->startDate != $publishDate) {
    $changed = true;
    $asset["metadata"]->startDate = $publishDate;
    echo "Date\n";
  }

  if ($asset["metadata"]->metaDescription != $blog->meta_description) {
    $changed = true;
    $asset["metadata"]->metaDescription = $blog->meta_description;
    echo "Description\n";
  }

  $summary = strip_tags($blog->post_summary, '<em>');
  if ($asset["metadata"]->teaser != $summary) {
    $changed = true;
    $asset["metadata"]->teaser = $summary;
    echo "Summary\n";
  }


  $strippedText = strip_tags($blog->post_body, '<p><em><a><strong><li><ol><ul>');
  $plainText = preg_replace('/\shref="\/([-a-z\/]+index)"/', ' href="Site://SarahLawrence.edu/$1"', $strippedText);

  if ($blog->featured_image != '') {
    preg_match('/\/(([-a-zA-Z0-9%\._]+)\.(jpg|png))/', $blog->featured_image, $matches);
    $imgName = $matches[1];
    $imgName = str_replace('%20', '-', $imgName);
    $imgName = str_replace('_', '-', $imgName);
    $imgPath = '_files/img/blog/' . $imgName;
    foreach ($asset["structuredData"]->structuredDataNodes->structuredDataNode as $sdnode) {
      // if ($sdnode->identifier == "group-settings") {
      //   foreach ($sdnode->structuredDataNodes->structuredDataNode as $settingsNode) {
      //     if ($settingsNode->identifier == 'image') {
      //       if ($settingsNode->filePath != $imgPath) {
      //         $settingsNode->filePath = $imgPath;
      //         $changed = true;
      //       }
      //     }
      //   } 
      // }
      if ($sdnode->identifier == "content") {
        if ($sdnode->structuredDataNodes->structuredDataNode[1]->text == 'Image' && $blog->featured_image != '') {
          if ($sdnode->structuredDataNodes->structuredDataNode[7]->structuredDataNodes->structuredDataNode[0]->filePath != $imgPath) {
            $sdnode->structuredDataNodes->structuredDataNode[7]->structuredDataNodes->structuredDataNode[0]->filePath = $imgPath;
            $changed = true;
            echo "Image\n";
          }
        }
        if ($sdnode->structuredDataNodes->structuredDataNode[1]->text == 'Text') {
          if ($sdnode->structuredDataNodes->structuredDataNode[3]->text != $plainText) {
            $sdnode->structuredDataNodes->structuredDataNode[3]->text = $plainText;
            $changed = true;
            echo "Text\n";
          }
        }
      }
    }
  } else {
    echo 'No-image';
    foreach ($asset["structuredData"]->structuredDataNodes->structuredDataNode as $sdnode) {
      if ($sdnode->identifier == "content") {
        if ($sdnode->structuredDataNodes->structuredDataNode[1]->text != 'Text') {
          $sdnode->structuredDataNodes->structuredDataNode[1]->text = 'Text';
          $changed = true;
          echo "Text\n";
        }
        if ($sdnode->structuredDataNodes->structuredDataNode[1]->text == 'Text') {
          if ($sdnode->structuredDataNodes->structuredDataNode[3]->text != $plainText) {
            $sdnode->structuredDataNodes->structuredDataNode[3]->text = $plainText;
            $changed = true;
            echo "Text\n";
          }
        }
      }
    }
  }
}

if (!$cron) {include(__DIR__.'/../html_header.php');}

foreach ($hs->blogs as $i => $blog) {
  if ( preg_match('/^campaign/', $blog->slug) && $blog->current_state != 'DRAFT' ) {
    preg_match('/^campaign\/(.+)$/', $blog->slug, $matches);
    $name = $matches[1];
    if ( in_array($name, $existingPages) ) {
      readPage($client, $auth, array ('type' => $asset_children_type, 'path' => array ('path' => $asset['path'].'/article/'.$name, 'siteName' => 'campaign.sarahlawrence.edu') ), 'page', $blog);
    } else {
      echo $name;
      $destFolder = array ('type' => 'folder', 'id' => '6e4829e8c0a8022b11e4367d7f93ee47');
      $copyParams = array ("newName" => $name, 'destinationContainerIdentifier' => $destFolder, "doWorkflow" => false);
      // The asset you're $copying
      if ($blog->featured_image == '') {
        $copying = array ('type' => 'page', 'id' => '6e48aabac0a8022b11e4367d7174ea7c' ); 
      } else {
        $copying = array ('type' => 'page', 'id' => 'd62af9a6c0a8022b74083c5ab80075b2' ); 
      }
      if ($_POST['action'] == 'edit') {
        $copy = $client->copy ( array ('authentication' => $auth, 'identifier' => $copying, 'copyParameters' => $copyParams ) );
      }
      if ($copy->copyReturn->success == 'true') {
        echo '<div class="s">Created successfully: '.$event_n.'</div>';
        $total['s']++;
        readPage($client, $auth, array ('type' => $asset_children_type, 'path' => array ('path' => $asset['path'].'/article/'.$name, 'siteName' => 'campaign.sarahlawrence.edu') ), 'page', $blog);
      } else {
        if ($_POST['debug'] == 'on' || $cron) {
          $result = $client->__getLastResponse();
        }
        echo '<div class="f">Creation failed: '.$event_n.'<div>'.extractMessage($result).'</div></div>';
        $total['f']++;
      }
    }
  }
}

// echo '<pre>';
// print_r($hs);
// echo '</pre>';


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
  global $asset_type, $asset_children_type, $data, $o, $cron, $total, $existingPages;
  if (!is_array($asset["children"]->child)) {
    $asset["children"]->child=array($asset["children"]->child);
  }
  foreach($asset["children"]->child as $child) {
    array_push($existingPages, str_replace('article/', '', $child->path->path) );
    if ($child->type == strtolower($asset_children_type)) {
      if (pagetest($child))
        readPage($client, $auth, array ('type' => $child->type, 'id' => $child->id), $child->type);
    } elseif ($child->type === strtolower($asset_type)) {
      if (foldertest($child))
        readFolder($client, $auth, array ('type' => $child->type, 'id' => $child->id));
    }
  }
}

function readPage($client, $auth, $id, $type, $blog) {
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
      
      editPage($client, $auth, $asset, $blog);
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


function editPage($client, $auth, $asset, $blog) {
  global $total, $asset_type, $asset_children_type, $data, $changed, $o, $cron;
  
  changes($asset, $blog);
  
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
