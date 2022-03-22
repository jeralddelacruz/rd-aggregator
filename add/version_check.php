<?php
	// VERSION CHECK
	$php_version = PHP_VERSION;
	$open_ssl_version = OPENSSL_VERSION_TEXT;
	$curl_version = curl_version();

	echo "<pre style=\"margin: auto; padding: 10px; position: fixed; background-color: gainsboro; z-index: 99;\">";
		echo "<b>VERSION CHECK</b>";
		echo "<br />";
		echo "PHP Version: {$php_version}";
		echo "<br />";
		echo "cURL Version: {$curl_version["version"]}";
		echo "<br />";
		echo "Open SSL Version: {$open_ssl_version}";
	echo "</pre>";
?>