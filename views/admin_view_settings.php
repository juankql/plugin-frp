<div class="wrap">
	<h2 style="text-align:center;padding-bottom:15px;">Freelance Test Plugin Configuration</h2>
	<form action="options.php" method="post" enctype="multipart/form-data">
	<?php
 		settings_fields('freelance_test-settings');
	?> 
		<table class="form-table">
			<tr>
				<th scope="row"><label for="ft_test_contact_email"><?php _e('Email address') ?></label></th>
				<td><input type="text" name="ft_test_contact_email" id="ft_test_contact_email" placeholder="Plugin admin email" value="<?php echo esc_attr(get_option('ft_test_contact_email')); ?>" style="width:100%;" ></td>
			</tr>
			<tr>
				<th scope="row">
					<label for="ft_test_accept_mail_content"><?php _e('Template for the acceptance mail') ?></label>
					<p style="padding-right:15px">You can use [freelance_name] for database stored freelance name substitution</p>  
				</th>
				<td>
					<textarea rows="5" cols="1" name="ft_test_accept_mail_content" id="ft_test_accept_mail_content" placeholder="" style="width:100%;" ><?php echo htmlspecialchars(get_option('ft_test_accept_mail_content'));?></textarea>
				</td>
			</tr>
			<tr>
				<th scope="row">
					<label for="ft_test_reject_mail_content"><?php _e('Template for the rejecting mail') ?></label>
					<p style="padding-right:15px">You can use [freelance_name] for database stored freelance name substitution</p>  
				</th>
				<td>
					<textarea rows="5" cols="1" name="ft_test_reject_mail_content" id="ft_test_reject_mail_content" placeholder="" style="width:100%;" ><?php echo htmlspecialchars(get_option('ft_test_reject_mail_content'));?></textarea>
				</td>
			</tr>
			<tr>
				<th scope="row">
					<label for="ft_test_file_url"><?php _e('Test instructions') ?></label>
				</th>
				<td>
					<textarea rows="5" cols="1" name="ft_test_file_url" id="ft_test_file_url" placeholder="" style="width:100%;" ><?php echo htmlspecialchars(get_option('ft_test_file_url'));?></textarea>
				</td>
			</tr>
		</table>
		<?php 
		do_settings_sections('freelance_test-settings'); 
		
		submit_button();?>
	</form>
</div>
