$(document).ready(function(){
	document.getElementById('form-email').onsubmit = function(){
		return checkEmailForm();
	};
	
	document.getElementById('form-password').onsubmit = function(){
		return checkPasswordForm();
	};
});

function checkEmailForm(){
	var new_email = $("#new_email").val();
	var confirm_new_email = $("#confirm_new_email").val();

	if(new_email != confirm_new_email){
		$("#new_email").addClass("is-invalid");
		$("#confirm_new_email").addClass("is-invalid");
		setTimeout(function(){
			alert("Emails does not match");
		},500);
		return false;
	}
}

function checkPasswordForm(){
	var new_password = $("#new_password").val();
	var confirm_new_password = $("#confirm_new_password").val();

	if(new_password != confirm_new_password){
		$("#new_password").addClass("is-invalid");
		$("#confirm_new_password").addClass("is-invalid");
		setTimeout(function(){
			alert("Passwords does not match")
		},500);
		return false;
	}
}
