<?php
require_once('include/include.php');
require_once('include/xajax_core/xajaxAIO.inc.php');

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
	<title>宿營?報名就對了啦! | 2014 交大資工 迎新宿營</title>
	<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/2.0.3/jquery.min.js?ver=2.0.3"></script>
	<link rel="stylesheet" type="text/css" href="../css/normalize.css" />
	<link rel="stylesheet" href="../css/semantic.css" type="text/css"/>
	<link rel="stylesheet" href="../css/reg_style.css" type="text/css"/>
	<script type="text/javascript" src="../js/semantic.js"></script>
	<script type="text/javascript">
/* <![CDATA[ */
var account = {
	submit: function(passwd) {
		$('#registerForm').form('validate form');
		<?php $regCheck->printScript(); ?>;
	},
	init: function() {
	}
}
function clearForm() {
	var elem = document.getElementById('registerForm').elements;
	for(i=0; i<elem.length; ++i) {
		if( elem[i].type=="text" || elem[i].type=="password" || elem[i].tagName=="TEXTAREA" ) elem[i].value="";
		if( elem[i].type=="radio" ) elem[i].checked="";
	}
	$('.ui.dropdown').dropdown("restore defaults");
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
$(function() {
	$('.ui.dropdown').dropdown() ;
	$('#registerForm')
		.form({
			name:				{ identifier: 'name',				rules: [ { type: 'empty', } ] },
			birthday:		{ identifier: 'birthday',		rules: [ { type: 'empty', } ] },
			idnum:			{ identifier: 'idnum',			rules: [ { type: 'empty', } ] },
			email:			{ identifier: 'email',			rules: [ { type: 'email', } ] },
			gender:			{ identifier: 'gender',			rules: [ { type: 'empty', } ] },
			telephone:	{ identifier: 'telephone',	rules: [ { type: 'empty', } ] },
			address:		{ identifier: 'address',		rules: [ { type: 'empty', } ] },
			emergency_cont:	{ identifier: 'emergency_cont',		rules: [ { type: 'empty', } ] },
			emergency_tel:	{ identifier: 'emergency_tel',		rules: [ { type: 'empty', } ] },
			relation:		{ identifier: 'relation',		rules: [ { type: 'empty', } ] },
			bloodtype:	{ identifier: 'bloodtype',	rules: [ { type: 'empty', } ] },
			graduation:	{ identifier: 'graduation',	rules: [ { type: 'empty', } ] },
			size:				{ identifier: 'size',				rules: [ { type: 'empty', } ] },
			food:				{ identifier: 'food',				rules: [ { type: 'checked', } ] },
		});
	});
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
						<label>姓名</label>
						<div class="ui labeled input">
							<input name="name" placeholder="請輸入真實姓名" type="text" tabindex="1">
							<div class="ui corner label"> <i class="asterisk icon"></i> </div>
						</div>
					</div>
					<div class="field">
						<label >生日</label>
						<div class="ui labeled input">
							<input name="birthday" placeholder="YYYY/MM/DD，ex:1995/03/23" type="text" tabindex="2">
							<div class="ui corner label"> <i class="asterisk icon"></i> </div>
						</div>
					</div>
					<div class="field">
						<label>我是</label>
						<div class="ui labeled dropdown selection" tabindex="3">
							<input name='gender' type="hidden">
							<div class="default text">...</div>
							<i class="dropdown icon"></i>
							<div class="menu">
								<div class="item" data-value="M">帥哥(男)</div>
								<div class="item" data-value="F">正咩(女)</div>
							</div>
							<div class="ui corner label"> <i class="asterisk icon"></i> </div>
						</div>
					</div>
					<div class="field">
						<label>學號</label>
						<input name="studentID" placeholder="請輸入學號" type="text" tabindex="4">
					</div>
				</div>
				<div class="two fields">
					<div class="four wide field">
						<label>身份證字號</label>
						<div class="ui labeled input">
							<input name="idnum" placeholder="首位大寫，A123456789" type="text" tabindex="5">
							<div class="ui corner label"> <i class="asterisk icon"></i> </div>
						</div>
					</div>
					<div class="twelve wide field">
						<label>E-mail</label>
						<div class="ui labeled input">
							<input name="email" placeholder="請輸入email" type="text" tabindex="6">
							<div class="ui corner label"> <i class="asterisk icon"></i> </div>
						</div>
					</div>
				</div>
				<div class="two fields">
					<div class="four wide field">
						<label>手機</label>
						<input name="cellphone" placeholder="0912345678" type="text" tabindex="7">
					</div>
					<div class="twelve wide field">
						<label>地址</label>
						<div class="ui labeled input">
							<input name="address" placeholder="你家住哪啊?" type="text" tabindex="8">
							<div class="ui corner label"> <i class="asterisk icon"></i> </div>
						</div>
					</div>
				</div>
				<div class="four fields">
					<div class="field">
						<label>家裡電話</label>
						<div class="ui labeled input">
							<input name="telephone" placeholder="03-5712121" type="text" tabindex="9">
							<div class="ui corner label"> <i class="asterisk icon"></i> </div>
						</div>
					</div>
					<div class="field">
						<label>緊急聯絡人</label>
						<div class="ui labeled input">
							<input name="emergency_cont" placeholder="" type="text" tabindex="10">
							<div class="ui corner label"> <i class="asterisk icon"></i> </div>
						</div>
					</div>
					<div class="field">
						<label>關係</label>
						<div class="ui labeled input">
							<input name="relation" placeholder="" type="text" tabindex="11">
							<div class="ui corner label"> <i class="asterisk icon"></i> </div>
						</div>
					</div>
					<div class="field">
						<label>緊急聯絡人電話</label>
						<div class="ui labeled input">
							<input name="emergency_tel" placeholder="0912345678" type="text" tabindex="12">
							<div class="ui corner label"> <i class="asterisk icon"></i> </div>
						</div>
					</div>
				</div>
				<div class="four fields">
					<div class="field">
						<label>血型(特殊血請註明)</label>
						<div class="ui labeled input">
							<input name="bloodtype" placeholder="A/B/AB/O/其它" type="text" tabindex="13">
							<div class="ui corner label"> <i class="asterisk icon"></i> </div>
						</div>
					</div>
					<div class="field">
						<div class="two fields">
							<div class="field">
								<label>飲食習慣</label>
								<div class="ui radio checkbox">
									<input name="food" id="meat" value="meat" type="radio" tabindex="14">
									<label for="meat">肉食性</label>
								</div>
							</div>
							<div class="field">
								<label>&nbsp;</label>
								<div class="ui radio checkbox">
									<input name="food" id="veg" value="veg" type="radio" tabindex="15">
									<label for="veg">草食性</label>
								</div>
							</div>
						</div>
					</div>
					<div class="field">
						<label>畢業高中</label>
						<input name="graduation" placeholder="ex：師大附中/台中女中" type="text" tabindex="16">
					</div>
					<div class="field">
						<label>營服尺寸</label>
						<div class="ui dropdown labeled selection" tabindex="17">
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
							<div class="ui corner label"> <i class="asterisk icon"></i> </div>
						</div>
					</div>
				</div>
				<div class="field">
					<label>特殊疾病</label>
					<input type="text" placeholder="看到總召就會想捏他的病…" name="disease" tabindex="18">
				</div>
				<div class="field">
					<label>為什麼想來迎新宿營咧?</label>
					<textarea id="reason_ta" name="reason" placeholder="因為聽說值星很帥呢A_A" tabindex="20"></textarea>
				</div>
				<div class="field">
					<label>對大學的期待</label>
					<textarea id="expectation_ta" name="expectation" placeholder="Write your expectation..." tabindex="21"></textarea>
				</div>
			</div>
		</form>
		<div id="button_div">
			<div class="ui down button" onclick="account.submit();" tabindex="22" >
				<i class="checkmark sign icon"></i>
				送出
			</div>
			<div class="ui down button" onclick="clearForm();" tabindex="23">
				<i class="trash icon"></i>
				清除
			</div>
		</div>
	</div>

<script>

</script>
</body>
</html>
