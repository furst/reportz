<?php

namespace model;

class Validation {

	/**
	 * @param  string  $string
	 * @param  integer $length
	 * @param  string  $type
	 * @return string
	 */
	public static function checkEmail($string, $length = 100, $type = "string") {

		$type = 'is_'.$type;

		if(!$type($string)) {
			throw new \Exception("fel typ på epost");
		}
	  	else if(empty($string)) {
	    	throw new \Exception("epost får inte vara tomt");
	    }
	  	else if(strlen($string) > $length) {
	    	throw new \Exception("epost är för långt");
	    }
	    else if (!filter_var($string, FILTER_VALIDATE_EMAIL)) {
    		throw new \Exception("epost är i fel format");
		}

	    return $string;
	}

	/**
	 * @param  string  $string
	 * @param  integer $length
	 * @param  string  $type
	 * @return string
	 */
	public static function check($string, $name, $length = 30, $type = "string") {

		$type = 'is_'.$type;

		if(!$type($string)) {
			throw new \Exception("eel format på $name");
		}
	  	else if(empty($string)) {
	    	throw new \Exception("$name får inte vara tomt");
	    }
	  	else if(strlen($string) > $length) {
	    	throw new \Exception("$name är för långt");
	    }

	    return $string;
	}

	/**
	 * @param  string $string
	 * @return string
	 */
	public static function checkName($string) {

		$name = 'namn';

		self::check($string, $name);

	    return $string;
	}

	/**
	 * @param  string $string
	 * @return string
	 */
	public static function checkDescription($string) {

		$name = 'beskrivning';

		self::check($string, $name, 200);

	    return $string;
	}
}