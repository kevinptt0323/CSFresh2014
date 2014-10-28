<?php
require_once('include/auth.php');
require_once('include/include.php');
require_once('include/xajax_core/xajaxAIO.inc.php');

$xajax = new xajax("register.php");
$regCheck = $xajax->registerFunction('regCheck');
$regCheck->useSingleQuote();
$regCheck->addParameter(XAJAX_FORM_VALUES, 'registerForm');
$xajax->processRequest();

function regCheck($form) {
	global $mysqli;
	$success = false;
	$objRes = new xajaxResponse();
	// disable apply system
	$objRes->assign('regMsg', 'innerHTML' , '已經結束報名，歡迎明年再來！');
	$objRes->call("registFailed");
	return $objRes;
	escape($form,
		['name', 'gender', 'studentID', 'address', 'email', 'idnum', 'birthday', 'telephone', 'cellphone', 'emergency_cont', 'relation', 'emergency_tel', 'food', 'disease', 'bloodtype', 'graduation', 'size', 'reason', 'expectation']
	);
	if( !check($form, $msg) );
	else if( $mysqli->connect_error )
		$msg = "資料庫錯誤，請稍後再試。";
	else {
		$query = "SELECT * FROM `Applications` WHERE `idnum` = '$form[idnum]' LIMIT 1;";
		$insert = "INSERT INTO `Applications` (
`name`, `time`, `gender`, `studentID`, `address`, `email`, `idnum`, `birthday`, `telephone`, `cellphone`, `emergency_cont`, `relation`, `emergency_tel`, `food`, `disease`, `bloodtype`, `graduation`, `size`, `reason`, `expectation`, `payment`
) VALUES (
'$form[name]', now(),  '$form[gender]', '$form[studentID]', '$form[address]', '$form[email]', '$form[idnum]', '$form[birthday]', '$form[telephone]', '$form[cellphone]', '$form[emergency_cont]', '$form[relation]', '$form[emergency_tel]', '$form[food]', '$form[disease]', '$form[bloodtype]', '$form[graduation]', '$form[size]', '$form[reason]', '$form[expectation]', 0
)";
		if( $result = $mysqli->query($query) ) {
			if( $result->num_rows )
				$msg = "你已經報名過了哦！別這麼急啦^.&lt;";
			else {
				if( $mysqli->query($insert) ) {
					$msg = $form['name'] . "，恭喜" . ($form['gender']=='M'?"你":"妳") . "報名成功！<br /><a href='../'>點此返回首頁</a>";
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
	$objRes->assign('regMsg', 'innerHTML' , $msg);
	if( $success ) $objRes->call("registSucceeded");
	else $objRes->call("registFailed");
	return $objRes;
}
function check($form, &$msg) {
	$msg = "";
	$checking = ['name', 'gender', 'address', 'email', 'idnum', 'birthday', 'telephone', 'emergency_cont', 'relation', 'emergency_tel', 'food', 'bloodtype', 'graduation', 'size'];
	foreach($checking as $str)
		if( @$form[$str] == "" ) $msg = "error";
	if( $msg != "" ) $msg = "紅框處不可為空白。";
	else if( !filter_var($form["email"], FILTER_VALIDATE_EMAIL) )
		$msg = "e-mail信箱格式錯誤！";
	else if( !@filter_var($form["idnum"], FILTER_CALLBACK, ["options"=>"checkIDNum"]))
		$msg = "身分證字號格式錯誤！";
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
function checkIDNum($id) {
	if( $id[0]=='*' ) return true;
	$id = strtoupper($id);
	//建立字母分數陣列
	$headPoint = [
		'A'=>1,'I'=>39,'O'=>48,'B'=>10,'C'=>19,'D'=>28,
		'E'=>37,'F'=>46,'G'=>55,'H'=>64,'J'=>73,'K'=>82,
		'L'=>2,'M'=>11,'N'=>20,'P'=>29,'Q'=>38,'R'=>47,
		'S'=>56,'T'=>65,'U'=>74,'V'=>83,'W'=>21,'X'=>3,
		'Y'=>12,'Z'=>30
	];
	//建立加權基數陣列
	$multiply = [8,7,6,5,4,3,2,1];
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
?>
