jQuery(
	function($){
		//functions for creating new inputs on the fly with the purpose of adding dinamically options for questions
		var field_id = 1; 
        ft_evento = function (evt) { 
           return (!evt) ? event : evt;
        }
        
        addField = function (value='') { 
           ++field_id;	
           nDiv = document.createElement('div');
           nDiv.className = 'created_dinamically';
           nDiv.id = 'ft_options_div' + (field_id); 
           nCampo = document.createElement('input'); 
           nCampo.name = 'ft_options[]';
           nCampo.id = 'ft_options_' + (field_id);   
           nCampo.type = 'input';
           nCampo.className ='new_input';
           nCampo.value = value;
           a = document.createElement('a');
           a.name = nDiv.id;
           a.onclick = delField;
           a.className = 'eliminar_fichero';
           a.innerHTML = 'Click to delete choice';
           nDiv.appendChild(nCampo);
           nDiv.appendChild(a);
           container = document.getElementById('choices');
           container.appendChild(nDiv);
        }
        
        delField = function (evt){
           evt = ft_evento(evt);
           nCampo = rObj(evt);
           div = document.getElementById(nCampo.name);
           div.parentNode.removeChild(div);
        }
        
        rObj = function (evt) { 
           return evt.srcElement ?  evt.srcElement : evt.target;
        }
	
	
	  $('body').on('click', '.ft_delete_applicant', function(e){	
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
		
		//Click on button for acept applicants, should send an email 
		$('body').on('click', '.accept_user', function(e){
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
		
		$('body').on('click', '.reject_user', function(e){
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
		
		$('body').on('click', '.eval_test', function(e){
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
				alert("Please all the fields on evaluation form are required");
			}   
		});
		
		$( '#ft_question_type' ).change( function () {
			
			var value = $( '#ft_question_type' ).val();
			
			$( '#range_max_min' ).hide();
			$( '#choices' ).hide();
			if ( value == 'number' ) {
				$( '#range_max_min' ).show();
			}
			
			if( value == 'radiobtn' || value == 'checkbox' ) {
				$( '#choices' ).show();
			}
		} )
		
		$( '.ft_insert_question' ).click( function( e ) {
			e.preventDefault();
			var security = $( '.ft_insert_question' ).data( 'insert_question_nonce' );
			var id = $('#ft_question_id').val();
			var data = new FormData( document.getElementById( "formquestion" ) );
			data.append( 'action', 'ft_admin_insert_question' );
			data.append( 'security',security );
			data.append( 'question_id',id );
			
			//validating the form, 
			if( question_form_valid() ) {  
				$.ajax ( {
					url: ajax_object.ajax_url+'?action=ft_admin_insert_question',
					type: "post",
					dataType: "json",
					data: data,
					contentType: false,
					processData: false,
					cache:false,
					success : function ( response ) {
						if ( response.type == 'ok' ) {
							alert(response.message );
							$( "#ft-question-list" ).html( response.list );
							clearQuestionForm();
						} else {
						 	alert(response.message);
						}
					}
				});
			} else {
				alert("Please, all fields on this form are required");
			}
		});
		
		question_form_valid = function () {
			if( $( '#ft_question_type' ).val() == '-1' ) {
				return false;
			}

			$('.new_input').each( function (index) {
				if( ( $( '#ft_question_type' ).val() == 'radiobtn' || $( '#ft_question_type' ).val() == 'checkbox' ) && this.val == '' ) {
					return false;
				}
			} );
			
			if( $( '#ft_insert_question_content' ).val() == '' ) {
				return false;
			}
			
			return true;
		}
		
		$( 'body' ).on( 'click', '.delete_question', function( e ) {
			e.preventDefault();
			var id = $( this ).data( 'question_id' );
			var security = $( this ).data( 'delete_question_nonce' );
			
			var data = new FormData();
			data.append( 'action', 'ft_admin_delete_question' );
			data.append( 'security',security );
			data.append( 'id',id );
			
			$.ajax( {
				url: ajax_object.ajax_url+'?action=ft_admin_delete_question',
				type: "post",
				dataType: "json",
				data: data,
				contentType: false,
				processData: false,
				cache:false,
				success : function( response ) {
					if ( response.type == 'ok' ) {
						alert( response.message );
						$( "#ft-question-list" ).html( response.list );
					}
				}
			} );
		} );
		
		clearQuestionForm = function () {
			$( '#ft_insert_question_content' ).val( '' );
			$( '#ft_question_id' ).val( '0' );
			$( '#ft_required' ).attr( "checked", false );
			$( '#ft_question_type' ).val( '-1' );
			
			$( '.created_dinamically' ).remove();
			$( '#range_max_min' ).hide();
			$( '#choices' ).hide();
			$( '#ft_options_1' ).val( '' );
			
		}
		
		$( 'body' ).on( 'load', function( e ) {
			clearQuestionForm();
		} );
		
		$( 'body' ).on( 'click', '.edit_question', function( e ) {
			e.preventDefault();
			var id = $(this).data('question_id');
			
			var data = new FormData();
			data.append('action', 'ft_admin_get_question_by_id');
			data.append('id',id);
			
			$.ajax( {
				url: ajax_object.ajax_url+'?action=ft_admin_get_question_by_id',
				type: "post",
				dataType: "json",
				data: data,
				contentType: false,
				processData: false,
				cache:false,
				success : function( response ) {
					if ( response.type == 'ok' ) {
						var question = response.question;
						$( '#range_max_min' ).hide();
						$( '#choices').hide();
						$( '#ft_insert_question_content' ).val( question.question_text );
						
						if( question.required == 'yes' ) {
							$( '#ft_required' ).attr( "checked", true );
						} else {
							$( '#ft_required' ).attr( "checked", false );
						}
						
						$( '#ft_question_id' ).val( question.id );
						
						$( '#ft_question_type' ).val( question.type );
						
						if( question.type == 'checkbox' || question.type == 'radiobutton' ) {
							$( '#choices' ).show();
							
							var options = question.options.split( '|' );
							$( '.created_dinamically' ).remove();
							field_id = 1; 
							for( var j=1; j<options.length; j++ ) {
								addField();
							}
							for( var i=1; i<= options.length; i++ ) {
								$( '#ft_options_'+i ).val( options[i-1] );
							}
						}
						
						if(question.type == 'number') {
							$('#range_max_min').show();
							
							$('.created_dinamically').remove();
								
							$('#ft_range_min').val( question.range_min );
							$('#ft_range_max').val( question.range_max );
						}
					}
				}
			} );
		});
	}
);
