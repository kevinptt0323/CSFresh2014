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
	else $objRes->call("registFailed");
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
<!DOCTYPE html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
	<title>宿營?報名就對了啦!</title>
	<script src="http://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.js"></script>
	<link rel="stylesheet" type="text/css" href="../css/normalize.css" />
	<link rel="stylesheet" href="../css/semantic.css" type="text/css"/>
	<link rel="stylesheet" href="../css/reg_style.css" type="text/css"/>
	<script src="../js/semantic.js"></script>
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
	$('#regMsg').removeClass('info').removeClass('error').addClass('positive');
	console.log("regist succeeded");
}
function registFailed() {
	$('#regMsg').removeClass('info').removeClass('positive').addClass('error');
	console.log("regist failed");
}
/* ]]> */
	</script>
	<?php $xajax->printJavascript(ROOT.'include'); ?>
</head>
<body>
	<div id="container" class="container">
		<div class="ui ignored info message" id="regMsg">
			<p>請輸入你的資料~</p>
		</div>
		<form class="ui form segment" name="registerForm" id="registerForm" method="POST" action="<?php echo ROOT; ?>">
			<div id="frm" class="frm">
				<div class="four fields">
					<div class="field">
						<label>姓名</label>
						<input name="name" placeholder="請輸入真實姓名" type="TEXT">
					</div>
					<div class="five wide field">
						<label>生日</label>
						<input name="birthday" placeholder="YYYY/MM/DD，ex:1995/03/23" type="TEXT">
					</div>
					<div class="three wide field">
						<label>我是</label>
						<div class="ui dropdown selection">
								<input name='gender' type="hidden">
								<div class="default text">...</div>
								<i class="dropdown icon"></i>
								<div class="menu">
										<div class="item" data-value="M">帥哥</div>
										<div class="item" data-value="F">正咩</div>
								</div>
						</div>
					</div>
					<div class="field">
						<label>學號</label>
						<input name="studentID" placeholder="請輸入學號" type="TEXT">
					</div>
				</div>
				<div class="two fields">
					<div class="five wide field">
							<label>身份證字號</label>
							<input name="idnum" placeholder="首位大寫，A123456789" type="TEXT">
					</div>
					<div class="eleven wide field">
							<label>E-mail</label>
							<input name="email" placeholder="請輸入email" type="TEXT">
					</div>
				</div>
				<div class="two fields">
						<div class="five wide field">
								<label>手機</label>
								<input name="cellphone" placeholder="0912-345678" type="TEXT">
						</div>
						<div class="eleven wide field">
								<label>地址</label>
								<input name="idnum" placeholder="你家住哪啊?" type="TEXT">
						</div>
				</div>
				<div class="four fields">
						<div class="five wide field">
								<label>家裡電話</label>
								<input name="telephone" placeholder="03-5712121" type="TEXT">
						</div>
						<div class="three wide field">
								<label>緊急聯絡人</label>
								<input name="emergency_cont" placeholder="" type="TEXT">
						</div>
						<div class="three wide field">
								<label>關係</label>
								<input name="relation" placeholder="" type="TEXT">
						</div>
						<div class="five wide field">
								<label>緊急聯絡人電話</label>
								<input name="emergency_tel" placeholder="0912-345678" type="TEXT">
						</div>
				</div>
				<div class="five fields">
						<div class="four wide field">
							<label>血型(特殊血請註明)</label>
							<input name="bloodtype" placeholder="A/B/AB/O/其它" type="TEXT">
						</div>
						<div class="three wide field" align="center">
							<label>飲食習慣</label>
								<div class="ui radio checkbox">
									<input name="food" value="meat" type="RADIO">
									<label>肉食性</label>
								</div>
						</div>
						<div class="two wide field">
								<label><br></label>
								<div class="ui radio checkbox">
									<input name="food" value="veg" type="RADIO">
									<label>草食性</label>
								</div>
						</div>
						<div class="five wide field">
							<label>畢業高中</label>
							<input name="graduation" placeholder="ex：師大附中/台中女中" type="TEXT">
						</div>
					<div class="two wide field">
						<label>營服尺寸</label>
						<div class="ui dropdown selection">
								<input name="size" type="hidden">
								<div class="default text">...</div>
								<i class="dropdown icon"></i>
								<div class="menu">
										<div class="item" data-value="XS">XS</div>
										<div class="item" data-value="S">S</div>
										<div class="item" data-value="M">M</div>
										<div class="item" data-value="L">L</div>
										<div class="item" data-value="XL">XL</div>
										<div class="item" data-value="2L">2L</div>
								</div>
						</div>
					</div>
				</div>
					<div class="field">
						<label>特殊疾病</label>
						<input type="text" placeholder="看到總召就會想捏他的病…" name="disease">
					</div>
				</div>
				<div class="field">
					<label>為什麼想來迎新宿營咧?</label>
					<textarea id="reason_ta" name="reason" placeholder="因為聽說值星很帥呢A_A"></textarea>
				</div>
				<div class="field">
					<label>對大學的期待</label>
					<textarea id="expection_ta" name="expection" placeholder="Write your expection..."></textarea>
				</div>
			</div>
		</form>
	</div>
					<center>

					<div id="submit_botton" class="ui down button" type="submit" onclick="$('#registerForm').form('validate form');account.submit();return false;" >
						<i class="checkmark sign icon"></i>
						送出
					</div>
					<div id="clear_botton" class="ui down button" type="button" onclick="clearForm();">
						<i class="trash icon"></i>
						清除
					</div>
					</center>

<script>
	$('.ui.dropdown').dropdown() ;
	$('.ui.checkbox').checkbox() ;
$('#registerForm')
  .form({
    firstName: {
      identifier : 'name',
      rules: [
        {
          type   : 'empty',
          prompt : 'Please enter your first name'
        }
      ]
    }
})

</script>
</body>
</html>
