<?php
require_once('include/auth.php');
require_once('include/include.php');
require_once('include/xajax_core/xajaxAIO.inc.php');

$xajax = new xajax();
$regCheck = $xajax->registerFunction('regCheck');
$regCheck->useSingleQuote();
$regCheck->addParameter(XAJAX_FORM_VALUES, 'registerForm');
$xajax->processRequest();

if( isset($_POST['username']) ) $username = $_POST['username'];
else $username = "";

function regCheck($form) {
	$success = false;
	$objRes = new xajaxResponse();
	$msg = "";
	if( !check($form, $msg) );
	else if( dbconn() )
		$msg = "資料庫錯誤，請稍後再試。";
	else {
		global $mysqli;
		$query = "SELECT * FROM `Account` WHERE `username` = '$form[username]' LIMIT 1;";
		$insert = "INSERT INTO `Account` (`username`, `passwd`, `email`, `name`, `department`, `graduate_year`) VALUES ('$form[username]', sha2('$form[passwd]',256), '$form[email]', '$form[name]', '$form[department]', '$form[graduate_year]')";
		if( $result = $mysqli->query($query) ) {
			if( $result->num_rows )
				$msg = "已經存在的帳號。";
			else if( $mysqli->query($insert) ) {
				$msg = "<span style='color:#00f'>成功新增使用者！<br /><a href='.' style='color:#000'>點此返回首頁</a></span>";
				$success = true;
			}
			else
				$msg = "資料庫錯誤，請稍後再試。";
		}
		else
			$msg = "資料庫錯誤，請稍後再試。";
	}
	$objRes->assign('regMsg', 'innerHTML' , $msg);
	if( $success ) $objRes->call("registSucceeded");
	return $objRes;
}
function check($form, &$msg) {
	if( $form['username'] === "" ) $msg .= "帳號 ";
	if( $form['passwd'] == "" ) $msg .= "密碼 ";
	if( $form['email'] == "" ) $msg .= "e-mail ";
	if( $form['name'] == "" ) $msg .= "姓名 ";
	if( $form['department'] == "" ) $msg .= "系所 ";
	if( $form['graduate_year'] == "" ) $msg .= "畢業年份 ";
	if( $msg != "" ) $msg = $msg . "不可為空白。";
	else if( !preg_match('/^[A-Za-z][A-Za-z0-9]*(?:_[A-Za-z0-9]+)*$/', $form['username']) )
		$msg = "帳號不可包含英文大小寫、數字以外的字元！";
	else if( !filter_var($form["email"], FILTER_VALIDATE_EMAIL) )
		$msg = "e-mail信箱格式錯誤！";
	else if( !is_numeric($form["graduate_year"]) )
		$msg = "畢業年份必須為數字！";
	else if( $form["passwd"] != $form["passwdChk"] )
		$msg = "密碼輸入不一致";
	if( $msg==="" ) return true;
	else {
		$msg = "輸入錯誤！" . $msg;
		return false;
	}
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>新增使用者</title>
	<script type="text/javascript" src="<?php echo ROOT; ?>include/sha256.js"></script>
	<script type="text/javascript" src="<?php echo ROOT; ?>register.js"></script>
	<script tpye="text/javascript">
/* <![CDATA[ */
var account = {
	salt: "<?php echo SALT; ?>",
	encrypt: function(passwd) {
		document.getElementById(passwd+"SHA256").value = sha256_digest(document.getElementById(passwd).value + this.salt);
	},
	submit: function(passwd) {
		this.encrypt('passwd');
		this.encrypt('passwdChk');
		<?php $regCheck->printScript(); ?>;
	},
	init: function() {
	}
}
function registSucceeded() {
	console.log("regist succeeded");
}
/* ]]> */
	</script>
	<?php $xajax->printJavascript(ROOT.'include'); ?>
	<link rel="stylesheet" type="text/css" href="<?php echo ROOT; ?>register.css?v=<?php echo currentVersion?>" />
	<style type="text/css">
<!--
#container {
}
#registerForm {
	text-align: center;
}
#registerForm input[type="text"],
#registerForm input[type="password"],
#registerForm select {
	width: 200px;
}
#registerForm > table {
	border: none;
	margin: auto;
}
#registerForm > table tr {
	height: 40px;
}
#registerForm > table td:first-child { text-align: right; }
.msg {
	color: #f00;
	font-weight: bold;
}
#regMsg {
	height: 40px;
}
-->
	</style>
</head>

<body>

<div id="container">
<form id="registerForm" action="<?php echo ROOT; ?>register.php" method="POST">
<div class="msg" id="regMsg"></div>
<table>
	<tr>
		<td>帳號：</td>
		<td><input type="text" name="username" size="20" placeholder="請輸入使用者帳號" autocomplete="off" tabindex="1" value="<?php echo $username ?>" /></td>
	</tr>
	<tr>
		<td>密碼：</td>
		<td><input type="password" id="passwd" placeholder="請輸入密碼" tabindex="2" /></td>
	</tr>
	<tr>
		<td>請再次輸入密碼：</td>
		<td><input type="password" id="passwdChk" placeholder="再輸入一次" tabindex="3" /></td>
	</tr>
	<tr>
		<td>e-mail：</td>
		<td><input type="text" name="email" size="128" placeholder="請輸入e-mail" autocomplete="off" tabindex="4" /></td>
	</tr>
	<tr>
		<td>姓名：</td>
		<td><input type="text" name="name" placeholder="請輸入姓名" autocomplete="off" tabindex="5" /></td>
	</tr>
	<tr>
		<td>系所：</td>
		<td><input type="text" name="department" placeholder="請輸入系所" autocomplete="off" tabindex="6" /></td>
	</tr>
	<tr>
		<td>畢業年份：</td>
		<td>
			<select name="graduate_year" id="graduate_year"> </select>
		</td>
	</tr>
</table>
<br />
<input type="hidden" id="passwdSHA256"name="passwd" />
<input type="hidden" id="passwdChkSHA256" name="passwdChk" />
<input type="hidden" id="salt" name="salt" />
<input type="submit" value="確認" name="submit" onclick="account.submit();return false;" tabindex="8" />
<input type="button" value="清除" name="clear" onclick="clearForm();"tabindex="9" />
</form>
</div>

</body>
</html>

