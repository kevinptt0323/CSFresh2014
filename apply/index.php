<?php
require_once('include/include.php');
require_once('include/xajax_core/xajaxAIO.inc.php');
session_start();

$xajax = new xajax("register.php");
$regCheck = $xajax->registerFunction('regCheck');
$regCheck->useSingleQuote();
$regCheck->addParameter(XAJAX_FORM_VALUES, 'registerForm');
$xajax->processRequest();

if( isset($_POST['name']) ) $username = $_POST['name'];
else $username = "";
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
	<script type="text/javascript">
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
	clearForm();
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
		<form class="ui form segment" name="registerForm" id="registerForm" method="POST" action="<?php echo ROOT; ?>register.php">
			<div id="frm" class="frm">
				<div class="four fields">
					<div class="field">
						<label >姓名</label>
						<input name="name" placeholder="請輸入真實姓名" type="TEXT" tabindex="1">
					</div>
					<div class="five wide field">
						<label >生日</label>
						<input name="birthday" placeholder="YYYY/MM/DD，ex:1995/03/23" type="TEXT" tabindex="2">
					</div>
					<div class="three wide field">
						<label>我是</label>
						<div class="ui dropdown selection" tabindex="3">
								<input name='gender' type="hidden">
								<div class="default text">...</div>
								<i class="dropdown icon"></i>
								<div class="menu">
										<div class="item" data-value="M">帥哥(男)</div>
										<div class="item" data-value="F">正咩(女)</div>
								</div>
						</div>
					</div>
					<div class="field">
						<label>學號</label>
						<input name="studentID" placeholder="請輸入學號" type="TEXT" tabindex="4">
					</div>
				</div>
				<div class="two fields">
					<div class="four wide field">
							<label>身份證字號</label>
							<input name="idnum" placeholder="首位大寫，A123456789" type="TEXT" tabindex="5">
					</div>
					<div class="twelve wide field">
							<label>E-mail</label>
							<input name="email" placeholder="請輸入email" type="TEXT" tabindex="6">
					</div>
				</div>
				<div class="two fields">
						<div class="four wide field">
								<label>手機</label>
								<input name="cellphone" placeholder="0912-345678" type="TEXT" tabindex="7">
						</div>
						<div class="twelve wide field">
								<label>地址</label>
								<input name="address" placeholder="你家住哪啊?" type="TEXT" tabindex="8">
						</div>
				</div>
				<div class="four fields">
						<div class="four wide field">
								<label>家裡電話</label>
								<input name="telephone" placeholder="03-5712121" type="TEXT" tabindex="9">
						</div>
						<div class="four wide field">
								<label>緊急聯絡人</label>
								<input name="emergency_cont" placeholder="" type="TEXT" tabindex="10">
						</div>
						<div class="three wide field">
								<label>關係</label>
								<input name="relation" placeholder="" type="TEXT" tabindex="11">
						</div>
						<div class="five wide field">
								<label>緊急聯絡人電話</label>
								<input name="emergency_tel" placeholder="0912-345678" type="TEXT" tabindex="12">
						</div>
				</div>
				<div class="five fields">
						<div class="four wide field">
							<label>血型(特殊血請註明)</label>
							<input name="bloodtype" placeholder="A/B/AB/O/其它" type="TEXT" tabindex="13">
						</div>
						<div id="food" class="three wide field">
							<label>飲食習慣</label>
								<div class="ui radio checkbox">
									<input name="food" value="meat" type="RADIO" tabindex="14">
									<label>肉食性</label>
								</div>
						</div>
						<div class="two wide field">
								<label><br></label>
								<div class="ui radio checkbox">
									<input name="food" value="veg" type="RADIO" tabindex="15">
									<label>草食性</label>
								</div>
						</div>
						<div class="five wide field">
							<label>畢業高中</label>
							<input name="graduation" placeholder="ex：師大附中/台中女中" type="TEXT" tabindex="16">
						</div>
					<div class="two wide field">
						<label>營服尺寸</label>
						<div tabindex="17" class="ui dropdown selection">
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
						<input type="text" placeholder="看到總召就會想捏他的病…" name="disease" tabindex="18">
					</div>
				</div>
				<div class="field">
					<label>為什麼想來迎新宿營咧?</label>
					<textarea id="reason_ta" name="reason" placeholder="因為聽說值星很帥呢A_A" tabindex="20"></textarea>
				</div>
				<div class="field">
					<label>對大學的期待</label>
					<textarea id="expection_ta" name="expectation" placeholder="Write your expection..." tabindex="21"></textarea>
				</div>
		</form>
	</div>
<div id="button_div">
	<div id="submit_button" class="ui down button" onclick="$('#registerForm').form('validate form');account.submit();return false;" tabindex="22" >
		<i class="checkmark sign icon"></i>
		送出
	</div>
	<div id="clear_button" class="ui down button" onclick="clearForm();" tabindex="23">
		<i class="trash icon"></i>
		清除
	</div>
</div>

<script>
	$('.ui.dropdown').dropdown() ;
	$('.ui.checkbox').checkbox() ;
$('#registerForm')
		.form({
				name: 	{ identifier: 'name',				rules: [ { type: 'empty', } ] },
		birthday:		{ identifier: 'birthday',		rules: [ { type: 'empty', } ] },
		studentID:	{ identifier: 'studentID',	rules: [ { type: 'empty', } ] },
		idnum:			{ identifier: 'idnum',			rules: [ { type: 'empty', } ] },
		email:			{ identifier: 'email',			rules: [ { type: 'email', } ] },
		gender:			{ identifier: 'gender',			rules: [ { type: 'empty', } ] },
		telephone:	{ identifier: 'telephone',	rules: [ { type: 'empty', } ] },
		address:		{ identifier: 'address',		rules: [ { type: 'empty', } ] },
		emergency_cont:		{ identifier: 'emergency_cont',		rules: [ { type: 'empty', } ] },
		emergency_tel:		{ identifier: 'emergency_tel',		rules: [ { type: 'empty', } ] },
		relation:		{ identifier: 'relation',		rules: [ { type: 'empty', } ] },
		bloodtype:	{ identifier: 'bloodtype',	rules: [ { type: 'empty', } ] },
		graduation:	{ identifier: 'graduation',	rules: [ { type: 'empty', } ] },
		size:				{ identifier: 'size',				rules: [ { type: 'empty', } ] },
		food:				{ identifier: 'food',				rules: [ { type: 'checked', } ] },

})

</script>
</body>
</html>
