<?php
date_default_timezone_set('America/New_York');
$title = 'Update Discipline Folder Areas-of-Study metadata';

$year = '2017-2018';

$start_asset = '817373157f00000101f92de5bea1554a';

// This just selects to show the folder names instead of asset names when the script is first loaded
$script = '$(document).ready(function() { if ( $("input[name=login]").val() == "" ) {$("#folder").prop("checked", true); $("#asset").prop("checked", false);} });';

// Optionally override the container/child types
$asset_type = 'folder';
$asset_children_type = 'reference';
$disciplineFolderID = '';
$relatedDisciplines = [];
$relatedAreas = [];

function pagetest($child) {
  global $year;
  if (preg_match('/^[a-z][-a-z\/]+\/'.$year.'\/related/',$child->path->path) )
    return true;
}
function foldertest($child) {
  global $year;
  $isClusterFolder = false;
  if ( preg_match('/^[a-z][-a-z\/]+$/',$child->path->path) || preg_match('/^[a-z][-a-z\/]+\/'.$year.'$/',$child->path->path) || preg_match('/^[a-z][-a-z\/]+\/'.$year.'\/related$/',$child->path->path) )
    return true;
}
function edittest($asset) {
  if ($asset["referencedAssetType"] == 'page')
    return true;
}


$areaLookup = [
'africana-studies' => 'Cross-Disciplinary Paths',
'anthropology' => 'History and the Social Sciences',
'architecture-and-design-studies' => 'Cross-Disciplinary Paths',
'art-history' => 'Humanities',
'asian-studies' => 'History and the Social Sciences',
'biology' => 'Science and Mathematics',
'chemistry' => 'Science and Mathematics',
'chinese' => 'Humanities',
'classics' => 'Humanities',
'cognitive-and-brain-science' => 'Cross-Disciplinary Paths',
'computer-science' => 'Science and Mathematics',
'dance' => 'Creative and Performing Arts',
'development-studies' => 'Cross-Disciplinary Paths',
'economics' => 'History and the Social Sciences',
'environmental-studies' => 'History and the Social Sciences',
'ethnic-and-diasporic-studies' => 'Cross-Disciplinary Paths',
'film-history' => 'Humanities',
'filmmaking-and-moving-image-arts' => 'Creative and Performing Arts',
'french' => 'Humanities',
'games-interactive-art-new-genres' => 'Cross-Disciplinary Paths',
'gender-and-sexuality-studies' => 'Cross-Disciplinary Paths',
'geography' => 'History and the Social Sciences',
'german' => 'Humanities',
'greek' => 'Humanities',
'health-science-and-society' => 'Cross-Disciplinary Paths',
'history' => 'History and the Social Sciences',
'international-studies' => 'Cross-Disciplinary Paths',
'italian' => 'Humanities',
'japanese' => 'Humanities',
'languages-and-literatures' => 'Humanities',
'latin' => 'Humanities',
'latin-american-and-latinoa-studies' => 'Cross-Disciplinary Paths',
'lesbian-gay-bisexual-and-transgender-studies' => 'Cross-Disciplinary Paths',
'literature' => 'Humanities',
'mathematics' => 'Science and Mathematics',
'middle-eastern-and-islamic-studies' => 'Cross-Disciplinary Paths',
'music' => 'Creative and Performing Arts',
'philosophy' => 'Humanities',
'physics' => 'Science and Mathematics',
'political-economy' => 'Cross-Disciplinary Paths',
'politics' => 'History and the Social Sciences',
'pre-health-program' => 'Cross-Disciplinary Paths',
'psychology' => 'History and the Social Sciences',
'public-policy' => 'History and the Social Sciences',
'religion' => 'Humanities',
'russian' => 'Humanities',
'science-and-mathematics' => 'Science and Mathematics',
'social-science' => 'Cross-Disciplinary Paths',
'sociology' => 'History and the Social Sciences',
'spanish' => 'Humanities',
'theatre' => 'Creative and Performing Arts',
'urban-studies' => 'Cross-Disciplinary Paths',
'visual-and-studio-arts' => 'Creative and Performing Arts',
'writing' => 'Creative and Performing Arts'
];

if (!$cron) {include('../html_header.php');}



