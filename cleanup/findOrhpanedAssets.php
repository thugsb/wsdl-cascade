<?php
date_default_timezone_set('America/New_York');
$title = 'Find files without relationships, and write them to orphanedPDFs.txt';

// The fields output are: id,name,cachepath,parentFolderID,isRecycled,isCurrentVersion,byteLength,versionDate,shouldBePublished,lastDatePublished,shouldBeIndexed

$type_override = 'file';
$start_asset = 'None';

// Optionally override the container/child types
$asset_type = 'folder';
$asset_children_type = 'file';

$message = "<a href='../indexes/orphanedPDFs.html'>View output</a>";

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

if (($handle = fopen("../indexes/pdf-report-2017-06-29.csv", "r")) !== FALSE) {
  $rows = array_map('str_getcsv', file("../indexes/pdf-report-2017-06-29.csv"));
  $header = array_shift($rows);
  $csv = array();
  foreach ($rows as $row) {
    $csv[] = array_combine($header, $row);
  }
}

$file = '../indexes/orphanedPDFs.html';
file_put_contents($file, '<table style="border:1px solid black;"><thead><tr><th>Path</th><th>shouldBePublished</th><th>shouldBeIndexed</th><th>lastDatePublished</th></tr></thead><tbody>');

// ,,

if (!$cron) {include(__DIR__.'/../html_header.php');}

foreach ($csv as $key => $value) {
  // if ($key >= 2380 && $key < 3390) {
    // echo '<pre>'; print_r($value); echo '</pre>';
    if ( !$value['isRecycled'] && $value['isCurrentVersion'] == 1 && $value['draftOriginalId'] == 'NULL' && ( $value['siteId'] == 'd9c23844c0a8022b3ff2b692a1ecfcf5' || $value['siteId'] == 'b1e7bedc7f00000100279c88a80da991' ) ) {
      readPage($client, $auth, array('id' => $value['id'], 'type' => 'file'), $value );
    }
  // }
}

file_put_contents($file, '</tbody></table>', FILE_APPEND);


function readPage($client, $auth, $id, $asset) {
  global $asset_type, $asset_children_type, $data, $o, $cron, $file;
  $reply = $client->listSubscribers ( array ('authentication' => $auth, 'identifier' => $id ) );
  if ($reply->listSubscribersReturn->success == 'true') {

    echo '<h4><a href="https://cms.slc.edu:8443/entity/relationships.act?type=file&id=' .$asset['id']. '">' . $asset['name'] . '</a></h4>';
    $subscribers = $reply->listSubscribersReturn->subscribers;
    if ( is_array($subscribers->assetIdentifier) ) {
      echo 'Array';
      // echo '<pre>'; print_r($subscribers); echo '</pre>';
    } elseif ( isset($subscribers->assetIdentifier) ) {
      echo 'One Exists';
      // echo '<pre>'; print_r($subscribers); echo '</pre>';
    } else {
      echo 'No relations';
      $link = '<tr><td><a href="https://cms.slc.edu:8443/entity/open.act?type=file&id=' .$asset['id']. '">' . $asset['cachepath'] . '</a></td><td>' . $asset['shouldBePublished'] . '</td><td>' . $asset['shouldBeIndexed'] . '</td><td>' . date(DATE_ISO8601, intval($asset['lastDatePublished'])/1000 ) . '</td></tr>' . "\n";
      try {
        file_put_contents($file, $link, FILE_APPEND);
      } catch (Exception $e) {
        echo 'Caught exception: ',  $e->getMessage(), "\n";
      }
    }
    
  } else {
    if ($cron) {
      $o[1] .= '<div style="padding:3px;color:#fff;background:#c00;">Failed to read page: '.$id.'</div>';
    } else {
      echo '<div class="f">Failed to read page: '.$id.'</div>';
    }
  }
}


?>
