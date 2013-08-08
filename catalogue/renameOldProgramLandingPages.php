<?php
date_default_timezone_set('America/New_York');
$title = 'Add (xxxx-xxxx) to the Title and Display Name of each of the Program/Discipline landing pages';

// $type_override = 'page';
$start_asset = '817373157f00000101f92de5bea1554a';

// Optionally override the container/child types
// $asset_type = 'assetFactoryContainer';
// $asset_children_type = 'assetFactory';

$year = '2012-2013';

function pagetest($child) {
  global $year;
  if (preg_match("/$year/", $child->path->path))
    return true;
}
function foldertest($child) {
  if (preg_match('/^[a-z][-a-z]+$/',$child->path->path) || ($child->path->siteId == '817373097f00000101f92de5898c08e5' && preg_match('/^[a-z][-a-z]+\/[-a-z]+$/',$child->path->path) ) || preg_match('/^humanities\/languages-and-literatures\/[-a-z]+$/',$child->path->path) )
    return true;
}
function edittest($asset) {
  if (preg_match('/Landing Page/', $asset["contentTypePath"]))
    return true;
}

function changes(&$asset) {
  /* If you wish to use $changed, make sure it's global, and set it to false. 
   * When something is changed, it becomes true: */
  global $changed, $year;
  $changed = false;
  if (!preg_match("/$year/", $asset["metadata"]->title) ) {
     $changed = true;
     $asset["metadata"]->title = $asset["metadata"]->title.' ('.$year.')';
  }
  if (!preg_match("/$year/", $asset["metadata"]->displayName) ) {
     $changed = true;
     $asset["metadata"]->displayName = $asset["metadata"]->displayName.' ('.$year.')';
  }
}

if (!$cron) {include('../header.php');}

?>