<div class="spinner_loading"></div>
<div class="row">
	<div class="row formulario">
		<div class="row" id="step1"> 
			<form id="form_mail" name="form_mail" method="post" enctype="multipart/form-data">
				<div class="row">
					<div class="span"><span>* Please enter your email address below in order to gain access to the test instructions:</span></div>
					<input disabled defaultvalue="<?php echo esc_attr($current_user->user_email);?>" class="form_component" id="email" name="email" placeholder="E-Mail" type="email" value="<?php echo $current_user->user_email;?>">
				</div>
				<div class="row" style="padding-top:20px; text-align:center;"> 
					<button id="mail_check_btn" type="button" class="btn btn-primary" title="Check the email">
						Access instructions
					</button> 
				</div>
			</form>
			<div id="message" class="ft_message"></div>
		</div>
		<div class="row" id="step2">
			<div class="row" style="text-align:justify;padding: 10px;">
				<p >Please read the test instructions below. You have 2.5 hours to complete your test. Once you have finished, pack your test in a .zip file and upload it using the form at the bottom of the page.</p>
				<hr>
				<h3 style="text-align:center;padding-top:0px;padding-bottom:0px;">Test instructions</h3> 
				<p id="test_instructions_container"></p>
				<hr>
			</div>
			<div class="row" style="text-align:right;padding: 10px;">
				<p id="start_time"></p>
				<p id="end_time"></p> 
			</div>
			<div id="send_file_form"> 
				<form id="formfile" name="formfile" method="post" enctype="multipart/form-data"> 
					<input type="hidden" name="email_user" id="email_user"> 
					<div>
						<div class="span"><span>* File Upload:</span></div>
						<div id="adjuntos">  
							<input class="form_component" id="file_upload" style="width:100%;" name="file_upload" type="file" >
						</div>
					</div>
				</form>   
			</div>
				<div class="row" style="padding-top:20px;text-align:center;">   
					<a class="btn btn-success enviar" href="#" id="upload_btn" style="width:20%;height:40px;font-size:16px;padding-top:8px;"><b>Upload</b></a>
				</div>
				<div id="step2_message" class="ft_message"></div> 
			</div>
		</div>
	</div>
</div>
