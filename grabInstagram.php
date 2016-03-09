<?php

parse_str(implode('&', array_slice($argv, 1)), $_GET);

if (PHP_SAPI == 'cli') {$cron = true;}
if (!$cron) {echo '<p>This script can only be run from the command line.</p>';}

( isset($_GET['account']) ? $account = $_GET['account'] : $account = 'sarahlawrencecollege');
if ( isset($_GET['user_id'])) {
	$user_id = $_GET['user_id'];
	// If user_id is set it will override the $account username
	$curl = curl_init();
	curl_setopt_array($curl, array(
		CURLOPT_RETURNTRANSFER => 1,
		CURLOPT_URL => "https://api.instagram.com/v1/users/$user_id/?access_token=1284963432.467ede5.569288e0262145459ecfd7d70ac50374",
	));
	$curlresult = curl_exec($curl);
	curl_close($curl);
	$user_info = json_decode($curlresult);
	$account = $user_info->data->username;
} elseif ($account == 'slcwritinginst') {
	$user_id = '1543232000';
} elseif ($account == 'sarahlawrencecollegeeccart') {
	$user_id = '2275179817';
} else {
	$user_id = '1284963432';
}

$curl = curl_init();
curl_setopt_array($curl, array(
	CURLOPT_RETURNTRANSFER => 1,
	CURLOPT_URL => "https://api.instagram.com/v1/users/$user_id/media/recent/?count=4&access_token=1284963432.467ede5.569288e0262145459ecfd7d70ac50374",
));
$curlresult = curl_exec($curl);
curl_close($curl);

$data = json_decode($curlresult);
/*echo '<pre>';print_r($data);echo '</pre>';*/

$success = true;
$message = '';
$output = '<div class="component cpt-instagram"><div class="list-inner"><h2><a target="blank" href="https://www.instagram.com/'.$account.'/" onclick="ga(\'send\', \'event\', \'Component\', \'Instagram Heading\', \'<?php echo $_SERVER[\'REQUEST_URI\']; ?>\')">Instagram <div class="icon i-ext-link" data-grunticon-embed="data-grunticon-embed"></div><small>'.$account.'</small></a></h2><div class="content">';
foreach ($data->data as $i => $media) {
	$url = parse_url( $media->images->thumbnail->url);
	$filename = end( explode( '/', $url['path'] ) );
	if( !file_exists("../_assets/instagram/".$account.'-'.$filename) ) {
		if ( copy($media->images->thumbnail->url, "../_assets/instagram/".$account.'-'.$filename ) ) {
			$message .= "<p style='color:#090'>Image $i copied successfully.</p>";
		} else {
			$message .= "<p style='color:#900'>Image $i copy failed.</p>";
			$success = false;
		}
	}
	$output .= '<div class="list-instagram"><a target="instagram" href="'.$media->link.'" onclick="ga(\'send\', \'event\', \'Component\', \'Instagram Image\', \'<?php echo $_SERVER[\'REQUEST_URI\']; ?>\')"><img src="/_assets/instagram/'.$account.'-'.$filename.'" alt="'.str_replace('"','',$media->caption->text).'"/></a></div>';
}
$output .= '</div></div></div>';

if (file_put_contents("../_assets/instagram/instagram-$account.html", $output) ) {
	$message .= '<p style="color:#090">File HTML written successfully.</p>';
} else {
	$message .=  '<p style="color:#900">File HTML writing failed.</p>';
	$success = false;
}


if ($cron) {
	$headers = 'From: stu@t.apio.ca' . "\r\n";
	$headers .= 'MIME-Version: 1.0' . "\r\n";
	$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
	// $headers .= 'Cc: wjoell@sarahlawrence.edu' . "\r\n";
        
	mail('stu@t.apio.ca', 'Cron grabInstagram'.($success ? '' : ' FAILED'), $message, $headers);
} else {
	echo $message;
	echo $output;
}

?>