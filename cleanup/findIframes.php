<?php
date_default_timezone_set('America/New_York');
$title = 'Just read all pages, looking for ones with iframes';

// $type_override = 'page';
// Site IDs updated 2015-02-19
$start_asset = 'cc912c097f0000020102c065cff41289,1304eb1e7f0000022b80da556a82a730,cd67add77f00000101f92de53c71ae3e,c621c0d17f00000101f92de5212d40b7,636641937f00000238a093168e3ef487,636943577f00000238a09316081f537a,75e224457f00000101f92de500562ba4,6369209d7f00000238a09316be6aa2e9,5b2c1f137f00000250abe6dcc7f763b9';
// $start_asset = 'a5784ced7f00000101f92de5e263be1b,85ebc5af7f00000257316e85c1c16610,f95157927f000002672330a666e12472,548fa6e57f00000101f92de5767a87bd,b732376b7f00000251436a90408943ae,9810140b7f00000100279c883b64b2cd,661d4e427f00000226f3bbadcced726a,289c1ac97f00000101b7715d14978ffc,77bd3c077f00000101f92de5ba74a03c';
// $start_asset = '047360737f00000101f92de55e007d02,1f3dbc877f0000024a873afa56b38976,2bd741d47f0000021312656bde305a48,a551401d7f00000274a0ceef3d1113fc,94a215b67f000001015d84e00c037aaf,b70b131e7f00000100279c88b0ebe56f,2f7dcabc7f00000101f92de527bf1fa7,f7cfec677f0000024ae410ca99ed2e30,2829e1f07f0000021312656b9b656c5d';
// $start_asset = '6369593e7f00000238a0931601deacbb,77462bbe7f00000204876d423af89708,cb699b287f0000026c8256cb44f0222d,63e913ea7f00000101f92de5a15894ea,778ad1ae7f00000101f92de5c4381945,8e0a7b777f00000278855613817cf6ed,b168f6d87f0000026aa9f1d00e55b0f7,73fa785a7f00000250ffdf132efb9565,1f03d6187f0000020b4b6df0dd3a7907';
// $start_asset = 'caf8346d7f00000244547c9d7fa9f62d,31c542967f0000026ebd304474604563,f95172ab7f000002672330a65e3a4124,40c6e3f17f00000205282140f46d05af,548d20737f00000101f92de555b84e14,8074b5c37f0000024640e1524f57703a,7dfae5507f000002684383e3cf817dda,9560e98c7f000002204f9c6d11d14a66,6b8f3c757f000002007f6ff8b16b362a';
// $start_asset = '6b8db64e7f000002007f6ff80c64293c,da7f18f97f00000246446e0ceeee5ecb,28068f5f7f0000022d407b91de6bed3b,0760b47d7f00000101fa0f190ef7de81,e02d9e887f000002095adf3c628dd7d9,640f30657f00000101f92de54b4647e7,def503967f00000204ada1dcfca14657,5272debc7f00000101f92de5f336e998,a2382acd7f00000265312942b0e50490';
// $start_asset = 'f940a60d7f000002672330a663045257,c3d213c17f00000101f92de53291da62,931fa1d97f0000020f1c572aef743886,0501a25c7f00000262deaa2ed25b9c83';
// $start_asset = 'dde177857f00000246be153906311094,ddf2c1847f00000246be153972e0fa42,ddf7c98e7f00000246be153952cda667,ddf70d177f00000246be1539932df4b1,ddf0d5a37f00000246be153957629cc1,d8d055c57f00000253b2df1311c74b73,ddf373da7f00000246be15390c07ceaa,ddf218917f00000246be15396b4f1ac2,ddf420907f00000246be15396893c2f3'; // Grad Programs
// $start_asset = '97ff3e0a7f00000237d19018ed4638cb,97c2d5057f00000237d190184865fc02,97fd77137f00000237d1901893293779,97fe605f7f00000237d190185120a46b,97fe79847f00000237d19018abd2fd6d,97fe9a3b7f00000237d190180450ea7d,97fed0477f00000237d19018a47305e2,97fee4f77f00000237d19018e9351f0c,97fef6f77f00000237d19018e091ed18'; // Exchange Sites
// $start_asset = '817373157f00000101f92de5bea1554a';
// $start_asset = '2891e3f87f00000101b7715d1ba2a7fb';
// $start_asset = '4e9e12a97f000001015d84e03ea3fb26';
// $start_asset = 'ab880f697f0000021a23b0063cc5fd6f';
// $start_asset = 'b1e7beea7f00000100279c882788d82e';



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
  if (!preg_match('/^events\/20/', $child->path->path))
    return true;
}
function edittest($asset) {
    return true;
}

function changes(&$asset) {
  global $changed, $total;
  $changed = false;
  
  if (strpos(json_encode($asset), '<iframe' ) ) {
    echo 'iframe found';
    preg_match('/<iframe.+<\/\iframe>/', print_r($asset, true), $iframes);
    $t = print_r($iframes, true);
    echo str_replace("\n",'',htmlspecialchars($t));
    $str = '<div><a href="https://cms.slc.edu:8443/entity/open.act?id='.$asset['id'].'&type=page#highlight">'.$asset['siteName'].'://'.$asset['path'].'</a>: '. str_replace("\n",'',htmlspecialchars($t)) ."</div>\n";
    if ($_POST['action'] == 'edit' && file_put_contents("indexes/iframes.html", $str, FILE_APPEND) !== false) {
      $total['s']++;
      echo '<div class="s">Written to iframes.html</div>';
    } else {
      $total['f']++;
      echo '<div class="f">Failed to write to iframes.html</div>';
    }
  } else {echo 'nope';}
}

if (!$cron) {include('../header.php');}

?>