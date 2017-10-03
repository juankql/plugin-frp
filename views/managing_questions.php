<div class="wrap">
	<h2 style="text-align:center;padding-bottom:15px;">Questions Manager</h2>
	<div style="">
		<form id="formquestion" name="formquestion" method="post" enctype="multipart/form-data">
			<input type="hidden" name="ft_question_id" id="ft_question_id" value="0">
			<div style="float:left;clear:left;text-align:right;padding-top:5px;">
				<label for="ft_insert_question_content" style="padding-right:15px"><b>Content for the new question</b></label><br/>
			</div>
			
			<div style="float:left;clear:both;text-align:right;padding-top:5px;width:100%;">
				<?php wp_editor( '', 'ft_insert_question_content', array( 'editor_height' => '150px;' ) ); ?>
			</div>
			
			<div style="float:left;clear:left;text-align:left;padding-top:5px;width:100%">
				<label for="ft_required" style="padding-right:15px"><b>This question is required</b></label>
				<input type="checkbox" name="ft_required" id="ft_required" value="yes" >
		</div>
			
			<div style="float:left;clear:left;text-align:left;padding-top:5px;width:100%">
				<label for="ft_question_type" style="padding-right:15px"><b>Answer type</b></label>
				<select name="ft_question_type" id="ft_question_type">
					<option value="-1">Answer type</option>
					<option value="number">Number</option>
					<option value="text">Text</option>
					<option value="longtext">Long text</option>
					<option value="url">Web url</option>
					<option value="email">Email direction</option>
					<option value="file">File upload</option>
					<option value="radiobtn">Select one choice (RadioButton)</option>
					<option value="checkbox">Select multiple choices (CheckBox)</option>
				</select>
			</div>
			
			<div class="hidden" id="range_max_min" style="float:left;clear:left;text-align:left;padding-top:5px;width:100%">
				<label for="ft_range_min" style="padding-right:15px"><b>Range Min Value</b></label><input type="number" name="ft_range_min" id="ft_range_min">
				<label for="ft_range_max" style="padding-right:15px"><b>Range Max Value</b></label><input type="number" name="ft_range_max" id="ft_range_max">
			</div>
			
			<div class="hidden" id="choices" style="float:left;clear:left;text-align:left;padding-top:5px;width:100%">
				<span>
					<b>Especify the choices</b><br/>
				</span>
				<input type="text" name="ft_options[]" id="ft_options_1"><br/>
				<span>
					<a onClick="addField()" style="font-size:14px;padding-bottom:20px !important;"><span class="dashicons dashicons-plus"></span> Click to add new choice</a>
				</span>
			</div>
			
			<div style="float:left;clear:left;text-align:center;margin-top:25px;">
				<?php echo '<button type="button" class="button-primary ft_insert_question" data-insert_question_nonce="'.wp_create_nonce('insert_question').'" title="Create or save the question">Save data</button>';?>
			</div>
		</form>
	</div>
	<div style="float:left;clear:left;text-align:center;margin-top:25px;">
		<hr>
		<h3>Questions list</h3>
		<table class="wp-list-table widefat fixed striped">
			<thead>
				<tr>
					<th scope="col" class="manage-column column-primary" style="width:5% !important;">#</th>
					<th scope="col" class="manage-column column-primary" style="width:40% !important;">Text</th>
					<th scope="col" class="manage-column">Required ?</th>
					<th scope="col" class="manage-column">Type</th>
					<th scope="col" class="manage-column">Range Min</th>
					<th scope="col" class="manage-column">Range Max</th>
					<th scope="col" class="manage-column">Options</th>
					<th scope="col" class="manage-column">Actions</th>
				</tr>
			</thead>
			<tbody id="ft-question-list">
				<?php
					echo $this->ft_populate_table_questions();
				?>
			</tbody>
			<tfoot>
				<tr>
					<th scope="col" class="manage-column column-primary" style="width:5% !important;">#</th>
					<th scope="col" class="manage-column column-primary" style="width:40% !important;">Text</th>
					<th scope="col" class="manage-column">Required ?</th>
					<th scope="col" class="manage-column">Type</th>
					<th scope="col" class="manage-column">Range Min</th>
					<th scope="col" class="manage-column">Range Max</th>
					<th scope="col" class="manage-column">Options</th>
					<th scope="col" class="manage-column">Actions</th>
				</tr>
			</tfoot>
		</table>
	</div>
</div>