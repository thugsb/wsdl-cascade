<?php

require_once implode(DIRECTORY_SEPARATOR, array(__DIR__, 'vendor', 'autoload.php'));
include_once(__DIR__.'/rollbar-init.php');
use \Rollbar\Rollbar;
use \Rollbar\Payload\Level;
use JsonSchema\Validator;

require_once(__DIR__.'/_config.php');

if (PHP_SAPI == 'cli') {
	parse_str(implode('&', array_slice($argv, 1)), $_GET);
	$cron = true;
}
if (!$cron) {echo '<p>Due to file permissions, this script can only be run from the command line. A preview of the output is below.</p>';}

( isset($_GET['account']) ? $account = $_GET['account'] : $account = INSTAGRAM_ACCOUNT);
( isset($_GET['site']) ? $site = $_GET['site'] : $site = INSTAGRAM_ACCOUNT);

if ( $site == INSTAGRAM_ACCOUNT ) {
	$serverPath = '/srv/www/htdocs/_assets/instagram/';
	$sitePath = '/_assets/instagram/';
} elseif ($site == 'local' ) {
	$serverPath = '/Users/Resist/Sites/instagram/';
	$sitePath = '../instagram/';
}

$curl = curl_init();
curl_setopt_array($curl, array(
	CURLOPT_RETURNTRANSFER => 1,
	CURLOPT_URL => "https://www.instagram.com/".$account.'/'
));
$curlresult = curl_exec($curl);
curl_close($curl);

$headers = 'From: '. SERVER_EMAIL . "\r\n" . 'Cc: '. CC_EMAIL;

if (!isset($email) ) {
	$email = DEVELOPER_EMAIL;
}

/* To use the local test JSON, comment out from here down to the two $testFile lines (and uncomment those) */

if ($curlresult === false) {
	$subject = 'Instagram Image Scraper cURL returned FALSE';
	$match_fail_message = 'This probably just means it timed out. If this happens repeatedly, it will need investigating. '."\n".'This occurred while running for the '. $account .' account.';
	$output = $subject . "\n" . $match_fail_message;
	$response = Rollbar::log(Level::warning(), $output);
  	if (!$response->wasSuccessful()) {
      mail($email, 'Logging with Rollbar FAILED', $output, $headers);
  	}
	exit;
}

// echo $curlresult;


//$scrape = preg_match('/window\._sharedData = .*environment_switcher_visible_server_guess": true}/', $curlresult, $matches);
$scrape = preg_match('/window\._sharedData = .*};/', $curlresult, $matches); // Make sure the end of the regex is the end of the JSON

// echo $matches[0];

if ( count($matches) < 1 ) {
	$subject = 'Instagram Image Scraper did not match regex';
	$match_fail_message = 'The instagram cURL worked but the scrape did not match the regex. This probably means the cURL was incomplete and is nothing to worry about. If this happens repeatedly, it might mean that Instagram has changed its HTML output and the script needs re-writing. '."\n".'This occurrred while running for the '. $account .' account.';
	$output = $subject . "\n" . $match_fail_message;
	$response = Rollbar::log(Level::warning(), $output);
  	if (!$response->wasSuccessful()) {
      mail($email, 'Logging with Rollbar FAILED', $output, $headers);
  	}
	mail(DEVELOPER_EMAIL, $subject, $match_fail_message, $headers);
	exit;
}

$json = str_replace('window._sharedData = ','', $matches[0]);
$json = preg_replace('/\};$/','}', $json);

$data = json_decode($json);

/*
$testFile = implode(DIRECTORY_SEPARATOR, array(__DIR__, 'instagram-test-data.json'));
$data = json_decode(file_get_contents($testFile));
*/

$validator = new JsonSchema\Validator;
$schemaFile	= implode(DIRECTORY_SEPARATOR, array(__DIR__, 'instagram-schema.json'));
$schema		= json_decode(file_get_contents($schemaFile));

$validator->validate($data, $schema);
if ($validator->isValid()) {
    if (!$cron) { echo "<p style='color:#090'>The supplied JSON validates against the schema.</p>\n"; }
} else {
	$subject = "ERROR: Instagram JSON does not validate.";
	$validationError = 'The JSON scraped from Instagram does not validate against the schema.'."\n".'This occurrred while running for the '. $account .' account.'."\n Violations:\n";
    foreach ($validator->getErrors() as $error) {
        $validationError .= sprintf("[%s] %s\n", $error['property'], $error['message']);
    }
	$output = $subject . "\n" . $validationError;
	$response = Rollbar::log(Level::error(), $output);
  	if (!$response->wasSuccessful()) {
      mail($email, 'Logging with Rollbar FAILED', $output, $headers);
  	}
    if (!$cron) { echo str_replace('\n','<br/>', $subject) . '<br/>' . str_replace('\n','<br/>', $validationError); }
    exit;
}

