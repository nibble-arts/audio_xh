<?php

namespace audio;

class View {


	private static $root;
	private static $current_path;


	public static function init($root) {
		self::$root = $root;
	}


	// display directory with subdirecotries and files
	public static function dir($path, $attr, $edit) {

		global $su;

		self::$current_path = $path;


		$ret .= '<div class="audio_dir_block">';
			$formats = explode(",", Config::formats());


			// get directory
			$dir = scandir(Path::create([self::$root, $path]));
			$dir = self::sort($path, $dir);


			// explode path
			$path_parts = array_filter(explode("/", $path));
			$name = end($path_parts);


			// is not at root
			if (count($path_parts)) {

				// remove last item
				array_pop($path_parts);

				// create up directory
				$up_path = Path::create($path_parts);


				// directory up
				if (Config::display_up()) {

					$ret .= '<div><a href="?' . $su . '&audio_path=' . $up_path . "/" . '&upload_dir=' . $up_path . '">';
						$ret .= '<img class="audio_icon" src="' . AUDIO_PLUGIN_BASE . 'images/up.png">';
					$ret .= '</a></div>';
				}
			}



			// display directory position
			if (Config::display_breadcrumb()) {
				$ret .= self::breadcrumbs(self::$current_path);
			}


			// display add directory button
			// $ret .= '<a href="?' . $su . '&audio_action=mkdir&audio_path=' . self::$current_path . '">MKDIR</a>';


			// iterate directory
			foreach ($dir as $file) {

				if ($file != "." && $file != "..") {

					$in_path = Path::create([self::$current_path, $file]);

					// is dir
					if (is_dir(Path::create([self::$root, $in_path]))) {

						$ret .= '<div class="audio_dir_link"><a href="?' . $su . '&audio_path=' . $in_path . '&upload_dir=' . $in_path . '">' . $file . '</a></div>';
					}

					// is file
					else {
						$type = strtolower(pathinfo($file, PATHINFO_EXTENSION));
						$name = pathinfo($file, PATHINFO_BASENAME);

						// valid file extension
						if (in_array($type, $formats)) {
							$ret .= self::file($path, $name, $attr, $edit);
						}
					}
				}
			}

		$ret .= '</div>';

		return $ret;
	}


	// display search
	public static function search() {

		global $su;

		$ret = "";

		if (Config::display_search()) {

			$ret .= '<div class="audio_search_wrapper">';

				$ret .= '<form method="post" action="?' . $su . '">';
			
					$ret .= '<input class="audio_input" type="text" name="audio_query">';
					$ret .= ' <input type="submit" value="' . Text::search() . '">';

				$ret .= '</form>';

			$ret .= '</div>';
		}

		return $ret;
	}


	// display search result
	public static function files($files) {

		$ret .= '<div class="audio_dir_block">';

			$ret .= '<div class="audio_title">' . Text::search_result() . '</div>';

			$ret .= '<div class="audio_count">' . count($files) . ' ' . Text::search_count() . '</div>';

			foreach ($files as $file) {

				// display file
				$ret .= self::file(pathinfo($file, PATHINFO_DIRNAME), pathinfo($file, PATHINFO_BASENAME), "", "", "");
			}

		$ret .= '</div>';

		return $ret;

	}



	// display file to play and download
	public static function file($path, $audio, $attr, $edit) {

		global $su;

		$path = Path::create([$path, $audio]);
		$ret = "";


		// check if file exists
		if (!file_exists(Path::create([self::$root, $path])) || !is_file(Path::create([self::$root, $path]))) {
			$ret .= sprintf('<div class="xh_warning">' . Text::file_dont_exists() . '</div>', $audio);
		}

		else {

			$type = pathinfo($audio, PATHINFO_EXTENSION);
			$name = pathinfo($audio, PATHINFO_FILENAME  );

			$ret .= "<div class='audio'>";

				// display file name as title
				if (AUDIO_DISPLAY_NAME) {
					$ret .= '<div class="audio_header">';

						if (Config::display_title() !== "false") {
							$ret .= '<div class="audio_file_title">' . $name . '</div>';
						}

						// display audio format
						if (Config::display_format() !== "false") {
							$ret .= '<div class="audio_type">' . $type . '</div>';
						}
					

					$ret .= '</div><div style="clear:both"></div>';
				}

				$ret .=  '<audio controls="" preload="auto">';

					$ret .= '<source src="' . Path::create([self::$root, $path]) . '">';

					$ret .= 'Your browser does not support the audio element.';
				$ret .= '</audio>';


				// add download button
				if (Config::download() == "allways" || ($attr == "download" && Config::download() == "optional")) {

					// create download link
					$ret .= '<a href="?&audio_action=audio_download&audio_path=' . self::$current_path . '&file=' . $path . '" target="_ blank">';

						$ret .= '<img class="audio_icon_big" src="' . AUDIO_PLUGIN_BASE . 'images/download.png">';

					$ret .= "</a>";
				}

				// add remove button
				if ($edit) {
					$ret .= '<a href="?' . $su . '&audio_action=audio_remove&file=' . $path . '&audio_path=' . self::$current_path . '">';
						$ret .= '<img class="audio_icon_big audio_delete" src="' . AUDIO_PLUGIN_BASE . 'images/delete.png">';
					$ret .= "</a>";
				}

			$ret .= "</div>";

		}

		$ret .= '<div style="clear:both"></div>';

		return $ret;
	}


	// sort dir by dir/file
	private static function sort($path, $dir) {

		$d = [];
		$f = [];

		$ret = [];

		// split in dirs and files
		foreach ($dir as $element) {

			if (is_dir(Path::create([Config::root(), $path, $element]))) {
				$d[] = $element;
			}

			else {
				$f[] = $element;
			}
		}

		// sort dirs and files
		natsort($d);
		natsort($f);

		$ret = array_merge($d, $f);

		return $ret;
	}


	// show path as breadcrumbs with navigation
	private static function breadcrumbs($path) {

		global $su;

		$crumbs = explode("/", $path);

		$pos = [];
		$temp = [];


		// create crumbs
		foreach ($crumbs as $idx => $crumb) {

			$pos[] = Path::create($crumb);

			// first crumb
			if ($idx == 0) {

				// is not home
				if (count($crumbs) > 1) {
					$temp[] = '<a href="?' . $su . '&audio_path=' . Path::create($pos) . '">Home</a>';
				}

				else {
					$temp[] = '<span class="audio_dir_title">Home</span>';
				}

			}

			// link to upper position
			elseif (intval($idx + 1) != count($crumbs)) {
				$temp[] = '<a href="?' . $su . '&audio_path=' . Path::create($pos) . '">' . $crumb . '</a>';
			}

			// current position
			else {
				$temp[] =  '<span class="audio_dir_title">' . $crumb . '</span>';
			}
		}

		// create breadcrumb line
		$ret = '<div class="audio_breadcrumb">';
			$ret .= implode(" - ", $temp);
		$ret .= '</div>';

		return $ret;
	}
}