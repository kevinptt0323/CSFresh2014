<?php
require_once('include/auth.php');
require_once('include/include.php');
session_start();

if( isset($_GET['howdoyouturnthison']) || isset($_SESSION['login']) && isset($_SESSION['admin']) ) {
	if( dbconn() )
		die("資料庫錯誤，請稍後再試。");
	else {
		global $mysqli;
		$content = "";
		if( isset($_GET['q']) ) $qq = $_GET['q'];
		else $qq = "";
		switch( $qq ) {
			default:
				$query = "SELECT a.aid AS '報名序號', a.time AS '時間', a.name AS '姓名', a.gender AS '性別',
									a.payment AS '繳費方式', p.date AS '繳費日期'
									FROM `Applications` AS `a`
									LEFT JOIN `Payment` AS `p` ON a.aid = p.aid
									ORDER BY a.time ASC";
				$content = makeTable($mysqli->query($query), "");
				break;
		}
	}
}
else {
	die();
	echo "<meta http-equiv=\"refresh\" content=\"0;url=http://CSFresh2014.nctucs.net/\" />\n";
}
function makeTable($res, $act) {
	if( $res ) {
		$list = '<table>' . "\n" . '<tr>';
		while( $field = $res->fetch_field() ) $list .= '<th>' . $field->name . '</th>';
		if( $act=="app" ) $list .= '<th></th><th></th><th></th>';
		else if( $act=="pay" ) $list .= '<th></th>';
		$list .= '</tr>' . "\n";
		while( $row = $res->fetch_array() ){
				$list .= '<tr>';
				for($i=0; $i<$res->field_count; $i++) $list .= '<td>' . $row[$i] . '</td>';
				if( $act=="app" ){
						$list .= '<td><a href="?q=info&aid='. $row['報名序號'] . '">詳細</a></td>';
						if( $row[7] != '' )
								$list.= '<td>登記現場繳費</td>';
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
<div class="container">
	<div class="nav"></div>
	<div class="content">
<?php echo $content; ?>
	</div>
</div>
<div>

</div>

</body>
</html>

