<?php
$title = 'Copying DD data to metadata for all course pages in specified years';

// $type_override = 'page';
$start_asset = '817373157f00000101f92de5bea1554a';

// Optionally override the container/child types
// $asset_type = 'assetFactoryContainer';
// $asset_children_type = 'assetFactory';

/*
 *  The following pagetest and foldertest match either the current years, 
 *  or the archived years.
 *  To change from one to the other, comment and uncomment the appropriate
 *  lines in BOTH pagetest and foldertest. Also, adjust the $year folder param.
 *  If you want to narrow down pages editing, add a course name of the end of 
 *  pagetest e.g. ...primary\/[a-z]allet/'
 */

// $year = '[-0-9]+'; // Matches all years
$year = '2015-2016';

function pagetest($child) {
  global $year;
  // if (preg_match('/^[a-z][-a-z\/]+\/courses\/_archived\/'.$year.'\/primary\/[a-zA-Z]/',$child->path->path))
  if (preg_match('/^[a-z][-a-z\/]+\/'.$year.'\/[a-zA-Z0-9]/',$child->path->path))
    return true;
}
function foldertest($child) {
  global $year;
  // if (preg_match('/^[a-z][-a-z\/]+$/',$child->path->path) || preg_match('/^[a-z][-a-z\/]+\/courses$/',$child->path->path) || preg_match('/^[a-z][-a-z\/]+\/courses\/_archived$/',$child->path->path) || preg_match('/^[a-z][-a-z\/]+\/courses\/_archived\/'.$year.'$/',$child->path->path) || preg_match('/^[a-z][-a-z\/]+\/courses\/_archived\/'.$year.'\/primary$/',$child->path->path))
  if (preg_match('/^[a-z][-a-z\/]+$/',$child->path->path) || preg_match('/^[a-z][-a-z\/]+\/'.$year.'$/',$child->path->path) )
    return true;
}
function edittest($asset) {
  if (preg_match('/^slc-catalogue-undergraduate\/Disicipline Course Pages/', $asset["contentTypePath"]))
    return true;
}

