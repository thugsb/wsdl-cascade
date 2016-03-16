<?php

// Edit these five variables, and look below for $output if you wish to edit the HTML output

$access_token = 'YOUR ACCESS KEY'; // You can get an access token here: https://elfsight.com/service/get-instagram-access-token/
$folder_path = '/WEB_ROOT/instagram/'; // Path on your server, ending with /
$web_folder_path = '/instagram/'; // Path to link the images to, ending with /
$email = 'YOUR EMAIL'; // Used to send alerts to let you know the cron has succeeded or not.
$image_count = 4; // Number of images you want.




if (PHP_SAPI == 'cli') {
	parse_str(implode('&', array_slice($argv, 1)), $_GET);
	$cli = true;
}
if (!$cli) {echo '<p>Due to required folder permissions, this script should only be run from the command line using `php instagram.php` or via cron. You can set the user_id variable to get the public images for other users.</p>';}


if ( isset( $_GET['user_id']) ) {
	$user_id = $_GET['user_id'];
} else {
	$user_id = 'self';
}

// Get the account name
$curl = curl_init();
curl_setopt_array($curl, array(
	CURLOPT_RETURNTRANSFER => 1,
	CURLOPT_URL => 'https://api.instagram.com/v1/users/'.$user_id.'/?access_token='.$access_token
));
$curlresult = curl_exec($curl);
curl_close($curl);
$user_info = json_decode($curlresult);
$account = $user_info->data->username;


// Get the latest media info
$curl = curl_init();
curl_setopt_array($curl, array(
	CURLOPT_RETURNTRANSFER => 1,
	CURLOPT_URL => 'https://api.instagram.com/v1/users/'.$user_id.'/media/recent/?count='.$image_count.'&access_token='.$access_token
));
$curlresult = curl_exec($curl);
curl_close($curl);
$data = json_decode($curlresult);


$imageChanged = false;
$copyFail = false;
$writeFail = false;
$message = '';





// Edit these next two lines for the container and title code
$output = '<div class="container">';
$output .= '<h2><a target="blank" href="https://www.instagram.com/'.$account.'/">'.$account.'</a></h2>';

foreach ($data->data as $i => $media) {
	$url = parse_url( $media->images->thumbnail->url);
	$filename = end( explode( '/', $url['path'] ) );
	if( !file_exists($folder_path.$account.'-'.$filename) ) {
		if ( copy($media->images->thumbnail->url, $folder_path.$account.'-'.$filename ) ) {
			$message .= "<p style='color:#090'>Image $i copied successfully.</p>";
			$imageChanged = true;
		} else {
			$message .= "<p style='color:#900'>Image $i copy failed. The .html output file will NOT be modified.</p>";
			$copyFail = true;
		}
	}

	// Edit this line to edit the code for each image
	$output .= '<a target="instagram" href="'.$media->link.'"><img src="'.$web_folder_path.$account.'-'.$filename.'" alt="'.str_replace('"','',$media->caption->text).'"/></a></div>';
}
// Make sure you close your container(s)
$output .= '</div>';






if ($imageChanged) {
	if (file_put_contents($folder_path."instagram-".$account.".html", $output) ) {
		$message .= '<p style="color:#090">File HTML written successfully.</p>';
	} else {
		$message .=  '<p style="color:#900">File HTML writing failed.</p>';
		$writeFail = true;
	}
}

if ($message == '') {$message = '<p style="color:#009">No changes needed for the <a href="https://www.instagram.com/'.$account.'/">'.$account.'</a> account.</p>';} else {$message .= '<p>The script ran for the <a href="https://www.instagram.com/'.$account.'/">'.$account.'</a> account.</p>';}

if ($cli) {
	$headers = 'MIME-Version: 1.0' . "\r\n";
	$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
	if ($copyFail || $writeFail) {$subject = 'FAILED: Instagram Image Grabber';} else {$subject = 'Instagram Image Grabber';}
	mail($email, $subject, $message, $headers);
} else {
	echo $message;
	echo $output;
	echo '<pre>';print_r($data);echo '</pre>';
	echo '<script>var data = '.$curlresult.'</script>';
}

?>