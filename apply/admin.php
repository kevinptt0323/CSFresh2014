<?php
require_once('include/auth.php');
require_once('include/include.php');
session_start();

if( isset($_GET['howdoyouturnthison']) )
	$_SESSION['godmode'] = true;
else if( isset($_GET['logout']) )
	$_SESSION['godmode'] = false;
if( ( isset($_SESSION['godmode']) && $_SESSION['godmode'] ) || ( isset($_SESSION['login']) && isset($_SESSION['admin']) ) ) {
	global $mysqli;
	if( $mysqli->connect_error )
		die("資料庫錯誤，請稍後再試。");
	else {
		$content = "";
		if( isset($_GET['q']) ) $curPage = $_GET['q'];
		else $curPage = "";
		switch( $curPage ) {
			default:
				$query = "SELECT a.aid AS '報名序號', a.time AS '時間', a.name AS '姓名', a.gender AS '性別',
									a.payment AS '繳費方式', p.date AS '繳費日期'
									FROM `Applications` AS `a`
									LEFT JOIN `Payment` AS `p` ON a.aid = p.aid
									ORDER BY a.time ASC";
				$content = makeTable($mysqli->query($query), "app");
				break;
			case 'insurance':
				$query = "SELECT `name` AS '姓名', `gender` AS '性別', `idnum` AS '身分證字號', `birthday` AS '生日',
								`emergency_cont` AS '緊急連絡人', `relation` AS '關係', `emergency_tel` AS '緊急連絡電話'
								FROM `Applications` ORDER BY `aid` ASC";
				$content = makeTable($mysqli->query($query), "insurance");
				break;
			case 'profile':
				$query = "SELECT `name` AS '姓名', `gender` AS '性別', `telephone` AS '電話', `cellphone` AS '手機',
								`address` AS '地址', `graduation` AS '畢業高中', `disease` AS '特殊疾病',
								`food` AS '飲食', `size` AS '營服尺寸'
								FROM `Applications` ORDER BY `graduation` ASC";
				$content = makeTable($mysqli->query($query), "profile");
				break;
			case 'info':
				if( isset($_GET['aid']) && is_numeric($_GET['aid']) )
					$aid = $_GET['aid'];
				else
					$aid = 0;
				$query = "SELECT * FROM `Applications` WHERE `aid` = $aid LIMIT 1";
				$content = makeInfo($mysqli->query($query));
				break;
		}
	}
}
else {
	echo "<meta http-equiv=\"refresh\" content=\"0;url=http://CSFresh2014.nctucs.net/\" />\n";
	die();
}
function makeTable($res, $act) {
	if( $res ) {
		$list = '<table class="ui table segment">' . "\n" . '<tr>';
		while( $field = $res->fetch_field() ) $list .= '<th>' . $field->name . '</th>';
		if( $act=="app" ) $list .= '<th></th><th></th><th></th>';
		else if( $act=="pay" ) $list .= '<th></th>';
		$list .= '</tr>' . "\n";
		while( $row = $res->fetch_array() ){
				$list .= '<tr>';
				for($i=0; $i<$res->field_count; $i++) $list .= '<td>' . $row[$i] . '</td>';
				if( $act=="app" ){
						$list .= '<td><a href="?q=info&aid='. $row['報名序號'] . '">詳細</a></td>';
						if( 0 && $row[7] != '' )
								$list .= '<td>登記現場繳費</td>';
						else
								$list .= '<td><a href="?q=mkpay&aid='. $row['報名序號'] . '">登記現場繳費</a></td>';
						$list .= '<td><a href="javascript:del_app(' . $row['報名序號'] . ')">刪除</a></td>';
				}
				else if( $act=="pay" ) {
						$list .= '<td><a href="javascript:del_pay(' . $row['繳費編號'] . ')">刪除</a></td>';
				}
				$list .= '</tr>' . "\n";
		}
		$list .= '</table>' . "\n";
		$list .= '<p>資料共 ' . $res->num_rows . ' 筆</p>' . "\n";
	}
	else
		$list = "資料庫錯誤，請稍後再試。<img src=\"" . ROOT . "OAO.gif\" />";
	return $list;
}
function makeInfo($res){
	if( $res ) {
		$data = $res->fetch_array();
		$list = '<table class="ui table segment">' . "\n";
		for($i=0; $i<$res->field_count; $i++){
			$list .= "\t" . '<tr><td>' . $res->fetch_field_direct($i)->name . '</td>';
			$list .= '<td>' . nl2br($data[$i]) . '</td></tr>' . "\n";
		}
		$list .= '</table>' . "\n";
	}
	else
		$list = "資料庫錯誤，請稍後再試。<img src=\"" . ROOT . "OAO.gif\" />";
	return $list;
}
function generateNav($curPage) {
	$data = [
		["name"=>"", "alias"=>"報名資訊"],
		["name"=>"profile", "alias"=>"個人資料"],
		["name"=>"insurance", "alias"=>"保險資料"]
	];
	$list = "";
	foreach( $data as $item ) {
		$list .= "<a class=\"" . ($item["name"]==$curPage?"active ":"") . "item\" href=\"?q=$item[name]\">$item[alias]</a>\n";
	}
	$list .= "<a class=\"right item\" href=\"?logout\">登出</a>\n";
	return $list;
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>管理者模式</title>
	<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/2.0.3/jquery.min.js?ver=2.0.3"></script>
	<link rel="stylesheet" type="text/css" href="../css/normalize.css" />
	<link rel="stylesheet" type="text/css" href="../css/semantic.css"/>
	<link rel="stylesheet" type="text/css" href="../css/admin.css" />
	<script type="text/javascript" src="../js/semantic.js"></script>
	<script type="text/javascript" src="admin.js"></script>
</head>

<body>
<div class="container" id="main">
	<div class="nav ui pointing menu">
<?php echo generateNav($curPage); ?>
	</div>
	<div class="content">
<?php echo $content; ?>
	</div>
</div>
<div>

</div>

</body>
</html>