function changes(&$asset) {
  global $changed, $total, $discNames, $relatedIDs, $year, $auth, $client;
  $changed = false;
  $newTitle = trim($asset['metadata']->title);
  $newTitle = preg_replace('/& /','and ',$newTitle);
  $newTitle = preg_replace('/&amp; /','and ',$newTitle);
  $newTitle = preg_replace('/< /','&lt; ',$newTitle);
  if ($asset["metadata"]->title != $newTitle) {
    $changed = true;
    $asset['metadata']->title = $newTitle;
  }
  foreach ($asset["structuredData"]->structuredDataNodes->structuredDataNode as $field) {
    if ($field->identifier == 'description') {
      $field->text = str_replace('&amp;nbsp;',' ',$field->text);
      $field->text = str_replace('&amp;#160;',' ',$field->text);
      if ($asset["metadata"]->teaser != $field->text) {$changed = true;}
      $asset["metadata"]->teaser = $field->text;
    } elseif ($field->identifier == 'faculty-set') {
      if(!is_array($field->structuredDataNodes->structuredDataNode)) {
        $field->structuredDataNodes->structuredDataNode = array($field->structuredDataNodes->structuredDataNode);
      }
      $max = count($field->structuredDataNodes->structuredDataNode)-1;
      if ($max > 5) { /* If you want to allow more faculty, make sure to to add the fields to the metadata set and then up these numbers to match */
        $max = 5;
        $total['f']++;
        echo '<div class="f">There are too many faculty connections for the metadata to take.</div>';
      }
      for ($i = 0;$i <= $max; $i++) {
        // echo 'Type: '.gettype($field->structuredDataNodes->structuredDataNode[$i]).'<br>';
        // print_r($field->structuredDataNodes->structuredDataNode[$i]->structuredDataNodes->structuredDataNode);
        if(!is_array($field->structuredDataNodes->structuredDataNode[$i]->structuredDataNodes->structuredDataNode)) {
          $field->structuredDataNodes->structuredDataNode[$i]->structuredDataNodes->structuredDataNode = array($field->structuredDataNodes->structuredDataNode[$i]->structuredDataNodes->structuredDataNode);
        }
        foreach ($field->structuredDataNodes->structuredDataNode[$i]->structuredDataNodes->structuredDataNode as $subfield) {
          if ($subfield->identifier == 'faculty') {
            if ($asset["metadata"]->dynamicFields->dynamicField[2*$i+18]->fieldValues->fieldValue->value != 'site://'.str_replace(':','/',$subfield->pagePath)) {$changed = true;}
            $asset["metadata"]->dynamicFields->dynamicField[2*$i+18]->fieldValues->fieldValue->value = 'site://'.str_replace(':','/',$subfield->pagePath); //Faculty Path
          } elseif ($subfield->identifier == 'note') {
            if ($asset["metadata"]->dynamicFields->dynamicField[2*$i+19]->fieldValues->fieldValue->value != $subfield->text) {$changed = true;}
            $asset["metadata"]->dynamicFields->dynamicField[2*$i+19]->fieldValues->fieldValue->value = $subfield->text; //Faculty Note
          }
        }
      }
    } elseif ($field->identifier == 'related-to') {
      if (strstr($field->text, '::CONTENT-XML-CHECKBOX::') ) {
        $related = explode('::CONTENT-XML-CHECKBOX::', $field->text);
        array_shift($related);
        foreach($related as $disc) {
          $discFolder = $discNames[$disc];
          if ($discFolder) {
            if ($relatedIDs[$discFolder]) {
              
              $reference = array(
                'reference' => array(
                  'name' => $asset['name'],
                  'parentFolderId' => $relatedIDs[$discFolder],
                  'referencedAssetId' => $asset['id'],
                  'siteName' => 'www.sarahlawrence.edu+catalogue',
                  'referencedAssetType' => 'page'
                )
              );

              $read = $client->read(array ('authentication' => $auth, 'identifier' => array('id'=>$relatedIDs[$discFolder], 'type' => 'folder') ) );
              if ($read->readReturn->success == 'true') {
                $dest = ( array ) $read->readReturn->asset->folder;
                if ($_POST['children'] == 'on' && !$cron) {
                  echo '<button class="btn" href="#cModal'.$dest['id'].'" data-toggle="modal">View Children</button><div id="cModal'.$dest['id'].'" class="modal hide" tabindex="-1" role="dialog" aria-hidden="true"><div class="modal-body">';
                    print_r($dest["children"]); // Shows all the children of the folder
                  echo '</div></div>';
                }
                $matchNew = false;
                foreach ($dest["children"] as $existingRef) {
                  if (basename($existingRef->path->path) == $asset['name']) {
                    $matchNew = true;
                    if (!$cron) {echo '<div>A reference for '.$asset['name'].' already exists in '.$dest['path'].'.</div>';}
                  }
                }
              } else {
                if ($cron) {
                  $o[1] .= '<div style="padding:3px;color:#fff;background:#c00;">Failed to read folder: '.$asset["path"].'</div>';
                } else {
                  echo '<div class="f">Failed to read folder: '.$discFolder.'</div>';
                }
              }

              if (!$matchNew) {
                if (!$cron) {echo "<div>A reference for ".$asset['name']." will be created in $discFolder/$year/related/ :</div>";}
                
                // Create the new reference
                if ($_POST['action'] == 'edit') {
                  $create = $client->create(array ('authentication' => $auth, 'asset' => $reference) );
                }
                if ($create->createReturn->success == 'true') {
                  if ($cron) {
                    $o[0] .= '<div style="color:#090;">A reference was created for <a href="https://cms.slc.edu:8443/entity/open.act?id='.$asset['id'].'&type='.$type.'#highlight">'.$asset['name']."</a> in $discFolder/$year/related/</div>";
                  } else {
                    echo '<div class="s">Creation success: '.$asset['name'].' in '.$discFolder.'</div>';
                  }
                  $total['s']++;
                } else {
                  if ($_POST['action'] == 'edit') {$result = $client->__getLastResponse();} else {$result = '';}
                  if ($cron) {
                    $o[1] .= '<div style="padding:3px;color:#fff;background:#c00;">Creation of a reference failed for <a href="https://cms.slc.edu:8443/entity/open.act?id='.$asset['id'].'&type=page#highlight">'.$asset['path']."</a><div>".htmlspecialchars(extractMessage($result)).'</div></div>';
                  } else {
                    echo '<div class="f">Creation Failed: '.$asset['name'].' in '.$discFolder.'<div>'.htmlspecialchars(extractMessage($result)).'</div></div>';
                  }
                  $total['f']++;
                }
              }
              
            } else {  
              if ($cron) {
                $o[1] .= '<div style="padding:3px;color:#fff;background:#c00;">Discipline related folder ID does not exist: '.$disc.'</div>';
              } else {
                echo '<div class="f">Discipline related folder ID does not exist: '.$disc.'</div>';
              }
            }
          } else {  
            if ($cron) {
              $o[1] .= '<div style="padding:3px;color:#fff;background:#c00;">Related Discipline does not exist: '.$disc.'</div>';
            } else {
              echo '<div class="f">Related Discipline does not exist: '.$disc.'</div>';
            }
          }
        }
      }
    }
  }
}

