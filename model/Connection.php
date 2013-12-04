<?php

namespace model;

class Connection {

	/**
	 * @var mysqli-object
	 */
	public static $mysqli;

	/**
	 * Databaseconnection
	 * @var strings
	 */
	private static $host = "localhost";
	private static $user = "root";
	private static $password = "root";
	private static $db = "report";

	public static function open() {
		$mysqli = new \mysqli(self::$host, self::$user, self::$password, self::$db);

		if (mysqli_connect_errno()) {
		    printf("Connect failed: %s\n", mysqli_connect_error());
		    exit();
		}

		self::$mysqli = $mysqli;
	}

	/**
	 * open a second connection when prepare doesn't work
	 * @return mysqli-object
	 */
	public static function openSecond() {
		$mysqli = new \mysqli(self::$host, self::$user, self::$password, self::$db);

		if (mysqli_connect_errno()) {
		    printf("Connect failed: %s\n", mysqli_connect_error());
		    exit();
		}

		return $mysqli;
	}

	public static function close() {
		self::$mysqli->close();
	}

	/**
	 * Get the latest inserted id
	 * @return [type] [description]
	 */
	public static function getId() {
		return self::$mysqli->insert_id;
	}

	/**
	 * Prepare helper
	 * @param  string $sql
	 * @param  string $param1 optional param
	 * @param  string $param2 optional param
	 * @param  string $param3 optional param
	 * @return $stmt
	 */
	public static function prepare($sql, $param1 = "a", $param2 = "a", $param3 = "a") {

		$stmt = self::$mysqli->prepare($sql);
		if ($stmt == FALSE) {
            throw new \Exception("prepare of [$sql] failed " . self::$mysqli->error);
        }

        if ($param1 != "a" && $param2 != "a" && $param3 != "a") {
			$stmt->bind_param('sss', $param1, $param2, $param3);
		}
		else if ($param1 != "a" && $param2 != "a") {
			$stmt->bind_param('ss', $param1, $param2);
		}
		else if ($param1 != "a") {
			$stmt->bind_param('s', $param1);
		}

        $stmt->execute();

        return $stmt;
	}
}




