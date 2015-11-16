<?php
date_default_timezone_set('America/New_York');
$title = 'Test';

// $type_override = 'page';
$start_asset = '017fde23c0a8022b17927988549f604b';

// Optionally override the container/child types
// $asset_type = 'assetFactoryContainer';
// $asset_children_type = 'assetFactory';

function pagetest($child) {
    return true;
}
function foldertest($child) {
    return false;
}
function edittest($asset) {
    return true;
}

function changes(&$asset) {
  /* If you wish to use $changed, make sure it's global, and set it to false. 
   * When something is changed, it becomes true: */
  global $changed;
  $changed = true;
  foreach ($asset["metadata"]->dynamicFields->dynamicField as $dyn) {
    if ($dyn->name == "academics") {
      $dyn->fieldValues->fieldValue = array();
    } elseif ($dyn->name == 'themes') {
      $dyn->fieldValues->fieldValue = array();
    } elseif ($dyn->name == 'audiences') {
      $dyn->fieldValues->fieldValue = array();
    } elseif ($dyn->name == 'sponsors') {
      $dyn->fieldValues->fieldValue = array();
    } elseif ($dyn->name == 'faculty-tag') {
      $dyn->fieldValues->fieldValue = array();
    } elseif ($dyn->name == 'studyDisciplines') {
      $dyn->fieldValues->fieldValue = array();
    } elseif ($dyn->name == 'studyAreas') {
      $dyn->fieldValues->fieldValue = array();
    } elseif ($dyn->name == 'studyGrad') {
      $dyn->fieldValues->fieldValue = array();
    } elseif ($dyn->name == 'studyOther') {
      $dyn->fieldValues->fieldValue = array();
    }
  }
}

if (!$cron) {include('header.php');}

?>