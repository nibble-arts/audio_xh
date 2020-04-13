<?php

/*
 * create a path from an array
 */

namespace audio;

class Index {

	private static $files;
	private static $keywords;


	// =================================================
	// create index file
	public static function index($root) {

		self::scan($root);
		self::parse();

		self::write();

	}


	// get count of keywords
	public static function count() {
		return count (self::$keywords);
	}


	// get count of files
	public static function files() {

		$files = parse_ini_file(Path::create([Config::root(), "files.ini"]));

		if ($files === false) {
			debug("keine datei");
		}

		return $files["files"];
	}


	// =================================================
	// find keyword
	public static function find($query) {

		self::read();

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


	// =================================================
	// write index file
	private static function write() {

		if (!file_put_contents(Path::create([Config::root(), "index.ini"]), Array2Ini::serialize(self::$keywords))) {

			Message::failure("index_write_failure");
		}

		if (!file_put_contents(Path::create([Config::root(), "files.ini"]), 'files = "' . count(self::$files) . '"')) {

			Message::failure("index_file_write_failure");
		}
	}


	// read index file
	public static function read() {
		self::$keywords = parse_ini_file(Path::create([Config::root(), "index.ini"]), true);
	}


	// =================================================
	// scan directory recursive
	private static function scan($root) {

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