<?php
/**
 * MailTemplate class.
 */
namespace Bluejacket; 
class MailTemplate
{
	public $_vars = array();
	//public $_partials;
	public $template;
	public $mail;
	public $content;

	/**
	 * __construct function.
	 *
	 * @access public
	 * @param mixed $template
	 * @return void
	 */
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

	/**
	 * set function.
	 *
	 * @access public
	 * @param mixed $s
	 * @param mixed $v
	 * @return void
	 */
	public function set($s,$v){
		$this->_vars[$s] = $v;
	}

	/**
	 * _getTemplate function.
	 *
	 * @access public
	 * @return void
	 */
	public function _getTemplate(){
		$content = file_get_contents($this->template);
		foreach($this->_vars as $k => $v){
			$content = str_replace("{{".$k."}}", $v, $content);
		}
		return $content;
	}


	/**
	 * addAddress function.
	 *
	 * @access public
	 * @param mixed $name
	 * @param mixed $email
	 * @return void
	 */
	public function addAddress($name,$email){
		$this->address[] = array(
			'name' => $name,
			'email' => $email
		);
	}

	/**
	 * send function.
	 *
	 * @access public
	 * @param mixed $subject
	 * @return void
	 */
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
