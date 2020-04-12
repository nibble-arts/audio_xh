<?php

/*
 * create a path from an array
 */

namespace audio;

class Index {

	private static $files;
	private static $keywords;


	public static function index($root) {

		self::scan($root);
		self::parse();
	}


	public static function scan($root) {

		// get directory
		$dir = scandir(Path::create([Config::root(), $root]));

		// iterate files
		foreach ($dir as $file) {

			if ($file != "." && $file != "..") {

				// is dir > recursion
				if (is_dir(Path::create([Config::root(), $root, $file]))) {
					array_merge(self::$files, self::scan(Path::create([$root, $file])));
				}

				else {
					self::$files[] = ["path" => $root, "file" => $file];
				}
			}

		}
	}


	// find keyword
	public static function find($query) {

		$result = [];

		// split words and use as and
		$words = array_filter(explode(" ", $query));

		foreach (self::$keywords as $keyword => $paths) {

			// iterate all query words
			foreach ($words as $word) {

				if (!is_array($result[$word])) {
					$result[$word] = [];
				}

				// hit?
				if (stripos($keyword, $word) !== false) {

					$result[$word] = array_unique(array_merge($result[$word], $paths));
				}

			}
		}


		// boolean and
		$and = [];

		foreach ($result as $entry) {

			// init and
			if (count($and) == 0) {
				$and = $entry;
			}

			$and = array_intersect($entry, $and);
		}

		return $and;
	}


	// parse files
	private static function parse() {

		foreach (self::$files as $file) {
			self::keywords($file);
		}
	}


	// get keywords
	private static function keywords($file) {

		// split into words
		$pattern = "$[\ \_\-]$";

		$name = pathinfo($file["file"],  PATHINFO_FILENAME);
		$words = preg_split($pattern, $name);

		// iterate words
		foreach ($words as $word) {

			// remove integer
			if (intval($word) == 0) {
				self::add_keyword($word, Path::create([$file["path"], $file["file"]]));
			}
		}
	}


	// add keyword to list
	private static function add_keyword($word, $path) {

		// create new keyword
		if (!isset(self::$keywords[$word])) {
			self::$keywords[$word] = [];
		}

		if (!in_array($word, self::$keywords[$words])) {
			self::$keywords[$word][] = $path;
		}
	}
}

?>