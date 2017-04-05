<?php
date_default_timezone_set('America/New_York');
$title = 'Adding an Applicable Group to Asset Factories in their AFcontainer';

$type_override = 'assetfactorycontainer';

// Note, also change the foldertest to TRUE for undergrad and FALSE for grad
$start_asset = '859908207f00000101f92de53e7c4d71,8598eabf7f00000101f92de5a7354dea,8562da157f00000101f92de5a835ef7d,859869be7f00000101f92de5fdd29102,8598b47d7f00000101f92de5f0838d0e'; // Undergrad
// $start_asset = '4e9e12dc7f000001015d84e0032be71f'; // Grad

$lastyear = '/2016-2017/';
$nextyearregex = '/2017-2018/';
$nextyear = '2017-2018';

$asset_type = 'assetFactoryContainer';
$asset_children_type = 'assetFactory';

function assetfactorytest($child) {
  return true;
}

function pagetest($child) {
  if (preg_match('/[a-z]/', $child->path->path))
    return true;
}
function foldertest($asset) {
  return true; // Undergrad
  // return false; // Grad
}
function edittest($asset) {
  return true;
}


function changes(&$asset) {
  global $changed, $nextyearregex, $nextyear, $lastyear, $folderNames;
  $changed = false;
  $name = $asset['name'];
  if (isset($folderNames[$name]) ) {
    if (!preg_match($nextyearregex, $asset['placementFolderPath'])) {
      $asset['placementFolderId'] = '';
      $asset['placementFolderPath'] = preg_replace($lastyear,$nextyear,$asset['placementFolderPath']);
      if ($asset['placementFolderPath'] == '/' || $asset['placementFolderPath'] == '') {
        echo 'ye';
        echo $name;
        echo $folderNames[$name];
        $asset['placementFolderPath'] = $folderNames[$name] . '/' . $nextyear;
      }
      $changed = true;
    }
  }
}

$folderNames = array(
'Anthropology' => 'anthropology',
'Art History' => 'art-history',
'Asian Studies' => 'asian-studies',
'Biology' => 'biology',
'Chemistry' => 'chemistry',
'Chinese' => 'chinese',
'Classics' => 'classics',
'Computer Science' => 'computer-science',
'Dance' => 'dance',
'Economics' => 'economics',
'Environmental Studies' => 'environmental-studies',
'Film History' => 'film-history',
'Filmmaking' => 'filmmaking-screenwriting-and-media-arts',
'French' => 'french',
'Geography' => 'geography',
'German' => 'german',
'Greek' => 'greek',
'History' => 'history',
'Italian' => 'italian',
'Japanese' => 'japanese',
'Latin' => 'latin',
'LGBT Studies' => 'lesbian-gay-bisexual-and-transgender-studies',
'Literature' => 'literature',
'Mathematics' => 'mathematics',
'Music' => 'music',
'Philosophy' => 'philosophy',
'Physics' => 'physics',
'Politics' => 'politics',
'Psychology' => 'psychology',
'Public Policy' => 'public-policy',
'Religion' => 'religion',
'Russian' => 'russian',
'Science, Tech and Society' => 'science-technology-and-society',
'Sociology' => 'sociology',
'Spanish' => 'spanish',
'Theatre' => 'theatre',
'Visual Arts' => 'visual-arts',
'Writing' => 'writing',
'Art of Teaching' => 'art-of-teaching',
'Child Development' => 'child-development',
'Dance' => 'dance-mfa',
'Dance Movement Therapy' => 'dance-movement-therapy',
'Health Advocacy' => 'health-advocacy',
'Human Genetics' => 'human-genetics',
'Theatre' => 'theatre-mfa',
'Womens History' => 'womens-history',
'Writing' => 'writing-mfa'
); 

if (!$cron)
  include('../header.php');

?>
