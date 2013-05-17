<?php
date_default_timezone_set('America/New_York');
$title = 'Just read all pages, looking for ones with videos';

// $type_override = 'page';
// $start_asset = 'e59cc45a7f00000100c46dcf503b2144';

// Optionally override the container/child types
// $asset_type = 'assetFactoryContainer';
// $asset_children_type = 'assetFactory';

function pagetest($child) {
    return true;
}
function foldertest($child) {
    return true;
}
function edittest($asset) {
    return true;
}

function changes(&$asset) {
  global $changed;
  $changed = false;
  $text = print_r($asset, true);
  if (preg_match('/edgecast/',$text) ) {
    $name = '';
    if (!$asset['path']) {$name = $asset['name'];}
    echo '<h4><a href="https://cms.slc.edu:8443/entity/open.act?id='.$asset['id'].'&type=page#highlight">'.$asset['path'].$name." (".preg_match('/edgecast/',$text)." matches)</a></h4>";
    
  }
}

if (!$cron) {include('header.php');}

?>