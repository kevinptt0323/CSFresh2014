<?php
require_once('include/auth.php');
require_once('include/include.php');
require_once('include/xajax_core/xajaxAIO.inc.php');
session_start();

$xajax = new xajax();
$loginCheck = $xajax->registerFunction('loginCheck');
$loginCheck->useSingleQuote();
$loginCheck->addParameter(XAJAX_FORM_VALUES, 'loginForm');
$xajax->processRequest();

function loginCheck($form) {
	global $mysqli;
	$success = false;
	$success_admin = false;
	$objRes = new xajaxResponse();
	escape($form, ['name', 'idnum']);
	if( $mysqli->connect_error )
		$msg = "資料庫錯誤，請稍後再試。";
	else {
		$query_admin = "SELECT * FROM `Admin` WHERE `username` = '$form[name]' AND `password` = sha2('" . $form["idnum"].SALT ."',256) LIMIT 1;";
		$query = "SELECT * FROM `Applications` WHERE `name` = '$form[name]' AND `idnum` = '$form[idnum]'LIMIT 1;";
		if( $result = $mysqli->query($query_admin) ) {
			if( $result->num_rows ) {
				$success_admin = true;
				$_SESSION['username'] = $form['name'];
				$_SESSION['admin'] = true;
				$msg = "系統管理員登入成功！";
			}
		}
		else
			$msg = "資料庫錯誤，請稍後再試。<img src=\"" . ROOT . "OAO.gif\" />";
		$result->free();
		if( !$success_admin ) {
			if( $result = $mysqli->query($query) ) {
				if( !$result->num_rows )
					$msg = "登入失敗。";
				else {
					$success = true;
					$_SESSION['aid'] = $form['aid'];
					$_SESSION['name'] = $form['name'];
					$msg = "登入成功！";
				}
			}
			else
				$msg = "資料庫錯誤，請稍後再試。<img src=\"" . ROOT . "OAO.gif\" />";
			$result->free();
		}
	}
	$objRes->assign('response', 'innerHTML', $msg);
	if( $success_admin ) {
		$objRes->call("loginAdminSucceeded");
		//$objRes->redirect("admin.php");
	}
	else if( $success ) {
		$objRes->call("loginSucceeded");
		$objRes->redirect(".");
	}
	else $objRes->call("loginFailed");
	return $objRes;
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
	<title>登入系統</title>
	<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/2.0.3/jquery.min.js?ver=2.0.3"></script>
	<link rel="stylesheet" type="text/css" href="../css/normalize.css" />
	<link rel="stylesheet" type="text/css" href="../css/semantic.css"/>
	<link rel="stylesheet" type="text/css" href="../css/login.css" />
	<script type="text/javascript" src="../js/semantic.js"></script>
	<script type="text/javascript">
/* <![CDATA[ */
function loginAdminSucceeded() {
	$('#response').removeClass("blue").removeClass("error").addClass("positive").show();
	console.log("login succeeded");
}
function loginSucceeded() {
	$('#response').removeClass("blue").removeClass("error").addClass("positive").show();
	console.log("login succeeded");
}
function loginFailed() {
	$('#response').removeClass("blue").removeClass("positive").addClass("error").show();
	console.log("login failed");
}
$(function() {
	$('#loginForm')
		.form({
			name: { identifier: 'name', rules: [{type: 'empty'},] },
			idnum: { identifier: 'idnum', rules: [{type: 'empty'},] }
		});
});
/* ]]> */
	</script>
	<?php $xajax->printJavascript(ROOT.'include'); ?>
</head>

<body>
	<div class="container" id="main">
		<div class="ui large attached message"> 登入 </div>
		<div class="ui blue attached fluid message" id="response"> </div>
		<form class="ui form attached fluid segment" name="loginForm" id="loginForm" onsubmit="$('#loginForm').form('validate form');<?php $loginCheck->printScript(); ?>;return false;">
			<div class="field">
				<label>姓名</label>
				<div class="ui left labeled icon input">
					<i class="user icon"></i>
					<input name="name" type="text" placeholder="姓名">
					<div class="ui corner label"> <i class="icon asterisk"></i> </div>
				</div>
			</div>
			<div class="field">
				<label>身分證字號</label>
				<div class="ui left labeled icon input">
					<i class="lock icon"></i>
					<input name="idnum" type="password" placeholder="身分證字號">
					<div class="ui corner label"> <i class="icon asterisk"></i> </div>
				</div>
			</div>
			<div id="button_div">
				<input type="submit" value="Login" class="ui blue submit button" />
			</div>
		</form>
	</div>

</body>
</html>

