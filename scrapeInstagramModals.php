<?php

if (PHP_SAPI == 'cli') {
	parse_str(implode('&', array_slice($argv, 1)), $_GET);
	$cron = true;
}
if (!$cron) {echo '<p>Due to file permissions, this script can only be run from the command line. A preview of the output is below.</p>';}

( isset($_GET['account']) ? $account = $_GET['account'] : $account = 'sarahlawrencecollege');

$curl = curl_init();
curl_setopt_array($curl, array(
	CURLOPT_RETURNTRANSFER => 1,
	CURLOPT_URL => "https://www.instagram.com/".$account.'/'
));
$curlresult = curl_exec($curl);
curl_close($curl);


$headers = 'MIME-Version: 1.0' . "\r\n";
$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
$headers .= 'Cc: wjoell@sarahlawrence.edu' . "\r\n";
$headers .= 'From: com@vm-www.slc.edu';


if ($curlresult === false) {
	$subject = 'Warning: Instagram Image Scraper Cron';
	$match_fail_message = 'The instagram cURL returned FALSE. This probably just means it timed out. If this happens repeatedly, it will need investigating.';
	mail('stu@t.apio.ca', $subject, $match_fail_message, $headers);
	exit;
}

// echo $curlresult;


//$scrape = preg_match('/window\._sharedData = .*environment_switcher_visible_server_guess": true}/', $curlresult, $matches);
$scrape = preg_match('/window\._sharedData = .*\}/', $curlresult, $matches); // loosen regex; Jeff Fowler 2017-01-20

// echo $matches[0];

if ( count($matches) < 1 ) {
	$subject = 'Failed script warning: Instagram Image Scraper Cron';
	$match_fail_message = 'The instagram cURL worked but the scrape did not match the regex. This probably means the cURL was incomplete and is nothing to worry about. If this happens repeatedly, it might mean that Instagram has changed its HTML output and the script needs re-writing.';
	mail('stu@t.apio.ca', $subject, $match_fail_message, $headers);
	exit;
}

$json = str_replace('window._sharedData = ','', $matches[0]);

$data = json_decode($json);

//print_r($data);

$media = $data->entry_data->ProfilePage[0]->user->media->nodes;


$imageChanged = false;
$copyFail = false;
$writeFail = false;
$matchUser = '/(?:@)([A-Za-z0-9_](?:(?:[A-Za-z0-9_]|(?:\.(?!\.))){0,28}(?:[A-Za-z0-9_]))?)/';
$matchHash = '/(?:#)([A-Za-z0-9_](?:(?:[A-Za-z0-9_]|(?:\.(?!\.))){0,28}(?:[A-Za-z0-9_]))?)/';
$message = '';
$output = '<div class="component cpt-instagram"><div class="list-inner"><h2><a target="blank" href="https://www.instagram.com/'.$account.'/">Instagram <div class="icon i-ext-link" data-grunticon-embed="data-grunticon-embed"></div><small>'.$account.'</small></a></h2><div class="content">'."\n\n";
foreach ($media as $key => $value) {
	$thumb_url = parse_url( $value->thumbnail_src );
	$thumb_filename = end( explode( '/', $thumb_url['path'] ) );
	if( !file_exists("../_assets/instagram/thumb/".$account.'-'.$value->code.'.jpg') ) {
		if ( $cron && copy($value->thumbnail_src, "../_assets/instagram/thumb/".$account.'-'.$value->code.'.jpg' ) ) {
			$message .= "<p style='color:#090'>Image thumb $key copied successfully.</p>";
			$imageChanged = true;
		} else {
			$message .= "<p style='color:#900'>Image thumb $key copy failed. The .html output file will NOT be modified.</p>";
			$copyFail = true;
		}
	}
	$large_url = parse_url( $value->display_src );
	$filename = end( explode( '/', $large_url['path'] ) );
	if( !file_exists("../_assets/instagram/large/".$account.'-'.$value->code.'.jpg') ) {
		if ( $cron && copy($value->display_src, "../_assets/instagram/large/".$account.'-'.$value->code.'.jpg' ) ) {
			$message .= "<p style='color:#090'>Large image $key copied successfully.</p>";
			$imageChanged = true;
		} else {
			$message .= "<p style='color:#900'>Large image $key copy failed. The .html output file will NOT be modified.</p>";
			$copyFail = true;
		}
	}
	// echo "<a href='https://www.instagram.com/p/$value->code/'><img src='$value->thumbnail_src'></a>";


	$captionWithUsers  = preg_replace($matchUser, '<a class="instagram-user" href="https://www.instagram.com/$1/">@$1</a>', $value->caption);
	$captionWithHashes = preg_replace($matchHash, '<a class="instagram-hashtag" href="https://www.instagram.com/explore/tags/$1/">#$1</a>', $captionWithUsers);

	if ($key < 4 ) {
		$output .= '<div class="list-instagram link-exp-lbx lbx-wide">'."\n";
	} else {
		$output .= '<div class="list-instagram link-exp-lbx lbx-wide lbx-only">'."\n";
	}
		if ($key < 4 ) {
			$output .= '	<a href="https://www.instagram.com/p/'.$value->code.'/" data-code="'.$value->code.'">'."\n"
					.'		<span class="img-wrap">'."\n"
					.'			<img src="/_assets/instagram/thumb/'.$account.'-'.$value->code.'.jpg'.'" alt="'.str_replace('"','',$value->caption).'"/>'."\n"
					.'			<span class="icon i-exp-img" data-grunticon-embed=""></span>'."\n"
					.'		</span>'."\n"
					.'	</a>'."\n";
		}
		$output .= '	<div class="cpt-lightbox" id="modal-instagram-'.$key.'">'."\n"
				.'		<div class="cpt-instagram"><h2><a href="https://www.instagram.com/'. $account .'/">Instagram<span class="icon i-ext-link" data-grunticon-embed=""></span></a></h2></div>'."\n"
				.'		<div class="inner-left"><div class="field-image ">'."\n"
				.'			<div class="link-wrap"><a target="instagram" href="https://www.instagram.com/p/'.$value->code.'/" data-code="'.$value->code.'">'."\n"
				.'				<img src="/_assets/instagram/large/'.$account.'-'.$value->code.'.jpg'.'" width="'.$value->dimensions->width.'" height="'.$value->dimensions->height.'" alt="'.str_replace('"','',$value->caption).'"/>'."\n"
				.'				<span class="icon i-ext-link" data-grunticon-embed=""></span>'."\n"
				.'			</a></div>'."\n"
				.'		</div></div>'."\n"
				.'		<div class="inner-right"><section class="field-body">'."\n"
				.'			<p>'. $captionWithHashes .'</p>'."\n"
				.'			<p><a target="instagram" href="https://www.instagram.com/p/'.$value->code.'/">Open in Instagram<span class="icon i-ext-link" data-grunticon-embed=""></span></a></p>'."\n"
				.'		</section></div>'."\n"
				.'	</div>'."\n";

	$output .= '</div>'."\n\n";
}
$output .= "\n\n".'</div></div></div>';

$existingFileContents = file_get_contents("../_assets/instagram/instagram-$account.html");

if ($imageChanged || !file_exists("../_assets/instagram/instagram-$account.html") || $output !== $existingFileContents ) {
	if ($cron && file_put_contents("../_assets/instagram/instagram-$account.html", $output) ) {
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
	// echo '<script>var data = "'. htmlspecialchars($curlresult) .'"; console.log(data);</script>';
}

?>
