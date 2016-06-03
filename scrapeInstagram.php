<?php

if (PHP_SAPI == 'cli') {
	parse_str(implode('&', array_slice($argv, 1)), $_GET);
	$cron = true;
}
if (!$cron) {echo '<p>This script should only be run from the command line.</p>';}

( isset($_GET['account']) ? $account = $_GET['account'] : $account = 'sarahlawrencecollege');

$curl = curl_init();
curl_setopt_array($curl, array(
	CURLOPT_RETURNTRANSFER => 1,
	CURLOPT_URL => "https://www.instagram.com/".$account.'/'
));
$curlresult = curl_exec($curl);
curl_close($curl);

// echo $curlresult;


$scrape = preg_match('/window\._sharedData = .*environment_switcher_visible_server_guess": true}/', $curlresult, $matches);

// echo $matches[0];

$json = str_replace('window._sharedData = ','', $matches[0]);

$data = json_decode($json);

//print_r($data);

$media = $data->entry_data->ProfilePage[0]->user->media->nodes;


$imageChanged = false;
$copyFail = false;
$writeFail = false;
$message = '';
$output = '<div class="component cpt-instagram"><div class="list-inner"><h2><a target="blank" href="https://www.instagram.com/'.$account.'/" onclick="ga(\'send\', \'event\', \'Component\', \'Instagram Heading\', \'<?php echo $_SERVER[\'REQUEST_URI\']; ?>\')">Instagram <div class="icon i-ext-link" data-grunticon-embed="data-grunticon-embed"></div><small>'.$account.'</small></a></h2><div class="content">';
foreach ($media as $key => $value) {
	if ($key > 3 ) {break;}
	$url = parse_url( $value->thumbnail_src );
	$filename = end( explode( '/', $url['path'] ) );
	if( !file_exists("../_assets/instagram/".$account.'-'.$filename) ) {
		if ( copy($value->thumbnail_src, "../_assets/instagram/".$account.'-'.$filename ) ) {
			$message .= "<p style='color:#090'>Image $key copied successfully.</p>";
			$imageChanged = true;
		} else {
			$message .= "<p style='color:#900'>Image $key copy failed. The .html output file will NOT be modified.</p>";
			$copyFail = true;
		}
	}
	// echo "<a href='https://www.instagram.com/p/$value->code/'><img src='$value->thumbnail_src'></a>";

	$output .= '<div class="list-instagram"><a target="instagram" href="https://www.instagram.com/p/'.$value->code.'/" onclick="ga(\'send\', \'event\', \'Component\', \'Instagram Image\', \'<?php echo $_SERVER[\'REQUEST_URI\']; ?>\')"><img src="/_assets/instagram/'.$account.'-'.$filename.'" alt="'.str_replace('"','',$value->caption).'"/></a></div>';
}
$output .= '</div></div></div>';

if ($imageChanged) {
	if (file_put_contents("../_assets/instagram/instagram-$account.html", $output) ) {
		$message .= '<p style="color:#090">File HTML written successfully.</p>';
	} else {
		$message .=  '<p style="color:#900">File HTML writing failed.</p>';
		$writeFail = true;
	}
}

if ($message == '') {$message = '<p style="color:#009">No changes needed for the <a href="https://www.instagram.com/'.$account.'/">'.$account.'</a> account.</p>';} else {$message .= '<p>The script ran for the <a href="https://www.instagram.com/'.$account.'/">'.$account.'</a> account.</p>';}

if ($cron) {
	$headers = 'MIME-Version: 1.0' . "\r\n";
	$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
	// $headers .= 'Cc: wjoell@sarahlawrence.edu' . "\r\n";
    $headers .= 'From: com@vm-www.slc.edu';
	if ($copyFail || $writeFail) {$subject = 'FAILED: Instagram Image Scraper Cron';} else {$subject = 'Instagram Image Scraper Cron';}
	mail('stu@t.apio.ca', $subject, $message, $headers);
} else {
	echo $message;
	echo $output;
	echo '<pre>';print_r($data);echo '</pre>';
	echo '<script>var data = '.$curlresult.'; console.log(data);</script>';
}

?>