//print_r($data);

$media = $data->entry_data->ProfilePage[0]->graphql->user->edge_owner_to_timeline_media->edges;

if ( !is_array($media) ) {
	if (!$cron) { echo 'Exiting'; }
	$subject = 'Instagram JSON Structure has CHANGED';
	$match_fail_message = 'The array of images was not found within the JSON, likely meaning the structure of the JSON itself has changed. The Scraper script will now exit and will NOT overwrite the HTML files. The instagram cURL worked and the scrape matched the regex. '."\n".'This occurrred while running for the '. $account .' account.';
	$output = $subject . "\n" . $match_fail_message;
	$response = Rollbar::log(Level::error(), $output);
  	if (!$response->wasSuccessful()) {
      mail($email, 'Logging with Rollbar FAILED', $output, $headers);
  	}
	exit;
}

$imageChanged = false;
$copyFail = false;
$writeFail = false;
$matchUser = '/(?:@)([A-Za-z0-9_](?:(?:[A-Za-z0-9_]|(?:\.(?!\.))){0,28}(?:[A-Za-z0-9_]))?)/';
$matchHash = '/(?:#)([A-Za-z0-9_](?:(?:[A-Za-z0-9_]|(?:\.(?!\.))){0,28}(?:[A-Za-z0-9_]))?)/';
$message = '';
$output = '<div class="component cpt-instagram"><div class="list-inner"><h2><a target="blank" href="https://www.instagram.com/'.$account.'/">Instagram <div class="icon i-ext-link" data-grunticon-embed="data-grunticon-embed"></div><small>'.$account.'</small></a></h2><div class="content">'."\n\n";
foreach ($media as $key => $value) {
	// echo '<pre>';
	// print_r($value);
	// echo '</pre>';
	// Set all the values here:
	$imageCaption = $value->node->edge_media_to_caption->edges[0]->node->text;
	$imageShortcode = $value->node->shortcode;
	$imageThumbnailSrc = $value->node->thumbnail_src;
	$imageDisplaySrc = $value->node->display_url;
	$imageWidth = $value->node->dimensions->width;
	$imageHeight = $value->node->dimensions->height;

	$thumb_url = parse_url( $imageThumbnailSrc );
	$thumb_url_array = explode( '/', $thumb_url['path'] );
	$thumb_filename = end( $thumb_url_array );
	if( !file_exists( $serverPath . "thumb/".$account.'-'.$imageShortcode.'.jpg') ) {
		if ( $cron && !empty($imageThumbnailSrc) && copy($imageThumbnailSrc, $serverPath ."thumb/".$account.'-'.$imageShortcode.'.jpg' ) ) {
			$message .= "Image thumb $key copied successfully.\n";
			$imageChanged = true;
		} else {
			$message .= "Image thumb $key copy FAILED. The .html output file will NOT be modified.\n";
			if (empty($imageThumbnailSrc)) {
				$message .= "The thumbnail image SRC is empty.\n";
			}
			$copyFail = true;
		}
	}
	$large_url = parse_url( $imageDisplaySrc );
	$file_path_array = explode( '/', $large_url['path'] );
	$filename = end( $file_path_array );
	if( !file_exists( $serverPath ."large/".$account.'-'.$imageShortcode.'.jpg') ) {
		if ( $cron && !empty($imageDisplaySrc) && copy($imageDisplaySrc, $serverPath ."large/".$account.'-'.$imageShortcode.'.jpg' ) ) {
			$message .= "Large image $key copied successfully.\n";
			$imageChanged = true;
		} else {
			$message .= "Large image $key copy FAILED. The .html output file will NOT be modified.\n";
			if (empty($imageDisplaySrc)) {
				$message .= "The display (large) image URL is empty.\n";
			}
			$copyFail = true;
		}
	}
	// echo "<a href='https://www.instagram.com/p/$imageShortcode/'><img src='$imageThumbnailSrc'></a>";


	$captionWithUsers  = preg_replace($matchUser, '<a class="instagram-user" href="https://www.instagram.com/$1/">@$1</a>', $imageCaption);
	$captionWithHashes = preg_replace($matchHash, '<a class="instagram-hashtag" href="https://www.instagram.com/explore/tags/$1/">#$1</a>', $captionWithUsers);

	$nf = new NumberFormatter('en_US', NumberFormatter::ORDINAL);

	if ($key < 4 ) {
		$output .= '<div class="list-instagram link-exp-lbx lbx-wide">'."\n";
	} else {
		$output .= '<div class="list-instagram link-exp-lbx lbx-wide lbx-only">'."\n";
	}
		if ($key < 4 ) {
			$output .= '	<a href="https://www.instagram.com/p/'.$imageShortcode.'/" data-code="'.$imageShortcode.'">'."\n"
					.'		<span class="img-wrap">'."\n"
					.'			<img src="'. $sitePath .'thumb/'.$account.'-'.$imageShortcode.'.jpg'.'" alt="'.str_replace('"','',$imageCaption).'"/>'."\n"
					.'			<span class="icon i-exp-img" data-grunticon-embed=""></span>'."\n"
					.'		</span>'."\n"
					.'	</a>'."\n";
		}
		$output .= '	<div class="cpt-lightbox" id="modal-instagram-'.$key.'">'."\n"
				.'		<div class="cpt-instagram"><h2><a href="https://www.instagram.com/'. $account .'/">Instagram<span class="icon i-ext-link" data-grunticon-embed=""></span></a></h2></div>'."\n"
				.'		<div class="inner-left"><div class="field-image ">'."\n"
				.'			<div class="link-wrap"><a target="instagram" href="https://www.instagram.com/p/'.$imageShortcode.'/" data-code="'.$imageShortcode.'">'."\n";
		if ($key < 4 ) {
			$output .= '				<img class="lazy" data-original="'. $sitePath .'large/'.$account.'-'.$imageShortcode.'.jpg'.'" src="'. $sitePath .'thumb/'.$account.'-'.$imageShortcode.'.jpg'.'" width="'.$imageWidth.'" height="'.$imageHeight.'" alt="'.str_replace('"','',$imageCaption).'"/>'."\n";
		} else {
			$output .= '				<img class="lazy" data-original="'. $sitePath .'large/'.$account.'-'.$imageShortcode.'.jpg'.'" src="'. $sitePath .'loading.gif" width="'.$imageWidth.'" height="'.$imageHeight.'" alt="'.str_replace('"','',$imageCaption).'"/>'."\n";
		}
		$output .= '				<span class="icon i-ext-link" data-grunticon-embed=""></span>'."\n"
				.'			</a></div>'."\n"
				.'		</div></div>'."\n"
				.'		<div class="inner-right"><div class="field-body">'."\n"
				.'			<p>'. $captionWithHashes .'</p>'."\n"
				.'			<p><a target="instagram" aria-label="View the '.$nf->format($key+1).' most recently published image in Instagram" href="https://www.instagram.com/p/'.$imageShortcode.'/">Open in Instagram<span class="icon i-ext-link" data-grunticon-embed=""></span></a></p>'."\n"
				.'		</div></div>'."\n"
				.'	</div>'."\n";

	$output .= '</div>'."\n\n";
}
$output .= "\n\n".'</div></div></div>';

