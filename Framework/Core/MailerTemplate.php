<?php
class MailTemplate
{
	public $_vars = array();
	//public $_partials;
	public $template;
	public $mail;
	public $content;
	
	public function __construct($template){
		$this->mail = new Mailer();
		$this->mail->IsSMTP();
		$this->mail->SMTPAuth = true; 
		$this->mail->isHTML(true); 
		$this->mail->thisAuth = true;  
		$this->mail->Host = SMTP_SERVER;
		$this->mail->Port = SMTP_PORT;
		$this->mail->Username = SMTP_EMAIL;
		$this->mail->Password = SMTP_PASSWORD;
		$this->mail->From = SMTP_EMAIL;
		$this->mail->FromName = SMTP_NAME;
		$this->mail->AuthType = SMTP_AUTH;
		
		$this->template = $template;
	}
	
	public function set($s,$v){
		$this->_vars[$s] = $v;
	}
	
	/*
	public function partial($v,$f){
		$this->_partials[$v] = $f;
	}
	*/
	
	public function _getTemplate(){
		$content = file_get_contents($this->template);
		foreach($this->_vars as $k => $v){
			$content = str_replace("{{".$k."}}", $v, $content);
		}
		return $content;
	}
	
	
	public function addAddress($name,$email){
		$this->address[] = array(
			'name' => $name,
			'email' => $email
		);
	}
	
	public function send($subject){
		$this->mail->Subject = !is_null($subject) ? $subject : null;
		$this->mail->Body = $this->_getTemplate();
		foreach($this->address as $mail){
			$this->mail->addAddress($mail['email'],$mail['name']);
		}
		if(!$this->mail->send()){
			return true;
		}
		return false; 
	}
}
?>