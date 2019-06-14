<?php 

class MinikitContact extends Minikit {

	public $errors = array();
	public $is_spam = false;
	public $nonce = 'MiniKitCF';
	public $nonce_fail = false;
	public $encryption_key = '';
	public $cipher = 'AES-128-CBC';
	public $to;
	public $from;
	public $subject;
	public $success;
	public $atts = array();
	
	function __construct() {
		add_action('init', array($this, 'init_minikit_contact'));
	}
	
	function init_minikit_contact() {
	
		// populate encryption_key with admin email
		$this->encryption_key = get_option('admin_email');
		
		// contact form shortcode
		add_shortcode('contact-form', array($this, 'contact_form_shortcode'));
		
		
		if(isset($_POST['mk-contact'])) {
			// maybe submit contact form
			$this->maybe_submit();
		}
			
	}
	
	function validate() {
	
		// check honeypot
		if(!empty($_POST['url'])) {
			$this->is_spam = true;
		}
		
		// check wp_nonce
		if(!isset($_POST['security']) || !wp_verify_nonce($_POST['security'], $this->nonce)) {
			$this->nonce_fail = true;
		}
	
		// check name
		if(empty($_POST['mk-name'])) {
			$this->errors['mk-name'] = 'Please enter your name';
		}
		// check email
		if(!filter_var($_POST['mk-email'], FILTER_VALIDATE_EMAIL)) {
			$this->errors['mk-email'] = 'Please enter a valid email address';
		}
		// check message
		if(empty($_POST['mk-message'])) {
			$this->errors['mk-message'] = 'Please fill the message field';
		}
	}
	
	function maybe_submit() {
		
		// validate input
		$this->validate();
		
		// check validation errors
		if(!empty($this->errors)) {
			// set errors session
			$_SESSION['mk-validation-errors'] = $this->errors;
			
			// return early
			return;
		}
		
		// check if it's spam or nonce incorrect
		
		if($this->is_spam || $this->nonce_fail) {
			// return early
			return;
		}
		
		// get attributes
		if(!empty($_POST['mk-atts'])) {
			$this->atts = json_decode($this->decrypt($_POST['mk-atts'], $this->encryption_key), true);
		}
		
		// fill recipient with admin email if no email provided
		$this->to = !empty($this->atts['to'])?$this->atts['to']:get_option('admin_email');
		
		// fill from email with att if provided
		$this->from = !empty($this->atts['from'])?$this->atts['from']:'no-reply@'.ltrim($_SERVER['HTTP_HOST'], 'www.');
		
		// fill subject with default message
		$this->subject = !empty($this->atts['subject'])?$this->atts['subject']:'Message from '.get_option('blogname');
		
		// fill success url
		$this->success = !empty($this->atts['success'])?$this->atts['success']:false;
		if(!empty($this->atts['success'])) {
			$this->success = $this->atts['success'];
		} else {
			if(wp_get_referer()) {
				$this->success = wp_get_referer();
			} else {
				$this->success = get_home_url();
			}
		}
		
		// send email
		$this->send_email();
		
		// redirect to success page
		wp_safe_redirect($this->success);
		
		exit();
		
	}
	
	function send_email() {
		
		// generate content
		$content = "From: ".$_POST['mk-name']."\r\nEmail: ".$_POST['mk-email'];
					
		if(!empty($_POST['mk-phone'])) {
			$content .= "\r\nPhone: ".$_POST['mk-phone'];
		}
		
		$content .= "\r\n\r\n".$_POST['mk-message'];
		
		// generate headers
		$headers = 'From: '.$_POST['mk-name'].' <'.$this->from.'>'."\r\nReply-To: " . $_POST['mk-email']."\r\n";
					
		if(!empty($this->atts['cc'])) {
			$headers .= 'Cc: '.$this->atts['cc']."\r\n";
		}
		if(!empty($this->atts['bcc'])) {
			$headers .= 'Bcc: '.$this->atts['bcc']."\r\n";
		}
		
		// send email
		wp_mail($this->to, $this->subject, $content, $headers);
		
	}
	
	function contact_form_shortcode($atts, $content = null) {
	
		$html = '<div class="mkcf-wrapper">';
		
		// check for validation errors
		if(!empty($_SESSION['mk-validation-errors'])) {
			$html .= '<div class="alert-box error">';
			$html .= implode('<br />', $_SESSION['mk-validation-errors']);
			$html .= '</div>';
			// unset validation errors
			unset($_SESSION['mk-validation-errors']);
		}
		
		$html .= '<form method="post" id="mk-contact-form">';
		// name field
		$html .= '<div class="input">';
		$html .= '<label for="mk-name">Name</label>';
		$html .= '<input type="text" class="input-text" id="mk-name" name="mk-name" value="" />';
		$html .= '</div>';
		
		// email field
		$html .= '<div class="input">';
		$html .= '<label for="mk-email">Email</label>';
		$html .= '<input type="email" class="input-text" id="mk-email" name="mk-email" value="" />';
		$html .= '</div>';
		
		// spam	bait field
		$html .= '<div class="input" style="display:none;">';
		$html .= '<label for="mk-url">URL</label>';
		$html .= '<input type="text" class="input-text" id="mk-url" name="url" value="" />';
		$html .= '</div>';
		
		// message field
		$html .= '<div class="input">';
		$html .= '<label for="mk-message">Message</label>';
		$html .= '<textarea name="mk-message" rows="5" id="mk-message" class="input-text"></textarea>';
		$html .= '</div>';
		
		// submit
		$html .= '<div class="input">';
		$html .= '<input type="hidden" value="'.$this->encrypt(json_encode($atts), $this->encryption_key).'" name="mk-atts" />';
		$html .= '<input type="hidden" name="security" value="'.wp_create_nonce($this->nonce).'" />';
		$html .= '<input type="submit" value="Send" name="mk-contact" class="button" />';
		$html .= '</div>';
		$html .= '</form>';
		$html .= '</div>';
		
		return $html;
	}
	
	
	function safe_b64encode($string) {
		$data = base64_encode($string);
		$data = str_replace(array('+', '/', '='), array('-', '_', ''), $data);
		return $data;
	}
	
	function safe_b64decode($string) {
		$data = str_replace(array('-', '_'), array('+', '/'), $string);
		$mod4 = strlen($data) % 4;
		if ($mod4) {
			$data .= substr('====', $mod4);
		}
		return base64_decode($data);
	}
	
	function encrypt($string, $key) {
		$ivlen = openssl_cipher_iv_length($this->cipher);
		$_SESSION['openssl_iv'] = openssl_random_pseudo_bytes($ivlen);
		return $this->safe_b64encode(openssl_encrypt($string, $this->cipher, $key, 0, $_SESSION['openssl_iv']));
	}
	
	function decrypt($string, $key) {
		return rtrim(openssl_decrypt($this->safe_b64decode($string), $this->cipher, $key, 0, $_SESSION['openssl_iv']), "\0");
	}
	
}

new MinikitContact;