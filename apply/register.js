/* <![CDATA[ */
window.onload = function() {
}
function clearForm() {
	var elem = document.getElementById('registerForm').elements;
	for(i=0; i<elem.length; ++i)
		if( elem[i].type=="text" || elem[i].type=="password" ) elem[i].value="";
}
/* ]]> */
