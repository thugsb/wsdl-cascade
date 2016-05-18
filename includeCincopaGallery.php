<?php
// This script requires the $cincopaID to be set, e.g.
// $cincopaID = 'A0JA9WN5fdRp';

if ( isset($cincopaID) ) {

$curl = curl_init();
curl_setopt ($curl, CURLOPT_URL, 'http://www.cincopa.com/media-platform/runtimeze/json.aspx?details=all&fid='.$cincopaID);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
$result = curl_exec ($curl);
curl_close ($curl);

$json = json_decode( $result );

if ( count($json->items) > 0 ) {
	echo '<div class="gallery">';

	foreach ($json->items as $i => $item) {
		$desc = '';
		if ($item->description != '') {
			$desc = "<figcaption><p>$item->description</p></figcaption>";
		}
		$output = <<<EOT
<div class='gallery-item'>
	<a class='image link-exp link-exp-lbx'>
		<img alt='$item->title' src='$item->thumbnail_url'/>
		<div class='icon-holder'><div class='icon i-exp-link' data-grunticon-embed='data-grunticon-embed'></div></div>
		<div class='cpt-lightbox' id='cincopaID$item->id'>
			<div class='field-image'>
				<img alt='$item->title' src='$item->content_url'>
			</div>
			$desc
		</div>
	</a>
</div>
EOT;
		echo $output;
	}

	echo '</div>'; // End .gallery
}


// echo '<pre>';
// print_r( $json );
// echo '</pre>';

}
