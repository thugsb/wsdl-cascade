<?php
date_default_timezone_set('America/New_York');
$title = 'Update the Data field as desired.';

// $type_override = 'page';
$start_asset = '90436311c0a8022b6953d233da12e9b3';

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
  
  $dataType = "tpl-faculty-index-dynamic";
  
	foreach ($asset["structuredData"]->structuredDataNodes->structuredDataNode as $sdnode) {
		if ($sdnode->identifier == "data") {
			if($sdnode->text != $dataType) {
				$sdnode->text = $dataType;
				$changed = true;
			}
		}
	}
}

if (!$cron) {include('../header.php');}

?>