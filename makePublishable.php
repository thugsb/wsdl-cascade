<?php
$title = 'Make publishable';

// $type_override = 'page';
$start_asset = 'af0997097f0000020fc8e9132bc9fdf3';

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
  if (preg_match('/Level 2 Magazine Page/', $asset["contentTypePath"]))
    return true;
}

function changes(&$asset) {
  /* If you wish to use $changed, make sure it's global, and set it to false. 
   * When something is changed, it becomes true: */
  global $changed;
  $changed = false;
  
  $t = str_replace('&rsquo;',"'",$asset['metadata']->title);
  if ($asset['metadata']->title != $t) {
    $asset['metadata']->title = $t;
    $changed = true;
  }
  
  foreach ($asset["structuredData"]->structuredDataNodes->structuredDataNode as $group) {
    if ($group->identifier == 'main_column') {
      foreach ($group->structuredDataNodes->structuredDataNode as $field) {
        if ($field->identifier == 'content') {
          $text = (string) $field->text;
          // $text = preg_replace('/src=([\'"])[a-zA-Z\.\/]*media/','src=$1/outcomes/media',$text);
          // $text = preg_replace('/src=([\'"])\/generations/','src=$1/outcomes',$text);
          // $text = preg_replace('/onmouse[a-z]+="[a-zA-Z0-9_\(\)\',\/\-\.]+"/','',$text);
          // $text = preg_replace('/href="([a-zA-Z0-9_\.\-]+).php.html"/','href="/outcomes/$1"',$text);
          // $text = preg_replace('/src="images/','src="/philanthropy/media/images',$text);
          if ($text != $field->text) {
            $field->text = $text;
            $changed = true;
          }
        }
      }
    }
    if ($group->identifier == 'right_sidebar') {
      foreach ($group->structuredDataNodes->structuredDataNode as $field) {
        $empty = false;
        if ($field->identifier == 'content') {
          // echo '<div>'.trim($field->text," \n\r").'</div>';
          if (trim($field->text," \n\r") == '') {
            $empty = true;
          }
          $text = (string) $field->text;
          // $text = preg_replace('/src=([\'"])[a-zA-Z\.\/]*media/','src=$1/outcomes/media',$text);
          // $text = preg_replace('/src=([\'"])\/generations/','src=$1/outcomes',$text);
          $text = preg_replace('/onMouseO[a-z]+="[a-zA-Z0-9_\(\)\',\/\-\.]+"/','',$text);
          // $text = preg_replace('/href="([a-zA-Z0-9_\.\-]+).php.html"/','href="/outcomes/featured/$1"',$text);
          $text = preg_replace('/href="\/philanthropy"\/feature\/([a-z-]+)"/','href="/philanthropy/featured/$1"',$text);
          // $text = preg_replace('/href="\/outcomes\/featured\/feature_one/','href="/outcomes/featured/index',$text);
          // $text = preg_replace('/href="\/outcomes\/featured\/feature_/','href="/outcomes/featured/',$text);
          // $text = preg_replace('/href="feature_([a-z]+).php.html/','href="/outcomes/featured/$1',$text);
          $text = preg_replace('/src="images/','src="/philanthropy/media/images',$text);
          if ($text != $field->text) {
            $field->text = $text;
            $changed = true;
          }
        }
      }
      foreach ($group->structuredDataNodes->structuredDataNode as $field) {
        if ($field->identifier == 'include') {
          if ($empty == true) {
            $field->text = 'No';
            $changed = true;
          }
        }
      }
    }
  }
}


if (!$cron)
  include('header.php');

?>