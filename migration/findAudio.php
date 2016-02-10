<?php
date_default_timezone_set('America/New_York');
$title = 'Find Archives Audio';

// $type_override = 'page';
$start_asset = 'a7cc9afbc0a8022b240c49c5c822ff5e';

// Optionally override the container/child types
// $asset_type = 'assetFactoryContainer';
// $asset_children_type = 'assetFactory';

function pagetest($child) {
  if (preg_match('/[a-z]/', $child->path->path))
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
  foreach ($asset["structuredData"]->structuredDataNodes->structuredDataNode as $sdnode) {
    if ($sdnode->identifier == "audio") {
      foreach ($sdnode->structuredDataNodes->structuredDataNode as $subnode) {
        if ($subnode->identifier == 'file') {
          echo "<div>File: $subnode->text</div>";
        }
        if ($subnode->identifier == 'include') {
          echo "<div>Included? $subnode->text</div>";
        }
      }
    }
  }
          
}

if (!$cron) {include('../header.php');}

?>