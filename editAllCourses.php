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
$year = '2010-2011';

function pagetest($child) {
  global $year;
  if (preg_match('/^[a-z][-a-z\/]+\/courses\/_archived\/'.$year.'\/primary\/[a-zA-Z]allet/',$child->path->path))
  // if (preg_match('/^[a-z][-a-z\/]+\/courses\/'.$year.'\/primary\/[a-zA-Z]allet/',$child->path->path))
    return true;
}
function foldertest($child) {
  global $year;
  if (preg_match('/^[a-z][-a-z\/]+$/',$child->path->path) || preg_match('/^[a-z][-a-z\/]+\/courses$/',$child->path->path) || preg_match('/^[a-z][-a-z\/]+\/courses\/_archived$/',$child->path->path) || preg_match('/^[a-z][-a-z\/]+\/courses\/_archived\/'.$year.'$/',$child->path->path) || preg_match('/^[a-z][-a-z\/]+\/courses\/_archived\/'.$year.'\/primary$/',$child->path->path))
  // if (preg_match('/^[a-z][-a-z\/]+$/',$child->path->path) || preg_match('/^[a-z][-a-z\/]+\/courses$/',$child->path->path) || preg_match('/^[a-z][-a-z\/]+\/courses\/'.$year.'$/',$child->path->path) || preg_match('/^[a-z][-a-z\/]+\/courses\/'.$year.'\/primary$/',$child->path->path))
    return true;
}
function edittest($asset) {
  if (preg_match('/^slc-catalogue-undergraduate\/Disicipline Course Pages/', $asset["contentTypePath"]))
    return true;
}

function changes(&$asset) {
  global $changed;
  $changed = false;
  if ($asset["metadata"]->title != trim($asset['metadata']->title)) {
    $changed = true;
    $asset['metadata']->title = trim($asset['metadata']->title);
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
    
    
    
    /* 
     ********************* WARNING *********************
     *
     * The following fields are now inline metadata and their content is hidden, 
     * so they're not required for cron and could potentially erase data by overwriting
     * more-recent metadata with the hidden data. Only use it for archived courses.
    **/
    
    // elseif ($field->identifier == 'visibility') {
    //   if ($asset["metadata"]->dynamicFields->dynamicField[10]->fieldValues->fieldValue->value != $field->text) {$changed = true;}
    //   $asset["metadata"]->dynamicFields->dynamicField[10]->fieldValues->fieldValue->value = $field->text;
    // } elseif ($field->identifier == 'semester') {
    //   if ($asset["metadata"]->dynamicFields->dynamicField[11]->fieldValues->fieldValue->value != $field->text) {$changed = true;}
    //   $asset["metadata"]->dynamicFields->dynamicField[11]->fieldValues->fieldValue->value = $field->text;
    // } elseif ($field->identifier == 'course-type') {
    //   if ($asset["metadata"]->dynamicFields->dynamicField[12]->fieldValues->fieldValue->value != $field->text) {$changed = true;}
    //   $asset["metadata"]->dynamicFields->dynamicField[12]->fieldValues->fieldValue->value = $field->text;
    // } elseif ($field->identifier == 'level') {
    //   $levels = explode("::CONTENT-XML-CHECKBOX::",$field->text);
    //   array_shift($levels);
    //   if (count($levels) > 1) {
    //     echo '<div>FYI: there are multiple levels on this course.</div>';
    //   }
    //   $asset["metadata"]->dynamicFields->dynamicField[13]->fieldValues->fieldValue = array();
    //   for ($i = 0;$i <= count($levels)-1; $i++) {
    //     if ($asset["metadata"]->dynamicFields->dynamicField[13]->fieldValues->fieldValue[$i]->value != $levels[$i]) {$changed = true;}
    //     $asset["metadata"]->dynamicFields->dynamicField[13]->fieldValues->fieldValue[$i]->value = $levels[$i];
    //   }
    // } elseif ($field->identifier == 'sorting-group') {
    //   if ($asset["metadata"]->dynamicFields->dynamicField[14]->fieldValues->fieldValue->value != $field->text) {$changed = true;}
    //   $asset["metadata"]->dynamicFields->dynamicField[14]->fieldValues->fieldValue->value = $field->text;
    // } elseif ($field->identifier == 'scope') {
    //   if ($asset["metadata"]->dynamicFields->dynamicField[15]->fieldValues->fieldValue->value != $field->text) {$changed = true;}
    //   $asset["metadata"]->dynamicFields->dynamicField[15]->fieldValues->fieldValue->value = $field->text;
    // } elseif ($field->identifier == 'admin') { //Addendum
    //   if ($asset["metadata"]->dynamicFields->dynamicField[16]->fieldValues->fieldValue->value != $field->structuredDataNodes->structuredDataNode[0]->text) {$changed = true;}
    //   if ($asset["metadata"]->dynamicFields->dynamicField[17]->fieldValues->fieldValue->value != $field->structuredDataNodes->structuredDataNode[1]->text) {$changed = true;}
    //   $asset["metadata"]->dynamicFields->dynamicField[16]->fieldValues->fieldValue->value = $field->structuredDataNodes->structuredDataNode[0]->text;
    //   $asset["metadata"]->dynamicFields->dynamicField[17]->fieldValues->fieldValue->value = $field->structuredDataNodes->structuredDataNode[1]->text;
    // }
    // echo "<div class='f'>Warning: This could be deleting current (meta)data</div>";
  }
}


if (!$cron)
  include('header.php');

?>