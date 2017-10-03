jQuery(
	function($){
		$body = $("body");

		$(document).on({
		    ajaxStart: function() { $body.addClass("loading");    },
		     ajaxStop: function() { $body.removeClass("loading"); }    
		});
		
		$('#step2').hide();

		$('#mail_check_btn').click( function() {
			var email =$('#email').val();
			if (email !="") { 
				$.ajax({
					url: ajax_object.ajax_url,
					type: "post",
					dataType: 'json',
					data: {'email' : email, 'action':'check_email', 'security':ajax_object.nonce_email},
					success : function( response ) {
						if (response.message == 'accepted') {
							$("#test_instructions_container").html(response.test_instructions);
							$("#message").html("");
							$("#start_time").html("Your test start at: "+response.start_time);
							$("#email_user").val(email);
							$('#step2').show();
							$("#send_file_form").show();
						} else {
							$("#test_instructions_container").html("");
							$("#message").html("Sorry, you have no enough priviledges to access the test instructions or had done the test already");
							$('#step2').hide();
						}
					}
				});
			} else {
				$("#message").html("Please enter your email to check if you have access to test instructions");
			}
		});

		$('#upload_btn').click( function(e){
			e.preventDefault();
			$('#formfile').submit();
		});

		$("#formfile").on("submit", function(e){
			e.preventDefault(); 
			if ($('#file_upload').val() != "") {
				var $imgFile    = $('#file_upload');
				var email_user = $('#email_user');
				var data = new FormData(document.getElementById("formfile"));

				data.append('action', 'freelance_upload_file');
				data.append('async-upload', $imgFile[0].files[0]);
				data.append('email_user', email_user.val());
				data.append('security',ajax_object.nonce_file_upload);

				$.ajax({
					url: ajax_object.ajax_url+'?action=freelance_upload_file',
					type: "post",
					dataType: "json",
					data: data,
					contentType: false,
					processData: false,
					cache:false,
					success : function( response ) {
						if(response.type == 'ok'){
							$("#end_time").html("Your test end at: "+response.end_time);
							$("#send_file_form").hide();
						} else {
							$("#step2_message").html(response.message);
							$("#send_file_form").show();
						}
						
						$('#file_upload').val('');
						$("#step2_message").html(response.message);
					}
				});
			} else {
				$("#step2_message").html("Please select the .zip file with your test answers.");
				return false;
			}
		});
		// For the register form.
		$('.udc_freelance_datepicker').datepicker({
            dateFormat: "yy-mm-dd",
            minDate: 1
        });
        
        $('#register_button').click( function(e){
			e.preventDefault();
			//var $imgFile    = $('#cv_file_upload');
			var data = new FormData(document.getElementById("form_register"));
            if ($("#form_register").valid()){
				data.append('action', 'freelance_register');
				//data.append('async-upload', $imgFile[0].files[0]);
				data.append('security',ajax_object.nonce_register);
				data.append('email',$('#email').val());

				$.ajax({
					url: ajax_object.ajax_url+'?action=freelance_register',
					type: "post",
					dataType: "json",
					data: data,
					contentType: false,
					processData: false,
					cache:false,
					success : function( response ) {
						$("#message").html(response.message);
						$('#cv_file_upload').val('');
					}
				});
			}
		});
		
		$("#form_register").validate({
        	rules: {
            	"name" :  {
                	required: true
                },
                "email" : {
                	required: true,
                	email: true
                },
                
            },
            errorPlacement: function(error, element) {
	            if ( element.is(":radio") ) 
	            {
	                error.appendTo( element.siblings('.span') );
	            }
	            else 
	            { // This is the default behavior 
	                error.insertAfter( element );
	            }
	         }	
        })
		
	}
);
