<?php
date_default_timezone_set('America/New_York');
$title = 'Read all pages, looking for ones with suspect content';

// $type_override = 'page';
$start_asset = '019ab6c77f00000101f92de57a5e5aaf,019b3b507f000002221c3dfed24ee171,047360737f00000101f92de55e007d02,0499307f7f0000020102c06596359dc2,052704657f00000215fab3c519b8e6a9,0760b47d7f00000101fa0f190ef7de81,0ffd1b5f7f00000101f92de5f1bee48e';
// $start_asset = '1304eb1e7f0000022b80da556a82a730,1697b03b7f00000101f92de5bdae9213,2829e1f07f0000021312656b9b656c5d,289c1ac97f00000101b7715d14978ffc,2bd741d47f0000021312656bde305a48,2f7dcabc7f00000101f92de527bf1fa7';
// $start_asset = '3bb277007f00000209340e798d372f55,3beb3e397f00000209340e79b7d2e67f,51d8cb0d7f00000101f92de5c3401157,5272debc7f00000101f92de5f336e998,548d20737f00000101f92de555b84e14,548fa6e57f00000101f92de5767a87bd';
// $start_asset = '63e913ea7f00000101f92de5a15894ea,640f30657f00000101f92de54b4647e7,6b8db64e7f000002007f6ff80c64293c,6b8f3c757f000002007f6ff8b16b362a,73fa785a7f00000250ffdf132efb9565,75e224457f00000101f92de500562ba4,778ad1ae7f00000101f92de5c4381945';
// $start_asset = '77bd3c077f00000101f92de5ba74a03c,8cba587a7f0000025a3b8730fb8a4620,8e0a7b777f00000278855613817cf6ed,931fa1d97f0000020f1c572aef743886,94a215b67f000001015d84e00c037aaf,9810140b7f00000100279c883b64b2cd';
// $start_asset = 'a551401d7f00000274a0ceef3d1113fc,a5784ced7f00000101f92de5e263be1b,af3e1a647f000001016a5ae9e825dd0f,b70b131e7f00000100279c88b0ebe56f';
// $start_asset = 'b1e7beea7f00000100279c882788d82e,ab880f697f0000021a23b0063cc5fd6f'; /* (news+mag) */
// $start_asset = 'c3d213c17f00000101f92de53291da62,c621c0d17f00000101f92de5212d40b7,c62291407f00000101f92de5b4b37193,caf8346d7f00000244547c9d7fa9f62d,cc912c097f0000020102c065cff41289,cd67add77f00000101f92de53c71ae3e,cd70cce97f00000101f92de5b87356bc';
// $start_asset = 'd89892d67f0000022e208d44695f11af,def503967f00000204ada1dcfca14657,e02d9e887f000002095adf3c628dd7d9,e59cc45a7f00000100c46dcf503b2144,28068f5f7f0000022d407b91de6bed3b,1f3dbc877f0000024a873afa56b38976,18e5a01e7f00000206d20ad8cf6d9894';
// $start_asset = '40b3d26d7f000002017feaf9495c23d9,f95157927f000002672330a666e12472,f7cfec677f0000024ae410ca99ed2e30,f95172ab7f000002672330a65e3a4124,f940a60d7f000002672330a663045257';

// The above does not include these sites: slc-cat-ugrad, slc-cat-grad, slc-faculty, wwww_archived
// $start_asset = '817373157f00000101f92de5bea1554a';
// $start_asset = '4e9e12a97f000001015d84e03ea3fb26';
// $start_asset = '2891e3f87f00000101b7715d1ba2a7fb';


function pagetest($child) {
    return true;
}
function foldertest($child) {
  if (!preg_match('/^_admin/', $child->path->path) && !preg_match('/^media/', $child->path->path))
    return true;
}
function edittest($asset) {
    return true;
}

function changes(&$asset, $type) {
  global $changed;
  $changed = false;
  
  if ($_POST['action'] == 'edit') {
    if ($asset["structuredData"]) {
      foreach ($asset["structuredData"]->structuredDataNodes->structuredDataNode as $group) {
        if ($group->identifier == 'main_column') {
          foreach ($group->structuredDataNodes->structuredDataNode as $field) {
            if ($field->identifier == 'content-section' && $field->text == 'Content Section') {
              outputData('content-section', $asset);
            }
          }
        }
      }
    }
    if ($asset["structuredData"]) {
      foreach ($asset["structuredData"]->structuredDataNodes->structuredDataNode as $group) {
        if ($group->identifier == 'related') {
          foreach ($group->structuredDataNodes->structuredDataNode as $field) {
            if ($field->identifier == 'include' && $field->text == 'Yes') {
              outputData('related', $asset);
            }
          }
        }
      }
    }
    if ($asset["structuredData"]) {
      foreach ($asset["structuredData"]->structuredDataNodes->structuredDataNode as $group) {
        if ($group->identifier == 'page_footer') {
          foreach ($group->structuredDataNodes->structuredDataNode as $field) {
            if ($field->identifier == 'include' && $field->text == 'Yes') {
              outputData('page_footer', $asset);
            }
          }
        }
      }
    }
    if ($asset["structuredData"]) {
      foreach ($asset["structuredData"]->structuredDataNodes->structuredDataNode as $group) {
        if ($group->identifier == 'flash-movie') {
          foreach ($group->structuredDataNodes->structuredDataNode as $field) {
            if ($field->identifier == 'write' && $field->text == 'Yes') {
              outputData('flash-movie', $asset);
            }
          }
        }
      }
    }
    if ($asset["structuredData"]) {
      foreach ($asset["structuredData"]->structuredDataNodes->structuredDataNode as $group) {
        if ($group->identifier == 'php-dynamic') {
          foreach ($group->structuredDataNodes->structuredDataNode as $field) {
            if ($field->identifier == 'config' && $field->text != '') {
              outputData('php-dynamic', $asset, $field->text);
            }
          }
        }
      }
    }
  }
  
}

if (!$cron) {include('html_header.php');}

function outputData($file, $asset, $output = '') {
  $myFile = "indexes/".$file.".html";
  $fh = fopen($myFile, 'a') or die("can't open file");
  $str = '<div class="'.$output.'"><a href="https://cms.slc.edu:8443/entity/open.act?id='.$asset['id'].'&type=page#highlight">'.$output.' — '.$asset['siteName'].'://'.$asset['path']."</a></div>\n";
  fwrite($fh, $str);
  fclose($fh);
}

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
        print_r($asset); // Shows all the children of the folder, as well as the folder data
      echo '</div></div>';
    }
    changes($asset, 'folder');
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
  
  changes($asset, 'page');
  
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
    $total['k']++;
  }
}

?>