<?php

/*
 * create a path from an array
 */

namespace audio;

class Path {

	public static function create($path) {

		$root = false;
		$result = [];

		// explode path if no array
		if (!is_array($path)) {
			$path = explode("/", $path);
		}

		foreach ($path as $idx_e => $part) {

			$temp = explode("/", $part);

			foreach ($temp as $idx_t => $element) {

				if ($idx_e == 0 && $idx_t == 0 && $element == "") {

					$result[] = "";
				}

				elseif ($element != "") {
					$result[] = $element;
				}
			}
		}

		return implode("/", $result);
	}
}

?>