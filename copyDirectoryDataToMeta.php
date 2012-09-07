<?php
$title = 'Copy the data from the structured data to metadata on staff assets';

// $type_override = 'page';
$start_asset = 'c953e5707f0000010176bfaa1775ca78';

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
  if (preg_match('/www-directory\/Directory - Staff/', $asset["contentTypePath"]))
    return true;
}

function changes(&$asset) {
  global $changed, $data, $total;
  $changed = false;
  // Grab the correct fields
  foreach ($asset["structuredData"]->structuredDataNodes->structuredDataNode as $sdnode) {
    if ($sdnode->identifier == "first") {
      $staff_first = $sdnode;
    } elseif ($sdnode->identifier == "last") {
      $staff_last = $sdnode;
    } elseif ($sdnode->identifier == "title") {
      $staff_title = $sdnode;
    } elseif ($sdnode->identifier == "email") {
      $staff_email = $sdnode;
    } elseif ($sdnode->identifier == "phone") {
      $phones = count($sdnode->structuredDataNodes->structuredDataNode);
      if ($phones > 6) {
        echo '<div class="f">Too many phone numbers: '.$phones.' fields</div>';
        $total['f']++;
      }
      $area = array();
      $pre = array();
      $ext = array();
      foreach ($sdnode->structuredDataNodes->structuredDataNode as $phonesdnode) {
        if ($phonesdnode->identifier == "area") {
          array_push($area, $phonesdnode->text);
        } elseif ($phonesdnode->identifier == "pre") {
          array_push($pre, $phonesdnode->text);
        } elseif ($phonesdnode->identifier == "ext") {
          array_push($ext, $phonesdnode->text);
        }
      }
      // print_r($ext);
    }
  }
  
  foreach ($asset["metadata"]->dynamicFields->dynamicField as $dyn) {
    if ($dyn->name == "first") {
      if ($dyn->fieldValues->fieldValue->value != $staff_first->text) {$changed = true;}
      $dyn->fieldValues->fieldValue->value = $staff_first->text;
    } elseif ($dyn->name == "last") {
      if ($dyn->fieldValues->fieldValue->value != $staff_last->text) {$changed = true;}
      $dyn->fieldValues->fieldValue->value = $staff_last->text;
    } elseif ($dyn->name == "title") {
      if ($dyn->fieldValues->fieldValue->value != $staff_title->text) {$changed = true;}
      $dyn->fieldValues->fieldValue->value = $staff_title->text;
    } elseif ($dyn->name == "email") {
      if ($dyn->fieldValues->fieldValue->value != $staff_email->text) {$changed = true;}
      $dyn->fieldValues->fieldValue->value = $staff_email->text;
    } elseif ($dyn->name == "area1") {
      if ($dyn->fieldValues->fieldValue->value != $area[0]) {$changed = true;}
      $dyn->fieldValues->fieldValue->value = $area[0];
    } elseif ($dyn->name == "pre1") {
      if ($dyn->fieldValues->fieldValue->value != $pre[0]) {$changed = true;}
      $dyn->fieldValues->fieldValue->value = $pre[0];
    } elseif ($dyn->name == "ext1") {
      if ($dyn->fieldValues->fieldValue->value != $ext[0]) {$changed = true;}
      $dyn->fieldValues->fieldValue->value = $ext[0];
    } elseif ($dyn->name == "area2") {
      if ($dyn->fieldValues->fieldValue->value != $area[1]) {$changed = true;}
      $dyn->fieldValues->fieldValue->value = $area[1];
    } elseif ($dyn->name == "pre2") {
      if ($dyn->fieldValues->fieldValue->value != $pre[1]) {$changed = true;}
      $dyn->fieldValues->fieldValue->value = $pre[1];
    } elseif ($dyn->name == "ext2") {
      if ($dyn->fieldValues->fieldValue->value != $ext[1]) {$changed = true;}
      $dyn->fieldValues->fieldValue->value = $ext[1];
    }
  }
}


if (!$cron)
  include('header.php');

?>