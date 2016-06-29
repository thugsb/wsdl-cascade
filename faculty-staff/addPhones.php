<?php
date_default_timezone_set('America/New_York');
$title = 'Test';

// $type_override = 'page';
$start_asset = '2891e3f87f00000101b7715d1ba2a7fb';

// Optionally override the container/child types
// $asset_type = 'assetFactoryContainer';
// $asset_children_type = 'assetFactory';

$edited = array();

include('./phoneNumbers.php');

function pagetest($child) {
  if (preg_match('/^[a-z]/', $child->path->path))
    return true;
}
function foldertest($child) {
    return false;
}
function edittest($asset) {
  if (preg_match('/Faculty/', $asset["contentTypePath"]))
    return true;
}

function changes(&$asset) {
  /* If you wish to use $changed, make sure it's global, and set it to false. 
   * When something is changed, it becomes true: */
  global $changed, $faculty, $edited;
  $changed = false;

  $name = $asset['name'];
  echo $faculty[$name];
  if ( $faculty[$name] ) {
    array_push($edited, $name);
    if ( strlen($faculty[$name]) == 4 ) {
      $number = "(914) 395-".$faculty[$name];
    } elseif ( strlen($faculty[$name]) == 8 ) {
      $number = "(914) ".$faculty[$name];
    } else {
      $number = false;
    }
    if ($number) {
      foreach ($asset["metadata"]->dynamicFields->dynamicField as $dyn) {
        if ($dyn->name == "phone") {
          echo "Was: ".$dyn->fieldValues->fieldValue->value;
          if ($dyn->fieldValues->fieldValue->value != $number) {
            $dyn->fieldValues->fieldValue->value = $number;
            $changed = true;
          }
          echo "Now: ".$dyn->fieldValues->fieldValue->value;
        }
      }
    }
  }
}

if (!$cron) {include('../header.php');}

print_r($edited);
foreach ($faculty as $name => $number) {
  if (!in_array($name, $edited) ) {
    echo "<div>$name</div>";
  }
}

?>