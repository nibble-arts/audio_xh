<?php

namespace audio;

class File {
	
	private static $file = false;
	private static $errors = [
		"upload_err_ok",
		"upload_err_ini_size",
		"upload_err_form_size",
		"upload_err_partial",
		"upload_err_no_file",
		"upload_err_no_tmp_dir",
		"upload_err_cant_write",
		"upload_err_extension"
	];
	
	// load files data from http post
	public static function init ($id) {

		if (isset($_FILES[$id])) {
			self::$file = $_FILES[$id];
		}
		else {
			self::$file = false;
		}
	}

	// check for file
	public static function has_file () {

		if (self::$file && self::$file["error"] == UPLOAD_ERR_OK && self::$file["size"] > 0) {
			return true;
		}
		else {
			return false;
		}
	}
	
	// copy file to destination
	public static function copy ($target) {

		if (isset($target)) {
			move_uploaded_file(self::tmp_name(), $target . "/" . self::$file["name"]);
		}

		else {
			Message::failure("error_no_target_dir");
		}
	
	}

	// get error string
	public static function error_string () {

		if (($id = self::error()) !== false) {
			return self::$errors[$id];
		}
	}

	// get file property
	// name, type, size, tmp_name, error
	public static function __callStatic ($name, $atte) {

		if (isset(self::$file [$name])) {
			return self::$file [$name];
		}
		
		return false;
	}

	// return FILES array
	public static function debug() {
		return self::$file;
	}
}

?>