function readFolder($client, $auth, $id) {
  global $asset_type, $asset_children_type, $data, $o, $cron, $disciplineFolderID, $relatedDisciplines, $relatedAreas;
  $folder = $client->read ( array ('authentication' => $auth, 'identifier' => $id ) );
  if ($folder->readReturn->success == 'true') {

    $asset = ( array ) $folder->readReturn->asset->$asset_type;

    if (preg_match('/^[a-z][-a-z\/]+$/',$asset['path'] ) ) {
      $disciplineFolderID = $asset['id'];
      $relatedDisciplines = [];
      $relatedAreas = [];
    }

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
  global $asset_type, $asset_children_type, $data, $o, $cron, $total, $year, $disciplineFolderID, $relatedDisciplines, $relatedAreas, $areaLookup;
  if (!is_array($asset["children"]->child)) {
    $asset["children"]->child=array($asset["children"]->child);
  }

  // We only want to edit the metadata for Clusters
  $isClusterFolder = false;
  if (is_array($asset["metadata"]->dynamicFields->dynamicField) ) {
    foreach ($asset["metadata"]->dynamicFields->dynamicField as $dyn) {
      if ($dyn->name == "folder-type" && $dyn->fieldValues->fieldValue->value == 'Cluster') {
        $isClusterFolder = true;
      }
    }
  }
  // This IF will single out the Clusters from the rest of the disciplines, and still allow the subfolders of clusters to be read
  if ( $asset['path'] == '/' || $isClusterFolder || preg_match('/^[a-z][-a-z\/]+\/'.$year.'/',$asset['path']) ) {
    if (!$cron && $isClusterFolder) { echo '<div class="s">Cluster</div>'; }
    foreach($asset["children"]->child as $child) {
      if ($child->type == strtolower($asset_children_type)) {
        if (pagetest($child))
          readPage($client, $auth, array ('type' => $child->type, 'id' => $child->id), $child->type);
      } elseif ($child->type === strtolower($asset_type)) {
        if (foldertest($child))
          readFolder($client, $auth, array ('type' => $child->type, 'id' => $child->id));
      }
    }
  } else {
    if (!$cron) { echo '<div class="k">Not a Cluster</div>'; }
  }

  // This will now take place only after the $relatedDisciplines array has been fully filled for this Cluster (after all the references in the related folder have been read)
  if ( preg_match('/^[a-z][-a-z\/]+\/'.$year.'\/related$/',$asset['path'] ) ) {
    // Get array of the Areas
    foreach ($relatedDisciplines as $disciplineFolderName) {
      if ( array_key_exists($disciplineFolderName, $areaLookup) ) {
        if ( !in_array($areaLookup[$disciplineFolderName], $relatedAreas) && $areaLookup[$disciplineFolderName] != 'Cross-Disciplinary Paths' ) {
          array_push($relatedAreas, $areaLookup[$disciplineFolderName]);
        }
      } else {
        if ($cron) {
          $o[1] .= '<div style="padding:3px;color:#fff;background:#c00;">Warning: Discipline "'. $disciplineFolderName .'" is not in lookup array</div>';
        } else {
          echo '<div class="f">Warning: Discipline "'. $disciplineFolderName .'" is not in lookup array</div>';
        }
        $total['f']++;
      }
    }
    sort($relatedAreas);



    // Read the discipline folder (again)
    $folder = $client->read ( array ('authentication' => $auth, 'identifier' => array( 'id' => $disciplineFolderID, 'type' => 'folder' ) ) );
    if ($folder->readReturn->success == 'true') {
      $asset = ( array ) $folder->readReturn->asset->$asset_type;

      $existingAreas = [];
      foreach ($asset["metadata"]->dynamicFields->dynamicField as $dyn) {
        if ($dyn->name == "areas") {
          foreach ($dyn->fieldValues->fieldValue as $existingArea) {
            array_push($existingAreas, $existingArea->value);
          }
          sort($existingAreas);


          if ($existingAreas != $relatedAreas) {
            if (!$cron) {
              echo '<div class="k">The areas have changed (existing and update respectively):</div>';
              echo '<pre>';
              print_r( $existingAreas );
              print_r( $relatedAreas );
              echo '</pre>';
            }
            $dyn->fieldValues->fieldValue = [];
            foreach ($relatedAreas as $areaName) {
              $val = new StdClass();
              $val->value = $areaName;
              array_push($dyn->fieldValues->fieldValue, $val);
            }
            // Now we need to rebuild the $dyn with the changed Areas, and finally edit the folder.
            editFolder($client, $auth, $asset, $dyn);
            
          } else {
            if (!$cron) { echo '<div class="k">No changes needed (the areas match)</div>'; }
            $o[3] .= $asset['name']."\n";
            $total['k']++;
          }
          break;
        }
      }
    } else {
      if ($cron) {
        $o[1] .= '<div style="padding:3px;color:#fff;background:#c00;">Discipline folder with ID '. $disciplineFolderID .' read failed</div>';
      } else {
        echo '<div class="f">Discipline folder read failed</div>';
      }
      $total['f']++;
    }

  }
}


function readPage($client, $auth, $id, $type) {
  global $asset_type, $asset_children_type, $data, $o, $cron, $disciplineFolderID, $relatedDisciplines;
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
    $referenceDiscipline = [];
    preg_match('/^([a-z][-a-z]+)/', $asset['referencedAssetPath'], $referenceDiscipline);
    if (!in_array($referenceDiscipline, $relatedDisciplines)) {
      array_push($relatedDisciplines, $referenceDiscipline[0]);
    }
    
  } else {
    if ($cron) {
      $o[1] .= 'Failed to read page: '.print_r($id, true)."\n";
    } else {
      echo '<div class="f">Failed to read page: '.print_r($id, true).'</div>';
    }
  }
}


function editFolder($client, $auth, $asset, $dyn) {
  global $total, $asset_type, $asset_children_type, $data, $changed, $o, $cron;
  
  $changed = true; // This only gets called if it's changed!
  
  if ($_POST['after'] == 'on' && !$cron) {
    echo '<button class="btn" href="#aModal'.$asset['id'].'" data-toggle="modal">View After</button><div id="aModal'.$asset['id'].'" class="modal hide" tabindex="-1" role="dialog" aria-hidden="true"><div class="modal-body">';
      print_r($asset); // Shows the page as it will be
    echo '</div></div>';
  }
  
  if ($changed == true) {
    if ($_POST['action'] == 'edit' || $cron) {
      $edit = $client->edit ( array ('authentication' => $auth, 'asset' => array($asset_type => $asset) ) );
    }
    if ($edit->editReturn->success == 'true') {
      if ($cron) {
        $o[2] .= '<div style="color:#090;">Edit success: <a href="https://cms.slc.edu:8443/entity/open.act?id='.$asset['id'].'&type='.$type.'#highlight">'.$asset['path']."</a></div>";
      } else {
        echo '<div class="s">Edit success</div>';
      }
      $total['s']++;
    } else {
      if ($_POST['debug'] == 'on' || $cron) {
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
