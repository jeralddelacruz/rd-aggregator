<?php
	$dbhost = "localhost";
	$dbuser = "root";
	// $dbpass = "it&IY!pJ3bNE";
	$dbpass = "";
	$dbname = "newscasc_db";
	$dbprefix = "newscasc_";
	$dbkey = md5($dbuser . "/" . $dbname . "/" . $dbpass);

	$SCRIPTURL = "http://rd-aggregator.test/";
	// $SCRIPTURL = "http://rd-aggregator.test:8080/";
	$MAINDOMAIN = "rd-aggregator.test";
	$APPNAME = "rd-aggregator";
	$USERDIR = "user";
	$ADMINDIR = "tertl3651!";
	$SPECIALTOKEN = "tertl3651!";

	$VERSION_NUM = "4.0";
?>