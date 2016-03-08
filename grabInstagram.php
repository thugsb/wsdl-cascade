<?php

if (PHP_SAPI == 'cli') {$cron = true;}

( isset($_GET['account']) ? $account = $_GET['account'] : $account = 'sarahlawrencecollege');
if ( isset($_GET['user_id'])) {
	$user_id = $_GET['user_id'];
	// If user_id is set it will override the $account username
	$curl = curl_init();
	curl_setopt_array($curl, array(
		CURLOPT_RETURNTRANSFER => 1,
		CURLOPT_URL => "https://api.instagram.com/v1/users/$user_id/?access_token=1284963432.467ede5.569288e0262145459ecfd7d70ac50374",
		CURLOPT_USERAGENT => 'Chrome 41.0.2228.0'
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
	CURLOPT_USERAGENT => 'Chrome 41.0.2228.0'
));
$curlresult = curl_exec($curl);
curl_close($curl);

$data = json_decode($curlresult);
/*echo '<pre>';print_r($data);echo '</pre>';*/


$output = '<div class="list-inner"><h2><a target="blank" href="https://instagram.com/'.$account.'/">Instagram <div class="icon i-ext-link" data-grunticon-embed="data-grunticon-embed"></div><small>'.$account.'</small></a></h2><div class="content">';
foreach ($data->data as $i => $media) {
	$output .= '<div class="list-instagram"><a target="instagram" href="'.$media->link.'"><img src="'.$media->images->thumbnail->url.'" alt="'.str_replace('"','',$media->caption->text).'"/></a></div>';
}
$output .= '</div></div>';
if (!$cron) {echo $output;}

if (file_put_contents("../_assets/includes/instagram-$account.html", $output) ) {
	if (!$cron) {echo 'success';}
}

?>