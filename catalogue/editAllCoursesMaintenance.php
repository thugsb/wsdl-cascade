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
  global $changed, $total, $discNames, $relatedIDs, $year, $auth, $client, $cron, $o;
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
    } elseif (preg_match('/^related-to-/', $field->identifier) ) {
      if (strstr($field->text, '::CONTENT-XML-CHECKBOX::') ) {
        $related = explode('::CONTENT-XML-CHECKBOX::', $field->text);
        array_shift($related);
        if ($related[0] != '') {
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
                if ($read->readReturn->success === 'true') {
                  $dest = ( array ) $read->readReturn->asset->folder;
                  if ($_POST['children'] == 'on' && !$cron) {
                    echo '<button class="btn" href="#cModal'.$dest['id'].'" data-toggle="modal">View Children</button><div id="cModal'.$dest['id'].'" class="modal hide" tabindex="-1" role="dialog" aria-hidden="true"><div class="modal-body">';
                      print_r($dest["children"]); // Shows all the children of the folder
                    echo '</div></div>';
                  }
                  $matchNew = false;
                  if (count($dest["children"]->child) === 0) {
                    $dest["children"]->child=array($dest["children"]->child);
                  }
                  if (!is_array($dest["children"]->child)) {
                    $child = $dest["children"]->child;
                    $dest["children"]->child = array();
                    $dest["children"]->child[0] = $child;
                  }
                  foreach ($dest["children"]->child as $key=>$existingRef) {
                    if (strcmp(basename($existingRef->path->path), $asset['name']) === 0) {
                      $matchNew = true;
                      if (!$cron) {echo '<div class="k">A reference for '.$asset['name'].' already exists in <strong>'.$dest['path'].'</strong>.</div>';}
                    }
                  }

                  if (!$matchNew) {
                    $matchNew = false;
                    if (!$cron) {echo "<div>A reference for ".$asset['name']." will be created in $discFolder/$year/related/ :</div>";}

                    // Create the new reference
                    if ($_POST['action'] == 'edit') {
                      $create = $client->create(array ('authentication' => $auth, 'asset' => $reference) );
                    }
                    if ($create->createReturn->success === 'true') {
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
                    $o[1] .= '<div style="padding:3px;color:#fff;background:#c00;">Failed to read folder: '.$asset["path"].'</div>';
                  } else {
                    echo '<div class="f">Failed to read folder: '.$discFolder.'</div>';
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
}

include('relatedIDs.php');


if (!$cron) {
  include('../header.php');
}

?>