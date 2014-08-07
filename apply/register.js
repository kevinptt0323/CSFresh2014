/* <![CDATA[ */
window.onload = function() {
	var year = (new Date()).getFullYear();
	var elem = document.getElementById('graduate_year');
	for(i=2000; i<2030; i++) {
		if( i == year+3 ) 
			elem.options.add(new Option(i,i,true));
		else
			elem.options.add(new Option(i,i));
	}
}
function clearForm() {
	var elem = document.getElementById('registerForm').elements;
	for(i=0; i<elem.length; ++i)
		if( elem[i].type=="text" || elem[i].type=="password" ) elem[i].value="";
}
/* ]]> */
