<?php
/*
Plugin Name: Freelance Test App
Plugin URI: https://updraftplus.com
Description: The main goal of this plugin is to be a platform for the freelances to upload their test results for the updraftplus team review.
Version: 2.0.0.AR
Author : Ing. Juan Carlos Quevedo Lussón
Author URI: https://updraftplus.com   
License: GPLv3 or later 
*/

if(!class_exists('WP_Frelance_Test')) {

class WP_Frelance_Test
{
	private $upload_dir = "";
	
	public function __construct(){
		add_action('admin_menu', array($this, 'ft_view_menu'));
		add_action('admin_init', array($this,'ft_register_settings'));
		add_action('admin_enqueue_scripts', array($this, 'admin_enqueue_scripts'), 99999);
		register_activation_hook(__FILE__, array($this, 'activate'));

		add_action( 'wp_ajax_check_email',  array($this,'check_email_action_callback') ); 
		add_action( 'wp_ajax_freelance_upload_file',  array($this,'freelance_upload_file') );
		add_action( 'wp_ajax_freelance_register', array($this,'freelance_register'));
		add_action( 'wp_ajax_ft_admin_delete_applicant', array($this,'ft_admin_delete_applicant')); 
		add_action( 'wp_ajax_ft_admin_accept_applicant', array($this,'ft_admin_accept_applicant')); 
		add_action( 'wp_ajax_ft_admin_denied_applicant', array($this,'ft_admin_denied_applicant'));
		add_action( 'wp_ajax_ft_admin_eval_test', array($this,'ft_admin_eval_test')); 
		
		add_shortcode('ft_frontend_interface', array($this, 'ft_frontend_interface'));
	}
	
	public function ft_admin_delete_applicant() {
	
		if (!current_user_can('manage_options')) die('Security check.');
	
		$action = $_REQUEST['action'];
		$id = $_REQUEST['id'];
		check_ajax_referer( 'delete_email'.$id, 'security' );   
		
		//
		global $wpdb;
		$table = $wpdb->prefix."freelance_test_data";   			
		$file_path_test = $wpdb->get_var($wpdb->prepare("SELECT zip_path FROM $table WHERE ID = %d",$id));
		$file_path_cv = $wpdb->get_var($wpdb->prepare("SELECT cv_path FROM $table WHERE ID = %d",$id));
		if($wpdb->delete( $table, array( 'ID' => $id ), array( '%d' ) )){
			if(is_file($file_path_test)){
				$file_path_info = pathinfo($file_path_test);
				if($file_path_info['dirname'] == 'ft_uploaded_files' && $file_path_info['extension'] == 'zip') {
					unlink($file_path_test);
				} else {
					$response['type'] = 'ok';
					$response['message'] = 'Could not delete applicant test file. Wrong file detected';
					$response['list'] = $this->ft_populate_table_freelancers();
					header('Content-Type: application/json');
					echo json_encode( $response );
					wp_die();
				}
			}
			if(is_file($file_path_cv)){
				$cv_path_info = pathinfo($file_path_test);
				if($cv_path_info['dirname'] == 'ft_uploaded_files') {
					unlink($file_path_cv);
				} else {
					$response['type'] = 'ok';
					$response['message'] = 'Could not delete applicant CV. Wrong file detected';
					$response['list'] = $this->ft_populate_table_freelancers();
					header('Content-Type: application/json');
					echo json_encode( $response );
					wp_die();
				}
			}
			$response['type'] = 'ok';
			$response['message'] = 'The applicant was succesfully deleted from the database.';
			$response['list'] = $this->ft_populate_table_freelancers();
			header('Content-Type: application/json');
			echo json_encode( $response );
			wp_die();
		} else {
			$response['type'] = 'ok';
			$response['message'] = 'Sorry. The applicant was not deleted from the database.';
			header('Content-Type: application/json');
			echo json_encode( $response );
			wp_die();
		}
	
	}
	
	public function ft_admin_denied_applicant() {
	
		if (!current_user_can('manage_options')) die('Security check.');

		$action = $_REQUEST['action'];
		$id = $_REQUEST['id'];
		check_ajax_referer( 'deny_applicant'.$id, 'security' );
		
		global $wpdb;
		$table = $wpdb->prefix."freelance_test_data";
		$applicant_data = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table WHERE ID = %d",$id),OBJECT);
		
		if(count($applicant_data) > 0){
			$ft_test_contact_email = get_option('ft_test_contact_email');
			$ft_template_accepting = get_option('ft_test_reject_mail_content');
			$filled_template = str_replace('[freelance_name]',esc_html($applicant_data->name),$ft_template_accepting);
			
	        $to=array($applicant_data->email);
	        $subject='Your freelance application has now been accepted';
	        $headers = array();
	        $headers[] = 'Reply-to: '.$ft_test_contact_email.' <'.$ft_test_contact_email.'>';
	        
			$message_content="<html>
				<head>
					<meta http-equiv='Content-Type' content='text/html; charset=utf-8' />
					<title>New Freelance Application</title>
					<style type='text/css'>
					<!--
						.style1 {
							font-size: 12px;
							color: #0000FF;
							font-style: italic;
						}
					-->
					</style>
				</head>

				<body>";
			 $message_content.=$filled_template;
			 $message_content.="			
						<hr>
						<p align='center' class='style1'>.:.:.:.:.:.:.:.:.:.: This email is autogenerated by Freelance Test Plugin :.:.:.:.:.:.:.:.:.:.</p>
						<p align='center' class='style1'>Please do not reply to this email.</p> 
				</body>
			</html>";
	         
	            
	        add_filter('wp_mail_content_type',create_function('', 'return "text/html";'));
	            
	           
	        if(wp_mail( $to, $subject, $message_content, $headers)){
	        	//Update the status in the database
	        	$wpdb->update($table,array('status' => 3),array('id' => $id));
				$response['type'] = 'ok';
				$response['message'] = 'The applicant was succesfully notified.';
				$response['list'] = $this->ft_populate_table_freelancers();
				header('Content-Type: application/json');
				echo json_encode( $response );
				wp_die();	
			} else {
				$response['type'] = 'ok';
				$response['message'] = 'Sorry. The applicant was not notified. Please try again later';
				$response['list'] = $this->ft_populate_table_freelancers();   
				header('Content-Type: application/json');
				echo json_encode( $response );
				wp_die();
			} 
		} else {
			$response['type'] = 'ok';
			$response['message'] = "Sorry. The applicant was already notified or doesn't exist on the database. Please refresh this page.";
			header('Content-Type: application/json');
			echo json_encode( $response );
			wp_die();
		}
	}
	
	public function ft_admin_accept_applicant() {
	
		if (!current_user_can('manage_options')) die('Security check.');

		$action = $_REQUEST['action'];
		$id = $_REQUEST['id'];
		check_ajax_referer( 'accept_applicant'.$id, 'security' );
		
		global $wpdb;
		$table = $wpdb->prefix."freelance_test_data";
		$applicant_data = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table WHERE ID = %d and status = 1",$id),OBJECT);
		
		if(count($applicant_data)> 0){
			$ft_test_contact_email = get_option('ft_test_contact_email');
			$ft_template_accepting = get_option('ft_test_accept_mail_content');
			$filled_template = str_replace('[freelance_name]',esc_html($applicant_data->name),$ft_template_accepting);
			
	        $to=array($applicant_data->email);
	        $subject='Your freelance application has now been accepted';
	        $headers = array();
	        $headers[] = 'Reply-to: '.$ft_test_contact_email.' <'.$ft_test_contact_email.'>';
	        
			$message_content="<html>
				<head>
					<meta http-equiv='Content-Type' content='text/html; charset=utf-8' />
					<title>New Freelance Application</title>
					<style type='text/css'>
					<!--
						.style1 {
							font-size: 12px;
							color: #0000FF;
							font-style: italic;
						}
					-->
					</style>
				</head>

				<body>";
			 $message_content.=$filled_template;
			 $message_content.="			
						<hr>
						<p align='center' class='style1'>.:.:.:.:.:.:.:.:.:.: This email is autogenerated by Freelance Test Plugin :.:.:.:.:.:.:.:.:.:.</p>
						<p align='center' class='style1'>Please do not reply to this email.</p> 
				</body>
			</html>";
	         
	            
	        add_filter('wp_mail_content_type',create_function('', 'return "text/html";'));
	            
	           
	        if(wp_mail($to, $subject, $message_content, $headers)){
	        	//Update the status in the database
	        	$applicant_data = $wpdb->update($table,array('status' => 2),array('id' => $id));
				$response['type'] = 'ok';
				$response['message'] = 'The applicant was succesfully notified.';
				$response['list'] = $this->ft_populate_table_freelancers();
				header('Content-Type: application/json');
				echo json_encode( $response );
				wp_die();	
			} else {
				$response['type'] = 'ok';
				$response['message'] = 'Sorry. The applicant was not notified. Please try again later';
				$response['list'] = $this->ft_populate_table_freelancers();       
				header('Content-Type: application/json');
				echo json_encode( $response );
				wp_die();
			} 
		} else {
			$response['type'] = 'ok';
			$response['message'] = "Sorry. The applicant was already notified or doesn't exist on the database. Please refresh this page.";
			header('Content-Type: application/json');
			echo json_encode( $response );
			wp_die();
		}
	}
	
	public function ft_admin_eval_test(){
	
		if (!current_user_can('manage_options')) die('Security check.');

		$action = $_REQUEST['action'];
		$id = $_REQUEST['id'];
		$test_eval = $_REQUEST['evaluation'];
		$test_comments = $_REQUEST['evaluation_comments'];   
		check_ajax_referer( 'eval_applicant'.$id, 'security' );
		$current_user = wp_get_current_user();   
		global $wpdb;
		$table = $wpdb->prefix."freelance_test_data";
		$applicant_data = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table WHERE ID = %d and status = 4",$id),OBJECT);
		
		if(count($applicant_data)> 0){
			$wpdb->update($table,array('test_comments' => $test_comments, 'test_eval' =>$test_eval),array('id' => $id));
			$ft_test_contact_email = get_option('ft_test_contact_email');
	        $to=array($ft_test_contact_email);
	        $subject='Freelance test evaluation notification';
	        $headers = array('Reply-to: '.$ft_test_contact_email.'<'.$ft_test_contact_email.'>');
	        
			$message_content.="<html>
				<head>
					<meta http-equiv='Content-Type' content='text/html; charset=utf-8' />
					<title>New Freelance Evaluation</title>
					<style type='text/css'>
					<!--
						.style1 {
							font-size: 12px;
							color: #0000FF;
							font-style: italic;
						}
					-->
					</style>
				</head>

				<body>
					<div>
						<h1 align='center'>Hi, admin</h1>
						<p>".htmlspecialchars($current_user->user_email)." has evaluated the test submited by the freelance applicant.
						<p><b>Freelance data</b></p>
						<p>Name:".htmlspecialchars($applicant_data->name)." </p> 
						<p>Email:".htmlspecialchars($applicant_data->email)." </p>
						<p><b>Evaluation data</b></p>
						<p>Evaluator email:".htmlspecialchars($current_user->user_email)." </p>
						<p>Evaluation:".htmlspecialchars($test_eval)." </p>
						<p>Evaluation note:".htmlspecialchars($test_comments)." </p>    
						<hr>
						
						<p align='center' class='style1'>.:.:.:.:.:.:.:.:.:.: This email is autogenerated by Freelance Test Plugin :.:.:.:.:.:.:.:.:.:.</p>
						<p align='center' class='style1'>Please do not reply to this email.</p> 
					</div>
				</body>
			</html>";
	         
	            
	        add_filter('wp_mail_content_type',create_function('', 'return "text/html";'));
	            
	           
	        if(wp_mail($to, $subject, $message_content, $headers)){
	        	//Update the status in the database
				$response['type'] = 'ok';
				$response['message'] = 'The applicant was succesfully notified.';
				$response['list'] = $this->ft_populate_table_freelancers();
				header('Content-Type: application/json');
				echo json_encode( $response );
				wp_die();	
			} else {
				$response['type'] = 'ok';
				$response['message'] = 'Sorry. The evaluation was not saved. Please try again later';
				$response['list'] = $this->ft_populate_table_freelancers();       
				header('Content-Type: application/json');
				echo json_encode( $response );
				wp_die();
			} 
		} else {
			$response['type'] = 'ok';
			$response['message'] = 'Sorry. The evaluation was already saved on the database. Please refresh this page.';
			header('Content-Type: application/json');
			echo json_encode( $response );
			wp_die();
		}	
	}
	
	public function my_upload_dir($upload) {
		$upload['subdir'] = '/ft_uploaded_files';
		$upload['path']   = $upload['basedir'].$upload['subdir'];
		$upload['url']    = $upload['baseurl'].$upload['subdir'];
		$this->upload_dir = $upload['path']; 

		return $upload;
	}
	
	public function freelance_register(){
		check_ajax_referer( 'register_form_check', 'security' );
		    
		global $wpdb;
		$email = sanitize_email( $_POST['email'] ); 
		$table = $wpdb->prefix."freelance_test_data";
		$wpdb->freelance_test_data = $table; 
		
		$query = "Select count(email) as counter FROM $table WHERE email=%s";
		
		$result = $wpdb->get_var($wpdb->prepare($query,$email)); 
		 
		if($result > 0) {
			$response['type'] = 'error';
			$response['message'] = 'This email is already registered.';
			$response['value'] = $result;
			header('Content-Type: application/json');
			echo json_encode( $response );
			wp_die();	
		} else {
			$cv_url = '';
			$cv_path = '';
			
		   if(isset($_FILES['async-upload']) && $_FILES['async-upload'] !== 'undefined') {
				$uploadedfile = $_FILES['async-upload'];
				$upload_overrides = array( 'test_form' => false );
				
				add_filter('upload_dir', array($this,'my_upload_dir'));
				$movefile = wp_handle_upload( $uploadedfile, $upload_overrides );
				remove_filter('upload_dir', array($this,'my_upload_dir'));
				$cv_url = $movefile['url'];
				$cv_path = $movefile['file'];	
			}
				
			$name = sanitize_text_field($_POST['name']);
			$long_term_goals = sanitize_text_field($_POST['long_term_goals']);
			$long_term_relationship = sanitize_text_field($_POST['looking_for']);
			$experience_time = sanitize_text_field($_POST['total_experience']);
			$php_level = sanitize_text_field($_POST['php_level']);
			$js_level = sanitize_text_field($_POST['js_level']);
			$wp_level = sanitize_text_field($_POST['wp_level']);
			$plugin_url = sanitize_text_field($_POST['plugin_url']);
			$backup_plugin = sanitize_text_field($_POST['backup_plugin']);
			$long_term_rate = sanitize_text_field($_POST['long_term_rate']);
			$starting_date = sanitize_text_field($_POST['starting_date']);
			$online_cv_link = sanitize_text_field($_POST['online_cv_link']);
			$test_date = sanitize_text_field($_POST['test_date']);
			
			if($wpdb->insert( $table, array('email' =>$email,'name'=> $name, 'long_term_goals'=>$long_term_goals,'long_term_relationship'=>$long_term_relationship,'experience_time' => $experience_time, 'php_level' => $php_level, 'js_level' => $js_level, 'wp_level' => $wp_level, 'plugin_link' => $plugin_url,'backup_plugin' => $backup_plugin, 'working_rate' => $long_term_rate, 'start_availability' => $starting_date, 'online_profile_link' => $online_cv_link, 'cv_url' => $cv_url, 'cv_path' => $cv_path, 'date_test' => $test_date))) {
			//We send the email to the plugin contact email
			$ft_test_contact_email = get_option('ft_test_contact_email');
            $to=array($ft_test_contact_email);
            $subject='New Freelance Application';
            $headers = array('Reply-to: '.$name.'<'.$email.'>');
            $developed_plugin = "No";
            if($plugin_url !="") {
            	$developed_plugin = "Yes. (<a href='".esc_url($plugin_url)."'>".htmlspecialchars($plugin_url)."</a>)";	
            }
            $used_backup_plugin = "No";
            if($backup_plugin !="" && $backup_plugin !="No") {
            	$used_backup_plugin = "Yes. (".htmlspecialchars($backup_plugin).")";	
            }
            $online_profile = "Not specified";
            if($online_cv_link !="") {
            	$online_profile = "Yes. (<a href='".esc_url($online_cv_link)."'>".htmlspecialchars($online_cv_link)."</a>)";	
            }
            $cv_file = "No uploaded";
            if($cv_url !="" && $cv_url !="No") {
            	$cv_file = "Yes. (<a href='".esc_url($cv_url)."'>".htmlspecialchars($cv_url)."</a>)";	
            }
            $message_content.="<html>
                        <head>
                        <meta http-equiv='Content-Type' content='text/html; charset=utf-8' />
                        <title>New Freelance Application</title>
                        <style type='text/css'>
                        <!--
                        .style1 {
                            font-size: 12px;
                            color: #0000FF;
                            font-style: italic;
                        }
                        -->
                        </style>
                        </head>

                        <body>
                        <div>
                        <h1 align='center'>New Freelance Application</h1>
                        <p><b>Name</b>: ".esc_html($name)."</p>
                        <p><b>Email</b>: ".esc_attr($email)."</p>
                        <p><b>Long terms goals</b>: ".esc_attr($long_term_goals)."</p> 
                        <p><b>Looking for</b>: ".esc_attr($long_term_relationship)."</p>
                        <p><b>Years of experience</b>: ".esc_attr($experience_time)."</p> 
                        <p><b>Development experience</b>:</p>
                        <p><b>PHP Level</b>: ".esc_attr($php_level)."/10</p>
                        <p><b>JS Level</b>: ".esc_attr($js_level)."/10</p>
                        <p><b>Wordpress Level</b>: ".esc_attr($wp_level)."/10</p>
                        <p><b>Had created an open source plugin?</b>: ".$developed_plugin."</p> 
                        <p><b>Had used an open backup plugin?</b>: ".esc_attr($used_backup_plugin)."</p> 
                        <p><b>Long term rate</b>: ".esc_attr($long_term_rate)."</p>
                        <p><b>Availability to start</b>: ".esc_attr($starting_date)."</p>  
                        <p><b>Online profile link</b>: ".$online_profile."</p> 
                        <p><b>Had uploaded CV?</b>: ".$cv_file."</p>
                        <p><b>Availability to take the test</b>: ".esc_attr($test_date)."</p> 
                        <hr>
                        <p align='center' class='style1'>.:.:.:.:.:.:.:.:.:.: This email is autogenerated by Freelance Test Plugin :.:.:.:.:.:.:.:.:.:.</p>
                        <p align='center' class='style1'>You can contact the applicant by replying to this email.</p> 
                        </div>
                        </body>
                        </html>";
         
            
            add_filter('wp_mail_content_type',create_function('', 'return "text/html";'));
            
           
            if(wp_mail( $to, $subject, $message_content, $headers)){
                $response['type'] = 'ok';
				$response['message'] = 'Thanks for your application. Our team will review your profile and we will contact you soon.';
				header('Content-Type: application/json');
				echo json_encode( $response );
				wp_die();	
            } else {
                $response['type'] = 'ok';
				$response['message'] = 'Thanks for your application. Our team will review your profile and we will contact you within the next few days.';
				header('Content-Type: application/json');
				echo json_encode( $response );
				wp_die();	 
            }
         } else {
                $response['type'] = 'error';
				$response['message'] = 'Sorry. The application was not sent. Please try again.';
				header('Content-Type: application/json');
				echo json_encode( $response );
				wp_die();
			}

		}  
	}
	
	public function freelance_upload_file(){
		check_ajax_referer( 'file_upload_check', 'security' );
		global $wpdb; 
		$email = sanitize_email( $_POST['email_user'] );
		$table = $wpdb->prefix."freelance_test_data";
		$wpdb->freelance_test_data = $table;
		$query = "Select * FROM $table WHERE email=%s";
		
		$result = $wpdb->get_row($wpdb->prepare($query,$email),OBJECT);
		
		if($result->zip_url == ''){
			$uploadedfile = $_FILES['async-upload'];

			$upload_overrides = array( 'test_form' => false );
			//checking for file type only zip or .rar or .tar  . Dropped off the MIME type checks
			//Also check the file extension, because the client may have forged the MIME type
			if(preg_match('/\.(zip|rar|tar(\.(gz|bz2)))$/i',$uploadedfile['name'])){
				add_filter('upload_dir', array($this,'my_upload_dir'));
				$movefile = wp_handle_upload( $uploadedfile, $upload_overrides );
				remove_filter('upload_dir', array($this,'my_upload_dir'));

				if ( $movefile && !isset( $movefile['error'] ) ) {
					$zip_url = $movefile['url'];
					$zip_path = $movefile['file'];
					$wpdb->query($wpdb->prepare("Update $table SET zip_url = %s, zip_path= %s, time_end = Now() WHERE email = %s",$zip_url,$zip_path,$email));

					$query = "Select * FROM $table WHERE email=%s";
					$result2 = $wpdb->get_row($wpdb->prepare($query,$email),OBJECT);
					$wpdb->update($table,array('status' => 4),array('id' => $result2->id));  
					$response['end_time'] = $result2->time_end;
					$response['type'] = 'ok';
					$response['message'] = 'Thank you for your time. Our team will review your code and we will contact you soon.';
					header('Content-Type: application/json');
					echo json_encode( $response );
					wp_die();
				} else {
					/**
						* Error generated by _wp_handle_upload()
						* @see _wp_handle_upload() in wp-admin/includes/file.php
						*/
					$response['type'] = 'error';
					$response['message'] = $movefile['error'];
					header('Content-Type: application/json');
					echo json_encode( $response );
					wp_die();
				}
			} else {
				$response['type'] = 'error';
				$response['message'] = "File type not allowed";
				header('Content-Type: application/json');
				echo json_encode( $response );
				wp_die();
			}
		} else {
			$response['type'] = 'error';
			$response['message'] = "You have already performed the test.";
			header('Content-Type: application/json');
			echo json_encode( $response );
			wp_die();
		}
		wp_die();
	}
	
	public function check_email_action_callback(){
		check_ajax_referer( 'email_check', 'security' );
		global $wpdb;
		$email = sanitize_email( $_POST['email'] );
		$table = $wpdb->prefix."freelance_test_data";
		$wpdb->freelance_test_data = $table;
		$query = "Select * FROM $table WHERE email=%s AND status=2";
		
		$result = $wpdb->get_row($wpdb->prepare($query,$email),OBJECT);
			
		
		if(count($result) > 0){
			$response['message'] = 'accepted';
			$response['test_instructions'] = get_option('ft_test_file_url');
			//set at the database the time the freelancer start the test
			if($result->time_begin == '0000-00-00 00:00:00'){
				$wpdb->query($wpdb->prepare("Update $table SET time_begin = Now() WHERE email = %s",$email));
			}
			
			$query = "Select * FROM $table WHERE email=%s"; 
			$result2 = $wpdb->get_row($wpdb->prepare($query,$email),OBJECT);
			
			$response['start_time'] = $result2->time_begin; 
			
			header('Content-Type: application/json');
			echo json_encode( $response );
			die();
		} else {
			$response['message'] = 'not authorized';
			header('Content-Type: application/json');
			echo json_encode( $response );
			die();
		}	
	}
	
	public static function activate(){
		global $wpdb;
		$table = $wpdb->prefix."freelance_test_data";
		$collate = '';

		if ($wpdb->has_cap('collation')) {
			if (!empty($wpdb->charset)) {
				$collate .= "DEFAULT CHARACTER SET $wpdb->charset";
			}
			if (!empty($wpdb->collate)) {
				$collate .= " COLLATE $wpdb->collate";
			}
		}

		require_once ABSPATH.'wp-admin/includes/upgrade.php';

		// Important: obey the magical/arbitrary rules for formatting this stuff: https://codex.wordpress.org/Creating_Tables_with_Plugins
		
		
		$structure = "CREATE TABLE $table (
			id int(9) NOT NULL auto_increment, 
			name varchar (100) NOT NULL default '',
			email varchar(80) NOT NULL,
			long_term_goals longtext,
			long_term_relationship varchar (255),
			experience_time int,
			php_level int,
			js_level int,
			wp_level int,
			plugin_link varchar (255),
			backup_plugin varchar(80),
			working_rate varchar(20),
			start_availability date, 
			online_profile_link varchar(255),
			cv_url varchar(255) default '',
			cv_path varchar(255) default '',
			date_test varchar (3),
			time_begin datetime NOT NULL default '0000-00-00 00:00:00',
			time_end datetime NOT NULL default '0000-00-00 00:00:00',
			zip_url varchar(255) default '',
			zip_path varchar(255) default '',
			status int default 1,
			test_eval int default -1,
			test_comments varchar (255),
		PRIMARY KEY  (id),
		UNIQUE KEY email (email)
		) $collate;";
		
		dbDelta($structure);
	}
		
	public static function deactivate() {
		global $wpdb;
		$table = $wpdb->prefix."freelance_test_data";
		$structure = "DROP TABLE IF EXISTS $table;";
		
		$wpdb->query($wpdb->prepare($structure,array()));
	} 
	
	public function ft_frontend_interface(){
		if (!is_user_logged_in()) {
			return 'You need to be logged in to see this content.';
		} else {
			if(!is_admin()) {
				add_action('ft_admin_footer', array($this,'ft_enqueue_styles'));
				add_action('ft_admin_footer', array($this,'ft_enqueue_scripts'));
				
				//Get the current user data and if doesnt exists on database then show the form for register application
				//if exists then show the form for taking test
      			$current_user = wp_get_current_user(); 
      			if($this->ft_is_applicant($current_user->user_email)) {
					include('views/frontend_view_test.php');
				} else {
					include('views/frontend_view_register.php');
				}
				do_action('ft_admin_footer'); 
			}		
		}  
	}

	private function ft_is_applicant($email) {
		global $wpdb;
		$email = sanitize_email($email);
		$table = $wpdb->prefix."freelance_test_data";
		$wpdb->freelance_test_data = $table;
		$query = "Select * FROM $table WHERE email=%s";
		
		$result = $wpdb->get_row($wpdb->prepare($query,$email),OBJECT);
		
		return (count($result) > 0);	
	}
	
	public function ft_enqueue_styles() {
		wp_enqueue_style('ft_styles', plugins_url('/',__FILE__).'ft_style.css');
		wp_enqueue_style('jquery-ui-datepicker-style' ,  plugins_url('/',__FILE__).'css/jquery-ui.min.css');
		wp_enqueue_style('bootstrap4css', plugins_url('/',__FILE__).'bootstrap-4.0.0-alpha.5-dist/css/bootstrap.min.css', array(), '4.0.0-alpha5');  
		wp_enqueue_style('font-awesome', plugins_url('/',__FILE__).'css/font-awesome.min.css');  
		
	}

	public function ft_enqueue_scripts() {
		wp_enqueue_script('jquery-ui-datepicker');
		
		wp_enqueue_script('bootstrap4js', plugins_url('/',__FILE__).'bootstrap-4.0.0-alpha.5-dist/js/bootstrap.min.js', array('jquery'), '4.0.0-alpha5'); 
		wp_enqueue_script('validate', plugins_url('/',__FILE__).'jquery.validate.min.js', array('jquery'), '4.0.0-alpha5');
		wp_enqueue_script('ft_scripts', plugins_url('/',__FILE__).'freelance_examen.js', array('jquery', 'jquery-ui-datepicker', 'bootstrap4js','validate'));
		
		$data = array(
			'upload_url' => admin_url('async-upload.php'),
			'ajax_url'   => admin_url('admin-ajax.php'),
			'nonce_email'      => wp_create_nonce('email_check'),
			'nonce_file_upload'  =>   wp_create_nonce('file_upload_check'),
			'nonce_register' => wp_create_nonce('register_form_check')
		);

		wp_localize_script( 'ft_scripts', 'ajax_object', $data );
	}

	public function admin_enqueue_scripts() {
		wp_enqueue_script('ft-admin', plugins_url('/',__FILE__).'ft_admin.js', array('jquery')); 
		$data = array(
			'ajax_url'   => admin_url('admin-ajax.php'),
		);

		wp_localize_script( 'ft-admin', 'ajax_object', $data ); 
		wp_enqueue_style('jquery-ui-datepicker-style' ,  plugins_url('/',__FILE__).'css/jquery-ui.min.css');     
	}
	
	public function ft_admin_view_settings(){
		// Checking if the user has priviledges for managing options else show a warning.
		if ( !current_user_can( 'manage_options' ) ) {
			wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
		}
		include('views/admin_view_settings.php');
		
	}
	
	private function ft_populate_table_freelancers() {
		add_thickbox(); 
		global $wpdb; 
		$table = $wpdb->prefix."freelance_test_data";
		$results = $wpdb->get_results("SELECT * FROM $table");   
		$lap =0;
		$final_table = "";
		foreach($results as $result){
			$lap++;
			$datetime1 = new DateTime($result->time_begin);
			
			$datetime2 = new DateTime($result->time_end);

			if($datetime2->format('U') > $datetime1->format('U')) {
				$interval = $datetime2->diff($datetime1);
				$date_diff = $interval->format('%Hh:%Im');
			} else {
				$date_diff = "";
			}

			//Preparing user answer to form
			$formated_user_answers='<p><b>Name:</b> '.esc_attr($result->name).'</p>';
			$formated_user_answers.='<p><b>Email:</b> '.esc_attr($result->email).'</p>';
			$formated_user_answers.='<div id="thickbox'.$result->id.'" style="display:none;">';
			$formated_user_answers.='<p><b>Name:</b> '.esc_attr($result->name).'</p>';
			$formated_user_answers.='<p><b>Email:</b> '.esc_attr($result->email).'</p>';
			$formated_user_answers.='<p><b>Long terms goals:</b> '.esc_attr($result->long_term_goals).'</p>';
			$formated_user_answers.='<p><b>Looking for:</b> '.esc_attr($result->long_term_relationship).'</p>';
			$formated_user_answers.='<p><b>Total experience:</b> '.esc_attr($result->experience_time).'</p>'; 
			$formated_user_answers.='<p><b>Developer level:</b></p>';
			$formated_user_answers.='<p><b>--PHP Level:</b> '.esc_attr($result->php_level).'</p>';   
			$formated_user_answers.='<p><b>--JS Level:</b> '.esc_attr($result->js_level).'</p>';
			$formated_user_answers.='<p><b>--WP Level:</b> '.esc_attr($result->wp_level).'</p>'; 
			$formated_user_answers.='<p><b>Plugin developed:</b> '.esc_attr($result->plugin_link).'</p>';  
			$formated_user_answers.='<p><b>Rate:</b> '.esc_attr($result->working_rate).'</p>';     
			$formated_user_answers.='<p><b>Availability to start:</b> '.esc_attr($result->start_availability).'</p>';  
			$formated_user_answers.='<p><b>Online CV:</b> '.esc_attr($result->online_profile_link).'</p>';
			$link_cv = $result->cv_url != "" ? '<a title="Download the file" href="'.esc_attr($result->cv_url).'">File</a>' : '';   
			$formated_user_answers.='<p><b>CV File:</b> '.$link_cv.'</p>';
			$formated_user_answers.='<p><b>Availability for test:</b> '.esc_attr($result->date_test).'</p></div>';
			$formated_user_answers.='<p><a href="#TB_inline?width=600&height=500&inlineId=thickbox'.$result->id.'" class="thickbox">Show the full freelance data</a></p>';    
			
			//preparing buttons to accept or reject user
			if($result->status == 1) { 
				$formated_buttons = '<button type="button" class="button-primary accept_user" data-delete_nonce="'.wp_create_nonce('accept_applicant'.esc_attr($result->id)).'" data-applicant_id = "'.esc_attr($result->id).'">Accept</button><br/><br/>';
				$formated_buttons .= '<button type="button" class="button-primary reject_user" data-delete_nonce="'.wp_create_nonce('deny_applicant'.esc_attr($result->id)).'" data-applicant_id = "'.esc_attr($result->id).'" >Reject</button><br/><br/>';
			} else {
				$formated_buttons ="";
			}
			
			//preparing input for test evaluation
			if($result->test_eval == "-1" && $result->zip_url != '') {
				$evaluation_form = '<p>Test evaluation</p><input class="eval_input" type="text" maxlength="2" style="width:100% !important;"><br/><br/>';
				$evaluation_form .='<p>Test comments</p><textarea class="eval_comments" id="eval_comments" rows="7" cols="1" class="form_component" style="width:100% !important;"></textarea><br/><br/>';
				$evaluation_form .='<button type="button" class="button-primary eval_test" data-delete_nonce="'.wp_create_nonce('eval_applicant'.esc_attr($result->id)).'" data-applicant_id = "'.esc_attr($result->id).'">Eval test</button>';
			} else {
				if($result->zip_url != '') {
					$evaluation_form = '<p>Test evaluation: </p>'.$result->test_eval.'/10<br/>';
					$evaluation_form .='<p>Test comments: </p>'.$result->test_comments;
				} else {
					$evaluation_form = '';	
				}
			}
			//Showing up the data in the table
			$final_table.= '<tr>';
			$final_table.= '<th scope="col" class="manage-column column-primary" style="width:1% !important;">'.$lap.'</th>';
			$final_table.= '<th scope="col" class="manage-column column-primary" style="width:40% !important;">'.$formated_user_answers.'</th>';
			$final_table.= '<th scope="col" class="manage-column">'.esc_attr($result->time_begin).'</th>';
			$final_table.= '<th scope="col" class="manage-column">'.$date_diff.'</th>';
			$link = ($result->zip_url != "") ? '<a title="Download the file" href="'.esc_attr($result->zip_url).'">File</a>' : '';
			$final_table.= '<th scope="col" class="manage-column">'.$link.'</th>'; 
			$final_table.= '<th scope="col" class="manage-column" style="width:20% !important;">'.$evaluation_form.'</th>'; 
			$final_table.= '<th scope="col" class="manage-column">'.$formated_buttons.'<button type="button" class="button-primary ft_delete_applicant" data-delete_nonce="'.wp_create_nonce('delete_email'.esc_attr($result->id)).'" data-applicant_id = "'.esc_attr($result->id).'" title="Delete the user from database">Delete</button></th>';
			$final_table.= '</tr>';
		}
		
		return $final_table; 	
	} 
		
	public function ft_admin_page(){
		if ( !current_user_can( 'manage_options' ) )  {
			wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
		}
		// Echoing the table with the results
		echo '<h2>List of Freelance Applicants.</h2>';	    
		echo '<table class="wp-list-table widefat fixed striped">';
		echo '<thead>';
		echo '<tr>';
		echo '<th scope="col" class="manage-column column-primary" style="width:1% !important;">#</th>'; 
		echo '<th scope="col" class="manage-column column-primary" style="width:40% !important;">Freelance application data</th>';  
		echo '<th scope="col" class="manage-column">Start Date</th>';
		echo '<th scope="col" class="manage-column">Test time</th>';
		echo '<th scope="col" class="manage-column">Zip url</th>';
		echo '<th scope="col" class="manage-column" style="width:20% !important;">Test results</th>';
		echo '<th scope="col" class="manage-column"> Actions </th>';	
		echo '</tr>';
		echo '</thead>';
		echo '<tbody id="ft-applicant-list">';
		echo $this->ft_populate_table_freelancers();
		echo '</tbody>';
		echo '<tfoot>';
		echo '<tr>';
		echo '<th scope="col" class="manage-column column-primary" style="width:5% !important;">#</th>'; 
		echo '<th scope="col" class="manage-column column-primary" style="width:40% !important;">Freelance application data</th>';
		echo '<th scope="col" class="manage-column">Start Date</th>';
		echo '<th scope="col" class="manage-column">Test time</th>';
		echo '<th scope="col" class="manage-column">Zip url</th>';
		echo '<th scope="col" class="manage-column">Test results</th>';
		echo '<th scope="col" class="manage-column"> Actions </th>';	
		echo '</tr>';
		echo '</tfoot>';
		echo '</table>';
	}

	public function ft_view_menu() {
			add_options_page( 'Freelance Test Plugin Configuration', 'Freelance Test', 'manage_options', 'freelance_test-settings', array($this,'ft_admin_view_settings') );
			add_menu_page( 'Freelance Test Plugin', 'Freelance Test', 'manage_options', 'ft_menu', array($this, 'ft_admin_page'));
	}

	public function ft_register_settings() {
		register_setting( 'freelance_test-settings', 'ft_test_file_url' );
		register_setting( 'freelance_test-settings', 'ft_test_contact_email');
		register_setting( 'freelance_test-settings', 'ft_test_reject_mail_content'); 
		register_setting( 'freelance_test-settings', 'ft_test_accept_mail_content');
	}

}

new WP_Frelance_Test();
}

