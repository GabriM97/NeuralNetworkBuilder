$(document).ready(function () {
	document.getElementById('main-form').onsubmit = function(){
		$("#upload-button").addClass("disabled").attr("disabled", true);
	};
});
