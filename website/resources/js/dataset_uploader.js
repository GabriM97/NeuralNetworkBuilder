$(document).ready(function () {
	storeDatasetUrl = document.referrer;	

	$("#dataset_file").change(function(){
		var filename = $('#dataset_file')[0].files[0].name;
		$('label[for="dataset_file"]').html(filename);
	});

	var pbar = $('#progressBar'),
		currentProgress = 0;

	function trackUploadProgress(e) {
		if (e.lengthComputable) {
			currentProgress = (e.loaded / e.total) * 100; // Amount uploaded in percent
			$(pbar).width(currentProgress + '%');
			
			//if (currentProgress == 100)	console.log('Progress : 100%');
		}
	}

	function uploadFile() {
		var formdata = new FormData($('#main-form')[0]);
		$.ajax({
			url: storeDatasetUrl,
			type: 'post',
			data: formdata,
			xhr: function () {
				// Custom XMLHttpRequest
				var appXhr = $.ajaxSettings.xhr();

				// Check if upload property exists, if "yes" then upload progress can be tracked otherwise "not"
				if (appXhr.upload) {
					// Attach a function to handle the progress of the upload
					appXhr.upload.addEventListener('progress', trackUploadProgress, false);
				}
				return appXhr;
			},
			success: function (data) {
				window.location.replace(data);
			},
			error: function (reject) {
				$('#progressBar').addClass("d-none");
				$("#upload-button").removeClass("disabled").attr("disabled", false);
				var response = $.parseJSON(reject.responseText);
                if( reject.status === 422 ) {
					$("span.invalid-feedback").remove();
                    $.each(response.errors, function (key, val) {
						$("#" + key).addClass("is-invalid")
                        $("#" + key).after('<span class="invalid-feedback" role="alert"><strong>' + val + '</strong></span>');
                    });
                }else{
					$("h2").after('<span class="invalid-feedback" role="alert"><strong>' + response.message + '</strong></span>');
				}
            },

			// Tell jQuery "Hey! don't worry about content-type and don't process the data"
			// These two settings are essential for the application
			contentType: false,
			processData: false
		})
	}

	$("#main-form").submit(function (e) {
		e.preventDefault();
		$(pbar).width(0).removeClass('d-none').addClass('active');
		$("#upload-button").addClass("disabled").attr("disabled", true);
		uploadFile();
	});
});
