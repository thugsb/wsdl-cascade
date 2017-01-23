<?php
date_default_timezone_set('America/New_York');
$title = 'Add or Remove metadata tags from Pages';

// $type_override = 'page';
$start_asset = '';

$message = "You probably want to put in specific page IDs, which can be generated <a href='https://www.sarahlawrence.edu/_reports/metadata-tags.html'>here</a> and <a href='https://www.sarahlawrence.edu/_reports/metadata-tagged.html'>here</a>.";

// Optionally override the container/child types
// $asset_type = 'assetFactoryContainer';
// $asset_children_type = 'assetFactory';

function pagetest($child) {
  if (preg_match('/[a-z]/', $child->path->path))
    return true;
}
function foldertest($child) {
    return false;
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
  
  foreach ($asset["metadata"]->dynamicFields->dynamicField as $dyn) {
    if ($dyn->name == "academics") {
      // if(!is_array($dyn->fieldValues->fieldValue)) {
      //   $dyn->fieldValues->fieldValue = array($dyn->fieldValues->fieldValue);
      // }
      // // removeCheckboxItem('Visual Arts', $dyn->fieldValues->fieldValue);
      // addCheckboxItem('Visual and Studio Arts', $dyn->fieldValues->fieldValue);
      // echo '<pre>';
      // print_r($dyn->fieldValues->fieldValue);
      // echo '</pre>';
		}
    if ($dyn->name == "audiences") {
      if(!is_array($dyn->fieldValues->fieldValue)) {
        $dyn->fieldValues->fieldValue = array($dyn->fieldValues->fieldValue);
      }
      // removeCheckboxItem('', $dyn->fieldValues->fieldValue);
      addCheckboxItem('Karen R. Lawrence', $dyn->fieldValues->fieldValue);
    }
    if ($dyn->name == "sponsors") {
      // if(!is_array($dyn->fieldValues->fieldValue)) {
      //   $dyn->fieldValues->fieldValue = array($dyn->fieldValues->fieldValue);
      // }
      //removeCheckboxItem('', $dyn->fieldValues->fieldValue);
      //addCheckboxItem('', $dyn->fieldValues->fieldValue);
    }
    if ($dyn->name == "themes") {
      // if(!is_array($dyn->fieldValues->fieldValue)) {
      //   $dyn->fieldValues->fieldValue = array($dyn->fieldValues->fieldValue);
      // }
      //removeCheckboxItem('', $dyn->fieldValues->fieldValue);
      //addCheckboxItem('', $dyn->fieldValues->fieldValue);
    }
    if ($dyn->name == "level") {
      // if ( $dyn->fieldValues->fieldValue->value !== 'None' ) {
      //   $dyn->fieldValues->fieldValue->value = 'None';
      //   $changed = true;
      // }
    }
	}
}

if (!$cron) {include('../header.php');}

?>