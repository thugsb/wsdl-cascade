<?php
$title = 'Change the courses scope to Graduate';

// $type_override = 'page';
$start_asset = '7cd6f38f7f0000024411971cf97ea0c3';

// Optionally override the container/child types
// $asset_type = 'assetFactoryContainer';
// $asset_children_type = 'assetFactory';

function pagetest($child) {
  if (preg_match('/[a-z]/', $child->path->path))
    return true;
}
function foldertest($child) {
  if (preg_match('/[a-z]/', $child->path->path))
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
  // if ($asset["metadata"]->teaser != 'test') {$changed = true;}
  foreach ($asset["metadata"]->dynamicFields->dynamicField as $dyn) {
    if ($dyn->name == "scope") {
      if ($dyn->fieldValues->fieldValue->value != 'Graduate') {
        $changed = true;
        $dyn->fieldValues->fieldValue->value = 'Graduate';
      }
    }
  }
}


if (!$cron)
  include('header.php');

?>