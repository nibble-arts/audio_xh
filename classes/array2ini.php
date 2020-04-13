<?php

namespace audio;


class Array2Ini {


	// serialize ini file
	public static function serialize($data) {

		$ret = "";

		// is section
		foreach ($data as $key => $value) {

			// key = section
			if (is_array($value)) {

				$ret .= self::section($key);

				foreach ($value as $k => $v) {

					$ret .= self::line($k, $v, self::quotes($v));
				}
			}

			// is single value
			else {
				$ret .= self::line($key, $value, self::quotes($value));
			}
		}

		return $ret;
	}


	// render key value line
	// $d optional quotes for value 
	private static function line($k, $v, $q) {

		return $k . ' = ' . $q . $v . $q . "\n";
	}


	// render section
	private static function section ($s) {
		return "[" . $s . "]\n";
	}


	// get quotes for text
	private static function quotes($value) {

		$q = '"';

		if (!is_int($value)) {

			if (strpos($value, '"') !== false) {
				$q = "'";
			}
		}

		return $q;
	}
}

?>