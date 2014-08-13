<?php
if(!isset($_SERVER['HTTPS']) || $_SERVER['HTTPS'] == ""){
	$redirect = "https://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
	header("Location: $redirect");
	die();
}
require_once('include/auth.php');
require_once('include/include.php');
require_once('include/xajax_core/xajaxAIO.inc.php');
session_start();

$xajax = new xajax();
$adminCheck = $xajax->registerFunction('adminCheck');
$adminCheck->useSingleQuote();
$adminCheck->addParameter(XAJAX_FORM_VALUES, 'adminForm');
$xajax->processRequest();

if( !isset($_GET[NEWADMINPASSWD]) && !(isset($_SESSION['username']) && isset($_SESSION['admin']) && $_SESSION['admin']) ) {
	die();
}
function adminCheck($form) {
	global $mysqli;
	$success = false;
	$objRes = new xajaxResponse();
	escape($form, ['username', 'password', 'passwordCheck', 'name']);
	if( !check($form, $msg) );
	else if( $mysqli->connect_error )
		$msg = "資料庫錯誤，請稍後再試。";
	else {
		$query = "SELECT * FROM `Admin` WHERE `username` = '$form[username]' LIMIT 1;";
		$insert = "INSERT INTO `Admin` (`username`, `password`, `name`) VALUE ('$form[username]', sha2('" . $form["password"].SALT . "',256), '$form[name]')";
		if( $result = $mysqli->query($query) ) {
			if( $result->num_rows )
				$msg = "已存在的帳號。";
			else {
				if( $result2 = $mysqli->query($insert) ) {
					$msg = "新增使用者成功！<br />";
					$success = true;
				}
				else
					$msg = "資料庫錯誤，請稍後再試。<img src=\"" . ROOT . "OAO.gif\" />";
				$result2->free();
			}
		}
		else
			$msg = "資料庫錯誤，請稍後再試。<img src=\"" . ROOT . "OAO.gif\" />";
		$result->free();
	}
	$objRes->assign('response', 'innerHTML', $msg);
	if( $success ) $objRes->call("adminSucceeded");
	else $objRes->call("adminFailed");
	return $objRes;
}
function check($form, &$msg) {
	$msg = "";
	$checking = ['username', 'password', 'passwordCheck', 'name'];
	foreach($checking as $str)
		if( $form[$str] == "" ) $msg = "error";
	if( $msg != "" ) $msg = "紅框處不可為空白。";
	else if( $form['password']!=$form['passwordCheck'] )
		$msg = "密碼輸入不一致。";
	if( $msg==="" ) return true;
	else {
		$msg = "輸入錯誤！<img src=\"" . ROOT . "OAO.gif\" /><br/ >" . $msg;
		return false;
	}
}
function escape(&$form, $checking) {
	global $mysqli;
	foreach($checking as $str) {
		$form[$str] = htmlspecialchars(@$form[$str]);
		$form[$str] = $mysqli->real_escape_string($form[$str]);
	}
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
	<title>新增管理員</title>
	<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/2.0.3/jquery.min.js?ver=2.0.3"></script>
	<link rel="stylesheet" type="text/css" href="../css/normalize.css" />
	<link rel="stylesheet" type="text/css" href="../css/semantic.css"/>
	<link rel="stylesheet" type="text/css" href="../css/new_admin.css" />
	<script type="text/javascript" src="../js/semantic.js"></script>
	<script type="text/javascript">
/* <![CDATA[ */
function adminSucceeded() {
	$('#response').removeClass("blue").removeClass("error").addClass("positive");
	console.log("add admin succeeded");
	clearForm();
}
function adminFailed() {
	$('#response').removeClass("blue").removeClass("positive").addClass("error");
	console.log("add admin failed");
}
function clearForm() {
	var elem = document.getElementById('adminForm').elements;
	for(i=0; i<elem.length; ++i) {
		if( elem[i].type=="text" || elem[i].type=="password" || elem[i].tagName=="TEXTAREA" ) elem[i].value="";
		if( elem[i].type=="radio" ) elem[i].checked="";
	}
	$('.ui.dropdown').dropdown("restore defaults");
}
$(function() {
	$('#adminForm')
		.form({
			username: { identifier: 'username', rules: [{type: 'empty'},] },
			password: { identifier: 'password', rules: [{type: 'empty'},] },
			passwordCheck: { identifier: 'passwordCheck', rules: [{type: 'empty'},] },
			name: { identifier: 'name', rules: [{type: 'empty'},] }
		});
});
/* ]]> */
	</script>
	<?php $xajax->printJavascript(ROOT.'include'); ?>
</head>

<body>
	<div class="container" id="main">
		<div class="ui large attached message"> 登入 </div>
		<div class="ui blue attached fluid message" id="response"> 密碼將以SHA-256的形式存在資料庫中，請安心使用！ </div>
		<form class="ui form attached fluid segment" name="adminForm" id="adminForm" onsubmit="$('#adminForm').form('validate form');<?php $adminCheck->printScript(); ?>;return false;">
			<div class="field">
				<label>帳號</label>
				<div class="ui left labeled icon input">
					<i class="user icon"></i>
					<input name="username" type="text" placeholder="帳號">
					<div class="ui corner label"> <i class="icon asterisk"></i> </div>
				</div>
			</div>
			<div class="field">
				<label>密碼</label>
				<div class="ui left labeled icon input">
					<i class="lock icon"></i>
					<input name="password" type="password" placeholder="密碼">
					<div class="ui corner label"> <i class="icon asterisk"></i> </div>
				</div>
			</div>
			<div class="field">
				<label>確認密碼</label>
				<div class="ui left labeled icon input">
					<i class="checkmark icon"></i>
					<input name="passwordCheck" type="password" placeholder="請再輸入一次密碼">
					<div class="ui corner label"> <i class="icon asterisk"></i> </div>
				</div>
			</div>
			<div class="field">
				<label>姓名</label>
				<div class="ui left labeled icon input">
					<i class="list icon"></i>
					<input name="name" type="text" placeholder="姓名">
					<div class="ui corner label"> <i class="icon asterisk"></i> </div>
				</div>
			</div>
			<div id="button_div">
				<input type="submit" value="送出" class="ui blue submit button" />
			</div>
		</form>
	</div>

</body>
</html>
