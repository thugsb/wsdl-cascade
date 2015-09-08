<?php
/********************************************************************************
 MachForm
  
 Copyright 2007-2015 Appnitro Software. This code cannot be redistributed without
 permission from http://www.appnitro.com/
 
 More info at: http://www.appnitro.com/
 ********************************************************************************/
	//Single Line Text
	function mf_display_text($element){
		global $mf_lang;

		//check for error
		$li_class = '';
		$li_style = '';
		$error_message = '';
		$span_required = '';
		$attr_readonly = '';
		$guidelines = '';
		$el_class = array();
		
		if($element->is_private == 1){
			$el_class[] = 'private';
		} 

		if($element->is_private == 2 && !$element->is_design_mode && !$element->is_edit_entry){
			$li_style = 'style="display: none"';
		}

		if(!empty($element->css_class)){
			$el_class[] = trim($element->css_class);
		}
		
		if(!empty($element->is_error)){
			$el_class[] = 'error';
			if($element->error_message != 'error_no_display'){
				$error_message = "<p class=\"error\">{$element->error_message}</p>";
			}
		}
		
		//check for required
		if($element->is_required){
			$span_required = "<span id=\"required_{$element->id}\" class=\"required\">*</span>";
		}

		//check for read-only attribute
		if($element->is_readonly){
			$attr_readonly = 'readonly="readonly"';
		}
		
		//check for guidelines
		if(!empty($element->guidelines)){
			$guidelines = "<p class=\"guidelines\" id=\"guide_{$element->id}\"><small>{$element->guidelines}</small></p>";
		}
		
		//check for constraint
		if($element->constraint == 'password'){
			$element_type = 'password';
		}else{
			$element_type = 'text';
		}

		//parse default value for some pre-defined variables
		if(!empty($element->default_value) && !$element->is_design_mode){
			$element->default_value = str_replace(array('{http_referer}','{request_uri}'), array($_SERVER['HTTP_REFERER'],$_SERVER['REQUEST_URI']), $element->default_value);
		}

		//check for placeholder
		$original_default_value = $element->default_value;
		
		//check for GET parameter to populate default value
		if(isset($_GET['element_'.$element->id])){
			$element->default_value = htmlspecialchars(mf_sanitize($_GET['element_'.$element->id]),ENT_QUOTES);
		}
		
		//check for populated value, if exist, use it instead default_value
		if(isset($element->populated_value['element_'.$element->id]['default_value'])){
			$element->default_value = $element->populated_value['element_'.$element->id]['default_value'];
		}
		
		//check for placeholder
		if( (!empty($element->enable_placeholder) && !empty($element->default_value) && ($element->default_value == $original_default_value)) ||
			(!empty($element->enable_placeholder) && empty($element->default_value) && !empty($original_default_value))
		 ){
			$attr_placeholder = 'placeholder="'.$original_default_value.'"';
			$element->default_value = '';
		}

		if($element->range_limit_by == 'c'){
			$range_limit_by = $mf_lang['range_type_chars'];
		}else if($element->range_limit_by == 'w'){
			$range_limit_by = $mf_lang['range_type_words'];
		}
		
		if(!empty($element->is_design_mode)){
			$range_limit_by = '<var class="range_limit_by">'.$range_limit_by.'</var>';
		}
		
		$input_handler = '';
		$maxlength = '';
		
		if(!empty($element->range_min) || !empty($element->range_max)){
			$currently_entered_length = 0;
			if(!empty($element->default_value)){
				if($element->range_limit_by == 'c'){
					if(function_exists('mb_strlen')){
						$currently_entered_length = mb_strlen($element->default_value);
					}else{
						$currently_entered_length = strlen($element->default_value);
					}
				}else if($element->range_limit_by == 'w'){
					$currently_entered_length = count(preg_split("/[\s\.]+/", $element->default_value, NULL, PREG_SPLIT_NO_EMPTY));
				}
			}
		}
		
		if(!empty($element->range_min) && !empty($element->range_max)){
			if($element->range_min == $element->range_max){
				$range_min_max_tag = sprintf($mf_lang['range_min_max_same'],"<var id=\"range_max_{$element->id}\">{$element->range_max}</var> {$range_limit_by}");
			}else{
				$range_min_max_tag = sprintf($mf_lang['range_min_max'],"<var id=\"range_min_{$element->id}\">{$element->range_min}</var>","<var id=\"range_max_{$element->id}\">{$element->range_max}</var> {$range_limit_by}");
			}

			$currently_entered_tag = sprintf($mf_lang['range_min_max_entered'],"<var id=\"currently_entered_{$element->id}\">{$currently_entered_length}</var> {$range_limit_by}");

			$range_limit_markup = "<span class=\"label\">{$range_min_max_tag}&nbsp;&nbsp; <em class=\"currently_entered\">{$currently_entered_tag}</em></span>";
			$input_handler = "onkeyup=\"limit_input({$element->id},'{$element->range_limit_by}',{$element->range_max});\" onchange=\"limit_input({$element->id},'{$element->range_limit_by}',{$element->range_max});\"";
			
			if($element->range_limit_by == 'c'){
				$maxlength = 'maxlength="'.$element->range_max.'"';
			}
		}elseif(!empty($element->range_max)){
			$range_max_tag = sprintf($mf_lang['range_max'],"<var id=\"range_max_{$element->id}\">{$element->range_max}</var> {$range_limit_by}");
			$currently_entered_tag = sprintf($mf_lang['range_max_entered'],"<var id=\"currently_entered_{$element->id}\">{$currently_entered_length}</var> {$range_limit_by}");

			$range_limit_markup = "<span class=\"label\">{$range_max_tag}&nbsp;&nbsp; <em class=\"currently_entered\">{$currently_entered_tag}</em></span>";
			$input_handler = "onkeyup=\"limit_input({$element->id},'{$element->range_limit_by}',{$element->range_max});\" onchange=\"limit_input({$element->id},'{$element->range_limit_by}',{$element->range_max});\"";
			
			if($element->range_limit_by == 'c'){
				$maxlength = 'maxlength="'.$element->range_max.'"';
			}
		}elseif(!empty($element->range_min)){
			$range_min_tag = sprintf($mf_lang['range_min'],"<var id=\"range_min_{$element->id}\">{$element->range_min}</var> {$range_limit_by}");
			$currently_entered_tag = sprintf($mf_lang['range_min_entered'],"<var id=\"currently_entered_{$element->id}\">{$currently_entered_length}</var> {$range_limit_by}");

			$range_limit_markup = "<span class=\"label\">{$range_min_tag}&nbsp;&nbsp; <em class=\"currently_entered\">{$currently_entered_tag}</em></span>";
			$input_handler = "onkeyup=\"count_input({$element->id},'{$element->range_limit_by}');\" onchange=\"count_input({$element->id},'{$element->range_limit_by}');\"";
		}else{
			$range_limit_markup = '';
		}
		
		if(!empty($element->is_design_mode)){
			$input_handler = '';
		}
		
		//if there is any error message unrelated with range rules, don't display the range markup
		if(!empty($error_message)){
			$range_limit_markup = '';
		}
		
		//build the li class
		if(!empty($el_class)){
			foreach ($el_class as $value){
				$li_class .= $value.' ';
			}
			
			$li_class = 'class="'.rtrim($li_class).'"';
		}

				
$element_markup = <<<EOT
		<li id="li_{$element->id}" {$li_style} {$li_class}>
		<label class="description" for="element_{$element->id}">{$element->title} {$span_required}</label>
		<div>
			<input id="element_{$element->id}" name="element_{$element->id}" {$attr_placeholder} {$attr_readonly} {$maxlength} class="element text {$element->size}" type="{$element_type}" value="{$element->default_value}"  {$input_handler} />
			{$range_limit_markup} 
		</div>{$guidelines} {$error_message}
		</li>
EOT;
		
		return $element_markup;
	}
	
	
	
	//Paragraph Text
	function mf_display_textarea($element){
		global $mf_lang;

		//check for error
		$li_class = '';
		$li_style = '';
		$error_message = '';
		$span_required = '';
		$attr_readonly = '';
		$guidelines = '';
		$el_class = array();
		
		if($element->is_private == 1){
			$el_class[] = 'private';
		}

		if($element->is_private == 2 && !$element->is_design_mode && !$element->is_edit_entry){
			$li_style = 'style="display: none"';
		}

		if(!empty($element->css_class)){
			$el_class[] = trim($element->css_class);
		}
		
		if(!empty($element->is_error)){
			$el_class[] = 'error';
			if($element->error_message != 'error_no_display'){
				$error_message = "<p class=\"error\">{$element->error_message}</p>";
			}
		}
		
		//check for required
		if($element->is_required){
			$span_required = "<span id=\"required_{$element->id}\" class=\"required\">*</span>";
		}

		//check for read-only attribute
		if($element->is_readonly){
			$attr_readonly = 'readonly="readonly"';
		}
		
		//check for guidelines
		if(!empty($element->guidelines)){
			$guidelines = "<p class=\"guidelines\" id=\"guide_{$element->id}\"><small>{$element->guidelines}</small></p>";
		}

		//parse default value for some pre-defined variables
		if(!empty($element->default_value) && !$element->is_design_mode){
			$element->default_value = str_replace(array('{http_referer}','{request_uri}'), array($_SERVER['HTTP_REFERER'],$_SERVER['REQUEST_URI']), $element->default_value);
		}
		
		//check for placeholder
		$original_default_value = $element->default_value;
		
		//check for GET parameter to populate default value
		if(isset($_GET['element_'.$element->id])){
			$element->default_value = htmlspecialchars(mf_sanitize($_GET['element_'.$element->id]),ENT_QUOTES);
		}

		//check for populated value, if exist, use it instead default_value
		if(isset($element->populated_value['element_'.$element->id]['default_value'])){
			$element->default_value = $element->populated_value['element_'.$element->id]['default_value'];
		}

		//check for placeholder
		if( (!empty($element->enable_placeholder) && !empty($element->default_value) && ($element->default_value == $original_default_value)) ||
			(!empty($element->enable_placeholder) && empty($element->default_value) && !empty($original_default_value))
		 ){
			$attr_placeholder = 'placeholder="'.$original_default_value.'"';
			$element->default_value = '';
		}
		
		if($element->range_limit_by == 'c'){
			$range_limit_by = $mf_lang['range_type_chars'];
		}else if($element->range_limit_by == 'w'){
			$range_limit_by = $mf_lang['range_type_words'];
		}
		
		if(!empty($element->is_design_mode)){
			$range_limit_by = '<var class="range_limit_by">'.$range_limit_by.'</var>';
		}
		
		$input_handler = '';
		
		if(!empty($element->range_min) || !empty($element->range_max)){
			$currently_entered_length = 0;
			if(!empty($element->default_value)){
				if($element->range_limit_by == 'c'){
					if(function_exists('mb_strlen')){
						$currently_entered_length = mb_strlen($element->default_value);
					}else{
						$currently_entered_length = strlen($element->default_value);
					}
				}else if($element->range_limit_by == 'w'){
					$currently_entered_length = count(preg_split("/[\s\.]+/", $element->default_value, NULL, PREG_SPLIT_NO_EMPTY));
				}
			}
		}
		
		if(!empty($element->range_min) && !empty($element->range_max)){
			if($element->range_min == $element->range_max){
				$range_min_max_tag = sprintf($mf_lang['range_min_max_same'],"<var id=\"range_max_{$element->id}\">{$element->range_max}</var> {$range_limit_by}");
			}else{
				$range_min_max_tag = sprintf($mf_lang['range_min_max'],"<var id=\"range_min_{$element->id}\">{$element->range_min}</var>","<var id=\"range_max_{$element->id}\">{$element->range_max}</var> {$range_limit_by}");
			}

			$currently_entered_tag = sprintf($mf_lang['range_min_max_entered'],"<var id=\"currently_entered_{$element->id}\">{$currently_entered_length}</var> {$range_limit_by}");

			$range_limit_markup = "<span class=\"label\">{$range_min_max_tag}&nbsp;&nbsp; <em class=\"currently_entered\">{$currently_entered_tag}</em></span>";
			$input_handler = "onkeyup=\"limit_input({$element->id},'{$element->range_limit_by}',{$element->range_max});\" onchange=\"limit_input({$element->id},'{$element->range_limit_by}',{$element->range_max});\"";
		}elseif(!empty($element->range_max)){
			$range_max_tag = sprintf($mf_lang['range_max'],"<var id=\"range_max_{$element->id}\">{$element->range_max}</var> {$range_limit_by}");
			$currently_entered_tag = sprintf($mf_lang['range_max_entered'],"<var id=\"currently_entered_{$element->id}\">{$currently_entered_length}</var> {$range_limit_by}");

			$range_limit_markup = "<span class=\"label\">{$range_max_tag}&nbsp;&nbsp; <em class=\"currently_entered\">{$currently_entered_tag}</em></span>";
			$input_handler = "onkeyup=\"limit_input({$element->id},'{$element->range_limit_by}',{$element->range_max});\" onchange=\"limit_input({$element->id},'{$element->range_limit_by}',{$element->range_max});\"";
		}elseif(!empty($element->range_min)){
			$range_min_tag = sprintf($mf_lang['range_min'],"<var id=\"range_min_{$element->id}\">{$element->range_min}</var> {$range_limit_by}");
			$currently_entered_tag = sprintf($mf_lang['range_min_entered'],"<var id=\"currently_entered_{$element->id}\">{$currently_entered_length}</var> {$range_limit_by}");

			$range_limit_markup = "<span class=\"label\">{$range_min_tag}&nbsp;&nbsp; <em class=\"currently_entered\">{$currently_entered_tag}</em></span>";
			$input_handler = "onkeyup=\"count_input({$element->id},'{$element->range_limit_by}');\" onchange=\"count_input({$element->id},'{$element->range_limit_by}');\"";
		}else{
			$range_limit_markup = '';
		}
		
		if(!empty($element->is_design_mode)){
			$input_handler = '';
		}
		
		//if there is any error message unrelated with range rules, don't display the range markup
		if(!empty($error_message)){
			$range_limit_markup = '';
		}
		
		//build the li class
		if(!empty($el_class)){
			foreach ($el_class as $value){
				$li_class .= $value.' ';
			}
			
			$li_class = 'class="'.rtrim($li_class).'"';
		}
		
$element_markup = <<<EOT
		<li id="li_{$element->id}" {$li_style} {$li_class}>
		<label class="description" for="element_{$element->id}">{$element->title} {$span_required}</label>
		<div>
			<textarea id="element_{$element->id}" name="element_{$element->id}" {$attr_placeholder} {$attr_readonly} class="element textarea {$element->size}" rows="8" cols="90" {$input_handler}>{$element->default_value}</textarea>
			{$range_limit_markup} 
		</div>{$guidelines} {$error_message}
		</li>
EOT;
		
		return $element_markup;
	}

	//Signature Field
	function mf_display_signature($element){
		global $mf_lang;

		//check for error
		$li_class = '';
		$li_style = '';
		$error_message = '';
		$span_required = '';
		$guidelines = '';
		$el_class = array();
		
		if($element->is_private == 1){
			$el_class[] = 'private';
		}

		if($element->is_private == 2 && !$element->is_design_mode && !$element->is_edit_entry){
			$li_style = 'style="display: none"';
		}

		if(!empty($element->css_class)){
			$el_class[] = trim($element->css_class);
		}
		
		if(!empty($element->is_error)){
			$el_class[] = 'error';
			if($element->error_message != 'error_no_display'){
				$error_message = "<p class=\"error\">{$element->error_message}</p>";
			}
		}
		
		//check for required
		if($element->is_required){
			$span_required = "<span id=\"required_{$element->id}\" class=\"required\">*</span>";
		}
		
		//check for guidelines
		if(!empty($element->guidelines)){
			$guidelines = "<p class=\"guidelines\" id=\"guide_{$element->id}\"><small>{$element->guidelines}</small></p>";
		}
		
		//check for populated value, if exist, use it instead default_value
		$signature_default_value = '[]';
		if(isset($element->populated_value['element_'.$element->id]['default_value']) && !empty($element->populated_value['element_'.$element->id]['default_value'])){
			$signature_default_value = $element->populated_value['element_'.$element->id]['default_value'];
		}
		
		//build the li class
		if(!empty($el_class)){
			foreach ($el_class as $value){
				$li_class .= $value.' ';
			}
			
			$li_class = 'class="'.rtrim($li_class).'"';
		}
		
		if(!empty($element->is_design_mode)){
			$signature_markup = "<div class=\"signature_pad {$element->size}\"><h6>Signature Pad</h6></div>";
		}else{
			if($element->size == 'small'){
				$canvas_height = 70;
				$line_margin_top = 50;
			}else if($element->size == 'medium'){
				$canvas_height = 130;
				$line_margin_top = 95;
			}else{
				$canvas_height = 260;
				$line_margin_top = 200;
			}

			$signature_markup = <<<EOT
	        <div class="mf_sig_wrapper {$element->size}">
	          <canvas class="mf_canvas_pad" width="309" height="{$canvas_height}"></canvas>
	          <input type="hidden" name="element_{$element->id}" id="element_{$element->id}">
	        </div>
	        <a class="mf_sigpad_clear element_{$element->id}_clear" href="#">Clear</a>
	        <script type="text/javascript">
				$(function(){
					var sigpad_options_{$element->id} = {
		               drawOnly : true,
		               displayOnly: false,
		               clear: '.element_{$element->id}_clear',
		               bgColour: '#fff',
		               penColour: '#000',
		               output: '#element_{$element->id}',
		               lineTop: {$line_margin_top},
		               lineMargin: 10,
		               validateFields: false
		        	};
		        	var sigpad_data_{$element->id} = {$signature_default_value};
		      		$('#mf_sigpad_{$element->id}').signaturePad(sigpad_options_{$element->id}).regenerate(sigpad_data_{$element->id});
				});
			</script>
EOT;
		}

$element_markup = <<<EOT
		<li id="li_{$element->id}" {$li_style} {$li_class}>
		<label class="description" for="element_{$element->id}">{$element->title} {$span_required}</label>
		<div id="mf_sigpad_{$element->id}">
			{$signature_markup} 
		</div>{$guidelines} {$error_message}
		</li>
EOT;
		
		return $element_markup;
	}
	
	
	//File Upload
	function mf_display_file($element){
		global $mf_lang;

		//check for error
		$li_class = '';
		$li_style = '';
		$error_message = '';
		$span_required = '';
		$guidelines = '';
		$el_class = array();
		
		if($element->is_private == 1){
			$el_class[] = 'private';
		}

		if($element->is_private == 2 && !$element->is_design_mode && !$element->is_edit_entry){
			$li_style = 'style="display: none"';
		}

		if(!empty($element->css_class)){
			$el_class[] = trim($element->css_class);
		}

		if(!empty($element->machform_path)){
			$machform_path = $element->machform_path;
		}
		
		if(!empty($element->is_error)){
			$el_class[] = 'error';
			if($element->error_message != 'error_no_display'){
				$error_message = "<p class=\"error\">{$element->error_message}</p>";
			}
		}
		
		//check for required
		if($element->is_required){
			$span_required = "<span id=\"required_{$element->id}\" class=\"required\">*</span>";
		}
		
		//check for guidelines
		if(!empty($element->guidelines)){
			$guidelines = "<p class=\"guidelines\" id=\"guide_{$element->id}\"><small>{$element->guidelines}</small></p>";
		}

		//check for populated value 
		if(!empty($element->populated_value['element_'.$element->id]['default_value'])){
			foreach ($element->populated_value['element_'.$element->id]['default_value'] as $data){
				$queue_id = "element_{$element->id}".substr(strtoupper(md5(mt_rand())),0,6);
				
				//trim filename if more than 20 characters
				if(strlen($data['filename']) > 20){
					$display_filename = substr($data['filename'],0,20)."...";
				}else{
					$display_filename = $data['filename'];
				}
				
				if($element->is_edit_entry){
					$db_live_status = 2;
				}else{
					$db_live_status = 1;
				}

				$queue_content .= <<<EOT
						<div class="uploadifyQueueItem completed" id="{$queue_id}">
						<div class="cancel">									
							<a href="javascript:remove_attachment('{$data['filename']}',{$element->form_id},{$element->id},'{$queue_id}',{$db_live_status},{$data['entry_id']});"><img border="0" src="{$machform_path}images/icons/delete.png"></a>
						</div>	
						<span class="fileName">
						  <img align="absmiddle" src="{$machform_path}images/icons/attach.gif" class="file_attached">{$display_filename} ({$data['filesize']})
						</span>
						</div>
EOT;
			
			}
			
			
		}
		
		
		
		if(!$element->is_design_mode && !empty($element->file_enable_advance)){
			
			if(!empty($element->populated_value['element_'.$element->id]['file_token'])){
				$file_token = $element->populated_value['element_'.$element->id]['file_token'];
				
				//check for existing listfile
				$listfile_name = $element->machform_data_path.$element->upload_dir."/form_{$element->form_id}/files/listfile_{$file_token}.php";
				if(file_exists($listfile_name)){
					$uploaded_files = file($listfile_name, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
					array_shift($uploaded_files);
					array_pop($uploaded_files);
					
					foreach($uploaded_files as $tmp_filename_path){
						$file_size = mf_format_bytes(filesize($tmp_filename_path));
						
						$tmp_filename_only =  basename($tmp_filename_path);
						$filename_value    =  substr($tmp_filename_only,strpos($tmp_filename_only,'-')+1);
						$filename_value    =  str_replace('.tmp', '', $filename_value);			
						$filename_value	   =  str_replace('|','',$filename_value);
						
						$queue_id = "element_{$element->id}".substr(strtoupper(md5(mt_rand())),0,6);
						
						//trim filename if more than 20 characters
						if(strlen($filename_value) > 20){
							$display_filename = substr($filename_value,0,20)."...";
						}else{
							$display_filename = $filename_value;
						}
				
						$queue_content .= <<<EOT
							<div class="uploadifyQueueItem completed" id="{$queue_id}">
							<div class="cancel">									
							<a href="javascript:remove_attachment('{$filename_value}',{$element->form_id},{$element->id},'{$queue_id}',0,'{$file_token}');"><img border="0" src="{$machform_path}images/icons/delete.png"></a>
						    </div>		
							<span class="fileName">
							  <img align="absmiddle" src="{$machform_path}images/icons/attach.gif" class="file_attached">{$display_filename} ({$file_size})
							</span>
							</div>
EOT;
					}
				}
				
				
				
			}else{
				$file_token = md5(uniqid(rand(), true)); 
			}
			
			//generate parameters for auto upload
			if(!empty($element->file_auto_upload)){
				$auto_upload = 'true';
			}else{
				$auto_upload = 'false';
				$upload_link_show_tag = "$(\"#element_{$element->id}_upload_link,#element_{$element->id}_upload_link_uploadifive\").show();";
				$upload_link_hide_tag = "$(\"#element_{$element->id}_upload_link,#element_{$element->id}_upload_link_uploadifive\").hide();";
			}
			
			//generate parameters for multi upload
			if(!empty($element->file_enable_multi_upload)){
				$multi_upload = 'true';
				$queue_limit  = $element->file_max_selection;
			}else{
				$multi_upload = 'false';
				$queue_limit  = 1;
			}
			
			//generate parameters for file size limit
			if(!empty($element->file_enable_size_limit)){
				if(!empty($element->file_size_max)){
					$file_size_max_bytes = 1048576 * $element->file_size_max;
					$size_limit 			= "'sizeLimit' : {$file_size_max_bytes},";
					$size_limit_uploadifive = "'fileSizeLimit'  : '{$element->file_size_max}MB',";
				}else{
					$size_limit = "'sizeLimit' : 10485760,"; //default 10MB
					$size_limit_uploadifive = "'fileSizeLimit'  : '0',";
				}
			}
			
			if(!empty($element->file_type_list) && !empty($element->file_enable_type_limit)){
				if($element->file_block_or_allow == 'a'){ //if this is an allow list
					$allowed_file_types = explode(',',$element->file_type_list);
					$file_type_limit_exts = implode(',',$allowed_file_types);

					array_walk($allowed_file_types,create_function('&$val','$val = "*.".strtolower(trim($val));'));
					$allowed_file_types_joined = implode(';',$allowed_file_types);
					
					$file_type_limit_allow = <<<EOT
					 'fileExt'     : '{$allowed_file_types_joined}',
  					 'fileDesc'    : '{$allowed_file_types_joined}',
EOT;
					
				}else if($element->file_block_or_allow == 'b'){ //if this is a block list
					$blocked_file_types = explode(',',$element->file_type_list);
					array_walk($blocked_file_types,create_function('&$val','$val = strtolower(trim($val));'));
					$blocked_file_types_joined = implode(',',$blocked_file_types);
					
					$file_type_limit_block = "'fileExtBlocked'  : '{$blocked_file_types_joined}',";
					$file_type_limit_exts  = $blocked_file_types_joined;
				}
			}
			
			$msg_queue_limited = sprintf($mf_lang['file_queue_limited'],$queue_limit);
			$msg_upload_max	   = sprintf($mf_lang['file_upload_max'],$element->file_size_max);
			$uploader_script = <<<EOT
<script type="text/javascript">
	$(function(){
		 if(is_support_html5_uploader()){
		 	$('#element_{$element->id}').uploadifive({
		 		'uploadScript'     : '{$machform_path}upload.php',
		 		'removeCompleted' : false,
				'formData'         : {
									  'form_id': {$element->form_id},
				        			  'element_id': {$element->id},
				        			  'file_token': '{$file_token}'
				                     },
				'auto'             : {$auto_upload},
				'multi'       	   : {$multi_upload},
				'queueSizeLimit' : {$queue_limit},
				{$size_limit_uploadifive}
				'queueID'          : 'element_{$element->id}_queue',
				'onAddQueueItem' : function(file) {
		            var file_block_or_allow  = '{$element->file_block_or_allow}';
		            var file_type_limit_exts = '{$file_type_limit_exts}';
		            var file_type_limit_exts_array = file_type_limit_exts.split(',');

		            var uploaded_file_ext 	 = file.name.split('.').pop().toLowerCase();
		            
		            var file_exist_in_array = false;
		            $.each(file_type_limit_exts_array,function(index,value){
		            	if(value == uploaded_file_ext){
		            		file_exist_in_array = true;
		            	}
		            });
					
		            if((file_block_or_allow == 'b' && file_exist_in_array == true) || (file_block_or_allow == 'a' && file_exist_in_array == false)){
		            	$("#" + file.queueItem.attr('id')).addClass('error');
			            $("#" + file.queueItem.attr('id') + ' span.fileinfo').text(" - {$mf_lang['file_type_limited']}");
		            }

		            {$upload_link_show_tag}
		            if($("html").hasClass("embed")){
				    	$.postMessage({mf_iframe_height: $('body').outerHeight(true)}, '*', parent );
				   	}
		        },
				'onUploadComplete' : function(file, response) { 
					{$upload_link_hide_tag}

					var is_valid_response = false;
					try{
						var response_json = jQuery.parseJSON(response);
						is_valid_response = true;
					}catch(e){
						is_valid_response = false;
						alert(response);
					}
					var queue_item_id =  file.queueItem.attr('id');
					
					if(is_valid_response == true && response_json.status == "ok"){
						var remove_link = "<a class=\"close\" href=\"javascript:remove_attachment('" + response_json.message + "',{$element->form_id},{$element->id},'" + queue_item_id + "',0,'{$file_token}');\"><img border=\"0\" src=\"{$machform_path}images/icons/delete.png\" /></a>";
						
						$("#" + queue_item_id + " a.close").replaceWith(remove_link);
				        $("#" + queue_item_id + ' span.filename').prepend('<img align="absmiddle" class="file_attached" src="{$machform_path}images/icons/attach.gif">'); 
			        }else{
			        	$("#" + queue_item_id).addClass('error');
			        	$("#" + queue_item_id + " a.close").replaceWith('<img style="float: right" border="0" src="{$machform_path}images/icons/exclamation.png" />');
						$("#" + queue_item_id + " span.fileinfo").text(" - {$mf_lang['file_error_upload']}");
					}

					if($("html").hasClass("embed")){
				    	$.postMessage({mf_iframe_height: $('body').outerHeight(true)}, '*', parent );
				   	} 

					if($("#form_{$element->form_id}").data('form_submitting') === true){
				       	upload_all_files();
					}
				}
			});
			$("#element_{$element->id}_upload_link").remove();
		 }else if($.browser.flash == true){
		      $('#element_{$element->id}').uploadify({
		        'uploader'   	  : '{$machform_path}js/uploadify/uploadify.swf',
		        'script'     	  : '{$machform_path}upload.php',
		        'cancelImg'  	  : '{$machform_path}images/icons/stop.png',
		        'removeCompleted' : false,
		        'displayData' 	  : 'percentage',
		        'scriptData'  : {
		        				 'form_id': {$element->form_id},
		        				 'element_id': {$element->id},
		        				 'file_token': '{$file_token}'
								},
				{$file_type_limit_allow}
				{$file_type_limit_block}
		        'auto'        : {$auto_upload},
		        'multi'       : {$multi_upload},
		        'queueSizeLimit' : {$queue_limit},
		        'onQueueFull'    : function (event,queueSizeLimit) {
				      alert('{$msg_queue_limited}');
				    },
		        'queueID'	  : 'element_{$element->id}_queue',
		        {$size_limit}
		        'buttonImg'   : '{$machform_path}images/upload_button.png',
		        'onError'     : function (event,ID,fileObj,errorObj) {
			      	if(errorObj.type == 'file_size_limited'){
			      		$("#element_{$element->id}" + ID + " span.percentage").text(' - {$msg_upload_max}');
					}else if(errorObj.type == 'file_type_blocked'){
						$("#element_{$element->id}" + ID + " span.percentage").text(" - {$mf_lang['file_type_limited']}");
					}
		        	{$upload_link_hide_tag}
			    },
		        'onSelectOnce' : function(event,data) {
				       {$upload_link_show_tag}
				       check_upload_queue({$element->id},{$multi_upload},{$queue_limit},'{$msg_queue_limited}');
				      
				       if($("html").hasClass("embed")){
				       		$.postMessage({mf_iframe_height: $('body').outerHeight(true)}, '*', parent );
				   	   }
				    },
				'onAllComplete' : function(event,data) {
				       $("#element_{$element->id}_upload_link").hide();

				       if($("html").hasClass("embed")){
				    		$.postMessage({mf_iframe_height: $('body').outerHeight(true)}, '*', parent );
				   	   }
				       
				       if($("#form_{$element->form_id}").data('form_submitting') === true){
				       		upload_all_files();
					   }
				    },
				'onComplete'  : function(event, ID, fileObj, response, data) {
					var is_valid_response = false;
					try{
						var response_json = jQuery.parseJSON(response);
						is_valid_response = true;
					}catch(e){
						is_valid_response = false;
						alert(response);
					}
					
					if(is_valid_response == true && response_json.status == "ok"){
						var remove_link = "<a href=\"javascript:remove_attachment('" + response_json.message + "',{$element->form_id},{$element->id},'element_{$element->id}" + ID + "',0,'{$file_token}');\"><img border=\"0\" src=\"{$machform_path}images/icons/delete.png\" /></a>";
						$("#element_{$element->id}" + ID + " > div.cancel > a").replaceWith(remove_link);
				        $("#element_{$element->id}" + ID + " > span.fileName").prepend('<img align="absmiddle" class="file_attached" src="{$machform_path}images/icons/attach.gif">'); 
			        }else{
			        	$("#element_{$element->id}" + ID).addClass('uploadifyError');
			        	$("#element_{$element->id}" + ID + " div.cancel > a ").replaceWith('<img border="0" src="{$machform_path}images/icons/exclamation.png" />');
						$("#element_{$element->id}" + ID + " span.percentage").text(" - {$mf_lang['file_error_upload']}");
					} 

					if($("html").hasClass("embed")){
				    	$.postMessage({mf_iframe_height: $('body').outerHeight(true)}, '*', parent );
				   	} 
			    }
		      });
			  $("#element_{$element->id}_upload_link_uploadifive").remove();
	     }else{
	     	$("#element_{$element->id}_token").remove();
		 }
    });
</script>
<input type="hidden" id="element_{$element->id}_token" name="element_{$element->id}_token" value="{$file_token}" />
<a id="element_{$element->id}_upload_link" style="display: none" href="javascript:$('#element_{$element->id}').uploadifyUpload();">{$mf_lang['file_attach']}</a>
<a id="element_{$element->id}_upload_link_uploadifive" style="display: none" href="javascript:$('#element_{$element->id}').uploadifive('upload');">{$mf_lang['file_attach']}</a>
EOT;
			$file_queue = "<div id=\"element_{$element->id}_queue\" class=\"file_queue\">{$queue_content}</div>";
		}
		
		if(!empty($queue_content)){
			$file_queue = "<div id=\"element_{$element->id}_queue\" class=\"file_queue uploadifyQueue\">{$queue_content}</div>";
		}
		
		//build the li class
		if(!empty($el_class)){
			foreach ($el_class as $value){
				$li_class .= $value.' ';
			}
			
			$li_class = 'class="'.rtrim($li_class).'"';
		}
		
		//disable the file upload field on design mode
		if($element->is_design_mode){
			$disabled_tag = 'disabled="disabled"';
		}

$element_markup = <<<EOT
		<li id="li_{$element->id}" {$li_style} {$li_class}>
		<label for="element_{$element->id}" class="description" for="element_{$element->id}">{$element->title} {$span_required}</label>
		<div>
			<input id="element_{$element->id}" name="element_{$element->id}" class="element file" type="file" {$disabled_tag} />
			{$file_queue} 
			{$uploader_script}
		</div>{$file_option} {$guidelines} {$error_message}
		</li>
EOT;
		
		return $element_markup;
	}
	
	//Website
	function mf_display_url($element){
		//check for error
		$li_class = '';
		$li_style = '';
		$error_message = '';
		$span_required = '';
		$attr_readonly = '';
		$guidelines = '';
		$el_class = array();
		
		if($element->is_private == 1){
			$el_class[] = 'private';
		}

		if($element->is_private == 2 && !$element->is_design_mode && !$element->is_edit_entry){
			$li_style = 'style="display: none"';
		}

		if(!empty($element->css_class)){
			$el_class[] = trim($element->css_class);
		}
		
		if(!empty($element->is_error)){
			$el_class[] = 'error';
			if($element->error_message != 'error_no_display'){
				$error_message = "<p class=\"error\">{$element->error_message}</p>";
			}
		}
		
		//check for required
		if($element->is_required){
			$span_required = "<span id=\"required_{$element->id}\" class=\"required\">*</span>";
		}

		//check for read-only attribute
		if($element->is_readonly){
			$attr_readonly = 'readonly="readonly"';
		}		
		
		//check for guidelines
		if(!empty($element->guidelines)){
			$guidelines = "<p class=\"guidelines\" id=\"guide_{$element->id}\"><small>{$element->guidelines}</small></p>";
		}
		
		//check for placeholder
		$original_default_value = $element->default_value;

		//check for default value
		if(empty($element->default_value)){
			$element->default_value = 'http://';
		}

		//check for GET parameter to populate default value
		if(isset($_GET['element_'.$element->id])){
			$element->default_value = htmlspecialchars(mf_sanitize($_GET['element_'.$element->id]),ENT_QUOTES);
		}
		
		//check for populated value, if exist, use it instead default_value
		if(!empty($element->populated_value['element_'.$element->id]['default_value'])){
			$element->default_value = $element->populated_value['element_'.$element->id]['default_value'];
		}

		//check for placeholder
		if( (!empty($element->enable_placeholder) && !empty($element->default_value) && ($element->default_value == $original_default_value)) ||
			(!empty($element->enable_placeholder) && empty($element->default_value) && !empty($original_default_value))
		 ){
			$attr_placeholder = 'placeholder="'.$original_default_value.'"';
			$element->default_value = '';
		}
		
		//build the li class
		if(!empty($el_class)){
			foreach ($el_class as $value){
				$li_class .= $value.' ';
			}
			
			$li_class = 'class="'.rtrim($li_class).'"';
		}
			
$element_markup = <<<EOT
		<li id="li_{$element->id}" {$li_style} {$li_class}>
		<label class="description" for="element_{$element->id}">{$element->title} {$span_required}</label>
		<div>
			<input id="element_{$element->id}" name="element_{$element->id}" {$attr_placeholder} {$attr_readonly} class="element text {$element->size}" type="text"  value="{$element->default_value}" /> 
		</div>{$guidelines} {$error_message}
		</li>
EOT;
		
		return $element_markup;
	}
	
	//Email
	function mf_display_email($element){
		//check for error
		$li_class = '';
		$li_style = '';
		$error_message = '';
		$span_required = '';
		$attr_readonly = '';
		$guidelines = '';
		$el_class = array();
		
		if($element->is_private == 1){
			$el_class[] = 'private';
		}

		if($element->is_private == 2 && !$element->is_design_mode && !$element->is_edit_entry){
			$li_style = 'style="display: none"';
		}

		if(!empty($element->css_class)){
			$el_class[] = trim($element->css_class);
		}
		
		if(!empty($element->is_error)){
			$el_class[] = 'error';
			if($element->error_message != 'error_no_display'){
				$error_message = "<p class=\"error\">{$element->error_message}</p>";
			}
		}
		
		//check for required
		if($element->is_required){
			$span_required = "<span id=\"required_{$element->id}\" class=\"required\">*</span>";
		}

		//check for read-only attribute
		if($element->is_readonly){
			$attr_readonly = 'readonly="readonly"';
		}
		
		//check for guidelines
		if(!empty($element->guidelines)){
			$guidelines = "<p class=\"guidelines\" id=\"guide_{$element->id}\"><small>{$element->guidelines}</small></p>";
		}
		
		//check for placeholder
		$original_default_value = $element->default_value;

		//check for GET parameter to populate default value
		if(isset($_GET['element_'.$element->id])){
			$element->default_value = htmlspecialchars(mf_sanitize($_GET['element_'.$element->id]),ENT_QUOTES);
		}

		//check for populated value, if exist, use it instead default_value
		if(!empty($element->populated_value['element_'.$element->id]['default_value'])){
			$element->default_value = $element->populated_value['element_'.$element->id]['default_value'];
		}

		//check for placeholder
		if( (!empty($element->enable_placeholder) && !empty($element->default_value) && ($element->default_value == $original_default_value)) ||
			(!empty($element->enable_placeholder) && empty($element->default_value) && !empty($original_default_value))
		 ){
			$attr_placeholder = 'placeholder="'.$original_default_value.'"';
			$element->default_value = '';
		}
		
		//build the li class
		if(!empty($el_class)){
			foreach ($el_class as $value){
				$li_class .= $value.' ';
			}
			
			$li_class = 'class="'.rtrim($li_class).'"';
		}
					
$element_markup = <<<EOT
		<li id="li_{$element->id}" {$li_style} {$li_class}>
		<label class="description" for="element_{$element->id}">{$element->title} {$span_required}</label>
		<div>
			<input id="element_{$element->id}" name="element_{$element->id}" {$attr_placeholder} {$attr_readonly} class="element text {$element->size}" type="text" maxlength="255" value="{$element->default_value}" /> 
		</div>{$guidelines} {$error_message}
		</li>
EOT;
		
		return $element_markup;
	}
	
	
	//Phone - Extended
	function mf_display_phone($element){
		//check for error
		$li_class = '';
		$li_style = '';
		$error_message = '';
		$span_required = '';
		$attr_readonly = '';
		$guidelines = '';
		$el_class = array('phone');
		
		if($element->is_private == 1){
			$el_class[] = 'private';
		}
		
		if($element->is_private == 2 && !$element->is_design_mode && !$element->is_edit_entry){
			$li_style = 'style="display: none"';
		}

		if(!empty($element->css_class)){
			$el_class[] = trim($element->css_class);
		}
		
		if(!empty($element->is_error)){
			$el_class[] = 'error';
			if($element->error_message != 'error_no_display'){
				$error_message = "<p class=\"error\">{$element->error_message}</p>";
			}
		}
		
		//check for required
		if($element->is_required){
			$span_required = "<span id=\"required_{$element->id}\" class=\"required\">*</span>";
		}

		//check for read-only attribute
		if($element->is_readonly){
			$attr_readonly = 'readonly="readonly"';
		}
		
		//check for guidelines
		if(!empty($element->guidelines)){
			$guidelines = "<p class=\"guidelines\" id=\"guide_{$element->id}\"><small>{$element->guidelines}</small></p>";
		}
		
		//check default value
		if(!empty($element->default_value)){
			//split into (xxx) xxx - xxxx
			$default_value_1 = substr($element->default_value,0,3);
			$default_value_2 = substr($element->default_value,3,3);
			$default_value_3 = substr($element->default_value,6,4);
		}

		//check for GET parameter to populate default value
		if(isset($_GET['element_'.$element->id.'_1'])){
			$default_value_1 = htmlspecialchars(mf_sanitize($_GET['element_'.$element->id.'_1']),ENT_QUOTES);
		}
		if(isset($_GET['element_'.$element->id.'_2'])){
			$default_value_2 = htmlspecialchars(mf_sanitize($_GET['element_'.$element->id.'_2']),ENT_QUOTES);
		}
		if(isset($_GET['element_'.$element->id.'_3'])){
			$default_value_3 = htmlspecialchars(mf_sanitize($_GET['element_'.$element->id.'_3']),ENT_QUOTES);
		}
		
		//check for populated values, if exist override the default value
		if(!empty($element->populated_value['element_'.$element->id.'_1']['default_value']) || 
		   !empty($element->populated_value['element_'.$element->id.'_2']['default_value']) ||
		   !empty($element->populated_value['element_'.$element->id.'_3']['default_value'])
		){
			$default_value_1 = '';
			$default_value_2 = '';
			$default_value_3 = '';
			$default_value_1 = $element->populated_value['element_'.$element->id.'_1']['default_value'];
			$default_value_2 = $element->populated_value['element_'.$element->id.'_2']['default_value'];
			$default_value_3 = $element->populated_value['element_'.$element->id.'_3']['default_value'];
		}
		
		//build the li class
		if(!empty($el_class)){
			foreach ($el_class as $value){
				$li_class .= $value.' ';
			}
			
			$li_class = 'class="'.rtrim($li_class).'"';
		}
		
$element_markup = <<<EOT
		<li id="li_{$element->id}" {$li_style} {$li_class}>
		<span class="description">{$element->title} {$span_required}</span>
		<span class="phone_1">
			<input id="element_{$element->id}_1" name="element_{$element->id}_1" {$attr_readonly} class="element text" size="3" maxlength="3" value="{$default_value_1}" type="text" /> -
			<label for="element_{$element->id}_1">###</label>
		</span>
		<span class="phone_2">
			<input id="element_{$element->id}_2" name="element_{$element->id}_2" {$attr_readonly} class="element text" size="3" maxlength="3" value="{$default_value_2}" type="text" /> -
			<label for="element_{$element->id}_2">###</label>
		</span>
		<span class="phone_3">
	 		<input id="element_{$element->id}_3" name="element_{$element->id}_3" {$attr_readonly} class="element text" size="4" maxlength="4" value="{$default_value_3}" type="text" />
			<label for="element_{$element->id}_3">####</label>
		</span>
		{$guidelines} {$error_message}
		</li>
EOT;
		

		return $element_markup;
	}
	
	//Phone - Simple
	function mf_display_simple_phone($element){
		//check for error
		$li_class = '';
		$li_style = '';
		$error_message = '';
		$span_required = '';
		$attr_readonly = '';
		$guidelines = '';
		$el_class = array();
		
		if($element->is_private == 1){
			$el_class[] = 'private';
		}
		
		if($element->is_private == 2 && !$element->is_design_mode && !$element->is_edit_entry){
			$li_style = 'style="display: none"';
		}

		if(!empty($element->css_class)){
			$el_class[] = trim($element->css_class);
		}
		
		if(!empty($element->is_error)){
			$el_class[] = 'error';
			if($element->error_message != 'error_no_display'){
				$error_message = "<p class=\"error\">{$element->error_message}</p>";
			}
		}
		
		//check for required
		if($element->is_required){
			$span_required = "<span id=\"required_{$element->id}\" class=\"required\">*</span>";
		}
		
		//check for read-only attribute
		if($element->is_readonly){
			$attr_readonly = 'readonly="readonly"';
		}

		//check for guidelines
		if(!empty($element->guidelines)){
			$guidelines = "<p class=\"guidelines\" id=\"guide_{$element->id}\"><small>{$element->guidelines}</small></p>";
		}
		
		//check for GET parameter to populate default value
		if(isset($_GET['element_'.$element->id])){
			$element->default_value = htmlspecialchars(mf_sanitize($_GET['element_'.$element->id]),ENT_QUOTES);
		}

		//check for populated value
		if(!empty($element->populated_value['element_'.$element->id]['default_value'])){
			$element->default_value = $element->populated_value['element_'.$element->id]['default_value'];
		}
		
		//build the li class
		if(!empty($el_class)){
			foreach ($el_class as $value){
				$li_class .= $value.' ';
			}
			
			$li_class = 'class="'.rtrim($li_class).'"';
		}
		
$element_markup = <<<EOT
		<li id="li_{$element->id}" {$li_style} {$li_class}>
		<label for="element_{$element->id}" class="description">{$element->title} {$span_required}</label>
		<div>
			<input id="element_{$element->id}" name="element_{$element->id}" {$attr_readonly} class="element text medium" type="text" maxlength="255" value="{$element->default_value}"/> 
		</div>{$guidelines} {$error_message}
		</li>
EOT;
		
		return $element_markup;
	}
	
	
	
	//Date - Normal
	function mf_display_date($element){
		
		global $mf_lang;
		
		$li_class = '';
		$li_style = '';
		$error_message = '';
		$span_required = '';
		$attr_readonly = '';
		$guidelines = '';
		$el_class = array('date_field');
		
		if($element->is_private == 1){
			$el_class[] = 'private';
		}
		
		if($element->is_private == 2 && !$element->is_design_mode && !$element->is_edit_entry){
			$li_style = 'style="display: none"';
		}

		if(!empty($element->css_class)){
			$el_class[] = trim($element->css_class);
		}
		
		if(!empty($element->is_error)){
			$el_class[] = 'error';
			if($element->error_message != 'error_no_display'){
				$error_message = "<p class=\"error\">{$element->error_message}</p>";
			}
		}
		
		//check for required
		if($element->is_required){
			$span_required = "<span id=\"required_{$element->id}\" class=\"required\">*</span>";
		}

		//check for read-only attribute
		if($element->is_readonly){
			$attr_readonly = 'readonly="readonly"';
		}
		
		//check for guidelines
		if(!empty($element->guidelines)){
			$guidelines = "<p class=\"guidelines\" id=\"guide_{$element->id}\"><small>{$element->guidelines}</small></p>";
		}
		
		//check for default value
		$cal_default_value = '';
		if(!empty($element->default_value)){
			//the default value can be mm/dd/yyyy or any valid english date words
			//we need to convert the default value into three parts (mm, dd, yyyy)
			$timestamp = strtotime($element->default_value);

			if(($timestamp !== false) && ($timestamp != -1)){
				$valid_default_date = date('m-d-Y', $timestamp);
				$valid_default_date = explode('-',$valid_default_date);
				
				$default_value_1 = (int) $valid_default_date[0];
				$default_value_2 = (int) $valid_default_date[1];
				$default_value_3 = (int) $valid_default_date[2];
			}else{ //it's not a valid date, display blank
				$default_value_1 = '';
				$default_value_2 = '';
				$default_value_3 = '';
			}
		}
		
		//check for GET parameter to populate default value
		if(isset($_GET['element_'.$element->id.'_1'])){
			$default_value_1 = (int) htmlspecialchars(mf_sanitize($_GET['element_'.$element->id.'_1']),ENT_QUOTES);
		}
		if(isset($_GET['element_'.$element->id.'_2'])){
			$default_value_2 = (int) htmlspecialchars(mf_sanitize($_GET['element_'.$element->id.'_2']),ENT_QUOTES);
		}
		if(isset($_GET['element_'.$element->id.'_3'])){
			$default_value_3 = (int) htmlspecialchars(mf_sanitize($_GET['element_'.$element->id.'_3']),ENT_QUOTES);
		}

		//if there's value submitted from the form, overwrite the default value
		if(!empty($element->populated_value['element_'.$element->id.'_1']['default_value']) || 
		   !empty($element->populated_value['element_'.$element->id.'_2']['default_value']) ||
		   !empty($element->populated_value['element_'.$element->id.'_3']['default_value'])
		){
			$default_value_1 = '';
			$default_value_2 = '';
			$default_value_3 = '';
			$default_value_1 = (int) $element->populated_value['element_'.$element->id.'_1']['default_value'];
			$default_value_2 = (int) $element->populated_value['element_'.$element->id.'_2']['default_value'];
			$default_value_3 = (int) $element->populated_value['element_'.$element->id.'_3']['default_value'];
		}
		
		if(!empty($default_value_1) && !empty($default_value_2) && !empty($default_value_3)){
			$cal_default_value = "\$('#element_{$element->id}_datepick').datepick('setDate', \$.datepick.newDate({$default_value_3}, {$default_value_1}, {$default_value_2}));";
		}
		
		$machform_path = '';
		if(!empty($element->machform_path)){
			$machform_path = $element->machform_path;
		}


		$cal_min_date = '';
		$cal_max_date = '';
		if(!empty($element->date_enable_range)){
			if(!empty($element->date_range_min)){ //value: yyyy-mm-dd
				$date_range_min = explode('-',$element->date_range_min);
				
				if($date_range_min[1] == '00' && ($date_range_min[2] == '00' || $date_range_min[2] == '01')){
					//this is relative date. format: XXXX-00-YY, where XXXX is number of days and YY is 00 or 01 (negative)
					$date_range_min[0] = (int) $date_range_min[0];

					if($date_range_min[2] == '00'){
						$cal_min_date =  ", minDate: -{$date_range_min[0]}";
					}else if($date_range_min[2] == '01'){ //if the value is negatove
						$cal_min_date =  ", minDate: +{$date_range_min[0]}";
					}
				}else{
					//minDate: YYYY, MM - 1, DD
					$cal_min_date = ", minDate: new Date({$date_range_min[0]}, {$date_range_min[1]} - 1, {$date_range_min[2]})"; 
				}
			}
			
			if(!empty($element->date_range_max)){ //value: yyyy-mm-dd
				$date_range_max = explode('-',$element->date_range_max);
				
				if($date_range_max[1] == '00' && ($date_range_max[2] == '00' || $date_range_max[2] == '01')){
					//this is relative date. format: XXXX-00-YY, where XXXX is number of days and YY is 00 or 01 (negative)
					$date_range_max[0] = (int) $date_range_max[0];
					
					if($date_range_max[2] == '00'){
						$cal_max_date =  ", maxDate: +{$date_range_max[0]}";
					}else if($date_range_max[2] == '01'){ //if the value is negative
						$cal_max_date =  ", maxDate: -{$date_range_max[0]}";
					}
				}else{
					//maxDate: YYYY, MM - 1, DD
					$cal_max_date = ", maxDate: new Date({$date_range_max[0]}, {$date_range_max[1]} - 1, {$date_range_max[2]})";
				}
			}
		}
		if(!empty($element->date_disable_past_future)){
			if($element->date_past_future == 'p'){ //disable past dates
				//set minDate to today's date
				$cal_min_date = ", minDate: new Date(".date('Y').", ".date('m')." - 1, ".date('d').")";
			}else if($element->date_past_future == 'f'){ //disable future dates
				//set maxDate to today's date
				$cal_max_date = ", maxDate: new Date(".date('Y').", ".date('m')." - 1, ".date('d').")";
			}
		}
		
		//disable weekend dates
		$cal_disable_weekend = '';
		if(!empty($element->date_disable_weekend)){
			$cal_disable_weekend = ' , onDate: $.datepick.noWeekends';
		}
		
		//disable specific dates
		$cal_disable_specific = '';
		$cal_disable_specific_callback = '';
		if(!empty($element->date_disable_specific) && !empty($element->date_disabled_list)){
			
			$date_disabled_list = explode(',',$element->date_disabled_list);
			$disabled_days = '';
			foreach ($date_disabled_list as $a_day){
				$a_day = trim($a_day);
				$a_day_exploded = explode('/',$a_day);
				$disabled_days .= "[{$a_day_exploded[0]}, {$a_day_exploded[1]}, {$a_day_exploded[2]}],";
			}
			$disabled_days = rtrim($disabled_days,',');
			$disabled_days = "var disabled_days_{$element->id} = [".$disabled_days."];";

$cal_disable_specific = <<<EOT
{$disabled_days}
			function disable_days_{$element->id}(date, inMonth) { 
			    var disable_weekend = {$element->date_disable_weekend};
				if (inMonth) { 
					var is_weekend = 0;
					if((date.getDay() || 7) >= 6){
						is_weekend = 1;
					}
					
			    	if(disable_weekend == 1 && is_weekend == 1){
			    		return {selectable: false};
			    	}else{
				        for (i = 0; i < disabled_days_{$element->id}.length; i++) { 
				            if (date.getMonth() + 1 == disabled_days_{$element->id}[i][0] && 
				                date.getDate() == disabled_days_{$element->id}[i][1] &&
				                date.getFullYear() == disabled_days_{$element->id}[i][2]
				                ) { 
				                return {dateClass: 'day_disabled', selectable: false}; 
				            } 
				        } 
			        }
			        
			    } 
			    return {}; 
			}	
EOT;
			$cal_disable_specific_callback = ", onDate: disable_days_{$element->id}";
		}
		
		//if this is edit entry, disable any date rules. admin should be able to use any dates
		if($element->is_edit_entry === true){
			$cal_min_date = '';
			$cal_max_date = '';
			$cal_disable_weekend = '';
			$cal_disable_specific_callback = '';
		}

		
$calendar_script = <<<EOT
<script type="text/javascript">
			{$cal_disable_specific}
			$('#element_{$element->id}_datepick').datepick({ 
	    		onSelect: select_date,
	    		showTrigger: '#cal_img_{$element->id}'
	    		{$cal_min_date}
	    		{$cal_max_date}
	    		{$cal_disable_weekend}
	    		{$cal_disable_specific_callback}
			});
			{$cal_default_value}
</script>
EOT;

		//don't call the calendar script if this is on edit_form page
		//or when the calendar fiel is read-only
		$cal_img_style = 'display: none';
		if($element->is_design_mode || $element->is_readonly){
			$calendar_script = '';
			$cal_img_style = 'display: block';
		}
		
		//build the li class
		if(!empty($el_class)){
			foreach ($el_class as $value){
				$li_class .= $value.' ';
			}
			
			$li_class = 'class="'.rtrim($li_class).'"';
		}
		
$element_markup = <<<EOT
		<li id="li_{$element->id}" {$li_style} {$li_class}>
		<span class="description">{$element->title} {$span_required}</span>
		<span class="date_mm">
			<input id="element_{$element->id}_1" name="element_{$element->id}_1" {$attr_readonly} class="element text" size="2" maxlength="2" value="{$default_value_1}" type="text" /> /
			<label for="element_{$element->id}_1">{$mf_lang['date_mm']}</label>
		</span>
		<span class="date_dd">
			<input id="element_{$element->id}_2" name="element_{$element->id}_2" {$attr_readonly} class="element text" size="2" maxlength="2" value="{$default_value_2}" type="text" /> /
			<label for="element_{$element->id}_2">{$mf_lang['date_dd']}</label>
		</span>
		<span class="date_yyyy">
	 		<input id="element_{$element->id}_3" name="element_{$element->id}_3" {$attr_readonly} class="element text" size="4" maxlength="4" value="{$default_value_3}" type="text" />
			<label for="element_{$element->id}_3">{$mf_lang['date_yyyy']}</label>
		</span>
	
		<span id="calendar_{$element->id}">
		    <input type="hidden" value="" name="element_{$element->id}_datepick" id="element_{$element->id}_datepick">
			<div style="{$cal_img_style}"><img id="cal_img_{$element->id}" class="datepicker" src="{$machform_path}images/calendar.gif" alt="Pick a date." /></div>	
		</span>
		{$calendar_script}
		{$guidelines} {$error_message}
		</li>
EOT;
	
		return $element_markup;
	}
	
	//Date - Normal
	function mf_display_europe_date($element){
		global $mf_lang;
		
		$li_class = '';
		$li_style = '';
		$error_message = '';
		$span_required = '';
		$attr_readonly = '';
		$guidelines = '';
		$el_class = array('europe_date_field');
		
		if($element->is_private == 1){
			$el_class[] = 'private';
		}

		if($element->is_private == 2 && !$element->is_design_mode && !$element->is_edit_entry){
			$li_style = 'style="display: none"';
		}

		if(!empty($element->css_class)){
			$el_class[] = trim($element->css_class);
		}
		
		if(!empty($element->is_error)){
			$el_class[] = 'error';
			if($element->error_message != 'error_no_display'){
				$error_message = "<p class=\"error\">{$element->error_message}</p>";
			}
		}
		
		//check for required
		if($element->is_required){
			$span_required = "<span id=\"required_{$element->id}\" class=\"required\">*</span>";
		}
		
		//check for read-only attribute
		if($element->is_readonly){
			$attr_readonly = 'readonly="readonly"';
		}

		//check for guidelines
		if(!empty($element->guidelines)){
			$guidelines = "<p class=\"guidelines\" id=\"guide_{$element->id}\"><small>{$element->guidelines}</small></p>";
		}
		
		//check for GET parameter to populate default value
		if(isset($_GET['element_'.$element->id.'_1'])){
			$default_value_1 = (int) htmlspecialchars(mf_sanitize($_GET['element_'.$element->id.'_1']),ENT_QUOTES);
		}
		if(isset($_GET['element_'.$element->id.'_2'])){
			$default_value_2 = (int) htmlspecialchars(mf_sanitize($_GET['element_'.$element->id.'_2']),ENT_QUOTES);
		}
		if(isset($_GET['element_'.$element->id.'_3'])){
			$default_value_3 = (int) htmlspecialchars(mf_sanitize($_GET['element_'.$element->id.'_3']),ENT_QUOTES);
		}

		//check for default value
		$cal_default_value = '';
		if(!empty($element->default_value)){
			//the default value can be mm/dd/yyyy or any valid english date words
			//we need to convert the default value into three parts (dd, mm, yyyy)
			$timestamp = strtotime($element->default_value);

			if(($timestamp !== false) && ($timestamp != -1)){
				$valid_default_date = date('d-m-Y', $timestamp);
				$valid_default_date = explode('-',$valid_default_date);
				
				$default_value_1 = (int) $valid_default_date[0];
				$default_value_2 = (int) $valid_default_date[1];
				$default_value_3 = (int) $valid_default_date[2];
			}else{ //it's not a valid date, display blank
				$default_value_1 = '';
				$default_value_2 = '';
				$default_value_3 = '';
			}
		}
		
		//if there's value submitted from the form, overwrite the default value
		if(!empty($element->populated_value['element_'.$element->id.'_1']['default_value']) || 
		   !empty($element->populated_value['element_'.$element->id.'_2']['default_value']) ||
		   !empty($element->populated_value['element_'.$element->id.'_3']['default_value'])
		){
			$default_value_1 = '';
			$default_value_2 = '';
			$default_value_3 = '';
			$default_value_1 = (int) $element->populated_value['element_'.$element->id.'_1']['default_value'];
			$default_value_2 = (int) $element->populated_value['element_'.$element->id.'_2']['default_value'];
			$default_value_3 = (int) $element->populated_value['element_'.$element->id.'_3']['default_value'];
		}
		
		if(!empty($default_value_1) && !empty($default_value_2) && !empty($default_value_3)){
			$cal_default_value = "\$('#element_{$element->id}_datepick').datepick('setDate', \$.datepick.newDate({$default_value_3}, {$default_value_2}, {$default_value_1}));";
		}
		
		$machform_path = '';
		if(!empty($element->machform_path)){
			$machform_path = $element->machform_path;
		}
		
		$cal_min_date = '';
		$cal_max_date = '';
		if(!empty($element->date_enable_range)){
			if(!empty($element->date_range_min)){ //value: yyyy-mm-dd
				$date_range_min = explode('-',$element->date_range_min);
				
				if($date_range_min[1] == '00' && ($date_range_min[2] == '00' || $date_range_min[2] == '01')){
					//this is relative date. format: XXXX-00-YY, where XXXX is number of days and YY is 00 or 01 (negative)
					$date_range_min[0] = (int) $date_range_min[0];

					if($date_range_min[2] == '00'){
						$cal_min_date =  ", minDate: -{$date_range_min[0]}";
					}else if($date_range_min[2] == '01'){ //if the value is negatove
						$cal_min_date =  ", minDate: +{$date_range_min[0]}";
					}
				}else{
					//minDate: YYYY, MM - 1, DD
					$cal_min_date = ", minDate: new Date({$date_range_min[0]}, {$date_range_min[1]} - 1, {$date_range_min[2]})"; 
				}
			}
			
			if(!empty($element->date_range_max)){ //value: yyyy-mm-dd
				$date_range_max = explode('-',$element->date_range_max);
				
				if($date_range_max[1] == '00' && ($date_range_max[2] == '00' || $date_range_max[2] == '01')){
					//this is relative date. format: XXXX-00-YY, where XXXX is number of days and YY is 00 or 01 (negative)
					$date_range_max[0] = (int) $date_range_max[0];
					
					if($date_range_max[2] == '00'){
						$cal_max_date =  ", maxDate: +{$date_range_max[0]}";
					}else if($date_range_max[2] == '01'){ //if the value is negative
						$cal_max_date =  ", maxDate: -{$date_range_max[0]}";
					}
				}else{
					//maxDate: YYYY, MM - 1, DD
					$cal_max_date = ", maxDate: new Date({$date_range_max[0]}, {$date_range_max[1]} - 1, {$date_range_max[2]})";
				}
			}
		}
		if(!empty($element->date_disable_past_future)){
			if($element->date_past_future == 'p'){ //disable past dates
				//set minDate to today's date
				$cal_min_date = ", minDate: new Date(".date('Y').", ".date('m')." - 1, ".date('d').")";
			}else if($element->date_past_future == 'f'){ //disable future dates
				//set maxDate to today's date
				$cal_max_date = ", maxDate: new Date(".date('Y').", ".date('m')." - 1, ".date('d').")";
			}
		}
		
		//disable weekend dates
		$cal_disable_weekend = '';
		if(!empty($element->date_disable_weekend)){
			$cal_disable_weekend = ' , onDate: $.datepick.noWeekends';
		}
		
		//disable specific dates
		$cal_disable_specific = '';
		$cal_disable_specific_callback = '';
		if(!empty($element->date_disable_specific) && !empty($element->date_disabled_list)){
			
			$date_disabled_list = explode(',',$element->date_disabled_list);
			$disabled_days = '';
			foreach ($date_disabled_list as $a_day){
				$a_day = trim($a_day);
				$a_day_exploded = explode('/',$a_day);
				$disabled_days .= "[{$a_day_exploded[0]}, {$a_day_exploded[1]}, {$a_day_exploded[2]}],";
			}
			$disabled_days = rtrim($disabled_days,',');
			$disabled_days = "var disabled_days_{$element->id} = [".$disabled_days."];";

$cal_disable_specific = <<<EOT
{$disabled_days}
			function disable_days_{$element->id}(date, inMonth) { 
			    var disable_weekend = {$element->date_disable_weekend};
				if (inMonth) { 
					var is_weekend = 0;
					if((date.getDay() || 7) >= 6){
						is_weekend = 1;
					}
					
			    	if(disable_weekend == 1 && is_weekend == 1){
			    		return {selectable: false};
			    	}else{
				        for (i = 0; i < disabled_days_{$element->id}.length; i++) { 
				            if (date.getMonth() + 1 == disabled_days_{$element->id}[i][0] && 
				                date.getDate() == disabled_days_{$element->id}[i][1] &&
				                date.getFullYear() == disabled_days_{$element->id}[i][2]
				                ) { 
				                return {dateClass: 'day_disabled', selectable: false}; 
				            } 
				        } 
			        }
			        
			    } 
			    return {}; 
			}	
EOT;
			$cal_disable_specific_callback = ", onDate: disable_days_{$element->id}";
		}
		
		//if this is edit entry, disable any date rules. admin should be able to use any dates
		if($element->is_edit_entry === true){
			$cal_min_date = '';
			$cal_max_date = '';
			$cal_disable_weekend = '';
			$cal_disable_specific_callback = '';
		}

		
$calendar_script = <<<EOT
<script type="text/javascript">
			{$cal_disable_specific}
			$('#element_{$element->id}_datepick').datepick({ 
	    		onSelect: select_europe_date,
	    		showTrigger: '#cal_img_{$element->id}'
	    		{$cal_min_date}
	    		{$cal_max_date}
	    		{$cal_disable_weekend}
	    		{$cal_disable_specific_callback}
			});
			{$cal_default_value}
</script>
EOT;

		//don't call the calendar script if this is on edit_form page
		//or when the calendar is read-only
		$cal_img_style = 'display: none';
		if($element->is_design_mode || $element->is_readonly){
			$calendar_script = '';
			$cal_img_style = 'display: block';
		}
		
		//build the li class
		if(!empty($el_class)){
			foreach ($el_class as $value){
				$li_class .= $value.' ';
			}
			
			$li_class = 'class="'.rtrim($li_class).'"';
		}
$element_markup = <<<EOT
		<li id="li_{$element->id}" {$li_style} {$li_class}>
		<span class="description">{$element->title} {$span_required}</span>
		<span class="date_dd">
			<input id="element_{$element->id}_1" name="element_{$element->id}_1" {$attr_readonly} class="element text" size="2" maxlength="2" value="{$default_value_1}" type="text" /> /
			<label for="element_{$element->id}_1">{$mf_lang['date_dd']}</label>
		</span>
		<span class="date_mm">
			<input id="element_{$element->id}_2" name="element_{$element->id}_2" {$attr_readonly} class="element text" size="2" maxlength="2" value="{$default_value_2}" type="text" /> /
			<label for="element_{$element->id}_2">{$mf_lang['date_mm']}</label>
		</span>
		<span class="date_yyyy">
	 		<input id="element_{$element->id}_3" name="element_{$element->id}_3" {$attr_readonly} class="element text" size="4" maxlength="4" value="{$default_value_3}" type="text" />
			<label for="element_{$element->id}_3">{$mf_lang['date_yyyy']}</label>
		</span>
	
		<span id="calendar_{$element->id}">
			<input type="hidden" value="" name="element_{$element->id}_datepick" id="element_{$element->id}_datepick">
			<div style="{$cal_img_style}"><img id="cal_img_{$element->id}" class="datepicker" src="{$machform_path}images/calendar.gif" alt="Pick a date." /></div>	
		</span>
		{$calendar_script}
		{$guidelines} {$error_message}
		</li>
EOT;
	
		return $element_markup;
	}
	
	
	//Multiple Choice
	function mf_display_radio($element){
		//check for error
		$li_class = '';
		$li_style = '';
		$error_message = '';
		$span_required = '';
		$guidelines = '';
		$el_class = array('multiple_choice');
		
		
		if($element->is_private == 1){
			$el_class[] = 'private';
		}

		if($element->is_private == 2 && !$element->is_design_mode && !$element->is_edit_entry){
			$li_style = 'style="display: none"';
		}
		
		if(!empty($element->css_class)){
			$el_class[] = trim($element->css_class);
		}
		
		if(!empty($element->choice_columns)){
			$col_number = (int) $element->choice_columns;
			if($col_number == 2){
				$el_class[] = 'two_columns';
			}else if($col_number == 3){
				$el_class[] = 'three_columns';
			}else if($col_number == 9){
				$el_class[] = 'inline_columns';
			}
		}
		
		
		if(!empty($element->is_error)){
			$el_class[] = 'error';
			$error_message = "<p class=\"error\">{$element->error_message}</p>";
		}
		
		//build the li class
		if(!empty($el_class)){
			foreach ($el_class as $value){
				$li_class .= $value.' ';
			}
			
			$li_class = 'class="'.rtrim($li_class).'"';
		}
		
		//check for required
		if($element->is_required){
			$span_required = "<span id=\"required_{$element->id}\" class=\"required\">*</span>";
		}
		
		//check for guidelines
		if(!empty($element->guidelines)){
			$guidelines = "<p class=\"guidelines\" id=\"guide_{$element->id}\"><small>{$element->guidelines}</small></p>";
		}
		
		$option_markup = '';
		
		//don't shuffle the choice on edit form page
		if(($element->constraint == 'random') && ($element->is_design_mode != true)){
			$temp = $element->options;
			shuffle($temp);
			$element->options = $temp;
		}
		
		$has_price_definition = false;
		$selected_price_value = 0;
		
		foreach ($element->options as $option){
			
			//check for Choice Limit
			//don't display the option if the total entries for the particular option has reached the limit
			if(!$element->is_design_mode && !$element->is_edit_entry && !empty($element->choice_max_entry)){
				$query = "SELECT COUNT(*) total_entry FROM ".MF_TABLE_PREFIX."form_{$element->form_id} where element_{$element->id} = ? and `status` = 1";
				$params = array($option->id);
			
				$sth = mf_do_query($query,$params,$element->dbh);
				$row = mf_do_fetch_result($sth);
				if($row['total_entry'] >= $element->choice_max_entry){
					continue;
				}
			}

			if($option->is_default){
				$checked = 'checked="checked"';
				$selected_price_value = $option->price_definition;

				//default value shouldn't be loaded during edit entry if the field is not admin only
				if(!empty($option->is_default) && ($element->is_private == 0) && $element->is_edit_entry){
					$checked = '';
				}
			}else{
				$checked = '';
			}
			
			//check for GET parameter to populate default value
			if(isset($_GET['element_'.$element->id]) && $_GET['element_'.$element->id] == $option->id){
				$checked = 'checked="checked"';
				$selected_price_value = $option->price_definition;
			}

			//check for populated values
			if(!empty($element->populated_value['element_'.$element->id]['default_value'])){
				$checked = '';

				if($element->populated_value['element_'.$element->id]['default_value'] == $option->id){
					$checked = 'checked="checked"';
					$selected_price_value = $option->price_definition;
				}
			}
			
			if(isset($option->price_definition)){
				$price_definition_data_attr = 'data-pricedef="'.$option->price_definition.'"';
				$has_price_definition = true;
			}else{
				$price_definition_data_attr = '';
			}
			
			$pre_option_markup = '';			
			$pre_option_markup .= "<input id=\"element_{$element->id}_{$option->id}\" {$price_definition_data_attr} name=\"element_{$element->id}\" class=\"element radio\" type=\"radio\" value=\"{$option->id}\" {$checked} />\n";
			$pre_option_markup .= "<label class=\"choice\" for=\"element_{$element->id}_{$option->id}\">{$option->option}</label>\n";
			
			$option_markup .= '<span>'.$pre_option_markup."</span>\n";
		}
		
		//if 'other choice' is enabled, add a new choice at the end and add text field
		if(!empty($element->choice_has_other)){
			//check for GET parameter to populate default value
			if(isset($_GET['element_'.$element->id.'_other'])){
				$element->populated_value['element_'.$element->id.'_other']['default_value'] = htmlspecialchars(mf_sanitize($_GET['element_'.$element->id.'_other']),ENT_QUOTES);
			}

			if(!empty($element->populated_value['element_'.$element->id.'_other']['default_value'])){
				$other_value = $element->populated_value['element_'.$element->id.'_other']['default_value'];
				$checked = 'checked="checked"';
			}else{
				$checked = '';
				$other_value = '';	
			}
			
			$pre_option_markup = '';
			$pre_option_markup .= "<input id=\"element_{$element->id}_0\" name=\"element_{$element->id}\" class=\"element radio\" type=\"radio\" value=\"\" {$checked} />\n";
			$pre_option_markup .= "<label class=\"choice other\" for=\"element_{$element->id}_0\">{$element->choice_other_label}</label>\n";
			$pre_option_markup .= "<label for=\"element_{$element->id}_other\" style=\"display: none\">{$element->choice_other_label}</label>\n";
			$pre_option_markup .= "<input type=\"text\" value=\"{$other_value}\" class=\"element text other\" name=\"element_{$element->id}_other\" id=\"element_{$element->id}_other\" onclick=\"\$('#element_{$element->id}_0').prop('checked',true).click();\" />\n";
			
			$option_markup .= '<span>'.$pre_option_markup."</span>\n";
		}

		if($has_price_definition === true){
			$price_data_tag = 'data-pricefield="radio" data-pricevalue="'.$selected_price_value.'"';
		}
		
$element_markup = <<<EOT
		<li id="li_{$element->id}" {$price_data_tag} {$li_style} {$li_class}>
		<span class="description">{$element->title} {$span_required}</span>
		<div>
			<fieldset>
				<legend style="display: none">{$element->title}</legend>
				{$option_markup}
			</fieldset>
		</div>{$guidelines} {$error_message}
		</li>
EOT;
		
		return $element_markup;
	}
	
	//Checkboxes
	function mf_display_checkbox($element){
		//check for error
		$li_class = '';
		$li_style = '';
		$error_message = '';
		$span_required = '';
		$guidelines = '';
		$el_class = array('checkboxes');
		
		if($element->is_private == 1){
			$el_class[] = 'private';
		}

		if($element->is_private == 2 && !$element->is_design_mode && !$element->is_edit_entry){
			$li_style = 'style="display: none"';
		}
		
		if(!empty($element->css_class)){
			$el_class[] = trim($element->css_class);
		}
		
		if(!empty($element->choice_columns)){
			$col_number = (int) $element->choice_columns;
			if($col_number == 2){
				$el_class[] = 'two_columns';
			}else if($col_number == 3){
				$el_class[] = 'three_columns';
			}else if($col_number == 9){
				$el_class[] = 'inline_columns';
			}
		}
		
		if(!empty($element->is_error)){
			$el_class[] = 'error';
			$error_message = "<p class=\"error\">{$element->error_message}</p>";
		}
		
		//build the class
		if(!empty($el_class)){
			foreach ($el_class as $value){
				$li_class .= $value.' ';
			}
			
			$li_class = 'class="'.rtrim($li_class).'"';
		}
		
		//check for required
		if($element->is_required){
			$span_required = "<span id=\"required_{$element->id}\" class=\"required\">*</span>";
		}
		
		//check for guidelines
		if(!empty($element->guidelines)){
			$guidelines = "<p class=\"guidelines\" id=\"guide_{$element->id}\"><small>{$element->guidelines}</small></p>";
		}
		
		//check for populated value first, if any exist, unselect all default value
		$is_populated = false;
		foreach ($element->options as $option){
			
			if(!empty($element->populated_value['element_'.$element->id.'_'.$option->id]['default_value'])){
				$is_populated = true;
				break;
			}
		}
	
		$option_markup = '';
		$has_price_definition = false;
		$selected_price_value = 0;
		
		foreach ($element->options as $option){
			if(!$is_populated){
				if($option->is_default && ($element->is_edit_entry !== true)){
					$checked = 'checked="checked"';
					$selected_price_value += (double) $option->price_definition;
				}else if(isset($_GET['element_'.$element->id.'_'.$option->id])){
					$checked = 'checked="checked"';
					$selected_price_value += (double) $option->price_definition;
				}else{
					$checked = '';
				}
			}else{
				
				if(!empty($element->populated_value['element_'.$element->id.'_'.$option->id]['default_value'])){
					$checked = 'checked="checked"';
					$selected_price_value += (double) $option->price_definition;
				}else{
					$checked = '';	
				}
			}
			
			if(isset($option->price_definition)){
				$price_definition_data_attr = 'data-pricedef="'.$option->price_definition.'"';
				$has_price_definition = true;
			}else{
				$price_definition_data_attr = '';
			}
			
			$pre_option_markup = '';
			$pre_option_markup .= "<input id=\"element_{$element->id}_{$option->id}\" {$price_definition_data_attr} name=\"element_{$element->id}_{$option->id}\" class=\"element checkbox\" type=\"checkbox\" value=\"1\" {$checked} />\n";
			$pre_option_markup .= "<label class=\"choice\" for=\"element_{$element->id}_{$option->id}\">{$option->option}</label>\n";
			
			$option_markup .= '<span>'.$pre_option_markup."</span>\n";
		}
		
		//if 'other checkbox' is enabled, add a new checkbox at the end and add text field
		if(!empty($element->choice_has_other)){
			//check for GET parameter to populate default value
			if(isset($_GET['element_'.$element->id.'_other'])){
				$element->populated_value['element_'.$element->id.'_other']['default_value'] = htmlspecialchars(mf_sanitize($_GET['element_'.$element->id.'_other']),ENT_QUOTES);
			}

			if(!empty($element->populated_value['element_'.$element->id.'_other']['default_value'])){
				$other_value = $element->populated_value['element_'.$element->id.'_other']['default_value'];
				$checked = 'checked="checked"';
			}else{
				$checked = '';
				$other_value = '';	
			}
			
			$pre_option_markup = '';
			$pre_option_markup .= "<input id=\"element_{$element->id}_0\" name=\"element_{$element->id}\" class=\"element checkbox\" onchange=\"clear_cb_other(this,{$element->id});\"  type=\"checkbox\" value=\"\" {$checked} />\n";
			$pre_option_markup .= "<label class=\"choice other\" for=\"element_{$element->id}_0\">{$element->choice_other_label}</label>\n";
			$pre_option_markup .= "<label style=\"display: none\" for=\"element_{$element->id}_other\">{$element->choice_other_label}</label>\n";
			$pre_option_markup .= "<input type=\"text\" value=\"{$other_value}\" class=\"element text other\" name=\"element_{$element->id}_other\" id=\"element_{$element->id}_other\" onclick=\"\$('#element_{$element->id}_0').prop('checked',true);\" />\n";
			
			$option_markup .= '<span>'.$pre_option_markup."</span>\n";
		}
		
		if($has_price_definition === true){
			$selected_price_value = (double) $selected_price_value;
			$price_data_tag = 'data-pricefield="checkbox" data-pricevalue="'.$selected_price_value.'"';
		}
		
$element_markup = <<<EOT
		<li id="li_{$element->id}" {$price_data_tag} {$li_style} {$li_class}>
		<span class="description">{$element->title} {$span_required}</span>
		<div>
			{$option_markup}
		</div>{$guidelines} {$error_message}
		</li>
EOT;
		
		return $element_markup;
	}

	
	//Dropdown
	function mf_display_select($element){
		//check for error
		$li_class = '';
		$li_style = '';
		$error_message = '';
		$span_required = '';
		$guidelines = '';
		$el_class = array('dropdown');
		
		if($element->is_private == 1){
			$el_class[] = 'private';
		}

		if($element->is_private == 2 && !$element->is_design_mode && !$element->is_edit_entry){
			$li_style = 'style="display: none"';
		}

		if(!empty($element->css_class)){
			$el_class[] = trim($element->css_class);
		}
		
		if(!empty($element->is_error)){
			$el_class[] = 'error';
			if($element->error_message != 'error_no_display'){
				$error_message = "<p class=\"error\">{$element->error_message}</p>";
			}
		}
		
		//check for required
		if($element->is_required){
			$span_required = "<span id=\"required_{$element->id}\" class=\"required\">*</span>";
		}
		
		//check for guidelines
		if(!empty($element->guidelines)){
			$guidelines = "<p class=\"guidelines\" id=\"guide_{$element->id}\"><small>{$element->guidelines}</small></p>";
		}
		
		$option_markup = '';
		$has_price_definition = false;
		$selected_price_value = 0;
		
		$has_default = false;
		foreach ($element->options as $option){
			
			//check for Choice Limit
			//don't display the option if the total entries for the particular option has reached the limit
			if(!$element->is_design_mode && !$element->is_edit_entry && !empty($element->choice_max_entry)){
				$query = "SELECT COUNT(*) total_entry FROM ".MF_TABLE_PREFIX."form_{$element->form_id} where element_{$element->id} = ? and `status` = 1";
				$params = array($option->id);
			
				$sth = mf_do_query($query,$params,$element->dbh);
				$row = mf_do_fetch_result($sth);
				if($row['total_entry'] >= $element->choice_max_entry){
					continue;
				}
			}
			
			if($option->is_default || (isset($_GET['element_'.$element->id]) && $_GET['element_'.$element->id] == $option->id)){
				$selected = 'selected="selected"';
				$has_default = true;
				$selected_price_value = $option->price_definition;

				//default value shouldn't be loaded during edit entry if the field is not admin only
				if(!empty($option->is_default) && ($element->is_private == 0) && $element->is_edit_entry){
					$has_default = false;
					$selected = '';
				}
			}else{
				$selected = '';
			}
			
			if(!empty($element->populated_value['element_'.$element->id]['default_value'])){
				$selected = '';
				if($element->populated_value['element_'.$element->id]['default_value'] == $option->id){
					$selected = 'selected="selected"';
					$selected_price_value = $option->price_definition;
				}
			}
			
			if(isset($option->price_definition)){
				$price_definition_data_attr = 'data-pricedef="'.$option->price_definition.'"';
				$has_price_definition = true;
			}else{
				$price_definition_data_attr = '';
			}
			
			$option_markup .= "<option value=\"{$option->id}\" {$price_definition_data_attr} {$selected}>{$option->option}</option>\n";
		}
		
		if(!$has_default){
			if(!empty($element->populated_value['element_'.$element->id]['default_value'])){
				$option_markup = '<option value=""></option>'."\n".$option_markup;
			}else{
				$option_markup = '<option value="" selected="selected"></option>'."\n".$option_markup;
			}
		}
		
		//build the li class
		if(!empty($el_class)){
			foreach ($el_class as $value){
				$li_class .= $value.' ';
			}
			
			$li_class = 'class="'.rtrim($li_class).'"';
		}
		
		if($has_price_definition === true){
			$price_data_tag = 'data-pricefield="select" data-pricevalue="'.$selected_price_value.'"';
		}
		
$element_markup = <<<EOT
		<li id="li_{$element->id}" {$price_data_tag} {$li_style} {$li_class}>
		<label class="description" for="element_{$element->id}">{$element->title} {$span_required}</label>
		<div>
		<select class="element select {$element->size}" id="element_{$element->id}" name="element_{$element->id}"> 
			{$option_markup}
		</select>
		</div>{$guidelines} {$error_message}
		</li>
EOT;

		return $element_markup;
	}
	
	
	//Name - Simple
	function mf_display_simple_name($element){
		
		global $mf_lang;
		
		$li_class = '';
		$li_style = '';
		$error_message = '';
		$span_required = '';
		$attr_readonly = '';
		$guidelines = '';
		$el_class = array('simple_name');
		
		if($element->is_private == 1){
			$el_class[] = 'private';
		}

		if($element->is_private == 2 && !$element->is_design_mode && !$element->is_edit_entry){
			$li_style = 'style="display: none"';
		}

		if(!empty($element->css_class)){
			$el_class[] = trim($element->css_class);
		}
		
		if(!empty($element->is_error)){
			$el_class[] = 'error';
			if($element->error_message != 'error_no_display'){
				$error_message = "<p class=\"error\">{$element->error_message}</p>";
			}
		}
		
		//check for required
		if($element->is_required){
			$span_required = "<span id=\"required_{$element->id}\" class=\"required\">*</span>";
		}
		
		//check for read-only attribute
		if($element->is_readonly){
			$attr_readonly = 'readonly="readonly"';
		}

		//check for guidelines
		if(!empty($element->guidelines)){
			$guidelines = "<p class=\"guidelines\" id=\"guide_{$element->id}\"><small>{$element->guidelines}</small></p>";
		}
		
		//check for GET parameter to populate default value
		if(isset($_GET['element_'.$element->id.'_1'])){
			$default_value_1 = htmlspecialchars(mf_sanitize($_GET['element_'.$element->id.'_1']),ENT_QUOTES);
		}
		if(isset($_GET['element_'.$element->id.'_2'])){
			$default_value_2 = htmlspecialchars(mf_sanitize($_GET['element_'.$element->id.'_2']),ENT_QUOTES);
		}

		//check for populated values, if exist override the default value
		if(!empty($element->populated_value['element_'.$element->id.'_1']['default_value']) || 
		   !empty($element->populated_value['element_'.$element->id.'_2']['default_value'])
		){
			$default_value_1 = '';
			$default_value_2 = '';
			$default_value_1 = $element->populated_value['element_'.$element->id.'_1']['default_value'];
			$default_value_2 = $element->populated_value['element_'.$element->id.'_2']['default_value'];
		}

		//build the li class
		if(!empty($el_class)){
			foreach ($el_class as $value){
				$li_class .= $value.' ';
			}
			
			$li_class = 'class="'.rtrim($li_class).'"';
		}
		
$element_markup = <<<EOT
		<li id="li_{$element->id}" {$li_style} {$li_class}>
		<span class="description">{$element->title} {$span_required}</span>
		<span class="simple_name_1">
			<input id="element_{$element->id}_1" name="element_{$element->id}_1" {$attr_readonly} type="text" class="element text" maxlength="255" size="8" value="{$default_value_1}" />
			<label for="element_{$element->id}_1">{$mf_lang['name_first']}</label>
		</span>
		<span class="simple_name_2">
			<input id="element_{$element->id}_2" name="element_{$element->id}_2" {$attr_readonly} type="text" class="element text" maxlength="255" size="14" value="{$default_value_2}" />
			<label for="element_{$element->id}_2">{$mf_lang['name_last']}</label>
		</span>{$guidelines} {$error_message}
		</li>
EOT;
		
		return $element_markup;
	}
	
	//Name - Simple, with Middle Name
	function mf_display_simple_name_wmiddle($element){
		global $mf_lang;
		
		$li_class = '';
		$li_style = '';
		$error_message = '';
		$span_required = '';
		$attr_readonly = '';
		$guidelines = '';
		$el_class = array('simple_name_wmiddle');
		
		if($element->is_private == 1){
			$el_class[] = 'private';
		}

		if($element->is_private == 2 && !$element->is_design_mode && !$element->is_edit_entry){
			$li_style = 'style="display: none"';
		}

		if(!empty($element->css_class)){
			$el_class[] = trim($element->css_class);
		}
		
		if(!empty($element->is_error)){
			$el_class[] = 'error';
			if($element->error_message != 'error_no_display'){
				$error_message = "<p class=\"error\">{$element->error_message}</p>";
			}
		}
		
		//check for required
		if($element->is_required){
			$span_required = "<span id=\"required_{$element->id}\" class=\"required\">*</span>";
		}
		
		//check for read-only attribute
		if($element->is_readonly){
			$attr_readonly = 'readonly="readonly"';
		}

		//check for guidelines
		if(!empty($element->guidelines)){
			$guidelines = "<p class=\"guidelines\" id=\"guide_{$element->id}\"><small>{$element->guidelines}</small></p>";
		}
		
		//check for GET parameter to populate default value
		if(isset($_GET['element_'.$element->id.'_1'])){
			$default_value_1 = htmlspecialchars(mf_sanitize($_GET['element_'.$element->id.'_1']),ENT_QUOTES);
		}
		if(isset($_GET['element_'.$element->id.'_2'])){
			$default_value_2 = htmlspecialchars(mf_sanitize($_GET['element_'.$element->id.'_2']),ENT_QUOTES);
		}
		if(isset($_GET['element_'.$element->id.'_3'])){
			$default_value_3 = htmlspecialchars(mf_sanitize($_GET['element_'.$element->id.'_3']),ENT_QUOTES);
		}
		
		//check for populated values, if exist override the default value
		if(!empty($element->populated_value['element_'.$element->id.'_1']['default_value']) || 
		   !empty($element->populated_value['element_'.$element->id.'_2']['default_value']) ||
		   !empty($element->populated_value['element_'.$element->id.'_3']['default_value'])
		){
			$default_value_1 = '';
			$default_value_2 = '';
			$default_value_3 = '';
			$default_value_1 = $element->populated_value['element_'.$element->id.'_1']['default_value'];
			$default_value_2 = $element->populated_value['element_'.$element->id.'_2']['default_value'];
			$default_value_3 = $element->populated_value['element_'.$element->id.'_3']['default_value'];
		}

		//build the li class
		if(!empty($el_class)){
			foreach ($el_class as $value){
				$li_class .= $value.' ';
			}
			
			$li_class = 'class="'.rtrim($li_class).'"';
		}
		
$element_markup = <<<EOT
		<li id="li_{$element->id}" {$li_style} {$li_class}>
		<span class="description">{$element->title} {$span_required}</span>
		<span class="simple_name_wmiddle_1">
			<input id="element_{$element->id}_1" name="element_{$element->id}_1" {$attr_readonly} type="text" class="element text" maxlength="255" size="8" value="{$default_value_1}" />
			<label for="element_{$element->id}_1">{$mf_lang['name_first']}</label>
		</span>
		<span class="simple_name_wmiddle_2">
			<input id="element_{$element->id}_2" name="element_{$element->id}_2" {$attr_readonly} type="text" class="element text" maxlength="255" size="8" value="{$default_value_2}" />
			<label for="element_{$element->id}_2">{$mf_lang['name_middle']}</label>
		</span>
		<span class="simple_name_wmiddle_3">
			<input id="element_{$element->id}_3" name="element_{$element->id}_3" {$attr_readonly} type="text" class="element text" maxlength="255" size="14" value="{$default_value_3}" />
			<label for="element_{$element->id}_3">{$mf_lang['name_last']}</label>
		</span>{$guidelines} {$error_message}
		</li>
EOT;
		
		return $element_markup;
	}

	//Name 
	function mf_display_name($element){
		global $mf_lang;
		
		$li_class = '';
		$li_style = '';
		$error_message = '';
		$span_required = '';
		$attr_readonly = '';
		$guidelines = '';
		$el_class = array('fullname');
		
		if($element->is_private == 1){
			$el_class[] = 'private';
		}

		if($element->is_private == 2 && !$element->is_design_mode && !$element->is_edit_entry){
			$li_style = 'style="display: none"';
		}

		if(!empty($element->css_class)){
			$el_class[] = trim($element->css_class);
		}
		
		if(!empty($element->is_error)){
			$el_class[] = 'error';
			if($element->error_message != 'error_no_display'){
				$error_message = "<p class=\"error\">{$element->error_message}</p>";
			}
		}
		
		//check for required
		if($element->is_required){
			$span_required = "<span id=\"required_{$element->id}\" class=\"required\">*</span>";
		}
		
		//check for read-only attribute
		if($element->is_readonly){
			$attr_readonly = 'readonly="readonly"';
		}

		//check for guidelines
		if(!empty($element->guidelines)){
			$guidelines = "<p class=\"guidelines\" id=\"guide_{$element->id}\"><small>{$element->guidelines}</small></p>";
		}

		//check for GET parameter to populate default value
		if(isset($_GET['element_'.$element->id.'_1'])){
			$default_value_1 = htmlspecialchars(mf_sanitize($_GET['element_'.$element->id.'_1']),ENT_QUOTES);
		}
		if(isset($_GET['element_'.$element->id.'_2'])){
			$default_value_2 = htmlspecialchars(mf_sanitize($_GET['element_'.$element->id.'_2']),ENT_QUOTES);
		}
		if(isset($_GET['element_'.$element->id.'_3'])){
			$default_value_3 = htmlspecialchars(mf_sanitize($_GET['element_'.$element->id.'_3']),ENT_QUOTES);
		}
		if(isset($_GET['element_'.$element->id.'_4'])){
			$default_value_4 = htmlspecialchars(mf_sanitize($_GET['element_'.$element->id.'_4']),ENT_QUOTES);
		}
		
		//check for populated values, if exist override the default value
		if(!empty($element->populated_value['element_'.$element->id.'_1']['default_value']) || 
		   !empty($element->populated_value['element_'.$element->id.'_2']['default_value']) ||
		   !empty($element->populated_value['element_'.$element->id.'_3']['default_value']) ||
		   !empty($element->populated_value['element_'.$element->id.'_4']['default_value'])
		){
			$default_value_1 = '';
			$default_value_2 = '';
			$default_value_3 = '';
			$default_value_4 = '';
			$default_value_1 = $element->populated_value['element_'.$element->id.'_1']['default_value'];
			$default_value_2 = $element->populated_value['element_'.$element->id.'_2']['default_value'];
			$default_value_3 = $element->populated_value['element_'.$element->id.'_3']['default_value'];
			$default_value_4 = $element->populated_value['element_'.$element->id.'_4']['default_value'];
		}
		
		//build the li class
		if(!empty($el_class)){
			foreach ($el_class as $value){
				$li_class .= $value.' ';
			}
			
			$li_class = 'class="'.rtrim($li_class).'"';
		}
		
$element_markup = <<<EOT
		<li id="li_{$element->id}" {$li_style} {$li_class}>
		<span class="description">{$element->title} {$span_required}</span>
		<span class="fullname_1">
			<input id="element_{$element->id}_1" name="element_{$element->id}_1" {$attr_readonly} type="text" class="element text" maxlength="255" size="2" value="{$default_value_1}" />
			<label for="element_{$element->id}_1">{$mf_lang['name_title']}</label>
		</span>
		<span class="fullname_2">
			<input id="element_{$element->id}_2" name="element_{$element->id}_2" {$attr_readonly} type="text" class="element text" maxlength="255" size="8" value="{$default_value_2}" />
			<label for="element_{$element->id}_2">{$mf_lang['name_first']}</label>
		</span>
		<span class="fullname_3">
			<input id="element_{$element->id}_3" name="element_{$element->id}_3" {$attr_readonly} type="text" class="element text" maxlength="255" size="14" value="{$default_value_3}" />
			<label for="element_{$element->id}_3">{$mf_lang['name_last']}</label>
		</span>
		<span class="fullname_4">
			<input id="element_{$element->id}_4" name="element_{$element->id}_4" {$attr_readonly} type="text" class="element text" maxlength="255" size="3" value="{$default_value_4}" />
			<label for="element_{$element->id}_4">{$mf_lang['name_suffix']}</label>
		</span>{$guidelines} {$error_message}
		</li>
EOT;
		
		return $element_markup;
	}
	
	//Name, with Middle
	function mf_display_name_wmiddle($element){
		global $mf_lang;
		
		$li_class = '';
		$li_style = '';
		$error_message = '';
		$span_required = '';
		$attr_readonly = '';
		$guidelines = '';
		$el_class = array('fullname_wmiddle');
		
		if($element->is_private == 1){
			$el_class[] = 'private';
		}

		if($element->is_private == 2 && !$element->is_design_mode && !$element->is_edit_entry){
			$li_style = 'style="display: none"';
		}

		if(!empty($element->css_class)){
			$el_class[] = trim($element->css_class);
		}
		
		if(!empty($element->is_error)){
			$el_class[] = 'error';
			if($element->error_message != 'error_no_display'){
				$error_message = "<p class=\"error\">{$element->error_message}</p>";
			}
		}
		
		//check for required
		if($element->is_required){
			$span_required = "<span id=\"required_{$element->id}\" class=\"required\">*</span>";
		}

		//check for read-only attribute
		if($element->is_readonly){
			$attr_readonly = 'readonly="readonly"';
		}
		
		//check for guidelines
		if(!empty($element->guidelines)){
			$guidelines = "<p class=\"guidelines\" id=\"guide_{$element->id}\"><small>{$element->guidelines}</small></p>";
		}
		
		//check for GET parameter to populate default value
		if(isset($_GET['element_'.$element->id.'_1'])){
			$default_value_1 = htmlspecialchars(mf_sanitize($_GET['element_'.$element->id.'_1']),ENT_QUOTES);
		}
		if(isset($_GET['element_'.$element->id.'_2'])){
			$default_value_2 = htmlspecialchars(mf_sanitize($_GET['element_'.$element->id.'_2']),ENT_QUOTES);
		}
		if(isset($_GET['element_'.$element->id.'_3'])){
			$default_value_3 = htmlspecialchars(mf_sanitize($_GET['element_'.$element->id.'_3']),ENT_QUOTES);
		}
		if(isset($_GET['element_'.$element->id.'_4'])){
			$default_value_4 = htmlspecialchars(mf_sanitize($_GET['element_'.$element->id.'_4']),ENT_QUOTES);
		}
		if(isset($_GET['element_'.$element->id.'_5'])){
			$default_value_5 = htmlspecialchars(mf_sanitize($_GET['element_'.$element->id.'_5']),ENT_QUOTES);
		}
		
		//check for populated values, if exist override the default value
		if(!empty($element->populated_value['element_'.$element->id.'_1']['default_value']) || 
		   !empty($element->populated_value['element_'.$element->id.'_2']['default_value']) ||
		   !empty($element->populated_value['element_'.$element->id.'_3']['default_value']) ||
		   !empty($element->populated_value['element_'.$element->id.'_4']['default_value']) ||
		   !empty($element->populated_value['element_'.$element->id.'_5']['default_value'])
		){
			$default_value_1 = '';
			$default_value_2 = '';
			$default_value_3 = '';
			$default_value_4 = '';
			$default_value_5 = '';
			$default_value_1 = $element->populated_value['element_'.$element->id.'_1']['default_value'];
			$default_value_2 = $element->populated_value['element_'.$element->id.'_2']['default_value'];
			$default_value_3 = $element->populated_value['element_'.$element->id.'_3']['default_value'];
			$default_value_4 = $element->populated_value['element_'.$element->id.'_4']['default_value'];
			$default_value_5 = $element->populated_value['element_'.$element->id.'_5']['default_value'];
		}

		//build the li class
		if(!empty($el_class)){
			foreach ($el_class as $value){
				$li_class .= $value.' ';
			}
			
			$li_class = 'class="'.rtrim($li_class).'"';
		}
		
$element_markup = <<<EOT
		<li id="li_{$element->id}" {$li_style} {$li_class}>
		<span class="description">{$element->title} {$span_required}</span>
		<span class="namewm_ext">
			<input id="element_{$element->id}_1" name="element_{$element->id}_1" {$attr_readonly} type="text" class="element text large" maxlength="255" value="{$default_value_1}" />
			<label for="element_{$element->id}_1">{$mf_lang['name_title']}</label>
		</span>
		<span class="namewm_first">
			<input id="element_{$element->id}_2" name="element_{$element->id}_2" {$attr_readonly} type="text" class="element text large" maxlength="255" value="{$default_value_2}" />
			<label for="element_{$element->id}_2">{$mf_lang['name_first']}</label>
		</span>
		<span class="namewm_middle">
			<input id="element_{$element->id}_3" name="element_{$element->id}_3" {$attr_readonly} type="text" class="element text large" maxlength="255" value="{$default_value_3}" />
			<label for="element_{$element->id}_3">{$mf_lang['name_middle']}</label>
		</span>
		<span class="namewm_last">
			<input id="element_{$element->id}_4" name="element_{$element->id}_4" {$attr_readonly} type="text" class="element text large" maxlength="255" value="{$default_value_4}" />
			<label for="element_{$element->id}_4">{$mf_lang['name_last']}</label>
		</span>
		<span class="namewm_ext">
			<input id="element_{$element->id}_5" name="element_{$element->id}_5" {$attr_readonly} type="text" class="element text large" maxlength="255" value="{$default_value_5}" />
			<label for="element_{$element->id}_5">{$mf_lang['name_suffix']}</label>
		</span>{$guidelines} {$error_message}
		</li>
EOT;
		
		return $element_markup;
	}
	
	//Time
	function mf_display_time($element){
		
		global $mf_lang;
		
		$li_class = '';
		$li_style = '';
		$error_message = '';
		$span_required = '';
		$attr_readonly = '';
		$guidelines = '';
		$el_class = array('time_field');
		
		if($element->is_private == 1){
			$el_class[] = 'private';
		}

		if($element->is_private == 2 && !$element->is_design_mode && !$element->is_edit_entry){
			$li_style = 'style="display: none"';
		}

		if(!empty($element->css_class)){
			$el_class[] = trim($element->css_class);
		}
		
		if(!empty($element->is_error)){
			$el_class[] = 'error';
			if($element->error_message != 'error_no_display'){
				$error_message = "<p class=\"error\">{$element->error_message}</p>";
			}
		}
		
		//check for required
		if($element->is_required){
			$span_required = "<span id=\"required_{$element->id}\" class=\"required\">*</span>";
		}

		//check for read-only attribute
		if($element->is_readonly){
			$attr_readonly = 'readonly="readonly"';
		}
		
		//check for guidelines
		if(!empty($element->guidelines)){
			$guidelines = "<p class=\"guidelines\" id=\"guide_{$element->id}\"><small>{$element->guidelines}</small></p>";
		}

		//check for GET parameter to populate default value
		if(isset($_GET['element_'.$element->id.'_1'])){
			$default_value_1 = htmlspecialchars(mf_sanitize($_GET['element_'.$element->id.'_1']),ENT_QUOTES);
		}
		if(isset($_GET['element_'.$element->id.'_2'])){
			$default_value_2 = htmlspecialchars(mf_sanitize($_GET['element_'.$element->id.'_2']),ENT_QUOTES);
		}
		if(isset($_GET['element_'.$element->id.'_3'])){
			$default_value_3 = htmlspecialchars(mf_sanitize($_GET['element_'.$element->id.'_3']),ENT_QUOTES);
		}
		if(isset($_GET['element_'.$element->id.'_4'])){
			if($_GET['element_'.$element->id.'_4'] == 'AM'){
				$selected_am = 'selected';
			}else{
				$selected_pm = 'selected';
			}
		}

		//check for populated values, if exist override the default value
		if(!empty($element->populated_value['element_'.$element->id.'_1']['default_value']) || 
		   !empty($element->populated_value['element_'.$element->id.'_2']['default_value']) ||
		   !empty($element->populated_value['element_'.$element->id.'_3']['default_value'])
		){
			$default_value_1 = '';
			$default_value_2 = '';
			$default_value_3 = '';
			$default_value_1 = $element->populated_value['element_'.$element->id.'_1']['default_value'];
			$default_value_2 = $element->populated_value['element_'.$element->id.'_2']['default_value'];
			$default_value_3 = $element->populated_value['element_'.$element->id.'_3']['default_value'];
		}
		
		if(!empty($element->populated_value['element_'.$element->id.'_4']['default_value'])){
			if($element->populated_value['element_'.$element->id.'_4']['default_value'] == 'AM'){
				$selected_am = 'selected';
			}else{
				$selected_pm = 'selected';
			}
		}
		
		if(!empty($element->time_showsecond)){
			$seconds_markup =<<<EOT
		<span>
			<input id="element_{$element->id}_3" name="element_{$element->id}_3" {$attr_readonly} class="element text" size="2" type="text" maxlength="2" value="{$default_value_3}" />
			<label for="element_{$element->id}_3">{$mf_lang['time_ss']}</label>
		</span>
EOT;
			$seconds_separator = ':';
		}else{
			$seconds_markup = '';
			$seconds_separator = '';
		}
		
		if(empty($element->time_24hour)){
			
			//since there is no readonly attribute for dropdown, we need to disable it and include the value as hidden field
			if($element->is_readonly){
				if($selected_pm == 'selected'){
					$am_pm_hidden_field_value = 'PM';
				}else{
					$am_pm_hidden_field_value = 'AM';
				}

				$am_pm_hidden_field_markup = "<input type=\"hidden\" name=\"element_{$element->id}_4\" value=\"{$am_pm_hidden_field_value}\" />";
				$am_pm_disabled_attribute = 'disabled="disabled"';
			}

			$am_pm_markup =<<<EOT
		<span>
			<select class="element select" style="width:5em" {$am_pm_disabled_attribute} id="element_{$element->id}_4" name="element_{$element->id}_4">
				<option value="AM" {$selected_am}>AM</option>
				<option value="PM" {$selected_pm}>PM</option>
			</select>
			<label for="element_{$element->id}_4">AM/PM</label>
			{$am_pm_hidden_field_markup}
		</span>
EOT;
		}
		
		//build the li class
		if(!empty($el_class)){
			foreach ($el_class as $value){
				$li_class .= $value.' ';
			}
			
			$li_class = 'class="'.rtrim($li_class).'"';
		}
		
$element_markup = <<<EOT
		<li id="li_{$element->id}" {$li_style} {$li_class}>
		<span class="description">{$element->title} {$span_required}</span>
		<span>
			<input id="element_{$element->id}_1" name="element_{$element->id}_1" {$attr_readonly} class="element text " size="2" type="text" maxlength="2" value="{$default_value_1}" /> : 
			<label for="element_{$element->id}_1">{$mf_lang['time_hh']}</label>
		</span>
		<span>
			<input id="element_{$element->id}_2" name="element_{$element->id}_2" {$attr_readonly} class="element text " size="2" type="text" maxlength="2" value="{$default_value_2}" /> {$seconds_separator} 
			<label for="element_{$element->id}_2">{$mf_lang['time_mm']}</label>
		</span>
		{$seconds_markup}
		{$am_pm_markup}{$guidelines} {$error_message}
		</li>
EOT;
		
		return $element_markup;
	}
	
	
	//Price
	function mf_display_money($element){
		global $mf_lang;
		
		$li_class = '';
		$li_style = '';
		$error_message = '';
		$span_required = '';
		$attr_readonly = '';
		$guidelines = '';
		$el_class = array('price');
		
		if($element->is_private == 1){
			$el_class[] = 'private';
		}

		//if the hidden property is enabled, hide the field
		if($element->is_private == 2 && !$element->is_design_mode && !$element->is_edit_entry){
			$li_style = 'style="display: none"';
			$el_class[] = 'hidden';
		}

		if(!empty($element->css_class)){
			$el_class[] = trim($element->css_class);
		}
		
		if(!empty($element->is_error)){
			$el_class[] = 'error';
			if($element->error_message != 'error_no_display'){
				$error_message = "<p class=\"error\">{$element->error_message}</p>";
			}
			
		}
		
		
		//check for required
		if($element->is_required){
			$span_required = "<span id=\"required_{$element->id}\" class=\"required\">*</span>";
		}
		
		//check for read-only attribute
		if($element->is_readonly){
			$attr_readonly = 'readonly="readonly"';
		}

		//check for guidelines
		if(!empty($element->guidelines)){
			$guidelines = "<p class=\"guidelines\" id=\"guide_{$element->id}\"><small>{$element->guidelines}</small></p>";
		}
		
		//build the li class
		if(!empty($el_class)){
			foreach ($el_class as $value){
				$li_class .= $value.' ';
			}
			
			$li_class = 'class="'.rtrim($li_class).'"';
		}
		
		if($element->constraint != 'yen'){ 
			if($element->constraint == 'pound'){
				$main_cur  = $mf_lang['price_pound_main'];
				$child_cur = $mf_lang['price_pound_sub'];
				$cur_symbol = '&#163;';
			}elseif ($element->constraint == 'euro'){
				$main_cur  = $mf_lang['price_euro_main'];
				$child_cur = $mf_lang['price_euro_sub'];
				$cur_symbol = '&#8364;';
			}elseif ($element->constraint == 'baht'){
				$main_cur  = $mf_lang['price_baht_main'];
				$child_cur = $mf_lang['price_baht_sub'];
				$cur_symbol = '&#3647;';
			}elseif ($element->constraint == 'rupees'){
				$main_cur  = $mf_lang['price_rupees_main'];
				$child_cur = $mf_lang['price_rupees_sub'];
				$cur_symbol = 'Rs';
			}elseif ($element->constraint == 'rand'){
				$main_cur  = $mf_lang['price_rand_main'];
				$child_cur = $mf_lang['price_rand_sub'];
				$cur_symbol = 'R';
			}elseif ($element->constraint == 'reais'){
				$main_cur  = $mf_lang['price_reais_main'];
				$child_cur = $mf_lang['price_reais_sub'];
				$cur_symbol = 'R&#36;';
			}elseif ($element->constraint == 'forint'){
				$main_cur  = $mf_lang['price_forint_main'];
				$child_cur = $mf_lang['price_forint_sub'];
				$cur_symbol = '&#70;&#116;';
			}elseif ($element->constraint == 'franc'){
				$main_cur  = $mf_lang['price_franc_main'];
				$child_cur = $mf_lang['price_franc_sub'];
				$cur_symbol = 'CHF';
			}elseif ($element->constraint == 'koruna'){
				$main_cur  = $mf_lang['price_koruna_main'];
				$child_cur = $mf_lang['price_koruna_sub'];
				$cur_symbol = '&#75;&#269;';
			}elseif ($element->constraint == 'krona'){
				$main_cur  = $mf_lang['price_krona_main'];
				$child_cur = $mf_lang['price_krona_sub'];
				$cur_symbol = 'kr';
			}elseif ($element->constraint == 'pesos'){
				$main_cur  = $mf_lang['price_pesos_main'];
				$child_cur = $mf_lang['price_pesos_sub'];
				$cur_symbol = '&#36;';
			}elseif ($element->constraint == 'ringgit'){
				$main_cur  = $mf_lang['price_ringgit_main'];
				$child_cur = $mf_lang['price_ringgit_sub'];
				$cur_symbol = 'RM';
			}elseif ($element->constraint == 'zloty'){
				$main_cur  = $mf_lang['price_zloty_main'];
				$child_cur = $mf_lang['price_zloty_sub'];
				$cur_symbol = '&#122;&#322;';
			}elseif ($element->constraint == 'riyals'){
				$main_cur  = $mf_lang['price_riyals_main'];
				$child_cur = $mf_lang['price_riyals_sub'];
				$cur_symbol = '&#65020;';
			}else{ //dollar
				$main_cur  = $mf_lang['price_dollar_main'];
				$child_cur = $mf_lang['price_dollar_sub'];
				$cur_symbol = '$';
			}

			//populate default value
			if(isset($element->default_value)){
				$default_value_1_2 = mf_to_float($element->default_value);
				$default_value_1   = floor($default_value_1_2);

				$exp = array();
				$exp = explode('.', $default_value_1_2 - $default_value_1);
				$default_value_2 = $exp[1];	
			}

			//check for GET parameter to populate default value
			if(isset($_GET['element_'.$element->id.'_1'])){
				$default_value_1 = htmlspecialchars(mf_sanitize($_GET['element_'.$element->id.'_1']),ENT_QUOTES);
			}
			if(isset($_GET['element_'.$element->id.'_2'])){
				$default_value_2 = htmlspecialchars(mf_sanitize($_GET['element_'.$element->id.'_2']),ENT_QUOTES);
			}
			if(isset($_GET['element_'.$element->id])){
				$default_value_1_2 = mf_to_float(htmlspecialchars(mf_sanitize($_GET['element_'.$element->id]),ENT_QUOTES));
				$default_value_1   = floor($default_value_1_2);

				$exp = array();
				$exp = explode('.', $default_value_1_2 - $default_value_1);
				$default_value_2 = $exp[1];	
			}

			//check for populated values, if exist override the default value
			if(!empty($element->populated_value['element_'.$element->id.'_1']['default_value']) || 
			   !empty($element->populated_value['element_'.$element->id.'_2']['default_value'])
			){
				$default_value_1 = '';
				$default_value_2 = '';
				$default_value_1 = $element->populated_value['element_'.$element->id.'_1']['default_value'];
				$default_value_2 = $element->populated_value['element_'.$element->id.'_2']['default_value'];
			}

			if(isset($element->price_definition)){
				$price_value  = $default_value_1.'.'.$default_value_2;
				$price_value  = (double) $price_value;
				
				$price_data_tag = 'data-pricevalue="'.$price_value.'" data-pricefield="money"';
			}		
		
$element_markup = <<<EOT
		<li id="li_{$element->id}" {$li_style} {$li_class} {$price_data_tag}>
		<span class="description">{$element->title} {$span_required}</span>
		<span class="symbol">{$cur_symbol}</span>
		<span>
			<input id="element_{$element->id}_1" name="element_{$element->id}_1" {$attr_readonly} class="element text currency" size="10" value="{$default_value_1}" type="text" /> .		
			<label for="element_{$element->id}_1">{$main_cur}</label>
		</span>
		<span>
			<input id="element_{$element->id}_2" name="element_{$element->id}_2" {$attr_readonly} class="element text" size="2" maxlength="2" value="{$default_value_2}" type="text" />
			<label for="element_{$element->id}_2">{$child_cur}</label>
		</span>
		{$guidelines} {$error_message}
		</li>
EOT;

		}else{ //for yen, only display one textfield
			$main_cur  = $mf_lang['price_yen'];
			$cur_symbol = '&#165;';

			$default_value = $element->default_value;

			//check for GET parameter to populate default value
			if(isset($_GET['element_'.$element->id])){
				$default_value = htmlspecialchars(mf_sanitize($_GET['element_'.$element->id]),ENT_QUOTES);
			}
		
			//check for populated values, if exist override the default value
			if(!empty($element->populated_value['element_'.$element->id]['default_value'])){
				$default_value = '';
				$default_value = $element->populated_value['element_'.$element->id]['default_value'];
			}
			
			if(isset($element->price_definition)){
				$price_value  = $default_value;
				$price_value  = (double) $price_value;
				
				$price_data_tag = 'data-pricevalue="'.$price_value.'" data-pricefield="money_simple"';
			}		
			
$element_markup = <<<EOT
		<li id="li_{$element->id}" {$li_style} {$li_class} {$price_data_tag}>
		<span class="description" for="element_{$element->id}">{$element->title} {$span_required}</span>
		<span class="symbol">{$cur_symbol}</span>
		<span>
			<input id="element_{$element->id}" name="element_{$element->id}" class="element text currency" {$attr_readonly} size="10" value="{$default_value}" type="text" />	
			<label for="element_{$element->id}">{$main_cur}</label>
		</span>
		{$guidelines} {$error_message}
		</li>
EOT;
		
		}



		return $element_markup;
	}
	
	//Section Break
	function mf_display_section($element){
		$li_class = '';
		$li_style = '';
		$el_class = array();
		
		$el_class[] = "section_break";
		
		if($element->is_private == 1){
			$el_class[] = 'private';
		}

		if($element->is_private == 2 && !$element->is_design_mode && !$element->is_edit_entry){
			$li_style = 'style="display: none"';
		}
		
		if(!empty($element->css_class)){
			$el_class[] = trim($element->css_class);
		}

		if(!empty($element->section_enable_scroll)){
			if($element->size == 'large'){
				$el_class[] = 'section_scroll_large';
			}else if($element->size == 'medium'){
				$el_class[] = 'section_scroll_medium';
			}else{
				$el_class[] = 'section_scroll_small';
			}
		}
		
		//build the li class
		if(!empty($el_class)){
			foreach ($el_class as $value){
				$li_class .= $value.' ';
			}
			
			$li_class = 'class="'.rtrim($li_class).'"';
		}
		
		$element->guidelines = $element->guidelines;			
$element_markup = <<<EOT
		<li id="li_{$element->id}" {$li_style} {$li_class}>
			<h3>{$element->title}</h3>
			<p>{$element->guidelines}</p>
		</li>
EOT;
		
		return $element_markup;
	}
	
	//Page Break
	function mf_display_page_break($element){
		
		$firstpage_class = '';
		
		if($element->page_number == 1){
			$firstpage_class = ' firstpage';
		}
		
		if($element->submit_use_image == 1){
			$btn_class = ' hide';
			$image_class = '';
		}else{
			$btn_class = '';
			$image_class = ' hide';
		}
		
		if(empty($element->submit_primary_img)){
			$element->submit_primary_img = 'images/empty.gif';
		}
		
		if(empty($element->submit_secondary_img)){
			$element->submit_secondary_img = 'images/empty.gif';
		}
		
$element_markup = <<<EOT
		<li id="li_{$element->id}" class="page_break{$firstpage_class}" title="Click to edit">
			<div>
				<table class="ap_table_pagination" width="100%" border="0" cellspacing="0" cellpadding="0">
					<tr>
						<td align="left" style="vertical-align: bottom">
							<input type="submit" disabled="disabled" value="{$element->submit_primary_text}" id="btn_submit_{$element->id}" name="btn_submit_{$element->id}" class="btn_primary btn_submit{$btn_class}">
							<input type="submit" disabled="disabled" value="{$element->submit_secondary_text}" id="btn_prev_{$element->id}" name="btn_prev_{$element->id}" class="btn_secondary btn_submit{$btn_class}">
							<input type="image" disabled="disabled" src="{$element->submit_primary_img}" alt="Continue" value="Continue" id="img_submit_{$element->id}" name="img_submit_{$element->id}" class="img_primary img_submit{$image_class}">
							<input type="image" disabled="disabled" src="{$element->submit_secondary_img}" alt="Previous" value="Previous" id="img_prev_{$element->id}" name="img_prev_{$element->id}" class="img_secondary img_submit{$image_class}">
						</td> 
						<td align="center" style="vertical-align: top" width="75px">
							<span id="pagenum_{$element->id}" name="pagenum_{$element->id}" class="ap_tp_num">{$element->page_number}</span>
							<span id="pagetotal_{$element->id}" name="pagetotal_{$element->id}" class="ap_tp_text">Page {$element->page_number} of {$element->page_total}</span>
						</td>
					</tr>
				</table>
			</div>
		</li>	
EOT;
		
		return $element_markup;
	}
	
	
	
	//Number
	function mf_display_number($element){
		
		global $mf_lang;

		//check for error
		$li_class = '';
		$li_style = '';
		$error_message = '';
		$span_required = '';
		$attr_readonly = '';
		$guidelines = '';
		$el_class = array();
		
		if($element->is_private == 1){
			$el_class[] = 'private';
		}

		if($element->is_private == 2 && !$element->is_design_mode && !$element->is_edit_entry){
			$li_style = 'style="display: none"';
		}

		if(!empty($element->css_class)){
			$el_class[] = trim($element->css_class);
		}
		
		if(!empty($element->is_error)){
			$el_class[] = 'error';
			if($element->error_message != 'error_no_display'){
				$error_message = "<p class=\"error\">{$element->error_message}</p>";
			}
		}
		
		//check for required
		if($element->is_required){
			$span_required = "<span id=\"required_{$element->id}\" class=\"required\">*</span>";
		}
		
		//check for read-only attribute
		if($element->is_readonly){
			$attr_readonly = 'readonly="readonly"';
		}

		//check for guidelines
		if(!empty($element->guidelines)){
			$guidelines = "<p class=\"guidelines\" id=\"guide_{$element->id}\"><small>{$element->guidelines}</small></p>";
		}

		//check for placeholder
		$original_default_value = $element->default_value;
		
		//check for GET parameter to populate default value
		if(isset($_GET['element_'.$element->id])){
			$element->default_value = htmlspecialchars(mf_sanitize($_GET['element_'.$element->id]),ENT_QUOTES);
		}

		//check for populated value, if exist, use it instead default_value
		if(isset($element->populated_value['element_'.$element->id]['default_value'])){
			$element->default_value = $element->populated_value['element_'.$element->id]['default_value'];
		}

		//check for placeholder
		if( (!empty($element->enable_placeholder) && !empty($element->default_value) && ($element->default_value == $original_default_value)) ||
			(!empty($element->enable_placeholder) && empty($element->default_value) && !empty($original_default_value))
		 ){
			$attr_placeholder = 'placeholder="'.$original_default_value.'"';
			$element->default_value = '';
		}
		
		$input_handler = '';
		$maxlength = '';
		
		if(!empty($element->range_min) || !empty($element->range_max)){
			$currently_entered_length = 0;
			if(!empty($element->default_value) && $element->range_limit_by == 'd'){
					if(function_exists('mb_strlen')){
						$currently_entered_length = mb_strlen($element->default_value);
					}else{
						$currently_entered_length = strlen($element->default_value);
					}
			}
		}
		
		if($element->range_limit_by == 'd'){
			$range_limit_by = $mf_lang['range_type_digit'];
			
			if(!empty($element->is_design_mode)){
				$range_limit_by = '<var class="range_limit_by">'.$range_limit_by.'</var>';
			}
			
			if(!empty($element->range_min) && !empty($element->range_max)){
				if($element->range_min == $element->range_max){
					$range_min_max_tag = sprintf($mf_lang['range_min_max_same'],"<var id=\"range_max_{$element->id}\">{$element->range_max}</var> {$range_limit_by}");
				}else{
					$range_min_max_tag = sprintf($mf_lang['range_min_max'],"<var id=\"range_min_{$element->id}\">{$element->range_min}</var>","<var id=\"range_max_{$element->id}\">{$element->range_max}</var> {$range_limit_by}");
				}

				$currently_entered_tag = sprintf($mf_lang['range_min_max_entered'],"<var id=\"currently_entered_{$element->id}\">{$currently_entered_length}</var> {$range_limit_by}");

				$range_limit_markup = "<span class=\"label\">{$range_min_max_tag}&nbsp;&nbsp; <em class=\"currently_entered\">{$currently_entered_tag}</em></span>";
				$input_handler = "onkeyup=\"limit_input({$element->id},'{$element->range_limit_by}',{$element->range_max});\" onchange=\"limit_input({$element->id},'{$element->range_limit_by}',{$element->range_max});\"";
				$maxlength = 'maxlength="'.$element->range_max.'"';
			}elseif(!empty($element->range_max)){
				$range_max_tag = sprintf($mf_lang['range_max'],"<var id=\"range_max_{$element->id}\">{$element->range_max}</var> {$range_limit_by}");
				$currently_entered_tag = sprintf($mf_lang['range_max_entered'],"<var id=\"currently_entered_{$element->id}\">{$currently_entered_length}</var> {$range_limit_by}");

				$range_limit_markup = "<span class=\"label\">{$range_max_tag}&nbsp;&nbsp; <em class=\"currently_entered\">{$currently_entered_tag}</em></span>";
				$input_handler = "onkeyup=\"limit_input({$element->id},'{$element->range_limit_by}',{$element->range_max});\" onchange=\"limit_input({$element->id},'{$element->range_limit_by}',{$element->range_max});\"";
				$maxlength = 'maxlength="'.$element->range_max.'"';
			}elseif(!empty($element->range_min)){
				$range_min_tag = sprintf($mf_lang['range_min'],"<var id=\"range_min_{$element->id}\">{$element->range_min}</var> {$range_limit_by}");
				$currently_entered_tag = sprintf($mf_lang['range_min_entered'],"<var id=\"currently_entered_{$element->id}\">{$currently_entered_length}</var> {$range_limit_by}");

				$range_limit_markup = "<span class=\"label\">{$range_min_tag}&nbsp;&nbsp; <em class=\"currently_entered\">{$currently_entered_tag}</em></span>";
				$input_handler = "onkeyup=\"count_input({$element->id},'{$element->range_limit_by}');\" onchange=\"count_input({$element->id},'{$element->range_limit_by}');\"";
			}else{
				$range_limit_markup = '';
			}

			
		}else if($element->range_limit_by == 'v'){
			if(!empty($element->range_min) && !empty($element->range_max)){
				$range_min_max_tag = sprintf($mf_lang['range_number_min_max'],"<var id=\"range_min_{$element->id}\">{$element->range_min}</var>","<var id=\"range_max_{$element->id}\">{$element->range_max}</var>");
				$range_limit_markup = "<span class=\"label\">{$range_min_max_tag}</span>";
			}elseif(!empty($element->range_max)){
				$range_max_tag = sprintf($mf_lang['range_number_max'],"<var id=\"range_max_{$element->id}\">{$element->range_max}</var>");
				$range_limit_markup = "<span class=\"label\">{$range_max_tag}</span>";
			}elseif(!empty($element->range_min)){
				$range_min_tag = sprintf($mf_lang['range_number_min'],"<var id=\"range_min_{$element->id}\">{$element->range_min}</var>");
				$range_limit_markup = "<span class=\"label\">{$range_min_tag}</span>";
			}else{
				$range_limit_markup = '';
			}
		}
		
		if(!empty($element->is_design_mode)){
			$input_handler = '';
		}
		
		//if there is any error message unrelated with range rules, don't display the range markup
		if(!empty($error_message)){
			$range_limit_markup = '';
		}
		
		//build the li class
		if(!empty($el_class)){
			foreach ($el_class as $value){
				$li_class .= $value.' ';
			}
			
			$li_class = 'class="'.rtrim($li_class).'"';
		}

		//build the tag for the quantity link if exist
		if(!empty($element->number_quantity_link)){
			$quantity_link_data_tag = 'data-quantity_link="'.$element->number_quantity_link.'"';
		}
		
$element_markup = <<<EOT
		<li id="li_{$element->id}" {$li_style} {$li_class}>
		<label class="description" for="element_{$element->id}">{$element->title} {$span_required}</label>
		<div>
			<input id="element_{$element->id}" name="element_{$element->id}" class="element text {$element->size}" {$attr_placeholder} {$attr_readonly} type="text" {$maxlength} {$quantity_link_data_tag} value="{$element->default_value}" {$input_handler} /> 
			{$range_limit_markup}
		</div>{$guidelines} {$error_message}
		</li>
EOT;
		
		return $element_markup;
	}
	
	//simple function to return an array of countries
	function mf_get_country_list(){
		$country[0]['label'] = "United States";
		$country[1]['label'] = "United Kingdom";
		$country[2]['label'] = "Canada";
		$country[3]['label'] = "Australia";
		$country[4]['label'] = "Netherlands";
		$country[5]['label'] = "France";
		$country[6]['label'] = "Germany";
		$country[7]['label'] = "-------";
		$country[8]['label'] = "Afghanistan";
		$country[9]['label'] = "Albania";
		$country[10]['label'] = "Algeria";
		$country[11]['label'] = "Andorra";
		$country[12]['label'] = "Antigua and Barbuda";
		$country[13]['label'] = "Argentina";
		$country[14]['label'] = "Armenia";
		$country[15]['label'] = "Austria";
		$country[16]['label'] = "Azerbaijan";
		$country[17]['label'] = "Bahamas";
		$country[18]['label'] = "Bahrain";
		$country[19]['label'] = "Bangladesh";
		$country[20]['label'] = "Barbados";
		$country[21]['label'] = "Belarus";
		$country[22]['label'] = "Belgium";
		$country[23]['label'] = "Belize";
		$country[24]['label'] = "Benin";
		$country[25]['label'] = "Bhutan";
		$country[26]['label'] = "Bolivia";
		$country[27]['label'] = "Bosnia and Herzegovina";
		$country[28]['label'] = "Botswana";
		$country[29]['label'] = "Brazil";
		$country[30]['label'] = "Brunei";
		$country[31]['label'] = "Bulgaria";
		$country[32]['label'] = "Burkina Faso";
		$country[33]['label'] = "Burundi";
		$country[34]['label'] = "Cambodia";
		$country[35]['label'] = "Cameroon";	
		$country[36]['label'] = "Cape Verde";
		$country[37]['label'] = "Cayman Islands";
		$country[38]['label'] = "Central African Republic";
		$country[39]['label'] = "Chad";
		$country[40]['label'] = "Chile";
		$country[41]['label'] = "China";
		$country[42]['label'] = "Colombia";
		$country[43]['label'] = "Comoros";
		$country[44]['label'] = "Congo";
		$country[45]['label'] = "Costa Rica";
		$country[46]['label'] = "Côte d'Ivoire";
		$country[47]['label'] = "Croatia";
		$country[48]['label'] = "Cuba";
		$country[49]['label'] = "Cyprus";
		$country[50]['label'] = "Czech Republic";
		$country[51]['label'] = "Denmark";
		$country[52]['label'] = "Djibouti";
		$country[53]['label'] = "Dominica";
		$country[54]['label'] = "Dominican Republic";
		$country[55]['label'] = "East Timor";
		$country[56]['label'] = "Ecuador";
		$country[57]['label'] = "Egypt";
		$country[58]['label'] = "El Salvador";
		$country[59]['label'] = "Equatorial Guinea";
		$country[60]['label'] = "Eritrea";
		$country[61]['label'] = "Estonia";
		$country[62]['label'] = "Ethiopia";
		$country[63]['label'] = "Fiji";
		$country[64]['label'] = "Finland";
		$country[65]['label'] = "Gabon";
		$country[66]['label'] = "Gambia";
		$country[67]['label'] = "Georgia";
		$country[68]['label'] = "Ghana";
		$country[69]['label'] = "Greece";
		$country[70]['label'] = "Grenada";
		$country[71]['label'] = "Guatemala";
		$country[72]['label'] = "Guernsey";
		$country[73]['label'] = "Guinea";
		$country[74]['label'] = "Guinea-Bissau";
		$country[75]['label'] = "Guyana";
		$country[76]['label'] = "Haiti";
		$country[77]['label'] = "Honduras";
		$country[78]['label'] = "Hong Kong";
		$country[79]['label'] = "Hungary";
		$country[80]['label'] = "Iceland";
		$country[81]['label'] = "India";
		$country[82]['label'] = "Indonesia";
		$country[83]['label'] = "Iran";
		$country[84]['label'] = "Iraq";
		$country[85]['label'] = "Ireland";
		$country[86]['label'] = "Israel";
		$country[87]['label'] = "Italy";
		$country[88]['label'] = "Jamaica";
		$country[89]['label'] = "Japan";
		$country[90]['label'] = "Jordan";
		$country[91]['label'] = "Kazakhstan";
		$country[92]['label'] = "Kenya";
		$country[93]['label'] = "Kiribati";
		$country[94]['label'] = "North Korea";
		$country[95]['label'] = "South Korea";
		$country[96]['label'] = "Kuwait";
		$country[97]['label'] = "Kyrgyzstan";
		$country[98]['label'] = "Laos";
		$country[99]['label'] = "Latvia";
		$country[100]['label'] = "Lebanon";
		$country[101]['label'] = "Lesotho";
		$country[102]['label'] = "Liberia";
		$country[103]['label'] = "Libya";
		$country[104]['label'] = "Liechtenstein";
		$country[105]['label'] = "Lithuania";
		$country[106]['label'] = "Luxembourg";
		$country[107]['label'] = "Macedonia";
		$country[108]['label'] = "Madagascar";
		$country[109]['label'] = "Malawi";
		$country[110]['label'] = "Malaysia";
		$country[111]['label'] = "Maldives";
		$country[112]['label'] = "Mali";
		$country[113]['label'] = "Malta";
		$country[114]['label'] = "Marshall Islands";
		$country[115]['label'] = "Mauritania";
		$country[116]['label'] = "Mauritius";
		$country[117]['label'] = "Mexico";
		$country[118]['label'] = "Micronesia";
		$country[119]['label'] = "Moldova";
		$country[120]['label'] = "Monaco";
		$country[121]['label'] = "Mongolia";
		$country[122]['label'] = "Montenegro";
		$country[123]['label'] = "Morocco";
		$country[124]['label'] = "Mozambique";
		$country[125]['label'] = "Myanmar";
		$country[126]['label'] = "Namibia";
		$country[127]['label'] = "Nauru";
		$country[128]['label'] = "Nepal";
		$country[129]['label'] = "New Zealand";
		$country[130]['label'] = "Nicaragua";
		$country[131]['label'] = "Niger";
		$country[132]['label'] = "Nigeria";
		$country[133]['label'] = "Norway";
		$country[134]['label'] = "Oman";
		$country[135]['label'] = "Pakistan";
		$country[136]['label'] = "Palau";
		$country[137]['label'] = "Panama";
		$country[138]['label'] = "Papua New Guinea";
		$country[139]['label'] = "Paraguay";
		$country[140]['label'] = "Peru";
		$country[141]['label'] = "Philippines";
		$country[142]['label'] = "Poland";
		$country[143]['label'] = "Portugal";
		$country[144]['label'] = "Puerto Rico";
		$country[145]['label'] = "Qatar";
		$country[146]['label'] = "Romania";
		$country[147]['label'] = "Russia";
		$country[148]['label'] = "Rwanda";
		$country[149]['label'] = "Saint Kitts and Nevis";
		$country[150]['label'] = "Saint Lucia";
		$country[151]['label'] = "Saint Vincent and the Grenadines";
		$country[152]['label'] = "Samoa";
		$country[153]['label'] = "San Marino";
		$country[154]['label'] = "Sao Tome and Principe";
		$country[155]['label'] = "Saudi Arabia";
		$country[156]['label'] = "Senegal";
		$country[157]['label'] = "Serbia and Montenegro";
		$country[158]['label'] = "Seychelles";
		$country[159]['label'] = "Sierra Leone";
		$country[160]['label'] = "Singapore";
		$country[161]['label'] = "Slovakia";
		$country[162]['label'] = "Slovenia";
		$country[163]['label'] = "Solomon Islands";
		$country[164]['label'] = "Somalia";
		$country[165]['label'] = "South Africa";
		$country[166]['label'] = "Spain";
		$country[167]['label'] = "Sri Lanka";
		$country[168]['label'] = "Sudan";
		$country[169]['label'] = "Suriname";
		$country[170]['label'] = "Swaziland";
		$country[171]['label'] = "Sweden";
		$country[172]['label'] = "Switzerland";
		$country[173]['label'] = "Syria";
		$country[174]['label'] = "Taiwan";
		$country[175]['label'] = "Tajikistan";
		$country[176]['label'] = "Tanzania";
		$country[177]['label'] = "Thailand";
		$country[178]['label'] = "Togo";
		$country[179]['label'] = "Tonga";
		$country[180]['label'] = "Trinidad and Tobago";
		$country[181]['label'] = "Tunisia";
		$country[182]['label'] = "Turkey";
		$country[183]['label'] = "Turkmenistan";
		$country[184]['label'] = "Tuvalu";
		$country[185]['label'] = "Uganda";
		$country[186]['label'] = "Ukraine";
		$country[187]['label'] = "United Arab Emirates";
		$country[188]['label'] = "Uruguay";
		$country[189]['label'] = "Uzbekistan";
		$country[190]['label'] = "Vanuatu";
		$country[191]['label'] = "Vatican City";
		$country[192]['label'] = "Venezuela";
		$country[193]['label'] = "Vietnam";
		$country[194]['label'] = "Yemen";
		$country[195]['label'] = "Zambia";
		$country[196]['label'] = "Zimbabwe";

		$country[0]['value'] = "United States";
		$country[1]['value'] = "United Kingdom";
		$country[2]['value'] = "Canada";
		$country[3]['value'] = "Australia";
		$country[4]['value'] = "Netherlands";
		$country[5]['value'] = "France";
		$country[6]['value'] = "Germany";
		$country[7]['value'] = "-------";
		$country[8]['value'] = "Afghanistan";
		$country[9]['value'] = "Albania";
		$country[10]['value'] = "Algeria";
		$country[11]['value'] = "Andorra";
		$country[12]['value'] = "Antigua and Barbuda";
		$country[13]['value'] = "Argentina";
		$country[14]['value'] = "Armenia";
		$country[15]['value'] = "Austria";
		$country[16]['value'] = "Azerbaijan";
		$country[17]['value'] = "Bahamas";
		$country[18]['value'] = "Bahrain";
		$country[19]['value'] = "Bangladesh";
		$country[20]['value'] = "Barbados";
		$country[21]['value'] = "Belarus";
		$country[22]['value'] = "Belgium";
		$country[23]['value'] = "Belize";
		$country[24]['value'] = "Benin";
		$country[25]['value'] = "Bhutan";
		$country[26]['value'] = "Bolivia";
		$country[27]['value'] = "Bosnia and Herzegovina";
		$country[28]['value'] = "Botswana";
		$country[29]['value'] = "Brazil";
		$country[30]['value'] = "Brunei";
		$country[31]['value'] = "Bulgaria";
		$country[32]['value'] = "Burkina Faso";
		$country[33]['value'] = "Burundi";
		$country[34]['value'] = "Cambodia";
		$country[35]['value'] = "Cameroon";	
		$country[36]['value'] = "Cape Verde";
		$country[37]['value'] = "Cayman Islands";
		$country[38]['value'] = "Central African Republic";
		$country[39]['value'] = "Chad";
		$country[40]['value'] = "Chile";
		$country[41]['value'] = "China";
		$country[42]['value'] = "Colombia";
		$country[43]['value'] = "Comoros";
		$country[44]['value'] = "Congo";
		$country[45]['value'] = "Costa Rica";
		$country[46]['value'] = "Côte d'Ivoire";
		$country[47]['value'] = "Croatia";
		$country[48]['value'] = "Cuba";
		$country[49]['value'] = "Cyprus";
		$country[50]['value'] = "Czech Republic";
		$country[51]['value'] = "Denmark";
		$country[52]['value'] = "Djibouti";
		$country[53]['value'] = "Dominica";
		$country[54]['value'] = "Dominican Republic";
		$country[55]['value'] = "East Timor";
		$country[56]['value'] = "Ecuador";
		$country[57]['value'] = "Egypt";
		$country[58]['value'] = "El Salvador";
		$country[59]['value'] = "Equatorial Guinea";
		$country[60]['value'] = "Eritrea";
		$country[61]['value'] = "Estonia";
		$country[62]['value'] = "Ethiopia";
		$country[63]['value'] = "Fiji";
		$country[64]['value'] = "Finland";
		$country[65]['value'] = "Gabon";
		$country[66]['value'] = "Gambia";
		$country[67]['value'] = "Georgia";
		$country[68]['value'] = "Ghana";
		$country[69]['value'] = "Greece";
		$country[70]['value'] = "Grenada";
		$country[71]['value'] = "Guatemala";
		$country[72]['value'] = "Guernsey";
		$country[73]['value'] = "Guinea";
		$country[74]['value'] = "Guinea-Bissau";
		$country[75]['value'] = "Guyana";
		$country[76]['value'] = "Haiti";
		$country[77]['value'] = "Honduras";
		$country[78]['value'] = "Hong Kong";
		$country[79]['value'] = "Hungary";
		$country[80]['value'] = "Iceland";
		$country[81]['value'] = "India";
		$country[82]['value'] = "Indonesia";
		$country[83]['value'] = "Iran";
		$country[84]['value'] = "Iraq";
		$country[85]['value'] = "Ireland";
		$country[86]['value'] = "Israel";
		$country[87]['value'] = "Italy";
		$country[88]['value'] = "Jamaica";
		$country[89]['value'] = "Japan";
		$country[90]['value'] = "Jordan";
		$country[91]['value'] = "Kazakhstan";
		$country[92]['value'] = "Kenya";
		$country[93]['value'] = "Kiribati";
		$country[94]['value'] = "North Korea";
		$country[95]['value'] = "South Korea";
		$country[96]['value'] = "Kuwait";
		$country[97]['value'] = "Kyrgyzstan";
		$country[98]['value'] = "Laos";
		$country[99]['value'] = "Latvia";
		$country[100]['value'] = "Lebanon";
		$country[101]['value'] = "Lesotho";
		$country[102]['value'] = "Liberia";
		$country[103]['value'] = "Libya";
		$country[104]['value'] = "Liechtenstein";
		$country[105]['value'] = "Lithuania";
		$country[106]['value'] = "Luxembourg";
		$country[107]['value'] = "Macedonia";
		$country[108]['value'] = "Madagascar";
		$country[109]['value'] = "Malawi";
		$country[110]['value'] = "Malaysia";
		$country[111]['value'] = "Maldives";
		$country[112]['value'] = "Mali";
		$country[113]['value'] = "Malta";
		$country[114]['value'] = "Marshall Islands";
		$country[115]['value'] = "Mauritania";
		$country[116]['value'] = "Mauritius";
		$country[117]['value'] = "Mexico";
		$country[118]['value'] = "Micronesia";
		$country[119]['value'] = "Moldova";
		$country[120]['value'] = "Monaco";
		$country[121]['value'] = "Mongolia";
		$country[122]['value'] = "Montenegro";
		$country[123]['value'] = "Morocco";
		$country[124]['value'] = "Mozambique";
		$country[125]['value'] = "Myanmar";
		$country[126]['value'] = "Namibia";
		$country[127]['value'] = "Nauru";
		$country[128]['value'] = "Nepal";
		$country[129]['value'] = "New Zealand";
		$country[130]['value'] = "Nicaragua";
		$country[131]['value'] = "Niger";
		$country[132]['value'] = "Nigeria";
		$country[133]['value'] = "Norway";
		$country[134]['value'] = "Oman";
		$country[135]['value'] = "Pakistan";
		$country[136]['value'] = "Palau";
		$country[137]['value'] = "Panama";
		$country[138]['value'] = "Papua New Guinea";
		$country[139]['value'] = "Paraguay";
		$country[140]['value'] = "Peru";
		$country[141]['value'] = "Philippines";
		$country[142]['value'] = "Poland";
		$country[143]['value'] = "Portugal";
		$country[144]['value'] = "Puerto Rico";
		$country[145]['value'] = "Qatar";
		$country[146]['value'] = "Romania";
		$country[147]['value'] = "Russia";
		$country[148]['value'] = "Rwanda";
		$country[149]['value'] = "Saint Kitts and Nevis";
		$country[150]['value'] = "Saint Lucia";
		$country[151]['value'] = "Saint Vincent and the Grenadines";
		$country[152]['value'] = "Samoa";
		$country[153]['value'] = "San Marino";
		$country[154]['value'] = "Sao Tome and Principe";
		$country[155]['value'] = "Saudi Arabia";
		$country[156]['value'] = "Senegal";
		$country[157]['value'] = "Serbia and Montenegro";
		$country[158]['value'] = "Seychelles";
		$country[159]['value'] = "Sierra Leone";
		$country[160]['value'] = "Singapore";
		$country[161]['value'] = "Slovakia";
		$country[162]['value'] = "Slovenia";
		$country[163]['value'] = "Solomon Islands";
		$country[164]['value'] = "Somalia";
		$country[165]['value'] = "South Africa";
		$country[166]['value'] = "Spain";
		$country[167]['value'] = "Sri Lanka";
		$country[168]['value'] = "Sudan";
		$country[169]['value'] = "Suriname";
		$country[170]['value'] = "Swaziland";
		$country[171]['value'] = "Sweden";
		$country[172]['value'] = "Switzerland";
		$country[173]['value'] = "Syria";
		$country[174]['value'] = "Taiwan";
		$country[175]['value'] = "Tajikistan";
		$country[176]['value'] = "Tanzania";
		$country[177]['value'] = "Thailand";
		$country[178]['value'] = "Togo";
		$country[179]['value'] = "Tonga";
		$country[180]['value'] = "Trinidad and Tobago";
		$country[181]['value'] = "Tunisia";
		$country[182]['value'] = "Turkey";
		$country[183]['value'] = "Turkmenistan";
		$country[184]['value'] = "Tuvalu";
		$country[185]['value'] = "Uganda";
		$country[186]['value'] = "Ukraine";
		$country[187]['value'] = "United Arab Emirates";
		$country[188]['value'] = "Uruguay";
		$country[189]['value'] = "Uzbekistan";
		$country[190]['value'] = "Vanuatu";
		$country[191]['value'] = "Vatican City";
		$country[192]['value'] = "Venezuela";
		$country[193]['value'] = "Vietnam";
		$country[194]['value'] = "Yemen";
		$country[195]['value'] = "Zambia";
		$country[196]['value'] = "Zimbabwe";

		return $country;
	}
	
	
	//Address
	function mf_display_address($element){
		
		$country = mf_get_country_list();

		$state_list[0]['label'] = 'Alabama';
		$state_list[1]['label'] = 'Alaska';
		$state_list[2]['label'] = 'Arizona';
		$state_list[3]['label'] = 'Arkansas';
		$state_list[4]['label'] = 'California';
		$state_list[5]['label'] = 'Colorado';
		$state_list[6]['label'] = 'Connecticut';
		$state_list[7]['label'] = 'Delaware';
		$state_list[8]['label'] = 'District of Columbia';
		$state_list[9]['label'] = 'Florida';
		$state_list[10]['label'] = 'Georgia';
		$state_list[11]['label'] = 'Hawaii';
		$state_list[12]['label'] = 'Idaho';
		$state_list[13]['label'] = 'Illinois';
		$state_list[14]['label'] = 'Indiana';
		$state_list[15]['label'] = 'Iowa';
		$state_list[16]['label'] = 'Kansas';
		$state_list[17]['label'] = 'Kentucky';
		$state_list[18]['label'] = 'Louisiana';
		$state_list[19]['label'] = 'Maine';
		$state_list[20]['label'] = 'Maryland';
		$state_list[21]['label'] = 'Massachusetts';
		$state_list[22]['label'] = 'Michigan';
		$state_list[23]['label'] = 'Minnesota';
		$state_list[24]['label'] = 'Mississippi';
		$state_list[25]['label'] = 'Missouri';
		$state_list[26]['label'] = 'Montana';
		$state_list[27]['label'] = 'Nebraska';
		$state_list[28]['label'] = 'Nevada';
		$state_list[29]['label'] = 'New Hampshire';
		$state_list[30]['label'] = 'New Jersey';
		$state_list[31]['label'] = 'New Mexico';
		$state_list[32]['label'] = 'New York';
		$state_list[33]['label'] = 'North Carolina';
		$state_list[34]['label'] = 'North Dakota';
		$state_list[35]['label'] = 'Ohio';
		$state_list[36]['label'] = 'Oklahoma';
		$state_list[37]['label'] = 'Oregon';
		$state_list[38]['label'] = 'Pennsylvania';
		$state_list[39]['label'] = 'Rhode Island';
		$state_list[40]['label'] = 'South Carolina';
		$state_list[41]['label'] = 'South Dakota';
		$state_list[42]['label'] = 'Tennessee';
		$state_list[43]['label'] = 'Texas';
		$state_list[44]['label'] = 'Utah';
		$state_list[45]['label'] = 'Vermont';
		$state_list[46]['label'] = 'Virginia';
		$state_list[47]['label'] = 'Washington';
		$state_list[48]['label'] = 'West Virginia';
		$state_list[49]['label'] = 'Wisconsin';
		$state_list[50]['label'] = 'Wyoming';

			
		$state_list[0]['value'] = 'Alabama';
		$state_list[1]['value'] = 'Alaska';
		$state_list[2]['value'] = 'Arizona';
		$state_list[3]['value'] = 'Arkansas';
		$state_list[4]['value'] = 'California';
		$state_list[5]['value'] = 'Colorado';
		$state_list[6]['value'] = 'Connecticut';
		$state_list[7]['value'] = 'Delaware';
		$state_list[8]['value'] = 'District of Columbia';
		$state_list[9]['value'] = 'Florida';
		$state_list[10]['value'] = 'Georgia';
		$state_list[11]['value'] = 'Hawaii';
		$state_list[12]['value'] = 'Idaho';
		$state_list[13]['value'] = 'Illinois';
		$state_list[14]['value'] = 'Indiana';
		$state_list[15]['value'] = 'Iowa';
		$state_list[16]['value'] = 'Kansas';
		$state_list[17]['value'] = 'Kentucky';
		$state_list[18]['value'] = 'Louisiana';
		$state_list[19]['value'] = 'Maine';
		$state_list[20]['value'] = 'Maryland';
		$state_list[21]['value'] = 'Massachusetts';
		$state_list[22]['value'] = 'Michigan';
		$state_list[23]['value'] = 'Minnesota';
		$state_list[24]['value'] = 'Mississippi';
		$state_list[25]['value'] = 'Missouri';
		$state_list[26]['value'] = 'Montana';
		$state_list[27]['value'] = 'Nebraska';
		$state_list[28]['value'] = 'Nevada';
		$state_list[29]['value'] = 'New Hampshire';
		$state_list[30]['value'] = 'New Jersey';
		$state_list[31]['value'] = 'New Mexico';
		$state_list[32]['value'] = 'New York';
		$state_list[33]['value'] = 'North Carolina';
		$state_list[34]['value'] = 'North Dakota';
		$state_list[35]['value'] = 'Ohio';
		$state_list[36]['value'] = 'Oklahoma';
		$state_list[37]['value'] = 'Oregon';
		$state_list[38]['value'] = 'Pennsylvania';
		$state_list[39]['value'] = 'Rhode Island';
		$state_list[40]['value'] = 'South Carolina';
		$state_list[41]['value'] = 'South Dakota';
		$state_list[42]['value'] = 'Tennessee';
		$state_list[43]['value'] = 'Texas';
		$state_list[44]['value'] = 'Utah';
		$state_list[45]['value'] = 'Vermont';
		$state_list[46]['value'] = 'Virginia';
		$state_list[47]['value'] = 'Washington';
		$state_list[48]['value'] = 'West Virginia';
		$state_list[49]['value'] = 'Wisconsin';
		$state_list[50]['value'] = 'Wyoming';
		
		global $mf_lang;
		
		$li_class = '';
		$li_style = '';
		$error_message = '';
		$span_required = '';
		$attr_readonly = '';
		$guidelines = '';
		
		$el_class = array();
		
		$el_class[] = 'address';
		
		if($element->is_private == 1){
			$el_class[] = 'private';
		}

		if($element->is_private == 2 && !$element->is_design_mode && !$element->is_edit_entry){
			$li_style = 'style="display: none"';
		}

		if(!empty($element->css_class)){
			$el_class[] = trim($element->css_class);
		}
		
		if(!empty($element->is_error)){
			$el_class[] = 'error';
			if($element->error_message != 'error_no_display'){
				$error_message = "<p class=\"error\">{$element->error_message}</p>";
			}
		}
		
		//check for required
		if($element->is_required){
			$span_required = "<span id=\"required_{$element->id}\" class=\"required\">*</span>";
		}
		
		//check for read-only attribute
		if($element->is_readonly){
			$attr_readonly = 'readonly="readonly"';
		}

		//check for guidelines
		if(!empty($element->guidelines)){
			$guidelines = "<p class=\"guidelines\" id=\"guide_{$element->id}\"><small>{$element->guidelines}</small></p>";
		}
		
		if(!empty($element->default_value)){
			$default_value_6 = $element->default_value;
		}

		//check for GET parameter to populate default value
		if(isset($_GET['element_'.$element->id.'_1'])){
			$default_value_1 = htmlspecialchars(mf_sanitize($_GET['element_'.$element->id.'_1']),ENT_QUOTES);
		}
		if(isset($_GET['element_'.$element->id.'_2'])){
			$default_value_2 = htmlspecialchars(mf_sanitize($_GET['element_'.$element->id.'_2']),ENT_QUOTES);
		}
		if(isset($_GET['element_'.$element->id.'_3'])){
			$default_value_3 = htmlspecialchars(mf_sanitize($_GET['element_'.$element->id.'_3']),ENT_QUOTES);
		}
		if(isset($_GET['element_'.$element->id.'_4'])){
			$default_value_4 = htmlspecialchars(mf_sanitize($_GET['element_'.$element->id.'_4']),ENT_QUOTES);
		}
		if(isset($_GET['element_'.$element->id.'_5'])){
			$default_value_5 = htmlspecialchars(mf_sanitize($_GET['element_'.$element->id.'_5']),ENT_QUOTES);
		}
		if(isset($_GET['element_'.$element->id.'_6'])){
			$default_value_6 = htmlspecialchars(mf_sanitize($_GET['element_'.$element->id.'_6']),ENT_QUOTES);
		}
		
		//check for populated values, if exist override the default value
		if(!empty($element->populated_value['element_'.$element->id.'_1']['default_value']) || 
		   !empty($element->populated_value['element_'.$element->id.'_2']['default_value']) ||
		   !empty($element->populated_value['element_'.$element->id.'_3']['default_value']) ||
		   !empty($element->populated_value['element_'.$element->id.'_4']['default_value']) ||
		   !empty($element->populated_value['element_'.$element->id.'_5']['default_value']) ||
		   !empty($element->populated_value['element_'.$element->id.'_6']['default_value'])
		){
			$default_value_1 = '';
			$default_value_2 = '';
			$default_value_3 = '';
			$default_value_4 = '';
			$default_value_5 = '';
			$default_value_1 = $element->populated_value['element_'.$element->id.'_1']['default_value'];
			$default_value_2 = $element->populated_value['element_'.$element->id.'_2']['default_value'];
			$default_value_3 = $element->populated_value['element_'.$element->id.'_3']['default_value'];
			$default_value_4 = $element->populated_value['element_'.$element->id.'_4']['default_value'];
			$default_value_5 = $element->populated_value['element_'.$element->id.'_5']['default_value'];
			$default_value_6 = $element->populated_value['element_'.$element->id.'_6']['default_value'];
		}
		
		//create country markup, if no default value, provide a blank option
		if(!empty($element->address_us_only)){
			$default_value_6 = 'United States';
		}
		
		if(empty($default_value_6)){
			$country_markup = '<option value="" selected="selected"></option>'."\n";
		}else{
			$country_markup = '';
		}
		

		foreach ($country as $data){
			if(!empty($data['value']) && $data['value'] == $default_value_6){
				$selected = 'selected="selected"';
				$selected_country = $data['value'];
			}else{
				$selected = '';
			}
			
			$country_markup .= "<option value=\"{$data['value']}\" {$selected}>{$data['label']}</option>\n";
		}

		//disable country dropdown and replace it with hidden fields when the property is read-only
		if($element->is_readonly){
			$country_disabled_attr = 'disabled="disabled"';
			$country_hidden_field_markup = "<input type=\"hidden\" name=\"element_{$element->id}_6\" value=\"{$selected_country}\" />";
		}

		//if this address field is restricted to US only
		if(empty($element->is_design_mode) && !empty($element->address_us_only)){
			$country_markup = '<option selected="selected" value="United States">United States</option>';
		}
		
		//decide which state markup being used
		if(empty($element->address_us_only)){
			//display simple input for the state
			$state_markup = "<input id=\"element_{$element->id}_4\" name=\"element_{$element->id}_4\" class=\"element text large\" {$attr_readonly}  value=\"{$default_value_4}\" type=\"text\" />";
		}else{

			if($element->is_readonly){
				$state_disabled_attr   = 'disabled="disabled"';
			}

			//display us state dropdown
			$state_markup = "<select class=\"element select large\" id=\"element_{$element->id}_4\" name=\"element_{$element->id}_4\" {$state_disabled_attr}>";
			$state_markup .= '<option value="" selected="selected">Select a State</option>'."\n";
			
			foreach ($state_list as $data){
				if($data['value'] == $default_value_4){
					$selected = 'selected="selected"';
					$selected_state = $data['value'];
				}else{
					$selected = '';
				}
				
				$state_markup .= "<option value=\"{$data['value']}\" {$selected}>{$data['label']}</option>\n";
			}
			
			$state_markup .= "</select>";
			
			if($element->is_readonly){
				$state_hidden_field_markup = "<input type=\"hidden\" name=\"element_{$element->id}_4\" value=\"{$selected_state}\" />";
				$state_markup .= $state_hidden_field_markup;
			}
		}

		
		//set the 'address line 2' visibility, based on selected option
		if(!empty($element->address_hideline2)){
			$address_line2_style = 'style="display: none"';
		}else{
			$address_line2_style = '';
		}
		
		//build the li class
		if(!empty($el_class)){
			foreach ($el_class as $value){
				$li_class .= $value.' ';
			}
			
			$li_class = 'class="'.rtrim($li_class).'"';
		}
		
$element_markup = <<<EOT
		<li id="li_{$element->id}" {$li_style} {$li_class}>
		<span class="description">{$element->title} {$span_required}</span>
		
		<div>
			<span id="li_{$element->id}_span_1">
				<input id="element_{$element->id}_1" name="element_{$element->id}_1" class="element text large" {$attr_readonly} value="{$default_value_1}" type="text" />
				<label for="element_{$element->id}_1">{$mf_lang['address_street']}</label>
			</span>
		
			<span id="li_{$element->id}_span_2" {$address_line2_style}>
				<input id="element_{$element->id}_2" name="element_{$element->id}_2" class="element text large" {$attr_readonly} value="{$default_value_2}" type="text" />
				<label for="element_{$element->id}_2">{$mf_lang['address_street2']}</label>
			</span>
		
			<span id="li_{$element->id}_span_3" class="left state_list">
				<input id="element_{$element->id}_3" name="element_{$element->id}_3" class="element text large" {$attr_readonly} value="{$default_value_3}" type="text" />
				<label for="element_{$element->id}_3">{$mf_lang['address_city']}</label>
			</span>
		
			<span id="li_{$element->id}_span_4" class="right state_list">
				{$state_markup}
				<label for="element_{$element->id}_4">{$mf_lang['address_state']}</label>
			</span>
		
			<span id="li_{$element->id}_span_5" class="left">
				<input id="element_{$element->id}_5" name="element_{$element->id}_5" class="element text large" {$attr_readonly} maxlength="15" value="{$default_value_5}" type="text" />
				<label for="element_{$element->id}_5">{$mf_lang['address_zip']}</label>
			</span>
			
			<span id="li_{$element->id}_span_6" class="right">
				<select class="element select large" id="element_{$element->id}_6" name="element_{$element->id}_6" {$country_disabled_attr}> 
				{$country_markup}	
				</select>
			<label for="element_{$element->id}_6">{$mf_lang['address_country']}</label>
		    </span>
		    {$country_hidden_field_markup}
	    </div>{$guidelines} {$error_message}
		</li>
EOT;
		
	
		return $element_markup;
	}
	
	
	//Captcha
	function mf_display_captcha($element){
		
		if(!empty($element->error_message)){
			$error_code = $element->error_message;
		}else{
			$error_code = '';
		}
					
		//check for error
		$error_class = '';
		$error_message = '';
		$span_required = '';
		$guidelines = '';
		global $mf_lang;		
		
		if(!empty($element->is_error)){
			if($element->error_message == 'el-required'){
				$element->error_message = $mf_lang['captcha_required'];
				$error_code = '';	
			}else if($element->error_message == 'el-text-required'){
				$element->error_message = $mf_lang['val_required'];
				$error_code = '';	
			}elseif ($element->error_message == 'incorrect-captcha-sol'){
				$element->error_message = $mf_lang['captcha_mismatch'];
			}elseif ($element->error_message == 'incorrect-text-captcha-sol'){
				$element->error_message = $mf_lang['captcha_text_mismatch'];
			}else{
				$element->error_message = "{$mf_lang['captcha_error']} ({$element->error_message})";
			}
			
			$error_class = 'class="error"';
			$error_message = "<p class=\"error\">{$element->error_message}</p>";
		}

		
		if(!empty($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] != 'off')){
			$use_ssl = true;
		}else if(isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https'){
			$use_ssl = true;
		}else if (isset($_SERVER['HTTP_FRONT_END_HTTPS']) && $_SERVER['HTTP_FRONT_END_HTTPS'] == 'on'){
			$use_ssl = true;
		}else{
			$use_ssl = false;
		}
		
		
		if($element->captcha_type == 'i'){ //if this is internal captcha type
		
			$machform_path = '';
			if(!empty($element->machform_path)){
				$machform_path = $element->machform_path;
			}
			
			$timestamp = time(); //use this as paramater for captcha.php, to prevent caching
			
			$element->title = $mf_lang['captcha_simple_image_title'];
$captcha_html = <<<EOT
<img id="captcha_image" src="{$machform_path}captcha.php?t={$timestamp}" width="200" height="60" alt="Please refresh your browser to see this image." /><br />
<input id="captcha_response_field" name="captcha_response_field" class="element text small" type="text" /><div id="dummy_captcha_internal"></div>
EOT;
	 		
		}else if($element->captcha_type == 'r'){ //if this is recaptcha
			$captcha_html = recaptcha_get_html(RECAPTCHA_PUBLIC_KEY, $error_code,$use_ssl);
	
			if($captcha_html === false){
				$domain = str_replace('www.','',$_SERVER['SERVER_NAME']);
				$captcha_html = "<b>Error!</b> You have enabled CAPTCHA but no API key available. <br /><br />To use CAPTCHA you must get an API key from <a href='".recaptcha_get_signup_url($domain,'MachForm')."'>http://recaptcha.net/api/getkey</a><br /><br />After getting the API key, save them into your <b>config.php</b> file.";
				$error_class = 'class="error"';
			}else{
				$recaptcha_post_message = <<<EOT
<script type="text/javascript">
    $(function(){
    	$.postMessage({mf_iframe_height: $('body').outerHeight(true) + 130}, '*', parent );
    });
</script>
EOT;
				//manually add 130px padding for recaptcha, since google is building the captcha after the dom is loaded
				$captcha_html .= "\n".$recaptcha_post_message; 
			}

			$recaptcha_theme = RECAPTCHA_THEME;
			$recaptcha_language = RECAPTCHA_LANGUAGE;
			$recaptcha_theme_init = <<<EOT
				<script type="text/javascript">
				 var RecaptchaOptions = {
				    theme : '{$recaptcha_theme}',
				    lang: '{$recaptcha_language}'
				 };
				</script>
EOT;

		}else if($element->captcha_type == 'n'){ //if this is reCAPTCHA V2 (No CAPTCHA)
			
			if(empty($element->recaptcha_site_key) || empty($element->recaptcha_secret_key)){
				$domain = str_replace('www.','',$_SERVER['SERVER_NAME']);
				$captcha_html = "<b>Error!</b> You have enabled reCAPTCHA V2 but your API keys are missing. <br /><br />To use reCAPTCHA V2 you must get API keys from <a href=\"https://www.google.com/recaptcha/admin\">https://www.google.com/recaptcha/admin</a><br /><br />After getting the API keys, save them into your Settings page.";
				$error_class = 'class="error"';
			}else{
				$captcha_html = '<div class="g-recaptcha" data-sitekey="'.$element->recaptcha_site_key.'"></div>';
				$recaptcha_post_message = <<<EOT
<script type="text/javascript">
    $(function(){
    	$.postMessage({mf_iframe_height: $('body').outerHeight(true) + 130}, '*', parent );
    });
</script>
EOT;
				//manually add 130px padding for recaptcha, since google is building the captcha after the dom is loaded
				$captcha_html .= "\n".$recaptcha_post_message; 
			}


		}else if($element->captcha_type == 't'){ //if this is simple text captcha
			
			$element->title = $mf_lang['captcha_simple_text_title'];

			$text_captcha = mf_get_text_captcha();
			
			$_SESSION['MF_TEXT_CAPTCHA_ANSWER'] = $text_captcha['answer'];
			$text_captcha_question = htmlspecialchars($text_captcha['question'],ENT_QUOTES);

			$captcha_html = <<<EOT
<span class="text_captcha">{$text_captcha_question}</span>
<input id="captcha_response_field" name="captcha_response_field" class="element text small" type="text" />
EOT;
		}
		
				
$element_markup = <<<EOT
		<li id="li_captcha" {$error_class}> {$recaptcha_theme_init}
		<label class="description" for="captcha_response_field">{$element->title} {$span_required}</label>
		<div>
			{$captcha_html}	
		</div>	 
		{$guidelines} {$error_message}
		</li>
EOT;
		
		return $element_markup;
	}
	
	//Matrix Table
	function mf_display_matrix($element){
		
		//check for error
		$li_class = '';
		$li_style = '';
		$error_message = '';
		$span_required = '';
		$el_class = array();
		
		$el_class[] = "matrix";
		
		if($element->is_private == 1){
			$el_class[] = 'private';
		}

		if($element->is_private == 2 && !$element->is_design_mode && !$element->is_edit_entry){
			$li_style = 'style="display: none"';
		}

		if(!empty($element->css_class)){
			$el_class[] = trim($element->css_class);
		}
		
		if(!empty($element->is_error)){
			$el_class[] = 'error';
			if($element->error_message != 'error_no_display'){
				$error_message = "<p class=\"error\">{$element->error_message}</p>";
			}
		}
		
		//check for required
		if($element->is_required){
			$span_required = "<span id=\"required_{$element->id}\" class=\"required\">*</span>";
		}
		
		//check matrix field type
		if($element->matrix_allow_multiselect){
			$input_type = 'checkbox';
		}else{
			$input_type = 'radio';
		}
		
		
		
		//calculate the table columns width
		$total_answer = count($element->options) + 1;
		$initial_width = 100 / $total_answer;
		$first_col_width = 2 * $initial_width;
		$first_col_width = round($first_col_width);
		$other_col_width = (100 - $first_col_width) / ($total_answer - 1);
		$other_col_width = round($other_col_width);

		//build th markup and first row markup		
		$th_markup = '';
		$first_row_td = '';
		$option_titles = array();
		$i=0;
		foreach($element->options as $option){
			$option_titles[$i] = $option->option;
			
			if($input_type == 'checkbox'){
				$option_id_var = '_'.$option->id;

				if(!empty($element->populated_value['element_'.$element->id.'_'.$option->id]['default_value']) && ($element->populated_value['element_'.$element->id.'_'.$option->id]['default_value'] == $option->id)){
					$checked_markup = 'checked="checked"';
				}else{
					$checked_markup = '';
				}
			}else{
				$option_id_var = '';
				
				if(!empty($element->populated_value['element_'.$element->id]['default_value']) && ($element->populated_value['element_'.$element->id]['default_value'] == $option->id)){
					$checked_markup = 'checked="checked"';
				}else{
					$checked_markup = '';
				}
			}
			
			$th_markup 	  .= "<th id=\"mc_{$element->id}_{$option->id}\" style=\"width: {$other_col_width}%\" scope=\"col\">{$option->option}</th>\n";
			$first_row_td .= "<td><label style=\"display: none\" for=\"element_{$element->id}_{$option->id}\">{$option_titles[$i]}</label><input id=\"element_{$element->id}_{$option->id}\" name=\"element_{$element->id}{$option_id_var}\" type=\"{$input_type}\" value=\"{$option->id}\" {$checked_markup} /></td>\n";
			$i++;
		}
		
		//build other rows markup
		$tr_markup = '';
		$show_alt = false;
		if(!empty($element->matrix_children)){
			foreach ($element->matrix_children as $matrix_item){
			
				$children_option_id = array();
				$children_option_id = explode(',',$matrix_item['children_option_id']);
				
				$td_markup = "<td class=\"first_col\">{$matrix_item['title']}</td>";
				$i=0;
				foreach ($children_option_id as $option_id){
					
					if($input_type == 'checkbox'){
						$option_id_var = '_'.$option_id;

						if(!empty($element->populated_value['element_'.$matrix_item['id'].'_'.$option_id]['default_value']) && ($element->populated_value['element_'.$matrix_item['id'].'_'.$option_id]['default_value'] == $option_id)){
							$checked_markup = 'checked="checked"';
						}else{
							$checked_markup = '';
						}
					}else{
						$option_id_var = '';
						
						if(!empty($element->populated_value['element_'.$matrix_item['id']]['default_value']) && ($element->populated_value['element_'.$matrix_item['id']]['default_value'] == $option_id)){
							$checked_markup = 'checked="checked"';
						}else{
							$checked_markup = '';
						}
					}
				
					$td_markup .= "<td><label style=\"display: none\" for=\"element_{$matrix_item['id']}_{$option_id}\">{$option_titles[$i]}</label><input id=\"element_{$matrix_item['id']}_{$option_id}\" name=\"element_{$matrix_item['id']}{$option_id_var}\" type=\"{$input_type}\" value=\"{$option_id}\" {$checked_markup} /></td>\n";
					$i++;
				}
				
				if($show_alt){
					$row_style = ' class="alt" ';
					$show_alt = false;
				}else{
					$row_style = '';
					$show_alt = true;
				}

				$tr_markup .= "<tr {$row_style} id=\"mr_{$matrix_item['id']}\">".$td_markup."</tr>";
			}
		}
		
		//build the li class
		if(!empty($el_class)){
			foreach ($el_class as $value){
				$li_class .= $value.' ';
			}
			
			$li_class = 'class="'.rtrim($li_class).'"';
		}
		
$element_markup = <<<EOT
		<li id="li_{$element->id}" {$li_style} {$li_class}>
			<table>
				<caption>
					{$element->guidelines} {$span_required}
				</caption>
			    <thead>
			    	<tr>
			        	<th style="width: {$first_col_width}%" scope="col"><span style="display: none">{$element->guidelines}</span></th>
			            {$th_markup}
			        </tr>
			    </thead>
			    <tbody>
			    	<tr class="alt" id="mr_{$element->id}">
			        	<td class="first_col">{$element->title}</td>
			            {$first_row_td}
			        </tr>
			        {$tr_markup}
			    </tbody>
			</table>
		{$error_message}
		</li>
EOT;
		
		return $element_markup;
	}
	
	
	//Main function to display a form
	//There are few mode when displaying a form
	//1. New blank form (form populated with default values)
	//2. New form with error (displayed when 1 submitted and having error, form populated with user inputs)
	//3. Edit form (form populated with data from db)
	//4. Edit form with error (displayed when #3 submitted and having error)

	function mf_display_form($dbh,$form_id,$form_params=array()){	
		
		global $mf_lang;

		$form_id = (int) $form_id;
		
		//parameters mapping
		if(isset($form_params['page_number'])){
			$page_number = (int) $form_params['page_number'];
		}else{
			$page_number = 1;
		}

		if(isset($form_params['populated_values'])){
			$populated_values = $form_params['populated_values'];
		}else{
			$populated_values = array();
		}

		if(isset($form_params['error_elements'])){
			$error_elements = $form_params['error_elements'];
		}else{
			$error_elements = array();
		}
		
		if(isset($form_params['custom_error'])){
			$custom_error = $form_params['custom_error'];
		}else{
			$custom_error = '';
		}
		
		if(isset($form_params['edit_id'])){
			$edit_id = (int) $form_params['edit_id'];
		}else{
			$edit_id = 0;
		}
		
		if(isset($form_params['integration_method'])){ //valid values are empty string, 'iframe' or 'php'
			$integration_method = $form_params['integration_method'];
		}else{
			$integration_method = '';
		}

		if(!empty($form_params['machform_path'])){
			$machform_path = $form_params['machform_path'];
		}else{
			$machform_path = '';
		}

		if(!empty($form_params['machform_data_path'])){
			$machform_data_path = $form_params['machform_data_path'];
		}else{
			$machform_data_path = '';
		}

		
		$mf_settings = mf_get_settings($dbh);
		
		//if there is custom error, don't show other errors
		if(!empty($custom_error)){
			$error_elements = array();
		}
		
		//get form properties data
		$query 	= "SELECT 
						 form_name,
						 form_name_hide,
						 form_description,
						 form_redirect,
						 form_success_message,
						 form_password,
						 form_unique_ip,
						 form_frame_height,
						 form_has_css,
						 form_active,
						 form_disabled_message,
						 form_captcha,
						 form_captcha_type,
						 form_review,
						 form_label_alignment,
						 form_language,
						 form_page_total,
						 form_lastpage_title,
						 form_submit_primary_text,
						 form_submit_secondary_text,
						 form_submit_primary_img,
						 form_submit_secondary_img,
						 form_submit_use_image,
						 form_pagination_type,
						 form_review_primary_text,
						 form_review_secondary_text,
						 form_review_primary_img,
						 form_review_secondary_img,
						 form_review_use_image,
						 form_review_title,
						 form_review_description,
						 form_resume_enable,
						 form_theme_id,
						 payment_show_total,
						 payment_total_location,
						 payment_enable_merchant,
						 payment_currency,
						 payment_price_type,
						 payment_price_amount,
						 form_limit_enable,
						 form_limit,
						 form_schedule_enable,
						 form_schedule_start_date,
						 form_schedule_end_date,
						 form_schedule_start_hour,
						 form_schedule_end_hour,
						 logic_field_enable,
						 logic_page_enable,
						 payment_enable_discount,
						 payment_discount_type,
						 payment_discount_amount,
						 payment_discount_element_id,
						 payment_enable_tax,
					 	 payment_tax_rate,
					 	 form_custom_script_enable,
					 	 form_custom_script_url 
				     FROM 
				     	 ".MF_TABLE_PREFIX."forms 
				    WHERE 
				    	 form_id = ?";
		$params = array($form_id);
		
		$sth = mf_do_query($query,$params,$dbh);
		$row = mf_do_fetch_result($sth);

		//check for non-existent or currently drafted forms or inactive forms
		if($row === false){
			die('This is not valid form URL.');
		}else{
			$form_active = (int) $row['form_active'];
		
			if($form_active !== 0 && $form_active !== 1){
				die('This is not valid form URL.');
			}
		}

		$form = new stdClass();
		
		$form->id 				= $form_id;
		$form->name 			= $row['form_name'];
		$form->name_hide 		= (int) $row['form_name_hide'];
		$form->description 		= $row['form_description'];
		$form->redirect 		= $row['form_redirect'];
		$form->success_message  = $row['form_success_message'];
		$form->password 		= $row['form_password'];
		$form->frame_height 	= $row['form_frame_height'];
		$form->unique_ip 		= $row['form_unique_ip'];
		$form->has_css 			= $row['form_has_css'];
		$form->active 			= $row['form_active'];
		$form->disabled_message = $row['form_disabled_message'];
		$form->captcha 			= $row['form_captcha'];
		$form->captcha_type 	= $row['form_captcha_type'];
		$form->review 			= $row['form_review'];
		$form->label_alignment  = $row['form_label_alignment'];
		$form->page_total 		= $row['form_page_total'];
		if($page_number === 0){ //this is edit_entry page
			$form->page_total = 1;
		}

		$form->lastpage_title 	= $row['form_lastpage_title'];
		$form->submit_primary_text 	 = $row['form_submit_primary_text'];
		$form->submit_secondary_text = $row['form_submit_secondary_text'];
		$form->submit_primary_img 	 = $row['form_submit_primary_img'];
		$form->submit_secondary_img  = $row['form_submit_secondary_img'];
		$form->submit_use_image  	 = (int) $row['form_submit_use_image'];
		$form->pagination_type		 = $row['form_pagination_type'];
		$form->review_primary_text 	 = $row['form_review_primary_text'];
		$form->review_secondary_text = $row['form_review_secondary_text'];
		$form->review_primary_img 	 = $row['form_review_primary_img'];
		$form->review_secondary_img  = $row['form_review_secondary_img'];
		$form->review_use_image  	 = (int) $row['form_review_use_image'];
		$form->review_title			 = $row['form_review_title'];
		$form->review_description	 = $row['form_review_description'];
		$form->resume_enable	 	 = $row['form_resume_enable'];
		$form->theme_id	    	 	 = (int) $row['form_theme_id'];
		$form->payment_show_total	 = (int) $row['payment_show_total'];
		$form->payment_total_location = $row['payment_total_location'];
		$form->payment_enable_merchant = (int) $row['payment_enable_merchant'];
		$form->payment_currency 	   = $row['payment_currency'];
		$form->payment_price_type 	   = $row['payment_price_type'];
		$form->payment_price_amount    = $row['payment_price_amount'];
		$form->limit_enable  	= (int) $row['form_limit_enable'];
		$form->limit  			= (int) $row['form_limit'];
		$form->schedule_enable  = (int) $row['form_schedule_enable'];
		$form->schedule_start_date  = $row['form_schedule_start_date'];
		$form->schedule_end_date  	= $row['form_schedule_end_date'];
		$form->schedule_start_hour  = $row['form_schedule_start_hour'];
		$form->schedule_end_hour  	= $row['form_schedule_end_hour'];
		$form->language 			= trim($row['form_language']);
		$form->logic_field_enable  	= (int) $row['logic_field_enable'];
		$form->logic_page_enable  	= (int) $row['logic_page_enable'];

		$form->enable_discount 		= (int) $row['payment_enable_discount'];
		$form->discount_type 	 	= $row['payment_discount_type'];
		$form->discount_amount 		= (float) $row['payment_discount_amount'];
		$form->discount_element_id 	= (int) $row['payment_discount_element_id'];

		$form->enable_tax 		 	= (int) $row['payment_enable_tax'];
		$form->tax_rate 			= (float) $row['payment_tax_rate'];

		$form->custom_script_enable = (int) $row['form_custom_script_enable'];
		$form->custom_script_url 	= $row['form_custom_script_url'];

		//if the form has page logic enabled, store the page history
		if(!empty($form->logic_page_enable) && !empty($page_number)){
			//store the page numbers into session for history
			if($page_number == 1){ //if there is no current history, initialize with page 1
				$_SESSION['mf_pages_history'][$form_id] = array();
				$_SESSION['mf_pages_history'][$form_id][] = 1; 
			}else{
				//if the pages history already exist and the current page number already being stored
				//we need to remove it from the array first, along with any subsequent pages
				if(in_array($page_number, $_SESSION['mf_pages_history'][$form_id])){
					$current_page_index = array_search($page_number, $_SESSION['mf_pages_history'][$form_id]);
					array_splice($_SESSION['mf_pages_history'][$form_id], $current_page_index);
				}

				$_SESSION['mf_pages_history'][$form_id][] = (int) $page_number;
			}
		}

		if(!empty($form->language)){
			mf_set_language($form->language);
		}

		if(empty($error_elements)){
			$form->is_error 	= 0;
		}else{
			$form->is_error 	= 1;
		}

		if(!empty($edit_id)){
			$form->active = 1;
		}
		
		
		if($form->page_total == 1){
			//if this form has review enabled and user are having $_SESSION['review_id'], then populate the form with that values
			if(!empty($form->review) && !empty($_SESSION['review_id']) && empty($populated_values)){
				$entry_params = array();
				$entry_params['machform_data_path'] = $machform_data_path;

				$populated_values = mf_get_entry_values($dbh,$form_id,$_SESSION['review_id'],true,$entry_params);
			}elseif (!empty($form->review) && !empty($_SESSION['review_id']) && !empty($populated_values)){ //if form review enabled and there is some validation error, the uploaded files needs to be displayed
				$entry_params = array();
				$entry_params['machform_data_path'] = $machform_data_path;

				$populated_file_values = mf_get_entry_values($dbh,$form_id,$_SESSION['review_id'],true,$entry_params);
			}
		}else{
			//if this is multipage form, always populate the fields
			$session_id = session_id();
			
			//if there is form resume key, load the record from ap_form_x table to ap_form_x_review table
			if(!empty($_SESSION['mf_form_resume_key'][$form_id]) && !empty($form->resume_enable)){
				$resume_key = $_SESSION['mf_form_resume_key'][$form_id];
				
				//first delete existing record within review table
				$query = "DELETE from `".MF_TABLE_PREFIX."form_{$form_id}_review` where session_id=? or resume_key=?";
				$params = array($session_id, $resume_key);
				
				mf_do_query($query,$params,$dbh);
				
				//copy data from ap_form_x table to ap_form_x_review table
				$query  = "SELECT * FROM `".MF_TABLE_PREFIX."form_{$form_id}` WHERE resume_key=?";
				$params = array($resume_key);
				
				$sth = mf_do_query($query,$params,$dbh);
				$row = mf_do_fetch_result($sth);
				
				$columns = array();
				if(!empty($row)){
					foreach($row as $column_name=>$column_data){
						if($column_name != 'id'){
							$columns[] = $column_name;
						}
					}	
				}
				
				if(empty($columns)){
					//invalid resume_key given, display error message
					$custom_error = 'Invalid Link! <br/>Please open the complete URL to resume your saved progress.';
				}else{	
				
					$columns_joined = implode("`,`",$columns);
					$columns_joined = '`'.$columns_joined.'`';
					
					//copy data from main table
					$query = "INSERT INTO `".MF_TABLE_PREFIX."form_{$form_id}_review`($columns_joined) SELECT {$columns_joined} from `".MF_TABLE_PREFIX."form_{$form_id}` WHERE resume_key=?";
					$params = array($resume_key);
					
					mf_do_query($query,$params,$dbh);
					
					$query = "UPDATE `".MF_TABLE_PREFIX."form_{$form_id}_review` set session_id=? WHERE resume_key=?";
					$params = array($session_id,$resume_key);
					
					mf_do_query($query,$params,$dbh);
					
					for($i=1;$i<=$form->page_total;$i++){
						$_SESSION['mf_form_loaded'][$form_id][$i] = true;
					}
					
					$_SESSION['mf_form_resume_loaded'][$form_id] = true;
					unset($_SESSION['mf_form_resume_key'][$form_id]);
				}
			}
			
			$query = "SELECT `id` from `".MF_TABLE_PREFIX."form_{$form_id}_review` where session_id=?";
			$params = array($session_id);
			
			$sth = mf_do_query($query,$params,$dbh);
			$row = mf_do_fetch_result($sth);

			//we need to check mf_form_loaded to make sure default values of fields are being loaded on the first view of the form
			if(empty($populated_values) && !empty($_SESSION['mf_form_loaded'][$form_id][$page_number])){
				$entry_params = array();
				$entry_params['machform_data_path'] = $machform_data_path;

				$populated_values = mf_get_entry_values($dbh,$form_id,$row['id'],true,$entry_params);
			}else{ //if there is some validation error, the uploaded files needs to be displayed
				$entry_params = array();
				$entry_params['machform_data_path'] = $machform_data_path;

				$populated_file_values = mf_get_entry_values($dbh,$form_id,$row['id'],true,$entry_params);
			}
		}
		
		//get price definitions for fields, if the merchant feature is enabled
		if(!empty($form->payment_enable_merchant) && $form->payment_price_type == 'variable'){
			$query = "select 
							element_id,
							option_id,
							`price` 
					   from 
					   		`".MF_TABLE_PREFIX."element_prices` 
					   where 
					   		form_id=? 
				   order by 
				   			element_id,option_id asc";
			$params = array($form_id);
			$sth = mf_do_query($query,$params,$dbh);
			while($row = mf_do_fetch_result($sth)){
				$element_prices_array[$row['element_id']][$row['option_id']] = $row['price'];
			}	
		}
		
		//get elements data
		//get element options first and store it into array
		$query = "SELECT 
						element_id,
						option_id,
						`position`,
						`option`,
						option_is_default 
				    FROM 
				    	".MF_TABLE_PREFIX."element_options 
				   where 
				   		form_id = ? and live=1 
				order by 
						element_id asc,`position` asc";
		$params = array($form_id);
		
		$sth = mf_do_query($query,$params,$dbh);
		while($row = mf_do_fetch_result($sth)){
			$element_id = $row['element_id'];
			$option_id  = $row['option_id'];
			$options_lookup[$element_id][$option_id]['position'] 		  = $row['position'];
			$options_lookup[$element_id][$option_id]['option'] 			  = $row['option'];
			$options_lookup[$element_id][$option_id]['option_is_default'] = $row['option_is_default'];
			
			if(isset($element_prices_array[$element_id][$option_id])){
				$options_lookup[$element_id][$option_id]['price_definition'] = $element_prices_array[$element_id][$option_id];
			}
		}
	
		$matrix_elements = array();
		
		//get elements data
		$element = array();

		if($page_number === 0){ //if page_number is 0, display all pages (this is being used on edit_entry page)
			$page_number_clause = '';
			$params = array($form_id);
		}else{
			$page_number_clause = 'and element_page_number = ?';
			$params = array($form_id,$page_number);
		}

		$query = "SELECT 
						element_id,
						element_title,
						element_guidelines,
						element_size,
						element_is_required,
						element_is_unique,
						element_is_readonly,
						element_is_private,
						element_type,
						element_position,
						element_default_value,
						element_enable_placeholder,
						element_constraint,
						element_choice_has_other,
						element_choice_other_label,
						element_choice_columns,
						element_choice_max_entry,
						element_time_showsecond, 
						element_time_24hour,
						element_address_hideline2,
						element_address_us_only,
						element_date_enable_range,
						element_date_range_min,
						element_date_range_max,
						element_date_enable_selection_limit,
						element_date_selection_max,
						element_date_disable_past_future,
						element_date_past_future,
						element_date_disable_weekend,
						element_date_disable_specific,
						element_date_disabled_list,
						element_file_enable_type_limit,
						element_file_block_or_allow,
						element_file_type_list,
						element_file_as_attachment,
						element_file_enable_advance,
						element_file_auto_upload,
						element_file_enable_multi_upload,
						element_file_max_selection,
						element_file_enable_size_limit,
						element_file_size_max,
						element_matrix_allow_multiselect,
						element_matrix_parent_id,
						element_range_min,
						element_range_max,
						element_range_limit_by,
						element_css_class,
						element_section_display_in_email,
						element_section_enable_scroll,
						element_number_enable_quantity,
						element_number_quantity_link
					FROM 
						".MF_TABLE_PREFIX."form_elements 
				   WHERE 
				   		form_id = ? and element_status='1' {$page_number_clause} and element_type <> 'page_break'
				ORDER BY 
						element_position asc";
		
		$sth = mf_do_query($query,$params,$dbh);
		
		$j=0;
		$has_calendar = false; //assume the form doesn't have calendar, so it won't load calendar.js
		$has_advance_uploader = false;
		$has_signature_pad = false;
		$has_guidelines = false;
		
		while($row = mf_do_fetch_result($sth)){
			
			$element[$j] = new stdClass();
			
			$element_id = $row['element_id'];
			
			//lookup element options first
			if(!empty($options_lookup[$element_id])){
				$element_options = array();
				$i=0;
				foreach ($options_lookup[$element_id] as $option_id=>$data){
					$element_options[$i] = new stdClass();
					$element_options[$i]->id 		 = $option_id;
					$element_options[$i]->option 	 = $data['option'];
					$element_options[$i]->is_default = $data['option_is_default'];
					$element_options[$i]->is_db_live = 1;
					
					if(isset($data['price_definition'])){
						$element_options[$i]->price_definition = $data['price_definition'];
					}
					
					$i++;
				}
			}
			
		
			//populate elements
			$element[$j]->title 		= nl2br($row['element_title']);
			$element[$j]->guidelines 	= nl2br($row['element_guidelines']);
			
			if(!empty($row['element_guidelines']) && ($row['element_type'] != 'section') && ($row['element_type'] != 'matrix') && ($row['element_is_private'] == 0)){
				$has_guidelines = true;
			}
			
			$element[$j]->size 			= $row['element_size'];
			$element[$j]->is_required 	= $row['element_is_required'];
			$element[$j]->is_readonly 	= $row['element_is_readonly'];
			$element[$j]->is_unique 	= $row['element_is_unique'];
			$element[$j]->is_private 	= $row['element_is_private'];
			$element[$j]->type 			= $row['element_type'];
			$element[$j]->position 		= $row['element_position'];
			$element[$j]->id 			= $row['element_id'];
			$element[$j]->is_db_live 	= 1;
			$element[$j]->form_id 		= $form_id;
			$element[$j]->choice_has_other   = (int) $row['element_choice_has_other'];
			$element[$j]->choice_other_label = $row['element_choice_other_label'];
			$element[$j]->choice_columns   	 = (int) $row['element_choice_columns'];
			
			$element[$j]->choice_max_entry   = (int) $row['element_choice_max_entry'];
			if(!empty($element[$j]->choice_max_entry)){
				$element[$j]->dbh = $dbh;
			}

			$element[$j]->time_showsecond    = (int) $row['element_time_showsecond'];
			$element[$j]->time_24hour    	 = (int) $row['element_time_24hour'];
			$element[$j]->address_hideline2	 = (int) $row['element_address_hideline2'];
			$element[$j]->address_us_only	 = (int) $row['element_address_us_only'];
			$element[$j]->date_enable_range	 = (int) $row['element_date_enable_range'];
			$element[$j]->date_range_min	 = $row['element_date_range_min'];
			$element[$j]->date_range_max	 = $row['element_date_range_max'];
			$element[$j]->date_enable_selection_limit	 = (int) $row['element_date_enable_selection_limit'];
			$element[$j]->date_selection_max	 		 = (int) $row['element_date_selection_max'];
			$element[$j]->date_disable_past_future	 	= (int) $row['element_date_disable_past_future'];
			$element[$j]->date_past_future	 			= $row['element_date_past_future'];
			$element[$j]->date_disable_weekend	 		= (int) $row['element_date_disable_weekend'];
			$element[$j]->date_disable_specific	 		= (int) $row['element_date_disable_specific'];
			$element[$j]->date_disabled_list	 		= $row['element_date_disabled_list'];
			$element[$j]->file_enable_type_limit	 	= (int) $row['element_file_enable_type_limit'];						
			$element[$j]->file_block_or_allow	 		= $row['element_file_block_or_allow'];
			$element[$j]->file_type_list	 			= $row['element_file_type_list'];
			$element[$j]->file_as_attachment	 		= (int) $row['element_file_as_attachment'];	
			$element[$j]->file_enable_advance	 		= (int) $row['element_file_enable_advance'];
			
			if(!empty($element[$j]->file_enable_advance) && ($row['element_type'] == 'file')){
				$has_advance_uploader = true;
			}
			
			$element[$j]->file_auto_upload	 			= (int) $row['element_file_auto_upload'];
			$element[$j]->file_enable_multi_upload	 	= (int) $row['element_file_enable_multi_upload'];
			$element[$j]->file_max_selection	 		= (int) $row['element_file_max_selection'];
			$element[$j]->file_enable_size_limit	 	= (int) $row['element_file_enable_size_limit'];
			$element[$j]->file_size_max	 				= (int) $row['element_file_size_max'];
			$element[$j]->matrix_allow_multiselect	 	= (int) $row['element_matrix_allow_multiselect'];
			$element[$j]->matrix_parent_id	 			= (int) $row['element_matrix_parent_id'];
			$element[$j]->upload_dir	 				= $mf_settings['upload_dir'];		
			$element[$j]->range_min	 					= $row['element_range_min'];
			$element[$j]->range_max	 					= $row['element_range_max'];
			$element[$j]->range_limit_by	 			= $row['element_range_limit_by'];
			$element[$j]->css_class	 					= $row['element_css_class'];
			$element[$j]->machform_path	 				= $machform_path;
			$element[$j]->machform_data_path	 		= $machform_data_path;
			$element[$j]->section_display_in_email	 	= (int) $row['element_section_display_in_email'];
			$element[$j]->section_enable_scroll	 		= (int) $row['element_section_enable_scroll'];

			if(!empty($form->payment_enable_merchant) && !empty($row['element_number_enable_quantity']) && !empty($row['element_number_quantity_link'])){
				$element[$j]->number_quantity_link	 	= $row['element_number_quantity_link'];
			}

			//this data came from db or form submit
			//being used to display edit form or redisplay form with errors and previous inputs
			//this should be optimized in the future, only pass necessary data, not the whole array			
			$element[$j]->populated_value = $populated_values;
			
			
			//set prices for price-enabled field
			if($row['element_type'] == 'money' && isset($element_prices_array[$row['element_id']][0])){
				$element[$j]->price_definition = 0;
			}
			
			//if there is file upload type, set form enctype to multipart
			if($row['element_type'] == 'file'){
				$form_enc_type = 'enctype="multipart/form-data"';
				
				//if this is single page form with review enabled or multipage form
				if ((!empty($form->review) && !empty($_SESSION['review_id']) && !empty($populated_file_values)) ||
					($form->page_total > 1 && !empty($populated_file_values))	
				) {
					//populate the default value for uploaded files, when validation error occured

					//make sure to keep the file token if exist
					if(!empty($populated_values['element_'.$row['element_id']]['file_token'])){
						$populated_file_values['element_'.$row['element_id']]['file_token'] = $populated_values['element_'.$row['element_id']]['file_token'];
					}

					$element[$j]->populated_value = $populated_file_values;
				}
			}

			if(!empty($edit_id) && $_SESSION['mf_logged_in'] === true){
				//if this is edit_entry page
				$element[$j]->is_edit_entry = true;
			}
			
			if(!empty($error_elements[$element[$j]->id])){
				$element[$j]->is_error 	    = 1;
				$element[$j]->error_message = $error_elements[$element[$j]->id];
			}
			
			
			$element[$j]->default_value = htmlspecialchars($row['element_default_value']);
			$element[$j]->enable_placeholder = $row['element_enable_placeholder'];
			
			
			$element[$j]->constraint 	= $row['element_constraint'];
			if(!empty($element_options)){
				$element[$j]->options 	= $element_options;
			}else{
				$element[$j]->options 	= '';
			}
			
			//check for signature type
			if($row['element_type'] == 'signature'){
				$has_signature_pad = true;
			}
			
			//check for calendar type
			if($row['element_type'] == 'date' || $row['element_type'] == 'europe_date'){
				$has_calendar = true;
				
				//if the field has date selection limit, we need to do query to existing entries and disable all date which reached the limit
				if(!empty($row['element_date_enable_selection_limit']) && !empty($row['element_date_selection_max'])){
					$sub_query = "select 
										selected_date 
									from (
											select 
												  date_format(element_{$row['element_id']},'%m/%d/%Y') as selected_date,
												  count(element_{$row['element_id']}) as total_selection 
										      from 
										      	  ".MF_TABLE_PREFIX."form_{$form_id} 
										     where 
										     	  status=1 and element_{$row['element_id']} is not null 
										  group by 
										  		  element_{$row['element_id']}
										 ) as A
								   where 
										 A.total_selection >= ?";
					$params = array($row['element_date_selection_max']);
					$sub_sth = mf_do_query($sub_query,$params,$dbh);
					$current_date_disabled_list = array();
					$current_date_disabled_list_joined = '';
					
					while($sub_row = mf_do_fetch_result($sub_sth)){
						$current_date_disabled_list[] = $sub_row['selected_date'];
					}
					
					$current_date_disabled_list_joined = implode(',',$current_date_disabled_list);
					if(!empty($element[$j]->date_disable_specific)){ //add to existing disable date list
						if(empty($element[$j]->date_disabled_list)){
							$element[$j]->date_disabled_list = $current_date_disabled_list_joined;
						}else{
							$element[$j]->date_disabled_list .= ','.$current_date_disabled_list_joined;
						}
					}else{
						//'disable specific date' is not enabled, we need to override and enable it from here
						$element[$j]->date_disable_specific = 1;
						$element[$j]->date_disabled_list = $current_date_disabled_list_joined;
					}
					
				}
			}
			
			//if the element is a matrix field and not the parent, store the data into a lookup array for later use when rendering the markup
			if($row['element_type'] == 'matrix' && !empty($row['element_matrix_parent_id'])){
				
				$parent_id 	 = $row['element_matrix_parent_id'];
				$el_position = $row['element_position'];
				$matrix_elements[$parent_id][$el_position]['title'] = $element[$j]->title; 
				$matrix_elements[$parent_id][$el_position]['id'] 	= $element[$j]->id; 
				
				$matrix_child_option_id = '';
				foreach($element_options as $value){
					$matrix_child_option_id .= $value->id.',';
				}
				$matrix_child_option_id = rtrim($matrix_child_option_id,',');
				$matrix_elements[$parent_id][$el_position]['children_option_id'] = $matrix_child_option_id; 
				
				//remove it from the main element array
				$element[$j] = array();
				unset($element[$j]);
				$j--;
			}
			
			$j++;
		}
		
		
		//add captcha if enabled
		//on multipage form, captcha should be displayed on the last page only
		if(!empty($form->captcha) && (empty($edit_id))){
			if($form->page_total == 1 || ($form->page_total == $page_number)){
				if($_SESSION['captcha_passed'][$form_id] !== true){
					$element[$j] = new stdClass();
					$element[$j]->type 			= 'captcha';
					$element[$j]->captcha_type 	= $form->captcha_type;
					$element[$j]->form_id 		= $form_id;
					$element[$j]->is_private	= 0;
					$element[$j]->recaptcha_site_key   = $mf_settings['recaptcha_site_key'];
					$element[$j]->recaptcha_secret_key = $mf_settings['recaptcha_secret_key'];

					if(!empty($error_elements['element_captcha'])){
						$element[$j]->is_error 	    = 1;
						$element[$j]->error_message = $error_elements['element_captcha'];
					}

					//initialize reCAPTCHA v2 header
					if($form->captcha_type == 'n'){
						
						switch ($form->language) {
							case 'bulgarian': $recaptcha2_language = 'bg'; break;
							case 'chinese': $recaptcha2_language = 'zh-TW'; break;
							case 'chinese_simplified': $recaptcha2_language = 'zh-CN'; break;
							case 'danish': $recaptcha2_language = 'da'; break;
							case 'dutch': $recaptcha2_language = 'nl'; break;
							case 'estonian': $recaptcha2_language = 'en'; break;
							case 'finnish': $recaptcha2_language = 'fi'; break;
							case 'french': $recaptcha2_language = 'fr'; break;
							case 'german': $recaptcha2_language = 'de'; break;
							case 'greek': $recaptcha2_language = 'el'; break;
							case 'hungarian': $recaptcha2_language = 'hu'; break;
							case 'indonesia': $recaptcha2_language = 'id'; break;
							case 'italian': $recaptcha2_language = 'it'; break;
							case 'japanese': $recaptcha2_language = 'ja'; break;
							case 'norwegian': $recaptcha2_language = 'no'; break;
							case 'polish': $recaptcha2_language = 'pl'; break;
							case 'portuguese': $recaptcha2_language = 'pt'; break;
							case 'romanian': $recaptcha2_language = 'ro'; break;
							case 'russian': $recaptcha2_language = 'ru'; break;
							case 'slovak': $recaptcha2_language = 'sk'; break;
							case 'spanish': $recaptcha2_language = 'es'; break;
							case 'swedish': $recaptcha2_language = 'sv'; break;
							default:
								$recaptcha2_language = 'en';
								break;
						}

						$recaptcha2_header 	 = "<script type=\"text/javascript\" src=\"https://www.google.com/recaptcha/api.js?hl={$recaptcha2_language}\"></script>\n";
					}
				}
			}
		}
		
		//generate html markup for each element
		$container_class = '';
		$all_element_markup = '';
		foreach ($element as $element_data){
			if(($element_data->is_private == 1) && empty($edit_id)){ //don't show private element on live forms
				continue;
			}
			
			//if this is matrix field, build the children data from $matrix_elements array
			if($element_data->type == 'matrix'){
				$element_data->matrix_children = $matrix_elements[$element_data->id];
			}
			
			$all_element_markup .= call_user_func('mf_display_'.$element_data->type,$element_data);
		}
		
		if(!empty($custom_error)){
			$form->error_message =<<<EOT
			<li id="error_message">
					<h3 id="error_message_title">{$custom_error}</h3>
			</li>	
EOT;
		}elseif(!empty($error_elements)){
			$form->error_message =<<<EOT
			<li id="error_message">
					<h3 id="error_message_title">{$mf_lang['error_title']}</h3>
					<p id="error_message_desc">{$mf_lang['error_desc']}</p>
			</li>	
EOT;
		}
		
		//if this form is using custom theme and not on edit entry page
		if(!empty($form->theme_id) && empty($edit_id)){
			//get the field highlight color for the particular theme
			$query = "SELECT 
							highlight_bg_type,
							highlight_bg_color,
							form_shadow_style,
							form_shadow_size,
							form_shadow_brightness,
							form_button_type,
							form_button_text,
							form_button_image,
							theme_has_css  
						FROM 
							".MF_TABLE_PREFIX."form_themes 
					   WHERE 
					   		theme_id = ?";
			$params = array($form->theme_id);
			
			$sth = mf_do_query($query,$params,$dbh);
			$row = mf_do_fetch_result($sth);
			
			$form_shadow_style 		= $row['form_shadow_style'];
			$form_shadow_size 		= $row['form_shadow_size'];
			$form_shadow_brightness = $row['form_shadow_brightness'];
			$theme_has_css = (int) $row['theme_has_css'];
			
			//if the theme has css file, make sure to refer to that file
			//otherwise, generate the css dynamically


			//make sure to put a timestamp for the CSS file for logged in users
			//so that the CSS file generated by the theme editor is always being applied immediately
			if(!empty($_SESSION['mf_logged_in'])){
				$css_timestamp_1 = "?t=".time(); 
				$css_timestamp_2 = "&t=".time();
			}

			if(!empty($theme_has_css)){
				$theme_css_link = '<link rel="stylesheet" type="text/css" href="'.$machform_path.$mf_settings['data_dir'].'/themes/theme_'.$form->theme_id.'.css'.$css_timestamp_1.'" media="all" />';
			}else{
				$theme_css_link = '<link rel="stylesheet" type="text/css" href="'.$machform_path.'css_theme.php?theme_id='.$form->theme_id.$css_timestamp_2.'" media="all" />';
			}
			
			if($row['highlight_bg_type'] == 'color'){
				$field_highlight_color = $row['highlight_bg_color'];
			}else{ 
				//if the field highlight is using pattern instead of color, set the color to empty string
				$field_highlight_color = ''; 
			}
			
			//get the css link for the fonts
			$font_css_markup = mf_theme_get_fonts_link($dbh,$form->theme_id);
			
			//get the form shadow classes
			if(!empty($form_shadow_style) && ($form_shadow_style != 'disabled')){
				preg_match_all("/[A-Z]/",$form_shadow_style,$prefix_matches);
				//this regex simply get the capital characters of the shadow style name
				//example: RightPerspectiveShadow result to RPS and then being sliced to RP
				$form_shadow_prefix_code = substr(implode("",$prefix_matches[0]),0,-1);
				
				$form_shadow_size_class  = $form_shadow_prefix_code.ucfirst($form_shadow_size);
				$form_shadow_brightness_class = $form_shadow_prefix_code.ucfirst($form_shadow_brightness);

				if(empty($integration_method)){ //only display shadow if the form is not being embedded using any method
					$form_container_class = $form_shadow_style.' '.$form_shadow_size_class.' '.$form_shadow_brightness_class;
				}
			}
			
			//get the button text/image setting
			if(empty($form->review)){
				if($row['form_button_type'] == 'text'){
					$submit_button_markup = '<input id="submit_form" class="button_text" type="submit" name="submit_form" value="'.$row['form_button_text'].'" />';
				}else{
					$submit_button_markup = '<input class="submit_img_primary" type="image" alt="Submit" id="submit_form" name="submit_form" src="'.$row['form_button_image'].'" />';
				}			
			}else{
				if($row['form_button_type'] == 'text'){
					$submit_button_markup = '<input id="submit_form" class="button_text" type="submit" name="submit_form" value="'.$mf_lang['continue_button'].'" />';
				}else{
					$submit_button_markup = '<input class="submit_img_primary" type="image" alt="Submit" id="submit_form" name="submit_form" src="'.$row['form_button_image'].'" />';
				}
			}
			
		}else{ //if the form doesn't have any theme being applied
			$field_highlight_color = '#FFF7C0';
			
			if(empty($integration_method)){
				$form_container_class = ''; //default shadow as of v4.2 is no-shadow
			}else{
				$form_container_class = ''; //dont show any shadow when the form being embedded
			}
			
			
			if(empty($form->review)){
				$submit_button_markup = '<input id="submit_form" class="button_text" type="submit" name="submit_form" value="'.$mf_lang['submit_button'].'" />';
			}else{
				$submit_button_markup = '<input id="submit_form" class="button_text" type="submit" name="submit_form" value="'.$mf_lang['continue_button'].'" />';
			}
		}
		
		//display edit_id if there is any, this is being called on edit_entry.php page
		if(!empty($edit_id)){
			$edit_markup = "<input type=\"hidden\" name=\"edit_id\" value=\"{$edit_id}\" />\n";
			$submit_button_markup = '<input id="submit_form" class="bb_button bb_green" type="submit" name="submit_form" value="Save Changes" />';
		}else{
			$edit_markup = '';
		}
		

		//check for specific form css, if any, use it instead
		if($form->has_css){
			$css_dir = $mf_settings['data_dir']."/form_{$form_id}/css/";
		}
		
		if(!empty($form->password) && empty($_SESSION['user_authenticated'])){ //if form require password and password hasn't set yet
			$show_password_form = true;
			
		}elseif (!empty($form->password) && !empty($_SESSION['user_authenticated']) && $_SESSION['user_authenticated'] != $form_id){ //if user authenticated but not for this form
			$show_password_form = true;
			
		}else{ //user authenticated for this form, or no password required
			$show_password_form = false;
		}

		
		if($show_password_form){
			$submit_button_markup = '<input id="submit_form" class="button_text" type="submit" name="submit_form" value="'.$mf_lang['submit_button'].'" />';
		}

		//default markup for single page form submit button
		$button_markup =<<<EOT
		<li id="li_buttons" class="buttons">
			    <input type="hidden" name="form_id" value="{$form->id}" />
			    {$edit_markup}
			    <input type="hidden" name="submit_form" value="1" />
			    <input type="hidden" name="page_number" value="{$page_number}" />
				{$submit_button_markup}
		</li>
EOT;
		
		//check for form limit rule
		$form_has_maximum_entries = false;
		
		if(!empty($form->limit_enable)){
			if(!empty($form->payment_enable_merchant)){
				//if the form has payment enabled, we only count paid records or unpaid records within the last 30 minutes
				$query = "select 
								count(*) total_row 
							from
								(select 
									  A.`id`,
									  A.`status`,
									  A.date_created,  
									  (select payment_status from ".MF_TABLE_PREFIX."form_payments where record_id=A.`id` and form_id = ?) payment_status
								  from 
								      ".MF_TABLE_PREFIX."form_{$form_id} A where A.`status` = 1) B
						   where 
							    (B.`payment_status` = 'paid' and B.`status` = 1) OR
							    (B.`payment_status` is null and B.status = 1 and B.date_created > now()-interval 30 minute)";
				$params = array($form_id);
			}else{
				$query = "select count(*) total_row from ".MF_TABLE_PREFIX."form_{$form_id} where `status`=1";
				$params = array();
			}

			$sth = mf_do_query($query,$params,$dbh);
			$row = mf_do_fetch_result($sth);
			
			$total_entries  = $row['total_row'];

			if($total_entries >= $form->limit){
				$form_has_maximum_entries = true;
			}
		}

		//check for automatic scheduling limit, if enabled
		if(!empty($form->schedule_enable) && empty($edit_id)){
			$schedule_start_time = strtotime($form->schedule_start_date.' '.$form->schedule_start_hour);
			$schedule_end_time = strtotime($form->schedule_end_date.' '.$form->schedule_end_hour);

			$current_time = strtotime(date("Y-m-d H:i:s"));

			if(!empty($schedule_start_time)){
				if($current_time < $schedule_start_time){
					$form->active = 0;
				}
			}

			if(!empty($schedule_end_time)){
				if($current_time > $schedule_end_time){
					$form->active = 0;
				}
			}
		}

		$is_edit_entry = false;
		if(!empty($edit_id) && $_SESSION['mf_logged_in'] === true){
			//if this is edit_entry page
			$is_edit_entry = true;
		}
				
		if( (empty($form->active) || $form_has_maximum_entries) && $is_edit_entry === false ){ //if form is not active, don't show the fields
			$form_desc_div ='';	
			$all_element_markup = '';
			$button_markup = '';
			$ul_class = 'class="password"';

			if($form_has_maximum_entries){
				$inactive_message = $mf_lang['form_limited'];
			}else{
				$inactive_message = $mf_lang['form_inactive'];
			}

			if(!empty($form->disabled_message)){
				$inactive_message = nl2br($form->disabled_message);
			}

			$custom_element =<<<EOT
			<li>
				<h2>{$inactive_message}</h2>
			</li>
EOT;
		}elseif($show_password_form){ //don't show form description if this page is password protected and user not authenticated
			$form_desc_div ='';	
			$all_element_markup = '';	
			$custom_element =<<<EOT
			<li>
				<h2>{$mf_lang['form_pass_title']}</h2>
				<div>
				<input type="password" value="" class="text" name="password" id="password" />
				<label for="password" class="desc">{$mf_lang['form_pass_desc']}</label>
				</div>
			</li>
EOT;
			$ul_class = 'class="password"';
		}else{
			if(!empty($form->name) || !empty($form->description)){
				$form->description = nl2br($form->description);
				$form_desc_div =<<<EOT
		<div class="form_description">
			<h2>{$form->name}</h2>
			<p>{$form->description}</p>
		</div>
EOT;
			}

			if(!empty($form->name_hide)){
				$form_desc_div = '';
			}
		}
		
		if(!$has_guidelines){
			$container_class .= " no_guidelines";
		}
		
		if($integration_method == 'iframe'){
			$html_class_tag = 'class="embed"';
		}
		
		if($has_calendar){
			
			//initialize the calendar, along with the selected form language
			switch ($form->language) {
				case 'dutch' : $datepick_lang = 'js/datepick/jquery.datepick-nl.js';break;
				case 'french' : $datepick_lang = 'js/datepick/jquery.datepick-fr.js';break;
				case 'german' : $datepick_lang = 'js/datepick/jquery.datepick-de.js';break;
				case 'italian' : $datepick_lang = 'js/datepick/jquery.datepick-it.js';break;
				case 'portuguese' : $datepick_lang = 'js/datepick/jquery.datepick-pt.js';break;
				case 'spanish' : $datepick_lang = 'js/datepick/jquery.datepick-es.js';break;
				case 'swedish' : $datepick_lang = 'js/datepick/jquery.datepick-sv.js';break;
				case 'japanese' : $datepick_lang = 'js/datepick/jquery.datepick-ja.js';break;
				case 'estonian' : $datepick_lang = 'js/datepick/jquery.datepick-et.js';break;
				case 'russian' : $datepick_lang = 'js/datepick/jquery.datepick-ru.js';break;
				case 'hungarian' : $datepick_lang = 'js/datepick/jquery.datepick-hu.js';break;
				case 'chinese' : $datepick_lang = 'js/datepick/jquery.datepick-zh-TW.js';break;
				case 'chinese_simplified' : $datepick_lang = 'js/datepick/jquery.datepick-zh-CN.js';break;
				case 'bulgarian' : $datepick_lang = 'js/datepick/jquery.datepick.js-bg';break;
				case 'danish' : $datepick_lang = 'js/datepick/jquery.datepick-da.js';break;
				case 'finnish' : $datepick_lang = 'js/datepick/jquery.datepick-fi.js';break;
				case 'polish' : $datepick_lang = 'js/datepick/jquery.datepick-pl.js';break;
				case 'greek' : $datepick_lang = 'js/datepick/jquery.datepick-el.js';break;
				case 'norwegian' : $datepick_lang = 'js/datepick/jquery.datepick-no.js';break;
				case 'romanian' : $datepick_lang = 'js/datepick/jquery.datepick-ro.js';break;
				case 'slovak' : $datepick_lang = 'js/datepick/jquery.datepick-sk.js';break;
				case 'indonesia' : $datepick_lang = 'js/datepick/jquery.datepick-id.js';break;
				default : $datepick_lang = ''; //the default english language
			}

			if(!empty($datepick_lang)) {
				$datepick_lang =  '<script type="text/javascript" src="'.$machform_path.$datepick_lang.'"></script>';
			}

			$calendar_init = '<script type="text/javascript" src="'.$machform_path.'js/datepick/jquery.datepick.js"></script>'."\n".
							 $datepick_lang . "\n".
							 '<script type="text/javascript" src="'.$machform_path.'js/datepick/jquery.datepick.ext.js"></script>'."\n".
							 '<link type="text/css" href="'.$machform_path.'js/datepick/smoothness.datepick.css" rel="stylesheet" />';
		}else{
			$calendar_init = '';
		}

		if($has_signature_pad){
			$signature_pad_init = '<!--[if lt IE 9]><script src="'.$machform_path.'js/signaturepad/flashcanvas.js"></script><![endif]-->'."\n".
							 	  '<script type="text/javascript" src="'.$machform_path.'js/signaturepad/jquery.signaturepad.min.js"></script>'."\n".
							 	  '<script type="text/javascript" src="'.$machform_path.'js/signaturepad/json2.min.js"></script>'."\n";
		}else{
			$signature_pad_init = '';
		}

		//generate conditional logic code, if enabled and not on edit entry page
		if(!empty($form->logic_field_enable) && empty($edit_id)){
			$logic_js = mf_get_logic_javascript($dbh,$form_id,$page_number);
		}else{
			$logic_js = '';
		}

		$page_main_title = $form->name;
		
		//if the form has multiple pages
		//display the pagination header
		if($form->page_total > 1 && $show_password_form === false){
			//build pagination header based on the selected type. possible values:
			//steps - display multi steps progress
			//percentage - display progress bar with percentage
			//disabled - disabled
			
			$page_breaks_data = array();
			$page_title_array = array();
			
			//get page titles
			$query = "SELECT 
							element_page_title,
							element_page_number,
							element_submit_use_image,
						    element_submit_primary_text,
							element_submit_secondary_text,
							element_submit_primary_img,
							element_submit_secondary_img 
						FROM 
							".MF_TABLE_PREFIX."form_elements
					   WHERE
							form_id = ? and element_status = 1 and element_type = 'page_break'
					ORDER BY 
					   		element_page_number asc";
			$params = array($form_id);
			
			$sth = mf_do_query($query,$params,$dbh);
			while($row = mf_do_fetch_result($sth)){
				$temp_page_number = $row['element_page_number'];
				$page_breaks_data[$temp_page_number]['use_image'] 		= $row['element_submit_use_image'];
				$page_breaks_data[$temp_page_number]['primary_text'] 	= $row['element_submit_primary_text'];
				$page_breaks_data[$temp_page_number]['secondary_text'] 	= $row['element_submit_secondary_text'];
				$page_breaks_data[$temp_page_number]['primary_img']		= $row['element_submit_primary_img'];
				$page_breaks_data[$temp_page_number]['secondary_img'] 	= $row['element_submit_secondary_img'];
				
				$page_title_array[] = $row['element_page_title'];
			}
			
			//add the last page buttons info into the array for easy lookup
			$page_breaks_data[$form->page_total]['use_image'] 		= $form->submit_use_image;
			$page_breaks_data[$form->page_total]['primary_text'] 	= $form->submit_primary_text;
			$page_breaks_data[$form->page_total]['secondary_text'] 	= $form->submit_secondary_text;
			$page_breaks_data[$form->page_total]['primary_img'] 	= $form->submit_primary_img;
			$page_breaks_data[$form->page_total]['secondary_img'] 	= $form->submit_secondary_img;
			
			$page_total = count($page_title_array) + 1;
				
			if(!empty($form->review)){
				$page_total++;
			}

			if(!empty($form->payment_enable_merchant)){
				$page_total++;
			}

			if($form->pagination_type == 'steps'){
				
				$page_titles_markup = '';
				
				$i=1;
				foreach ($page_title_array as $page_title){
					if($i == $page_number){
						$ap_tp_num_active = ' ap_tp_num_active';
						$ap_tp_text_active = ' ap_tp_text_active';
					}else{
						$ap_tp_num_active = '';
						$ap_tp_text_active = '';
					}
					
					$page_titles_markup .= '<td align="center"><span id="page_num_'.$i.'" class="ap_tp_num'.$ap_tp_num_active.'">'.$i.'</span><span id="page_title_'.$i.'" class="ap_tp_text'.$ap_tp_text_active.'">'.$page_title.'</span></td><td align="center" class="ap_tp_arrow">&gt;</td>'."\n";
					$i++;
				}
				
				//add the last page title into the pagination header markup
				if($i == $page_number){
					$ap_tp_num_active = ' ap_tp_num_active';
					$ap_tp_text_active = ' ap_tp_text_active';
				}else{
					$ap_tp_num_active = '';
					$ap_tp_text_active = '';
				}
				$page_titles_markup .= '<td align="center"><span id="page_num_'.$i.'" class="ap_tp_num'.$ap_tp_num_active.'">'.$i.'</span><span id="page_title_'.$i.'" class="ap_tp_text'.$ap_tp_text_active.'">'.$form->lastpage_title.'</span></td>';
			
				//if form review enabled, we need to add the pagination header
				if(!empty($form->review)){
					$i++;
					$page_titles_markup .= '<td align="center" class="ap_tp_arrow">&gt;</td><td align="center"><span id="page_num_'.$i.'" class="ap_tp_num">'.$i.'</span><span id="page_title_'.$i.'" class="ap_tp_text">'.$form->review_title.'</span></td>';
				}

				//if payment enabled, we need to add the pagination header
				if(!empty($form->payment_enable_merchant)){
					$i++;
					$page_titles_markup .= '<td align="center" class="ap_tp_arrow">&gt;</td><td align="center"><span id="page_num_'.$i.'" class="ap_tp_num">'.$i.'</span><span id="page_title_'.$i.'" class="ap_tp_text">'.$mf_lang['form_payment_header_title'].'</span></td>';
				}
				
				$pagination_header =<<<EOT
			<li id="pagination_header" class="li_pagination">
			 <table class="ap_table_pagination" width="100%" border="0" cellspacing="0" cellpadding="0">
			  <tr> 
			  	{$page_titles_markup}
			  </tr>
			</table>
			</li>
EOT;
			}else if($form->pagination_type == 'percentage'){
				
				$percent_value = round(($page_number/$page_total) * 100);
				
				if($percent_value == 100){ //it's not make sense to display 100% when the form is not really submitted yet
					$percent_value = 99;
				}
				
				if(!empty($form->review) && !empty($form->payment_enable_merchant)){
					if(($page_total-2) == $page_number){ //if this is last page of the form
						$current_page_title = $form->lastpage_title;
					}else{
						$current_page_title = $page_title_array[$page_number-1];
					}
				}else if(!empty($form->review) || !empty($form->payment_enable_merchant)){
					if(($page_total-1) == $page_number){ //if this is last page of the form
						$current_page_title = $form->lastpage_title;
					}else{
						$current_page_title = $page_title_array[$page_number-1];
					}
				}else{
					if($page_total == $page_number){ //if this is last page of the form
						$current_page_title = $form->lastpage_title;
					}else{
						$current_page_title = $page_title_array[$page_number-1];
					}
				}

				
				$page_number_title = sprintf($mf_lang['page_title'],$page_number,$page_total);
				$pagination_header =<<<EOT
			<li id="pagination_header" class="li_pagination">
			    <h3 id="page_title_{$page_number}">{$page_number_title} - {$current_page_title}</h3>
				<div class="mf_progress_container">          
			    	<div id="mf_progress_percentage" class="mf_progress_value" style="width: {$percent_value}%"><span>{$percent_value}%</span></div>
				</div>
			</li>
EOT;
			}else{			
				$pagination_header = '';
			}

			
			$page_main_title = $form->name.' - '.sprintf($mf_lang['page_title'],$page_number,$page_total)." ({$current_page_title})";
			

			//build the submit buttons markup
			if(empty($edit_id)){
				if(empty($page_breaks_data[$page_number]['use_image'])){ //if using text buttons as submit
				
					if($page_number > 1){
						$button_secondary_markup = '<input class="button_text btn_secondary" type="submit" id="submit_secondary" name="submit_secondary" value="'.$page_breaks_data[$page_number]['secondary_text'].'" />';
					}
					
					$button_markup =<<<EOT
			<li id="li_buttons" class="buttons">
				    <input type="hidden" name="form_id" value="{$form->id}" />
				    {$edit_markup}
				    <input type="hidden" name="submit_form" value="1" />
				    <input type="hidden" name="page_number" value="{$page_number}" />
					<input class="button_text btn_primary" type="submit" id="submit_primary" name="submit_primary" value="{$page_breaks_data[$page_number]['primary_text']}" />
					{$button_secondary_markup}
			</li>
EOT;
				}else{ //if using images as submit
					
					if($page_number > 1){
						$button_secondary_markup = '<input class="submit_img_secondary" type="image" alt="Previous" id="submit_secondary" name="submit_secondary" src="'.$page_breaks_data[$page_number]['secondary_img'].'" />';
					}
					
					$button_markup =<<<EOT
			<li id="li_buttons" class="buttons">
				    <input type="hidden" name="form_id" value="{$form->id}" />
				    {$edit_markup}
				    <input type="hidden" name="submit_form" value="1" />
				    <input type="hidden" name="page_number" value="{$page_number}" />
				 	<input class="submit_img_primary" type="image" alt="Continue" id="submit_primary" name="submit_primary" src="{$page_breaks_data[$page_number]['primary_img']}" />
					{$button_secondary_markup}
			</li>
EOT;
					
				}
			}else{ //if there is edit_id, then this is edit_entry page, display a standard button
				$button_markup =<<<EOT
			<li id="li_buttons" class="buttons">
				    <input type="hidden" name="form_id" value="{$form->id}" />
				    {$edit_markup}
				    <input type="hidden" name="submit_form" value="1" />
				    <input type="hidden" name="page_number" value="{$page_number}" />
					<input class="button_text btn_primary" type="submit" id="submit_primary" name="submit_primary" value="Save Changes" />
			</li>
EOT;
			}
			
		}
		
		if($has_advance_uploader){

			if(!empty($machform_path)){
				$mf_path_script =<<<EOT
<script type="text/javascript">
var __machform_path = '{$machform_path}';
</script>
EOT;
			}

			$advance_uploader_js =<<<EOT
<script type="text/javascript" src="{$machform_path}js/uploadify/swfobject.js"></script>
<script type="text/javascript" src="{$machform_path}js/uploadify/jquery.uploadify.js"></script>
<script type="text/javascript" src="{$machform_path}js/uploadifive/jquery.uploadifive.js"></script>
<script type="text/javascript" src="{$machform_path}js/jquery.jqplugin.min.js"></script>
{$mf_path_script}
EOT;
		}

		if($integration_method == 'iframe'){
			$auto_height_js =<<<EOT
<script type="text/javascript" src="{$machform_path}js/jquery.ba-postmessage.min.js"></script>
<script type="text/javascript">
    $(function(){
    	$.postMessage({mf_iframe_height: $('body').outerHeight(true)}, '*', parent );
    });
</script>
EOT;
		}
		
		//if the form has resume enabled and this is multi page form (single page form doesn't have resume option)
		if(!empty($form->resume_enable) && $form->page_total > 1 && $show_password_form === false && empty($inactive_message)){
			
			if(!empty($error_elements['element_resume_email'])){
				$li_resume_email_style = '';
				$li_resume_error_message = "<p class=\"error\">{$error_elements['element_resume_email']}</p>";
				$li_resume_class = 'class="error"';
				$li_resume_checked = 'checked="checked"';
				$li_resume_button_status = 1;
			}else{
				$li_resume_email_style = 'style="display: none"';
				$li_resume_error_message = '';
				$li_resume_class = '';
				$li_resume_checked = '';
				$li_resume_button_status = 0;
			}
			
			$form_resume_markup = <<<EOT
			<li id="li_resume_checkbox">
			<div>
				<span><input type="checkbox" value="1" class="element checkbox" name="element_resume_checkbox" id="element_resume_checkbox" {$li_resume_checked}>
					<label for="element_resume_checkbox" class="choice">{$mf_lang['resume_checkbox_title']}</label>
				</span>
			</div> 
			</li>
			<li id="li_resume_email" {$li_resume_class} {$li_resume_email_style} data-resumebutton="{$li_resume_button_status}" data-resumelabel="{$mf_lang['resume_submit_button_text']}">
				<label for="element_resume_email" class="description">{$mf_lang['resume_email_input_label']}</label>
				<div>
					<input type="text" value="{$populated_values['element_resume_email']}" class="element text medium" name="element_resume_email" id="element_resume_email"> 
				</div><p id="guide_resume_email" class="guidelines"><small>{$mf_lang['resume_guideline']}</small></p> {$li_resume_error_message}
			</li>
EOT;

		}
		
		//if the form has enabled merchant support and set the total payment to be displayed
		if(!empty($form->payment_enable_merchant) && !empty($form->payment_show_total)){
			
			$currency_symbol = '&#36;';
			
			switch($form->payment_currency){
				case 'USD' : $currency_symbol = '&#36;';break;
				case 'EUR' : $currency_symbol = '&#8364;';break;
				case 'GBP' : $currency_symbol = '&#163;';break;
				case 'AUD' : $currency_symbol = 'A&#36;';break;
				case 'CAD' : $currency_symbol = 'C&#36;';break;
				case 'JPY' : $currency_symbol = '&#165;';break;
				case 'THB' : $currency_symbol = '&#3647;';break;
				case 'HUF' : $currency_symbol = '&#70;&#116;';break;
				case 'CHF' : $currency_symbol = 'CHF';break;
				case 'CZK' : $currency_symbol = '&#75;&#269;';break;
				case 'SEK' : $currency_symbol = 'kr';break;
				case 'DKK' : $currency_symbol = 'kr';break;
				case 'NOK' : $currency_symbol = 'kr';break;
				case 'PHP' : $currency_symbol = '&#36;';break;
				case 'IDR' : $currency_symbol = 'Rp';break;
				case 'MYR' : $currency_symbol = 'RM';break;
				case 'ZAR' : $currency_symbol = 'R';break;
				case 'PLN' : $currency_symbol = '&#122;&#322;';break;
				case 'BRL' : $currency_symbol = 'R&#36;';break;
				case 'HKD' : $currency_symbol = 'HK&#36;';break;
				case 'MXN' : $currency_symbol = 'Mex&#36;';break;
				case 'TWD' : $currency_symbol = 'NT&#36;';break;
				case 'TRY' : $currency_symbol = 'TL';break;
				case 'NZD' : $currency_symbol = '&#36;';break;
				case 'SGD' : $currency_symbol = '&#36;';break;
			}
		
			if($form->payment_price_type == 'variable'){	
				//if this is multipage form, we need to get the total selected price from other pages
				if($form->page_total > 1){
					$other_page_total_payment = (double) mf_get_payment_total($dbh,$form_id,$session_id,$page_number);
					$other_page_total_data_tag = 'data-basetotal="'.$other_page_total_payment.'"';
				}else{
					$other_page_total_data_tag = 'data-basetotal="0"';
				}
			}elseif ($form->payment_price_type == 'fixed') {
				$other_page_total_data_tag = 'data-basetotal="'.$form->payment_price_amount.'"';
			}

			//display tax info if enabled
			if(!empty($form->enable_tax) && !empty($form->tax_rate)){
				$tax_markup = "<h5>+{$mf_lang['tax']} {$form->tax_rate}&#37;</h5>";
			}

			//display discount info if applicable
			//the discount info can only being displayed when the form is having review enabled or a multipage form
			if(!empty($form->review) || $form->page_total > 1){
				$session_id = session_id();

				$is_discount_applicable = false;

				//if the discount element for the current session having any value, we can be certain that the discount code has been validated and applicable
				if(!empty($form->enable_discount)){
					$query = "select element_{$form->discount_element_id} coupon_element from ".MF_TABLE_PREFIX."form_{$form_id}_review where `session_id` = ?";
					$params = array($session_id);
					
					$sth = mf_do_query($query,$params,$dbh);
					$row = mf_do_fetch_result($sth);
					
					if(!empty($row['coupon_element'])){
						$is_discount_applicable = true;
					}
				}

				if($is_discount_applicable){
					if($form->discount_type == 'percent_off'){
						$discount_markup = "<h5>-{$mf_lang['discount']} {$form->discount_amount}&#37;</h5>";
					}else{
						$discount_markup = "<h5>-{$mf_lang['discount']} {$currency_symbol}{$form->discount_amount}</h5>";
					}
				}
			}


			if(!empty($tax_markup) || !empty($discount_markup)){
				$payment_extra_markup =<<<EOT
				<span class="total_extra">
					{$discount_markup}
					{$tax_markup}
				</span>
EOT;
			}
			
			$payment_total_markup = <<<EOT
			<li class="total_payment" {$other_page_total_data_tag}>
				<span class="total_main">
					<h3>{$currency_symbol}<var>0</var></h3>
					<h5>{$mf_lang['payment_total']}</h5>
				</span>
				{$payment_extra_markup}
			</li>
EOT;
			
			if(empty($form->active) || $form_has_maximum_entries || $is_edit_entry){ 
				//if form is not active or this is edit_entry page, don't show the total payment
				$payment_total_markup = '';
			}
			
			if($form->payment_total_location == 'top'){
				$payment_total_markup_top = $payment_total_markup;
			}else if($form->payment_total_location == 'bottom'){
				$payment_total_markup_bottom = $payment_total_markup;
			}else if($form->payment_total_location == 'top-bottom' || $form->payment_total_location == 'all'){
				$payment_total_markup_top 	 = $payment_total_markup;
				$payment_total_markup_bottom = $payment_total_markup;
			}
		}
		
		if(!empty($inactive_message)){
			$pagination_header = '';
			$button_markup = '';
		}

		if(empty($mf_settings['disable_machform_link'])){
			$powered_by_markup = 'Powered by <a href="http://www.appnitro.com" target="_blank">MachForm</a>';
		}else{
			$powered_by_markup = '';
		}

		$jquery_url = $machform_path.'js/jquery.min.js';

		//load custom javascript if enabled
		$custom_script_js = '';
		if(!empty($form->custom_script_enable) && !empty($form->custom_script_url)){
			$custom_script_js = '<script type="text/javascript" src="'.$form->custom_script_url.'"></script>';
		}

		//if advanced form code being used, display the form without body container
		if($integration_method == 'php'){
			$container_class .= " integrated";

			if(!empty($edit_id)){
				$view_css_markup = '<link rel="stylesheet" type="text/css" href="css/edit_entry.css" media="all" />';
			}else{
				$view_css_markup = "<link rel=\"stylesheet\" type=\"text/css\" href=\"{$machform_path}{$css_dir}view.css\" media=\"all\" />";
			}

			$form_markup = <<<EOT
{$view_css_markup}
<link rel="stylesheet" type="text/css" href="{$machform_path}view.mobile.css" media="all" />
{$theme_css_link}
{$font_css_markup}
<style>
html{
	background: none repeat scroll 0 0 transparent;
	background-color: transparent;
}
</style>
<script type="text/javascript" src="{$jquery_url}"></script>
<script type="text/javascript" src="{$machform_path}js/jquery-ui/ui/jquery.effects.core.js"></script>
<script type="text/javascript" src="{$machform_path}view.js"></script>
{$recaptcha2_header}
{$advance_uploader_js}
{$calendar_init}
{$signature_pad_init}
{$logic_js}
{$custom_script_js}
<div id="main_body" class="{$container_class}">

	<div id="form_container">
	
		<h1><a>{$form->name}</a></h1>
		<form id="form_{$form->id}" class="appnitro {$form->label_alignment}" {$form_enc_type} method="post" data-highlightcolor="{$field_highlight_color}" action="#main_body">
			{$form_desc_div}						
			<ul {$ul_class}>
			{$pagination_header}
			{$payment_total_markup_top}
			{$form->error_message}
			{$all_element_markup}
			{$custom_element}
			{$payment_total_markup_bottom}
			{$form_resume_markup}
			{$button_markup}
			</ul>
		</form>	
		<div id="footer">
			{$powered_by_markup}
		</div>
	</div>	
</div>

EOT;
		}else{
			$self_address = htmlentities($_SERVER['PHP_SELF']); //prevent XSS
			$form_markup = <<<EOT
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html lang="en" xml:lang="en" {$html_class_tag} xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
<title>{$page_main_title}</title>
<link rel="stylesheet" type="text/css" href="{$machform_path}{$css_dir}view.css" media="all" />
<link rel="stylesheet" type="text/css" href="{$machform_path}view.mobile.css" media="all" />
{$theme_css_link}
{$font_css_markup}
<script type="text/javascript" src="{$jquery_url}"></script>
<script type="text/javascript" src="{$machform_path}js/jquery-ui/ui/jquery.effects.core.js"></script>
<script type="text/javascript" src="{$machform_path}view.js"></script>
{$recaptcha2_header}
{$advance_uploader_js}
{$calendar_init}
{$signature_pad_init}
{$logic_js}
{$auto_height_js}
{$custom_script_js}
</head>
<body id="main_body" class="{$container_class}">
	
	<div id="form_container" class="{$form_container_class}">
	
		<h1><a>{$form->name}</a></h1>
		<form id="form_{$form->id}" class="appnitro {$form->label_alignment}" {$form_enc_type} method="post" data-highlightcolor="{$field_highlight_color}" action="{$self_address}">
			{$form_desc_div}						
			<ul {$ul_class}>
			{$pagination_header}
			{$payment_total_markup_top}
			{$form->error_message}
			{$all_element_markup}
			{$custom_element}
			{$payment_total_markup_bottom}
			{$form_resume_markup}
			{$button_markup}
			</ul>
		</form>	
		<div id="footer">
			{$powered_by_markup}
		</div>
	</div>
	
	</body>
</html>
EOT;
		}

		return $form_markup;
		
	}
	
	
	//display the form within the form builder page
	function mf_display_raw_form($dbh,$form_id){
		
		global $mf_lang;
		
		
		//get form properties data
		$query 	= "select 
						 form_name,
						 form_description,
						 form_label_alignment,
						 form_page_total,
						 form_lastpage_title,
						 form_submit_primary_text,
						 form_submit_secondary_text,
						 form_submit_primary_img,
						 form_submit_secondary_img,
						 form_submit_use_image,
						 form_pagination_type 
				     from 
				     	 ".MF_TABLE_PREFIX."forms 
				    where 
				    	 form_id = ?";
		$params = array($form_id);
	
		$sth = mf_do_query($query,$params,$dbh);
		$row = mf_do_fetch_result($sth);
	
		$form = new stdClass();
		
		$form->id 				= $form_id;
		$form->name 			= $row['form_name'];
		$form->description 		= $row['form_description'];
		$form->label_alignment 	= $row['form_label_alignment'];
		$form->page_total 		= $row['form_page_total'];
		$form->lastpage_title 	= $row['form_lastpage_title'];
		$form->submit_primary_text 	 = $row['form_submit_primary_text'];
		$form->submit_secondary_text = $row['form_submit_secondary_text'];
		$form->submit_primary_img 	 = $row['form_submit_primary_img'];
		$form->submit_secondary_img  = $row['form_submit_secondary_img'];
		$form->submit_use_image  	 = (int) $row['form_submit_use_image'];
		$form->pagination_type		 = $row['form_pagination_type'];
		
		$matrix_elements = array();
		
		//get elements data
		//get element options first and store it into array
		$query = "select 
						element_id,
						option_id,
						`position`,
						`option`,
						option_is_default 
				    from 
				    	".MF_TABLE_PREFIX."element_options 
				   where 
				   		form_id = ? and live = 1 
				order by 
						element_id asc,`position` asc";
		$params = array($form_id);
		$sth = mf_do_query($query,$params,$dbh);
		
		while($row = mf_do_fetch_result($sth)){
			$element_id = $row['element_id'];
			$option_id  = $row['option_id'];
			$options_lookup[$element_id][$option_id]['position'] 		  = $row['position'];
			$options_lookup[$element_id][$option_id]['option'] 			  = $row['option'];
			$options_lookup[$element_id][$option_id]['option_is_default'] = $row['option_is_default'];
		}
	
		
		//get elements data
		$element = array();
		$query = "select 
						element_id,
						element_title,
						element_guidelines,
						element_size,
						element_is_required,
						element_is_readonly,
						element_is_unique,
						element_is_private,
						element_type,
						element_position,
						element_default_value,
						element_enable_placeholder,
						element_constraint,
						element_choice_has_other,
						element_choice_other_label,
						element_choice_columns,
						element_time_showsecond, 
						element_time_24hour,
						element_address_hideline2,
						element_address_us_only,
						element_date_enable_range,
						element_date_range_min,
						element_date_range_max,
						element_date_enable_selection_limit,
						element_date_selection_max,
						element_date_disable_past_future,
						element_date_past_future,
						element_date_disable_weekend,
						element_date_disable_specific,
						element_date_disabled_list,
						element_file_enable_type_limit,
						element_file_block_or_allow,
						element_file_type_list,
						element_file_as_attachment,
						element_file_enable_advance,
						element_file_auto_upload,
						element_file_enable_multi_upload,
						element_file_max_selection,
						element_file_enable_size_limit,
						element_file_size_max,
						element_submit_use_image,
						element_submit_primary_text,
						element_submit_secondary_text,
						element_submit_primary_img,
						element_submit_secondary_img,
						element_page_title,
						element_page_number,
						element_matrix_allow_multiselect,
						element_matrix_parent_id,
						element_range_min,
						element_range_max,
						element_range_limit_by,
						element_section_display_in_email,
						element_section_enable_scroll 
					from 
						".MF_TABLE_PREFIX."form_elements 
				   where 
				   		form_id = ? and element_status='1'
				order by 
						element_position asc";
		$params = array($form_id);
	
		$sth = mf_do_query($query,$params,$dbh);
		
		$j=0;
		$has_calendar = false;
		$has_guidelines = false;
		
		$page_title_array = array();
		
		while($row = mf_do_fetch_result($sth)){
			$element_id = $row['element_id'];
			
			//lookup element options first
			$element_options = array();
			if(!empty($options_lookup[$element_id])){
				
				$i=0;
				foreach ($options_lookup[$element_id] as $option_id=>$data){
					$element_options[$i] = new stdClass();
					$element_options[$i]->id 		 = $option_id;
					$element_options[$i]->option 	 = $data['option'];
					$element_options[$i]->is_default = $data['option_is_default'];
					$element_options[$i]->is_db_live = 1;
					
					$i++;
				}
			}
			
			//populate elements
			$element[$j] = new stdClass();
			$element[$j]->title 		= nl2br($row['element_title']);
			$element[$j]->guidelines 	= nl2br($row['element_guidelines']);
			
			if(!empty($row['element_guidelines']) && ($row['element_type'] != 'section') && ($row['element_type'] != 'matrix')){
				$has_guidelines = true;
			}
			
			$element[$j]->size 			= $row['element_size'];
			$element[$j]->default_value = htmlspecialchars($row['element_default_value']);
			$element[$j]->is_required 	= $row['element_is_required'];
			$element[$j]->is_readonly 	= $row['element_is_readonly'];
			$element[$j]->is_unique 	= $row['element_is_unique'];
			$element[$j]->is_private 	= $row['element_is_private'];
			$element[$j]->type 			= $row['element_type'];
			$element[$j]->position 		= $row['element_position'];
			$element[$j]->id 			= $row['element_id'];
			$element[$j]->is_db_live 	 = 1;
			$element[$j]->is_design_mode = true;
			$element[$j]->enable_placeholder = (int) $row['element_enable_placeholder'];
			$element[$j]->choice_has_other   = (int) $row['element_choice_has_other'];
			$element[$j]->choice_other_label = $row['element_choice_other_label'];
			$element[$j]->choice_columns   	 = (int) $row['element_choice_columns'];
			$element[$j]->time_showsecond	 = (int) $row['element_time_showsecond'];
			$element[$j]->time_24hour	 	 = (int) $row['element_time_24hour'];
			$element[$j]->address_hideline2	 = (int) $row['element_address_hideline2'];
			$element[$j]->address_us_only	 = (int) $row['element_address_us_only'];
			$element[$j]->date_enable_range	 = (int) $row['element_date_enable_range'];
			$element[$j]->date_range_min	 = $row['element_date_range_min'];
			$element[$j]->date_range_max	 = $row['element_date_range_max'];
			$element[$j]->date_enable_selection_limit	= (int) $row['element_date_enable_selection_limit'];
			$element[$j]->date_selection_max	 		= (int) $row['element_date_selection_max'];
			$element[$j]->date_disable_past_future	 	= (int) $row['element_date_disable_past_future'];
			$element[$j]->date_past_future	 			= $row['element_date_past_future'];
			$element[$j]->date_disable_weekend	 		= (int) $row['element_date_disable_weekend'];
			$element[$j]->date_disable_specific	 		= (int) $row['element_date_disable_specific'];
			$element[$j]->date_disabled_list	 		= $row['element_date_disabled_list'];
			$element[$j]->file_enable_type_limit	 	= (int) $row['element_file_enable_type_limit'];						
			$element[$j]->file_block_or_allow	 		= $row['element_file_block_or_allow'];
			$element[$j]->file_type_list	 			= $row['element_file_type_list'];
			$element[$j]->file_as_attachment	 		= (int) $row['element_file_as_attachment'];	
			$element[$j]->file_enable_advance	 		= (int) $row['element_file_enable_advance'];
			$element[$j]->file_auto_upload	 			= (int) $row['element_file_auto_upload'];
			$element[$j]->file_enable_multi_upload	 	= (int) $row['element_file_enable_multi_upload'];
			$element[$j]->file_max_selection	 		= (int) $row['element_file_max_selection'];
			$element[$j]->file_enable_size_limit	 	= (int) $row['element_file_enable_size_limit'];
			$element[$j]->file_size_max	 				= (int) $row['element_file_size_max'];
			$element[$j]->submit_use_image	 			= (int) $row['element_submit_use_image'];
			$element[$j]->submit_primary_text	 		= $row['element_submit_primary_text'];
			$element[$j]->submit_secondary_text	 		= $row['element_submit_secondary_text'];
			$element[$j]->submit_primary_img	 		= $row['element_submit_primary_img'];
			$element[$j]->submit_secondary_img	 		= $row['element_submit_secondary_img'];
			$element[$j]->page_title	 				= $row['element_page_title'];
			$element[$j]->page_number	 				= (int) $row['element_page_number'];
			$element[$j]->page_total	 				= $form->page_total;
			$element[$j]->matrix_allow_multiselect	 	= (int) $row['element_matrix_allow_multiselect'];
			$element[$j]->matrix_parent_id	 			= (int) $row['element_matrix_parent_id'];
			$element[$j]->range_min	 					= $row['element_range_min'];
			$element[$j]->range_max	 					= $row['element_range_max'];
			$element[$j]->range_limit_by	 			= $row['element_range_limit_by'];
			$element[$j]->section_display_in_email	 	= (int) $row['element_section_display_in_email'];
			$element[$j]->section_enable_scroll	 		= (int) $row['element_section_enable_scroll'];

			$element[$j]->constraint 	= $row['element_constraint'];
			if(!empty($element_options)){
				$element[$j]->options 	= $element_options;
			}else{
				$element[$j]->options 	= '';
			}
			
			if($row['element_type'] == 'page_break'){
				$page_title_array[] = $row['element_page_title'];
			}
			
			//if the element is a matrix field and not the parent, store the data into a lookup array for later use when rendering the markup
			if($row['element_type'] == 'matrix' && !empty($row['element_matrix_parent_id'])){
				
				$parent_id 	 = $row['element_matrix_parent_id'];
				$el_position = $row['element_position'];
				$matrix_elements[$parent_id][$el_position]['title'] = $element[$j]->title; 
				$matrix_elements[$parent_id][$el_position]['id'] 	= $element[$j]->id; 
				
				$matrix_child_option_id = '';
				foreach($element_options as $value){
					$matrix_child_option_id .= $value->id.',';
				}
				$matrix_child_option_id = rtrim($matrix_child_option_id,',');
				$matrix_elements[$parent_id][$el_position]['children_option_id'] = $matrix_child_option_id; 
				
				//remove it from the main element array
				$element[$j] = array();
				unset($element[$j]);
				$j--;
			}
			
			$j++;
		}

		
		
		
		//generate html markup for each element
		$all_element_markup = '';
		foreach ($element as $element_data){
			//if this is matrix field, build the children data from $matrix_elements array
			if($element_data->type == 'matrix'){
				$element_data->matrix_children = $matrix_elements[$element_data->id];
			}
			$all_element_markup .= call_user_func('mf_display_'.$element_data->type,$element_data);
		}
		
		if(empty($all_element_markup)){
			$all_element_markup = '<li id="li_dummy">&nbsp;</li>';
		}	
				
				

		if(!empty($form->name) || !empty($form->description)){
			$form->description = nl2br($form->description);
			$form_desc_div =<<<EOT
		<div id="form_header" class="form_description">
			<h2 id="form_header_title">{$form->name}</h2>
			<p id="form_header_desc">{$form->description}</p>
		</div>
EOT;
		}else{
			$form_desc_div =<<<EOT
		<div id="form_header" class="form_description">
			<h2 id="form_header_title"><i>This form has no title</i></h2>
			<p id="form_header_desc"></p>
		</div>
EOT;
		}

		if($has_guidelines){
			$container_class = "integrated";
		}else{
			$container_class = "integrated no_guidelines";
		}
		
		
		//if the form has multiple pages
		//display the pagination header
		if($form->page_total > 1){
			
			
			//build pagination header based on the selected type. possible values:
			//steps - display multi steps progress
			//percentage - display progress bar with percentage
			//disabled - disabled
			
			if($form->pagination_type == 'steps'){
				
				$page_titles_markup = '';
				
				$i=1;
				foreach ($page_title_array as $page_title){
					if($i==1){
						$ap_tp_num_active = ' ap_tp_num_active';
						$ap_tp_text_active = ' ap_tp_text_active';
					}else{
						$ap_tp_num_active = '';
						$ap_tp_text_active = '';
					}
					
					$page_titles_markup .= '<td align="center"><span id="page_num_'.$i.'" class="ap_tp_num'.$ap_tp_num_active.'">'.$i.'</span><span id="page_title_'.$i.'" class="ap_tp_text'.$ap_tp_text_active.'">'.$page_title.'</span></td><td align="center" class="ap_tp_arrow">&gt;</td>'."\n";
					$i++;
				}
				
				//add the last page title into the pagination header markup
				$page_titles_markup .= '<td align="center"><span id="page_num_'.$i.'" class="ap_tp_num">'.$i.'</span><span id="page_title_'.$i.'" class="ap_tp_text">'.$form->lastpage_title.'</span></td>';
				
			
				$pagination_header =<<<EOT
			<li id="pagination_header" class="li_pagination" title="Click to edit">
			 <table class="ap_table_pagination" width="100%" border="0" cellspacing="0" cellpadding="0">
			  <tr> 
			  	{$page_titles_markup}
			  </tr>
			</table>
			</li>
EOT;
			}else if($form->pagination_type == 'percentage'){
				$page_total = count($page_title_array) + 1;
				$percent_value = round((1/$page_total) * 100);
				$pagination_header =<<<EOT
			<li id="pagination_header" class="li_pagination" title="Click to edit">
			    <h3 id="page_title_1">Page 1 of {$page_total} - {$page_title_array[0]}</h3>
				<div class="mf_progress_container">          
			    	<div id="mf_progress_percentage" class="mf_progress_value" style="width: {$percent_value}%"><span>{$percent_value}%</span></div>
				</div>
			</li>
EOT;
			}else{
			
				$pagination_header =<<<EOT
			<li id="pagination_header" class="li_pagination" title="Click to edit">
			    <h3 class="no_header">Pagination Header Disabled</h3>
			</li>
EOT;
			}

			if($form->submit_use_image == 1){
				$btn_class = ' hide';
				$image_class = '';
			}else{
				$btn_class = '';
				$image_class = ' hide';
			}  	
			
			if(empty($form->submit_primary_img)){
				$form->submit_primary_img = 'images/empty.gif';
			}
			
			if(empty($form->submit_secondary_img)){
				$form->submit_secondary_img = 'images/empty.gif';
			}

			$pagination_footer =<<<EOT
		<li title="Click to edit" class="page_break synched" id="li_lastpage">
			<div>
				<table width="100%" cellspacing="0" cellpadding="0" border="0" class="ap_table_pagination">
					<tbody><tr>
						<td align="left" style="vertical-align: bottom;">
							<input type="submit" class="btn_primary btn_submit{$btn_class}" name="btn_submit_lastpage" id="btn_submit_lastpage" value="{$form->submit_primary_text}" disabled="disabled">
							<input type="submit" class="btn_secondary btn_submit{$btn_class}" name="btn_prev_lastpage" id="btn_prev_lastpage" value="{$form->submit_secondary_text}" disabled="disabled">
							<input type="image" src="{$form->submit_primary_img}" class="img_primary img_submit{$image_class}" alt="Submit" name="img_submit_lastpage" id="img_submit_lastpage" value="Submit" disabled="disabled">
							<input type="image" src="{$form->submit_secondary_img}" class="img_secondary img_submit{$image_class}" alt="Previous" name="img_prev_lastpage" id="img_prev_lastpage" value="Previous" disabled="disabled">
						</td> 
						<td width="75px" align="center" style="vertical-align: top;">
							<span class="ap_tp_num" name="pagenum_lastpage" id="pagenum_lastpage">{$form->page_total}</span>
							<span class="ap_tp_text" name="pagetotal_lastpage" id="pagetotal_lastpage">Page {$form->page_total} of {$form->page_total}</span>
						</td>
					</tr>
				</tbody></table>
			</div>
		</li>
EOT;
		}

			
		$form_markup =<<<EOT
<div id="main_body" class="{$container_class}">
		
	<div id="form_container">
	
		<h1><a>{$form->name}</a></h1>
		<form id="form_builder_preview" class="appnitro {$form->label_alignment}" method="post" action="#main_body">
			{$form_desc_div}				
			<ul {$ul_class} id="form_builder_sortable" title="Click field to edit. Drag to reorder.">
			{$pagination_header}	
			{$all_element_markup}
			{$pagination_footer}
			</ul>
		</form>	
	</div>
</div>
EOT;
		return $form_markup;
		
	}
	
	function mf_display_success($dbh,$form_id,$form_params=array()){
		global $mf_lang;
		
		if(!empty($form_params['integration_method'])){
			$integration_method = $form_params['integration_method'];
		}else{
			$integration_method = '';
		}

		if(!empty($form_params['machform_path'])){
			$machform_path = $form_params['machform_path'];
		}else{
			$machform_path = '';
		}



		$mf_settings = mf_get_settings($dbh);
		
		//get form properties data
		$query 	= "select 
						  form_success_message,
						  form_has_css,
						  form_name,
						  form_theme_id,
						  form_language,
						  form_custom_script_enable,
					 	  form_custom_script_url 
				     from 
				     	 ".MF_TABLE_PREFIX."forms 
				    where 
				    	 form_id=?";
		$params = array($form_id);
		
		$sth = mf_do_query($query,$params,$dbh);
		$row = mf_do_fetch_result($sth);
	
		$form = new stdClass();
		
		$form->id 				= $form_id;
		$form->success_message  = $row['form_success_message'];
		$form->has_css 			= $row['form_has_css'];
		$form->name 			= $row['form_name'];
		$form->theme_id 		= $row['form_theme_id'];
		$form->language 		= trim($row['form_language']);

		$form->custom_script_enable = (int) $row['form_custom_script_enable'];
		$form->custom_script_url 	= $row['form_custom_script_url'];
		
		if(!empty($form->language)){
			mf_set_language($form->language);
		}

		//parse success messages with template variables
		if(!empty($_SESSION['mf_success_entry_id']) && empty($_SESSION['mf_form_resume_url'][$form_id])){
			
			$entry_id = $_SESSION['mf_success_entry_id'];
			$form->success_message = mf_parse_template_variables($dbh,$form_id,$entry_id,$form->success_message);
		}
		
		//check for specific form css, if any, use it instead
		if($form->has_css){
			$css_dir = $mf_settings['data_dir']."/form_{$form_id}/css/";
		}
		
		//if this form is using custom theme
		if(!empty($form->theme_id)){
			//get the field highlight color for the particular theme
			$query = "SELECT 
							highlight_bg_type,
							highlight_bg_color,
							form_shadow_style,
							form_shadow_size,
							form_shadow_brightness,
							form_button_type,
							form_button_text,
							form_button_image,
							theme_has_css  
						FROM 
							".MF_TABLE_PREFIX."form_themes 
					   WHERE 
					   		theme_id = ?";
			$params = array($form->theme_id);
			
			$sth = mf_do_query($query,$params,$dbh);
			$row = mf_do_fetch_result($sth);
			
			$form_shadow_style 		= $row['form_shadow_style'];
			$form_shadow_size 		= $row['form_shadow_size'];
			$form_shadow_brightness = $row['form_shadow_brightness'];
			$theme_has_css = (int) $row['theme_has_css'];
			
			//if the theme has css file, make sure to refer to that file
			//otherwise, generate the css dynamically
			
			if(!empty($theme_has_css)){
				$theme_css_link = '<link rel="stylesheet" type="text/css" href="'.$machform_path.$mf_settings['data_dir'].'/themes/theme_'.$form->theme_id.'.css" media="all" />';
			}else{
				$theme_css_link = '<link rel="stylesheet" type="text/css" href="'.$machform_path.'css_theme.php?theme_id='.$form->theme_id.'" media="all" />';
			}
			
			if($row['highlight_bg_type'] == 'color'){
				$field_highlight_color = $row['highlight_bg_color'];
			}else{ 
				//if the field highlight is using pattern instead of color, set the color to empty string
				$field_highlight_color = ''; 
			}
			
			//get the css link for the fonts
			$font_css_markup = mf_theme_get_fonts_link($dbh,$form->theme_id);
			
			//get the form shadow classes
			if(!empty($form_shadow_style) && ($form_shadow_style != 'disabled')){
				preg_match_all("/[A-Z]/",$form_shadow_style,$prefix_matches);
				//this regex simply get the capital characters of the shadow style name
				//example: RightPerspectiveShadow result to RPS and then being sliced to RP
				$form_shadow_prefix_code = substr(implode("",$prefix_matches[0]),0,-1);
				
				$form_shadow_size_class  = $form_shadow_prefix_code.ucfirst($form_shadow_size);
				$form_shadow_brightness_class = $form_shadow_prefix_code.ucfirst($form_shadow_brightness);

				if(empty($integration_method)){ //only display shadow if the form is not being embedded using any method
					$form_container_class = $form_shadow_style.' '.$form_shadow_size_class.' '.$form_shadow_brightness_class;
				}
			}
			
			
			
		}else{ //if the form doesn't have any theme being applied
			$field_highlight_color = '#FFF7C0';
			
			if(empty($integration_method)){
				$form_container_class = ''; //default shadow as of v4.2 is no-shadow
			}else{
				$form_container_class = ''; //dont show any shadow when the form being embedded
			}
		}
		
		
		if(!empty($_SESSION['mf_form_resume_url'][$form_id])){

			$resume_success_title = $mf_lang['resume_success_title'];
			$resume_success_content = sprintf($mf_lang['resume_success_content'],$_SESSION['mf_form_resume_url'][$form_id]);

			$success_markup = <<<EOT
			<h2>{$resume_success_title}</h2>
			<h3>{$resume_success_content}</h3>
EOT;
		}else{
			$success_markup = "<h2>{$form->success_message}</h2>";		
		}

		if(empty($mf_settings['disable_machform_link'])){
			$powered_by_markup = 'Powered by <a href="http://www.appnitro.com" target="_blank">MachForm</a>';
		}else{
			$powered_by_markup = '';
		}
		
		//load custom javascript if enabled
		$custom_script_js = '';
		if(!empty($form->custom_script_enable) && !empty($form->custom_script_url)){
			$custom_script_js = '<script type="text/javascript" src="'.$form->custom_script_url.'"></script>';
		}

		$jquery_url = $machform_path.'js/jquery.min.js';

		if($integration_method == 'php'){
			$form_markup = <<<EOT
<link rel="stylesheet" type="text/css" href="{$machform_path}{$css_dir}view.css" media="all" />
<link rel="stylesheet" type="text/css" href="{$machform_path}view.mobile.css" media="all" />
{$theme_css_link}
{$font_css_markup}
{$custom_script_js}
<style>
html{
	background: none repeat scroll 0 0 transparent;
}
</style>

<div id="main_body" class="integrated">
	<div id="form_container">
		<h1><a>Appnitro MachForm</a></h1>
		<div class="form_success">
			{$success_markup}
		</div>
		<div id="footer" class="success">
			{$powered_by_markup}
		</div>		
	</div>
	
</div>
EOT;

		}else{
	
			if($integration_method == 'iframe'){
				$embed_class = 'class="embed"';
				$auto_height_js =<<<EOT
<script type="text/javascript" src="{$jquery_url}"></script>
<script type="text/javascript" src="{$machform_path}js/jquery.ba-postmessage.min.js"></script>
<script type="text/javascript">
    $(function(){
    	$.postMessage({mf_iframe_height: $('body').outerHeight(true)}, '*', parent );
    });
</script>
EOT;
			}else{
				$embed_class = '';
			}
			
			$form_markup = <<<EOT
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html lang="en" xml:lang="en" {$embed_class} xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
<title>{$form->name}</title>
<link rel="stylesheet" type="text/css" href="{$machform_path}{$css_dir}view.css" media="all" />
<link rel="stylesheet" type="text/css" href="{$machform_path}view.mobile.css" media="all" />
{$theme_css_link}
{$font_css_markup}
{$auto_height_js}
{$custom_script_js}
</head>
<body id="main_body">
	<div id="form_container" class="{$form_container_class}">
	
		<h1><a>Appnitro MachForm</a></h1>
			
		<div class="form_success">
			{$success_markup}
		</div>
		<div id="footer" class="success">
			{$powered_by_markup}
		</div>		
	</div>
</body>
</html>
EOT;
		}

		return $form_markup;
	}
	
	
	
	//display form confirmation page
	function mf_display_form_review($dbh,$form_id,$record_id,$from_page_num,$form_params=array()){
		global $mf_lang;
		
		if(!empty($form_params['integration_method'])){
			$integration_method = $form_params['integration_method'];
		}else{
			$integration_method = '';
		}

		if(!empty($form_params['machform_path'])){
			$machform_path = $form_params['machform_path'];
		}else{
			$machform_path = '';
		}

		if(!empty($form_params['machform_data_path'])){
			$machform_data_path = $form_params['machform_data_path'];
		}else{
			$machform_data_path = '';
		}

		$mf_settings = mf_get_settings($dbh);
		
		//get form properties data
		$query 	= "select 
						  form_name,
						  form_has_css,
						  form_redirect,
						  form_language,
						  form_review_primary_text,
						  form_review_secondary_text,
						  form_review_primary_img,
						  form_review_secondary_img,
						  form_review_use_image,
						  form_review_title,
						  form_review_description,
						  form_resume_enable,
						  form_page_total,
						  form_lastpage_title,
						  form_pagination_type,
						  form_theme_id,
						  payment_show_total,
						  payment_total_location,
						  payment_enable_merchant,
						  payment_currency,
						  payment_price_type,
						  payment_price_amount,
						  payment_enable_discount,
						  payment_discount_type,
						  payment_discount_amount,
						  payment_discount_element_id,
						  payment_enable_tax,
					 	  payment_tax_rate,
					 	  logic_field_enable,
					 	  form_custom_script_enable,
					 	  form_custom_script_url 
				     from 
				     	 ".MF_TABLE_PREFIX."forms 
				    where 
				    	 form_id=?";
		$params = array($form_id);
		
		$sth = mf_do_query($query,$params,$dbh);
		$row = mf_do_fetch_result($sth);
		
		$form_language = $row['form_language'];

		if(!empty($form_language)){
			mf_set_language($form_language);
		}
		
		$form_has_css 			= $row['form_has_css'];
		$form_redirect			= $row['form_redirect'];
		$form_review_primary_text 	 = $row['form_review_primary_text'];
		$form_review_secondary_text  = $row['form_review_secondary_text'];
		$form_review_primary_img 	 = $row['form_review_primary_img'];
		$form_review_secondary_img   = $row['form_review_secondary_img'];
		$form_review_use_image  	 = (int) $row['form_review_use_image'];
		$form_review_title			 = $row['form_review_title'];
		$form_review_description	 = $row['form_review_description'];
		$form_page_total 			 = $row['form_page_total'];
		$form_lastpage_title 		 = $row['form_lastpage_title'];
		$form_pagination_type		 = $row['form_pagination_type'];
		$form_name					 = htmlspecialchars($row['form_name'],ENT_QUOTES);
		$form_theme_id				 = $row['form_theme_id'];
		$form_resume_enable  	 	 = (int) $row['form_resume_enable'];
		$logic_field_enable 		 = (int) $row['logic_field_enable'];

		$payment_show_total	 		 = (int) $row['payment_show_total'];
		$payment_total_location 	 = $row['payment_total_location'];
		$payment_enable_merchant 	 = (int) $row['payment_enable_merchant'];
		$payment_currency 	   		 = $row['payment_currency'];
		$payment_price_type 	     = $row['payment_price_type'];
		$payment_price_amount    	 = $row['payment_price_amount'];

		$payment_enable_discount 		= (int) $row['payment_enable_discount'];
		$payment_discount_type 	 		= $row['payment_discount_type'];
		$payment_discount_amount 		= (float) $row['payment_discount_amount'];
		$payment_discount_element_id 	= (int) $row['payment_discount_element_id'];

		$payment_enable_tax 		 	= (int) $row['payment_enable_tax'];
		$payment_tax_rate 				= (float) $row['payment_tax_rate'];

		$form_custom_script_enable 		= (int) $row['form_custom_script_enable'];
		$form_custom_script_url 		= $row['form_custom_script_url'];
		
		//prepare entry data for previewing
		$param['strip_download_link'] = true;
		$param['review_mode']    	  = true;
		$param['show_attach_image']   = true;
		$param['machform_data_path']   = $machform_data_path;
		$param['machform_path']   	   = $machform_path;

		
		$entry_details = mf_get_entry_details($dbh,$form_id,$record_id,$param);

		//if logic is enable, get hidden elements
		//we'll need it to hide section break
		if($logic_field_enable){
			$entry_values = mf_get_entry_values($dbh,$form_id,$record_id,true);
			foreach ($entry_values as $element_name => $value) {
				$input_data[$element_name] = $value['default_value'];
			}

			$hidden_elements = array();
			for($i=1;$i<=$form_page_total;$i++){
				$current_page_hidden_elements = array();
				$current_page_hidden_elements = mf_get_hidden_elements($dbh,$form_id,$i,$input_data);
				
				$hidden_elements += $current_page_hidden_elements; //use '+' so that the index won't get lost
			}
		}
		
		$entry_data = '<table id="machform_review_table" width="100%" border="0" cellspacing="0" cellpadding="0"><tbody>'."\n";
		
		$toggle = false;
		foreach ($entry_details as $data){
			//0 should be displayed, empty string don't
			if((empty($data['value']) || $data['value'] == '&nbsp;') && $data['value'] !== 0 && $data['value'] !== '0' && $data['element_type'] !== 'section'){
				continue;
			}	

			//don't display page break within review page
			if($data['label'] == 'mf_page_break' && $data['value'] == 'mf_page_break'){
				continue;
			}

			if($toggle){
				$toggle = false;
				$row_style = 'class="alt"';
			}else{
				$toggle = true;
				$row_style = '';
			}	
			
			if($data['element_type'] == 'section') {
				
				//if this section break is hidden due to logic, don't display it
				if(!empty($hidden_elements) && !empty($hidden_elements[$data['element_id']])){
					continue;
				}

				if(!empty($data['label']) && !empty($data['value']) && ($data['value'] != '&nbsp;')){
					$section_separator = '<br/>';
				}else{
					$section_separator = '';
				}

				$section_break_content = '<span class="mf_section_title">'.nl2br($data['label']).'</span>'.$section_separator.'<span class="mf_section_content">'.nl2br($data['value']).'</span>';

				$entry_data .= "<tr>\n";
				$entry_data .= "<td class=\"mf_review_section_break\" width=\"100%\" colspan=\"2\">".$section_break_content."</td>\n";
				$entry_data .= "</tr>\n";
			}else if($data['element_type'] == 'signature') {
				$element_id = $data['element_id'];

				if($data['element_size'] == 'small'){
					$canvas_height = 70;
				}else if($data['element_size'] == 'medium'){
					$canvas_height = 130;
				}else{
					$canvas_height = 260;
				}

				$signature_markup = <<<EOT
							<div class="mf_sigpad_view" id="mf_sigpad_{$element_id}">
								<canvas class="mf_canvas_pad" width="309" height="{$canvas_height}"></canvas>
							</div>
							<script type="text/javascript">
								$(function(){
									var sigpad_options_{$element_id} = {
										               drawOnly : true,
										               displayOnly: true,
										               bgColour: '#fff',
										               penColour: '#000',
										               validateFields: false
									};
									var sigpad_data_{$element_id} = {$data['value']};
									$('#mf_sigpad_{$element_id}').signaturePad(sigpad_options_{$element_id}).regenerate(sigpad_data_{$element_id});
								});
							</script>
EOT;

				$entry_data .= "<tr {$row_style}>\n";
				$entry_data .= "<td class=\"mf_review_label\" width=\"40%\" style=\"vertical-align: top\">{$data['label']}</td>\n";
				$entry_data .= "<td class=\"mf_review_value\" width=\"60%\">{$signature_markup}</td>\n";
				$entry_data .= "</tr>\n";
			}else{
	  			$entry_data .= "<tr {$row_style}>\n";
	  	    	$entry_data .= "<td class=\"mf_review_label\" width=\"40%\">{$data['label']}</td>\n";
	  			$entry_data .= "<td class=\"mf_review_value\" width=\"60%\">".nl2br($data['value'])."</td>\n";
	  			$entry_data .= "</tr>\n";
  			}
 		}   	
		 	
   	    $entry_data .= '</tbody></table>';

		//check for specific form css, if any, use it instead
		if($form_has_css){
			$css_dir = $mf_settings['data_dir']."/form_{$form_id}/css/";
		}
		
		if($integration_method == 'iframe'){
			$embed_class = 'class="embed"';
		}
		
		
		//if the form has multiple pages
		//display the pagination header
		if($form_page_total > 1){
			
			//build pagination header based on the selected type. possible values:
			//steps - display multi steps progress
			//percentage - display progress bar with percentage
			//disabled - disabled
			
			$page_breaks_data = array();
			$page_title_array = array();
			
			//get page titles
			$query = "SELECT 
							element_page_title
						FROM 
							".MF_TABLE_PREFIX."form_elements
					   WHERE
							form_id = ? and element_status = 1 and element_type = 'page_break'
					ORDER BY 
					   		element_page_number asc";
			$params = array($form_id);
			
			$sth = mf_do_query($query,$params,$dbh);
			while($row = mf_do_fetch_result($sth)){
				$page_title_array[] = $row['element_page_title'];
			}
			
			if($form_pagination_type == 'steps'){
				
				$page_titles_markup = '';
				
				$i=1;
				foreach ($page_title_array as $page_title){
					$page_titles_markup .= '<td align="center"><span id="page_num_'.$i.'" class="ap_tp_num">'.$i.'</span><span id="page_title_'.$i.'" class="ap_tp_text">'.$page_title.'</span></td><td align="center" class="ap_tp_arrow">&gt;</td>'."\n";
					$i++;
				}
				
				//add the last page title into the pagination header markup
				$page_titles_markup .= '<td align="center"><span id="page_num_'.$i.'" class="ap_tp_num">'.$i.'</span><span id="page_title_'.$i.'" class="ap_tp_text">'.$form_lastpage_title.'</span></td>';
			
				$i++;
				$page_titles_markup .= '<td align="center" class="ap_tp_arrow">&gt;</td><td align="center"><span id="page_num_'.$i.'" class="ap_tp_num ap_tp_num_active">'.$i.'</span><span id="page_title_'.$i.'" class="ap_tp_text ap_tp_text_active">'.$form_review_title.'</span></td>';
				
				//if payment enabled, we need to add the pagination header
				if(!empty($payment_enable_merchant)){
					$i++;
					$page_titles_markup .= '<td align="center" class="ap_tp_arrow">&gt;</td><td align="center"><span id="page_num_'.$i.'" class="ap_tp_num">'.$i.'</span><span id="page_title_'.$i.'" class="ap_tp_text">'.$mf_lang['form_payment_header_title'].'</span></td>';
				}
				
				$pagination_header =<<<EOT
			<ul>
			<li id="pagination_header" class="li_pagination">
			 <table class="ap_table_pagination" width="100%" border="0" cellspacing="0" cellpadding="0">
			  <tr> 
			  	{$page_titles_markup}
			  </tr>
			</table>
			</li>
			</ul>
EOT;
			}else if($form_pagination_type == 'percentage'){
				
				if(!empty($payment_enable_merchant)){
					$page_total = count($page_title_array) + 3;
					$current_page_number = $page_total - 1;
					$percent_value = round(($current_page_number/$page_total) * 100);
				}else{
					$page_total = count($page_title_array) + 2;
					$current_page_number = $page_total;
					$percent_value = 99; //it's not make sense to display 100% when the form is not really submitted yet
				}
				
				$page_number_title = sprintf($mf_lang['page_title'],$current_page_number,$page_total);
				$pagination_header =<<<EOT
			<ul>
				<li id="pagination_header" class="li_pagination" title="Click to edit">
			    <h3 id="page_title_{$page_total}">{$page_number_title}</h3>
				<div class="mf_progress_container">          
			    	<div id="mf_progress_percentage" class="mf_progress_value" style="width: {$percent_value}%"><span>{$percent_value}%</span></div>
				</div>
				</li>
			</ul>
EOT;
			}else{			
				$pagination_header = '';
			}
			
		}




		
		//build the button markup (image or text)
		if(!empty($form_review_use_image)){
			$button_markup =<<<EOT
<input id="review_submit" class="submit_img_primary" type="image" name="review_submit" alt="{$form_review_primary_text}" src="{$form_review_primary_img}" />
<input id="review_back" class="submit_img_secondary" type="image" name="review_back" alt="{$form_review_secondary_text}" src="{$form_review_secondary_img}" />
EOT;
		}else{
			$button_markup =<<<EOT
<input id="review_submit" class="button_text btn_primary" type="submit" name="review_submit" value="{$form_review_primary_text}" />
<input id="review_back" class="button_text btn_secondary" type="submit" name="review_back" value="{$form_review_secondary_text}" />
EOT;
		}

		//if this form is using custom theme
		if(!empty($form_theme_id)){
			//get the field highlight color for the particular theme
			$query = "SELECT 
							highlight_bg_type,
							highlight_bg_color,
							form_shadow_style,
							form_shadow_size,
							form_shadow_brightness,
							form_button_type,
							form_button_text,
							form_button_image,
							theme_has_css  
						FROM 
							".MF_TABLE_PREFIX."form_themes 
					   WHERE 
					   		theme_id = ?";
			$params = array($form_theme_id);
			
			$sth = mf_do_query($query,$params,$dbh);
			$row = mf_do_fetch_result($sth);
			
			$form_shadow_style 		= $row['form_shadow_style'];
			$form_shadow_size 		= $row['form_shadow_size'];
			$form_shadow_brightness = $row['form_shadow_brightness'];
			$theme_has_css = (int) $row['theme_has_css'];
			
			//if the theme has css file, make sure to refer to that file
			//otherwise, generate the css dynamically
			
			if(!empty($theme_has_css)){
				$theme_css_link = '<link rel="stylesheet" type="text/css" href="'.$machform_path.$mf_settings['data_dir'].'/themes/theme_'.$form_theme_id.'.css" media="all" />';
			}else{
				$theme_css_link = '<link rel="stylesheet" type="text/css" href="'.$machform_path.'css_theme.php?theme_id='.$form_theme_id.'" media="all" />';
			}
			
			if($row['highlight_bg_type'] == 'color'){
				$field_highlight_color = $row['highlight_bg_color'];
			}else{ 
				//if the field highlight is using pattern instead of color, set the color to empty string
				$field_highlight_color = ''; 
			}
			
			//get the css link for the fonts
			$font_css_markup = mf_theme_get_fonts_link($dbh,$form_theme_id);
			
			//get the form shadow classes
			if(!empty($form_shadow_style) && ($form_shadow_style != 'disabled')){
				preg_match_all("/[A-Z]/",$form_shadow_style,$prefix_matches);
				//this regex simply get the capital characters of the shadow style name
				//example: RightPerspectiveShadow result to RPS and then being sliced to RP
				$form_shadow_prefix_code = substr(implode("",$prefix_matches[0]),0,-1);
				
				$form_shadow_size_class  = $form_shadow_prefix_code.ucfirst($form_shadow_size);
				$form_shadow_brightness_class = $form_shadow_prefix_code.ucfirst($form_shadow_brightness);

				if(empty($integration_method)){ //only display shadow if the form is not being embedded using any method
					$form_container_class = $form_shadow_style.' '.$form_shadow_size_class.' '.$form_shadow_brightness_class;
				}
			}
			
			
			
		}else{ //if the form doesn't have any theme being applied
			$field_highlight_color = '#FFF7C0';
			
			if(empty($integration_method)){
				$form_container_class = ''; //default shadow as of v4.2 is no-shadow
			}else{
				$form_container_class = ''; //dont show any shadow when the form being embedded
			}
		}

		//if the form has enabled merchant support and set the total payment to be displayed
		if(!empty($payment_enable_merchant) && !empty($payment_show_total)){
			
			$currency_symbol = '&#36;';
			
			switch($payment_currency){
				case 'USD' : $currency_symbol = '&#36;';break;
				case 'EUR' : $currency_symbol = '&#8364;';break;
				case 'GBP' : $currency_symbol = '&#163;';break;
				case 'AUD' : $currency_symbol = 'A&#36;';break;
				case 'CAD' : $currency_symbol = 'C&#36;';break;
				case 'JPY' : $currency_symbol = '&#165;';break;
				case 'THB' : $currency_symbol = '&#3647;';break;
				case 'HUF' : $currency_symbol = '&#70;&#116;';break;
				case 'CHF' : $currency_symbol = 'CHF';break;
				case 'CZK' : $currency_symbol = '&#75;&#269;';break;
				case 'SEK' : $currency_symbol = 'kr';break;
				case 'DKK' : $currency_symbol = 'kr';break;
				case 'NOK' : $currency_symbol = 'kr';break;
				case 'PHP' : $currency_symbol = '&#36;';break;
				case 'IDR' : $currency_symbol = 'Rp';break;
				case 'MYR' : $currency_symbol = 'RM';break;
				case 'ZAR' : $currency_symbol = 'R';break;
				case 'PLN' : $currency_symbol = '&#122;&#322;';break;
				case 'BRL' : $currency_symbol = 'R&#36;';break;
				case 'HKD' : $currency_symbol = 'HK&#36;';break;
				case 'MXN' : $currency_symbol = 'Mex&#36;';break;
				case 'TWD' : $currency_symbol = 'NT&#36;';break;
				case 'TRY' : $currency_symbol = 'TL';break;
				case 'NZD' : $currency_symbol = '&#36;';break;
				case 'SGD' : $currency_symbol = '&#36;';break;
			}
		
			
			$session_id = session_id();

			if($payment_price_type == 'variable'){
				$total_payment = (double) mf_get_payment_total($dbh,$form_id,$session_id,0);
			}elseif ($payment_price_type == 'fixed') {
				$total_payment = $payment_price_amount;
			}

			$total_payment = sprintf("%.2f",$total_payment);

			//display tax info if enabled
			if(!empty($payment_enable_tax) && !empty($payment_tax_rate)){
				$tax_markup = "<h5>+{$mf_lang['tax']} {$payment_tax_rate}&#37;</h5>";
			}

			//display discount info if applicable
			//the discount info can only being displayed when the form is having review enabled or a multipage form
			$is_discount_applicable = false;

			//if the discount element for the current session having any value, we can be certain that the discount code has been validated and applicable
			if(!empty($payment_enable_discount)){
				$query = "select element_{$payment_discount_element_id} coupon_element from ".MF_TABLE_PREFIX."form_{$form_id}_review where `id` = ?";
				$params = array($record_id);
				
				$sth = mf_do_query($query,$params,$dbh);
				$row = mf_do_fetch_result($sth);
				
				if(!empty($row['coupon_element'])){
					$is_discount_applicable = true;
				}
			}

			if($is_discount_applicable){
				if($payment_discount_type == 'percent_off'){
					$discount_markup = "<h5>-{$mf_lang['discount']} {$payment_discount_amount}&#37;</h5>";
				}else{
					$discount_markup = "<h5>-{$mf_lang['discount']} {$currency_symbol}{$payment_discount_amount}</h5>";
				}
			}
			
			if(!empty($tax_markup) || !empty($discount_markup)){
				$payment_extra_markup =<<<EOT
				<span class="total_extra">
					{$discount_markup}
					{$tax_markup}
				</span>
EOT;
			}

			$payment_total_markup = <<<EOT
				<ul><li class="total_payment mf_review">
					<span>
						<h3>{$currency_symbol}<var>{$total_payment}</var></h3>
						<h5>{$mf_lang['payment_total']}</h5>
					</span>
					{$payment_extra_markup}
				</li></ul>
EOT;
			
		}
		
		if(empty($mf_settings['disable_machform_link'])){
			$powered_by_markup = 'Powered by <a href="http://www.appnitro.com" target="_blank">MachForm</a>';
		}else{
			$powered_by_markup = '';
		}

		//load custom javascript if enabled
		$custom_script_js = '';
		if(!empty($form_custom_script_enable) && !empty($form_custom_script_url)){
			$custom_script_js = '<script type="text/javascript" src="'.$form_custom_script_url.'"></script>';
		}

		//check for any 'signature' field, if there is any, we need to include the javascript library to display the signature
		$query = "select 
						count(form_id) total_signature_field 
					from 
						".MF_TABLE_PREFIX."form_elements 
				   where 
				   		element_type = 'signature' and 
				   		element_status=1 and 
				   		form_id=?";
		$params = array($form_id);

		$sth = mf_do_query($query,$params,$dbh);
		$row = mf_do_fetch_result($sth);
		if(!empty($row['total_signature_field'])){
			$has_signature_field = true;
		}else{
			$has_signature_field = false;
		}

		$self_address = htmlentities($_SERVER['PHP_SELF']); //prevent XSS
		$jquery_url	  = $machform_path.'js/jquery.min.js';

		if($integration_method == 'php'){

			if($has_signature_field){
				$signature_pad_init = '<!--[if lt IE 9]><script src="'.$machform_path.'js/signaturepad/flashcanvas.js"></script><![endif]-->'."\n".
									  '<script type="text/javascript" src="'.$machform_path.'js/signaturepad/jquery.signaturepad.min.js"></script>'."\n".
									  '<script type="text/javascript" src="'.$machform_path.'js/signaturepad/json2.min.js"></script>'."\n";
			}

			$form_markup = <<<EOT
<script type="text/javascript" src="{$jquery_url}"></script>
<script type="text/javascript" src="{$machform_path}js/jquery-ui/ui/jquery.effects.core.js"></script>
<script type="text/javascript" src="{$machform_path}view.js"></script>
{$signature_pad_init}
{$custom_script_js}
<link rel="stylesheet" type="text/css" href="{$machform_path}{$css_dir}view.css" media="all" />
{$theme_css_link}
{$font_css_markup}
<style>
html{
	background: none repeat scroll 0 0 transparent;
}
</style>

<div id="main_body" class="integrated">
	<div id="form_container">
		<form id="form_{$form->id}" class="appnitro" method="post" action="{$self_address}">
		    <div class="form_description">
				<h2>{$form_review_title}</h2>
				<p>{$form_review_description}</p>
			</div>
			{$pagination_header}
			{$entry_data}
			<ul>
			{$payment_total_markup}
			<li id="li_buttons" class="buttons">
			    <input type="hidden" name="id" value="{$form_id}" />
			    <input type="hidden" name="mf_page_from" value="{$from_page_num}" />
			    {$button_markup}
			</li>
			</ul>
		</form>		
	</div>
</div>
EOT;
		}else{

			if($integration_method == 'iframe'){	
				$auto_height_js =<<<EOT
<script type="text/javascript" src="{$machform_path}js/jquery.ba-postmessage.min.js"></script>
<script type="text/javascript">
    $(function(){
    	$.postMessage({mf_iframe_height: $('body').outerHeight(true)}, '*', parent );
    });
</script>
EOT;
			}

			if($has_signature_field){
					$signature_pad_init = '<!--[if lt IE 9]><script src="'.$machform_path.'js/signaturepad/flashcanvas.js"></script><![endif]-->'."\n".
										  '<script type="text/javascript" src="'.$machform_path.'js/signaturepad/jquery.signaturepad.min.js"></script>'."\n".
										  '<script type="text/javascript" src="'.$machform_path.'js/signaturepad/json2.min.js"></script>'."\n";
			}

			$form_markup = <<<EOT
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html lang="en" xml:lang="en" {$embed_class} xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
<title>{$form_name}</title>
<link rel="stylesheet" type="text/css" href="{$machform_path}{$css_dir}view.css" media="all" />
<link rel="stylesheet" type="text/css" href="{$machform_path}view.mobile.css" media="all" />
{$theme_css_link}
{$font_css_markup}
<script type="text/javascript" src="{$jquery_url}"></script>
<script type="text/javascript" src="{$machform_path}js/jquery-ui/ui/jquery.effects.core.js"></script>
<script type="text/javascript" src="{$machform_path}view.js"></script>
{$auto_height_js}
{$signature_pad_init}
{$custom_script_js}
</head>
<body id="main_body">
	
	<img id="top" src="{$machform_path}images/top.png" alt="" />
	<div id="form_container" class="{$form_container_class}">
	
		<h1><a>MachForm</a></h1>
		<form id="form_{$form_id}" class="appnitro" method="post" action="{$self_address}">
		    <div class="form_description">
				<h2>{$form_review_title}</h2>
				<p>{$form_review_description}</p>
			</div>
			{$pagination_header}
			{$payment_total_markup}
			{$entry_data}
			<ul>
			<li id="li_buttons" class="buttons">
			    <input type="hidden" name="id" value="{$form_id}" />
			    <input type="hidden" name="mf_page_from" value="{$from_page_num}" />
			    {$button_markup}
			</li>
			</ul>
		</form>		
			
	</div>
	<img id="bottom" src="{$machform_path}images/bottom.png" alt="" />
	</body>
</html>
EOT;
		}

		return $form_markup;
	}

	//display form payment page
	function mf_display_form_payment($dbh,$form_id,$record_id,$form_params=array()){
		global $mf_lang;
		
		if(!empty($form_params['integration_method'])){
			$integration_method = $form_params['integration_method'];
		}else{
			$integration_method = '';
		}

		if(!empty($form_params['machform_path'])){
			$machform_path = $form_params['machform_path'];
		}else{
			$machform_path = '';
		}

		if(!empty($form_params['machform_data_path'])){
			$machform_data_path = $form_params['machform_data_path'];
		}else{
			$machform_data_path = '';
		}

		//check for payment_token
		//if exist, the user is resuming the payment from previously unpaid entry
		//we need to set all necessary session if the token is valid
		if(!empty($form_params['pay_token'])){
			$payment_resume_token = trim($form_params['pay_token']);
			$payment_resume_token = base64_decode($payment_resume_token);

			$exploded  = explode('-', $payment_resume_token);
			$record_id = (int) $exploded[0];
			$date_created_md5 = $exploded[1];

			//compare the date created md5 with the existing record
			$query = "SELECT date_created FROM ".MF_TABLE_PREFIX."form_{$form_id} WHERE `id`=?";
			$params = array($record_id);
		
			$sth = mf_do_query($query,$params,$dbh);
			$row = mf_do_fetch_result($sth);

			if(md5($row['date_created']) == $date_created_md5){
				$_SESSION['mf_payment_record_id'][$form_id] = $record_id;
				$_SESSION['mf_form_payment_access'][$form_id]  = true;
				$_SESSION['mf_form_completed'][$form_id] = true;
			}
		}

		//check permission to access this page
		if($_SESSION['mf_form_payment_access'][$form_id] !== true){
			return "Your session has been expired. Please <a href='view.php?id={$form_id}'>click here</a> to start again.";
		}

		$mf_settings = mf_get_settings($dbh);
		
		//get form properties data
		$query 	= "select 
						  form_name,
						  form_has_css,
						  form_redirect,
						  form_language,
						  form_review,
						  form_review_primary_text,
						  form_review_secondary_text,
						  form_review_primary_img,
						  form_review_secondary_img,
						  form_review_use_image,
						  form_review_title,
						  form_review_description,
						  form_resume_enable,
						  form_page_total,
						  form_lastpage_title,
						  form_pagination_type,
						  form_theme_id,
						  payment_show_total,
						  payment_total_location,
						  payment_enable_merchant,
						  payment_merchant_type,
						  payment_currency,
						  payment_price_type,
						  payment_price_name,
						  payment_price_amount,
						  payment_ask_billing,
						  payment_ask_shipping,
						  payment_stripe_live_public_key,
						  payment_stripe_test_public_key,
						  payment_stripe_enable_test_mode,
						  payment_braintree_live_encryption_key,
						  payment_braintree_test_encryption_key,
						  payment_braintree_enable_test_mode,
						  payment_enable_recurring,
						  payment_recurring_cycle,
						  payment_recurring_unit,
						  payment_enable_trial,
						  payment_trial_period,
						  payment_trial_unit,
						  payment_trial_amount,
						  payment_delay_notifications,
						  payment_enable_tax,
					 	  payment_tax_rate,
					 	  payment_enable_discount,
						  payment_discount_type,
						  payment_discount_amount,
						  payment_discount_element_id,
						  form_custom_script_enable,
					 	  form_custom_script_url  
				     from 
				     	 ".MF_TABLE_PREFIX."forms 
				    where 
				    	 form_id=?";
		$params = array($form_id);
		
		$sth = mf_do_query($query,$params,$dbh);
		$row = mf_do_fetch_result($sth);
		
		$form_language = $row['form_language'];

		if(!empty($form_language)){
			mf_set_language($form_language);
		}
		
		$form_payment_title			 = $mf_lang['form_payment_title'];
		$form_payment_description	 = $mf_lang['form_payment_description'];

		$form_has_css 			= $row['form_has_css'];
		$form_redirect			= $row['form_redirect'];
		$form_review  	 		= (int) $row['form_review'];
		$form_review_primary_text 	 = $row['form_review_primary_text'];
		$form_review_secondary_text  = $row['form_review_secondary_text'];
		$form_review_primary_img 	 = $row['form_review_primary_img'];
		$form_review_secondary_img   = $row['form_review_secondary_img'];
		$form_review_use_image  	 = (int) $row['form_review_use_image'];
		$form_review_title			 = $row['form_review_title'];
		$form_review_description	 = $row['form_review_description'];
		$form_page_total 			 = (int) $row['form_page_total'];
		$form_lastpage_title 		 = $row['form_lastpage_title'];
		$form_pagination_type		 = $row['form_pagination_type'];
		$form_name					 = htmlspecialchars($row['form_name'],ENT_QUOTES);
		$form_theme_id				 = $row['form_theme_id'];
		$form_resume_enable  	 	 = (int) $row['form_resume_enable'];

		$form_custom_script_enable 	= (int) $row['form_custom_script_enable'];
		$form_custom_script_url 	= $row['form_custom_script_url'];

		$payment_show_total	 		 = (int) $row['payment_show_total'];
		$payment_total_location 	 = $row['payment_total_location'];
		$payment_enable_merchant 	 = (int) $row['payment_enable_merchant'];
		$payment_enable_tax 		 = (int) $row['payment_enable_tax'];
		$payment_tax_rate 			 = (float) $row['payment_tax_rate'];

		$payment_currency 	   		 = $row['payment_currency'];
		$payment_price_type 	     = $row['payment_price_type'];
		$payment_price_amount    	 = $row['payment_price_amount'];
		$payment_price_name			 = htmlspecialchars($row['payment_price_name'],ENT_QUOTES);
		$payment_ask_billing 	 	 = (int) $row['payment_ask_billing'];
		$payment_ask_shipping 	 	 = (int) $row['payment_ask_shipping'];
		$payment_merchant_type		 = $row['payment_merchant_type'];
		$payment_stripe_enable_test_mode = (int) $row['payment_stripe_enable_test_mode'];
		$payment_stripe_live_public_key	 = trim($row['payment_stripe_live_public_key']);
		$payment_stripe_test_public_key	 = trim($row['payment_stripe_test_public_key']);

		$payment_braintree_live_encryption_key  = trim($row['payment_braintree_live_encryption_key']);
		$payment_braintree_test_encryption_key  = trim($row['payment_braintree_test_encryption_key']);
		$payment_braintree_enable_test_mode 	= (int) $row['payment_braintree_enable_test_mode'];

		$payment_enable_recurring = (int) $row['payment_enable_recurring'];
		$payment_recurring_cycle  = (int) $row['payment_recurring_cycle'];
		$payment_recurring_unit   = $row['payment_recurring_unit'];

		//paypal pro and braintree currently doesn't support creating subscription through API
		if(in_array($payment_merchant_type, array('paypal_rest','braintree'))){
			$payment_enable_recurring = 0;
		}

		$payment_enable_trial = (int) $row['payment_enable_trial'];
		$payment_trial_period = (int) $row['payment_trial_period'];
		$payment_trial_unit   = $row['payment_trial_unit'];
		$payment_trial_amount = (float) $row['payment_trial_amount'];

		$payment_enable_discount = (int) $row['payment_enable_discount'];
		$payment_discount_type 	 = $row['payment_discount_type'];
		$payment_discount_amount = (float) $row['payment_discount_amount'];
		$payment_discount_element_id = (int) $row['payment_discount_element_id'];

		$payment_delay_notifications = (int) $row['payment_delay_notifications'];

		$is_discount_applicable = false;

		//if the discount element for the current entry_id having any value, we can be certain that the discount code has been validated and applicable
		if(!empty($payment_enable_discount)){
			$query = "select element_{$payment_discount_element_id} coupon_element from ".MF_TABLE_PREFIX."form_{$form_id} where `id` = ? and `status` = 1";
			$params = array($record_id);
			
			$sth = mf_do_query($query,$params,$dbh);
			$row = mf_do_fetch_result($sth);
			
			if(!empty($row['coupon_element'])){
				$is_discount_applicable = true;
			}
		}

		//check for specific form css, if any, use it instead
		if($form_has_css){
			$css_dir = $mf_settings['data_dir']."/form_{$form_id}/css/";
		}
		
		if($integration_method == 'iframe'){
			$embed_class = 'class="embed"';
		}
		
		//get total payment
		$currency_symbol 	  = '&#36;';
		
		switch($payment_currency){
				case 'USD' : $currency_symbol = '&#36;';break;
				case 'EUR' : $currency_symbol = '&#8364;';break;
				case 'GBP' : $currency_symbol = '&#163;';break;
				case 'AUD' : $currency_symbol = 'A&#36;';break;
				case 'CAD' : $currency_symbol = 'C&#36;';break;
				case 'JPY' : $currency_symbol = '&#165;';break;
				case 'THB' : $currency_symbol = '&#3647;';break;
				case 'HUF' : $currency_symbol = '&#70;&#116;';break;
				case 'CHF' : $currency_symbol = 'CHF';break;
				case 'CZK' : $currency_symbol = '&#75;&#269;';break;
				case 'SEK' : $currency_symbol = 'kr';break;
				case 'DKK' : $currency_symbol = 'kr';break;
				case 'NOK' : $currency_symbol = 'kr';break;
				case 'PHP' : $currency_symbol = '&#36;';break;
				case 'IDR' : $currency_symbol = 'Rp';break;
				case 'MYR' : $currency_symbol = 'RM';break;
				case 'ZAR' : $currency_symbol = 'R';break;
				case 'PLN' : $currency_symbol = '&#122;&#322;';break;
				case 'BRL' : $currency_symbol = 'R&#36;';break;
				case 'HKD' : $currency_symbol = 'HK&#36;';break;
				case 'MXN' : $currency_symbol = 'Mex&#36;';break;
				case 'TWD' : $currency_symbol = 'NT&#36;';break;
				case 'TRY' : $currency_symbol = 'TL';break;
				case 'NZD' : $currency_symbol = '&#36;';break;
				case 'SGD' : $currency_symbol = '&#36;';break;
		}

		if($payment_price_type == 'variable'){

			$total_payment_amount = (double) mf_get_payment_total($dbh,$form_id,$record_id,0,'live');
			$payment_items = mf_get_payment_items($dbh,$form_id,$record_id,'live');
			
			
			//build the payment list markup
			$payment_list_items_markup = '';
			if(!empty($payment_items)){
				foreach ($payment_items as $item) {
					if($item['quantity'] > 1){
						$quantity_tag = ' <span style="font-weight: normal;padding-left:5px">x'.$item['quantity'].'</span>';
					}else{
						$quantity_tag = '';
					}

					if($item['type'] == 'money'){
						$payment_list_items_markup .= "<li>{$item['title']} <span>{$currency_symbol}{$item['amount']}{$quantity_tag}</span></li>"."\n";
					}else if($item['type'] == 'checkbox'){
						$payment_list_items_markup .= "<li>{$item['sub_title']} <span>{$currency_symbol}{$item['amount']}{$quantity_tag}</span></li>"."\n";
					}else if($item['type'] == 'select' || $item['type'] == 'radio'){
						$payment_list_items_markup .= "<li>{$item['title']} <em>({$item['sub_title']})</em> <span>{$currency_symbol}{$item['amount']}{$quantity_tag}</span></li>"."\n";
					}
				}

				//calculate discount if applicable
				if($is_discount_applicable){
					$payment_calculated_discount = 0;

					if($payment_discount_type == 'percent_off'){
						//the discount is percentage
						$payment_calculated_discount = ($payment_discount_amount / 100) * $total_payment_amount;
						$payment_calculated_discount = round($payment_calculated_discount,2); //round to 2 digits decimal
						$discount_percentage_label  = '('.$payment_discount_amount.'%)';
					}else{
						//the discount is fixed amount
						$payment_calculated_discount = round($payment_discount_amount,2); //round to 2 digits decimal
						$discount_percentage_label = '';
					}

					$total_payment_amount -= $payment_calculated_discount;

					$payment_list_items_markup .= "<li>{$mf_lang['discount']} {$discount_percentage_label}<span>-{$currency_symbol}{$payment_calculated_discount}</span></li>"."\n";
				}

				//calculate tax if enabled
				if(!empty($payment_enable_tax) && !empty($payment_tax_rate)){
					$payment_tax_amount = ($payment_tax_rate / 100) * $total_payment_amount;
					$payment_tax_amount = round($payment_tax_amount,2); //round to 2 digits decimal

					$total_payment_amount += $payment_tax_amount;
					$payment_list_items_markup .= "<li>{$mf_lang['tax']} ({$payment_tax_rate}%)<span>{$currency_symbol}{$payment_tax_amount}</span></li>"."\n";
				}
			}
		}else if($payment_price_type == 'fixed'){
			$total_payment_amount = $payment_price_amount;

			$payment_list_items_markup = "<li>{$payment_price_name}</li>";

			//calculate discount if applicable
			if($is_discount_applicable){
				$payment_calculated_discount = 0;

				if($payment_discount_type == 'percent_off'){
					//the discount is percentage
					$payment_calculated_discount = ($payment_discount_amount / 100) * $total_payment_amount;
					$payment_calculated_discount = round($payment_calculated_discount,2); //round to 2 digits decimal
					$discount_amount_label = $payment_discount_amount.'%';
				}else{
					//the discount is fixed amount
					$payment_calculated_discount = round($payment_discount_amount,2); //round to 2 digits decimal
					$discount_amount_label = $currency_symbol.$payment_calculated_discount;
				}

				$total_payment_amount -= $payment_calculated_discount;
				$discount_label = "-{$mf_lang['discount']} {$discount_amount_label}";

				$payment_list_items_markup .= "<li>{$discount_label}</li>";
			}


			//calculate tax if enabled
			$tax_label = '';
			if(!empty($payment_enable_tax) && !empty($payment_tax_rate)){
				$payment_tax_amount = ($payment_tax_rate / 100) * $total_payment_amount;
				$payment_tax_amount = round($payment_tax_amount,2); //round to 2 digits decimal

				$total_payment_amount += $payment_tax_amount;
				$tax_label = "+{$mf_lang['tax']} {$payment_tax_rate}%";

				$payment_list_items_markup .= "<li>{$tax_label}</li>";
			}

			
		}

		
		//construct payment terms
		if(!empty($payment_enable_recurring)){
			$payment_plurals = '';
			if($payment_recurring_cycle > 1){
				$payment_plurals = 's';
				$payment_recurring_cycle_markup = $payment_recurring_cycle.' ';
			}

			if(!empty($payment_enable_trial)){
				//recurring with trial period
				$payment_trial_price = $currency_symbol.$payment_trial_amount;
				if(empty($payment_trial_amount)){
					$payment_trial_price = 'free';
				}

				$payment_trial_plurals = '';
				if($payment_trial_period > 1){
					$payment_trial_plurals = 's';
				}

				$payment_term_markup =<<<EOT
					<li class="payment_summary_term">
						<em>Trial period: {$payment_trial_period} {$payment_trial_unit}{$payment_trial_plurals} ({$payment_trial_price})</em><br>
						<em>Then you will be charged {$currency_symbol}{$total_payment_amount} every {$payment_recurring_cycle_markup}{$payment_recurring_unit}{$payment_plurals}</em>
					</li>
EOT;
				$total_payment_amount = $payment_trial_amount; //when trial being enabled, we need to display the trial amount into the TOTAL
			}else{
				$payment_term_markup = "<li class=\"payment_summary_term\"><em>You will be charged {$currency_symbol}{$total_payment_amount} every {$payment_recurring_cycle_markup}{$payment_recurring_unit}{$payment_plurals}</em></li>";
			}
		}
		
		//if the form has multiple pages
		//display the pagination header
		if($form_page_total > 1){
			
			//build pagination header based on the selected type. possible values:
			//steps - display multi steps progress
			//percentage - display progress bar with percentage
			//disabled - disabled
			
			$page_breaks_data = array();
			$page_title_array = array();
			
			//get page titles
			$query = "SELECT 
							element_page_title
						FROM 
							".MF_TABLE_PREFIX."form_elements
					   WHERE
							form_id = ? and element_status = 1 and element_type = 'page_break'
					ORDER BY 
					   		element_page_number asc";
			$params = array($form_id);
			
			$sth = mf_do_query($query,$params,$dbh);
			while($row = mf_do_fetch_result($sth)){
				$page_title_array[] = $row['element_page_title'];
			}
			
			if($form_pagination_type == 'steps'){
				
				$page_titles_markup = '';
				
				$i=1;
				foreach ($page_title_array as $page_title){
					$page_titles_markup .= '<td align="center"><span id="page_num_'.$i.'" class="ap_tp_num">'.$i.'</span><span id="page_title_'.$i.'" class="ap_tp_text">'.$page_title.'</span></td><td align="center" class="ap_tp_arrow">&gt;</td>'."\n";
					$i++;
				}
				
				//add the last page title into the pagination header markup
				$page_titles_markup .= '<td align="center"><span id="page_num_'.$i.'" class="ap_tp_num">'.$i.'</span><span id="page_title_'.$i.'" class="ap_tp_text">'.$form_lastpage_title.'</span></td>';
				
				if(!empty($form_review)){
					$i++;
					$page_titles_markup .= '<td align="center" class="ap_tp_arrow">&gt;</td><td align="center"><span id="page_num_'.$i.'" class="ap_tp_num">'.$i.'</span><span id="page_title_'.$i.'" class="ap_tp_text">'.$form_review_title.'</span></td>';
				}

				$i++;
				$page_titles_markup .= '<td align="center" class="ap_tp_arrow">&gt;</td><td align="center"><span id="page_num_'.$i.'" class="ap_tp_num ap_tp_num_active">'.$i.'</span><span id="page_title_'.$i.'" class="ap_tp_text ap_tp_text_active">'.$mf_lang['form_payment_header_title'].'</span></td>';
				
				
				$pagination_header =<<<EOT
			<ul>
			<li id="pagination_header" class="li_pagination">
			 <table class="ap_table_pagination" width="100%" border="0" cellspacing="0" cellpadding="0">
			  <tr> 
			  	{$page_titles_markup}
			  </tr>
			</table>
			</li>
			</ul>
EOT;
			}else if($form_pagination_type == 'percentage'){
				
				$page_total = count($page_title_array) + 2;
				if(!empty($form_review)){
					$page_total++;
				}
				
				$percent_value = 99;
				
				$page_number_title = sprintf($mf_lang['page_title'],$page_total,$page_total);
				$pagination_header =<<<EOT
			<ul>
				<li id="pagination_header" class="li_pagination" title="Click to edit">
			    <h3 id="page_title_{$page_total}">{$page_number_title}</h3>
				<div class="mf_progress_container">          
			    	<div id="mf_progress_percentage" class="mf_progress_value" style="width: {$percent_value}%"><span>{$percent_value}%</span></div>
				</div>
				</li>
			</ul>
EOT;
			}else{			
				$pagination_header = '';
			}
			
		}

		
		//build the button markup
		$button_markup =<<<EOT
<input id="btn_submit_payment" class="button_text btn_primary" type="submit" data-originallabel="{$mf_lang['payment_submit_button']}" value="{$mf_lang['payment_submit_button']}" />
EOT;

		//if this form is using custom theme
		if(!empty($form_theme_id)){
			//get the field highlight color for the particular theme
			$query = "SELECT 
							highlight_bg_type,
							highlight_bg_color,
							form_shadow_style,
							form_shadow_size,
							form_shadow_brightness,
							form_button_type,
							form_button_text,
							form_button_image,
							theme_has_css  
						FROM 
							".MF_TABLE_PREFIX."form_themes 
					   WHERE 
					   		theme_id = ?";
			$params = array($form_theme_id);
			
			$sth = mf_do_query($query,$params,$dbh);
			$row = mf_do_fetch_result($sth);
			
			$form_shadow_style 		= $row['form_shadow_style'];
			$form_shadow_size 		= $row['form_shadow_size'];
			$form_shadow_brightness = $row['form_shadow_brightness'];
			$theme_has_css = (int) $row['theme_has_css'];
			
			//if the theme has css file, make sure to refer to that file
			//otherwise, generate the css dynamically
			
			if(!empty($theme_has_css)){
				$theme_css_link = '<link rel="stylesheet" type="text/css" href="'.$machform_path.$mf_settings['data_dir'].'/themes/theme_'.$form_theme_id.'.css" media="all" />';
			}else{
				$theme_css_link = '<link rel="stylesheet" type="text/css" href="'.$machform_path.'css_theme.php?theme_id='.$form_theme_id.'" media="all" />';
			}
			
			if($row['highlight_bg_type'] == 'color'){
				$field_highlight_color = $row['highlight_bg_color'];
			}else{ 
				//if the field highlight is using pattern instead of color, set the color to empty string
				$field_highlight_color = ''; 
			}
			
			//get the css link for the fonts
			$font_css_markup = mf_theme_get_fonts_link($dbh,$form_theme_id);
			
			//get the form shadow classes
			if(!empty($form_shadow_style) && ($form_shadow_style != 'disabled')){
				preg_match_all("/[A-Z]/",$form_shadow_style,$prefix_matches);
				//this regex simply get the capital characters of the shadow style name
				//example: RightPerspectiveShadow result to RPS and then being sliced to RP
				$form_shadow_prefix_code = substr(implode("",$prefix_matches[0]),0,-1);
				
				$form_shadow_size_class  = $form_shadow_prefix_code.ucfirst($form_shadow_size);
				$form_shadow_brightness_class = $form_shadow_prefix_code.ucfirst($form_shadow_brightness);

				if(empty($integration_method)){ //only display shadow if the form is not being embedded using any method
					$form_container_class = $form_shadow_style.' '.$form_shadow_size_class.' '.$form_shadow_brightness_class;
				}
			}
			
			
			
		}else{ //if the form doesn't have any theme being applied
			$field_highlight_color = '#FFF7C0';
			
			if(empty($integration_method)){
				$form_container_class = ''; //default shadow as of v4.2 is no-shadow
			}else{
				$form_container_class = ''; //dont show any shadow when the form being embedded
			}
		}

		if(empty($mf_settings['disable_machform_link'])){
			$powered_by_markup = 'Powered by <a href="http://www.appnitro.com" target="_blank">MachForm</a>';
		}else{
			$powered_by_markup = '';
		}

		$self_address = htmlentities($_SERVER['PHP_SELF']); //prevent XSS

		$country = mf_get_country_list();
		$country_markup = '<option value="" selected="selected"></option>'."\n";

		foreach ($country as $data){
			$country_markup .= "<option value=\"{$data['value']}\">{$data['label']}</option>\n";
		}

		$billing_address_markup = '';
		if(!empty($payment_ask_billing)){
			$billing_address_markup =<<<EOT
				<li id="li_billing_address" class="address">
					<label class="description">Billing Address <span class="required">*</span></label>
					<div>
						<span id="li_billing_span_1">
							<input id="billing_street" class="element text large" value="" type="text" />
							<label for="billing_street">{$mf_lang['address_street']}</label>
						</span>
					
						<span id="li_billing_span_2" class="left state_list">
							<input id="billing_city" class="element text large" value="" type="text" />
							<label for="billing_city">{$mf_lang['address_city']}</label>
						</span>
					
						<span id="li_billing_span_3" class="right state_list">
							<input id="billing_state" class="element text large" value="" type="text" />
							<label for="billing_state">{$mf_lang['address_state']}</label>
						</span>
					
						<span id="li_billing_span_4" class="left">
							<input id="billing_zipcode" class="element text large" maxlength="15" value="{$default_value_5}" type="text" />
							<label for="billing_zipcode">{$mf_lang['address_zip']}</label>
						</span>
						
						<span id="li_billing_span_5" class="right">
							<select class="element select large" id="billing_country"> 
								{$country_markup}	
							</select>
						<label for="billing_country">{$mf_lang['address_country']}</label>
					    </span>
				    </div><p id="billing_error_message" class="error" style="display: none"></p>
				</li>
EOT;
		}

		$shipping_address_markup = '';
		if(!empty($payment_ask_shipping)){

			//if both billing and shipping being enabled, display a checkbox to allow the user to mark the address as the same
			if(!empty($payment_ask_billing)){
				$same_shipping_markup =<<<EOT
					<div>
					    <input type="checkbox" value="1" checked="checked" class="checkbox" id="mf_same_shipping_address">
						<label for="mf_same_shipping_address" class="choice">My shipping address is the same as my billing address</label>
					</div>
EOT;
				$shipping_display = 'display: none';
			}

			$shipping_address_markup =<<<EOT
				<li id="li_shipping_address" class="address">
					<label class="description shipping_address_detail" style="{$shipping_display}">Shipping Address <span class="required">*</span></label>
					<div class="shipping_address_detail" style="{$shipping_display}">
						<span id="li_shipping_span_1">
							<input id="shipping_street" class="element text large" value="" type="text" />
							<label for="shipping_street">{$mf_lang['address_street']}</label>
						</span>
					
						<span id="li_shipping_span_2" class="left state_list">
							<input id="shipping_city" class="element text large" value="" type="text" />
							<label for="shipping_city">{$mf_lang['address_city']}</label>
						</span>
					
						<span id="li_shipping_span_3" class="right state_list">
							<input id="shipping_state" class="element text large" value="" type="text" />
							<label for="shipping_state">{$mf_lang['address_state']}</label>
						</span>
					
						<span id="li_shipping_span_4" class="left">
							<input id="shipping_zipcode" class="element text large" maxlength="15" value="{$default_value_5}" type="text" />
							<label for="shipping_zipcode">{$mf_lang['address_zip']}</label>
						</span>
						
						<span id="li_shipping_span_5" class="right">
							<select class="element select large" id="shipping_country"> 
								{$country_markup}	
							</select>
						<label for="shipping_country">{$mf_lang['address_country']}</label>
					    </span>
					    <p id="shipping_error_message" class="error" style="display: none"></p>
				    </div>
				    {$same_shipping_markup}
				</li>
EOT;
		}

		$credit_card_logos = array();
		$credit_card_logos['visa'] 		 = '<img src="'.$machform_path.'images/cards/visa.png" alt="Visa" title="Visa" />';
		$credit_card_logos['mastercard'] = '<img src="'.$machform_path.'images/cards/mastercard.png" alt="MasterCard" title="MasterCard" />';
		$credit_card_logos['amex'] 		 = '<img src="'.$machform_path.'images/cards/amex.png" alt="American Express" title="American Express" />';
		$credit_card_logos['jcb'] 		 = '<img src="'.$machform_path.'images/cards/jcb.png" alt="JCB" title="JCB" />';
		$credit_card_logos['discover']   = '<img src="'.$machform_path.'images/cards/discover.png" alt="Discover" title="Discover" />';
		$credit_card_logos['diners'] 	 = '<img src="'.$machform_path.'images/cards/diners.png" alt="Diners Club" title="Diners Club" />';

		$accepted_card_types = array('visa','mastercard','amex','jcb','discover','diners'); //the default accepted credit card types

		if($payment_merchant_type == 'stripe'){
			if(!empty($payment_stripe_enable_test_mode)){
				$stripe_public_key = $payment_stripe_test_public_key;
			}else{
				$stripe_public_key = $payment_stripe_live_public_key;
			}

			$merchant_js =<<<EOT
<script type="text/javascript" src="https://js.stripe.com/v1/"></script>
<script type="text/javascript">
	Stripe.setPublishableKey('{$stripe_public_key}');
</script>
<script type="text/javascript" src="{$machform_path}js/payment_stripe.js"></script>
EOT;
			
			if($payment_currency != 'USD'){
				$accepted_card_types = array('visa','mastercard','amex');
			}
		}else if($payment_merchant_type == 'authorizenet'){

			$merchant_js =<<<EOT
<script type="text/javascript" src="{$machform_path}js/jquery.payment.js"></script>
<script type="text/javascript" src="{$machform_path}js/payment_authorizenet.js"></script>
EOT;
		}else if($payment_merchant_type == 'paypal_rest'){

			$merchant_js =<<<EOT
<script type="text/javascript" src="{$machform_path}js/jquery.payment.js"></script>
<script type="text/javascript" src="{$machform_path}js/payment_paypal_rest.js"></script>
EOT;
			$accepted_card_types = array('visa','mastercard','amex','discover');
		}else if($payment_merchant_type == 'braintree'){
			if(!empty($payment_braintree_enable_test_mode)){
				$braintree_client_side_encryption_key = $payment_braintree_test_encryption_key;
			}else{
				$braintree_client_side_encryption_key = $payment_braintree_live_encryption_key;
			}

			$merchant_js =<<<EOT
<script type="text/javascript" src="{$machform_path}js/jquery.payment.js"></script>
<script type="text/javascript" src="https://js.braintreegateway.com/v1/braintree.js"></script>
<script>
  var mf_braintree = Braintree.create("{$braintree_client_side_encryption_key}");
</script>
<script type="text/javascript" src="{$machform_path}js/payment_braintree.js"></script>
EOT;
		}

		//build the credit card logo markup
		$credit_card_logo_markup = '';
		foreach ($accepted_card_types as $card_type) {
			$credit_card_logo_markup .= $credit_card_logos[$card_type]."\n";
		}

		$jquery_url = $machform_path.'js/jquery.min.js';

		$current_year = date("Y");
		$year_dropdown_markup = '';
		foreach (range($current_year, $current_year + 15) as $year) {
			$year_dropdown_markup .= "<option value=\"{$year}\">{$year}</option>"."\n";
		}

		//load custom javascript if enabled
		$custom_script_js = '';
		if(!empty($form_custom_script_enable) && !empty($form_custom_script_url)){
			$custom_script_js = '<script type="text/javascript" src="'.$form_custom_script_url.'"></script>';
		}

		if($integration_method == 'php'){

			$form_markup = <<<EOT
<link rel="stylesheet" type="text/css" href="{$machform_path}{$css_dir}view.css" media="all" />
<link rel="stylesheet" type="text/css" href="{$machform_path}view.mobile.css" media="all" />
{$theme_css_link}
{$font_css_markup}
<script type="text/javascript" src="{$jquery_url}"></script>
<script type="text/javascript" src="{$machform_path}js/jquery-ui/ui/jquery.effects.core.js"></script>
<script type="text/javascript" src="{$machform_path}view.js"></script>
{$merchant_js}
{$custom_script_js}
<style>
html{
	background: none repeat scroll 0 0 transparent;
}
</style>

<div id="main_body" class="integrated no_guidelines" data-machformpath="{$machform_path}">
	<div id="form_container">
		<form id="form_{$form_id}" class="appnitro" method="post" action="javascript:" data-highlightcolor="{$field_highlight_color}">
		    <div class="form_description">
				<h2>{$form_payment_title}</h2>
				<p>{$form_payment_description}</p>
			</div>
			{$pagination_header}
			
			<ul class="payment_summary">
				<li class="payment_summary_amount total_payment" data-basetotal="{$total_payment_amount}">
					<span>
						<h3>{$currency_symbol}<var>0</var></h3>
						<h5>{$mf_lang['payment_total']}</h5>
					</span>
				</li>
				<li class="payment_summary_list">
					<ul class="payment_list_items">
						{$payment_list_items_markup}
					</ul>
				</li>
				{$payment_term_markup}
			</ul>
			<ul class="payment_detail_form">
				<li id="error_message" style="display: none">
						<h3 id="error_message_title">{$mf_lang['error_title']}</h3>
						<p id="error_message_desc">{$mf_lang['error_desc']}</p>
				</li>	
				<li id="li_accepted_cards">
					{$credit_card_logo_markup}
				</li>
				<li id="li_credit_card" class="credit_card">
					<label class="description">Credit Card <span class="required">*</span></label>
					<div>
						<span id="li_cc_span_1" class="left">
							<input id="cc_first_name" class="element text large" value="" type="text" />
							<label for="cc_first_name">First Name</label>
						</span>
					
						<span id="li_cc_span_2" class="right">
							<input id="cc_last_name" class="element text large" value="" type="text" />
							<label for="cc_last_name">Last Name</label>
						</span>

						<span id="li_cc_span_3" class="left">
							<input id="cc_number" class="element text large" value="" type="text" />
							<label for="cc_number">Credit Card Number</label>
						</span>
					
						<span id="li_cc_span_4" class="right">
							<input id="cc_cvv" class="element text large" value="" type="text" />
							<label for="cc_cvv">CVV</label>
						</span>

						<span id="li_cc_span_5" style="text-align: right">
							<img id="cc_secure_icon" src="{$machform_path}images/icons/lock.png" alt="Secure" title="Secure" /> 
							<label for="cc_expiry_month" style="display: inline">Expiration: </label>
							<select class="element select" id="cc_expiry_month">
								<option value="01">01 - January</option>
								<option value="02">02 - February</option>
								<option value="03">03 - March</option>
								<option value="04">04 - April</option>
								<option value="05">05 - May</option>
								<option value="06">06 - June</option>
								<option value="07">07 - July</option>
								<option value="08">08 - August</option>
								<option value="09">09 - September</option>
								<option value="10">10 - October</option>
								<option value="11">11 - November</option>
								<option value="12">12 - December</option>
							</select>
							<select class="element select" id="cc_expiry_year">
								{$year_dropdown_markup}
							</select>
						</span>
					</div><p id="credit_card_error_message" class="error" style="display: none"></p>
				</li>
				<li id="li_2" class="section_break">
				</li>
				{$billing_address_markup}
				{$shipping_address_markup}
				<li id="li_buttons" class="buttons">
					<input type="hidden" id="form_id" value="{$form_id}" />
				    {$button_markup}
				    <img id="mf_payment_loader_img" style="display: none" src="{$machform_path}images/loader_small_grey.gif" />
				</li>
			</ul>
		</form>		
		<form id="form_payment_redirect" method="post" action="{$self_address}">
			<input type="hidden" id="form_id_redirect" name="form_id_redirect" value="{$form_id}" />
		</form>		
	</div>
</div>
EOT;
		}else{

			if($integration_method == 'iframe'){
				$auto_height_js =<<<EOT
<script type="text/javascript" src="{$machform_path}js/jquery.ba-postmessage.min.js"></script>
<script type="text/javascript">
    $(function(){
    	$.postMessage({mf_iframe_height: $('body').outerHeight(true)}, '*', parent );
    });
</script>
EOT;
			}

			$form_markup = <<<EOT
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html lang="en" xml:lang="en" {$embed_class} xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
<title>{$form_name}</title>
<link rel="stylesheet" type="text/css" href="{$machform_path}{$css_dir}view.css" media="all" />
<link rel="stylesheet" type="text/css" href="{$machform_path}view.mobile.css" media="all" />
{$theme_css_link}
{$font_css_markup}
<script type="text/javascript" src="{$jquery_url}"></script>
<script type="text/javascript" src="{$machform_path}js/jquery-ui/ui/jquery.effects.core.js"></script>
<script type="text/javascript" src="{$machform_path}view.js"></script>
{$merchant_js}
{$auto_height_js}
{$custom_script_js}
</head>
<body id="main_body" class="no_guidelines" data-machformpath="{$machform_path}">
	
	<div id="form_container" class="{$form_container_class}">
	
		<h1><a>MachForm</a></h1>
		<form id="form_{$form_id}" class="appnitro" method="post" action="javascript:" data-highlightcolor="{$field_highlight_color}">
		    <div class="form_description">
				<h2>{$form_payment_title}</h2>
				<p>{$form_payment_description}</p>
			</div>
			{$pagination_header}
			
			<ul class="payment_summary">
				<li class="payment_summary_amount total_payment" data-basetotal="{$total_payment_amount}">
					<span>
						<h3>{$currency_symbol}<var>0</var></h3>
						<h5>{$mf_lang['payment_total']}</h5>
					</span>
				</li>
				<li class="payment_summary_list">
					<ul class="payment_list_items">
						{$payment_list_items_markup}
					</ul>
				</li>
				{$payment_term_markup}
			</ul>
			<ul class="payment_detail_form">
				<li id="error_message" style="display: none">
						<h3 id="error_message_title">{$mf_lang['error_title']}</h3>
						<p id="error_message_desc">{$mf_lang['error_desc']}</p>
				</li>	
				<li id="li_accepted_cards">
					{$credit_card_logo_markup}
				</li>
				<li id="li_credit_card" class="credit_card">
					<label class="description">Credit Card <span class="required">*</span></label>
					<div>
						<span id="li_cc_span_1" class="left">
							<input id="cc_first_name" class="element text large" value="" type="text" />
							<label for="cc_first_name">First Name</label>
						</span>
					
						<span id="li_cc_span_2" class="right">
							<input id="cc_last_name" class="element text large" value="" type="text" />
							<label for="cc_last_name">Last Name</label>
						</span>

						<span id="li_cc_span_3" class="left">
							<input id="cc_number" class="element text large" value="" type="text" />
							<label for="cc_number">Credit Card Number</label>
						</span>
					
						<span id="li_cc_span_4" class="right">
							<input id="cc_cvv" class="element text large" value="" type="text" />
							<label for="cc_cvv">CVV</label>
						</span>

						<span id="li_cc_span_5" style="text-align: right">
							<img id="cc_secure_icon" src="{$machform_path}images/icons/lock.png" alt="Secure" title="Secure" /> 
							<label for="cc_expiry_month" style="display: inline">Expiration: </label>
							<select class="element select" id="cc_expiry_month">
								<option value="01">01 - January</option>
								<option value="02">02 - February</option>
								<option value="03">03 - March</option>
								<option value="04">04 - April</option>
								<option value="05">05 - May</option>
								<option value="06">06 - June</option>
								<option value="07">07 - July</option>
								<option value="08">08 - August</option>
								<option value="09">09 - September</option>
								<option value="10">10 - October</option>
								<option value="11">11 - November</option>
								<option value="12">12 - December</option>
							</select>
							<select class="element select" id="cc_expiry_year">
								{$year_dropdown_markup}
							</select>
						</span>
					</div><p id="credit_card_error_message" class="error" style="display: none"></p>
				</li>
				<li id="li_2" class="section_break">
				</li>
				{$billing_address_markup}
				{$shipping_address_markup}
				<li id="li_buttons" class="buttons">
					<input type="hidden" id="form_id" value="{$form_id}" />
				    {$button_markup}
				    <img id="mf_payment_loader_img" style="display: none" src="{$machform_path}images/loader_small_grey.gif" />
				</li>
			</ul>
		</form>		
		<form id="form_payment_redirect" method="post" action="{$self_address}">
			<input type="hidden" id="form_id_redirect" name="form_id_redirect" value="{$form_id}" />
		</form>	
	</div>
	
	</body>
</html>
EOT;
		}

		return $form_markup;
	}
	
?>