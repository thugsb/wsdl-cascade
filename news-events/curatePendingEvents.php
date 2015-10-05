<?php
date_default_timezone_set('America/New_York');
$title = 'Curate Events';

include_once('eventFolderIDs.php');

// $type_override = 'page';
$start_asset = $pending_folder;

$message = 'NOTE: This page requires JavaScript. Optionally, use the GET argument "?date=yyyy-mm-dd" to filter by date, where dd and mm are not required.';
$user = $_POST['login'];
$password = $_POST['password'];
$client = $_POST['client'];
$script = <<<EOS

$(function() {
  $('body').append('<iframe name="result" id="result" style="position:fixed; bottom:0; right:0; width:50%; background:#fff; transition:height .35s;"></iframe><div class="btn btn-info" id="iframe-expander" style="position:fixed; bottom:0; right:50%;">Expand Results</div>');
  $('#iframe-expander').click(function() { $('#result').toggleClass('bigger'); });
  
  $('.event-form').append('<div class="tag-section tag-areas"><label><input type="checkbox" name="areas[]" value="Humanities"/>Humanities</label><label><input type="checkbox" name="areas[]" value="History and the Social Sciences"/>History and the Social Sciences</label><label><input type="checkbox" name="areas[]" value="Science and Mathematics"/>Science and Mathematics</label><label><input type="checkbox" name="areas[]" value="Creative and Performing Arts"/>Creative and Performing Arts</label></div>');
  $('.event-form').append('<div class="tag-section tag-disciplines"><label><input type="checkbox" name="disciplines[]" value="Africana Studies"/>Africana Studies</label><label><input type="checkbox" name="disciplines[]" value="Anthropology"/>Anthropology</label><label><input type="checkbox" name="disciplines[]" value="Art History"/>Art History</label><label><input type="checkbox" name="disciplines[]" value="Asian Studies"/>Asian Studies</label><label><input type="checkbox" name="disciplines[]" value="Biology"/>Biology</label><label><input type="checkbox" name="disciplines[]" value="Chemistry"/>Chemistry</label><label><input type="checkbox" name="disciplines[]" value="Computer Science"/>Computer Science</label><label><input type="checkbox" name="disciplines[]" value="Dance"/>Dance</label><label><input type="checkbox" name="disciplines[]" value="Design Studies"/>Design Studies</label><label><input type="checkbox" name="disciplines[]" value="Economics"/>Economics</label><label><input type="checkbox" name="disciplines[]" value="Environmental Studies"/>Environmental Studies</label><label><input type="checkbox" name="disciplines[]" value="Ethnic and Diasporic Studies"/>Ethnic and Diasporic Studies</label><label><input type="checkbox" name="disciplines[]" value="Film History"/>Film History</label><label><input type="checkbox" name="disciplines[]" value="French"/>French</label><label><input type="checkbox" name="disciplines[]" value="Geography"/>Geography</label><label><input type="checkbox" name="disciplines[]" value="German"/>German</label><label><input type="checkbox" name="disciplines[]" value="Greek"/>Greek</label><label><input type="checkbox" name="disciplines[]" value="History"/>History</label><label><input type="checkbox" name="disciplines[]" value="Italian"/>Italian</label><label><input type="checkbox" name="disciplines[]" value="Japanese"/>Japanese</label><label><input type="checkbox" name="disciplines[]" value="Latin"/>Latin</label><label><input type="checkbox" name="disciplines[]" value="Latin American and Latino/a Studies"/>Latin American and Latino/a Studies</label><label><input type="checkbox" name="disciplines[]" value="LGBT"/>LGBT</label><label><input type="checkbox" name="disciplines[]" value="Literature"/>Literature</label><label><input type="checkbox" name="disciplines[]" value="Mathematics"/>Mathematics</label><label><input type="checkbox" name="disciplines[]" value="Modern Languages and Literatures"/>Modern Languages and Literatures</label><label><input type="checkbox" name="disciplines[]" value="Music"/>Music</label><label><input type="checkbox" name="disciplines[]" value="Philosophy"/>Philosophy</label><label><input type="checkbox" name="disciplines[]" value="Physics"/>Physics</label><label><input type="checkbox" name="disciplines[]" value="Politics"/>Politics</label><label><input type="checkbox" name="disciplines[]" value="Pre-med"/>Pre-med</label><label><input type="checkbox" name="disciplines[]" value="Psychology"/>Psychology</label><label><input type="checkbox" name="disciplines[]" value="Public Policy"/>Public Policy</label><label><input type="checkbox" name="disciplines[]" value="Religion"/>Religion</label><label><input type="checkbox" name="disciplines[]" value="Russian"/>Russian</label><label><input type="checkbox" name="disciplines[]" value="Science, Technology, and Society"/>Science, Technology, and Society</label><label><input type="checkbox" name="disciplines[]" value="Sociology"/>Sociology</label><label><input type="checkbox" name="disciplines[]" value="Spanish"/>Spanish</label><label><input type="checkbox" name="disciplines[]" value="Theatre"/>Theatre</label><label><input type="checkbox" name="disciplines[]" value="Visual Arts"/>Visual Arts</label><label><input type="checkbox" name="disciplines[]" value="Digital Imagery"/>Digital Imagery</label><label><input type="checkbox" name="disciplines[]" value="Drawing"/>Drawing</label><label><input type="checkbox" name="disciplines[]" value="Filmmaking"/>Filmmaking</label><label><input type="checkbox" name="disciplines[]" value="Painting"/>Painting</label><label><input type="checkbox" name="disciplines[]" value="Photography"/>Photography</label><label><input type="checkbox" name="disciplines[]" value="Printmaking"/>Printmaking</label><label><input type="checkbox" name="disciplines[]" value="Sculpture"/>Sculpture</label><label><input type="checkbox" name="disciplines[]" value="Women"/>Women</label><label><input type="checkbox" name="disciplines[]" value="Writing"/>Writing</label></div>');
  $('.event-form').append('<div class="tag-section tag-programs"><label><input type="checkbox" name="programs[]" value="Art of Teaching"/>Art of Teaching</label><label><input type="checkbox" name="programs[]" value="Child Development"/>Child Development</label><label><input type="checkbox" name="programs[]" value="MFA Dance"/>MFA Dance</label><label><input type="checkbox" name="programs[]" value="Dance Movement Therapy"/>Dance Movement Therapy</label><label><input type="checkbox" name="programs[]" value="Health Advocacy"/>Health Advocacy</label><label><input type="checkbox" name="programs[]" value="Human Genetics"/>Human Genetics</label><label><input type="checkbox" name="programs[]" value="MFA Theatre"/>MFA Theatre</label><label><input type="checkbox" name="programs[]" value="Womens History"/>Womens History</label><label><input type="checkbox" name="programs[]" value="MFA Writing"/>MFA Writing</label></div>');
  $('.event-form').append('<div class="tag-section tag-studies"><label><input type="checkbox" name="studies[]" value="Continuing Education"/>Continuing Education</label><label><input type="checkbox" name="studies[]" value="Child Development Institute"/>Child Development Institute</label><label><input type="checkbox" name="studies[]" value="Early Childhood Center"/>Early Childhood Center</label><label><input type="checkbox" name="studies[]" value="Special Programs"/>Special Programs</label><label><input type="checkbox" name="studies[]" value="Study Abroad"/>Study Abroad</label></div>');
  $('.event-form').append('<div class="tag-section tag-audiences"><label><input type="checkbox" name="audiences[]" value="Administration"/>Administration</label><label><input type="checkbox" name="audiences[]" value="Alumni"/>Alumni</label><label><input type="checkbox" name="audiences[]" value="Donors"/>Donors</label><label><input type="checkbox" name="audiences[]" value="Faculty"/>Faculty</label><label><input type="checkbox" name="audiences[]" value="Graduate"/>Graduate</label><label><input type="checkbox" name="audiences[]" value="High School"/>High School</label><label><input type="checkbox" name="audiences[]" value="Neighbors"/>Neighbors</label><label><input type="checkbox" name="audiences[]" value="Parents"/>Parents</label><label><input type="checkbox" name="audiences[]" value="President"/>President</label><label><input type="checkbox" name="audiences[]" value="Staff"/>Staff</label><label><input type="checkbox" name="audiences[]" value="Students"/>Students</label><label><input type="checkbox" name="audiences[]" value="Undergraduate"/>Undergraduate</label></div>');
  $('.event-form').append('<div class="tag-section tag-themes"><label><input type="checkbox" name="themes[]" value="Academics"/>Academics</label><label><input type="checkbox" name="themes[]" value="Admission"/>Admission</label><label><input type="checkbox" name="themes[]" value="Advancement"/>Advancement</label><label><input type="checkbox" name="themes[]" value="Alumni"/>Alumni</label><label><input type="checkbox" name="themes[]" value="Athletics"/>Athletics</label><label><input type="checkbox" name="themes[]" value="Careers"/>Careers</label><label><input type="checkbox" name="themes[]" value="Commencement"/>Commencement</label><label><input type="checkbox" name="themes[]" value="NYC and Local"/>NYC and Local</label><label><input type="checkbox" name="themes[]" value="Reunion"/>Reunion</label><label><input type="checkbox" name="themes[]" value="Student Life"/>Student Life</label></div>');
  
  $('.event-form').append('<input name="login" type="hidden" value="$user"/><input name="password" type="hidden" value="$password"/><input name="client" type="hidden" value="$client"/><input name="type" type="hidden" value="page"/><input name="action" type="hidden" value="edit"/>');
  $('.event-form').prepend('<div class="btn-group pull-right"><input type="submit" name="submit" class="btn btn-success" value="Enable and Tag"/><input type="submit" name="submit" class="btn btn-warning" value="Reject"/></div>');
  
  $('.event-form .btn-success').click(function(e) {
    if ( $(this).is('.disabled') ) { e.preventDefault(); return false; }
    $(this).closest('form').attr('action','enable.php');
    $(this).closest('form').prepend('<div class="label label-success pull-right">Enable request sent</div>');
    $(this).closest('form').find('.btn').addClass('disabled');
  });
  $('.event-form .btn-warning').click(function(e) {
    if ( $(this).is('.disabled') ) { e.preventDefault(); return false; }
    $(this).closest('form').attr('action','reject.php');
    $(this).closest('form').prepend('<div class="label label-important pull-right">Reject request sent</div>');
    $(this).closest('form').find('.btn').addClass('disabled');
  });
});

