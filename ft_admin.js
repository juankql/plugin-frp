jQuery(
	function($){
	  $('.ft_delete_applicant').click( function(e){	
			e.preventDefault();
			var id = $(this).data('applicant_id');
			var security = $(this).data('delete_nonce'); 
			
			var data = new FormData();
			data.append('action', 'ft_admin_delete_applicant');
			data.append('security',security);
			data.append('id',id);     
			
			$.ajax({
				url: ajax_object.ajax_url+'?action=ft_admin_delete_applicant',
				type: "post",
				dataType: "json",
				data: data,
				contentType: false,
				processData: false,
				cache:false,
				success : function( response ) {
					if (response.type == 'ok') {
						alert(response.message);
						$("#ft-applicant-list").html(response.list);
					} 
				}
			});
		});
		
		//Vlick on button for acept applicants, should send an email 
		$('.accept_user').click(function(e){
			e.preventDefault();
			var id = $(this).data('applicant_id');
			var security = $(this).data('delete_nonce');
			var data = new FormData();
			data.append('action', 'ft_admin_accept_applicant');
			data.append('security',security);
			data.append('id',id); 
			
			$.ajax({
				url: ajax_object.ajax_url+'?action=ft_admin_accept_applicant',
				type: "post",
				dataType: "json",
				data: data,
				contentType: false,
				processData: false,
				cache:false,
				success : function( response ) {
					if (response.type == 'ok') {
						alert(response.message);
						$("#ft-applicant-list").html(response.list);
					} 
				}
			});   
		});	
		
		$('.reject_user').click(function(e){
			e.preventDefault();
			var id = $(this).data('applicant_id');
			var security = $(this).data('delete_nonce');
			var data = new FormData();
			data.append('action', 'ft_admin_denied_applicant');
			data.append('security',security);
			data.append('id',id); 
			
			$.ajax({
				url: ajax_object.ajax_url+'?action=ft_admin_denied_applicant',
				type: "post",
				dataType: "json",
				data: data,
				contentType: false,
				processData: false,
				cache:false,
				success : function( response ) {
					if (response.type == 'ok') {
						alert(response.message);
						$("#ft-applicant-list").html(response.list);
					} 
				}
			});   
		});	
		
		$('.eval_test').click(function(e){
			e.preventDefault();
			if($(this).siblings('.eval_input').val() !== '' && $(this).siblings('.eval_comments').val() !== '') {
				var id = $(this).data('applicant_id');
				var security = $(this).data('delete_nonce');
				var data = new FormData();
				data.append('action', 'ft_admin_eval_test');
				data.append('security',security);
				data.append('id',id);
				data.append('evaluation',$(this).siblings('.eval_input').val()); 
				data.append('evaluation_comments',$(this).siblings('.eval_comments').val()); 
				$.ajax({
					url: ajax_object.ajax_url+'?action=ft_admin_eval_test',
					type: "post",
					dataType: "json",
					data: data,
					contentType: false,
					processData: false,
					cache:false,
					success : function( response ) {
						if (response.type == 'ok') {
							alert(response.message);
							$("#ft-applicant-list").html(response.list);
						} 
					}
				});
			} else {
				alert("Please ensure that all fields are filled.");
			}   
		});
		
	}
);
