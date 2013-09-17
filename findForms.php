<?php
date_default_timezone_set('America/New_York');
$title = 'Just read all pages, looking for ones with Forms';

// $type_override = 'page';
$start_asset = '019ab6c77f00000101f92de57a5e5aaf,019b3b507f000002221c3dfed24ee171,047360737f00000101f92de55e007d02,0499307f7f0000020102c06596359dc2,052704657f00000215fab3c519b8e6a9,0760b47d7f00000101fa0f190ef7de81,0ffd1b5f7f00000101f92de5f1bee48e';
// $start_asset = '1304eb1e7f0000022b80da556a82a730,1697b03b7f00000101f92de5bdae9213,2829e1f07f0000021312656b9b656c5d,289c1ac97f00000101b7715d14978ffc,2bd741d47f0000021312656bde305a48,2f7dcabc7f00000101f92de527bf1fa7';
// $start_asset = '3bb277007f00000209340e798d372f55,3beb3e397f00000209340e79b7d2e67f,51d8cb0d7f00000101f92de5c3401157,5272debc7f00000101f92de5f336e998,548d20737f00000101f92de555b84e14,548fa6e57f00000101f92de5767a87bd';
// $start_asset = '63e913ea7f00000101f92de5a15894ea,640f30657f00000101f92de54b4647e7,6b8db64e7f000002007f6ff80c64293c,6b8f3c757f000002007f6ff8b16b362a,73fa785a7f00000250ffdf132efb9565,75e224457f00000101f92de500562ba4,778ad1ae7f00000101f92de5c4381945';
// $start_asset = '77bd3c077f00000101f92de5ba74a03c,8cba587a7f0000025a3b8730fb8a4620,8e0a7b777f00000278855613817cf6ed,931fa1d97f0000020f1c572aef743886,94a215b67f000001015d84e00c037aaf,9810140b7f00000100279c883b64b2cd';
// $start_asset = 'a551401d7f00000274a0ceef3d1113fc,a5784ced7f00000101f92de5e263be1b,af3e1a647f000001016a5ae9e825dd0f,b70b131e7f00000100279c88b0ebe56f';
// $start_asset = 'b1e7beea7f00000100279c882788d82e,ab880f697f0000021a23b0063cc5fd6f'; /* (news+mag) */
// $start_asset = 'c3d213c17f00000101f92de53291da62,c621c0d17f00000101f92de5212d40b7,c62291407f00000101f92de5b4b37193,caf8346d7f00000244547c9d7fa9f62d,cc912c097f0000020102c065cff41289,cd67add77f00000101f92de53c71ae3e,cd70cce97f00000101f92de5b87356bc';
// $start_asset = 'd89892d67f0000022e208d44695f11af,def503967f00000204ada1dcfca14657,e02d9e887f000002095adf3c628dd7d9,e59cc45a7f00000100c46dcf503b2144,28068f5f7f0000022d407b91de6bed3b,1f3dbc877f0000024a873afa56b38976,18e5a01e7f00000206d20ad8cf6d9894';
// $start_asset = '40b3d26d7f000002017feaf9495c23d9,f95157927f000002672330a666e12472,f7cfec677f0000024ae410ca99ed2e30,f95172ab7f000002672330a65e3a4124,f940a60d7f000002672330a663045257';

// The above does not include these sites: slc-cat-ugrad, slc-cat-grad, slc-faculty, wwww_archived


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
  
  if ($asset["structuredData"]) {
    foreach ($asset["structuredData"]->structuredDataNodes->structuredDataNode as $group) {
      if ($group->identifier == 'php-dynamic') {
        foreach ($group->structuredDataNodes->structuredDataNode as $field) {
          if ($field->identifier == 'config' && $field->text == 'Form') {
            if ($_POST['action'] == 'edit') {
              $myFile = "indexes/forms.html";
              $fh = fopen($myFile, 'a') or die("can't open file");
              $str = '<div><a href="https://cms.slc.edu:8443/entity/open.act?id='.$asset['id'].'&type=page#highlight">'.$asset['siteName'].'://'.$asset['path']."</a></div>\n";
              fwrite($fh, $str);
              fclose($fh);
            }
          }
        }
      }
    }
  }
  
  // Finding edgecast and blip.tv relics
  // $text = print_r($asset, true);
  // if (preg_match('/edgecast/si',$text) or preg_match('/blip/si',$text) ) {
  //   $name = '';
  //   if (!$asset['path']) {$name = $asset['name'];}
  //   echo '<h4><a href="https://cms.slc.edu:8443/entity/open.act?id='.$asset['id'].'&type=page#highlight">'.$asset['path'].$name." (".(preg_match_count('/edgecast/si',$text)+preg_match_count('/blip/si',$text))." matches)</a></h4>";
  //   
  // }
}

if (!$cron) {include('header.php');}

?>