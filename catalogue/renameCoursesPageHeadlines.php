<?php
$title = 'Update the Page Heading of Courses pages for the new year.';

// $type_override = 'page';
$start_asset = '5272debc7f00000101f92de5f336e998';

// Optionally override the container/child types
// $asset_type = 'assetFactoryContainer';
// $asset_children_type = 'assetFactory';

function pagetest($child) {
  if (preg_match('/courses$/', $child->path->path))
    return true;
}
function foldertest($child) {
  if (preg_match('/^[a-z]/', $child->path->path))
    return true;
}
function edittest($asset) {
  if (preg_match('/www-undergraduate\/Level 2 Page - Courses/', $asset["contentTypePath"]) || preg_match('/www-graduate\/Level 2 Page/', $asset["contentTypePath"]))
    return true;
}

function changes(&$asset) {
  /* If you wish to use $changed, make sure it's global, and set it to false. 
   * When something is changed, it becomes true: */
  global $changed;
  $changed = false;
  $old = '/^2011-2012/';
  $new = '2012-2013';
  foreach ($asset["metadata"]->dynamicFields->dynamicField as $dyn) {
    if ($dyn->name == "page-heading") {
      if (preg_match($old, $dyn->fieldValues->fieldValue->value)) {
        $changed = true;
        $change = preg_replace($old, $new, $dyn->fieldValues->fieldValue->value);
        $dyn->fieldValues->fieldValue->value = $change;
      }
    }
  }
}


if (!$cron)
  include('header.php');

?>