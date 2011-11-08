<?php

/**
 * Handle file uploads via XMLHttpRequest
 */
class qqUploadedFileXhr {
	/**
	 * Save the file to the specified path
	 * @return boolean TRUE on success
	 */
	function save($path) {
		$input = fopen("php://input", "r");
		$temp = tmpfile();
		$realSize = stream_copy_to_stream($input, $temp);
		fclose($input);

		if ($realSize != $this->getSize()){            
			return false;
		}

		$target = fopen($path, "w");        
		fseek($temp, 0, SEEK_SET);
		stream_copy_to_stream($temp, $target);
		fclose($target);

		return true;
	}
	function getName() {
		return $_GET['qqfile'];
	}
	function getSize() {
		if (isset($_SERVER["CONTENT_LENGTH"])){
			return (int)$_SERVER["CONTENT_LENGTH"];            
		} else {
			throw new Exception('Getting content length is not supported.');
		}      
	}   
}

/**
 * Handle file uploads via regular form post (uses the $_FILES array)
 */
class qqUploadedFileForm {  
	/**
	 * Save the file to the specified path
	 * @return boolean TRUE on success
	 */
	function save($path) {
		if(!move_uploaded_file($_FILES['qqfile']['tmp_name'], $path)){
			return false;
		}
		return true;
	}
	function getName() {
		return $_FILES['qqfile']['name'];
	}
	function getSize() {
		return $_FILES['qqfile']['size'];
	}
}

class qqFileUploader {
	private $allowedExtensions = array();
	private $sizeLimit = 10485760;
	private $file;

	function __construct(array $allowedExtensions = array(), $sizeLimit = 10485760){        
		$allowedExtensions = array_map("strtolower", $allowedExtensions);

		$this->allowedExtensions = $allowedExtensions;        
		$this->sizeLimit = $sizeLimit;

		$this->checkServerSettings();       

		if (isset($_GET['qqfile'])) {
			$this->file = new qqUploadedFileXhr();
		} elseif (isset($_FILES['qqfile'])) {
			$this->file = new qqUploadedFileForm();
		} else {
			$this->file = false; 
		}
	}

	private function checkServerSettings(){        
		$postSize = $this->toBytes(ini_get('post_max_size'));
		$uploadSize = $this->toBytes(ini_get('upload_max_filesize'));        

		if ($postSize < $this->sizeLimit || $uploadSize < $this->sizeLimit){
			$size = max(1, $this->sizeLimit / 1024 / 1024) . 'M';             
			die("{'error':'increase post_max_size and upload_max_filesize to $size'}");    
		}        
	}

	private function toBytes($str){
		$val = trim($str);
		$last = strtolower($str[strlen($str)-1]);
		switch($last) {
		case 'g': $val *= 1024;
		case 'm': $val *= 1024;
		case 'k': $val *= 1024;        
		}
		return $val;
	}

	/**
	 * Preform requested target patch checks depending on the unique setting
	 * 
	 * @var string chosen filename target_path
	 * @return string of resulting target_path
	 * @access private
	 */
	function __handleUnique($target_path){
		$pathinfo = pathinfo($target_path);
		$ext = $pathinfo['extension'];

		$temp_path = substr($target_path, 0, strlen($target_path) - strlen($ext) - 1); //temp path without the ext
		$i=1;
		while(file_exists($target_path)){
			$target_path = $temp_path . '-' . $i . '.' . $ext;
			$i++;
		}
		return $target_path;
	}

	/**
	 * Returns array('success'=>true) or array('error'=>'error message')
	 */
	function handleUpload($uploadDirectory, $replaceOldFile = FALSE){
		if (!is_writable($uploadDirectory)){
			return array('error' => "Server error. Upload directory isn't writable.");
		}

		if (!$this->file){
			return array('error' => 'No files were uploaded.');
		}

		$size = $this->file->getSize();

		if ($size == 0) {
			return array('error' => 'File is empty');
		}

		if ($size > $this->sizeLimit) {
			return array('error' => 'File is too large');
		}

		$pathinfo = pathinfo($this->file->getName());
		$filename = $pathinfo['filename'];
		$ext = $pathinfo['extension'];

		if($this->allowedExtensions && !in_array(strtolower($ext), $this->allowedExtensions)){
			$these = implode("\r\n", $this->allowedExtensions);
			return array('error' => 'Formato do arquivo inválido.' . "\r\n\r\n" . 'Formatos válidos: ' . $these . "\r\n");
		}

		$target_path = $uploadDirectory . DS . $this->file->getName();

		if(!$replaceOldFile){
			$target_path = $this->__handleUnique($target_path);
		}

		if ($this->file->save($target_path)){
			return array(
				'success'=>true,
				'file' => $this->file->getName(),
			);
		} else {
			return array('error'=> 'Could not save uploaded file.' .
				'The upload was cancelled, or server error encountered');
		}

	}    
}

class FileUploaderComponent extends Object {
	var $allowedExtensions;
	var $sizeLimit;

	function initialize(&$controller){
		// list of valid extensions, ex. array("jpeg", "xml", "bmp")
		$this->allowedExtensions = array();
		// max file size in bytes
		$this->sizeLimit = 2 * 1024 * 1024;
	}

	function upload() {
		$uploader = new qqFileUploader($this->allowedExtensions, $this->sizeLimit);
		$result = $uploader->handleUpload('files' . DS . 'scde' . DS . session_id());
		return $result;
		// to pass data through iframe you will need to encode all html tags
		//echo htmlspecialchars(json_encode($result), ENT_NOQUOTES);
	}
}

?>
