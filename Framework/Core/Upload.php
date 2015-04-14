<?php
/**
 * Upload class.
 */
namespace Bluejacket; 
class Upload
{
	public $file;

	/**
	 * config function.
	 *
	 * @access public
	 * @param array $file (default: array())
	 * @return void
	 */
	public function config($file=array()){
		$this->file = $file;
	}

	/**
	 * upload function.
	 *
	 * @access public
	 * @param array $fileInput (default: array())
	 * @return void
	 */
	public function upload($fileInput=array()){
		if(is_array($this->file['allowedTypes'])) $allowedTypes = $this->file['allowedTypes'];
		if(is_numeric($this->file['maxSize'])) $allowedMaxSize = $this->file['maxSize'];
		if(isset($this->file['uploadFolder'])){
			$uploadFolder = $this->file['uploadFolder'];

			/* if(SITE_ROOT) $baseFolder = SITE_ROOT.'/'; */
		}

		if($fileInput == array()) return false;

		if(is_array($fileInput["name"])){
			foreach($fileInput as $k => $v){
				foreach($v as $d => $e){
					$f[$k] = $e;
				}
			}
			$fileInput = $f;
		}


		if($this->check($fileInput,$allowedMaxSize,$allowedTypes)){
			if(file_exists($uploadFolder.$fileInput["name"])){
				return false;
			}else{
				$fileExt = explode(".", $fileInput["name"]);
				$fileName = $fileExt[0];
				$fileExt = end($fileExt);

				$newFileName = $this->fixName($fileName)."_".rand(0,9999999).".".$fileExt;

				$normalPath = $uploadFolder.$newFileName;

				/*
				if(SITE_ROOT) $movePath = $baseFolder.$uploadFolder.$newFileName;
				else $movePath = $uploadFolder.$newFileName;
				*/

				$movePath = $uploadFolder.$newFileName;
				move_uploaded_file($fileInput["tmp_name"],$movePath);
				return array(
					'input' => $fileInput,
					'name' => $newFileName,
					'folder' => $uploadFolder,
					'extention' => $fileExt,
					'path' => $normalPath
				);
			}
		}

		return false;

	}

	/**
	 * check function.
	 *
	 * @access public
	 * @param mixed $fileInput
	 * @param mixed $fileSize
	 * @param array $fileType (default: array())
	 * @return void
	 */
	public function check($fileInput,$fileSize,$fileType=array()){
		$extention = false;
		$size = false;


		$fileSize = $fileSize * (1024 * 1024);
		$fileExt = explode(".", $fileInput['name']);
		$fileExt = strtolower(end($fileExt));

		if(is_array($fileType)){
			$allowedTypes = $fileType;
			if(in_array($fileExt, $allowedTypes)){
				$extention = true;
			}
		}

		if($fileInput['size'] <= $fileSize){
			$size = true;
		}


		if($extention && $size){
			return true;
		}
		return false;

	}

	/**
	 * fixName function.
	 *
	 * @access public
	 * @param mixed $text
	 * @return void
	 */
	public function fixName($text) {
		$text = trim($text);
		$search = array('Ç','ç','Ğ','ğ','ı','İ','Ö','ö','Ş','ş','Ü','ü',' ',')','(','#');
		$replace = array('c','c','g','g','i','i','o','o','s','s','u','u','_','_','_','_');
		$new_text = str_replace($search,$replace,$text);
		return $new_text;
	}
}
?>
