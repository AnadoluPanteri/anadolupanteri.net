<?php
/**
 * Security class.
 */
class Security
{
	public $blacklist=array();

	/**
	 * browser function.
	 *
	 * @access public
	 * @return void
	 */
	public function browser(){
		if($_SERVER['HTTP_USER_AGENT']){
			$browser = $this->getBrowser();
			if($browser['platform']=="other"){
				error_log("Other platform dedected: [".$_SERVER['REMOTE_ADDR']."] ".$browser['name']." / ".$browser['userAgent']);
			}
		}
	}

	/**
	 * getBrowser function.
	 *
	 * @access public
	 * @return void
	 */
	public function getBrowser(){
		$u_agent = $_SERVER['HTTP_USER_AGENT'];
		$bname = 'Unknown';
		$platform = 'Unknown';
		$version= "";

		//First get the platform?
		if (preg_match('/linux/i', $u_agent)) {
			$platform = 'linux';
		}
		elseif (preg_match('/macintosh|mac os x/i', $u_agent)) {
			$platform = 'mac';
		}
		elseif (preg_match('/windows|win32/i', $u_agent)) {
			$platform = 'windows';
		}else{
			$platform = 'other';
		}

		// Next get the name of the useragent yes seperately and for good reason
		if(preg_match('/MSIE/i',$u_agent) && !preg_match('/Opera/i',$u_agent))
		{
			$bname = 'Internet Explorer';
			$ub = "MSIE";
		}
		elseif(preg_match('/Firefox/i',$u_agent))
		{
			$bname = 'Mozilla Firefox';
			$ub = "Firefox";
		}
		elseif(preg_match('/Chrome/i',$u_agent))
		{
			$bname = 'Google Chrome';
			$ub = "Chrome";
		}
		elseif(preg_match('/Safari/i',$u_agent))
		{
			$bname = 'Apple Safari';
			$ub = "Safari";
		}
		elseif(preg_match('/Opera/i',$u_agent))
		{
			$bname = 'Opera';
			$ub = "Opera";
		}
		elseif(preg_match('/Netscape/i',$u_agent))
		{
			$bname = 'Netscape';
			$ub = "Netscape";
		}

		// finally get the correct version number
		$known = array('Version', $ub, 'other');
		$pattern = '#(?<browser>' . join('|', $known) .
			')[/ ]+(?<version>[0-9.|a-zA-Z.]*)#';
		if (!preg_match_all($pattern, $u_agent, $matches)) {
			// we have no matching number just continue
		}

		// see how many we have
		$i = count($matches['browser']);
		if ($i != 1) {
			//we will have two since we are not using 'other' argument yet
			//see if version is before or after the name
			if (strripos($u_agent,"Version") < strripos($u_agent,$ub)){
				$version= $matches['version'][0];
			}
			else {
				$version= $matches['version'][1];
			}
		}
		else {
			$version= $matches['version'][0];
		}

		// check if we have a number
		if ($version==null || $version=="") {$version="?";}

		return array(
			'userAgent' => $u_agent,
			'name'      => $bname,
			'version'   => $version,
			'platform'  => $platform,
			'pattern'    => $pattern
		);
	}


	/**
	 * __clearPOST function.
	 *
	 * @access public
	 * @return void
	 */
	public function __clearPOST(){
		if($_POST && is_array($_POST)){
			foreach ($_POST as $post => $content){
				$_POST[$post]=htmlspecialchars(stripcslashes(stripslashes($content)),ENT_QUOTES);
			}
			error_log("Post Data Cleaned.");
		}
	}

	/**
	 * __clearGET function.
	 *
	 * @access public
	 * @return void
	 */
	public function __clearGET(){
		if($_GET && is_array($_GET)){
			foreach ($_GET as $get => $content){
				$_GET[$get]=htmlspecialchars(stripcslashes(stripslashes($content)));
			}
			if(APP_DEBUGING) error_log("Get Data Cleaned.");
		}
	}

	/**
	 * __clearCUSTOM function.
	 *
	 * @access public
	 * @param mixed $custom
	 * @return void
	 */
	public function __clearCUSTOM($custom){
		if($custom && is_array($custom)){
			foreach ($custom as $cust => $content){
				$custom[$cust]=htmlspecialchars(stripcslashes(stripslashes($content)));
			}
			if(APP_DEBUGING) error_log("$custom Data Cleaned.");
		}
	}

	/**
	 * clearHtmlContents function.
	 *
	 * @access public
	 * @param mixed $content
	 * @return void
	 */
	public function clearHtmlContents($content){
		return htmlspecialchars(stripcslashes(stripslashes($content)));;
	}


	/**
	 * clear function.
	 *
	 * @access public
	 * @param mixed $custom (default: null)
	 * @return void
	 */
	public function clear($custom=null){
		if(!is_null($custom)){
			$this->__clearCUSTOM($custom);
		}else{
			$this->__clearGET();
			$this->__clearPOST();
		}
	}


	/**
	 * convertHTML function.
	 *
	 * @access public
	 * @param mixed $text
	 * @return void
	 */
	public function convertHTML($text){

	}


	/**
	 * htmlsan function.
	 *
	 * @access public
	 * @param mixed $htmlsanitize
	 * @return void
	 */
	public function htmlsan($htmlsanitize){
		return htmlspecialchars($htmlsanitize, ENT_QUOTES, 'UTF-8');
	}

	/**
	 * filter function.
	 *
	 * @access public
	 * @param mixed $type
	 * @param mixed $input
	 * @return void
	 */
	public function filter($type,$input){}


	/**
	 * init function.
	 *
	 * @access public
	 * @param string $objects (default: array('post')
	 * @param mixed 'get')
	 * @return void
	 */
	public function init($objects=array('post','get')){
		if(is_array($objects)){
			if(in_array('post',$objects)) $this->__clearPOST();
			if(in_array('get',$objects)) $this->__clearGET();
		}
		//$this->clear();
		//$this->browser();
	}

	/**
	 * checkEmail function.
	 *
	 * @access public
	 * @param mixed $email
	 * @return void
	 */
	public function checkEmail($email) {
		if(preg_match("/^([a-zA-Z0-9])+([a-zA-Z0-9\._-])*@([a-zA-Z0-9_-])+([a-zA-Z0-9\._-]+)+$/",$email)){
			list($username,$domain)=split('@',$email);
			if(!checkdnsrr($domain,'MX')) {
				return false;
			}
			return true;
		}
		return false;
	}

	/**
	 * blockAndroid function.
	 *
	 * @access public
	 * @param mixed $msg (default: null)
	 * @param mixed $site (default: null)
	 * @return void
	 */
	public function blockAndroid($msg=null,$site=null){
		$ua = strtolower($_SERVER['HTTP_USER_AGENT']);
		if(stripos($ua,'android') !== false) { // && stripos($ua,'mobile') !== false) {
			if(!is_null($msg)){
				echo $msg;
			}
			if(!is_null($site)){
				header("Location: ".$site);
			}
			exit();
		}
	}

	/**
	 * oldIE function.
	 *
	 * @access public
	 * @param mixed $msg (default: null)
	 * @param mixed $site (default: null)
	 * @return void
	 */
	public function oldIE($msg=null,$site=null){
		$ua = strtolower($_SERVER['HTTP_USER_AGENT']);
		if(stripos($ua,'msie') !== false) { // && stripos($ua,'mobile') !== false) {
			if(!is_null($msg)){
				echo $msg;
			}
			if(!is_null($site)){
				header("Location: ".$site);
			}
			exit();
		}
	}
}
?>
