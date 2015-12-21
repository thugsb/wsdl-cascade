<?php
date_default_timezone_set('America/New_York');
$title = 'Copy the old studyXXX metadata tags to the academics+themes+audiences';

// $type_override = 'page';
$start_asset = '52bcf8e07f000002001344a89985168b';

// Optionally override the container/child types
// $asset_type = 'assetFactoryContainer';
// $asset_children_type = 'assetFactory';

$academics = array();
$audiences = array();
$themes = array();

function pagetest($child) {
  if (!preg_match('/index/', $child->path->path) && !preg_match('/redirect/', $child->path->path))
    return true;
}
function foldertest($child) {
  return true;
}
function edittest($asset) {
  if (preg_match('/News -/', $asset["contentTypePath"]))
    return true;
}

function changes(&$asset) {
  global $changed, $academics, $audiences, $themes;
  $academics = array();
  $audiences = array();
  $themes = array();
  $changed = false;
  
  foreach ($asset["metadata"]->dynamicFields->dynamicField as $dyn) {
    if ($dyn->name == "studyAreas") {
      addToArray($dyn, $academics);
    } elseif ($dyn->name == "studyDisciplines") {
      addToArray($dyn, $academics);
    } elseif ($dyn->name == "studyGrad") {
      addToArray($dyn, $academics);
    } elseif ($dyn->name == "studyOther") {
      addToArray($dyn, $academics);
    } elseif ($dyn->name == "constituents") {
      addToArray($dyn, $audiences);
    }
  }
  print_r($academics);
  print_r($audiences);
  print_r($themes);
  foreach ($asset["metadata"]->dynamicFields->dynamicField as $dyn) {
    if ($dyn->name == "academics") {
      $dyn->fieldValues->fieldValue = array();
      foreach ($academics as $entry) {
        $val = new StdClass();
        if ($entry == 'LALS') {
          $val->value = 'Latin American and Latino/a Studies';
          array_push($dyn->fieldValues->fieldValue, $val);
        } elseif ($entry == 'STS') {
          $val->value = 'Science, Technology, and Society';
          array_push($dyn->fieldValues->fieldValue, $val);
        } elseif ($entry == 'CCE') {
          $val->value = 'Continuing Education';
          array_push($dyn->fieldValues->fieldValue, $val);
        } elseif ($entry == 'CDI') {
          $val->value = 'Child Development Institute';
          array_push($dyn->fieldValues->fieldValue, $val);
        } elseif ($entry == 'ECC') {
          $val->value = 'Early Childhood Center';
          array_push($dyn->fieldValues->fieldValue, $val);
        } elseif ($entry == 'Career Services') {
          $val->value = '';
          array_push($themes, 'Careers');
        } else {
          $val->value = $entry;
          array_push($dyn->fieldValues->fieldValue, $val);
        }
      }
    }
    if ($dyn->name == "audiences") {
      $dyn->fieldValues->fieldValue = array();
      foreach ($audiences as $entry) {
        $val = new StdClass();
        if ($entry == 'Alums') {
          $val->value = 'Alumni';
        } else {
          $val->value = $entry;
        }
        array_push($dyn->fieldValues->fieldValue, $val);
      }
    }
    if ($dyn->name == "themes") {
      foreach ($themes as $entry) {
        $val = new StdClass();
        $val->value = $entry;
        array_push($dyn->fieldValues->fieldValue, $val);
      }
    }
  }
  
  if (count($academics) > 0 || count($audiences) > 0 || count($themes) > 0) {
    $changed = true;
  }
}

function addToArray($dyn, &$array) {
  global $academics, $audiences;
  if ( $dyn->fieldValues->fieldValue && gettype($dyn->fieldValues->fieldValue) == 'array') {
    foreach ($dyn->fieldValues->fieldValue as $obj) {
      array_push($array, $obj->value);
    }
  } elseif ( $dyn->fieldValues->fieldValue && gettype($dyn->fieldValues->fieldValue) == 'object') {
    array_push($array, $dyn->fieldValues->fieldValue->value);
  }
}

if (!$cron) {include('../header.php');}

?>