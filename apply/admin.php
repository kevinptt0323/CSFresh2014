<?php
session_start();
if( isset($_SESSION['login']) && isset($_SESSION['admin']) ) {
}
else {
	echo "<meta http-equiv=\"refresh\" content=\"0;url=http://CSFresh2014.nctucs.net/\" />\n";
	die();
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>管理者模式</title>
	<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/2.0.3/jquery.min.js?ver=2.0.3"></script>
	<link type="text/css" rel="stylesheet" href="style.css" />
	<style type="text/css">
<!--

-->
	</style>
</head>

<body>

<div>

</div>

</body>
</html>

