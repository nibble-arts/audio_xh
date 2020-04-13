<?php

namespace audio;

class Main {

	// ================================================
	// initialise access class
	public static function init ($config, $text) {

		Session::load();
		Config::init($config["audio"]);
		Text::init($text["audio"]);

		File::init("audio_file");
	}


	// execute action
	public static function action() {

		switch (Session::get("audio_action")) {

			// save downloaded file
			case "audio_upload":

				// set message/error
				if (File::error()) {
					Message::failure(File::error_string());
				}
				else {
					Message::success(File::error_string());
				}


				// file exists
				if (File::has_file()) {

					// check file size
					if((File::size() > Config::file_max_size()) && Config::file_max_size() != "") {
						Message::failure("error_file_size");
					}

					// copy file to destinition
					else {
						File::copy(Path::create([Config::root(), Session::param("audio_path")]));
					}


					// neu indizieren
					Index::index($path);

				}

				break;


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

						// neu indizieren
						Index::index($path);
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