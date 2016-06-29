<?php
date_default_timezone_set('America/New_York');
$title = 'Test';

// $type_override = 'page';
$start_asset = '84c767da7f000002172e5dc52f2e4567';

// Optionally override the container/child types
// $asset_type = 'assetFactoryContainer';
// $asset_children_type = 'assetFactory';

function pagetest($child) {
  if (preg_match('/[a-z]/', $child->path->path))
    return true;
}
function foldertest($child) {
  if (preg_match('/^[a-z]/', $child->path->path))
    return true;
}
function edittest($asset) {
  if (preg_match('/Disicipline Course Pages/', $asset["contentTypePath"]))
    return true;
}

function changes(&$asset) {
  /* If you wish to use $changed, make sure it's global, and set it to false. 
   * When something is changed, it becomes true: */
  global $changed;
  $changed = false;
  $levelExists = false;
  foreach ($asset["metadata"]->dynamicFields->dynamicField as $dyn) {
    if ($dyn->name == "level") {
      $levelExists = true;
      if (!isset($dyn->fieldValues->fieldValue) ) {
        $dyn->fieldValues->fieldValue = new StdClass();
        $dyn->fieldValues->fieldValue->value = 'None';
        $changed = true;
      } elseif (!isset($dyn->fieldValues->fieldValue->value) || $dyn->fieldValues->fieldValue->value == '') {
        $dyn->fieldValues->fieldValue->value = 'None';
        $changed = true;
      }
    }
  }
  if ($levelExists == false) {
    $el = new StdClass();
    $el->name = 'level';
    $el->fieldValues = new StdClass();
    $el->fieldValues->fieldValue = new StdClass();
    $el->fieldValues->fieldValue->value = 'None';
    array_push($asset["metadata"]->dynamicFields->dynamicField, $el);
    $changed = true;
  }
}

if (!$cron) {include('../header.php');}

?>