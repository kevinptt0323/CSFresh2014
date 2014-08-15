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
$paymentCheck = $xajax->registerFunction('paymentCheck');
$paymentCheck->useSingleQuote();
$paymentCheck->addParameter(XAJAX_FORM_VALUES, 'paymentForm');
$xajax->processRequest();

if( !isset($_SESSION['name']) ) {
	echo "<meta http-equiv=\"refresh\" content=\"0;url=http://CSFresh2014.nctucs.net/login.php\" />\n";
	die();
}

$name = $_SESSION['name'];
$query = "SELECT * FROM `Payment` WHERE `aid` = '$_SESSION[aid]' LIMIT 1;";
if( $result = $mysqli->query($query) ) {
	if( $result->num_rows ) {
		$row = $result->fetch_array();
		switch( $row['pay_type'] ) {
			case '0': case '1': $pay_type = "匯款繳費";
			case '2': $pay_type = "現場繳費";
		}
		$date = $row['date'];
		$account = $row['account'];
	}
	$pay_type = "匯款繳費";
}

function paymentCheck($form) {
	global $mysqli;
	$success = false;
	$success_admin = false;
	$objRes = new xajaxResponse();
	escape($form, ['account', 'date']);
	if( $mysqli->connect_error )
		$msg = "資料庫錯誤，請稍後再試。";
	else {
		$query = "SELECT * FROM `Payment` WHERE `aid` = '$_SESSION[aid]' LIMIT 1;";
		$insert = "INSERT INTO `Payment` (`aid`, `pay_type`, `account`, `date`) VALUE ('$_SESSION[aid]', 1, '$form[account]', '$form[date]')";
		$update = "UPDATE `Applications` SET `payment` = '1' WHERE `aid` = '$_SESSION[aid]'";
		if( $result = $mysqli->query($query) ) {
			if( $result->num_rows )
				$msg = "你已經填寫過了哦！^.&lt;";
			else {
				if( $mysqli->query($insert) && $mysqli->query($update) ) {
					$msg =  "填寫成功！<a href='login.php?logout'>點此登出</a>";
					$success = true;
				}
				else
					$msg = "資料庫錯誤，請稍後再試。<img src=\"" . ROOT . "OAO.gif\" />";
			}
		}
		else
			$msg = "資料庫錯誤，請稍後再試。<img src=\"" . ROOT . "OAO.gif\" />";
		$result->free();
	}
	$objRes->assign('response', 'innerHTML' , $msg);
	if( $success ) $objRes->call("paymentSucceeded");
	else $objRes->call("paymentFailed");
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
	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
	<title>填寫匯款資訊</title>
	<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/2.0.3/jquery.min.js?ver=2.0.3"></script>
	<link rel="stylesheet" type="text/css" href="../css/normalize.css" />
	<link rel="stylesheet" type="text/css" href="../css/semantic.css"/>
	<link rel="stylesheet" type="text/css" href="../css/payment.css" />
	<script type="text/javascript" src="../js/semantic.js"></script>
	<script type="text/javascript">
/* <![CDATA[ */
function paymentSucceeded() {
	$('#response').removeClass("blue").removeClass("error").addClass("positive").show();
	console.log("add payment succeeded");
	clearForm();
}
function paymentFailed() {
	$('#response').removeClass("blue").removeClass("positive").addClass("error").show();
	console.log("add payment failed");
}
function clearForm() {
	var elem = document.getElementById('paymentForm').elements;
	for(i=0; i<elem.length; ++i) {
		if( elem[i].disabled ) continue;
		if( elem[i].type=="text" || elem[i].type=="password" || elem[i].tagName=="TEXTAREA" ) elem[i].value="";
		if( elem[i].type=="radio" ) elem[i].checked="";
	}
	$('.ui.dropdown').dropdown("restore defaults");
}
$(function() {
	$('#paymentForm')
		.form({
			account: { identifier: 'account', rules: [{type: 'length[5]', prompt: '請輸入5個數字'}] },
			date: { identifier: 'date', rules: [{type: 'empty', prompt: '請輸入日期'}] }
		});
});
/* ]]> */
	</script>
	<?php $xajax->printJavascript(ROOT.'include'); ?>
</head>

<body>
	<div class="container" id="main">
		<div class="ui large attached message"> 填寫匯款資訊 </div>
		<div class="ui blue attached fluid message" id="response"></div>
		<form class="ui form attached fluid segment" name="paymentForm" id="paymentForm" onsubmit="$('#paymentForm').form('validate form');<?php $paymentCheck->printScript(); ?>;return false;">
			<div class="field">
				<label>姓名</label>
				<div class="ui left labeled icon input">
					<i class="user icon"></i>
					<input value="<?php echo $name; ?>" type="text" placeholder="帳號" disabled="true">
				</div>
			</div>
			<div class="field">
				<label>繳費方式</label>
				<div class="ui left labeled icon input">
					<i class="payment icon"></i>
					<input value="<?php echo $pay_type; ?>" type="text" placeholder="帳號" disabled="true">
				</div>
			</div>
			<div class="field">
				<label>匯款(轉帳)帳號末5碼</label>
				<div class="ui left labeled icon input">
					<i class="info letter icon"></i>
<?php
if( isset($account) ) {
	echo "\t\t\t\t\t<input value=\"$account\" disabled=\"true\" type=\"text\" placeholder=\"匯款(轉帳)帳號末5碼\">\n";
}
else {
	echo "\t\t\t\t\t<input name=\"account\" type=\"text\" placeholder=\"匯款(轉帳)帳號末5碼\">\n";
	echo "\t\t\t\t\t<div class=\"ui corner label\"> <i class=\"icon asterisk\"></i> </div>\n";
}
?>
				</div>
			</div>
			<div class="field">
				<label>交易日期</label>
				<div class="ui left labeled icon input">
					<i class="calendar icon"></i>
<?php
if( isset($date) ) {
	echo "\t\t\t\t\t<input value=\"$date\" disabled=\"true\" type=\"text\" placeholder=\"MM/DD，ex:01/01\">\n";
}
else {
	echo "\t\t\t\t\t<input name=\"date\" type=\"text\" placeholder=\"MM/DD，ex:01/01\">\n";
	echo "\t\t\t\t\t<div class=\"ui corner label\"> <i class=\"icon asterisk\"></i> </div>\n";
}
?>
				</div>
			</div>
			<div id="button_div">
				<?php echo isset($account)?"":'<input type="submit" value="送出" class="ui blue submit button" />' . "\n"; ?>
				<a href="login.php?logout" class="ui blue button">登出</a>
			</div>
		</form>
	</div>

</body>
</html>
