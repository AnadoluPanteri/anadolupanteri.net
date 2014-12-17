<?php
class Text
{
	public function convert($text,$lang_input,$lang_output, $strstat=false) {
		switch($lang_input){
			case 'tr':
				$search = array('Ç','ç','Ğ','ğ','ı','İ','Ö','ö','Ş','ş','Ü','ü',' ');
				break;
			case 'en':
				$search = array('c','c','g','g','i','i','o','o','s','s','u','u','_');
				break;

				/* başka diller eklenecek */
		}

		switch($lang_output){
			case 'tr':
				$replace = array('Ç','ç','Ğ','ğ','ı','İ','Ö','ö','Ş','ş','Ü','ü',' ');
				break;
			case 'en':
				$replace = array('c','c','g','g','i','i','o','o','s','s','u','u','_');
				break;

				/* başka diller eklenecek */
		}
		$text = trim($text);
		$new_text = str_replace($search,$replace,$text);

		if($strstat){
			switch($strstat){
				case 'lower':
					$new_text = strtolower($new_text);
					break;
				case 'upper':
					$new_text = strtoupper($new_text);
					break;
			}
		}
		return $new_text;
	}
}
?>
