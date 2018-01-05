function validateForm() {
	var a = document.forms["Form"]["searchText"].value;

	if (a == null || a == "") {
		alert("Please Enter search text");
		return false;
	}
}
