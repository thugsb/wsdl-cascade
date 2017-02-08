<?php 
if( preg_match('/^https:\/\/cms\.slc\.edu:8443\/render\/page\.act/', $_SERVER['HTTP_REFERER'] ) && $_GET['sNwb7F'] == 'fSn4Ca2' ) {
  $output = array();
  exec('cd /srv/www/repos/elasticsearch-migration/utilities && php index-events.php', $output);
  echo 'Result: (empty indicates no problems)';
  echo '<pre>'; print_r($output); echo '</pre>'; 
  echo 'Finished.';
} else {
  die("Failed");
}
?>
