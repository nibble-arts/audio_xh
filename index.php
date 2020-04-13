<?php

/*
Audio Plugin main script
*/

define ('AUDIO_PLUGIN_BASE', $pth["folder"]["plugin"]);



// init class autoloader
spl_autoload_register(function ($path) {

	if ($path && strpos($path, "audio\\") !== false) {
		$path = "classes/" . str_replace("audio\\", "", strtolower($path)) . ".php";

		include_once $path;
	}
});


// init mail plugin class
audio\Main::init($plugin_cf, $plugin_tx);
audio\Main::action();



// mail plugin function
// load single audio file $audio
// or load all files from the directory $audio
function audio($audio = false, $attr = false) {

	global $onload;

	$edit = false;


	// memberaccess installed
	// and logged
	// and user is in news access group
	if (class_exists("ma\Access") && ma\Access::user() && ma\Groups::user_is_in_group(ma\Access::user()->username(), audio\Config::admin_group())) {

		$edit = true;
	}


	// add path snippet from http attributes
	$path = audio\Session::get("audio_path");
	$root = audio\Config::root();



	// add javascript for delete
	$ret = '<script type="text/javascript" src="' . AUDIO_PLUGIN_BASE . 'script/audio.js"></script>';

	// add to onload
	$onload .= "audio_init('" . audio\Text::delete_confirm() . "');";


	// init vies
	audio\View::init($root);


	$ret .= '<div class="audio_wrapper">';

		$ret .= audio\View::file_count();

		$ret .= audio\View::search();


		// ==========================================================
		// show search result
		if ($query = audio\Session::param("audio_query")) {

			// search
			$files = audio\Index::find($query);
			$ret .= audio\View::files($files);
		}


		// ==========================================================
		// check for directory
		if (is_dir(audio\Path::create([$root, $path]))) {
			$ret .= audio\View::dir($path, $attr, $edit);
		}

		else {
			$ret .= audio\View::file($path, $audio, $attr, $edit);
		}


		// ==========================================================
		// add upload
		if (audio\Config::display_upload() !== "false") {
			$ret .= audio\View::upload();
		}

		$ret = audio\Message::render() . $ret;

	$ret .= '</div>';

	return $ret;
}

?>