EOS;

// Optionally override the container/child types
// $asset_type = 'assetFactoryContainer';
// $asset_children_type = 'assetFactory';

function pagetest($child) {
  global $acad_year;
  $pattern = '/^events\/'.$acad_year.'\/_pending\/'.$_GET['date'].'/';
  if (preg_match($pattern, $child->path->path))
    return true;
}
function foldertest($child) {
  if (preg_match('/^_[a-z]/', $child->path->path))
    return true;
}
function edittest($asset) {
  if (preg_match('/[a-z]/', $asset["contentTypePath"]))
    return true;
}

function changes(&$asset) {
  /* If you wish to use $changed, make sure it's global, and set it to false. 
   * When something is changed, it becomes true: */
  global $changed;
  $changed = false;
  // if ($asset["metadata"]->teaser != 'test') {
  //    $changed = true;
  //    $asset["metadata"]->teaser = 'test';
  // }
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
      
      foreach ($asset["metadata"]->dynamicFields->dynamicField as $dyn) {
        if ($dyn->name == 'begin') {
          $begin = intval($dyn->fieldValues->fieldValue->value)/1000;
        }
        if ($dyn->name == 'end') {
          $end = intval($dyn->fieldValues->fieldValue->value)/1000;
        }
        if ($dyn->name == 'sponsor') {
          $sponsor = $dyn->fieldValues->fieldValue->value;
        }
        if ($dyn->name == 'location') {
          $location = $dyn->fieldValues->fieldValue->value;
        }
        if ($dyn->name == 'type') {
          $type = $dyn->fieldValues->fieldValue->value;
        }
        if ($dyn->name == 'recurring') {
          $recurring = $dyn->fieldValues->fieldValue->value;
        }
        if ($dyn->name == 'eventsource') {
          $eventsource = $dyn->fieldValues->fieldValue->value;
        }
      }
      foreach ($asset["structuredData"]->structuredDataNodes->structuredDataNode as $sdn) {
        if ($sdn->identifier == 'main_column') {
          foreach ($sdn->structuredDataNodes->structuredDataNode as $main_node) {
            if ($main_node->identifier == 'content') {
              $content = $main_node->text;
            }
          }
        }
      }
      echo '<form class="event-form clearfix" method="POST" target="result">';
        echo '<input type="hidden" name="id" value="'.$asset['id'].'"/>';
        echo '<h4><a target="_blank" href="https://cms.slc.edu:8443/entity/open.act?id='.$asset['id'].'&type=page&">'.$asset['metadata']->title.'</a></h4>';
        echo '<div>'.$asset['path'].$name.'</div>';
        echo '<div class="k">'.gmdate("D M dS, H:i", $begin).'</div> - <div class="k">'.gmdate('D M dS, H:i', $end).'</div>'.($recurring == 'False' ? '' : '<div class="label label-info">Recurring</div>').' <a class="label label-success" target="_blank" href="'.$eventsource.'">Source</a>';
        echo '<div><strong>Location:</strong> '.$location.'</div>';
        echo '<div><strong>Sponsor:</strong> '.$sponsor.'</div>';
        echo '<div><strong>Type:</strong> '.$type.'</div>';
        echo '<div style="max-width:600px; background:#dde;">'.$asset['metadata']->summary.'</div>';
        echo '<div style="max-width:600px;">'.$content.'</div>';
      echo "</form>";
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

?>
