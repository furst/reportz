<?php

namespace model;

class User {

	/**
	 * Holder for session
	 * @var string
	 */
	private static $isLoggedInHolder = 'model::User::isLoggedIn';

	/**
	 * @var string
	 */
	private $username = "adde";

	/**
	 * @var string
	 */
	private $password = "abc123";

	/**
	 * See if login is valid
	 * @param  string $username
	 * @param  string $password
	 */
	public function login($username, $password) {
		if ($username == $this->username && $password == $this->password) {
			$this->setloggedIn();
		} else {
			throw new \Exception("Wrong username or password");
		}
	}

	public function logout() {
		unset($_SESSION[self::$isLoggedInHolder]);
	}

	private function setLoggedIn() {
		$_SESSION[self::$isLoggedInHolder] = true;
	}

	/**
	 * @return boolean
	 */
	public static function isLoggedIn() {
		if (isset($_SESSION[self::$isLoggedInHolder])) {
			return true;
		}
		return false;
	}
}
