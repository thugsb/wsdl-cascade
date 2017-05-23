<?php
$title = 'Edit titles based on H2 in content';

// $type_override = 'page';
$start_asset = 'b35a22097f0000022546c5306f8509e6';

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
  global $changed;
  $changed = false;
  foreach ($asset["structuredData"]->structuredDataNodes->structuredDataNode as $group) {
    if ($group->identifier == 'main_column') {
      foreach ($group->structuredDataNodes->structuredDataNode as $field) {
        if ($field->identifier == 'content') {
          preg_match('%<h2\b[^>]*>.*?</h2>%', $field->text, $h2s);
          // echo $h2s[0];
          if (count($h2s) == 1) {
            if (preg_match('/8217/',$h2s[0])) {echo 'yes';}
            $newTitle = str_replace("&#8217;","'",$h2s[0]);
            $newTitle = str_replace("&rsquo;","'",$newTitle);
            $newTitle = str_replace("&amp; ","and ",$newTitle);
            if ($asset['metadata']->title != strip_tags($newTitle) ) {
              $asset['metadata']->title = strip_tags($newTitle);
              $changed = true;
            }
            $text = str_replace($newTitle,'',$field->text);
            if ($field->text != $text) {
              $field->text = $text;
              $changed = true;
            }
          } elseif (count($h2s) > 1) {
            echo "Multiple H2s";
          }
          
        }
      }
    }
  }
}


if (!$cron)
  include('header.php');

?>