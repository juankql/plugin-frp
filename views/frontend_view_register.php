<div class="spinner_loading"></div>
<div class="row" id="registration_form">
	<div class="row formulario">
		<div class="col" id="step1"> 
			<form id="form_register" name="form_register" method="post" enctype="multipart/form-data">
				<div class="row">
					<div class="span"><span>Hello. Please answer the following questions to help us get to know you.</span></div>
				</div>
				<div class="row">
					<div class="span"><span>1) First Name</span></div>
					<input defaultvalue="<?php echo esc_attr($current_user->display_name);?>" class="form_component" id="name" name="name" placeholder="" type="text" value="<?php echo esc_attr($current_user->display_name);?>" >
				</div>
				<div class="row">
					<div class="span"><span>2) Email</span></div>
					<input disabled defaultvalue="<?php echo esc_attr($current_user->user_email);?>" class="form_component" id="email" name="email" placeholder="E-Mail" type="email" value="<?php echo esc_attr($current_user->user_email);?>" >
				</div>
				<div class="row">
					<div class="span"><span>3) What are your long term goals? Why do you want to work for UpdraftPlus?  Anything that would support your application</span></div>
					<textarea name="long_term_goals" id="long_term_goals" rows="15" cols="1" class="form_component" ></textarea>
				</div>
				<div class="row">
					<div class="span"><span>4) What are you looking for? </span></div>
					<input type="radio" name="looking_for" id="long_project" value="A long-term full-time single employer / client" >
					<label for="long_project"><span>A long-term full-time single employer / client </span></label> <br/>
					<input type="radio" name="looking_for" id="short_project" value="Short-term project work" >
					<label for="short_project" ><span>Short-term project work</span></label> 
				</div>
				<div class="row">
					<div class="span"><span>5) How many years of development experience do you have in total? </span></div>
					<input defaultvalue="" class="form_component" id="total_experience" name="total_experience" value=""  >
				</div>
				<div class="row">
					<div class="span"><span>6) What is your relative level of PHP, JS, WordPress plugin development experience? (1 up to 10)</span></div>
					<div class="column col-md-6 text-md-right">PHP</div><div class="column col-md-2"><input defaultvalue="" class="form_component" id="php_level" name="php_level" type="number" value=""></div><div class="column col-md-4"></div>
					<div class="column col-md-6 text-md-right">Javascript</div><div class="column col-md-2"><input defaultvalue="" class="form_component" id="js_level" name="js_level" type="number" value=""></div><div class="column col-md-4"></div> 
					<div class="column col-md-6 text-md-right">Wordpress plugin</div><div class="column col-md-2"><input defaultvalue="" class="form_component" id="wp_level" name="wp_level" type="number" value=""></div><div class="column col-md-4"></div> 
				</div>
				<div class="row">
					<div class="span"><span>7) Have you created an open source plugin?  If so, please link to where we can see it (e.g. ideally wordpress.org or github). If you have done more than one, please send us your favourite</span></div>
					<input defaultvalue="" class="form_component" id="plugin_url" name="plugin_url" type="url" value="" >
				</div>
				<div class="row">
					<div class="span"><span>8) What long term rate would you consider working for, for a good project that's permanent and full time?</span></div>
					US$ <input defaultvalue="" class="form_component" style="width:50%;display:inline;" id="long_term_rate" name="long_term_rate" value="" > per year
				</div>
				<div class="row">
					<div class="span"><span>9) What is your availability to start?</span></div>
					<input defaultvalue="" class="form_component udc_freelance_datepicker" id="starting_date" name="starting_date" type="date" value="" >
				</div>
				<div class="row">
					<div class="span"><span>10) Link to your online CV or online profile (e.g. LinkedIn, GitHub, personal website)</span></div>
					<input defaultvalue="" class="form_component" id="online_cv_link" name="online_cv_link" type="text" value="">
				</div>
				<div class="row">
					<div class="span"><span>11) Do you have a CV/resume? If so, please upload it here</span></div>
					<input class="form_component" id="cv_file_upload" style="width:100%;" name="cv_file_upload" type="file" >   
				</div>
				<div class="row">
					<div class="span"><span>12) Will you be able to take a PHP and/or JS test within the next 7 days?  This will involve writing an application to a brief within 2½ hours to evaluate your code. </span></div>
					<input type="radio" name="test_available" id="test_available" value="Yes" >
					<label for="long_project"><span>Yes</span></label>
					<input type="radio" name="test_available" id="test_available" value="No" >
					<label for="short_project" ><span>No</span></label> 
				</div>
				<div class="row" style="padding-top:20px; text-align:center;"> 
					<button id="register_button" type="button" class="btn btn-primary" title="Save the form">
						Send
					</button> 
				</div>
			</form>
			<div id="message" class="ft_message"></div>
		</div>
	</div>
</div>
