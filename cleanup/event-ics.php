<?php
date_default_timezone_set('America/New_York');
$title = 'Test the existence of pages in Cascade that are related to *.ics files found on the server';

$lines = file('../indexes/stu-ics-list.txt', FILE_IGNORE_NEW_LINES);


// $type_override = 'page';
// $start_asset = 'e59cc45a7f00000100c46dcf503b2144';

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
}

if (!$cron) {include('../header.php');}

foreach ($lines as $path) {
  $cascadePath = preg_replace('/\.ics$/', '', substr($path, 14) );
  $reply = $client->read ( array ('authentication' => $auth, 'identifier' => array(type => 'page', path => array( path => $cascadePath, siteName => CASCADE_SITE_PREFIX.'news-events') ) ) );
  // echo '<pre>';
  // print_r($reply);
  // echo '</pre>';
  if ($reply->readReturn->success == 'true') {
    echo '<div class="s">true</div> '.$cascadePath.'<br>';
    $total['s']++;

  } else {
    echo '<div class="f">false</div> '.$cascadePath.'<br>';
    $total['f']++;
  }
}


?>
