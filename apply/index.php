<?php
require_once('include/auth.php');
require_once('include/include.php');
require_once('include/xajax_core/xajaxAIO.inc.php');
session_start();

$xajax = new xajax();
$regCheck = $xajax->registerFunction('regCheck');
$regCheck->useSingleQuote();
$regCheck->addParameter(XAJAX_FORM_VALUES, 'registerForm');
$xajax->processRequest();

if( isset($_POST['name']) ) $username = $_POST['name'];
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
		$query = "SELECT * FROM `Applications` WHERE `name` = '$form[name]' LIMIT 1;";
		/*$insert = "INSERT INTO `Applications` (
`name`, `gender`, `studentID`, `address`, `email`, `idnum`, `birthday`, `telephone`, `cellphone`, `emergency_cont`, `relation`, `emergency_tel`, `food`, `disease`, `bloodtype`, `graduation`, `size`, `reason`, `expection`
) VALUES (
'$form[name]', '$form[gender]', '$form[studentID]', '$form[address]', '$form[email]', '$form[idnum]', '$form[birthday]', '$form[telephone]', '$form[cellphone]', '$form[emergency_cont]', '$form[relation]', '$form[emergency_tel]', '$form[food]', '$form[disease]', '$form[bloodtype]', '$form[graduation]', '$form[size]', '$form[reason]', '$form[expection]'
)";*/
		if( $result = $mysqli->query($query) ) {
			if( $result->num_rows )
				$msg = "你已經報名過了哦！別這麼急啦^.&lt;";
			/*else if( $mysqli->query($insert) ) {
				$msg = "<span style='color:#00f'>成功報名！<br /><a href='.' style='color:#000'>點此返回首頁</a></span>";
				$success = true;
			}*/
			else {
				$msg =  "暫時不開放報名唷～";
				//$msg = "資料庫錯誤，請稍後再試。<img src=\"" . ROOT . "OAO.gif\" />";
			}
		}
		else
			$msg = "資料庫錯誤，請稍後再試。<img src=\"" . ROOT . "OAO.gif\" />";
	}
	$objRes->assign('regMsg', 'innerHTML' , $msg);
	if( $success ) $objRes->call("registSucceeded");
	return $objRes;
}
function checkIDNum($id) {
	$id = strtoupper($id);
	//建立字母分數陣列
	$headPoint = array(
		'A'=>1,'I'=>39,'O'=>48,'B'=>10,'C'=>19,'D'=>28,
		'E'=>37,'F'=>46,'G'=>55,'H'=>64,'J'=>73,'K'=>82,
		'L'=>2,'M'=>11,'N'=>20,'P'=>29,'Q'=>38,'R'=>47,
		'S'=>56,'T'=>65,'U'=>74,'V'=>83,'W'=>21,'X'=>3,
		'Y'=>12,'Z'=>30
	);
	//建立加權基數陣列
	$multiply = array(8,7,6,5,4,3,2,1);
	//檢查身份字格式是否正確
	if (ereg("^[a-zA-Z][1-2][0-9]+$",$id) AND strlen($id) == 10){
		//切開字串
		$len = strlen($id);
		for($i=0; $i<$len; $i++){
			$stringArray[$i] = substr($id,$i,1);
		}
		//取得字母分數
		$total = $headPoint[array_shift($stringArray)];
		//取得比對碼
		$point = array_pop($stringArray);
		//取得數字分數
		$len = count($stringArray);
		for($j=0; $j<$len; $j++){
			$total += $stringArray[$j]*$multiply[$j];
		}
		//計算餘數碼並比對
		$last = (($total%10) == 0 )?0:(10-($total%10));
		if ($last != $point) {
			return false;
		} else {
			return true;
		}
	}  else {
	   return false;
	}
}
function check($form, &$msg) {
	return true;
	if( $form['name'] == "" )			$msg .= "姓名 ";
	if( @$form['gender'] == "" )		$msg .= "性別 ";
	if( @$form['studentID'] == "" )		$msg .= "學號 ";
	if( @$form['address'] == "" )		$msg .= "住址 ";
	if( @$form['email'] == "" )			$msg .= "e-mail ";
	if( @$form['idnum'] == "" )			$msg .= "身分證字號 ";
	if( @$form['birthday'] == "" )		$msg .= "生日 ";
	if( @$form['telephone'] == "" )		$msg .= "連絡電話(家) ";
	if( @$form['emergency_cont'] == "" ) $msg .= "緊急連絡人 ";
	if( @$form['relation'] == "" )		$msg .= "關係 ";
	if( @$form['emergency_tel'] == "" )	$msg .= "緊急連絡人電話 ";
	if( @$form['food'] == "" )			$msg .= "飲食習慣 ";
	if( @$form['bloodtype'] == "" )		$msg .= "血型 ";
	if( @$form['graduation'] == "" )	$msg .= "畢業高中 ";
	if( @$form['size'] == "" )			$msg .= "營服尺寸 ";
	if( @$msg != "" ) $msg = $msg . "不可為空白。";
	else if( !filter_var($form["email"], FILTER_VALIDATE_EMAIL) )
		$msg = "e-mail信箱格式錯誤！";
	else if( !@filter_var($form["idnum"], FILTER_CALLBACK, array("options"=>"checkIDNum")))
		$msg = "身分證字號格式錯誤！";
	else if( !is_numeric($form["graduate_year"]) )
		$msg = "畢業年份必須為數字！";
	if( $msg==="" ) return true;
	else {
		$msg = "輸入錯誤！<img src=\"" . ROOT . "OAO.gif\" /><br/ >" . $msg;
		return false;
	}
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>新增使用者</title>
	<script type="text/javascript" src="<?php echo ROOT; ?>register.js"></script>
	<script tpye="text/javascript">
/* <![CDATA[ */
var account = {
	submit: function(passwd) {
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
<form id="registerForm" action="<?php echo ROOT; ?>index.php" method="POST">
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
<input type="submit" value="確認" name="submit" onclick="account.submit();return false;" tabindex="8" />
<input type="button" value="清除" name="clear" onclick="clearForm();" tabindex="9" />
</form>
</div>

</body>
</html>

