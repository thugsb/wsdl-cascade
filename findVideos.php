<?php
date_default_timezone_set('America/New_York');
$title = 'Just read all pages, looking for ones with videos';

// $type_override = 'page';
// $start_asset = 'e59cc45a7f00000100c46dcf503b2144';

// Optionally override the container/child types
// $asset_type = 'assetFactoryContainer';
// $asset_children_type = 'assetFactory';

function preg_match_count($pattern, $input) {
  if(preg_match_all($pattern, $input, $matches, PREG_PATTERN_ORDER)) {
    return count($matches[0]);
  }
  return 0;
}

function pagetest($child) {
    return true;
}
function foldertest($child) {
  if (!preg_match('/^events\/2012/', $child->path->path))
    return true;
}
function edittest($asset) {
    return true;
}

function changes(&$asset) {
  global $changed;
  $changed = false;
  $text = print_r($asset, true);
  if (preg_match('/edgecast/si',$text) or preg_match('/blip/si',$text) ) {
    $name = '';
    if (!$asset['path']) {$name = $asset['name'];}
    echo '<h4><a href="https://cms.slc.edu:8443/entity/open.act?id='.$asset['id'].'&type=page#highlight">'.$asset['path'].$name." (".(preg_match_count('/edgecast/si',$text)+preg_match_count('/blip/si',$text))." matches)</a></h4>";
    
  }
}

if (!$cron) {include('header.php');}

?>