include('relatedIDs.php');

$discNames = array (
"Africana Studies" => "africana-studies",
"Anthropology" => "anthropology",
"Art History" => "art-history",
"Asian Studies" => "asian-studies",
"Biology" => "biology",
"Chemistry" => "chemistry",
"Chinese" => "chinese",
"Classics" => "classics",
"Cognitive and Brain Science" => "cognitive-and-brain-science",
"Computer Science" => "computer-science",
"Dance" => "dance",
"Architecture and Design Studies" => "design-studies",
"Development Studies" => "development-studies",
"Economics" => "economics",
"Environmental Studies" => "environmental-studies",
"Ethnic and Diasporic Studies" => "ethnic-and-diasporic-studies",
"Film History" => "film-history",
"Filmmaking, Screenwriting and Media Arts" => "filmmaking-screenwriting-media-arts",
"French" => "french",
"Games, Interactivity, and Playable Media" => "games-interactive-media",
"Gender and Sexuality Studies" => "gender-and-sexuality-studies",
"Geography" => "geography",
"German" => "german",
"Global Studies" => "global-studies",
"Greek (Ancient)" => "greek",
"Health, Science, and Society" => "health-science-society",
"History" => "history",
"International Studies" => "international-studies",
"Italian" => "italian",
"Japanese" => "japanese",
"Modern and Classical Languages and Literatures" => "languages-and-literatures",
"Latin" => "latin",
"Latin American and Latino/a Studies" => "latin-american-and-latinoa-studies",
"Lesbian, Gay, Bisexual, and Transgender Studies" => "lesbian-gay-bisexual-and-transgender-studies",
"Literature" => "literature",
"Mathematics" => "mathematics",
"Middle Eastern and Islamic Studies" => "middle-eastern-and-islamic-studies",
"Music" => "music",
"Philosophy" => "philosophy",
"Physics" => "physics",
"Political Economy" => "political-economy",
"Politics" => "politics",
"Pre-Health Program" => "pre-health-program",
"Psychology" => "psychology",
"Public Policy" => "public-policy",
"Religion" => "religion",
"Russian" => "russian",
"Science and Mathematics" => "science-and-mathematics",
"Science, Technology, and Society" => "science-technology-and-society",
"Social Science" => "social-science",
"Sociology" => "sociology",
"Spanish" => "spanish",
"Theatre" => "theatre",
"Urban Studies" => "urban-studies",
"Visual Arts" => "visual-arts",
"Writing" => "writing"
);

if (!$cron) {
  include('../header.php');
}

?>