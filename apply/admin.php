<?php
if(!isset($_SERVER['HTTPS']) || $_SERVER['HTTPS'] == ""){
	$redirect = "https://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
	header("Location: $redirect");
	die();
}
require_once('include/auth.php');
require_once('include/include.php');
session_start();

if( isset($_SESSION['admin_username']) && isset($_SESSION['admin']) && $_SESSION['admin'] ) {
	global $mysqli;
	if( $mysqli->connect_error )
		die("資料庫錯誤，請稍後再試。");
	else {
		$content = "";
		if( isset($_GET['q']) ) $curPage = $_GET['q'];
		else $curPage = "info";
		switch( $curPage ) {
			case 'info':
			default:
				$curPage = "info";
				if( isset($_GET['aid']) && is_numeric($_GET['aid']) ) {
					$aid = $_GET['aid'];
					$query = "SELECT * FROM `Applications` WHERE `aid` = $aid LIMIT 1";
					$content = makeInfo($result = $mysqli->query($query));
				}
				else {
					$query = "SELECT a.aid AS '報名序號', a.time AS '時間', a.name AS '姓名', a.studentID AS '學號', a.gender AS '性別',
										CASE WHEN a.payment=0 THEN '尚未繳費' WHEN a.payment=1 THEN '匯款' WHEN a.payment=2 THEN '現場繳費' END AS '繳費方式',
										p.date AS '繳費日期', p.account AS '帳號末五碼'
										FROM `Applications` AS `a`
										LEFT JOIN `Payment` AS `p` ON a.aid = p.aid
										ORDER BY a.time ASC";
					$content = makeTable($result = $mysqli->query($query), "info");
				}
				$result->free();
				break;
			case 'insurance':
				$query = "SELECT aid AS '報名序號', `name` AS '姓名', `gender` AS '性別', `idnum` AS '身分證字號', `birthday` AS '生日',
								`emergency_cont` AS '緊急連絡人', `relation` AS '關係', `emergency_tel` AS '緊急連絡電話'
								FROM `Applications` ORDER BY `time` ASC";
				$content = makeTable($result = $mysqli->query($query), "insurance");
				$result->free();
				break;
			case 'profile':
				$query = "SELECT aid AS '報名序號', `name` AS '姓名', `gender` AS '性別', `telephone` AS '電話', `cellphone` AS '手機',
								`address` AS '地址', `graduation` AS '畢業高中', `disease` AS '特殊疾病',
								CASE WHEN food='meat' THEN '葷' WHEN food='veg' THEN '素' END AS '飲食', `size` AS '營服尺寸'
								FROM `Applications` ORDER BY `time` ASC";
				$content = makeTable($result = $mysqli->query($query), "profile");
				$result->free();
				break;
			case 'del_app':
				$aid = $_GET['aid'];
				//eventlog('DELETE id: '.$data['aid'].' name: '.$data['name'].' cell: '.$data['cellphone'].' IP: '.$_SERVER['REMOTE_ADDR']);
				//echo '刪除失敗，請聯絡管理員';
				//break;
				$mysqli->query("DELETE FROM `Applications` WHERE `aid` = $aid LIMIT 1");
				$mysqli->query("DELETE FROM `Payment` WHERE `aid` = $aid LIMIT 1");
				echo '<script type="text/javascript">history.back();</script>';
				die();
				break;
			case 'mkpay':
				$aid = $_GET['aid'];
				if( ($result = $mysqli->query("SELECT * FROM `Applications` WHERE `aid` = '$aid' LIMIT 1;")) && $result->num_rows ) {
					$mysqli->query("INSERT INTO `Payment` (`aid`, `pay_type`, `date`, `uid`) VALUE ('$aid', 2, DATE_FORMAT(NOW(), '%m/%e'), '$_SESSION[admin_uid]')");
					$mysqli->query("UPDATE `Applications` SET `payment` = '2' WHERE `aid` = '$aid'");
				}
				$result->free();
				echo '<script type="text/javascript">history.back();</script>';
				die();
				break;
			case 'update':
				if( isset($_GET['aid']) && isset($_POST['name']) && isset($_POST['value']) ) {
					$aid = $_GET['aid'];
					$name = $_POST['name'];
					$value = $_POST['value'];
					if( ($result = $mysqli->query("SELECT * FROM `Applications` WHERE `aid` = '$aid' LIMIT 1;")) && $result->num_rows ) {
						$mysqli->query("UPDATE `Applications` SET `$name` = '$value' WHERE `aid` = '$aid'");
					}
					$result->free();
				}
				echo '<script type="text/javascript">history.back();</script>';
				die();
				break;
			/*
			case 'del_pay':
				$pid = $_GET['pid'];
				$res = mysql_query("SELECT * FROM `payment` WHERE `pid` = $pid LIMIT 1");
				$data = mysql_fetch_array($res);
				eventlog('DELETE PAYMENT pid: '.$data['pid'].' id: '.$data['aid'].' type: '.$data['type'].' account: '.$data['account'].' IP: '.$_SERVER['REMOTE_ADDR']); 
				mysql_query("DELETE FROM `payment` WHERE `pid` = $pid LIMIT 1");
				echo '<script type="text/javascript">history.back();</script>';
				break;
			*/
		}
	}
}
else {
	echo "<meta http-equiv=\"refresh\" content=\"0;url=http://CSFresh2014.nctucs.net/apply/login.php\" />\n";
	die();
}
function makeTable($res, $act) {
	if( $res ) {
		$list = '<table class="ui table segment">' . "\n\t<tr>";
		while( $field = $res->fetch_field() ) $list .= "<th>$field->name</th>";
		$list .= "<th></th>";
		if( $act=="info" ) $list .= "<th></th><th></th>";
		else if( $act=="pay" ) $list .= "<th></th>";
		$list .= "</tr>\n";
		while( $row = $res->fetch_array() ){
				$list .= "\t<tr>";
				for($i=0; $i<$res->field_count; $i++) $list .= "<td>$row[$i]</td>";
				$list .= '<td><a href="?q=info&aid='. $row['報名序號'] . '">詳細</a></td>';
				if( $act=="info" ){
					if( $row['繳費方式']=="尚未繳費" )
						$list .= '<td><a href="javascript:mkpay('. $row['報名序號'] . ')">登記現場繳費</a></td>';
					else
						$list .= "<td></td>";
					$list .= '<td><a href="javascript:del_app(' . $row['報名序號'] . ')">刪除</a></td>';
				}
				else if( $act=="pay" ) {
						$list .= '<td><a href="javascript:del_pay(' . $row['繳費編號'] . ')">刪除</a></td>';
				}
				$list .= "</tr>\n";
		}
		$list .= "</table>\n";
		$list .= "<p>資料共 $res->num_rows 筆</p>\n";
	}
	else
		$list = "資料庫錯誤，請稍後再試。<img src=\"" . ROOT . "OAO.gif\" />";
	return $list;
}
function makeInfo($res){
	if( $res ) {
		$data = $res->fetch_array();
		$list = "<table class=\"ui table segment\">\n";
		for($i=0; $i<$res->field_count; $i++){
			$list .= "\t<tr>\n\t\t<td>" . $res->fetch_field_direct($i)->name . "</td>\n";
			$list .= "\t\t<td><div class=\"data\">" . nl2br($data[$i]) . "</div><div class=\"edit\">";
			if( $i>2 ) {
				$list .= '<form class="ui small action input editing" action="?q=update&aid=' . $data[0] . '" method="post" rel="' . $i . '">';
				$list .= '<input type="hidden" name="name" value="' . $res->fetch_field_direct($i)->name . '" />';
				$list .= '<input type="text" name="value" value="' . nl2br($data[$i]) . '" />';
				$list .= '<div class="ui small button submit" onclick="submitForm(' . $i . ');">確認</div><div class="ui small button cancel">取消</div>';
				$list .= "</form>";
			}
			else
				$list .= nl2br($data[$i]);
			$list .= "</div>\n</td></tr>\n";
		}
		$list .= "</table>\n";
	}
	else
		$list = "資料庫錯誤，請稍後再試。<img src=\"" . ROOT . "OAO.gif\" />";
	return $list;
}
function generateNav($curPage) {
	$data = [
		["name"=>"info", "alias"=>"報名資訊"],
		["name"=>"profile", "alias"=>"個人資料"],
		["name"=>"insurance", "alias"=>"保險資料"],
		["name"=>"", "alias"=>"&nbsp;"],
	];
	$list = "";
	foreach( $data as $item ) {
		$list .= "<a class=\"" . ($item["name"]==$curPage?"active ":"") . "item\" href=\"?q=$item[name]\">$item[alias]</a>\n";
	}
	$list .= "<div class=\"right menu\">\n";
	$list .= "\t<span class=\"item\">" . $_SESSION['admin_name'] . "，你好</span>\n";
	$list .= "\t<a class=\"item\" href=\"new_admin.php?" . NEWADMINPASSWD . "\" target=\"_blank\">新增管理員</a>\n";
	$list .= "\t<a class=\"item\" href=\"login.php\" target=\"_blank\">新增匯款資訊</a>\n";
	$list .= "\t<a class=\"item\" href=\"login.php?logout_admin\">登出</a>\n";
	$list .= "</div>\n";
	return $list;
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>管理者模式 | 2014 交大資工 迎新宿營</title>
	<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/2.0.3/jquery.min.js?ver=2.0.3"></script>
	<link rel="stylesheet" type="text/css" href="../css/normalize.css" />
	<link rel="stylesheet" type="text/css" href="../css/semantic.css"/>
	<link rel="stylesheet" type="text/css" href="../css/admin.css" />
	<script type="text/javascript" src="../js/semantic.js"></script>
<script type="text/javascript">
/* <![CDATA[ */
function del_app(aid){
	if( confirm("確定刪除編號 "+aid+" ?") && confirm("真的不後悔刪除編號 "+aid+" ?") ) location.href = "?q=del_app&aid=" + aid;
}
function mkpay(aid){
	if(confirm("確定登記編號 "+aid+" 現場繳費?")) location.href = "?q=mkpay&aid=" + aid;
}
function submitForm(formID) {
	$("form.editing").eq(formID).submit();
	$("form.editing").eq(formID).parent().parent().children("div.edit").hide();
	$("form.editing").eq(formID).parent().parent().children("div.data").show();
}
$(function() {
	$("td:nth-child(2)").bind("dblclick", function() {
		$(this).children("div.data").hide();
		$(this).children("div.edit").show();
	});
	$("div.edit .submit").bind("click", function() {
	});
	$("div.edit .cancel").bind("click", function() {
		$(this).parent().parent().parent().children("div.edit").hide();
		$(this).parent().parent().parent().children("div.data").show();
	});
});
/* ]]> */
</script>
</head>

<body>
<div class="container" id="main">
	<div class="nav ui secondary pointing blue menu">
<?php echo generateNav($curPage); ?>
	</div>
	<div class="content">
		<p>雙擊欲修改的項目以編輯。修改並送出之後，建議先按重新整理以確認資料更新。</p>
<?php echo $content; ?>
	</div>
</div>
<div>

</div>

</body>
</html>

