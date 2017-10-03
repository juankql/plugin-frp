<div class="spinner_loading"></div>
<div class="row" id="registration_form">
	<div class="row formulario">
		<div class="col" id="step1">
			<form id="form_register" name="form_register" method="post" enctype="multipart/form-data">
				<div class="row">
					<div class="span"><span>Hello, help us to know you better. Answer the following questions.</span></div>
				</div>
				<div class="row">
					<div class="span"><span>1)First Name</span></div>
					<input defaultvalue="<?php echo $current_user->display_name;?>" class="form_component" id="name" name="name" placeholder="" type="text" value="<?php echo $current_user->display_name;?>" >
				</div>
				<div class="row">
					<div class="span"><span>2) Email</span></div>
					<input disabled defaultvalue="<?php echo $current_user->user_email;?>" class="form_component" id="email" name="email" placeholder="E-Mail" type="email" value="<?php echo $current_user->user_email;?>" >
				</div>
				<?php
					$question_counter = 2;
					foreach( $questions as $question ) {
						$question_counter++;
							
				?>
				<div class="row">
					<div class="span"><span><?php echo $question_counter.')'.$question->question_text; ?></span></div>
					<?php
						echo $this->input_for_question($question);
					?>
				</div>
				<?php } ?>
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