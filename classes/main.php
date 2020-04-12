<?php

namespace audio;

class Main {

	// ================================================
	// initialise access class
	public static function init ($config, $text) {

		Session::load();
		Config::init($config["audio"]);
		Text::init($text["audio"]);

	}


	// execute action
	public static function action() {

		switch (Session::get("audio_action")) {


			// force download file
			case "audio_download":

				$file_url = Path::create([Config::root(), Session::param("file")]);

				header('Content-Type: application/octet-stream');
				header("Content-Transfer-Encoding: Binary"); 
				header("Content-disposition: attachment; filename=\"" . basename($file_url) . "\"");

				readfile($file_url);

				die();
				break;


			// delete file
			case "audio_remove":

				$file = Path::create([Config::root(), Session::get("file")]);

				if (file_exists($file)) {

					if (unlink ($file)) {
						Message::success("file_deleted");
					}

					else {
						Message::failure("file_delete_error");
					}
				}

				else {
					Message::failure("file_dont_exist");
				}
				break;
		}

	}
}

?>