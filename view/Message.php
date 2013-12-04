<?php

namespace view;

class Message {

	/**
	 * @return string message
	 */
	public static function loginError() {
		return "Felaktigt användarnamn eller lösenord";
	}

	/**
	 * @return string message
	 */
	public static function reportUpdated() {
		return "Rapport uppdaterad";
	}

	/**
	 * @return string message
	 */
	public static function testcaseUpdated() {
		return "Testfall uppdaterat";
	}

	/**
	 * @return string message
	 */
	public static function reportCreated() {
		return "Rapport skapad";
	}

	/**
	 * @return string message
	 */
	public static function testcaseAdded() {
		return "Testfall tillagt";
	}
}