$existingFileContents = file_get_contents($serverPath ."instagram-$account.html");

if ($imageChanged || !file_exists($serverPath ."instagram-$account.html") || $output !== $existingFileContents || !$copyFail ) {
	if ($cron && file_put_contents($serverPath ."instagram-$account.html", $output) ) {
		$message .= 'File HTML written successfully.'."\n";
	} else {
		$message .=  'File HTML writing FAILED.'."\n";
		$writeFail = true;
	}
}

if ($message == '') {$message = 'No changes needed for the https://www.instagram.com/'.$account.' account.'."\n";} else {$message .= 'The script ran for the https://www.instagram.com/'.$account.' account.'."\n";}

if ($cron) {
	$headers = 'From: '. SERVER_EMAIL . "\r\n" . 'Cc: '. CC_EMAIL;
	$subject = 'Instagram Image Scraper failed';
	$rollbarOutput = $subject . "\n" . $message . "\n Full scraped JSON: \n". $json;
	if ($copyFail || $writeFail) {
		$response = Rollbar::log(Level::warning(), $rollbarOutput);
	  	if (!$response->wasSuccessful()) {
	      mail($email, 'Logging with Rollbar FAILED', $rollbarOutput, $headers);
	  	}
	}
} else {
	echo '<pre>'.$message.'</pre>';
	echo $output;
	echo '<pre>';print_r($data);echo '</pre>';
	echo '
	<script src="//ajax.googleapis.com/ajax/libs/jquery/2.1.4/jquery.min.js"></script>
	<script>
	$("img").each(function() {
		if ( $(this).has("data-original") ) {
			$(this).attr("src", $(this).data("original"))
		}
	});
	</script>';
	// echo '<script>var data = "'. htmlspecialchars($curlresult) .'"; console.log(data);</script>';
}

?>
