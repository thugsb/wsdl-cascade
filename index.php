<?php
echo $_SERVER['HTTPS'];

$whitelist = array(
    '127.0.0.1', // localhost
    '::1' // IPv6 localhost
);
if(in_array($_SERVER['REMOTE_ADDR'], $whitelist)){
	foreach (new DirectoryIterator(".") as $fn) {
		$name = $fn->getFilename();
    	echo "<div><a href='$name'>$name</a></div>";
	}
}

?>--