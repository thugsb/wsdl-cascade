<?php
$title = 'Graduate - Copying DD data to metadata for all course pages in specified years';

// $type_override = 'page';
$start_asset = '4e9e12a97f000001015d84e03ea3fb26';

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
$year = '2014-2015';

function pagetest($child) {
  global $year;
  // if (preg_match('/^[a-z][-a-z\/]+\/courses\/_archived\/'.$year.'\/primary\/[a-zA-Z]/',$child->path->path))
  if (preg_match('/^[a-z][-a-z\/]+\/courses\/'.$year.'\/primary\/[a-zA-Z]/',$child->path->path))
    return true;
}
function foldertest($child) {
  global $year;
  // if (preg_match('/^[a-z][-a-z\/]+$/',$child->path->path) || preg_match('/^[a-z][-a-z\/]+\/courses$/',$child->path->path) || preg_match('/^[a-z][-a-z\/]+\/courses\/_archived$/',$child->path->path) || preg_match('/^[a-z][-a-z\/]+\/courses\/_archived\/'.$year.'$/',$child->path->path) || preg_match('/^[a-z][-a-z\/]+\/courses\/_archived\/'.$year.'\/primary$/',$child->path->path))
  if (preg_match('/^[a-z][-a-z\/]+$/',$child->path->path) || preg_match('/^[a-z][-a-z\/]+\/courses$/',$child->path->path) || preg_match('/^[a-z][-a-z\/]+\/courses\/'.$year.'$/',$child->path->path) || preg_match('/^[a-z][-a-z\/]+\/courses\/'.$year.'\/primary$/',$child->path->path))
    return true;
}
function edittest($asset) {
  if (preg_match('/^www-graduate\/Program Courses/', $asset["contentTypePath"]))
    return true;
}

function changes(&$asset) {
  global $changed;
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
      if ($asset["metadata"]->teaser != $field->text) {$changed = true;}
      $asset["metadata"]->teaser = $field->text;
    } elseif ($field->identifier == 'faculty-set') {
      if(!is_array($field->structuredDataNodes->structuredDataNode)) {
        $field->structuredDataNodes->structuredDataNode = array($field->structuredDataNodes->structuredDataNode);
      }
      $max = count($field->structuredDataNodes->structuredDataNode)-1;
      if ($max > 5) { /* If you want to allow more faculty, make sure to to add the fields to the metadata set and then up these numbers to match */
        $max = 5;
        echo '<div class="f">There are too many faculty connections for the metadata to take.</div>';
      }
      for ($i = 0;$i <= $max; $i++) {
        // echo 'Type: '.gettype($field->structuredDataNodes->structuredDataNode[$i]).'<br>';
        // print_r($field->structuredDataNodes->structuredDataNode[$i]->structuredDataNodes->structuredDataNode);
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
    }
  }
}


if (!$cron)
  include('../header.php');

?>